<?php

use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductPriceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí se registran las rutas de la API RESTful para la gestión de productos,
| divisas y precios en diferentes monedas.
|
*/

// Rutas de Divisas
Route::prefix('currencies')->group(function () {
    Route::get('/', [CurrencyController::class, 'index'])->name('currencies.index');
    Route::post('/', [CurrencyController::class, 'store'])->name('currencies.store');
    Route::get('/{id}', [CurrencyController::class, 'show'])->whereNumber('id')->name('currencies.show');
    Route::put('/{id}', [CurrencyController::class, 'update'])->whereNumber('id')->name('currencies.update');
});

// Rutas de Productos
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::post('/', [ProductController::class, 'store'])->name('products.store');
    Route::get('/{id}', [ProductController::class, 'show'])->whereNumber('id')->name('products.show');
    Route::put('/{id}', [ProductController::class, 'update'])->whereNumber('id')->name('products.update');
    Route::delete('/{id}', [ProductController::class, 'destroy'])->whereNumber('id')->name('products.destroy');

    // Rutas anidadas de Precios por Divisa para un Producto
    Route::get('/{id}/prices', [ProductPriceController::class, 'index'])->whereNumber('id')->name('products.prices.index');
    Route::post('/{id}/prices', [ProductPriceController::class, 'store'])->whereNumber('id')->name('products.prices.store');
});
