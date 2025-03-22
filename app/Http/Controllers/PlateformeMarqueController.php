<?php

namespace App\Http\Controllers;
use App\Models\LinkedinUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PlateformeMarqueController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $linkedinUserList = LinkedinUser::where('user_id', $user->id)->get();
        return view('plateforme-marque', ['linkedinUserList' => $linkedinUserList]);
    }
}
