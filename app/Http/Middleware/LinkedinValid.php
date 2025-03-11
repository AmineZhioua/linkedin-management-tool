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
        
        // THIS CODE BELOW WAS CHANGED FOR REASONS OF PERFORMANCE
        // Check if the user has at least one valid LinkedIn subscription
        $hasValidLinkedinSubscription = UserSubscription::join('subscriptions', 'user_subscriptions.subscription_id', '=', 'subscriptions.id')
            ->where('user_subscriptions.user_id', $userId)
            ->whereDate('user_subscriptions.date_expiration', '>', Carbon::now())
            ->where('subscriptions.linkedin', true)
            ->exists();

        if (!$hasValidLinkedinSubscription) {
            return redirect()->route('subscriptions')
                ->with('linkedin_error', 'Vous devez souscrire à un nouveau abonnement LinkedIn pour continuer.');
        }

        return $next($request);
    }
}