<?php

namespace App\Http\Controllers;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use App\Models\LinkedinUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


class LinkedInController extends Controller
{
    public function index()
    {
        return view('linkedin-login');
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
            'scope' => 'r_basicprofile r_organization_social w_organization_social rw_organization_admin',
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
                return redirect()->route('subscription')
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
                return redirect()->route('subscription')
                    ->with('linkedin_error', 'Failed to retrieve access token from LinkedIn.');
            }
    
            $data = $response->json();
            $access_token = $data['access_token'] ?? null;
            $refresh_token = $data['refresh_token'] ?? null;
    
            if (!$access_token) {
                return redirect()->route('subscription')
                    ->with('linkedin_error', 'Failed to retrieve access token.');
            }
    
            // Fetch LinkedIn user profile
            $profileResponse = Http::withHeaders([
                'Authorization' => "Bearer $access_token",
            ])->get('https://api.linkedin.com/v2/me');
    
            if ($profileResponse->failed()) {
                return redirect()->route('subscription')
                    ->with('linkedin_error', 'Failed to fetch LinkedIn profile.');
            }
    
            $linkedinUser = $profileResponse->json();
            $linkedin_id = $linkedinUser['id'] ?? null;
            $linkedin_firstname = $linkedinUser['localizedFirstName'] ?? null;
            $linkedin_lastname = $linkedinUser['localizedLastName'] ?? null;
            $linkedin_picture = $linkedinUser['profilePicture']['displayImage'] ?? null;
    
            if (!$linkedin_id || !$linkedin_firstname || !$linkedin_lastname) {
                return redirect()->route('subscription')
                    ->with('linkedin_error', 'Une erreur s\'est produite lors de la récupération des données.');
            }
    
            // Link LinkedIn account to the authenticated user
            $userId = Auth::id();
            if (!$userId) {
                return redirect()->route('login')->with('linkedin_error', "Vous devez vous authentifier !");
            }
    
            $existingLinkedinUser = LinkedinUser::where('user_id', $userId)->first();
    
            if ($existingLinkedinUser) {
                $existingLinkedinUser->update([
                    'linkedin_id' => $linkedin_id,
                    'linkedin_firstname' => $linkedin_firstname,
                    'linkedin_lastname' => $linkedin_lastname,
                    'linkedin_token' => $access_token,
                    'linkedin_refresh_token' => $refresh_token,
                ]);
            } else {
                LinkedinUser::create([
                    'user_id' => $userId,
                    'linkedin_id' => $linkedin_id,
                    'linkedin_firstname' => $linkedin_firstname,
                    'linkedin_lastname' => $linkedin_lastname,
                    'linkedin_token' => $access_token,
                    'linkedin_refresh_token' => $refresh_token,
                ]);
            }
    
            return redirect()->route('home')->with('linkedin_success', 'Votre compte LinkedIn a été lié avec succès !');
    
        } catch (\Exception $e) {
            return redirect()->route('subscription')
                ->with('linkedin_error', 'Une erreur s\'est produite : ' . $e->getMessage());
        }
    }
    
}
