<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription;
use App\Models\UserSubscription;
use Carbon\Carbon;


class SubscriptionController extends Controller
{
   public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        
        $userSubscriptions = UserSubscription::where('user_id', $user->id)
            ->whereDate('date_expiration', '>', Carbon::now())
            ->get();

        $subscriptions = Subscription::all();
        
        return view('subscription', [
            'userSubscriptions' => $userSubscriptions,
            'subscriptions' => $subscriptions,
        ]);
    }
}
