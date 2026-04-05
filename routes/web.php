<?php

use App\Http\Controllers\SocialiteController;
use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return view('welcome');
//});

// SSO Authentication Routes
Route::middleware('web')->group(function () {
    Route::get('/auth/redirect', [SocialiteController::class, 'redirect'])->name('auth.redirect');
    Route::get('/auth/callback', [SocialiteController::class, 'callback'])->name('auth.callback');
});

