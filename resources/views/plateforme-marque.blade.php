<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=League+Spartan:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <title>Lemonade - Plateforme de Marque</title>
    <link rel="icon" type="image/x-icon" href="/build/assets/lemonade-logo.svg">

    <!-- FontAwesome Kit -->
    <script src="https://kit.fontawesome.com/33f8496b80.js" crossorigin="anonymous"></script>
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #FEF4E5 !important;
        }

        .socials img {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header class="d-flex align-items-center justify-content-between container">
        <!-- Lemonade Logo & Text -->
        <a href="{{ route('welcome') }}" class="d-flex align-items-center gap-2 flex-grow-1">
            <img 
                src="/build/assets/lemonade-logo.svg" 
                alt="Lemonade Logo"
                height="50"
                width="50"
            />
            <img 
                src="/build/assets/icons/Lemonade-black.svg" 
                alt="Lemonade Text"
                class="lemonade-text"
                height="50"
                width="120"
            />
        </a>

        <!-- Auth Dropdown Menu -->
        <div class="flex align-items-center gap-3">
            <div class="relative text-black text-sm font-semibold">
                <!-- Dropdown Toggle Button -->
                <button 
                    id="navbarDropdown" 
                    class="nav-item dropdown px-5 py-2 rounded-full border border-black flex items-center"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                >
                    {{ Auth::user()->name }}
                </button>

                <!-- Dropdown Menu Component -->
                <x-login-dropdown />
            </div>


            <!-- LinkedIn Accounts -->
            <div class="text-black text-sm fw-semibold">
                <button class="nav-item dropdown bg-transparent rounded-ful">
                    <!-- Display the first account picture -->
                    <a id="navbarDropdown" class="nav-link dropdown-toggle text-black d-flex align-items-center gap-2 relative" 
                        href="#" role="button"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre
                    >
                        <img 
                            src="{{ $linkedinUserList[0]->linkedin_picture ?? '/build/assets/images/default-profile.png' }}"
                            alt="profile-picture" 
                            height="50" 
                            width="50" 
                            class="rounded-full"
                        />
                        <!-- LinkedIn Logo -->
                        <img 
                            src="/build/assets/icons/linkedin-blue.svg" 
                            alt="LinkedIn Logo" 
                            height="25"
                            width="25" 
                            class="absolute bottom-[-3px] right-[10px]"
                        />
                    </a>

                    <!-- Dropdown menu with the rest of the accounts -->
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        @foreach($linkedinUserList as $index => $linkedinUser)
                            <a class="dropdown-item bg-pink-200 d-flex align-items-center gap-2 px-2" href="#">
                                <img 
                                    src="{{ $linkedinUser->linkedin_picture ?? '/build/assets/images/default-profile.png' }}"
                                    alt="profile-picture" 
                                    height="35" 
                                    width="35" 
                                    class="rounded-full"
                                />
                                <p class="mb-0">{{ $linkedinUser->linkedin_firstname }} {{ $linkedinUser->linkedin_lastname }}</p>
                            </a>
                        @endforeach
                    </div>
                </button>
            </div>
        </div>
    </header>

    @if(session('profile_error'))
    <popup path="/build/assets/popups/sad-face.svg">
        <p>{{ session('profile_error') }}</p>
    </popup>
    @endif

    <!-- Main Section -->
    <main id="app" class="vh-100 pt-8 flex-1 overflow-hidden" style="border-top: 1px solid #BBBBBB;">
            <plateforme-card :existing-plateforme="{{ $existingPlateforme ? json_encode($existingPlateforme) : 'null' }}"></plateforme-card>
    </main>



    <!-- Footer Setion -->
    <footer class="d-flex align-items-center justify-content-between container py-7">
        <!-- Copyright & Date -->
        <div class="d-flex copyright flex-col text-black fw-semibold">
            <span>Copyright ©
                <script>
                    document.write(new Date().getFullYear());
                </script>
                Lemonade.
            </span>
            <span>Tous droits réservés</span>
        </div>

        <!-- Lemonade Logo -->
        <img 
            src="/build/assets/lemonade-logo.svg" 
            alt="Lemonade Logo"
            height="40"
            width="40"
        />
        <div class="flex gap-3 socials">
            <!-- LinkedIn Icon -->
            <img 
                src="/build/assets/icons/linkedin-black.svg" 
                alt="LinkedIn Logo" 
                height="25"
                width="25" 
            />
            <!-- Instagram Icon -->
            <img 
                src="/build/assets/icons/instagram-black.svg" 
                alt="LinkedIn Logo" 
                height="25"
                width="25" 
            />
            <!-- TikTok Icon -->
            <img 
                src="/build/assets/icons/tiktok-black.svg" 
                alt="LinkedIn Logo" 
                height="25"
                width="25" 
            />
        </div>
    </footer>
</body>
</html>