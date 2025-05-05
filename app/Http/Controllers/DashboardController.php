<?php

namespace App\Http\Controllers;

use App\Models\LinkedinCampaign;
use App\Models\LinkedinUser;
use App\Models\ScheduledLinkedinPost;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $linkedinUserList = LinkedinUser::where('user_id', $user->id)->get();
        $campaigns = LinkedinCampaign::where('user_id', $user->id)->get();
        $posts = ScheduledLinkedinPost::where('user_id', $user->id)->get();

        return view('dashboard', [
            'user' => $user,
            'linkedinUserList' => $linkedinUserList,
            'campaigns' => $campaigns,
            'posts' => $posts,
        ]);
    }


    public function getPostsForCampaign(Request $request) {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'campaign_id' => 'required|integer|exists:linkedin_campaigns,id',
        ]);

        $posts = ScheduledLinkedinPost::where('user_id', $user->id)
            ->where('campaign_id', $validated['campaign_id'])
            ->get();

        // If There Are No Posts for the Campaign
        if($posts->isEmpty()) {
            return response()->json(['message' => 'No posts found for this campaign.'], 404);
        }

        return response()->json($posts, 200);
    }


    public function addNotification(Request $request) {
        try {
            $validated = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'campaign_id' => 'required|integer|exists:linkedin_campaigns,id',
                'linkedin_user_id' => 'required|integer|exists:linkedin_users,id',
                'event_name' => 'required|string',
                'message' => 'required|string'
            ]);
    
            $notification = UserNotification::create([
                'user_id' => $validated['user_id'],
                'campaign_id' => $validated['campaign_id'],
                'linkedin_user_id' => $validated['linkedin_user_id'],
                'event_name' => $validated['event_name'],
                'message' => $validated['message']
            ]);
    
            return response()->json([
                'id' => $notification->id,
                'status' => 201,
                'message' => 'Notification a été créer avec succès !'
            ], 201);
        } catch(\Exception $e) {
            Log::error('Error creating Notification', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            return response()->json(['error' => 'Une erreur s\'est produite lors de l\'enregistrement de la notification ! ' . $e], 500);
        }
    }
}