<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SummaryController;
use App\Http\Controllers\Api\RoomsWithCctvController;

Route::get('/summary', SummaryController::class);
Route::get('/rooms-with-cctv', RoomsWithCctvController::class);

