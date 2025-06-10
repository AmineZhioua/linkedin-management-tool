<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Boostinteraction;
use App\Models\LinkedinCampaign;
use App\Models\ScheduledLinkedinPost;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminMetricsController extends Controller
{
    /**
     * Get user metrics for the dashboard chart.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserMetrics(Request $request)
    {
        try {
            $filter = $request->input('filter', 'suspended');
            $data = [];
            $labels = [];

            if ($filter === 'email_verified') {
                $verifiedCount = User::whereNotNull('email_verified_at')->count();
                $unverifiedCount = User::whereNull('email_verified_at')->count();
                $labels = ['Verified', 'Not Verified'];
                $data = [$verifiedCount, $unverifiedCount];
            } elseif ($filter === 'active_last_hour') {
                $activeCount = User::where('last_activity', '>=', Carbon::now()->subHour())->count();
                $inactiveCount = User::where('last_activity', '<', Carbon::now()->subHour())
                                    ->orWhereNull('last_activity')
                                    ->count();
                $labels = ['Active Last Hour', 'Inactive'];
                $data = [$activeCount, $inactiveCount];
            } else {
                $suspendedCount = User::whereNotNull('suspend_end')
                                     ->where('suspend_end', '>', Carbon::now())
                                     ->count();
                $notSuspendedCount = User::whereNull('suspend_end')
                                        ->orWhere('suspend_end', '<=', Carbon::now())
                                        ->count();
                $labels = ['Suspended', 'Not Suspended'];
                $data = [$suspendedCount, $notSuspendedCount];
            }

            return response()->json([
                'labels' => $labels,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getUserMetrics: ' . $e->getMessage() . ' | Stack: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Server error occurred'], 500);
        }
    }

    /**
     * Get campaign metrics for the dashboard chart.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCampaignMetrics()
    {
        try {
            $totalCampaigns = LinkedinCampaign::count();
            $campaignsToday = LinkedinCampaign::whereDate('created_at', Carbon::today())->count();
            $labels = ['Total Campaigns', 'Campaigns Created Today'];
            $data = [$totalCampaigns, $campaignsToday];

            return response()->json([
                'labels' => $labels,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getCampaignMetrics: ' . $e->getMessage() . ' | Stack: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Server error occurred'], 500);
        }
    }

    /**
     * Get boost interaction metrics for the dashboard chart.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBoostInteractionMetrics()
    {
        try {
            $acceptedCount = Boostinteraction::where('status', 'accepted')->count();
            $pendingCount = Boostinteraction::where('status', 'pending')->count();
            $declinedCount = Boostinteraction::where('status', 'declined')->count();
            $labels = ['Accepted', 'Pending', 'Declined'];
            $data = [$acceptedCount, $pendingCount, $declinedCount];

            return response()->json([
                'labels' => $labels,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getBoostInteractionMetrics: ' . $e->getMessage() . ' | Stack: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Server error occurred'], 500);
        }
    }

    /**
     * Get post metrics for the dashboard chart.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPostMetrics()
    {
        try {
            $totalPosts = ScheduledLinkedinPost::count();
            $postsScheduledToday = ScheduledLinkedinPost::whereDate('scheduled_time', Carbon::today())->count();
            $labels = ['Total Posts', 'Posts Scheduled Today'];
            $data = [$totalPosts, $postsScheduledToday];

            return response()->json([
                'labels' => $labels,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getPostMetrics: ' . $e->getMessage() . ' | Stack: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Server error occurred'], 500);
        }
    }
}