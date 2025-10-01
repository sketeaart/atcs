<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OtpController;

Route::middleware(['web','auth'])->group(function(){
    Route::get('/otp', [OtpController::class, 'show'])->name('otp.show');
    Route::get('/otp/send', [OtpController::class, 'send'])->name('otp.send');
    Route::post('/otp/verify', [OtpController::class, 'verify'])->name('otp.verify');
});

