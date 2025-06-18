<?php

namespace App\Http\Controllers;

use App\Models\ExtraInformation;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InfoFormController extends Controller
{
    /**
     * Display the form for additional information
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $userId = Auth::id();

        $userInfo = ExtraInformation::where("user_id", $userId)->first();
        $userSubscription = UserSubscription::where("user_id", $userId)->first();

        if ($userInfo && $userSubscription) {
            return redirect()->route('main.dashboard')
                ->with('info-exists', 'Vous pouvez modifier vos informations en accédant aux paramètres.');
        } elseif ($userInfo && !$userSubscription) {
            return redirect()->route('subscriptions')
                ->with('info-exists', 'Vous pouvez modifier vos informations en accédant aux paramètres.');
        }

        return view('info-form');
    }

    /**
     * Add extra information for a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addExtraInformation(Request $request)
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'user_image' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'profession' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'nom_entreprise' => 'required|string|max:255',
            'adresse_entreprise' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $imagePath = $request->file('user_image')->store('images', 'public');
            $data = $validated;
            $data['user_image'] = $imagePath;
            $data['user_id'] = $userId;

            ExtraInformation::create($data);
            DB::commit();

            return response()->json([
                'message' => 'Vos informations ont été sauvegardées avec succès !'
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error while saving the user information', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $userId,
            ]);

            return response()->json([
                'message' => 'Vos informations ont été sauvegardées avec succès !'
            ], 201);
        }
    }

    /**
     * Update extra information for a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateExtraInformation(Request $request)
    {
        $userId = Auth::id();
        $info = ExtraInformation::where('user_id', $userId)->first();

        if (!$info) {
            return redirect()->route('main.dashboard')
                ->with('error', 'Aucune information supplémentaire trouvée pour cet utilisateur.');
        }

        $validated = $request->validate([
            'user_image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'profession' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'nom_entreprise' => 'required|string|max:255',
            'adresse_entreprise' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $data = $validated;
            unset($data['user_image']); // Remove and handle separately

            if ($request->hasFile('user_image')) {
                if ($info->user_image) {
                    $oldPath = str_replace('images/', '', $info->user_image);
                    if (Storage::disk('public')->exists('images/' . $oldPath)) {
                        Storage::disk('public')->delete('images/' . $oldPath);
                    }
                }
                // Store new image
                $data['user_image'] = $request->file('user_image')->store('images', 'public');
            }

            $info->update($data);
            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Vos informations ont été modifié avec succès !'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error while updating the user information', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $userId,
            ]);

            return response()->json([
                'status' => 500,
                'message' => 'Une erreur s\'est produite lors de la mise à jour de vos informations !'
            ], 500);
        }
    }
}