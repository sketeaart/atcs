<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StreamController;

Route::view('/', 'welcome')->name('welcome');

Route::middleware(['web'])->group(function () {
    Route::middleware(['auth'])->group(function () {
        Route::view('/dashboard', 'pages.dashboard')->name('dashboard');
        Route::view('/maps', 'pages.maps')->name('maps');
        Route::view('/location', 'pages.location')->name('location');
        Route::view('/contact', 'pages.contact')->name('contact');
        Route::view('/notifications', 'pages.notifications')->name('notifications');
        Route::view('/messages', 'pages.messages')->name('messages');
        Route::get('/stream/{cctv}', [StreamController::class, 'show'])->name('stream.show');
    });
});

