<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'user_id' => "required|integer|exists:users,id",
            'campaign_id' => "required|integer|exists:linkedin_campaigns,id",
            'notification_id' => "required|integer|exists:user_notifications,id",
            'read_at' => 'required|date_format:Y-m-d H:i:s',
        ]);

        // $notification = UserNotification::where("id", $validated["notification_id"])
        //     ->where("user_id", $validated["user_id"])
        //     ->where("campaign_id", $validated["campaign_id"])
        //     ->first();

        $notification = UserNotification::where("id", $validated['notification_id']);

        if($notification) {
            $notification->read_at = Carbon::parse($validated['read_at']);
            $notification->save();

            return response()->json([
                'success' => true,
                'message' => 'Notification marquée comme lu !'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Une erreur s\'est produite !' 
        ], 500);
    }
}
