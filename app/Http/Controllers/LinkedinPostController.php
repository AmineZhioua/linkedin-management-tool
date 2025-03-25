<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\LinkedinUser;
use Illuminate\Http\Request;

class LinkedinPostController extends Controller
{
    public function index() {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $linkedinUserList = LinkedinUser::where('user_id', $user->id)->get();

        return view('linkedin-post', ['linkedinUserList' => $linkedinUserList]);
    }
}
