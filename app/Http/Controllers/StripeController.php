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

    // Create a new session for the payment
    public function session(Request $request) {
        require_once 'C:\Users\SBS\Desktop\lemonade\vendor\autoload.php';
        require_once 'C:\Users\SBS\Desktop\lemonade\config\stripe.php';

        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $YOUR_DOMAIN = 'http://127.0.0.1:8000';
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
            'success_url' => route('success', ['pricingMode' => $pricingMode, 'title' => $title]),
            'cancel_url' => $YOUR_DOMAIN . '/subscriptions',
        ]);

        return response()->json(['url' => $checkout_session->url]);
    }

    // Handle subscription success and update contract
    public function success(Request $request) {
        $userId = Auth::id();
        $pricingMode = $request->query('pricingMode');
        $title = $request->query('title');

        // Fallback to session if query param not available
        if (empty($title)) {
            $title = $request->session()->get('subscription_title');
            Log::info('Retrieved title from session:', ['title' => $title]);
        }

        Log::info('Subscription Plan Title:', ['title' => $title]);

        // Calculate expiration date based on subscription type
        $expirationDate = ($pricingMode === 'mensuel') ? Carbon::now()->addMonth() : Carbon::now()->addYear();

        // Default values for the contract
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

        Log::info('Setting contract values:', [
            'user_id' => $userId,
            'linkedin' => $linkedin,
            'whatsapp' => $whatsapp,
            'expiration' => $expirationDate
        ]);

        // Create or update the contract
        Contract::updateOrCreate(
            ['user_id' => $userId],
            [
                'linkedin' => $linkedin,
                'whatsapp' => $whatsapp,
                'date_expiration' => $expirationDate,
                'updated_at' => Carbon::now(),
            ]
        );

        // Clear session data
        $request->session()->forget(['subscription_title', 'subscription_pricing_mode']);

        return redirect()->route('home')->with('success', 'Subscription activated successfully!');
    }
}