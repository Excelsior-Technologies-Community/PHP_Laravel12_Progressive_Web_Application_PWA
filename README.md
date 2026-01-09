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

This project demonstrates a Laravel 12 Progressive Web Application (PWA) with a complete Product CRUD (Create, Read, Update, Delete) module.

The application supports:

* PWA installation from the browser
* Offline fallback support
* Service Worker caching
* A dedicated browser-only install page
* Direct opening of the Product CRUD after PWA installation



---

## Features

* Installable PWA (Desktop & Mobile)
* Separate browser-only Install Page
* PWA starts directly on Product List page
* Offline fallback page
* Service Worker caching
* Web App Manifest support
* Product CRUD with image upload
* Dark UI layout
* Numeric-only price validation

---

## Folder Structure

```text
laravel-pwa/
├── app/
├── bootstrap/
│   └── providers.php
├── config/
│   └── pwa.php
├── public/
│   ├── sw.js
│   ├── offline.html
│   ├── manifest.json
│   ├── logo.png
│   └── products/
├── resources/
│   └── views/
│       ├── install.blade.php
│       ├── layouts/
│       │   └── app.blade.php
│       └── product/
│           ├── index.blade.php
│           ├── create.blade.php
│           └── edit.blade.php
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

        'start_url' => '/product',
        'scope'     => '/',


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

## Step 8: Create Browser-Only Install Page

File: resources/views/install.blade.php

```html
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

  <img width="1737" height="473" alt="Screenshot 2026-01-09 122642" src="https://github.com/user-attachments/assets/9cfb2141-8cc3-426c-9e66-3d8e37ae81d6" />


* Click **Install App** button

  <img width="1742" height="338" alt="Screenshot 2026-01-09 122743" src="https://github.com/user-attachments/assets/0d0d4f92-ac8c-4b5d-9bc5-0cacb24873c9" />





### Offline Test (DevTools)

* Open DevTools (F12)
* Go to **Application → Service Workers**
* Enable **Offline** checkbox
* Refresh page

  <img width="1694" height="886" alt="Screenshot 2026-01-09 123148" src="https://github.com/user-attachments/assets/bc89ec25-f008-41bf-affc-b5747ebbbf0d" />

*

 * <img width="1919" height="1033" alt="image" src="https://github.com/user-attachments/assets/6b184cfd-9402-4736-9c2f-6e58af8d7b2d" />


---


## Step 12: Product CRUD (Create, Read, Update, Delete)

This section explains how to add a **basic Product CRUD module** inside the **Laravel 12 PWA application**.
All pages work correctly in both **Browser** and **Installed PWA** mode.

---

## Step 12.1: Create Product Model & Migration

Run the following command:

```bash
php artisan make:model Product -m
```

### Migration File

**File:** `database/migrations/xxxx_create_products_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

Run migration:

```bash
php artisan migrate
```

---

## Step 12.2: Product Model

**File:** `app/Models/Product.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'image'
    ];
}
```

---

## Step 12.3: Product Controller

Create controller:

```bash
php artisan make:controller ProductController
```

**File:** `app/Http/Controllers/ProductController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->get();
        return view('product.index', compact('products'));
    }

    public function create()
    {
        return view('product.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required',
            'price'       => 'required|numeric',
            'description' => 'nullable',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('products'), $imageName);
            $data['image'] = $imageName;
        }

        Product::create($data);

        return redirect()->route('product.index');
    }

    public function edit(Product $product)
    {
        return view('product.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'        => 'required',
            'price'       => 'required|numeric',
            'description' => 'nullable',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image && file_exists(public_path('products/'.$product->image))) {
                unlink(public_path('products/'.$product->image));
            }

            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('products'), $imageName);
            $data['image'] = $imageName;
        }

        $product->update($data);

        return redirect()->route('product.index');
    }

    public function destroy(Product $product)
    {
        if ($product->image && file_exists(public_path('products/'.$product->image))) {
            unlink(public_path('products/'.$product->image));
        }

        $product->delete();

        return redirect()->route('product.index');
    }
}
```

---

## Step 12.4: Routes

**File:** `routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return redirect('/product'); // PWA start page
});

Route::get('/install', function () {
    return view('install'); // Browser-only install page
});

Route::resource('product', ProductController::class);
```

---

## Step 12.5: Layout File

**File:** `resources/views/layouts/app.blade.php`

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel PWA</title>

    @PwaHead
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            margin: 0;
            background: linear-gradient(135deg, #0f0f0f, #1c1c1c);
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 16px;
            border-radius: 12px;
            border: none;
            background: #1f1f1f;
            color: #fff;
        }

        .btn {
            padding: 10px 18px;
            border-radius: 25px;
            border: none;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-primary { background: #0d6efd; color: #fff; }
        .btn-secondary { background: #6c757d; color: #fff; }
        .btn-danger { background: #dc3545; color: #fff; }
    </style>
</head>

<body>

<h1>Laravel 12 PWA</h1>

<div class="container">
    @yield('content')
</div>

<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js');
    }
</script>

</body>
</html>
```

---

## Step 12.6: Product Blade Files

### Product List

**File:** `resources/views/product/index.blade.php`

