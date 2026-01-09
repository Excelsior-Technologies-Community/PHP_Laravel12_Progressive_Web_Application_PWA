<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Install Laravel 12 PWA</title>

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
        }
        .install-btn img{
            width:32px;
            height:32px;
            border-radius:50%;
        }
    </style>
</head>

<body>

<h1>Install Laravel PWA</h1>
<p>Install this app from browser</p>

<button id="installBtn" class="install-btn">
    <img src="/logo.png">
    Install App
</button>

<script>
let deferredPrompt = null;
const installBtn = document.getElementById('installBtn');

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
});

installBtn.addEventListener('click', async () => {
    if (!deferredPrompt) return alert('Already installed');
    deferredPrompt.prompt();
    deferredPrompt = null;
});
</script>

</body>
</html>
