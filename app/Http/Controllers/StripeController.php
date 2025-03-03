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


    // Session Function
    public function session(Request $request) {
        require_once base_path('vendor/autoload.php');
        
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $price = $request->get('price');
        $pricingMode = $request->get('pricingMode');
        $subscriptionId = $request->get('subscription_id');
        $discount = $request->get('discount');


        // Store session data for later retrieval
        $request->session()->put('subscription_id', $subscriptionId);
        $request->session()->put('subscription_pricing_mode', $pricingMode);


        $subscription = Subscription::find($subscriptionId);
        if (!$subscription) {
            return response()->json(['error' => 'Subscription not found'], 404);
        }

        // Calculate price
        if($discount > 0) {
            $price = $price - ($price * $discount / 100);
        }

        $checkout_session = Session::create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $subscription->name,
                        ],
                        'unit_amount' => $price * 100,
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => route('success', ['pricingMode' => $pricingMode, 'subscription_id' => $subscriptionId]),
            'cancel_url' => route('cancel'),
        ]);

        return response()->json(['url' => $checkout_session->url]);
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

        return redirect()->route('home')->with('success_payment', 'Your Subscription for ' . $subscription->name . ' is now Activated!');
    }



    // Cancel Function
    public function cancel() {
        // This function is gonna be more developed in the future
        return redirect()->route('subscriptions')->with('cancel_payment', 'Subscription Cancelled!');
    }
}
