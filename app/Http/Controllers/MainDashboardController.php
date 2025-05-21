<?php

namespace App\Http\Controllers;

use App\Models\ExtraInformation;
use App\Models\LinkedinCampaign;
use App\Models\LinkedinUser;
use App\Models\ScheduledLinkedinPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MainDashboardController extends Controller
{
    public function index() {
        $user = Auth::user();

        $userLinkedinAccounts = LinkedinUser::where("user_id", $user->id)->get();
        $userLinkedinPosts = ScheduledLinkedinPost::where("user_id", $user->id)->get();
        $userCampaigns = LinkedinCampaign::where("user_id", $user->id)->get();
        $userAdditionalInfo = ExtraInformation::where("user_id", $user->id)->first();


        return view('main-dashboard', [
            "userLinkedinAccounts" => $userLinkedinAccounts,
            "userLinkedinPosts" => $userLinkedinPosts,
            "userCampaigns" => $userCampaigns,
            "userAdditionalInfo" => $userAdditionalInfo,
        ]);
    }
}
