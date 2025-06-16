<?php

namespace App\Http\Controllers;

use App\Models\Boostinteraction;
use App\Models\LinkedinCampaign;
use App\Models\LinkedinUser;
use App\Models\ScheduledLinkedinPost;
use App\Models\UserNotification;
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


    public function notification(Request $request) {
        try {
            $validated = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'campaign_id' => 'nullable|integer',
                'linkedin_user_id' => 'required|integer|exists:linkedin_users,id',
                'event_name' => 'required|string',
                'message' => 'required|string',
            ]);
        
                $notification = UserNotification::create([
                    'user_id' => $validated['user_id'],
                    'campaign_id' => $validated['campaign_id'],
                    'linkedin_user_id' => $validated['linkedin_user_id'],
                    'event_name' => $validated['event_name'],
                    'message' => $validated['message'],
                ]);

                return response()->json([
                    'status' => 201,
                    'message' =>' notification a été créée avec succès !',
                ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating Notifications', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            return response()->json([
                'error' => 'Une erreur s\'est produite lors de l\'enregistrement des notifications ! ' . $e
            ], 500);
        }
    }


    public function requestBoostInteraction(Request $request) {
        try {
            $validated = $request->validate([
                "post_id" => "required|integer|exists:scheduled_linkedin_posts,id",
                "linkedin_user_id" => "required|integer|exists:linkedin_users,id",
                "post_url" => "required|url",
                "nb_likes" => "required|integer",
                "nb_comments" => "required|integer",
                "message" => "nullable|string"
            ]);
        
            $userId = Auth::id();
        
            $post = ScheduledLinkedinPost::findOrFail($validated["post_id"]);
            
            DB::beginTransaction();
            
            try {
                $boost = BoostInteraction::create([
                    "user_id" => $userId,
                    "linkedin_user_id" => $validated["linkedin_user_id"],
                    "post_id" => $post->id,
                    "post_url" => $validated["post_url"],
                    "status" => "pending",
                    "nb_likes" => $validated["nb_likes"],
                    "nb_comments" => $validated["nb_comments"],
                    "message" => $validated["message"] ?? null
                ]);
                                
                DB::commit();
                
                return response()->json([
                    "status" => 201,
                    "message" => "Votre requête de Boost a été envoyée à l'administrateur !"
                ], 201);
                
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Post not found for boost request', [
                'post_id' => $request->post_id,
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                "status" => 404,
                "message" => "Post non trouvé !"
            ], 404);
        } catch(\Exception $e) {
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


    public function deleteBoostRequest(Request $request) {
        try {
            $validated = $request->validate([
                "request_id" => "required|integer|exists:boostinteractions,id"
            ]);

            $boostRequest = Boostinteraction::where('id', $validated["request_id"])->first();
            
            if(!$boostRequest) {
                return response()->json([
                    "message" => "Demande de Boost non trouvé !",
                ], 404);
            }

            DB::beginTransaction();
            $boostRequest->delete();
            DB::commit();

            return response()->json([
                "message" => "Demande de Boost supprimé avec succès !"
            ], 200);
        } catch(\Exception $e) {
            DB::rollback();

            return response()->json([
                "message" => "Une erreur s\'est produite ! Veuillez réessayer",
                "error_occurred" => $e->getMessage(),
            ], 500);
        }
    }



    public function updateBoostRequest(Request $request) {
        try {
            $validated = $request->validate([
                "request_id" => "required|integer|exists:boostinteractions,id",
                "nb_likes" => "required|integer",
                "nb_comments" => "required|integer",
                "message" => "nullable|string"
            ]);

            $boostRequestToUpdate = Boostinteraction::where("id", $validated["request_id"])->first();

            if($boostRequestToUpdate) {
                $boostRequestToUpdate->nb_likes = $validated["nb_likes"];
                $boostRequestToUpdate->nb_comments = $validated["nb_comments"];
                $boostRequestToUpdate->message = $validated["message"];

                $boostRequestToUpdate->save();

                return response()->json([
                    "message" => "Demande de Boost modifié avec succès !"
                ], 200);
            } else {
                return response()->json([
                    "message" => "Demande de Boost non trouvé !"
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Une erreur s'est produite ! Veuillez réessasyer",
                "error_occurred" => $e->getMessage()
            ], 500);
        }
    }
}