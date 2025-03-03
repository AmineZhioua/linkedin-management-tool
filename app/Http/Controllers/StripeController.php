<?php

namespace App\Http\Controllers;

use App\Models\Contract;
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
        require_once 'D:\Work\Laravel-workspace\lemonade\vendor\autoload.php';
        require_once 'D:\Work\Laravel-workspace\lemonade\config\stripe.php';

        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $price = $request->get('price');
        $pricingMode = $request->get('pricingMode');
        $title = $request->get('title');

        // Store session data for later retrieval
        $request->session()->put('subscription_title', $title);
        $request->session()->put('subscription_pricing_mode', $pricingMode);

        $checkout_session = Session::create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $title,
                        ],
                        'unit_amount' => $price * 100,
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => route('success', ['pricingMode' => $pricingMode, 'title' => $title]),
            'cancel_url' => route('cancel'),
        ]);

        return response()->json(['url' => $checkout_session->url]);
    }


    // Success function
    public function success(Request $request) {
        $userId = Auth::id();
        $pricingMode = $request->query('pricingMode');
        $title = $request->query('title');

        if (empty($title)) {
            $title = $request->session()->get('subscription_title');
        }

        // Calculate expiration date based on subscription type
        $expirationDate = ($pricingMode === 'mensuel') ? Carbon::now()->addMonth() : Carbon::now()->addYear();

        $linkedin = 0;
        $whatsapp = 0;

        // Set values based on the subscription name
        if ($title === 'Linkedin Plan') {
            $linkedin = 1;
        } elseif ($title === 'Whatsapp plan') {
            $whatsapp = 1;
        } elseif ($title === 'Pack Linkedin & Whatsapp') {
            $linkedin = 1;
            $whatsapp = 1;
        }

        Contract::updateOrCreate(
            ['user_id' => $userId],
            [
                'linkedin' => $linkedin,
                'whatsapp' => $whatsapp,
                'date_expiration' => $expirationDate,
                'updated_at' => Carbon::now(),
            ]
        );

        $request->session()->forget(['subscription_title', 'subscription_pricing_mode']);

        return redirect()->route('home')->with('success_payment', 'Your Subscription for ' . $title . ' is now Activated!');
    }



    // Cancel Function
    public function cancel() {
        // This function is gonna be more developed in the future to cancel the subscription or subscriptions
        return redirect()->route('subscriptions')->with('cancel_payment', 'Subscription Cancelled!');
    }
}