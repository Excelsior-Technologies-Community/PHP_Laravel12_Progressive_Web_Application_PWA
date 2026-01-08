<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel 12 PWA</title>

    {{-- ✅ PWA HEAD --}}
    @PwaHead

    <style>
        body{
            background:#000;
            color:#fff;
            text-align:center;
            padding-top:100px;
            font-family: Arial, sans-serif;
        }

        .install-btn{
            margin-top:30px;
            display:inline-flex;
            align-items:center;
            gap:12px;
            background:#fff;
            color:#000;
            padding:12px 22px;
            border-radius:30px;
            border:none;
            font-size:16px;
            cursor:pointer;
            font-weight:600;
            transition:all 0.3s ease;
        }

        .install-btn:hover{
            transform:scale(1.05);
        }

        .install-btn img{
            width:32px;
            height:32px;
            border-radius:50%;
        }
    </style>
</head>

<body>

    <h1>Laravel PWA Ready</h1>
    <p>Install this app from browser</p>

    {{-- 🔥 INSTALL BUTTON WITH LOGO --}}
    <button id="installBtn" class="install-btn">
        <img src="/logo.png" alt="Logo">
        <span>Install App</span>
    </button>

    <script>
        let deferredPrompt = null;
        const installBtn = document.getElementById('installBtn');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
        });

        installBtn.addEventListener('click', async () => {
            if (!deferredPrompt) {
                alert('App already installed or not supported');
                return;
            }

            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;

            if (outcome === 'accepted') {
                console.log('PWA installed');
            }

            deferredPrompt = null;
        });
    </script>

    {{-- ✅ SERVICE WORKER REGISTER (VERY IMPORTANT) --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(() => console.log('Service Worker Registered'))
                    .catch(err => console.log('SW registration failed:', err));
            });
        }
    </script>

</body>
</html>
