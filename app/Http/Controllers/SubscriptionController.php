<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Subscription;

class SubscriptionController extends Controller
{
   public function index()
    {
        $subscriptions = Subscription::all();
        return view('subscription', compact('subscriptions'));
    }
}
