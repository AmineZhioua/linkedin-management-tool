<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UpdateLastActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Update last_activity only if it's older than 5 minutes to reduce DB writes
            if (!$user->last_activity || $user->last_activity->lt(now()->subMinutes(5))) {
                $user->update(['last_activity' => now()]);
            }
        }

        return $next($request);
    }
}