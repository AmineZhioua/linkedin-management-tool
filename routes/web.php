<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\LinkedInController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SubscriptionController as AdminSubscriptionController;
use App\Http\Controllers\Admin\BoostinteractionController;



Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Auth::routes([
    'verify' => true,
]);

// Google Auth Routes
Route::get('auth/google/redirect', [GoogleController::class, 'redirect'])->name('google-auth');
Route::get('auth/google/callback', [GoogleController::class, 'callback']);

// Home Page route with middleware for checking if user is verified and has subscription
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')
    ->middleware('verified', 'auth', 'check.subscriptions');

// Route for Handling Subscription page, Payment and Cancelation
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/session', [StripeController::class, 'session'])->name('session');
    Route::get('/success', [StripeController::class, 'success'])->name('success');
    Route::get('/cancel', [StripeController::class, 'cancel'])->name('cancel');

    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions');
    // Route for Applying Coupon Code in Subscription Page
    Route::post('/apply-coupon', [StripeController::class, 'applyCoupon'])->name('applyCoupon');
});

// LinkedIn Auth Page Routes
Route::middleware(['auth', 'verified', 'linkedin.valid'])->group(function() {
    Route::get('/login-linkedin', [LinkedInController::class, 'index'])->name('login-linkedin');
    Route::get('/linkedin/auth', [LinkedInController::class, 'redirect'])->name('linkedin.auth');
    Route::get('/linkedin/callback', [LinkedInController::class, 'callback'])->name('linkedin.callback');
    Route::get('/linkedin/logout', [LinkedInController::class, 'logout'])->name('linkedin.logout');
    Route::get('/linkedin/delete', [LinkedInController::class, 'delete'])->name('linkedin.delete');

    Route::get('/dashboard/linkedin', [App\Http\Controllers\DashboardController::class, 'index'])
        ->middleware('linkedin.account.exist')->name('dashboard');
});

// Routes for "Plateforme de Marque" Page
Route::middleware(['auth', 'verified', 'check.subscriptions'])->group(function() {
    Route::get('/plateforme-marque', [App\Http\Controllers\PlateformeMarqueController::class, 'index'])->name('plateforme-marque');
    Route::post('/save-platform-info', [App\Http\Controllers\PlateformeMarqueController::class, 'store'])->name('plateforme.store');
    Route::get('/marque', [App\Http\Controllers\PlateformeMarqueController::class, 'showMarque'])->name('marque.show');
});

// Routes for "Linkedin Post" Page
Route::middleware(['auth', 'verified', 'linkedin.valid', 'linkedin.account.exist'])->group(function() {
    Route::get('/linkedin-post', [App\Http\Controllers\LinkedinPostController::class, 'index'])->name('linkedin-post');
    Route::post('/linkedin/post-text', [LinkedInController::class, 'postTextOnly']);
    Route::post('/linkedin/registermedia', [LinkedInController::class, 'registerMedia']);
    Route::post('/linkedin/upload-media-binary', [LinkedInController::class, 'uploadMediaBinary']);
    Route::post('/linkedin/share-article', [LinkedInController::class, 'shareArticle']);
    Route::post('/linkedin/schedule-post', [LinkedInController::class, 'publish']);
    Route::post('/linkedin/create-campaign', [LinkedInController::class, 'createCampaign']);
});

Route::delete('/linkedin/delete-post', [App\Http\Controllers\LinkedinPostController::class, 'deletePost'])->name('delete.post');
Route::put('/linkedin/update-post', [App\Http\Controllers\LinkedinPostController::class, 'updatePost'])->name('update.post');
Route::get('/linkedin/get-campaign-posts', [App\Http\Controllers\DashboardController::class, 'getPostsForCampaign'])->name('get.posts.campaign');
Route::get('/linkedin/get-campaign-posts-for-day', [App\Http\Controllers\LinkedinPostController::class, 'getCampaignPostsForDay'])
    ->name('get.posts.campaign.for.day');


Route::get('/linkedin/get-social-actions', [App\Http\Controllers\KpiController::class, 'getSocialActions'])->name('get.social.actions');
Route::post('/add-notification', [App\Http\Controllers\DashboardController::class, 'addNotification'])->name('add.notification');
Route::post('/notifications', [App\Http\Controllers\DashboardController::class, 'notification'])->name('notifications');

Route::get('/get-notifications', [App\Http\Controllers\DashboardController::class, 'getNotifications'])->name('get.notifications');
Route::post('/mark-as-read', [App\Http\Controllers\NotificationController::class])->name('mark.as.read');


// Admin Routes (without admin middleware)
Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin');
    })->name('admin.dashboard');

    Route::resource('users', UserController::class)->except(['show'])->names([
        'index' => 'admin.users.index',
        'create' => 'admin.users.create',
        'store' => 'admin.users.store',
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);
     Route::resource('subscriptions', AdminSubscriptionController::class)->except(['show'])->names([
        'index' => 'admin.subscriptions.index',
        'create' => 'admin.subscriptions.create',
        'store' => 'admin.subscriptions.store',
        'edit' => 'admin.subscriptions.edit',
        'update' => 'admin.subscriptions.update',
        'destroy' => 'admin.subscriptions.destroy',
    ]);

    // New routes for coupons
    Route::get('/subscriptions/active', [AdminSubscriptionController::class, 'active'])->name('admin.subscriptions.active');
    Route::get('/subscriptions/active/create', [AdminSubscriptionController::class, 'createActive'])->name('admin.subscriptions.active.create');
    Route::post('/subscriptions/active', [AdminSubscriptionController::class, 'storeActive'])->name('admin.subscriptions.active.store');
    Route::get('/subscriptions/active/{userSubscription}/edit', [AdminSubscriptionController::class, 'editActive'])->name('admin.subscriptions.active.edit');
    Route::put('/subscriptions/active/{userSubscription}', [AdminSubscriptionController::class, 'updateActive'])->name('admin.subscriptions.active.update');
    Route::delete('/subscriptions/active/{userSubscription}', [AdminSubscriptionController::class, 'destroyActive'])->name('admin.subscriptions.active.destroy');

    Route::get('/coupons', [AdminSubscriptionController::class, 'indexCoupons'])->name('admin.coupons.index');
    Route::get('/coupons/create', [AdminSubscriptionController::class, 'createCoupon'])->name('admin.coupons.create');
    Route::post('/coupons', [AdminSubscriptionController::class, 'storeCoupon'])->name('admin.coupons.store');
    Route::get('/coupons/{coupon}/edit', [AdminSubscriptionController::class, 'editCoupon'])->name('admin.coupons.edit');
    Route::put('/coupons/{coupon}', [AdminSubscriptionController::class, 'updateCoupon'])->name('admin.coupons.update');
    Route::delete('/coupons/{coupon}', [AdminSubscriptionController::class, 'destroyCoupon'])->name('admin.coupons.destroy');
    Route::resource('boostinteractions', BoostinteractionController::class)->only(['index', 'update', 'destroy'])->names([
        'index' => 'admin.boostinteractions.index',
        'update' => 'admin.boostinteractions.update',
        'destroy' => 'admin.boostinteractions.destroy',
    ]);
    Route::get('/profile', [UserController::class, 'profile'])->name('admin.profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('admin.profile.update');
});
