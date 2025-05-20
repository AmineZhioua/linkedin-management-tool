<?php

namespace App\Http\Controllers;

use App\Models\LinkedinUser;
use App\Models\ScheduledLinkedinPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class KpiController extends Controller
{
    public function getSocialActions(Request $request) {
        $validator = Validator::make($request->all(), [
            'linkedin_user_id' => 'required|integer|exists:linkedin_users,id',
            'linkedin_token' => 'required|string',
            'post_id' => 'required|integer|exists:scheduled_linkedin_posts,id',
            'urn' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $validated = $validator->validated();
    
        $url = 'https://api.linkedin.com/v2/socialActions/' . $validated['urn'];
    
        $headers = [
            "Authorization: Bearer " . $validated['linkedin_token'],
            "Content-Type: application/json",
            "X-Restli-Protocol-Version: 2.0.0",
            "LinkedIn-Version: 202501"
        ];
    
        $curl = curl_init();
    
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_TIMEOUT, 300);
    
        $response = curl_exec($curl);
    
        if ($response === false) {
            $error = curl_error($curl);
            curl_close($curl);
            Log::error('cURL error: ' . $error);
            return response()->json(['error' => 'Failed to make request to LinkedIn API'], 500);
        }
    
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
    
        if ($httpCode == 200) {
            $data = json_decode($response, true);
            if ($data === null) {
                Log::error('Failed to decode JSON response: ' . $response);
                return response()->json(['error' => 'Invalid response from LinkedIn API'], 500);
            }
            return response()->json($data);
        } else {
            Log::error('LinkedIn API error: ' . $response);
            if ($httpCode == 401) {
                // TRANSLATE THEM TO FRENCH
                return response()->json(['error' => 'Unauthorized: Invalid or expired token'], 401);
            } elseif ($httpCode == 403) {
                return response()->json(['error' => 'Forbidden: Insufficient permissions'], 403);
            } elseif ($httpCode == 404) {
                return response()->json(['error' => 'Not Found: The Post does not exist'], 404);
            } else {
                return response()->json(['error' => 'An error occurred while retrieving social metadata'], $httpCode);
            }
        }
    }


    public function getTopActiveAccount(Request $request) {
        $validated = $request->validate([
            'accounts' => 'required|array',
            'accounts.*.id' => 'required|integer|exists:linkedin_users,id',
        ]);

        $accountIds = collect($validated['accounts'])->pluck('id')->toArray();

        $accounts = LinkedinUser::whereIn('id', $accountIds)->withCount([
                'posts as posts_count' => function ($query) {
                    $query->from('scheduled_linkedin_posts');
                },
                'campaigns as campaigns_count' => function ($query) {
                    $query->from('linkedin_campaigns');
                }
            ])->get();


        $topAccount = $accounts->sortByDesc(function ($account) {
            return $account->posts_count + $account->campaigns_count;
        })->first();

        return response()->json([
            'top_account' => [
                'id' => $topAccount->id,
                'picture' => $topAccount->linkedin_picture,
                'firstname' => $topAccount->linkedin_firstname,
                'lastname' => $topAccount->linkedin_lastname,
                'posts_count' => $topAccount->posts_count,
                'campaigns_count' => $topAccount->campaigns_count,
                'total_activity' => $topAccount->posts_count + $topAccount->campaigns_count,
            ]
        ], 200);
    }



    // public function getPostConsistency(Request $request) {
    //     $validator = Validator::make($request->all(), [
    //         'linkedin_user_id' => 'required|integer|exists:linkedin_users,id',
    //         'week_start' => 'nullable|date',
    //     ]);
    
    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 400);
    //     }
    
    //     $validated = $validator->validated();
    //     $userId = $validated['linkedin_user_id'];
    
    //     $weekStart = isset($validated['week_start'])
    //         ? Carbon::parse($validated['week_start'])->startOfWeek(Carbon::MONDAY)
    //         : Carbon::now()->startOfWeek(Carbon::MONDAY);
    //     $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
    
    //     $posts = ScheduledLinkedinPost::where('linkedin_user_id', $userId)
    //         ->whereIn('status', ['posted', 'queued', 'draft'])
    //         ->whereBetween('scheduled_time', [$weekStart, $weekEnd])
    //         ->get();
    
    //     Log::info('Posts found: ', [
    //         'count' => $posts->count(),
    //         'week_start' => $weekStart->toDateString(),
    //         'week_end' => $weekEnd->toDateString(),
    //         'posts' => $posts->pluck(['scheduled_time', 'status'])->toArray()
    //     ]);
    
    //     $dayMapping = [
    //         'Monday' => 'Lundi',
    //         'Tuesday' => 'Mardi',
    //         'Wednesday' => 'Mercredi',
    //         'Thursday' => 'Jeudi',
    //         'Friday' => 'Vendredi',
    //         'Saturday' => 'Samedi',
    //         'Sunday' => 'Dimanche',
    //     ];
    
    //     $daysOfWeek = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
    //     $postCounts = array_fill_keys($daysOfWeek, 0);
    
    //     foreach ($posts as $post) {
    //         $englishDay = Carbon::parse($post->scheduled_time)->format('l');
    //         $frenchDay = $dayMapping[$englishDay] ?? null;
    //         if ($frenchDay) {
    //             $postCounts[$frenchDay]++;
    //         }
    //     }
    
    //     return response()->json([
    //         'post_consistency' => $postCounts,
    //         'week_start' => $weekStart->toDateString(),
    //         'week_end' => $weekEnd->toDateString(),
    //     ], 200);
    // }

}
