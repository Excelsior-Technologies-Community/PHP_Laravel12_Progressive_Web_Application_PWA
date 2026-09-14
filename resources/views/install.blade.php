<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Install Laravel 12 PWA</title>

    @PwaHead

    <style>

        * {
            box-sizing: border-box;
        }

        body {

            margin: 0;

            min-height: 100vh;

            display: flex;

            justify-content: center;

            align-items: center;

            padding: 20px;

            background:
                linear-gradient(
                    135deg,
                    #050505,
                    #1c1c1c
                );

            color: #fff;

            font-family:
                Arial,
                sans-serif;

        }

        .install-container {

            width: 100%;

            max-width: 480px;

            text-align: center;

            background: #111;

            padding: 40px 30px;

            border-radius: 24px;

            box-shadow:
                0 20px 60px
                rgba(0, 0, 0, 0.5);

        }

        .logo {

            width: 100px;

            height: 100px;

            object-fit: cover;

            border-radius: 20px;

            margin-bottom: 20px;

        }

        h1 {

            margin: 0 0 10px;

            font-size: 30px;

        }

        p {

            color: #aaa;

            line-height: 1.6;

        }

        .status {

            margin: 25px 0;

            padding: 14px;

            border-radius: 14px;

            font-weight: 600;

        }

        .status-ready {

            background:
                rgba(13, 110, 253, 0.15);

            color: #74aaff;

            border:
                1px solid
                rgba(13, 110, 253, 0.3);

        }

        .status-installed {

            background:
                rgba(25, 135, 84, 0.15);

            color: #58d68d;

            border:
                1px solid
                rgba(25, 135, 84, 0.3);

        }

        .status-unavailable {

            background:
                rgba(255, 193, 7, 0.12);

            color: #ffd666;

            border:
                1px solid
                rgba(255, 193, 7, 0.3);

        }

        .install-btn {

            width: 100%;

            margin-top: 10px;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 12px;

            background: #0d6efd;

            color: #fff;

            padding: 14px 22px;

            border-radius: 30px;

            border: none;

            font-size: 16px;

            cursor: pointer;

            font-weight: 600;

        }

        .install-btn:hover {

            opacity: 0.9;

        }

        .install-btn:disabled {

            opacity: 0.5;

            cursor: not-allowed;

        }

        .install-btn img {

            width: 30px;

            height: 30px;

            border-radius: 8px;

        }

        .back-btn {

            display: inline-block;

            margin-top: 20px;

            color: #aaa;

            text-decoration: none;

        }

        .back-btn:hover {

            color: #fff;

        }

        .info {

            margin-top: 20px;

            font-size: 12px;

            color: #777;

        }

    </style>

</head>


<body>

<div class="install-container">

    <img
        src="/logo.png"
        alt="Laravel PWA Logo"
        class="logo"
    >


    <h1>
        Laravel 12 PWA
    </h1>


    <p>
        Install this Progressive Web Application
        on your device for a faster app-like experience.
    </p>


    {{-- INSTALL STATUS --}}

    <div
        id="installStatus"
        class="status status-ready"
    >
        Checking installation status...
    </div>


    {{-- INSTALL BUTTON --}}

    <button
        id="installBtn"
        class="install-btn"
        type="button"
    >

        <img
            src="/logo.png"
            alt="PWA"
        >

        <span id="installButtonText">
            Install App
        </span>

    </button>


    {{-- BACK TO PRODUCT LIST --}}

    <a
        href="{{ route('product.index') }}"
        class="back-btn"
    >
        ← Continue to Product List
    </a>


    <div
        id="info"
        class="info"
    >
        Checking browser installation support...
    </div>

</div>


