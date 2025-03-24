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
                return redirect()->route('login');
            }

            // Validate the incoming request
            $request->validate([
                'nom_marque' => 'nullable|string|max:255',
                'domaine_marque' => 'nullable|string|max:255',
                'logo_marque' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg',
                'description_marque' => 'nullable|string|max:500',
                'mode' => 'required|string|in:create,update,partial',
                'logo_changed' => 'nullable|string|in:true,false',
            ]);

            // Determine if we're creating, updating, or saving partial data
            $isUpdate = $request->mode === 'update';
            $isPartial = $request->mode === 'partial';
            
            if ($isUpdate || $isPartial) {
                $platformInfo = PlateformeMarque::where('user_id', $user->id)->first();
                
                if (!$platformInfo) {
                    $platformInfo = new PlateformeMarque();
                    $platformInfo->user_id = $user->id;
                }
            } else {
                $platformInfo = new PlateformeMarque();
                $platformInfo->user_id = $user->id;
            }
            
            // Update fields - only update fields that are provided in partial mode
            if ($request->has('nom_marque')) {
                $platformInfo->nom_marque = $request->nom_marque;
            }
            
            if ($request->has('domaine_marque')) {
                $platformInfo->domaine_marque = $request->domaine_marque;
            }
            
            if ($request->has('description_marque')) {
                $platformInfo->description_marque = $request->description_marque;
            }

            // Handle logo upload if provided
            if ($request->hasFile('logo_marque') && $request->logo_changed === 'true') {
                // Delete old logo if exists
                if (($isUpdate || $isPartial) && $platformInfo->logo_marque) {
                    Storage::disk('public')->delete($platformInfo->logo_marque);
                }
                
                // Store new logo
                $path = $request->file('logo_marque')->store('logos', 'public');
                $platformInfo->logo_marque = $path;
            }

            $platformInfo->save();

            $message = $isPartial ? 'Informations enregistrées, vous pouvez revenir plus tard' : 
                      ($isUpdate ? 'Informations mises à jour avec succès' : 'Informations soumises avec succès');

            return response()->json([
                'message' => $message,
                'plateforme' => $platformInfo
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}