<?php

use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/{username}', [FrontendController::class, 'index'])->name('index');

Route::get('/{username}/find-product', [App\Http\Controllers\ProductController::class, 'find'])->name('product.find');
Route::get('/{username}/find-product/result', [App\Http\Controllers\ProductController::class, 'findResults'])->name('product.find-result');
Route::get('/{username}/product/{id}', [App\Http\Controllers\ProductController::class, 'show'])->name('product.show');
Route::get('/{username}/cart', [App\Http\Controllers\TransactionController::class, 'cart'])->name('cart');
