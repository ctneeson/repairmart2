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

    // Order status and updates
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->name('orders.update-status');
    Route::patch('/orders/{order}/amount', [OrderController::class, 'updateAmount'])
        ->name('orders.update-amount');
    Route::post('/orders/{order}/feedback', [OrderController::class, 'addFeedback'])
        ->name('orders.feedback');

    // Order comments and attachments
    Route::post('/orders/{order}/comments', [OrderController::class, 'storeComment'])
        ->name('orders.comments.store');

    // Order attachment routes
    Route::get('/orders/{order}/attachments', [OrderController::class, 'attachments'])
        ->name('orders.attachments');
    Route::put('/orders/{order}/attachments', [OrderController::class, 'updateAttachments'])
        ->name('orders.updateAttachments');
    Route::post('/orders/{order}/attachments', [OrderController::class, 'addAttachments'])
        ->name('orders.addAttachments');
    Route::get('/orders/{order}/attachments/{attachment}/download', [OrderController::class, 'downloadAttachment'])
        ->name('orders.attachments.download');

});