<?php

namespace App\Http\Controllers;

use App\Models\LinkedinCampaign;
use App\Models\LinkedinUser;
use App\Models\ScheduledLinkedinPost;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
}