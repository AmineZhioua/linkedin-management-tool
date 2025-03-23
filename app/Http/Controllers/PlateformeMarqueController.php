<?php

namespace App\Http\Controllers;
use App\Models\LinkedinUser;
use App\Models\PlateformeMarque;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlateformeMarqueController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Get existing platform data if available
        $existingPlateforme = PlateformeMarque::where('user_id', $user->id)->first();
        
        $linkedinUserList = LinkedinUser::where('user_id', $user->id)->get();
        
        return view('plateforme-marque', [
            'linkedinUserList' => $linkedinUserList,
            'existingPlateforme' => $existingPlateforme
        ]);
    }
    
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            // Validate the incoming request
            $request->validate([
                'nom_marque' => 'required|string|max:255',
                'domaine_marque' => 'required|string|max:255',
                'logo_marque' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg',
                'description_marque' => 'nullable|string|max:500',
                'mode' => 'required|string|in:create,update',
                'logo_changed' => 'nullable|string|in:true,false',
            ]);

            // Determine if we're creating or updating
            $isUpdate = $request->mode === 'update';
            
            if ($isUpdate) {
                // Find existing record
                $platformInfo = PlateformeMarque::where('user_id', $user->id)->first();
                
                if (!$platformInfo) {
                    // If not found, create new one
                    $platformInfo = new PlateformeMarque();
                    $platformInfo->user_id = $user->id;
                }
            } else {
                // Create new record
                $platformInfo = new PlateformeMarque();
                $platformInfo->user_id = $user->id;
            }
            
            // Update fields
            $platformInfo->nom_marque = $request->nom_marque;
            $platformInfo->domaine_marque = $request->domaine_marque;
            $platformInfo->description_marque = $request->description_marque;

            // Handle logo upload if provided
            if ($request->hasFile('logo_marque') && $request->logo_changed === 'true') {
                // Delete old logo if exists
                if ($isUpdate && $platformInfo->logo_marque) {
                    Storage::disk('public')->delete($platformInfo->logo_marque);
                }
                
                // Store new logo
                $path = $request->file('logo_marque')->store('logos', 'public');
                $platformInfo->logo_marque = $path;
            }

            $platformInfo->save();

            return response()->json([
                'message' => $isUpdate ? 'Informations mises à jour avec succès' : 'Informations soumises avec succès',
                'plateforme' => $platformInfo
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}