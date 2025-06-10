<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    /**
     * Display a listing of the alerts.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $alerts = Alert::when($search, function ($query, $search) {
            return $query->where('message', 'like', "%{$search}%");
        })->latest()->paginate(10);

        return view('admin', compact('alerts', 'search'));
    }

    /**
     * Show the form for creating a new alert.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin');
    }

    /**
     * Store a newly created alert in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
        ]);

        Alert::create($validated);

        return redirect()->route('admin.alerts.index')->with('success', 'Alert created successfully.');
    }

    /**
     * Show the form for editing the specified alert.
     *
     * @param Alert $alert
     * @return \Illuminate\View\View
     */
    public function edit(Alert $alert)
    {
        return view('admin', compact('alert'));
    }

    /**
     * Update the specified alert in storage.
     *
     * @param Request $request
     * @param Alert $alert
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Alert $alert)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
        ]);

        $alert->update($validated);

        return redirect()->route('admin.alerts.index')->with('success', 'Alert updated successfully.');
    }

    /**
     * Remove the specified alert from storage.
     *
     * @param Alert $alert
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Alert $alert)
    {
        $alert->delete();

        return redirect()->route('admin.alerts.index')->with('success', 'Alert deleted successfully.');
    }
}