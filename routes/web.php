<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmailVerifyController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'about')->name('about');

// Logged in or logged out
Route::get('/listings/search', [ListingController::class, 'search'])->name('listings.search');

// User cannot be logged in
Route::middleware(['guest'])->group(function () {

    Route::get('/signup', [SignupController::class, 'create'])->name('signup');
    Route::post('/signup', [SignupController::class, 'store'])->name('signup.store');
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPassword'])
        ->name('password.reset-request');
    Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword'])
        ->name('password.reset-email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetPassword'])
        ->name('password.reset');
    Route::post('/reset-password/{token}', [PasswordResetController::class, 'resetPassword'])
        ->name('password.update');

});

// User must be logged in
Route::middleware(['auth'])->group(function () {

    // Logout
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // User Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.updatePassword');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Account must be verified
    Route::middleware(['verified'])->group(function () {
        // Listings
        Route::resource('listings', ListingController::class)
            ->except(['index', 'show']);
        Route::get('/listings/watchlist', [ListingController::class, 'watchlist'])->name('listings.watchlist');
        Route::get('/listings/{listing}/attachments', [ListingController::class, 'listingAttachments'])
            ->name('listings.attachments');
        Route::put('/listings/{listing}/attachments', [ListingController::class, 'updateAttachments'])
            ->name('listings.updateAttachments');
        Route::post('/listings/{listing}/attachments', [ListingController::class, 'addAttachments'])
            ->name('listings.addAttachments');

    });

});

Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
Route::get('/listings/{listing}', [ListingController::class, 'show'])->name('listings.show');

// Verify signup email
Route::get('/email/verify/{id}/{hash}', [EmailVerifyController::class, 'verify'])
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');
Route::get('/email/verify', [EmailVerifyController::class, 'notice'])
    ->middleware(['auth'])
    ->name('verification.notice');
Route::post('/email/verification-notification', [EmailVerifyController::class, 'send'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');
// Socialite login
Route::get('/login/oauth/{provider}', [SocialiteController::class, 'redirectToProvider'])
    ->name('login.oauth');
Route::get('/callback/oauth/{provider}', [SocialiteController::class, 'handleProviderCallback']);

Route::resources([
    'orders' => OrderController::class,
]);

Route::resource('quotes', QuoteController::class);
