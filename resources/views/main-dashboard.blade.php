<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lemonade - Main Dashboard</title>
    <link rel="icon" type="image/x-icon" href="/build/assets/lemonade-logo.svg">
    <!-- FontAwesome Kit -->
    <script src="https://kit.fontawesome.com/8ecf1b98e7.js" crossorigin="anonymous"></script>
    <!-- Vite and CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            height: 100vh;
            width: 100%;
            overflow: auto;
            font-family: "Inter", sans-serif !important;
            background-color: #FEF4E5;
        }
        body::-webkit-scrollbar {
            width: 6px;
        }
        body::-webkit-scrollbar-track {
            background: transparent;
        }
        body.no-scroll {
            overflow: hidden;
        }
        body::-webkit-scrollbar-thumb {
            background: gray;
            border-radius: 1px;
        }
    </style>
</head>
<body id="app" class="flex flex-col relative">
    <!-- Header Section Start -->
    <header class="flex items-center justify-between py-0 px-3 bg-black">
        <!-- Logo Section -->
        <div class="flex items-center gap-2">
            <img 
                src="/build/assets/lemonade-logo.svg" 
                alt="Lemonade Logo" 
                height="35"
                width="35"
            />
            <img 
                src="/build/assets/Lemonade.png" 
                alt="Lemonade Text" 
                height="50"
                width="120"
            />
        </div>

        <div class="flex flex-row-reverse items-center gap-4">
            <!-- Profile Dropdown -->
            <div class="relative">
                <!-- Profile Image Button -->
                <button id="profileDropdownBtn" class="h-[40px] w-[40px] cursor-pointer focus:outline-none">
                    <img 
                        src="{{ $userImage ?? '/build/assets/images/default-profile.png' }}"
                        alt="User Profile" 
                        class="rounded-full object-cover"
                    />
                </button>

                <!-- Dropdown Menu -->
                <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded shadow-lg z-50">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>

            <!-- Button for Notifications -->
            <notifications-bell 
                :user-id="{{ Auth::user()->id }}" 
                :user-linkedin-accounts="{{ json_encode($userLinkedinAccounts) }}" 
            />
        </div>
    </header>
    <!-- Header Section End -->

    <main class="flex-1 h-full">
        <main-section
            :user='@json($user)'
            :user-linkedin-accounts='@json($userLinkedinAccounts)'
            :user-linkedin-posts='@json($userLinkedinPosts)'
            :campaigns='@json($userCampaigns)'
            :user-boost-requests='@json($userBoostRequests)'
            :subscription-data='@json($subscriptionData)'
            :user-additional-info='@json($userAdditionalInfo)'
            :user-subscription='@json($userSubscription)'
            :subscriptions='@json($subscriptions)'
        />
    </main>
    <!-- Main Section End -->

    <!-- Script to toggle dropdown -->
    <script>
        document.addEventListener('click', function (e) {
            const btn = document.getElementById('profileDropdownBtn');
            const menu = document.getElementById('profileDropdown');

            if (btn.contains(e.target)) {
                menu.classList.toggle('hidden');
            } else if (!menu.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>