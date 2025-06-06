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
use App\Http\Controllers\EditAccountController;
use App\Http\Controllers\InfoFormController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Route for the suspension page
Route::get('/suspended', function () {
    return view('suspend');
})->name('suspended');

Auth::routes([
    'verify' => true,
]);

// Google Auth Routes
Route::get('auth/google/redirect', [GoogleController::class, 'redirect'])->name('google-auth');
Route::get('auth/google/callback', [GoogleController::class, 'callback']);

// Edit Account Routes (Middleware Removed for Testing)
Route::get('/edit-account', [EditAccountController::class, 'index'])->name('edit.account');
Route::put('/edit-account/profile', [EditAccountController::class, 'updateProfile'])->name('edit.account.profile');
Route::get('/edit-account/user-data', [EditAccountController::class, 'getUserData'])->name('edit.account.user-data');

// Routes & Middleware for the User Additional Information Page and requests
Route::middleware(['auth', 'verified', 'suspend'])->group(function() {
    // Route for ADDITIONAL INFORMATION
    Route::get('/user/additional-infomation', [App\Http\Controllers\InfoFormController::class, 'index'])->name('additional.info');
    // Route for the post request
    Route::post('/extra-info/add', [App\Http\Controllers\InfoFormController::class, 'addExtraInformation'])->name('add.extra.info');
    Route::put('/edit-account/extra-info/update', [InfoFormController::class, 'updateExtraInformation'])->name('edit.account.extra-info.update');
});



// Subscription Routes
Route::middleware(['auth', 'verified', 'check.additional.info', 'suspend'])->group(function () {
    Route::post('/session', [StripeController::class, 'session'])->name('session');
    Route::get('/success', [StripeController::class, 'success'])->name('success');
    Route::get('/cancel', [StripeController::class, 'cancel'])->name('cancel');

    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions');
    Route::post('/apply-coupon', [StripeController::class, 'applyCoupon'])->name('applyCoupon');
});

// LinkedIn Auth Routes
Route::middleware(['auth', 'verified', 'linkedin.valid', 'check.additional.info', 'suspend'])->group(function () {
    Route::get('/login-linkedin', [LinkedInController::class, 'index'])->name('login-linkedin');
    Route::get('/linkedin/auth', [LinkedInController::class, 'redirect'])->name('linkedin.auth');
    Route::get('/linkedin/callback', [LinkedInController::class, 'callback'])->name('linkedin.callback');
    Route::get('/linkedin/logout', [LinkedInController::class, 'logout'])->name('linkedin.logout');
    Route::get('/linkedin/delete', [LinkedInController::class, 'delete'])->name('linkedin.delete');

    Route::get('/dashboard/linkedin', [App\Http\Controllers\DashboardController::class, 'index'])
        ->middleware(['linkedin.account.exist', 'update.last.activity'])->name('dashboard');

    Route::delete('/linkedin/post/delete', [App\Http\Controllers\LinkedInController::class, 'deletePostFromLinkedin'])->name('delete.linkedin.post');
});

// LinkedIn Post Routes
Route::middleware(['auth', 'verified', 'linkedin.valid', 'linkedin.account.exist', 'check.additional.info', 'suspend'])->group(function () {
    Route::get('/linkedin-post', [App\Http\Controllers\LinkedinPostController::class, 'index'])->name('linkedin-post');
    Route::post('/linkedin/post-text', [LinkedInController::class, 'postTextOnly']);
    Route::post('/linkedin/registermedia', [LinkedInController::class, 'registerMedia']);
    Route::post('/linkedin/upload-media-binary', [LinkedInController::class, 'uploadMediaBinary']);
    Route::post('/linkedin/share-article', [LinkedInController::class, 'shareArticle']);
    Route::post('/linkedin/schedule-post', [LinkedInController::class, 'publish']);
    Route::post('/linkedin/schedule-single-post', [LinkedInController::class, 'publishSinglePost']);
    Route::post('/linkedin/create-campaign', [LinkedInController::class, 'createCampaign']);
});


// ROUTE FOR REGISTERING MULTI IMAGE POST MEDIA
Route::post('/linkedin/register-multi-image', [LinkedInController::class, 'registerMultiImageMedia']);


// Other Routes
Route::delete('/linkedin/delete-post', [App\Http\Controllers\LinkedinPostController::class, 'deletePost'])->name('delete.post');
Route::put('/linkedin/update-post', [App\Http\Controllers\LinkedinPostController::class, 'updatePost'])->name('update.post');
Route::post('/linkedin/save-draft', [App\Http\Controllers\LinkedinPostController::class, 'saveAsDraft'])->name('save.post.draft');

Route::get('/linkedin/get-campaign-posts', [App\Http\Controllers\DashboardController::class, 'getPostsForCampaign'])->name('get.posts.campaign');
Route::get('/linkedin/get-campaign-posts-for-day', [App\Http\Controllers\LinkedinPostController::class, 'getCampaignPostsForDay'])
    ->name('get.posts.campaign.for.day');

Route::get('/linkedin/get-social-actions', [App\Http\Controllers\KpiController::class, 'getSocialActions'])->name('get.social.actions');

Route::post('/linkedin/top-account', [App\Http\Controllers\KpiController::class, 'getTopActiveAccount'])->name('get.top.active.account');
Route::get('/linkedin/post-consistency', [App\Http\Controllers\KpiController::class, 'getPostConsistency'])->name('post.consistency');

Route::post('/notifications', [App\Http\Controllers\DashboardController::class, 'notification'])->name('notifications');
Route::get('/get-notifications', [App\Http\Controllers\NotificationsController::class, 'getNotifications'])->name('get.notifications');
Route::post('/linkedin/mark-all-as-read', [App\Http\Controllers\NotificationsController::class, 'markAllAsRead'])->name('mark.as.read');
Route::post('/boost-interaction/request', [App\Http\Controllers\DashboardController::class, 'requestBoostInteraction'])
    ->name('boost.interaction.request');
Route::delete('/boost-interaction/delete', [App\Http\Controllers\DashboardController::class, 'deleteBoostRequest'])->name('delete.boost.request');
Route::put('/boost-interaction/update', [App\Http\Controllers\DashboardController::class, 'updateBoostRequest'])->name("update.boost.request");

Route::get('/main/dashboard', [App\Http\Controllers\MainDashboardController::class, 'index'])->name('main.dashboard');
Route::delete('/campaign/delete', [App\Http\Controllers\LinkedInController::class, 'deleteCampaign'])->name('delete.campaign');




// Plateforme de Marque Routes
Route::middleware(['auth', 'verified', 'check.subscriptions', 'check.additional.info', 'suspend'])->group(function () {
    Route::get('/plateforme-marque', [App\Http\Controllers\PlateformeMarqueController::class, 'index'])->name('plateforme-marque');
    Route::post('/save-platform-info', [App\Http\Controllers\PlateformeMarqueController::class, 'store'])->name('plateforme.store');
    Route::get('/marque', [App\Http\Controllers\PlateformeMarqueController::class, 'showMarque'])->name('marque.show');
});

// Admin Routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
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
    Route::post('/users/suspend', [UserController::class, 'suspend'])->name('admin.users.suspend');
    Route::post('/users/remove-suspension', [UserController::class, 'removeSuspension'])->name('admin.users.remove-suspension');
});