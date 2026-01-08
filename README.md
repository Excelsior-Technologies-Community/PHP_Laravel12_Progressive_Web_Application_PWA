# PHP_Laravel12_Progressive_Web_Application_PWA

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/PWA-Enabled-5A0FC8?style=for-the-badge&logo=googlechrome&logoColor=white" />
  <img src="https://img.shields.io/badge/Service%20Worker-Active-0A66C2?style=for-the-badge&logo=javascript&logoColor=white" />
  <img src="https://img.shields.io/badge/Offline-Supported-2EA44F?style=for-the-badge&logo=cachet&logoColor=white" />
  <img src="https://img.shields.io/badge/HTTPS-Required-success?style=for-the-badge&logo=letsencrypt&logoColor=white" />
</p>

---

## Overview

This project demonstrates how to integrate **Progressive Web Application (PWA)** features into a **Laravel 12** application. It includes app installation support, offline fallback pages, service worker handling, and a web app manifest for a native-like experience on desktop and mobile devices.

---

## Features

* Installable PWA (Desktop & Mobile)
* Custom "Install App" button
* Offline support with fallback page
* Service Worker for caching and network handling
* Web App Manifest configuration
* App icon & splash screen support
* Works on localhost and HTTPS production environments
* Compatible with modern browsers (Chrome, Edge)

---

## Folder Structure

```text
laravel-pwa/
├── app/
├── bootstrap/
├── config/
│   └── pwa.php
├── public/
│   ├── sw.js
│   ├── offline.html
│   ├── manifest.json
│   └── logo.png
├── resources/
│   └── views/
│       └── welcome.blade.php
├── routes/
│   └── web.php
└── README.md
```

---

## Requirements

* PHP 8.2+
* Laravel 12
* Composer
* Node.js & NPM (optional, for frontend assets)
* HTTPS (required for production PWA)

---

## Step 1: Create Laravel Project

```bash
composer create-project laravel/laravel laravel-pwa

php artisan serve
```

---

## Step 2: Install Laravel PWA Package

```bash
composer require erag/laravel-pwa
```

---

## Step 3: Register Service Provider (Laravel 11 / 12)

**File:** `bootstrap/providers.php`

```php
<?php

use Illuminate\Foundation\Application;
use EragLaravelPwa\EragLaravelPwaServiceProvider;

return [
    EragLaravelPwaServiceProvider::class,
];
```

---

## Step 4: Publish PWA Configuration

```bash
php artisan erag:install-pwa
```

This command will:

* Create `config/pwa.php`
* Generate `public/manifest.json`
* Generate `public/serviceworker.js`

---

## Step 5: Configure PWA Settings

**File:** `config/pwa.php`

```php
<?php

return [

    'install-button' => true,

    'manifest' => [
        'name' => 'Laravel 12 PWA',
        'short_name' => 'L12PWA',
        'background_color' => '#ffffff',
        'theme_color' => '#0d6efd',
        'display' => 'standalone',
        'description' => 'Laravel 12 Progressive Web Application',
        'icons' => [
            [
                'src' => '/logo.png',
                'sizes' => '512x512',
                'type' => 'image/png',
            ],
        ],
    ],

    'debug' => env('APP_DEBUG', false),

    'livewire-app' => false,
];
```

---

## Step 6: Add App Logo

Place your logo file here:

```
public/logo.png
```

Requirements:

* PNG format
* 512x512 size

Test in browser:

```
http://127.0.0.1:8000/logo.png
```

---

## Step 7: Update Manifest File

```bash
php artisan erag:update-manifest
```

This refreshes `public/manifest.json`.

---

## Step 8: Add PWA Meta & Install Button

**File:** `resources/views/welcome.blade.php`

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel 12 PWA</title>

    {{--  PWA HEAD --}}
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

    {{--  INSTALL BUTTON WITH LOGO --}}
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

    {{--  SERVICE WORKER REGISTER (VERY IMPORTANT) --}}
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

