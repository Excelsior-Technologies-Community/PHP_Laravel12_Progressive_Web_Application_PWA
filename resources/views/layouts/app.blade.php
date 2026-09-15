<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

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

            background:
                linear-gradient(
                    135deg,
                    #0f0f0f,
                    #1c1c1c
                );

            color: #fff;

            font-family:
                'Segoe UI',
                sans-serif;

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

            margin:
                0 auto 20px;

            display: flex;

            justify-content:
                space-between;

            align-items: center;

            gap: 10px;

            flex-wrap: wrap;

        }


        .status-badge {

            display: inline-flex;

            align-items: center;

            gap: 8px;

            padding:
                8px 14px;

            border-radius: 30px;

            font-size: 13px;

            font-weight: 600;

        }


        .status-online {

            background:
                rgba(
                    25,
                    135,
                    84,
                    0.18
                );

            color: #52d48b;

            border:
                1px solid
                rgba(
                    25,
                    135,
                    84,
                    0.4
                );

        }


        .status-offline {

            background:
                rgba(
                    220,
                    53,
                    69,
                    0.18
                );

            color: #ff7b88;

            border:
                1px solid
                rgba(
                    220,
                    53,
                    69,
                    0.4
                );

        }


        .cache-status {

            display: inline-flex;

            align-items: center;

            gap: 7px;

            padding:
                8px 14px;

            border-radius: 30px;

            background:
                rgba(
                    13,
                    110,
                    253,
                    0.15
                );

            color: #72aaff;

            border:
                1px solid
                rgba(
                    13,
                    110,
                    253,
                    0.3
                );

            font-size: 13px;

            font-weight: 600;

        }


        /* ===================================================== */
        /* UPDATE NOTICE */
        /* ===================================================== */

        .update-notice {

            display: none;

            max-width: 1000px;

            margin:
                0 auto 20px;

            padding:
                14px 18px;

            border-radius: 14px;

            background:
                rgba(
                    13,
                    110,
                    253,
                    0.15
                );

            border:
                1px solid
                rgba(
                    13,
                    110,
                    253,
                    0.35
                );

            color: #9bc4ff;

        }


        .update-notice.show {

            display: flex;

            justify-content:
                space-between;

            align-items: center;

            gap: 10px;

            flex-wrap: wrap;

        }


        /* ===================================================== */
        /* BUTTONS */
        /* ===================================================== */

        .btn {

            padding:
                10px 18px;

            border-radius: 25px;

            border: none;

            cursor: pointer;

            font-size: 14px;

            font-weight: 600;

            display: inline-block;

        }


        .btn-primary {

            background:
                #0d6efd;

            color: #fff;

        }


        .btn-danger {

            background:
                #dc3545;

            color: #fff;

        }


        .btn-secondary {

            background:
                #6c757d;

            color: #fff;

        }


        .btn-success {

            background:
                #198754;

            color: #fff;

        }


        .btn:hover {

            opacity: 0.9;

            text-decoration: none;

        }


        .btn:disabled {

            opacity: 0.5;

            cursor: not-allowed;

        }


        /* ===================================================== */
        /* CARDS */
        /* ===================================================== */

        .card {

            background: #111;

            border-radius: 16px;

            padding: 20px;

            margin-bottom: 15px;

            box-shadow:
                0 10px 30px
                rgba(
                    0,
                    0,
                    0,
                    0.4
                );

        }


        .card h3 {

            margin:
                0 0 5px;

        }


        .price {

            color: #0d6efd;

            font-weight: bold;

        }


        /* ===================================================== */
        /* FORMS */
        /* ===================================================== */

        form {

            background: #111;

            padding: 25px;

            border-radius: 20px;

            box-shadow:
                0 10px 30px
                rgba(
                    0,
                    0,
                    0,
                    0.4
                );

        }


        input,
        textarea {

            width: 100%;

            padding: 12px;

            margin-top: 6px;

            margin-bottom: 16px;

            border-radius: 12px;

            border: none;

            background: #1f1f1f;

            color: #fff;

            outline: none;

        }


        input[type="file"] {

            background: none;

        }


        textarea {

            resize: vertical;

        }


        img {

            border-radius: 12px;

            margin-top: 10px;

        }


        .actions {

            display: flex;

            gap: 10px;

            flex-wrap: wrap;

            margin-top: 10px;

        }


        /* ===================================================== */
        /* OFFLINE NOTICE */
        /* ===================================================== */

        .offline-notice {

            display: none;

            background:
                rgba(
                    220,
                    53,
                    69,
                    0.15
                );

            border:
                1px solid
                rgba(
                    220,
                    53,
                    69,
                    0.35
                );

            color: #ff9da7;

            padding:
                12px 16px;

            border-radius: 12px;

            margin-bottom: 20px;

            font-size: 14px;

        }


        .offline-notice.show {

            display: block;

        }


        /* ===================================================== */
        /* CACHE NOTICE */
        /* ===================================================== */

        .cache-notice {

            display: none;

            background:
                rgba(
                    13,
                    110,
                    253,
                    0.12
                );

            border:
                1px solid
                rgba(
                    13,
                    110,
                    253,
                    0.3
                );

            color: #8ab9ff;

            padding:
                12px 16px;

            border-radius: 12px;

            margin-bottom: 20px;

            font-size: 14px;

        }


        .cache-notice.show {

            display: block;

        }


        /* ===================================================== */
        /* MOBILE */
        /* ===================================================== */

        @media(max-width:600px) {

            body {

                padding: 12px;

            }


            h1 {

                font-size: 22px;

            }


            .pwa-status-bar {

                align-items:
                    stretch;

                flex-direction:
                    column;

            }


            .status-badge,
            .cache-status {

                justify-content:
                    center;

            }


            .container {

                width: 100%;

            }

        }

    </style>

