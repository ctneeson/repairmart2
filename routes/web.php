<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $person = [
        'name' => 'Jake Blues',
        'email' => 'jake@blues.com',
    ];
    $aboutPageURL = route(name: 'about');
    dd($aboutPageURL);
    dump($person);
    return view('welcome');
});

Route::view('/about', 'about')->name('about');

Route::get('/listing/{id}', function (string $id) {
    return "listingId = $id";
    // return view('listing');
})->whereNumber('id');