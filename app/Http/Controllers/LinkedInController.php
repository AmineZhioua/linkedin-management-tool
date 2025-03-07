<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class LinkedInController extends Controller
{
   public function index()
    {
        return view('linkedin-login');
    }
}
