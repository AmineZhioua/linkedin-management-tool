<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $users = User::getCachedUsers($search);

        return view('admin', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin');
    }

    /**
     * Store a newly created user in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:user,admin'],
            'post_perm' => ['required', 'boolean'],
            'boost_perm' => ['required', 'boolean'],
            'image' => ['nullable', 'string', 'max:255'],
            'suspend_end' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'post_perm' => $request->post_perm,
            'boost_perm' => $request->boost_perm,
            'image' => $request->image,
            'suspend_end' => $request->suspend_end,
        ]);

        // Clear user-related caches
        Cache::forget('active_users_count');
        Cache::flush(); // Clear all caches

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param User $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        return view('admin', compact('user'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:user,admin'],
            'post_perm' => ['required', 'boolean'],
            'boost_perm' => ['required', 'boolean'],
            'image' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'suspend_end' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'post_perm' => $request->post_perm,
            'boost_perm' => $request->boost_perm,
            'image' => $request->image,
            'suspend_end' => $request->suspend_end,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Clear user-related caches
        Cache::forget('active_users_count');
        Cache::flush(); // Clear all caches

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        $user->delete();

        // Clear user-related caches
        Cache::forget('active_users_count');
        Cache::flush(); // Clear all caches

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Show the form for editing the authenticated user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function profile()
    {
        $user = Auth::user();
        return view('admin', compact('user'));
    }

    /**
     * Update the authenticated user's profile in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:user,admin'],
            'post_perm' => ['required', 'boolean'],
            'boost_perm' => ['required', 'boolean'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'suspend_end' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'post_perm' => $request->post_perm,
            'boost_perm' => $request->boost_perm,
            'suspend_end' => $request->suspend_end,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Clear user-related caches
        Cache::forget('active_users_count');
        Cache::flush(); // Clear all caches

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully.');
    }

    /**
     * Suspend a user by setting the suspension end date.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function suspend(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'suspend_date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $user = User::findOrFail($request->user_id);
        $user->update([
            'suspend_end' => $request->suspend_date,
        ]);

        // Clear user-related caches
        Cache::forget('active_users_count');
        Cache::flush(); // Clear all caches

        return redirect()->route('admin.users.index')->with('success', 'User suspended successfully.');
    }

    /**
     * Remove suspension from a user by setting suspend_end to null.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeSuspension(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $user = User::findOrFail($request->user_id);
        $user->update([
            'suspend_end' => null,
        ]);

        // Clear user-related caches
        Cache::forget('active_users_count');
        Cache::flush(); // Clear all caches

        return redirect()->route('admin.users.index')->with('success', 'User suspension removed successfully.');
    }
}