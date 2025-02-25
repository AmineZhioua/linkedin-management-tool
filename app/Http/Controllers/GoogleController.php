<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
<<<<<<< HEAD
use Laravel\Socialite\Facades\Socialite; // ✅ Import Socialite
use App\Models\User; // ✅ Import User model
use Illuminate\Support\Facades\Auth; // ✅ Import Auth
use Illuminate\Support\Carbon; // ✅ Import Carbon for timestamps
=======
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
>>>>>>> bab1b1c (Google Auth fixed & DONE)

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

<<<<<<< HEAD
    public function callbackGoogle()
    {
        try {
            // Get the Google user data
            $google_user = Socialite::driver('google')->user();
            
            // Check if the user already exists based on Google ID
            $user = User::where('google_id', $google_user->getId())->first();

            if (!$user) {
                // If no user exists, create a new user and mark as verified
                $new_user = User::create([
                    'name' => $google_user->getName(),
                    'email' => $google_user->getEmail(),
                    'google_id' => $google_user->getId(),
                    'email_verified_at' => now(),
                ]);
                
                Auth::login($new_user);
                return redirect()->intended('home');
            } else {
                // If user exists, update their email_verified_at if not already set
                if (!$user->email_verified_at) {
                    $user->email_verified_at = Carbon::now(); // ✅ Mark email as verified
                    $user->save();
                }
                
                Auth::login($user);
                return redirect()->intended('home');
            }
        } catch (\Throwable $th) {
            // Handle any errors and display them
            dd('Something went wrong!', $th->getMessage());
        }
    }
}
=======

    public function callback() {
        $googleUser = Socialite::driver('google')->user();

        $user = User::updateOrCreate(
            ['google_id' => $googleUser->id],
            [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => Str::password(12),
                'email_verified_at' => now(),
            ]
        );

        Auth::login($user, true);

        return redirect()->route('home');
    }
}
>>>>>>> bab1b1c (Google Auth fixed & DONE)
