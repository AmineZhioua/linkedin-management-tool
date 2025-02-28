<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\User;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class StripeController extends Controller
{
    public function checkout() {
        return view('checkout');
    }

    // Create a new session for the payment
    public function session(Request $request) {
        require_once 'D:\Work\Laravel-workspace\lemonade\vendor\autoload.php';
        require_once 'D:\Work\Laravel-workspace\lemonade\config\stripe.php';

        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $YOUR_DOMAIN = 'http://127.0.0.1:8000';
        $price = $request->get('price');
        $pricingMode = $request->get('pricingMode');
        $title = $request->get('title');

        $checkout_session = Session::create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $title,
                        ],
                        'unit_amount' => $price * 100,
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => route('success', ['pricingMode' => $pricingMode]),
            'cancel_url' => $YOUR_DOMAIN . '/subscriptions',
        ]);

        return response()->json(['url' => $checkout_session->url]);
    }


    // Success Method for Subscription payment
    public function success(Request $request) {
        $userId = Auth::id();

        $pricingMode = $request->query('pricingMode');

        // Calculate expiration date
        $expirationDate = ($pricingMode === 'mensuel') ? Carbon::now()->addDays(30) : Carbon::now()->addYear();

        $linkedin = true;
        $whatsapp = true;

        // Create or update the contract
        Contract::updateOrCreate(
            ['user_id' => $userId],
            [
                'linkedin' => $linkedin,
                'whatsapp' => $whatsapp,
                'date_expiration' => $expirationDate,
            ]
        );
        
        return redirect()->route('home')->with('success', 'Subscription activated successfully!');
    }
}