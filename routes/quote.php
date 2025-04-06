<?php

use App\Http\Controllers\QuoteController;


Route::middleware(['auth'])->group(function () {
    // Quote routes
    Route::get('/quotes/create/{listing}', [QuoteController::class, 'create'])->name('quotes.create');
    Route::post('/quotes', [QuoteController::class, 'store'])->name('quotes.store');
    Route::get('/quotes/{quote}', [QuoteController::class, 'show'])->name('quotes.show');
});