<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Boostinteraction;
use Illuminate\Http\Request;

class BoostinteractionController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        $boostInteractions = Boostinteraction::with('user')
            ->where('status', $status)
            ->latest()
            ->paginate(10); 
        return view('admin', compact('boostInteractions'));
    }

    public function update(Request $request, Boostinteraction $boostinteraction)
    {
        $request->validate([
            'status' => 'required|in:accepted,declined', // Changed from approved,rejected
        ]);

        $boostinteraction->update([
            'status' => $request->status,
        ]);

        return redirect()->route('admin.boostinteractions.index')
            ->with('success', 'Boost interaction status updated successfully.');
    }
    public function destroy(Boostinteraction $boostinteraction)
    {
        $boostinteraction->delete();
        return redirect()->route('admin.boostinteractions.index')
            ->with('success', 'Boost interaction deleted successfully.');
    }
}