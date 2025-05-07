<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Log;


class NotificationsController extends Controller
{
    public function markAsRead(Request $request) {
        try {
            $validated = $request->validate([
                'notification_id' => "required|integer|exists:user_notifications,id",
            ]);
    
            // Find the notification by ID
            $notification = UserNotification::where("id", $validated['notification_id'])->first();
    
            if($notification) {
                $notification->read_at = now();
                $notification->save();
    
                return response()->json([
                    'success' => true,
                    'message' => 'Notification marquée comme lu !'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification non trouvée!'
                ], 404);
            }
        } catch(\Exception $e) {
            Log::error("Error marking notification as read", [
                "error" => $e->getMessage(),
                "trace" => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite: ' . $e->getMessage()
            ], 500);
        }
    }
}
