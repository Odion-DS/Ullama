<?php

use App\Http\Controllers\SocialiteController;
use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return view('welcome');
//});

// SSO Authentication Routes
Route::get('/auth/callback', [SocialiteController::class, 'callback'])->name('auth.callback');

