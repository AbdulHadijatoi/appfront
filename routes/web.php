<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', [ProductController::class, 'index']);

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/{product}', [ProductController::class, 'show'])->name('show');
    // more public routes for products can be added here
});

Route::middleware('guest')->group(function(){
    Route::get('/login', [AuthController::class, 'loginPage'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

require __DIR__.'/admin.php';