<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\GoogleController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Auth::routes([
    'verify' => true,
]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('verified');

<<<<<<< HEAD
Route::get('auth/google',[GoogleController::class,'redirect'])->name('google-auth');
Route::get('auth/google/call-back', [GoogleController::class,'callbackGoogle']);
=======

Route::get('auth/google/redirect', [GoogleController::class, 'redirect']);

Route::get('auth/google/callback', [GoogleController::class, 'callback']);
>>>>>>> bab1b1c (Google Auth fixed & DONE)
