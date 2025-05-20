<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\LinkedinUser;
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
    public function checkout()
    {
        return view('checkout');
    }

    public function applyCoupon(Request $request)
    {
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

    public function session(Request $request)
    {
        require_once base_path('vendor/autoload.php');

        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $price = floatval($request->get('price'));
        $pricingMode = $request->get('pricingMode');
        $subscriptionId = $request->get('subscription_id');
        $discount = $request->get('discount');

        $subscription = Subscription::find($subscriptionId);
        if (!$subscription) {
            return redirect()->route('subscriptions')
                ->with('subscription_notfound', "Abonnement supprimé ou n'est pas trouvé!");
        }

        // Apply Discount if available
        if ($discount > 0) {
            $price = $price - ($price * $discount / 100);
        }

        try {
            $checkout_session = Session::create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => ['name' => $subscription->name],
                        'unit_amount' => intval($price * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('success', ['pricingMode' => $pricingMode, 'subscription_id' => $subscriptionId]),
                'cancel_url' => route('cancel'),
            ]);

            return response()->json(['url' => $checkout_session->url]);

        } catch (\Exception $e) {
            return redirect()->route('subscriptions')
                ->with('payment_error', "Une Erreur s'est produite! Réessayez plus tard.");
        }
    }

    // Success Payment Function
    public function success(Request $request)
    {
        $userId = Auth::id();
        $pricingMode = $request->query('pricingMode');
        $subscriptionId = $request->query('subscription_id') ?? session('subscription_id');

        $userLinkedinAccount = LinkedinUser::where('user_id', $userId)->first();

        $subscription = Subscription::find($subscriptionId);
        if (!$subscription) {
            return redirect()->route('subscriptions')
                ->with('subscription_notfound', "Abonnement supprimé ou n'est pas trouvé!");
        }

        $expirationDate = ($pricingMode === 'mensuel') ? Carbon::now()->addMonth() : Carbon::now()->addYear();

        UserSubscription::updateOrCreate(
            [
                'user_id' => $userId,
                'subscription_id' => $subscription->id,
                'pricing_mode' => $pricingMode
            ],
            [
                'date_expiration' => $expirationDate,
                'boost_likes' => $subscription->boost_likes,
                'available_posts' => $subscription->available_posts
            ]
        );

        // Update user permissions based on subscription features
        $user = Auth::user();
        if ($user instanceof \App\Models\User) {
            $user->post_perm = ($subscription->linkedin == 1 || $subscription->whatsapp == 1) ? true : false;
            $user->save();
        } else {
            Log::error('Authenticated user is not an instance of the User model.');
        }

        session()->forget(['subscription_id', 'pricingMode']);

        if (!$userLinkedinAccount) {
            return redirect()->route('login-linkedin')
                ->with('success_payment', 'Votre Abonnement est maintenant Activé!');
        } else {
            return redirect()->route('dashboard')
                ->with('success_payment', 'Votre Abonnement est maintenant Activé!');
        }
    }

    public function cancel()
    {
        return redirect()->route('subscriptions')
            ->with('cancel_payment', 'Abonnement Annulé!');
    }
}