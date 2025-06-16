<?php

namespace App\Http\Controllers;

use App\Models\LinkedinCampaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function updateCampaign(Request $request) {
        try {
            $validated = $request->validate([
                "linkedin_id" => "required|integer|exists:linkedin_users,id",
                "campaign_id" => "required|integer|exists:linkedin_campaigns,id",
                'name' => 'required|string',
                'description' => 'required|string',
                'target_audience' => 'required|string',
                'frequency_per_day' => 'required|integer|min:1',
                'couleur' => 'required|string',
                'start_date' => 'required|date|after:now',
                'end_date' => 'required|date|after:start_date',
            ]);
            
            $campaignToUpdate = LinkedinCampaign::where("id", $validated["campaign_id"])->first();

            if(!$campaignToUpdate) {
                return response()->json([
                    "status" => 404,
                    "messsage" => "Campagne à modifier non trouvé !"
                ], 404);
            }

            $campaignToUpdate->name = $validated["name"];
            $campaignToUpdate->description = $validated["description"];
            $campaignToUpdate->target_audience = $validated["target_audience"];
            $campaignToUpdate->frequency_per_day = $validated["frequency_per_day"];
            $campaignToUpdate->color = $validated["couleur"];
            $campaignToUpdate->start_date = $validated["start_date"];
            $campaignToUpdate->end_date = $validated["end_date"];

            $campaignToUpdate->save();

            return response()->json([
                "status" => 200,
                "message" => "Votre campagne a été modifié avec succès !"
            ], 200);
        } catch(\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Une erreur s'est produite lors de la modification de la campagne !"
            ], 500);
        }
    }
}
