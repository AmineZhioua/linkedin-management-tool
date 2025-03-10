<?php

namespace App\Http\Controllers;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use App\Models\LinkedinUser;
use Illuminate\Support\Facades\Auth;


class LinkedInController extends Controller
{
    public function index()
    {
        return view('linkedin-login');
    }


    // Redirect function to redirect the user to LinkedIn
    public function redirect() {
        return Socialite::driver('linkedin')->redirect();
    }


    // Callback function to handle the response from LinkedIn
    public function callback() {
        // try {
            $linkedinUser = Socialite::driver('linkedin')->user();

            dd($linkedinUser);

        //     $userId = Auth::id();
        //     if(!$userId) {
        //         // WE CAN ADD A POPUP MESSAGE HERE
        //         return redirect()->route('login')->with('linkedin_error', "Vous devez s'authentifier!");
        //     }

        //     // Check if the user has already linked their LinkedIn account
        //     $existingLinkedinUser = LinkedinUser::where('user_id', $userId)->first();

        //     if ($existingLinkedinUser) {
        //         $existingLinkedinUser->update([
        //             'linkedin_id' => $linkedinUser->id,
        //             'linkedin_token' => $linkedinUser->token,
        //             'linkedin_refresh_token' => $linkedinUser->refreshToken,
        //         ]);
        //     } else {
        //         LinkedinUser::create([
        //             'user_id' => $userId,
        //             'linkedin_id' => $linkedinUser->id,
        //             'linkedin_token' => $linkedinUser->token,
        //             'linkedin_refresh_token' => $linkedinUser->refreshToken,
        //         ]);
        //     }

        //     return redirect()->route('home')->with('linkedin_success', 'Votre LinkedIn a été lié avec succès!');

        // } catch(\Exception $e) {
        //     return redirect()->route('subscription')
        //         ->with(
        //             'linkedin_error', 
        //             'Une erreur s\'est produite lors de la liaison de votre compte LinkedIn. Veuillez réessayer plus tard.'
        //         );
        // }
    }
}
