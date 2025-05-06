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

        $posts = ScheduledLinkedinPost::where('user_id', $user->id)->where('campaign_id', $validated['campaign_id'])->get();

        // If There Are No Posts for the Campaign
        if($posts->isEmpty()) {
            return response()->json(['message' => 'No posts found for this campaign.'], 404);
        }

        return response()->json($posts, 200);
    }


    public function addNotification(Request $request) {
        try {
            $validated = $request->validate([
                'notifications' => 'required|array',
                'notifications.*.user_id' => 'required|integer|exists:users,id',
                'notifications.*.campaign_id' => 'required|integer|exists:linkedin_campaigns,id',
                'notifications.*.linkedin_user_id' => 'required|integer|exists:linkedin_users,id',
                'notifications.*.event_name' => 'required|string',
                'notifications.*.message' => 'required|string',
            ]);
    
            $createdNotifications = [];
    
            foreach ($validated['notifications'] as $notificationData) {
                $notification = UserNotification::create([
                    'user_id' => $notificationData['user_id'],
                    'campaign_id' => $notificationData['campaign_id'],
                    'linkedin_user_id' => $notificationData['linkedin_user_id'],
                    'event_name' => $notificationData['event_name'],
                    'message' => $notificationData['message'],
                ]);
                $createdNotifications[] = $notification->id;
            }
    
            return response()->json([
                'status' => 201,
                'message' => count($createdNotifications) . ' notifications ont été créées avec succès !',
                'notification_ids' => $createdNotifications,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating Notifications', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            return response()->json(['error' => 'Une erreur s\'est produite lors de l\'enregistrement des notifications ! ' . $e], 500);
        }
    }


    public function getNotifications() {
        try {
            $user_id = Auth::id();

            $notifications = UserNotification::where('user_id', $user_id)->get();

            if(count($notifications) == 0) {
                return response()->json([
                    "status" => 200,
                    "message" => "Pas de notifications pour le moment"
                ]);
            }
                
            return response()->json([
                "status" => 201,
                "data" => json_decode($notifications),
            ]);

        } catch(\Exception $e) {
            Log::error('Error retrieving notifications', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Une erreur s\'est produite lors de la récupération des notifications' . $e], 500);
        }
    }
}