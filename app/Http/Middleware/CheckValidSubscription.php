<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Contract;

class CheckValidSubscription
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
    $userId = Auth::id();

    if ($userId) {
        $validContract = Contracts::where('user_id', $userId)
            ->where('date_expiration', '>', Carbon::now())
            ->exists();

        if (!$validContract) {
            return redirect()->route('subscriptions');
        }
    }

    return $next($request);
}}