<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\LoginController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'about')->name('about');

Route::get('/signup', [SignupController::class, 'create'])->name('signup');
Route::post('/signup', [SignupController::class, 'store'])->name('signup.store');
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

Route::get('/listings/search', [ListingController::class, 'search'])->name('listings.search');
Route::get('/listings/watchlist', [ListingController::class, 'watchlist'])->name('listings.watchlist');
Route::resource('listings', ListingController::class);
Route::get('/listings/{listing}/attachments', [ListingController::class, 'listingAttachments'])
    ->name('listings.attachments');
Route::put('/listings/{listing}/attachments', [ListingController::class, 'updateAttachments'])
    ->name('listings.updateAttachments');
Route::post('/listings/{listing}/attachments', [ListingController::class, 'addAttachments'])
    ->name('listings.addAttachments');

Route::resources([
    'listings' => ListingController::class,
    'orders' => OrderController::class,
]);

Route::resource('quotes', QuoteController::class);
