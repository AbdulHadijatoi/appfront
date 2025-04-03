<?php

use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'products'])->name('products');
        Route::get('add', [ProductController::class, 'addProductForm'])->name('add.product');
        Route::post('add', [ProductController::class, 'addProduct'])->name('add.product.submit');
        Route::get('edit/{id}', [ProductController::class, 'editProduct'])->name('edit.product');
        Route::put('edit/{id}', [ProductController::class, 'updateProduct'])->name('update.product');
        Route::delete('delete/{id}', [ProductController::class, 'deleteProduct'])->name('delete.product');
    });
});