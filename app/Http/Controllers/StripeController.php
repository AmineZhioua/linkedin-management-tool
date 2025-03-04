<?php

namespace App\Http\Controllers;

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

    $price = $request->get('price');
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

    $checkout_session = Session::create([
        'line_items' => [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => ['name' => $subscription->name],
                'unit_amount' => $finalPrice * 100, // Convert to cents
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => route('success', ['pricingMode' => $pricingMode, 'subscription_id' => $subscriptionId]),
        'cancel_url' => route('cancel'),
    ]);

    return response()->json(['url' => $checkout_session->url]);
}

}
