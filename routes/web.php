<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\WatchlistController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

// Auth routes for signup, login, password reset, and socialite
require __DIR__ . '/auth.php';
// Auth routes for emails
require __DIR__ . '/email.php';
// Auth routes for quotes
require __DIR__ . '/quotes.php';
// Auth routes for orders
require __DIR__ . '/orders.php';


// Logged in or logged out
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'about')->name('about');
Route::get('/listings/search', [ListingController::class, 'search'])->name('listings.search');


// AUTH: User must be logged in
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/activity-summary', [DashboardController::class, 'activitySummary'])
        ->name('dashboard.activitySummary');
    // User Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.updatePassword');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // User profile public view
    Route::get('/profile/{user}/show', [ProfileController::class, 'show'])
        ->name('profile.show');

    // Listing - show phone number
    Route::get('/listings/{listing}/phone', [ListingController::class, 'showPhone'])
        ->name('listings.showPhone');

    // Watchlist
    Route::get('/watchlist', [WatchlistController::class, 'index'])
        ->name('watchlist.index');
    Route::post('/watchlist/{listing}', [WatchlistController::class, 'storeDestroy'])
        ->name('watchlist.storeDestroy');

    // *** Account must be verified ***
    Route::middleware(['verified'])->group(function () {

        // *** Customer only ***
        Route::middleware(['role:customer'])->group(function () {
            Route::get('listings/create', [ListingController::class, 'create'])
                ->name('listings.create');
            Route::get('listings', [ListingController::class, 'index'])
                ->name('listings.index');
            Route::post('listings', [ListingController::class, 'store'])
                ->name('listings.store');
            Route::get('/listings/{listing}/edit', [ListingController::class, 'edit'])
                ->name('listings.edit');
            Route::put('/listings/{listing}', [ListingController::class, 'update'])
                ->name('listings.update');
            Route::delete('/listings/{listing}', [ListingController::class, 'destroy'])
                ->name('listings.destroy');
            Route::get('/listings/{listing}/relist', [ListingController::class, 'relist'])
                ->name('listings.relist');
            Route::get('/listings/{listing}/attachments', [ListingController::class, 'listingAttachments'])
                ->name('listings.attachments');
            Route::put('/listings/{listing}/attachments', [ListingController::class, 'updateAttachments'])
                ->name('listings.updateAttachments');
            Route::post('/listings/{listing}/attachments', [ListingController::class, 'addAttachments'])
                ->name('listings.addAttachments');
        });

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

Route::get('/listings/{listing}', [ListingController::class, 'show'])
    ->name('listings.show')
    ->where('listing', '[0-9]+');
