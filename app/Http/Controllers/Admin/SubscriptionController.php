<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Coupon;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    // Subscriptions CRUD
    public function index()
    {
        $subscriptions = Subscription::all();
        return view('admin', compact('subscriptions'));
    }

    public function create()
    {
        return view('admin');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'monthly_price' => ['required', 'numeric', 'min:0'],
            'yearly_price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'features' => ['required', 'array', 'min:1'],
            'features.*' => ['required', 'string', 'max:255'],
            'linkedin' => ['required', 'boolean'],
            'whatsapp' => ['required', 'boolean'],
            'discount' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        Subscription::create([
            'name' => $request->name,
            'monthly_price' => $request->monthly_price,
            'yearly_price' => $request->yearly_price,
            'description' => $request->description,
            'features' => $request->features,
            'linkedin' => $request->linkedin,
            'whatsapp' => $request->whatsapp,
            'discount' => $request->discount,
        ]);

        return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription created successfully.');
    }

    public function edit(Subscription $subscription)
    {
        return view('admin', compact('subscription'));
    }

    public function update(Request $request, Subscription $subscription)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'monthly_price' => ['required', 'numeric', 'min:0'],
            'yearly_price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'features' => ['required', 'array', 'min:1'],
            'features.*' => ['required', 'string', 'max:255'],
            'linkedin' => ['required', 'boolean'],
            'whatsapp' => ['required', 'boolean'],
            'discount' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $subscription->update([
            'name' => $request->name,
            'monthly_price' => $request->monthly_price,
            'yearly_price' => $request->yearly_price,
            'description' => $request->description,
            'features' => $request->features,
            'linkedin' => $request->linkedin,
            'whatsapp' => $request->whatsapp,
            'discount' => $request->discount,
        ]);

        return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription updated successfully.');
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();
        return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription deleted successfully.');
    }

    // Active Subscriptions CRUD
    public function active()
    {
        $userSubscriptions = UserSubscription::with(['user', 'subscription'])->get();
        return view('admin', compact('userSubscriptions'));
    }

    public function createActive()
    {
        $users = User::all();
        $subscriptions = Subscription::all();
        return view('admin', compact('users', 'subscriptions'));
    }

    public function storeActive(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'subscription_id' => ['required', 'exists:subscriptions,id'],
            'pricing_mode' => ['required', 'in:monthly,yearly'],
            'date_expiration' => ['nullable', 'date', 'after:today'],
        ]);

        UserSubscription::create([
            'user_id' => $request->user_id,
            'subscription_id' => $request->subscription_id,
            'pricing_mode' => $request->pricing_mode,
            'date_expiration' => $request->date_expiration,
        ]);

        return redirect()->route('admin.subscriptions.active')->with('success', 'Active subscription created successfully.');
    }

    public function editActive(UserSubscription $userSubscription)
    {
        $users = User::all();
        $subscriptions = Subscription::all();
        return view('admin', compact('userSubscription', 'users', 'subscriptions'));
    }

    public function updateActive(Request $request, UserSubscription $userSubscription)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'subscription_id' => ['required', 'exists:subscriptions,id'],
            'pricing_mode' => ['required', 'in:monthly,yearly'],
            'date_expiration' => ['nullable', 'date', 'after:today'],
        ]);

        $userSubscription->update([
            'user_id' => $request->user_id,
            'subscription_id' => $request->subscription_id,
            'pricing_mode' => $request->pricing_mode,
            'date_expiration' => $request->date_expiration,
        ]);

        return redirect()->route('admin.subscriptions.active')->with('success', 'Active subscription updated successfully.');
    }

    public function destroyActive(UserSubscription $userSubscription)
    {
        $userSubscription->delete();
        return redirect()->route('admin.subscriptions.active')->with('success', 'Active subscription deleted successfully.');
    }

    // Coupons CRUD
    public function indexCoupons()
    {
        $coupons = Coupon::all();
        return view('admin', compact('coupons'));
    }

    public function createCoupon()
    {
        return view('admin');
    }

    public function storeCoupon(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:coupons,code'],
            'discount' => ['required', 'numeric', 'min:0'],
            'type' => ['required', 'in:fixed,percentage'],
            'expires_at' => ['nullable', 'date', 'after:today'],
        ]);

        Coupon::create([
            'code' => $request->code,
            'discount' => $request->discount,
            'type' => $request->type,
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully.');
    }

    public function editCoupon(Coupon $coupon)
    {
        return view('admin', compact('coupon'));
    }

    public function updateCoupon(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:coupons,code,' . $coupon->id],
            'discount' => ['required', 'numeric', 'min:0'],
            'type' => ['required', 'in:fixed,percentage'],
            'expires_at' => ['nullable', 'date', 'after:today'],
        ]);

        $coupon->update([
            'code' => $request->code,
            'discount' => $request->discount,
            'type' => $request->type,
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated successfully.');
    }

    public function destroyCoupon(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted successfully.');
    }
}