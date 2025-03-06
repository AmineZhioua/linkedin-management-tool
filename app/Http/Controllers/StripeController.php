<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\UserSubscription;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    public function checkout() {
        return view('checkout');
    }

public function applyCoupon(Request $request) {
    $couponCode = $request->input('coupon_code');
    $coupon = Coupon::where('code', $couponCode)->first();

    if ($coupon && $coupon->isValid()) {
        session(['applied_coupon' => $couponCode]);
        return response()->json([
            'success' => true, 
            'discount' => $coupon->discount, 
            'type' => $coupon->type
        ]);
    }

    return response()->json(['success' => false, 'error' => 'Code promo invalide ou expiré.']);
}

    public function session(Request $request) {
        require_once base_path('vendor/autoload.php');
        
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $price = floatval($request->get('price'));
        $pricingMode = $request->get('pricingMode');
        $subscriptionId = $request->get('subscription_id');
        $discount = 0;

        $couponCode = session('applied_coupon');

        $subscription = Subscription::find($subscriptionId);
        if (!$subscription) {
            return response()->json(['error' => 'Subscription not found'], 404);
        }

        if (!empty($couponCode)) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->isValid()) {
                $discount = ($coupon->type === 'percentage') 
                    ? ($price * $coupon->discount) / 100 
                    : $coupon->discount;
            } else {
                session()->forget('applied_coupon');
            }
        }

        $finalPrice = max(0, $price - $discount);

        try {
            $checkout_session = Session::create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => ['name' => $subscription->name],
                        'unit_amount' => intval($finalPrice * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('success', ['pricingMode' => $pricingMode, 'subscription_id' => $subscriptionId]),
                'cancel_url' => route('cancel'),
            ]);

            return response()->json(['url' => $checkout_session->url]);
        } catch (\Exception $e) {
            Log::error('Stripe Checkout Error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while creating the checkout session.'], 500);
        }
    }

    public function success(Request $request) {
        $userId = Auth::id();
        $pricingMode = $request->query('pricingMode');
        $subscriptionId = $request->query('subscription_id') ?? session('subscription_id');

        $subscription = Subscription::find($subscriptionId);
        if (!$subscription) {
            return redirect()->route('subscriptions')->with('error', 'Subscription not found!');
        }

        $expirationDate = ($pricingMode === 'mensuel') ? Carbon::now()->addMonth() : Carbon::now()->addYear();

        UserSubscription::updateOrCreate(
            ['user_id' => $userId, 'subscription_id' => $subscription->id],
            ['date_expiration' => $expirationDate]
        );

        session()->forget(['subscription_id', 'subscription_pricing_mode', 'applied_coupon']);

        return redirect()->route('home')->with('success_payment', 'Your Subscription is now Activated!');
    }

    public function cancel() {
        return redirect()->route('subscriptions')->with('cancel_payment', 'Subscription Cancelled!');
    }
}
