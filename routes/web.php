<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\SubscriptionController;


Route::get('/', function () {
    return view('welcome');
})->name('welcome');


Auth::routes([
    'verify' => true,
]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('verified');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/session', [StripeController::class, 'session'])->name('session');
    Route::get('/success', [StripeController::class, 'success'])->name('success');
    // Route::get('/cancel', [StripeController::class, 'cancel'])->name('cancel');

    // Subscription Routes
    Route::get('/subscriptions', [SubscriptionController::class, 'index']);
});

// Google Auth Routes
Route::get('auth/google/redirect', [GoogleController::class, 'redirect'])->name('google-auth');

Route::get('auth/google/callback', [GoogleController::class, 'callback']);