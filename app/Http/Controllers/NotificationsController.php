<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class NotificationsController extends Controller
{
    public function getNotifications() {
        try {
            $user_id = Auth::id();

            $notifications = UserNotification::where('user_id', $user_id)
                ->orderBy('created_at', 'desc')
                ->get();

            $unreadCount = UserNotification::where('user_id', $user_id)
            ->whereNull('read_at')
            ->count();

            if ($notifications->isEmpty()) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Pas de Notifications pour le moment'
                ]);
            }
                
            return response()->json([
                "status" => 201,
                "data" => json_decode($notifications),
                "unread_count" => $unreadCount,
            ], 201);

        } catch(\Exception $e) {
            Log::error('Error retrieving notifications', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Une erreur s\'est produite lors de la récupération des notifications' . $e], 500);
        }
    }


    public function markAllAsRead() {
        $user_id = Auth::id();

        $unreadNotifs = UserNotification::where('user_id', $user_id)
            ->whereNull('read_at')->update(['read_at' => Carbon::now()->toDateTimeString()]);

            return response()->json([
                'status' => 200
            ], 200);
    }

    public function deleteNotification(Request $request) {
        try {
            $validated = $request->validate([
                "notification_id" => "required|integer|exists:user_notifications,id"
            ]);

            $notifToDelete = UserNotification::where("id", $validated['notification_id'])->first();

            if(!$notifToDelete) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Notification à supprimer non trouvé !'
                ], 404);
            }

            DB::beginTransaction();
            $notifToDelete->delete();
            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Notification supprimé avec succès !'
            ], 200);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Une erreur s\'est produite lors de la suppression !'
            ], 500);
        }
    }
}
