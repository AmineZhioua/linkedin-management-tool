<?php

namespace App\Http\Middleware;

use App\Models\ExtraInformation;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckAdditionalInfo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $userInfoExists = ExtraInformation::where("user_id", $user->id)->first();

        if(!$userInfoExists) {
            return redirect()->route('additional.info');
        }

        return $next($request);
    }
}
