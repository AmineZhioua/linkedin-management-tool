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

// Route for Applying Coupon Code in Subscription Page
Route::post('/apply-coupon', [App\Http\Controllers\StripeController::class, 'applyCoupon'])->name('applyCoupon');


// Home Page route with middleware for checking if user is verified and has subscription
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')
    ->middleware('verified', 'auth', 'check.subscriptions');


// Route for Handling Subscription page, Payment and Cancelation
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/session', [StripeController::class, 'session'])->name('session');
    Route::get('/success', [StripeController::class, 'success'])->name('success');
    Route::get('/cancel', [StripeController::class, 'cancel'])->name('cancel');

    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions');
});


// Google Auth Routes
Route::get('auth/google/redirect', [GoogleController::class, 'redirect'])->name('google-auth');
Route::get('auth/google/callback', [GoogleController::class, 'callback']);



// LinkedIn Auth Route
Route::middleware(['auth', 'verified', 'linkedin.valid'])->group(function() {
    Route::get('/login-linkedin', [App\Http\Controllers\LinkedInController::class, 'index'])->name('login-linkedin');
    Route::get('/linkedin/auth', [App\Http\Controllers\LinkedInController::class, 'redirect'])->name('linkedin.auth');
    Route::get('/linkedin/callback', [App\Http\Controllers\LinkedInController::class, 'callback'])->name('linkedin.callback');
    Route::get('/linkedin/logout', [App\Http\Controllers\LinkedInController::class, 'logout'])->name('linkedin.logout');
    Route::get('/linkedin/delete', [App\Http\Controllers\LinkedInController::class, 'delete'])->name('linkedin.delete');

    Route::get('/dashboard/linkedin', [App\Http\Controllers\DashboardController::class, 'index'])
        ->middleware('linkedin.account.exist')->name('dashboard');
});


// Route for "Plateforme de Marque" Page
Route::get('/plateforme-marque', [App\Http\Controllers\PlateformeMarqueController::class, 'index'])->name('plateforme-marque');
Route::post('/save-platform-info', [App\Http\Controllers\PlateformeMarqueController::class, 'store'])->name('plateforme.store');


// Route for "Linkedin Post" Page
Route::get('/linkedin-post', [App\Http\Controllers\LinkedinPostController::class, 'index'])->name('linkedin-post');
Route::post('/linkedin/publish', [App\Http\Controllers\LinkedInController::class, 'postTextOnly']);