<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\LinkedinUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


class LinkedInController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $linkedinUserList = LinkedinUser::where('user_id', $user->id)->get();
        return view('linkedin-login', [
            'user' => $user,
            'linkedinUserList' => $linkedinUserList,
        ]);
    }


    // Redirect function to redirect the user to LinkedIn
    public function redirect() {
        $client_id=config('services.linkedin.client_id');
        $client_secret=config('services.linkedin.client_secret');
        $url = 'https://www.linkedin.com/oauth/v2/authorization';
        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => $client_id,
            'redirect_uri' => route('linkedin.callback'),
            'state' => 'random_string',
            'scope' => 'email w_member_social profile openid r_basicprofile',
        ]);

        return redirect($url . '?' . $query);
    }


    // Callback function to handle the response from LinkedIn
    public function callback(Request $request) {
        try {
            // Retrieve authorization code from LinkedIn
            $code = $request->query('code');
            $state = $request->query('state');
    
            if (!$code || !$state) {
                return redirect()->route('subscriptions')
                    ->with('linkedin_error', 'Invalid LinkedIn authorization response.');
            }
    
            // Exchange authorization code for an access token
            $client_id = config('services.linkedin.client_id');
            $client_secret = config('services.linkedin.client_secret');
            $redirect_uri = route('linkedin.callback');
    
            $response = Http::asForm()->post('https://www.linkedin.com/oauth/v2/accessToken', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $redirect_uri,
                'client_id' => $client_id,
                'client_secret' => $client_secret,
            ]);
    
            if ($response->failed()) {
                return redirect()->route('login-linkedin')
                    ->with('linkedin_error', 'Une erreur s\'est produite! Réessayer plus tard.');
            }
    
            $data = $response->json();
            $access_token = $data['access_token'] ?? null;
            $refresh_token = $data['refresh_token'] ?? null;
    
            if (!$access_token) {
                return redirect()->route('login-linkedin')
                    ->with('linkedin_error', 'Une erreur s\'est produite! Veuillez réessayer plus tard.');
            }
    
            // Fetch LinkedIn user profile
            $profileResponse = Http::withHeaders([
                'Authorization' => "Bearer $access_token",
            ])->get('https://api.linkedin.com/v2/me?projection=(id,localizedFirstName,localizedLastName,profilePicture(displayImage~:playableStreams))');
    
            if ($profileResponse->failed()) {
                return redirect()->route('login-linkedin')
                    ->with('linkedin_error', 'Une erreur s\'est produite lors de la récupération des données.');
            }
    
            $linkedinUser = $profileResponse->json();
            $linkedin_id = $linkedinUser['id'] ?? null;
            $linkedin_firstname = $linkedinUser['localizedFirstName'] ?? null;
            $linkedin_lastname = $linkedinUser['localizedLastName'] ?? null;
            $linkedin_picture = $linkedinUser['profilePicture']['displayImage~']['elements'][0]['identifiers'][0]['identifier'] ?? null;
    
            if (!$linkedin_id || !$linkedin_firstname || !$linkedin_lastname || !$linkedin_picture) {
                return redirect()->route('login-linkedin')
                    ->with('linkedin_error', 'Une erreur s\'est produite lors de la récupération des données.');
            }
    
            // Link LinkedIn account to the authenticated user
            $userId = Auth::id();
            if (!$userId) {
                return redirect()->route('login')->with('linkedin_error', "Vous devez vous authentifier !");
            }

            // Check if the LinkedIn ID is already linked to another user
            $existingAccount = LinkedinUser::where('linkedin_id', $linkedin_id)->first();

            if ($existingAccount && $existingAccount->user_id !== $userId) {
                return redirect()->route('login-linkedin')
                    ->with('linkedin_error', 'Ce compte LinkedIn est déjà lié à un autre utilisateur.');
            }
                
            $existingLinkedinUser = LinkedinUser::where('user_id', $userId)->first();
    
            if ($existingLinkedinUser) {
                $existingLinkedinUser->update([
                    'linkedin_id' => $linkedin_id,
                    'linkedin_firstname' => $linkedin_firstname,
                    'linkedin_lastname' => $linkedin_lastname,
                    'linkedin_picture' => $linkedin_picture,
                    'linkedin_token' => $access_token,
                    'linkedin_refresh_token' => $refresh_token,
                ]);
            } else {
                LinkedinUser::create([
                    'user_id' => $userId,
                    'linkedin_id' => $linkedin_id,
                    'linkedin_firstname' => $linkedin_firstname,
                    'linkedin_lastname' => $linkedin_lastname,
                    'linkedin_picture' => $linkedin_picture,
                    'linkedin_token' => $access_token,
                    'linkedin_refresh_token' => $refresh_token,
                ]);
            }
    
            return redirect()->route('home')->with('linkedin_success', 'Votre compte LinkedIn a été lié avec succès !');
    
        } catch (\Exception $e) {
            return redirect()->route('login-linkedin')
                ->with('linkedin_error', 'Une erreur s\'est produite : ' . $e->getMessage());
        }
    }
    
}
