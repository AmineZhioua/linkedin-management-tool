<?php

namespace App\Http\Middleware;

use App\Models\ScheduledLinkedinPost;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPostNumberForKpis
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

        $userPostedPosts = ScheduledLinkedinPost::where("user_id", $user->id)
            ->where("status", "posted")
            ->count();

        if ($userPostedPosts < 10) {
            return response()->json([
                'error' => 'Vous devez avoir au moins 10 posts publiés pour activer les KPIs.'
            ], 403);
        }

        return $next($request);
    }
}