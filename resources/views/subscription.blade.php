<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <title>Lemonade - Pricing</title>
    <style>
        .price-btns button {
            box-shadow: 0 4px 6px 0px rgb(0 0 0 / 76%);
        }
    </style>
</head>
<body class="container overflow-y-scroll" id="app">
    <!-- Header Section -->
    <header class="d-flex justify-content-between align-items-center py-3">
        <a href="{{ route('welcome') }}" class="d-flex align-items-center gap-2">
            <img src="/build/assets/lemonade-logo.svg" alt="Lemonade Logo" height="40" width="40" />
            <img src="/build/assets/icons/Lemonade-black.svg" alt="Lemonade Text" class="lemonade-text" height="40" width="100" />
        </a>

        <button class="nav-item dropdown bg-pink-200 px-3 py-1 rounded-2xl">
            <a id="navbarDropdown" class="nav-link dropdown-toggle text-black" href="#" role="button"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                {{ Auth::user()->name }}
            </a>

            <x-login-dropdown />
        </button>
    </header>
    
    <!-- Display this Popup whenever the User subscription expires (Popup.vue) -->
    @if(session('expired'))
    <popup path="/build/assets/popups/sad-face.svg">
        <p>{{ session('expired') }}</p>
    </popup>
    @endif

    <!-- Display this Popup whenever the User subscription is not found (Popup.vue) -->
    @if(session('subscription_notfound'))
    <popup path="/build/assets/popups/sad-face.svg">
        <p>{{ session('subscription_notfound') }}</p>
    </popup>
    @endif

    <!-- Display this Popup whenever the User cancel his subscription (Popup.vue) -->
    @if(session('cancel_payment'))
    <popup path="/build/assets/popups/sad-face.svg">
        <p>{{ session('cancel_payment') }}</p>
    </popup>
    @endif

    <!-- Display this Popup whenever the User's LinkedIn Subscription Ends (Popup.vue) -->
    @if(session('linkedin_error'))
    <popup path="/build/assets/popups/sad-face.svg">
        <p>{{ session('linkedin_error') }}</p>
    </popup>
    @endif

    <!-- Pricing Section -->
    <main class="d-flex justify-content-center align-items-center flex-col mt-4 relative">
        <!-- Title -->
        <h1 class="text-black text-3xl font-bold text-center">Des tarifs abordables mais un suivi de qualité !</h1>

        <!-- Coupon Code Input -->
        <div class="mt-4 w-full text-center">
            <input 
                type="text" 
                id="coupon-code" 
                class="py-2 px-6 border rounded-full mr-2 bg-white" 
                placeholder="Entrez votre Code Promo"
            />
            <button 
                id="apply-coupon" 
                class="bg-pink-300 text-white px-4 py-2 rounded-full"
            >
                Appliquer
            </button>
            <p id="coupon-message" class="text-red-500 mt-2"></p>
        </div>

        <!-- Pricing Cards & Buttons Vue Component (SubscriptionCards.vue) -->
        <subscription-cards 
            :user-subscriptions="{{ json_encode($userSubscriptions) }}" 
            :subscriptions="{{ json_encode($subscriptions) }}" 
        />
    </main>

</body>
</html>