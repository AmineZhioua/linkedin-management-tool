<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\UserSubscription;
use App\Models\Subscription;

class LinkedinValid
{
    public function handle(Request $request, Closure $next)
    {
        $userId = Auth::id();

        if (!$userId) {
            return redirect()->route('login');
        }

        // Get all user subscriptions
        $userSubscriptions = UserSubscription::where('user_id', $userId)
            ->whereDate('date_expiration', '>', Carbon::now())
            ->get();

        $hasValidLinkedinSubscription = false;

        foreach ($userSubscriptions as $userSubscription) {
            $subscription = Subscription::find($userSubscription->subscription_id);

            if ($subscription && $subscription->linkedin) {
                $hasValidLinkedinSubscription = true;
                break;
            }
        }

        if (!$hasValidLinkedinSubscription) {
            return redirect()->route('subscriptions')
                ->with('linkedin_error', 'Vous devez souscrire à un abonnement LinkedIn pour continuer.');
        }

        return $next($request);
    }
}