```blade
@extends('layouts.app')

@section('content')

<h2>All Products</h2>

<a class="btn btn-primary" href="{{ route('product.create') }}">➕ Add Product</a>

<br><br>

@if($products->count() === 0)
    <p>No products available.</p>
@endif

<div class="card" style="padding:0;">
    <table style="width:100%; border-collapse:collapse;">
        <thead style="background:#f3f4f6;">
            <tr>
                <th style="padding:12px; color:#111;">#</th>
                <th style="padding:12px; color:#111;">Image</th>
                <th style="padding:12px; color:#111;">Name</th>
                <th style="padding:12px; color:#111;">Details</th>
                <th style="padding:12px; color:#111;">Price</th>
                <th style="padding:12px; color:#111;">Action</th>
            </tr>
        </thead>


        <tbody>
        @foreach($products as $index => $product)
            <tr style="border-bottom:1px solid #e5e7eb;">
                <td style="padding:12px;">{{ $index + 1 }}</td>

                {{-- IMAGE --}}
                <td style="padding:12px;">
                    @if($product->image)
                        <img src="{{ asset('products/'.$product->image) }}"
                             style="width:50px;height:50px;object-fit:cover;border-radius:6px;">
                    @else
                        -
                    @endif
                </td>

                {{-- NAME --}}
                <td style="padding:12px; font-weight:600;">
                    {{ $product->name }}
                </td>

                {{-- DETAILS --}}
                <td style="padding:12px; color:#555;">
                    {{ $product->description ?? '-' }}
                </td>

                {{-- PRICE --}}
                <td style="padding:12px;">
                    ₹{{ number_format($product->price, 2) }}
                </td>

                {{-- ACTION --}}
                <td style="padding:12px;">
                    <a class="btn btn-secondary btn-sm"
                       href="{{ route('product.edit', $product) }}">
                        Edit
                    </a>

                    <form method="POST"
                          action="{{ route('product.destroy', $product) }}"
                          style="display:inline;"
                          onsubmit="return confirm('Delete this product?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@endsection

```

---

### Add Product

**File:** `resources/views/product/create.blade.php`

```blade
@extends('layouts.app')

@section('content')

<h2>Add Product</h2>

<form method="POST" enctype="multipart/form-data" action="{{ route('product.store') }}">
    @csrf

    {{-- PRODUCT NAME --}}
    <input
        type="text"
        name="name"
        placeholder="Product Name"
        required
    >

    {{-- PRICE (ONLY NUMERIC ALLOWED) --}}
    <input
        type="number"
        name="price"
        placeholder="Price"
        step="0.01"
        min="0"
        required
        oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
    >

    {{-- DESCRIPTION (NOW VISIBLE & WORKING) --}}
    <textarea
        name="description"
        placeholder="Description"
        rows="4"
    ></textarea>

    {{-- IMAGE --}}
    <input type="file" name="image">

    <button class="btn btn-primary">Save Product</button>
</form>

@endsection

```

---

### Edit Product

**File:** `resources/views/product/edit.blade.php`

```blade
@extends('layouts.app')

@section('content')

<h2>Edit Product</h2>

<form method="POST"
      action="{{ route('product.update', $product) }}"
      enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- PRODUCT NAME --}}
    <label>Product Name</label>
    <input
        type="text"
        name="name"
        value="{{ old('name', $product->name) }}"
        required
        placeholder="Product Name"
    >

    {{-- PRICE (NUMERIC ONLY) --}}
    <label>Price</label>
    <input
        type="number"
        name="price"
        step="0.01"
        min="0"
        value="{{ old('price', $product->price) }}"
        required
        placeholder="Price"
        oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
    >

    {{-- DESCRIPTION --}}
    <label>Description</label>
    <textarea
        name="description"
        rows="4"
        placeholder="Product Description"
    >{{ old('description', $product->description) }}</textarea>

    {{-- IMAGE --}}
    <label>Change Image</label>
    <input type="file" name="image">

    {{-- IMAGE PREVIEW --}}
    @if($product->image)
        <img
            src="{{ asset('products/'.$product->image) }}"
            style="width:160px;margin-top:10px;border-radius:12px;"
        >
    @endif

    {{-- ACTION BUTTONS --}}
    <div class="actions" style="margin-top:20px;">
        <button type="submit" class="btn btn-primary">
            Update Product
        </button>

        <a href="{{ route('product.index') }}"
           class="btn btn-secondary">
            Cancel
        </a>
    </div>

</form>

@endsection

```

---

## Output

### Browser

* Product List: `http://127.0.0.1:8000/product`

  <img width="1735" height="539" alt="Screenshot 2026-01-09 123434" src="https://github.com/user-attachments/assets/a0cb2b59-7f9b-4d84-a70d-a85540f6fff2" />

* Add Product Page

  <img width="1717" height="684" alt="Screenshot 2026-01-09 123443" src="https://github.com/user-attachments/assets/2f34b8d0-1f86-46cc-9371-c12eb558ff22" />

* Edit Product Page

  <img width="1749" height="879" alt="Screenshot 2026-01-09 123457" src="https://github.com/user-attachments/assets/28d9dcdf-5fb9-4c90-b282-1a28a89058c6" />


### PWA

* Product List Page

  <img width="1919" height="575" alt="Screenshot 2026-01-09 123616" src="https://github.com/user-attachments/assets/be00e72c-f323-46ff-89b8-c94c3a958580" />

* Add Product Page

  <img width="1919" height="662" alt="Screenshot 2026-01-09 123624" src="https://github.com/user-attachments/assets/e3796186-3f59-44d1-9de6-e6424adc0c41" />

* Edit Product Page

  <img width="1919" height="842" alt="Screenshot 2026-01-09 123650" src="https://github.com/user-attachments/assets/7622f1c2-1094-4f3e-8e5b-c0dec6dcc0ca" />


---

✔ CRUD works

✔ PWA compatible

✔ Offline safe

---



