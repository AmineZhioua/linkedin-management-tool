<?php

namespace App\Http\Controllers;

use App\Models\Boostinteraction;
use App\Models\ExtraInformation;
use App\Models\LinkedinCampaign;
use App\Models\LinkedinUser;
use App\Models\ScheduledLinkedinPost;
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

        Log::info('User ID: ' . $user->id);
        Log::info('Current date: ' . now()->toDateString());

        $subscription = UserSubscription::where('user_id', $user->id)
            ->where('date_expiration', '>', now()->toDateString())
            ->select('boost_likes', 'boost_comments')
            ->first();

        Log::info('Subscription: ' . json_encode($subscription));

        if (!$subscription) {
            Log::warning('No active subscription found for user ' . $user->id);
        }

        $userLinkedinAccounts = LinkedinUser::where('user_id', $user->id)->get();
        $userLinkedinPosts = ScheduledLinkedinPost::where('user_id', $user->id)->get();
        $userCampaigns = LinkedinCampaign::where('user_id', $user->id)->get();
        $userAdditionalInfo = ExtraInformation::where('user_id', $user->id)->first();
        $userBoostRequests = Boostinteraction::where('user_id', $user->id)->get();

        return view('main-dashboard', [
            'user' => $user,
            'userLinkedinAccounts' => $userLinkedinAccounts,
            'userLinkedinPosts' => $userLinkedinPosts,
            'userCampaigns' => $userCampaigns,
            'userAdditionalInfo' => $userAdditionalInfo,
            'userBoostRequests' => $userBoostRequests,
            'subscription' => $subscription ?: (object)['boost_likes' => 0, 'boost_comments' => 0],
        ]);
    }
}