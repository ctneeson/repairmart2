<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\WatchlistController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;

// Auth routes for signup, login, password reset, and socialite
require_once __DIR__ . '/auth.php';


// Logged in or logged out
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'about')->name('about');
Route::get('/listings/search', [ListingController::class, 'search'])->name('listings.search');


// AUTH: User must be logged in
Route::middleware(['auth'])->group(function () {

    // User Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.updatePassword');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Listing - show phone number
    Route::get('/listings/{listing}/phone', [ListingController::class, 'showPhone'])
        ->name('listings.showPhone');

    // *** Account must be verified ***
    Route::middleware(['verified'])->group(function () {
        // Listings
        Route::resource('listings', ListingController::class)
            ->except(['index', 'show']);
        Route::get('/listings/{listing}/attachments', [ListingController::class, 'listingAttachments'])
            ->name('listings.attachments');
        Route::put('/listings/{listing}/attachments', [ListingController::class, 'updateAttachments'])
            ->name('listings.updateAttachments');
        Route::post('/listings/{listing}/attachments', [ListingController::class, 'addAttachments'])
            ->name('listings.addAttachments');

        // Watchlist
        Route::get('/watchlist', [WatchlistController::class, 'index'])
            ->name('watchlist.index');
        Route::post('/watchlist/{listing}', [WatchlistController::class, 'storeDestroy'])
            ->name('watchlist.storeDestroy');


        // *** Admin Only ***
        Route::middleware(['role:admin'])->group(function () {
            // Profile management
            Route::get('/profile/search', [ProfileController::class, 'search'])->name('profile.search');
            Route::get('/profile/{user}', [ProfileController::class, 'index'])->name('profile.admin.index');
            Route::put('/profile/{user}', [ProfileController::class, 'update'])->name('profile.admin.update');
            Route::delete('/profile/{user}', [ProfileController::class, 'destroy'])->name('profile.admin.destroy');
        });

    });

});

Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
Route::get('/listings/{listing}', [ListingController::class, 'show'])->name('listings.show');

Route::resources([
    'orders' => OrderController::class,
]);

Route::resource('quotes', QuoteController::class);
