<?php

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

Route::post('/apply-coupon', [App\Http\Controllers\StripeController::class, 'applyCoupon'])->name('applyCoupon');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
    ->name('home')
    ->middleware('verified', 'auth', 'check.subscriptions');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/session', [StripeController::class, 'session'])->name('session');
    Route::get('success', [StripeController::class, 'success'])->name('success');
    Route::get('/cancel', [StripeController::class, 'cancel'])->name('cancel');
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions');
});

// Google Auth Routes
Route::get('auth/google/redirect', [GoogleController::class, 'redirect'])->name('google-auth');
Route::get('auth/google/callback', [GoogleController::class, 'callback']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
