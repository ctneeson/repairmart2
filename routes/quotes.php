<?php

use App\Http\Controllers\QuoteController;

Route::middleware(['auth'])->group(function () {

    Route::get('/quotes', [QuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/create/{listing}', [QuoteController::class, 'create'])->name('quotes.create');
    Route::post('/quotes', [QuoteController::class, 'store'])->name('quotes.store');
    Route::get('/quotes/{quote}', [QuoteController::class, 'show'])->name('quotes.show');
    Route::get('/quotes/{quote}/edit', [QuoteController::class, 'edit'])->name('quotes.edit');
    Route::put('/quotes/{quote}', [QuoteController::class, 'update'])->name('quotes.update');
    Route::delete('/quotes/{quote}', [QuoteController::class, 'destroy'])->name('quotes.destroy');
    Route::get('/quotes/{quote}/attachments', [QuoteController::class, 'attachments'])->name('quotes.attachments');
    Route::put('/quotes/{quote}/attachments', [QuoteController::class, 'updateAttachments'])->name('quotes.updateAttachments');
    Route::post('/quotes/{quote}/attachments', [QuoteController::class, 'addAttachments'])->name('quotes.addAttachments');

});