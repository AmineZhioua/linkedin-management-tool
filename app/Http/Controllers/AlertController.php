<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function getAlerts(Request $request) {
        $alerts = Alert::all();

        return response()->json([
            'alerts' => $alerts,
            'status' => 200,
        ], 200);
    }
}
