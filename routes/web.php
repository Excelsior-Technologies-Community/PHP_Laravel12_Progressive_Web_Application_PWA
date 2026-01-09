<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;


Route::get('/', function () {
    return redirect('/product'); //  PWA start page
});

Route::get('/install', function () {
    return view('install'); //  browser-only install page
});

Route::resource('product', ProductController::class);

