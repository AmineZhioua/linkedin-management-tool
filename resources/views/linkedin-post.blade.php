<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=League+Spartan:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <title>Lemonade - Publier sur LinkedIn</title>
    <link rel="icon" type="image/x-icon" href="/build/assets/lemonade-logo.svg">

    <!-- FontAwesome Kit -->
    <script src="https://kit.fontawesome.com/33f8496b80.js" crossorigin="anonymous"></script>
</head>
<body class="flex flex-col"
    style="background-color: #FEF4E5; font-family: 'Inter', sans-serif; min-height:100vh;">
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
                src="/build/assets/icons/lemonade-black.svg" 
                alt="Lemonade Text"
                class="lemonade-text"
                height="50"
                width="120"
            />
        </a>

        <!-- Auth Dropdown Menu -->
        <div class="flex align-items-center gap-3">
            <div class="text-black text-sm font-semibold">
                <button class="nav-item dropdown bg-white hover:bg-gray-50 px-5 py-2 rounded-full transition-colors duration-200 shadow-sm" style="border: 1px solid rgba(0,0,0,0.2);">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle text-black flex items-center" href="#" role="button"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->name }}
                    </a>
                    <x-login-dropdown />
                </button>
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

    <!-- Main Section -->
    <main id="app" class="vh-100 pt-8 flex-1" style="border-top: 1px solid #BBBBBB;">
        <linkedin-postcard :linkedin-userlist="{{ $linkedinUserList }}" />
    </main>
</body>
</html>