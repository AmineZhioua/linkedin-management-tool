<?php

namespace App\Http\Controllers;

use App\Models\ExtraInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class InfoFormController extends Controller
{
    public function index() {
        // $userId = Auth::id();

        // $userInfo = ExtraInformation::where("user_id", $userId)->first();

        // if($userInfo) {
        //     return back()
        //         ->with('info-exists', 'Vous pouvez modifier vos informations en accédant au paramétres.');
        // }

        return view('info-form');
    }


    public function addExtraInformation(Request $request) {
        $validated = $request->validate([
            'user_image' => 'required|file',
            'profession' => 'required|string',
            'adresse' => 'required|string',
            'telephone' => 'required|string',
            'nom_entreprise' => 'required|string',
            'adresse_entreprise' => 'required|string'
        ]);

        $userId = Auth::id();

        DB::beginTransaction();

        try {
            $info = ExtraInformation::create([
                'user_id' => $userId,
                'user_image' => $validated["user_image"]->store('images', 'public'),
                'profession' => $validated["profession"],
                'adresse' => $validated["adresse"],
                'telephone' => $validated["telephone"],
                'nom_entreprise' => $validated["nom_entreprise"],
                'adresse_entreprise' => $validated["adresse_entreprise"],
            ]);

            DB::commit();

            return response()->json([
                "status" => 201,
                "message" => "Vos informations ont été sauvegardées avec succès!"
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error while saving the user information', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $userId ?? null
            ]);

            return response()->json([
                "status" => 500,
                "message" => "Une erreur s'est produite lors de l'enregistrement de vos informations !"
            ], 500);
        }
    }
}
