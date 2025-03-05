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

    public function session(Request $request) {
        require_once base_path('vendor/autoload.php');
        
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $price = floatval($request->get('price'));
        $pricingMode = $request->get('pricingMode');
        $subscriptionId = $request->get('subscription_id');
        $couponCode = $request->get('coupon_code'); // Get coupon code from request
        $discount = 0;

        // Store session data for later retrieval
        $request->session()->put('subscription_id', $subscriptionId);
        $request->session()->put('subscription_pricing_mode', $pricingMode);

        // Validate subscription
        $subscription = Subscription::find($subscriptionId);
        if (!$subscription) {
            return response()->json(['error' => 'Subscription not found'], 404);
        }

        // Check if a valid coupon is applied
        if (!empty($couponCode)) {
            $coupon = Coupon::where('code', $couponCode)->first();

            if ($coupon && $coupon->isValid()) {
                if ($coupon->type === 'percentage') {
                    $discount = ($price * $coupon->discount) / 100;
                } else {
                    $discount = $coupon->discount;
                }
            } else {
                return response()->json(['error' => 'Invalid or expired coupon'], 400);
            }
        }

        // Calculate final price
        $finalPrice = max(0, $price - $discount);

        try {
            $checkout_session = Session::create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => ['name' => $subscription->name],
                        'unit_amount' => intval($finalPrice * 100), // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'allow_promotion_codes' => true, // ✅ Enables the coupon input in Stripe Checkout
                'success_url' => route('success', ['pricingMode' => $pricingMode, 'subscription_id' => $subscriptionId]),
                'cancel_url' => route('cancel'),
            ]);

            return response()->json(['url' => $checkout_session->url]);

        } catch (\Exception $e) {
            Log::error('Stripe Checkout Error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while creating the checkout session.'], 500);
        }
    }

    // Success Function
    public function success(Request $request) {
        $userId = Auth::id();
        $pricingMode = $request->query('pricingMode');
        $subscriptionId = $request->query('subscription_id');

        if (empty($subscriptionId)) {
            $subscriptionId = $request->session()->get('subscription_id');
        }

        // Retrieve the subscription from the database
        $subscription = Subscription::find($subscriptionId);
        if (!$subscription) {
            return redirect()->route('subscriptions')->with('error', 'Subscription not found!');
        }

        // Determine expiration date
        $expirationDate = ($pricingMode === 'mensuel') ? Carbon::now()->addMonth() : Carbon::now()->addYear();

        UserSubscription::updateOrCreate(
            ['user_id' => $userId, 'subscription_id' => $subscription->id],
            ['date_expiration' => $expirationDate]
        );

        // Clear session data
        $request->session()->forget(['subscription_id', 'subscription_pricing_mode']);

        return redirect()->route('home')->with('success_payment', 'Your Subscription is now Activated!');
    }

    // Cancel Function
    public function cancel() {
        return redirect()->route('subscriptions')->with('cancel_payment', 'Subscription Cancelled!');
    }
}