```

---

## Step 9: Create Offline Page

**File:** `public/offline.html`

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Check your internet connection</title>
    <style> body {margin: 0;background: linear-gradient(172deg, #181722 20%, #ff2d20 100%, #8d7171 100%);display: flex;justify-content: center;align-items: center;height: 100vh;font-family: "Lato", sans-serif;}.container {text-align: center;}.text {color: #fff;font-weight: 300;font-size: 45px;margin-bottom: 40px;animation: fade-in-out 2.5s infinite;}.box {width: 240px;height: 150px;position: relative;margin: 0 auto;}.comp, .server {border: 3px solid #fff;}.comp {width: 80px;height: 55px;border-radius: 5px;position: absolute;top: 0;}.comp:after {content: "";position: absolute;top: 19px;left: 5px;width: 65px;height: 10px;border-radius: 360px;border: 3px solid #fff;}.loader {position: absolute;top: 26px;left: 12px;width: 8px;height: 7px;background: #fff;border-radius: 8%;animation: loader 5s infinite linear 0.5s;}.con {position: absolute;top: 28px;left: 85px;width: 100px;height: 3px;background: #fff;}.byte {position: absolute;top: 25px;left: 80px;width: 9px;height: 9px;background: #fff;border-radius: 50%;opacity: 0;animation: byte_animate 5s infinite linear 0.5s;z-index: 6;}.server {width: 35px;height: 65px;border-radius: 360px;background: #eaecf4;transform: rotateX(58deg);position: absolute;top: 6px;left: 185px;z-index: 1;}.server:before {content: "";position: absolute;top: -47px;left: -3px;width: 35px;height: 35px;background: #d3bbba;border-radius: 50%;border: 3px solid #fff;z-index: 20;}.server:after {content: "";position: absolute;top: -26px;left: -3px;width: 35px;height: 40px;background: #fff;border-left: 3px solid #fff;border-right: 3px solid #fff;z-index: 17;}@keyframes byte_animate {0% {opacity: 0;left: 80px }4% {opacity: 1 }46% {opacity: 1 }50% {opacity: 0;left: 185px }54% {opacity: 1 }96% {opacity: 1 }100% {opacity: 0;left: 80px }}@keyframes loader {0% {width: 8px }100% {width: 63px }}@keyframes fade-in-out {0%, 100% {opacity: 1 }50% {opacity: 0 }}</style>
</head>
<body>
<div class="container">
    <div class="text">CONNECTING</div>
    <div class="box">
        <div class="comp"></div>
        <div class="loader"></div>
        <div class="con"></div>
        <div class="byte"></div>
        <div class="server"></div>
    </div>
</div>
</body>
</html>

```

---

## Step 10: Service Worker

**File:** `public/sw.js`

```js
"use strict";

const CACHE_NAME = "laravel-pwa-offline-v2";
const OFFLINE_URL = "/offline.html";

// Install – cache offline page
self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.add(OFFLINE_URL))
    );
    self.skipWaiting();
});

// Activate
self.addEventListener("activate", (event) => {
    event.waitUntil(self.clients.claim());
});

// Fetch – FORCE offline page when network fails
self.addEventListener("fetch", (event) => {

    if (event.request.destination === "document") {
        event.respondWith(
            fetch(event.request)
                .then(response => response)
                .catch(() => caches.match(OFFLINE_URL))
        );
    }
});

```

---

## Step 11: Test PWA

### Install Test

* Open app in Chrome

  <img width="1738" height="713" alt="Screenshot 2026-01-08 130849" src="https://github.com/user-attachments/assets/df4564c3-7955-4e55-9606-b28a580b8a69" />

* Click **Install App** button

  <img width="1741" height="418" alt="Screenshot 2026-01-08 130900" src="https://github.com/user-attachments/assets/f9f626e7-f84c-4783-9398-3459fde7fb0d" />

*
* <img width="1919" height="1030" alt="Screenshot 2026-01-08 130916" src="https://github.com/user-attachments/assets/9272bab4-9e6a-43dc-8cc7-cb1cee2a9d90" />



### Offline Test (DevTools)

* Open DevTools (F12)
* Go to **Application → Service Workers**
* Enable **Offline** checkbox
* Refresh page

  <img width="1776" height="997" alt="Screenshot 2026-01-08 142119" src="https://github.com/user-attachments/assets/8e921b39-3ac7-4622-bd66-0456da6caaae" />


---

## Final Checklist

* PWA install popup works
* App installs on desktop/mobile
* Offline page loads correctly
* Logo & manifest configured
* Service worker is active

---

