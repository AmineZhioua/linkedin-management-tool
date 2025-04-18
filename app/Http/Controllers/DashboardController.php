<?php

namespace App\Http\Controllers;

use App\Models\LinkedinCampaign;
use App\Models\LinkedinUser;
use App\Models\ScheduledLinkedinPost;
use Illuminate\Support\Facades\Auth;

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
}