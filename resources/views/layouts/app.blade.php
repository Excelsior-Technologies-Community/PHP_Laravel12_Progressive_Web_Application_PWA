<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        Laravel PWA
    </title>

    @PwaHead

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: linear-gradient(135deg, #0f0f0f, #1c1c1c);
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
        }

        a {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        h2 {
            margin-bottom: 20px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        /* ===================================================== */
        /* PWA STATUS BAR */
        /* ===================================================== */

        .pwa-status-bar {
            max-width: 1000px;
            margin: 0 auto 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pwa-status-left, .pwa-status-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
        }

        .status-online {
            background: rgba(25, 135, 84, 0.18);
            color: #52d48b;
            border: 1px solid rgba(25, 135, 84, 0.4);
        }

        .status-offline {
            background: rgba(220, 53, 69, 0.18);
            color: #ff7b88;
            border: 1px solid rgba(220, 53, 69, 0.4);
        }

        .sync-badge {
            display: none;
            align-items: center;
            gap: 7px;
            padding: 8px 14px;
            border-radius: 30px;
            background: rgba(255, 193, 7, 0.18);
            color: #ffd24d;
            border: 1px solid rgba(255, 193, 7, 0.4);
            font-size: 13px;
            font-weight: 600;
        }

        .cache-status {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 14px;
            border-radius: 30px;
            background: rgba(13, 110, 253, 0.15);
            color: #72aaff;
            border: 1px solid rgba(13, 110, 253, 0.3);
            font-size: 13px;
            font-weight: 600;
        }

        /* ===================================================== */
        /* UPDATE NOTICE */
        /* ===================================================== */

        .update-notice {
            display: none;
            max-width: 1000px;
            margin: 0 auto 20px;
            padding: 14px 18px;
            border-radius: 14px;
            background: rgba(13, 110, 253, 0.15);
            border: 1px solid rgba(13, 110, 253, 0.35);
            color: #9bc4ff;
        }

        .update-notice.show {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* ===================================================== */
        /* BUTTONS */
        /* ===================================================== */

        .btn {
            padding: 10px 18px;
            border-radius: 25px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary { background: #0d6efd; color: #fff; }
        .btn-danger { background: #dc3545; color: #fff; }
        .btn-secondary { background: #6c757d; color: #fff; }
        .btn-success { background: #198754; color: #fff; }
        .btn-warning { background: #ffc107; color: #000; }
        .btn-outline {
            background: rgba(255,255,255,0.08);
            color: #ddd;
            border: 1px solid rgba(255,255,255,0.18);
        }

        .btn:hover { opacity: 0.9; text-decoration: none; }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }

        /* ===================================================== */
        /* CARDS */
        /* ===================================================== */

        .card {
            background: #111;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }

        .card h3 { margin: 0 0 5px; }
        .price { color: #0d6efd; font-weight: bold; }

        /* ===================================================== */
        /* FORMS */
        /* ===================================================== */

        form {
            background: #111;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }

        input, textarea, select {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            margin-bottom: 16px;
            border-radius: 12px;
            border: 1px solid #333;
            background: #1f1f1f;
            color: #fff;
            outline: none;
        }

        input[type="file"] { background: none; border: none; }
        textarea { resize: vertical; }
        img { border-radius: 12px; margin-top: 10px; }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        /* ===================================================== */
        /* NOTICES */
        /* ===================================================== */

        .offline-notice {
            display: none;
            background: rgba(220, 53, 69, 0.15);
            border: 1px solid rgba(220, 53, 69, 0.35);
            color: #ff9da7;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .offline-notice.show { display: block; }

        .cache-notice {
            display: none;
            background: rgba(13, 110, 253, 0.12);
            border: 1px solid rgba(13, 110, 253, 0.3);
            color: #8ab9ff;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .cache-notice.show { display: block; }

        /* ===================================================== */
        /* CAMERA BARCODE SCANNER MODAL */
        /* ===================================================== */

        #pwaScannerModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(8px);
            z-index: 999999;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 20px;
        }

        .scanner-card {
            background: #181818;
            border: 1px solid #333;
            border-radius: 24px;
            width: 100%;
            max-width: 480px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,0.8);
            display: flex;
            flex-direction: column;
        }

        .scanner-header {
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #2a2a2a;
        }

        .scanner-header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
        }

        .scanner-viewport {
            position: relative;
            width: 100%;
            height: 320px;
            background: #000;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #pwaScannerVideo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .scanner-guide {
            position: absolute;
            width: 220px;
            height: 180px;
            border: 2px solid #0d6efd;
            border-radius: 16px;
            box-shadow: 0 0 0 4000px rgba(0, 0, 0, 0.5);
            pointer-events: none;
        }

        .scanner-laser {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: #ff3b30;
            box-shadow: 0 0 10px #ff3b30, 0 0 20px #ff3b30;
            animation: laserScan 2s infinite alternate ease-in-out;
        }

        @keyframes laserScan {
            0% { top: 5%; }
            100% { top: 95%; }
        }

        .scanner-footer {
            padding: 16px 20px;
            text-align: center;
            font-size: 13px;
            color: #aaa;
            border-top: 1px solid #2a2a2a;
        }

        /* ===================================================== */
        /* MOBILE */
        /* ===================================================== */

        @media(max-width:600px) {
            body { padding: 12px; }
            h1 { font-size: 22px; }
            .pwa-status-bar { align-items: stretch; flex-direction: column; }
            .status-badge, .cache-status, .sync-badge { justify-content: center; }
            .container { width: 100%; }
        }
    </style>

</head>

<body>

<h1>
    Laravel 12 PWA
</h1>

{{-- ========================================================= --}}
{{-- PWA REALTIME STATUS BAR --}}
{{-- ========================================================= --}}

<div class="pwa-status-bar">

    <div class="pwa-status-left">
        <div id="networkStatus" class="status-badge status-online">
            🟢 Online
        </div>

        <div id="pwaSyncQueueBadge" class="sync-badge">
            ⚡ 0 Pending Sync
        </div>

        <button id="manualSyncBtn" type="button" onclick="window.PwaEngine.syncOfflineQueue()" class="btn btn-warning" style="display:none; padding:6px 14px; font-size:12px; border-radius:20px;">
            🔄 Sync Now
        </button>
    </div>

    <div class="pwa-status-right">
        <div id="cacheStatus" class="cache-status">
            📦 PWA Cache Ready
        </div>
    </div>

</div>

{{-- ========================================================= --}}
{{-- UPDATE AVAILABLE --}}
{{-- ========================================================= --}}

<div id="updateNotice" class="update-notice">
    <span>
        🔄 A new version of the PWA is available.
    </span>
    <button id="refreshPwaButton" class="btn btn-primary" type="button">
        Refresh
    </button>
</div>

{{-- ========================================================= --}}
{{-- OFFLINE MESSAGE --}}
{{-- ========================================================= --}}

<div id="offlineNotice" class="offline-notice">
    🔴 You are currently offline. Offline-First mode active: All create, edit, and delete actions will be saved locally in IndexedDB and synchronized automatically when online.
</div>

{{-- ========================================================= --}}
{{-- CACHE MESSAGE --}}
{{-- ========================================================= --}}

<div id="cacheNotice" class="cache-notice">
    📦 You are viewing cached product data. All local modifications will sync to the server upon reconnection.
</div>

<div class="container">
    @yield('content')
</div>

{{-- ========================================================= --}}
{{-- CAMERA BARCODE & QR CODE SCANNER MODAL --}}
{{-- ========================================================= --}}

<div id="pwaScannerModal">
    <div class="scanner-card">
        <div class="scanner-header">
            <h3>📷 Barcode & QR Scanner</h3>
            <button type="button" onclick="window.closeBarcodeScanner()" class="btn btn-outline" style="padding:4px 10px; border-radius:12px;">
                ✕
            </button>
        </div>
        <div class="scanner-viewport">
            <video id="pwaScannerVideo" playsinline autoplay muted></video>
            <div class="scanner-guide">
                <div class="scanner-laser"></div>
            </div>
        </div>
        <div class="scanner-footer">
            <span id="pwaScannerStatus">Align barcode or QR code within the frame</span>
        </div>
    </div>
</div>

<!-- Load PWA Engine (Offline Sync, Scanner, Web Share, Badging) -->
<script src="/js/pwa-sync.js"></script>

<script>
    /*
    |--------------------------------------------------------------------------
    | NETWORK STATUS
    |--------------------------------------------------------------------------
    */

    const networkStatus = document.getElementById('networkStatus');
    const offlineNotice = document.getElementById('offlineNotice');

    function updateNetworkStatus() {
        if (navigator.onLine) {
            networkStatus.textContent = '🟢 Online';
            networkStatus.classList.remove('status-offline');
            networkStatus.classList.add('status-online');
            offlineNotice.classList.remove('show');
        } else {
            networkStatus.textContent = '🔴 Offline';
            networkStatus.classList.remove('status-online');
            networkStatus.classList.add('status-offline');
            offlineNotice.classList.add('show');
        }
    }

    window.addEventListener('online', updateNetworkStatus);
    window.addEventListener('offline', updateNetworkStatus);
    updateNetworkStatus();

    /*
    |--------------------------------------------------------------------------
    | SERVICE WORKER
    |--------------------------------------------------------------------------
    */

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').then(function (registration) {
                console.log('Service Worker registered:', registration.scope);
                registration.update();

                if (registration.waiting) {
                    showUpdateNotice(registration.waiting);
                }

                registration.addEventListener('updatefound', function () {
                    const newWorker = registration.installing;
                    if (!newWorker) return;

                    newWorker.addEventListener('statechange', function () {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            showUpdateNotice(newWorker);
                        }
                    });
                });
            }).catch(function (error) {
                console.error('Service Worker registration failed:', error);
            });
        });

        navigator.serviceWorker.addEventListener('controllerchange', function () {
            window.location.reload();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE NOTICE
    |--------------------------------------------------------------------------
    */

    const updateNotice = document.getElementById('updateNotice');
    const refreshPwaButton = document.getElementById('refreshPwaButton');
    let refreshingPwa = false;

    function showUpdateNotice(worker) {
        updateNotice.classList.add('show');
        refreshPwaButton.onclick = function () {
            if (refreshingPwa) return;
            refreshingPwa = true;
            worker.postMessage({ type: 'SKIP_WAITING' });
        };
    }

    /*
    |--------------------------------------------------------------------------
    | CACHE MODE
    |--------------------------------------------------------------------------
    */

    if (!navigator.onLine) {
        const cacheNotice = document.getElementById('cacheNotice');
        if (cacheNotice) {
            cacheNotice.classList.add('show');
        }
    }
</script>

</body>
</html>
