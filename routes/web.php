<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\HelloController;


Route::get('/', [HomeController::class, 'index'])->name('home');


Route::view('/about', 'about')->name('about');


Route::resources([
    'listings' => ListingController::class,
    'orders' => OrderController::class,
]);
