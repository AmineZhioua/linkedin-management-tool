<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeController extends Controller
{
    public function checkout() {
        return view('checkout');
    }

    public function session(Request $request) {
        require_once 'D:\Work\Laravel-workspace\lemonade\vendor\autoload.php';
        require_once 'D:\Work\Laravel-workspace\lemonade\config\stripe.php';

        Stripe::setApiKey(config('stripe.secret_key'));

        $YOUR_DOMAIN = 'http://127.0.0.1:8000';
        $price = $request->get('price');
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
            'success_url' => $YOUR_DOMAIN . '/home',
            'cancel_url' => $YOUR_DOMAIN . '/',
        ]);

        return response()->json(['url' => $checkout_session->url]);
    }
}