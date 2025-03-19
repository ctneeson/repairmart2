<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\LoginController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'about')->name('about');

Route::get('/signup', [SignupController::class, 'create'])->name('signup');
Route::get('/login', [LoginController::class, 'create'])->name('login');

Route::get('/listings/search', [ListingController::class, 'search'])->name('listings.search');
Route::get('/listings/watchlist', [ListingController::class, 'watchlist'])->name('listings.watchlist');
Route::resource('listings', ListingController::class);

Route::resources([
    'listings' => ListingController::class,
    'orders' => OrderController::class,
]);
