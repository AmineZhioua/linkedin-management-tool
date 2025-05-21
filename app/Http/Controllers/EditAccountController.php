<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ExtraInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EditAccountController extends Controller
{
    /**
     * Redirect to the main dashboard
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        return redirect()->route('main.dashboard');
    }

    // Rest of the controller remains unchanged
    public function updateProfile(Request $request)
    {
        $userId = Auth::id();
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId),
            ],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        DB::beginTransaction();
        try {
            $user->name = $validated['name'];
            $user->email = $validated['email'];

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();
            DB::commit();

            return back()->with('success', 'Votre profil a été mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating user profile', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $userId,
            ]);

            return redirect()->route('main.dashboard')
                ->with('error', 'Une erreur s\'est produite lors de la mise à jour de votre profil.');
        }
    }

    public function getUserData()
    {
        $user = Auth::user();
        $extraInfo = ExtraInformation::where('user_id', $user->id)->first();
        
        $userData = [
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'post_perm' => $user->post_perm,
                'boost_perm' => $user->boost_perm,
                'suspend_end' => $user->suspend_end ? $user->suspend_end->format('Y-m-d') : null,
            ],
            'extraInfo' => $extraInfo ? [
                'user_image' => $extraInfo->user_image,
                'user_image_filename' => basename($extraInfo->user_image ?? ''),
                'profession' => $extraInfo->profession,
                'adresse' => $extraInfo->adresse,
                'telephone' => $extraInfo->telephone,
                'nom_entreprise' => $extraInfo->nom_entreprise,
                'adresse_entreprise' => $extraInfo->adresse_entreprise,
            ] : []
        ];

        return response()->json($userData);
    }
}