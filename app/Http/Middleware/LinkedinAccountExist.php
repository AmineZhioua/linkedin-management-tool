<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LinkedinUser;

class LinkedinAccountExist
{
    public function handle(Request $request, Closure $next) {
        $userId = Auth::id();

        if (!$userId) {
            return redirect()->route('login');
        }

        // Check if the user has at least one LinkedIn account linked
        $hasLinkedinAccount = LinkedinUser::where('user_id', $userId)->exists();

        if (!$hasLinkedinAccount) {
            return redirect()->route('login-linkedin')
                ->with('linkedin_error', 'Veuillez lier au moins un compte LinkedIn pour accéder à cette page');
        }

        return $next($request);
    }
}
