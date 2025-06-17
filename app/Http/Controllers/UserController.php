<?php

namespace App\Http\Controllers;

use App\Models\LinkedinUser;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function deleteLinkedinAccount(Request $request) {
        try {
            $validated = $request->validate([
                "linkedin_id" => "required|integer|exists:linkedin_users,id"
            ]);

            $accountToDelete = LinkedinUser::where("id", $validated["linkedin_id"])->first();

            if(!$accountToDelete) {
                return response()->json([
                    "status" => 404,
                    "message" => "Compte à supprimer non trouvé !"
                ], 404);
            }

            $accountToDelete->delete();
            
            return response()->json([
                'message' => 'Compte supprimé avec succès !',
                'status' => 200
            ], 200);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Une erreur s\'est produite lors de la suppression du compte !',
                'error_message' => $e->getMessage()
            ], 500);
        }
    }
}
