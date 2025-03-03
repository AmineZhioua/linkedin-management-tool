<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Contract;

class CheckValidSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $userId = Auth::id();

        if (!$userId) {
            return redirect()->route('login');
        }

        $userHasSubscription = Contract::where('user_id', $userId)->exists();

        if (!$userHasSubscription) {
            return redirect()->route('subscriptions');
        }

        // Check if user has a valid subscription
        $validSubscription = Contract::where('user_id', $userId)
            ->where('date_expiration', '>', Carbon::now())
            ->exists();

        if (!$validSubscription) {
            return redirect()->route('subscriptions')
                ->with('expired', 'Your subscription has expired. Please renew your subscription to continue.');
        }

        return $next($request);
    }
}