</head>


<body>


<h1>
    Laravel 12 PWA
</h1>


{{-- ========================================================= --}}
{{-- PWA STATUS --}}
{{-- ========================================================= --}}

<div class="pwa-status-bar">

    <div
        id="networkStatus"
        class="status-badge status-online"
    >
        🟢 Online
    </div>


    <div
        id="cacheStatus"
        class="cache-status"
    >
        📦 PWA Cache Ready
    </div>

</div>


{{-- ========================================================= --}}
{{-- UPDATE AVAILABLE --}}
{{-- ========================================================= --}}

<div
    id="updateNotice"
    class="update-notice"
>

    <span>
        🔄 A new version of the PWA is available.
    </span>


    <button
        id="refreshPwaButton"
        class="btn btn-primary"
        type="button"
    >
        Refresh
    </button>

</div>


{{-- ========================================================= --}}
{{-- OFFLINE MESSAGE --}}
{{-- ========================================================= --}}

<div
    id="offlineNotice"
    class="offline-notice"
>

    🔴 You are currently offline.

    Previously cached PWA content
    will be used where available.

</div>


{{-- ========================================================= --}}
{{-- CACHE MESSAGE --}}
{{-- ========================================================= --}}

<div
    id="cacheNotice"
    class="cache-notice"
>

    📦 You are viewing cached product data.

    Some actions require an internet connection.

</div>


<div class="container">

    @yield('content')

</div>


<script>

    /*
    |--------------------------------------------------------------------------
    | NETWORK STATUS
    |--------------------------------------------------------------------------
    */

    const networkStatus =
        document.getElementById(
            'networkStatus'
        );


    const offlineNotice =
        document.getElementById(
            'offlineNotice'
        );


    function updateNetworkStatus() {

        if (navigator.onLine) {

            networkStatus.textContent =
                '🟢 Online';


            networkStatus.classList.remove(
                'status-offline'
            );


            networkStatus.classList.add(
                'status-online'
            );


            offlineNotice.classList.remove(
                'show'
            );

        } else {

            networkStatus.textContent =
                '🔴 Offline';


            networkStatus.classList.remove(
                'status-online'
            );


            networkStatus.classList.add(
                'status-offline'
            );


            offlineNotice.classList.add(
                'show'
            );

        }

    }


    window.addEventListener(
        'online',
        updateNetworkStatus
    );


    window.addEventListener(
        'offline',
        updateNetworkStatus
    );


    updateNetworkStatus();


    /*
    |--------------------------------------------------------------------------
    | SERVICE WORKER
    |--------------------------------------------------------------------------
    */

    if ('serviceWorker' in navigator) {

        window.addEventListener(
            'load',
            function () {

                navigator.serviceWorker
                    .register('/sw.js')
                    .then(
                        function (registration) {

                            console.log(
                                'Service Worker registered:',
                                registration.scope
                            );


                            /*
                            | Check for updates
                            */

                            registration.update();


                            /*
                            | New worker waiting
                            */

                            if (
                                registration.waiting
                            ) {

                                showUpdateNotice(
                                    registration.waiting
                                );

                            }


                            registration.addEventListener(
                                'updatefound',
                                function () {

                                    const newWorker =
                                        registration.installing;


                                    if (!newWorker) {
                                        return;
                                    }


                                    newWorker.addEventListener(
                                        'statechange',
                                        function () {

                                            if (
                                                newWorker.state ===
                                                'installed'
                                                &&
                                                navigator
                                                    .serviceWorker
                                                    .controller
                                            ) {

                                                showUpdateNotice(
                                                    newWorker
                                                );

                                            }

                                        }
                                    );

                                }
                            );

                        }
                    )
                    .catch(
                        function (error) {

                            console.error(
                                'Service Worker registration failed:',
                                error
                            );

                        }
                    );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | SERVICE WORKER CONTROLLER CHANGE
        |--------------------------------------------------------------------------
        */

        navigator.serviceWorker.addEventListener(
            'controllerchange',
            function () {

                window.location.reload();

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE NOTICE
    |--------------------------------------------------------------------------
    */

    const updateNotice =
        document.getElementById(
            'updateNotice'
        );


    const refreshPwaButton =
        document.getElementById(
            'refreshPwaButton'
        );


    let refreshingPwa = false;


    function showUpdateNotice(worker) {

        updateNotice.classList.add(
            'show'
        );


        refreshPwaButton.onclick =
            function () {

                if (refreshingPwa) {
                    return;
                }


                refreshingPwa = true;


                worker.postMessage({
                    type: 'SKIP_WAITING'
                });

            };

    }


    /*
    |--------------------------------------------------------------------------
    | CACHE STATUS
    |--------------------------------------------------------------------------
    */

    if ('caches' in window) {

        caches
            .open('laravel-pwa-pages-v4')
            .then(
                function (cache) {

                    cache
                        .match('/product')
                        .then(
                            function (response) {

                                if (response) {

                                    console.log(
                                        'Product page is available in cache.'
                                    );

                                }

                            }
                        );

                }
            )
            .catch(
                function (error) {

                    console.log(
                        'Cache check failed:',
                        error
                    );

                }
            );

    }


    /*
    |--------------------------------------------------------------------------
    | CACHE MODE
    |--------------------------------------------------------------------------
    */

    if (!navigator.onLine) {

        const cacheNotice =
            document.getElementById(
                'cacheNotice'
            );


        if (cacheNotice) {

            cacheNotice.classList.add(
                'show'
            );

        }

    }

</script>


</body>

</html>