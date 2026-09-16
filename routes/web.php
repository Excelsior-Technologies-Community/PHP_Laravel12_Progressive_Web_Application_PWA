<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;


/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('product.index');
});


/*
|--------------------------------------------------------------------------
| PWA Install Page
|--------------------------------------------------------------------------
*/

Route::get('/install', function () {
    return view('install');
})->name('pwa.install');


/*
|--------------------------------------------------------------------------
| Product Extra Features
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Export Products
|--------------------------------------------------------------------------
*/

Route::get(
    '/products/export',
    [ProductController::class, 'export']
)->name('product.export');


/*
|--------------------------------------------------------------------------
| PWA Offline Background Sync & API List
|--------------------------------------------------------------------------
*/

Route::post(
    '/products/sync',
    [ProductController::class, 'sync']
)->name('product.sync');

Route::get(
    '/products/api-list',
    [ProductController::class, 'apiList']
)->name('product.apiList');


/*
|--------------------------------------------------------------------------
| Bulk Delete Products
|--------------------------------------------------------------------------
*/

Route::post(
    '/products/bulk-delete',
    [ProductController::class, 'bulkDelete']
)->name('product.bulkDelete');


/*
|--------------------------------------------------------------------------
| Duplicate Product
|--------------------------------------------------------------------------
*/

Route::post(
    '/product/{product}/duplicate',
    [ProductController::class, 'duplicate']
)->name('product.duplicate');


/*
|--------------------------------------------------------------------------
| Product CRUD
|--------------------------------------------------------------------------
*/

Route::resource(
    'product',
    ProductController::class
);
