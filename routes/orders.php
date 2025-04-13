<?php

use App\Http\Controllers\OrderController;

// Order routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Create order from quote
    Route::get('/quotes/{quote}/order/create', [OrderController::class, 'create'])
        ->name('orders.create');
    Route::post('/quotes/{quote}/order', [OrderController::class, 'store'])
        ->name('orders.store');

    // Regular order routes
    Route::get('/orders', [OrderController::class, 'index'])
        ->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])
        ->name('orders.show');
    Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])
        ->name('orders.edit');
    Route::put('/orders/{order}', [OrderController::class, 'update'])
        ->name('orders.update');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])
        ->name('orders.destroy');
});