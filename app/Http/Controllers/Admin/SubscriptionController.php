<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            'whatsapp' => ['required', 'boolean'],
            'discount' => ['required', 'numeric', 'min:0', 'max:100'],
            'boost_likes' => ['required', 'integer', 'min:0'],
            'available_posts' => ['required', 'integer', 'min:0'],
            'boost_comments' => ['required', 'integer', 'min:0'],
        ]);

        Subscription::create([
            'name' => $request->name,
            'monthly_price' => $request->monthly_price,
            'yearly_price' => $request->yearly_price,
            'description' => $request->description,
            'whatsapp' => $request->whatsapp,
            'discount' => $request->discount,
            'boost_likes' => $request->boost_likes,
            'available_posts' => $request->available_posts,
            'boost_comments' => $request->boost_comments,
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
            'whatsapp' => ['required', 'boolean'],
            'discount' => ['required', 'numeric', 'min:0', 'max:100'],
            'boost_likes' => ['required', 'integer', 'min:0'],
            'available_posts' => ['required', 'integer', 'min:0'],
            'boost_comments' => ['required', 'integer', 'min:0'],
        ]);

        $subscription->update([
            'name' => $request->name,
            'monthly_price' => $request->monthly_price,
            'yearly_price' => $request->yearly_price,
            'description' => $request->description,
            'whatsapp' => $request->whatsapp,
            'discount' => $request->discount,
            'boost_likes' => $request->boost_likes,
            'available_posts' => $request->available_posts,
            'boost_comments' => $request->boost_comments,
        ]);

        return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription updated successfully.');
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();
        return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription deleted successfully.');
    }

    // Active Subscriptions CRUD
    public function active(Request $request)
    {
        $search = $request->input('search');
        $query = UserSubscription::with(['user', 'subscription']);

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $userSubscriptions = $query->paginate(10);

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
            'expires_at' => ['nullable', 'date', 'after:today'],
        ]);

        UserSubscription::create([
            'user_id' => $request->user_id,
            'subscription_id' => $request->subscription_id,
            'pricing_mode' => $request->pricing_mode,
            'expires_at' => $request->expires_at,
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
            'expires_at' => ['nullable', 'date', 'after:today'],
        ]);

        $userSubscription->update([
            'user_id' => $request->user_id,
            'subscription_id' => $request->subscription_id,
            'pricing_mode' => $request->pricing_mode,
            'expires_at' => $request->expires_at,
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
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'discount' => 'required|numeric|min:0',
            'type' => 'required|in:percentage,fixed',
            'expires_at' => 'nullable|date|after_or_equal:today',
        ]);

        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Promo code created successfully.');
    }

    public function editCoupon(Coupon $coupon)
    {
        return view('admin', compact('coupon'));
    }

    public function updateCoupon(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code,' . $coupon->id,
            'discount' => 'required|numeric|min:0',
            'type' => 'required|in:percentage,fixed',
            'expires_at' => 'nullable|date|after_or_equal:today',
        ]);

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Promo code updated successfully.');
    }

    public function destroyCoupon(Coupon $coupon)
    {
        try {
            $coupon->delete();
            return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete coupon: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete coupon. Please check the logs.');
        }
    }
}