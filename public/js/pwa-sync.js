/**
 * Laravel 12 Progressive Web Application (PWA) Engine
 * - Offline IndexedDB Background Sync
 * - Camera Barcode & QR Code Scanner
 * - Native Device Capabilities (Web Share, App Badging, Network Monitor)
 */

(function () {
    'use strict';

    const DB_NAME = 'LaravelPwaDB';
    const DB_VERSION = 1;
    const QUEUE_STORE = 'offline_queue';
    const PRODUCTS_STORE = 'cached_products';

    // ------------------------------------------------------------------------
    // 1. IndexedDB Helper
    // ------------------------------------------------------------------------

    function openDatabase() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(DB_NAME, DB_VERSION);

            request.onupgradeneeded = function (event) {
                const db = event.target.result;
                if (!db.objectStoreNames.contains(QUEUE_STORE)) {
                    db.createObjectStore(QUEUE_STORE, { keyPath: 'temp_id' });
                }
                if (!db.objectStoreNames.contains(PRODUCTS_STORE)) {
                    db.createObjectStore(PRODUCTS_STORE, { keyPath: 'id' });
                }
            };

            request.onsuccess = function (event) {
                resolve(event.target.result);
            };

            request.onerror = function (event) {
                console.error('IndexedDB Error:', event.target.error);
                reject(event.target.error);
            };
        });
    }

    async function queueOfflineAction(action, data) {
        const db = await openDatabase();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(QUEUE_STORE, 'readwrite');
            const store = tx.objectStore(QUEUE_STORE);
            const temp_id = 'offline_' + Date.now() + '_' + Math.random().toString(36).substring(2, 7);

            const item = {
                temp_id: temp_id,
                action: action, // 'CREATE' | 'UPDATE' | 'DELETE'
                data: data,
                timestamp: new Date().toISOString()
            };

            const req = store.put(item);
            req.onsuccess = () => {
                updateQueueUI();
                resolve(item);
            };
            req.onerror = (e) => reject(e.target.error);
        });
    }

    async function getOfflineQueue() {
        const db = await openDatabase();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(QUEUE_STORE, 'readonly');
            const store = tx.objectStore(QUEUE_STORE);
            const req = store.getAll();
            req.onsuccess = () => resolve(req.result || []);
            req.onerror = (e) => reject(e.target.error);
        });
    }

    async function removeQueueItems(tempIds) {
        if (!tempIds || tempIds.length === 0) return;
        const db = await openDatabase();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(QUEUE_STORE, 'readwrite');
            const store = tx.objectStore(QUEUE_STORE);
            tempIds.forEach(id => store.delete(id));
            tx.oncomplete = () => {
                updateQueueUI();
                resolve();
            };
            tx.onerror = (e) => reject(e.target.error);
        });
    }

    // ------------------------------------------------------------------------
    // 2. Background Sync Engine
    // ------------------------------------------------------------------------

    let isSyncing = false;

    async function syncOfflineQueue() {
        if (!navigator.onLine || isSyncing) return;

        const queue = await getOfflineQueue();
        if (queue.length === 0) {
            updateQueueUI();
            return;
        }

        isSyncing = true;
        showToast(`🔄 Syncing ${queue.length} offline change(s)...`, 'info');

        const syncBtn = document.getElementById('manualSyncBtn');
        if (syncBtn) {
            syncBtn.classList.add('animate-spin');
            syncBtn.disabled = true;
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                document.querySelector('input[name="_token"]')?.value || '';

            const response = await fetch('/products/sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-PWA-Sync': '1'
                },
                body: JSON.stringify({ queue: queue })
            });

            if (response.ok) {
                const result = await response.json();
                const tempIds = queue.map(q => q.temp_id);
                await removeQueueItems(tempIds);

                showToast(`✅ ${result.message || 'Offline changes synchronized!'}`, 'success');

                // Update App Badging
                if (result.total_products !== undefined) {
                    window.updateAppBadge(result.total_products);
                }

                // Auto reload if on product list page to show fresh data
                setTimeout(() => {
                    if (window.location.pathname.includes('/product') && !window.location.pathname.includes('/create') && !window.location.pathname.includes('/edit')) {
                        window.location.reload();
                    }
                }, 1200);

            } else {
                console.warn('Sync server returned non-200:', response.status);
            }
        } catch (err) {
            console.error('Failed to sync offline queue:', err);
        } finally {
            isSyncing = false;
            if (syncBtn) {
                syncBtn.classList.remove('animate-spin');
                syncBtn.disabled = false;
            }
            updateQueueUI();
        }
    }

    async function updateQueueUI() {
        const queue = await getOfflineQueue();
        const count = queue.length;
        const queueBadge = document.getElementById('pwaSyncQueueBadge');
        const manualSyncBtn = document.getElementById('manualSyncBtn');

        if (queueBadge) {
            if (count > 0) {
                queueBadge.textContent = `⚡ ${count} Pending Sync`;
                queueBadge.style.display = 'inline-flex';
            } else {
                queueBadge.style.display = 'none';
            }
        }

        if (manualSyncBtn) {
            manualSyncBtn.style.display = (count > 0 && navigator.onLine) ? 'inline-flex' : 'none';
        }

        // Set or clear App Badge
        if ('setAppBadge' in navigator) {
            if (count > 0) {
                navigator.setAppBadge(count).catch(() => {});
            }
        }
    }

    // ------------------------------------------------------------------------
    // 3. Web Share API & App Badging API
    // ------------------------------------------------------------------------

    window.shareProduct = async function (name, price, url) {
        const shareData = {
            title: name || 'Product from Laravel PWA',
            text: `${name} - ₹${price}\nCheck out this product on Laravel PWA!`,
            url: url || window.location.href
        };

        if (navigator.share && navigator.canShare && navigator.canShare(shareData)) {
            try {
                await navigator.share(shareData);
                showToast('📤 Shared successfully!', 'success');
            } catch (err) {
                if (err.name !== 'AbortError') {
                    console.log('Error sharing:', err);
                }
            }
        } else {
            // Desktop / fallback: Copy URL to clipboard
            try {
                await navigator.clipboard.writeText(shareData.url);
                showToast('🔗 Product link copied to clipboard!', 'success');
            } catch (e) {
                prompt('Copy product link:', shareData.url);
            }
        }
    };

    window.updateAppBadge = function (count) {
        if ('setAppBadge' in navigator) {
            if (count > 0) {
                navigator.setAppBadge(count).catch(() => {});
            } else {
                navigator.clearAppBadge().catch(() => {});
            }
        }
    };

    // ------------------------------------------------------------------------
    // 4. Camera Barcode & QR Code Scanner
    // ------------------------------------------------------------------------

    let scannerStream = null;
    let scannerCallback = null;
    let scannerAnimationId = null;

    window.openBarcodeScanner = async function (callback) {
        scannerCallback = callback;
        const modal = document.getElementById('pwaScannerModal');
        const video = document.getElementById('pwaScannerVideo');
        const statusText = document.getElementById('pwaScannerStatus');

        if (!modal || !video) {
            alert('Barcode scanner modal component not found.');
            return;
        }

        modal.style.display = 'flex';
        statusText.textContent = 'Initializing camera...';

        try {
            const constraints = {
                video: {
                    facingMode: { ideal: 'environment' },
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            };

            scannerStream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = scannerStream;
            await video.play();
            statusText.textContent = 'Point camera at any Barcode or QR Code';

            startBarcodeDetection(video);

        } catch (err) {
            console.error('Camera access error:', err);
            statusText.textContent = '⚠️ Camera permission denied or not available.';
        }
    };

    window.closeBarcodeScanner = function () {
        const modal = document.getElementById('pwaScannerModal');
        const video = document.getElementById('pwaScannerVideo');

        if (scannerAnimationId) {
            cancelAnimationFrame(scannerAnimationId);
            scannerAnimationId = null;
        }

        if (scannerStream) {
            scannerStream.getTracks().forEach(track => track.stop());
            scannerStream = null;
        }

        if (video) {
            video.srcObject = null;
        }

        if (modal) {
            modal.style.display = 'none';
        }
    };

    function playBeepSound() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, ctx.currentTime);
            gain.gain.setValueAtTime(0.2, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.15);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.15);
        } catch (e) {}

        if ('vibrate' in navigator) {
            navigator.vibrate([80, 40, 80]);
        }
    }

    async function startBarcodeDetection(video) {
        let detector = null;

        if ('BarcodeDetector' in window) {
            try {
                const supportedFormats = await BarcodeDetector.getSupportedFormats();
                detector = new BarcodeDetector({ formats: supportedFormats });
            } catch (e) {
                console.warn('BarcodeDetector error:', e);
            }
        }

        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d', { willReadFrequently: true });

        async function scanFrame() {
            if (!scannerStream || video.readyState !== video.HAVE_ENOUGH_DATA) {
                scannerAnimationId = requestAnimationFrame(scanFrame);
                return;
            }

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            if (detector) {
                try {
                    const barcodes = await detector.detect(video);
                    if (barcodes.length > 0) {
                        const codeValue = barcodes[0].rawValue;
                        handleScannedCode(codeValue);
                        return;
                    }
                } catch (e) {}
            }

            scannerAnimationId = requestAnimationFrame(scanFrame);
        }

        scannerAnimationId = requestAnimationFrame(scanFrame);
    }

    function handleScannedCode(codeValue) {
        playBeepSound();
        showToast(`🎯 Scanned: ${codeValue}`, 'success');
        window.closeBarcodeScanner();

        if (typeof scannerCallback === 'function') {
            scannerCallback(codeValue);
        }
    }

    // ------------------------------------------------------------------------
    // 5. Toast Notifications
    // ------------------------------------------------------------------------

    function showToast(message, type = 'info') {
        let container = document.getElementById('pwaToastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'pwaToastContainer';
            container.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:99999;display:flex;flex-direction:column;gap:10px;pointer-events:none;max-width:90vw;';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        const bg = type === 'success' ? '#198754' : type === 'error' ? '#dc3545' : '#0d6efd';
        toast.style.cssText = `background:${bg};color:#fff;padding:12px 18px;border-radius:12px;font-size:13px;font-weight:600;box-shadow:0 10px 25px rgba(0,0,0,0.5);display:flex;align-items:center;gap:8px;pointer-events:auto;transition:all 0.3s ease;transform:translateY(20px);opacity:0;`;
        toast.textContent = message;

        container.appendChild(toast);

        requestAnimationFrame(() => {
            toast.style.transform = 'translateY(0)';
            toast.style.opacity = '1';
        });

        setTimeout(() => {
            toast.style.transform = 'translateY(20px)';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // ------------------------------------------------------------------------
    // 6. Global Hooks & Offline Interception
    // ------------------------------------------------------------------------

    window.PwaEngine = {
        queueOfflineAction,
        getOfflineQueue,
        syncOfflineQueue,
        showToast
    };

    window.addEventListener('online', () => {
        showToast('🟢 Internet Connection Restored! Syncing...', 'success');
        syncOfflineQueue();
    });

    window.addEventListener('offline', () => {
        showToast('🔴 You are offline. Changes will be saved locally in IndexedDB.', 'error');
        updateQueueUI();
    });

    document.addEventListener('DOMContentLoaded', () => {
        updateQueueUI();
        if (navigator.onLine) {
            syncOfflineQueue();
        }
    });

})();
