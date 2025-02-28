<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Middleware\CheckValidSubscription;



Route::get('/', function () {
    return view('welcome');
})->name('welcome');


Auth::routes([
    'verify' => true,
]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
    ->name('home')
    ->middleware('verified', 'subscriptions');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/session', [StripeController::class, 'session'])->name('session');
    // Update the success route to use the new handleSubscriptionSuccess method
    Route::get('success', [StripeController::class, 'handleSubscriptionSuccess'])->name('subscription.success');
    // Route::get('/cancel', [StripeController::class, 'cancel'])->name('cancel');

    // Subscription Routes
    Route::get('/subscriptions', [SubscriptionController::class, 'index']);
});

// Google Auth Routes
Route::get('auth/google/redirect', [GoogleController::class, 'redirect'])->name('google-auth');

Route::get('auth/google/callback', [GoogleController::class, 'callback']);
