<?php

namespace App\Http\Controllers;

use App\Models\LinkedinUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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


    public function deleteAccount(Request $request) {
        try {
            $validated = $request->validate([
                "user_id" => "required|integer|exists:users,id"
            ]);

            $userToDelete = \App\Models\User::find($validated["user_id"]);

            if(!$userToDelete) {
                return response()->json([
                    "status" => 404,
                    "message" => "Compte à supprimer non trouvé"
                ], 404);
            }

            DB::beginTransaction();
            $userToDelete->delete();
            DB::commit();

            return response()->json([
                "status" => 200,
                "message" => "Compte supprimé avec succès !"
            ], 200);
        } catch(\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Une erreur s'est produite lors de la suppression du compte !",
                "error_message" => $e->getMessage()
            ], 500);
        }
    }
}
