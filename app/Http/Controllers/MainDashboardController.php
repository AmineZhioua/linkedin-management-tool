<?php

namespace App\Http\Controllers;

use App\Models\Boostinteraction;
use App\Models\ExtraInformation;
use App\Models\LinkedinCampaign;
use App\Models\LinkedinUser;
use App\Models\ScheduledLinkedinPost;
use App\Models\Subscription;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MainDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            Log::error('No authenticated user found');
            return redirect()->route('login');
        }

        $subscription = UserSubscription::where('user_id', $user->id)
            ->where('date_expiration', '>', now()->toDateString())
            ->select('boost_likes', 'boost_comments')
            ->first();

        // All Subscriptions 
        $subscriptions = Subscription::all();

        Log::info('Subscription: ' . json_encode($subscription));

        if (!$subscription) {
            Log::warning('No active subscription found for user ' . $user->id);
        }

        $userLinkedinAccounts = LinkedinUser::where('user_id', $user->id)->get();
        $userLinkedinPosts = ScheduledLinkedinPost::where('user_id', $user->id)->get();
        $userCampaigns = LinkedinCampaign::where('user_id', $user->id)->get();
        $userAdditionalInfo = ExtraInformation::where('user_id', $user->id)->first();
        $userBoostRequests = Boostinteraction::where('user_id', $user->id)->get();
        $userSubscription = UserSubscription::where('user_id', $user->id)->where('date_expiration', '>', now()->toDateString())->first();

        $userImage = $userAdditionalInfo ? "/storage/{$userAdditionalInfo->user_image}" : null;

        return view('main-dashboard', [
            'user' => $user,
            'userLinkedinAccounts' => $userLinkedinAccounts,
            'userLinkedinPosts' => $userLinkedinPosts,
            'userCampaigns' => $userCampaigns,
            'userAdditionalInfo' => $userAdditionalInfo,
            'userBoostRequests' => $userBoostRequests,
            'subscriptionData' => $subscription ?: (object)['boost_likes' => 0, 'boost_comments' => 0],
            'userImage' => $userImage,
            'userSubscription' => $userSubscription,
            'subscriptions' => $subscriptions
        ]);
    }
}