<script>

    /*
    |--------------------------------------------------------------------------
    | PWA INSTALL VARIABLES
    |--------------------------------------------------------------------------
    */

    let deferredPrompt = null;


    const installBtn =
        document.getElementById(
            'installBtn'
        );


    const installButtonText =
        document.getElementById(
            'installButtonText'
        );


    const installStatus =
        document.getElementById(
            'installStatus'
        );


    const info =
        document.getElementById(
            'info'
        );


    /*
    |--------------------------------------------------------------------------
    | CHECK WHETHER PWA IS ALREADY INSTALLED
    |--------------------------------------------------------------------------
    */

    function isPWAInstalled() {

        return (

            window.matchMedia(
                '(display-mode: standalone)'
            ).matches

            ||

            window.navigator.standalone === true

            ||

            document.referrer.startsWith(
                'android-app://'
            )

        );

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE INSTALL UI
    |--------------------------------------------------------------------------
    */

    function updateInstallUI() {


        /*
        |--------------------------------------------------------------------------
        | ALREADY INSTALLED
        |--------------------------------------------------------------------------
        */

        if (isPWAInstalled()) {

            installStatus.textContent =
                '✓ App is already installed';

            installStatus.className =
                'status status-installed';

            installButtonText.textContent =
                'App Already Installed';

            installBtn.disabled = true;

            info.textContent =
                'Laravel PWA is running as an installed application.';

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | INSTALL PROMPT AVAILABLE
        |--------------------------------------------------------------------------
        */

        if (deferredPrompt) {

            installStatus.textContent =
                '📱 App is ready to install';

            installStatus.className =
                'status status-ready';

            installButtonText.textContent =
                'Install App';

            installBtn.disabled = false;

            info.textContent =
                'Your browser supports PWA installation.';

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | PROMPT NOT AVAILABLE YET
        |--------------------------------------------------------------------------
        */

        installStatus.textContent =
            'ℹ️ Installation prompt is not currently available';

        installStatus.className =
            'status status-unavailable';

        installButtonText.textContent =
            'Install App';

        installBtn.disabled = false;

        info.textContent =
            'The browser will provide the installation option when the PWA becomes installable.';

    }


    /*
    |--------------------------------------------------------------------------
    | BEFORE INSTALL PROMPT
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        'beforeinstallprompt',
        function (event) {

            console.log(
                'beforeinstallprompt event fired'
            );


            event.preventDefault();


            deferredPrompt = event;


            updateInstallUI();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | INSTALL BUTTON CLICK
    |--------------------------------------------------------------------------
    */

    installBtn.addEventListener(
        'click',
        async function () {


            /*
            |--------------------------------------------------------------------------
            | ALREADY INSTALLED
            |--------------------------------------------------------------------------
            */

            if (isPWAInstalled()) {

                installStatus.textContent =
                    '✓ App is already installed';

                installStatus.className =
                    'status status-installed';

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | INSTALL PROMPT NOT AVAILABLE
            |--------------------------------------------------------------------------
            */

            if (!deferredPrompt) {

                installStatus.textContent =
                    'ℹ️ Installation prompt is not available yet.';

                installStatus.className =
                    'status status-unavailable';

                info.textContent =
                    'Please use Chrome or Edge and make sure the PWA requirements are satisfied.';

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | SHOW INSTALL PROMPT
            |--------------------------------------------------------------------------
            */

            deferredPrompt.prompt();


            const result =
                await deferredPrompt.userChoice;


            console.log(
                'PWA installation result:',
                result.outcome
            );


            /*
            |--------------------------------------------------------------------------
            | CLEAR PROMPT
            |--------------------------------------------------------------------------
            */

            deferredPrompt = null;


            updateInstallUI();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | APP INSTALLED EVENT
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        'appinstalled',
        function () {

            console.log(
                'Laravel PWA installed successfully'
            );


            deferredPrompt = null;


            installStatus.textContent =
                '✓ Laravel PWA installed successfully';


            installStatus.className =
                'status status-installed';


            installButtonText.textContent =
                'App Already Installed';


            installBtn.disabled = true;


            info.textContent =
                'The application has been installed successfully.';

        }
    );


    /*
    |--------------------------------------------------------------------------
    | DISPLAY MODE CHANGE
    |--------------------------------------------------------------------------
    */

    const displayMode =
        window.matchMedia(
            '(display-mode: standalone)'
        );


    displayMode.addEventListener(
        'change',
        function () {

            updateInstallUI();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | INITIAL UI
    |--------------------------------------------------------------------------
    */

    updateInstallUI();

</script>

</body>

</html>