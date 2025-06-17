<?php

namespace App\Http\Controllers;

use App\Models\Boostinteraction;
use App\Models\LinkedinCampaign;
use App\Models\LinkedinUser;
use App\Models\ScheduledLinkedinPost;
use App\Models\UserNotification;
use App\Models\UserSubscription; // Correct import
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
  public function index()
{
    $user = Auth::user();
    if (!$user) {
        return redirect()->route('login');
    }

    // Log for debugging
    Log::info('User ID: ' . $user->id);
    Log::info('Current date: ' . now()->toDateString());

    $subscription = UserSubscription::where('user_id', $user->id)
        ->where('date_expiration', '>', now()->toDateString())
        ->select('boost_likes', 'boost_comments')
        ->first();

    Log::info('Subscription: ' . json_encode($subscription));

    if (!$subscription) {
        Log::warning('No active subscription found for user ' . $user->id);
    }

    $linkedinUserList = LinkedinUser::where('user_id', $user->id)->get();
    $campaigns = LinkedinCampaign::where('user_id', $user->id)->get();
    $posts = ScheduledLinkedinPost::where('user_id', $user->id)->get();
    $boostRequests = Boostinteraction::where('user_id', $user->id)->get();

    return view('dashboard', [
        'user' => $user,
        'linkedinUserList' => $linkedinUserList,
        'campaigns' => $campaigns,
        'posts' => $posts,
        'subscription' => $subscription ?: (object)['boost_likes' => 0, 'boost_comments' => 0],
        'boostRequests' => $boostRequests
    ]);
}
public function fetchDashboardData()
{
    $user = Auth::user();
    if (!$user) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $subscription = UserSubscription::where('user_id', $user->id)
        ->where('date_expiration', '>', now()->toDateString())
        ->select('boost_likes', 'boost_comments')
        ->first();

    $linkedinUserList = LinkedinUser::where('user_id', $user->id)->get();
    $boostRequests = Boostinteraction::where('user_id', $user->id)->get();

    return response()->json([
        'boostRequests' => $boostRequests,
        'linkedinAccounts' => $linkedinUserList,
        'subscriptionData' => $subscription ?: (object)[
            'boost_likes' => 0,
            'boost_comments' => 0
        ]
    ]);
}

    public function getPostsForCampaign(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'campaign_id' => 'required|integer|exists:linkedin_campaigns,id',
        ]);

        $posts = ScheduledLinkedinPost::where('user_id', $user->id)->where('campaign_id', $validated['campaign_id'])->get();

        if ($posts->isEmpty()) {
            return response()->json(['message' => 'No posts found for this campaign.'], 404);
        }

        return response()->json($posts, 200);
    }

    public function notification(Request $request)
    {
        try {
            Log::info('Données reçues :', $request->all());
            $validated = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'linkedin_user_id' => 'required|integer|exists:linkedin_users,id',
                'campaign_id' => 'nullable|integer|exists:linkedin_campaigns,id',
                'event_name' => 'required|string',
                'message' => 'required|string',
            ]);

            $notification = UserNotification::create([
                'user_id' => $validated['user_id'],
                'campaign_id' => $validated['campaign_id'] ?? null,
                'linkedin_user_id' => $validated['linkedin_user_id'],
                'event_name' => $validated['event_name'],
                'message' => $validated['message'],
            ]);

            return response()->json([
                'status' => 201,
                'message' => 'notification a été créée avec succès !',
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating Notifications', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            return response()->json([
                'error' => 'Une erreur s\'est produite lors de l\'enregistrement des notifications ! ' . $e->getMessage()
            ], 500);
        }
    }

    public function requestBoostInteraction(Request $request)
    {
        try {
            $validated = $request->validate([
                "post_id" => "required|integer|exists:scheduled_linkedin_posts,id",
                "linkedin_user_id" => "required|integer|exists:linkedin_users,id",
                "post_url" => "required|url",
                "nb_likes" => "required|integer|min:0",
                "nb_comments" => "required|integer|min:0",
                "message" => "nullable|string"
            ]);

            $userId = Auth::id();

            // Fetch the user's active subscription
            $userSubscription = UserSubscription::where('user_id', $userId)
                ->whereDate('date_expiration', '>', now())
                ->first();

            if (!$userSubscription) {
                return response()->json([
                    "status" => 403,
                    "message" => "Vous n'avez pas d'abonnement actif !"
                ], 403);
            }

            // Check if requested likes and comments exceed available amounts
            if ($validated['nb_likes'] > $userSubscription->boost_likes) {
                return response()->json([
                    "status" => 400,
                    "message" => "Vous ne pouvez pas demander autant de likes. Vous avez {$userSubscription->boost_likes} likes disponibles."
                ], 400);
            }

            if ($validated['nb_comments'] > $userSubscription->boost_comments) {
                return response()->json([
                    "status" => 400,
                    "message" => "Vous ne pouvez pas demander autant de commentaires. Vous avez {$userSubscription->boost_comments} commentaires disponibles."
                ], 400);
            }

            $post = ScheduledLinkedinPost::findOrFail($validated["post_id"]);

            DB::beginTransaction();

            try {
                $boost = Boostinteraction::create([
                    "user_id" => $userId,
                    "linkedin_user_id" => $validated["linkedin_user_id"],
                    "post_id" => $post->id,
                    "post_url" => $validated["post_url"],
                    "status" => "pending",
                    "nb_likes" => $validated["nb_likes"],
                    "nb_comments" => $validated["nb_comments"],
                    "message" => $validated["message"] ?? null
                ]);

                // Decrement boost_likes and boost_comments
                $userSubscription->decrement('boost_likes', $validated['nb_likes']);
                $userSubscription->decrement('boost_comments', $validated['nb_comments']);

                DB::commit();

                return response()->json([
                    "status" => 201,
                    "message" => "Votre requête de Boost a été envoyée à l'administrateur !"
                ], 201);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Post not found for boost request', [
                'post_id' => $request->post_id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                "status" => 404,
                "message" => "Post non trouvé !"
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error while creating Boost request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'post_id' => $request->post_id ?? null,
                'user_id' => Auth::id() ?? null
            ]);

            return response()->json([
                "status" => 500,
                "message" => "Une erreur s'est produite lors de l'envoi de la requête !"
            ], 500);
        }
    }

    public function deleteBoostRequest(Request $request)
    {
        try {
            $validated = $request->validate([
                "request_id" => "required|integer|exists:boostinteractions,id"
            ]);

            $boostRequest = Boostinteraction::where('id', $validated["request_id"])->first();

            if (!$boostRequest) {
                return response()->json([
                    "message" => "Demande de Boost non trouvé !",
                ], 404);
            }

            DB::beginTransaction();
            try {
                // Restore boost_likes and boost_comments to user_subscription
                $userSubscription = UserSubscription::where('user_id', $boostRequest->user_id)
                    ->whereDate('date_expiration', '>', now())
                    ->first();

                if ($userSubscription) {
                    $userSubscription->increment('boost_likes', $boostRequest->nb_likes);
                    $userSubscription->increment('boost_comments', $boostRequest->nb_comments);
                }

                $boostRequest->delete();
                DB::commit();

                return response()->json([
                    "message" => "Demande de Boost supprimé avec succès !"
                ], 200);
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error deleting Boost request', [
                'error' => $e->getMessage(),
                'request_id' => $request->request_id ?? null,
                'user_id' => Auth::id() ?? null
            ]);

            return response()->json([
                "message" => "Une erreur s'est produite ! Veuillez réessayer",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function updateBoostRequest(Request $request)
    {
        try {
            $validated = $request->validate([
                "request_id" => "required|integer|exists:boostinteractions,id",
                "nb_likes" => "required|integer|min:0",
                "nb_comments" => "required|integer|min:0",
                "message" => "nullable|string"
            ]);

            $userId = Auth::id();

            // Fetch the user's active subscription
            $userSubscription = UserSubscription::where('user_id', $userId)
                ->whereDate('date_expiration', '>', now())
                ->first();

            if (!$userSubscription) {
                return response()->json([
                    "status" => 403,
                    "message" => "Vous n'avez pas d'abonnement actif !"
                ], 403);
            }

            $boostRequest = Boostinteraction::where("id", $validated["request_id"])->first();

            if (!$boostRequest) {
                return response()->json([
                    "status" => 404,
                    "message" => "Demande de Boost non trouvé !"
                ], 404);
            }

            // Calculate the difference in likes and comments
            $likesDifference = $validated['nb_likes'] - $boostRequest->nb_likes;
            $commentsDifference = $validated['nb_comments'] - $boostRequest->nb_comments;

            // Check if the differences exceed available amounts
            if ($likesDifference > $userSubscription->boost_likes) {
                return response()->json([
                    "status" => 400,
                    "message" => "Vous ne pouvez pas demander autant de likes. Vous avez {$userSubscription->boost_likes} likes disponibles."
                ], 400);
            }

            if ($commentsDifference > $userSubscription->boost_comments) {
                return response()->json([
                    "status" => 400,
                    "message" => "Vous ne pouvez pas demander autant de commentaires. Vous avez {$userSubscription->boost_comments} commentaires disponibles."
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Update the boost request
                $boostRequest->nb_likes = $validated['nb_likes'];
                $boostRequest->nb_comments = $validated['nb_comments'];
                $boostRequest->message = $validated['message'];
                $boostRequest->save();

                // Adjust boost_likes and boost_comments in user_subscription
                if ($likesDifference != 0) {
                    $userSubscription->decrement('boost_likes', $likesDifference);
                }
                if ($commentsDifference != 0) {
                    $userSubscription->decrement('boost_comments', $commentsDifference);
                }

                DB::commit();

                return response()->json([
                    "status" => 200,
                    "message" => "Demande de Boost modifié avec succès !"
                ], 200);
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error updating Boost request', [
                'error' => $e->getMessage(),
                'request_id' => $request->request_id ?? null,
                'user_id' => Auth::id() ?? null
            ]);

            return response()->json([
                "status" => 500,
                "message" => "Une erreur s'est produite ! Veuillez réessayer",
                "error" => $e->getMessage()
            ], 500);
        }
    }
}