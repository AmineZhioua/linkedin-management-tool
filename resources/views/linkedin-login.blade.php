<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <title>Lemonade - Connexion au LinkedIn</title>
    <style>
        .ajout-btn:hover,
        .linkedin-account:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="vh-100 overflow-hidden d-flex login" id="app">
        <!-- "Déja un compte" Section -->
        <div class="absolute top-4 right-4 text-black text-sm fw-semibold" style="z-index: 1000;">
            <button class="nav-item dropdown bg-pink-200 px-3 py-1 rounded-2xl">
                <a id="navbarDropdown" class="nav-link dropdown-toggle text-black" href="#" role="button"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                    {{ Auth::user()->name }}
                </a>

                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item bg-pink-200" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </button>
        </div>

        <!-- Lemonade Logo -->
        <div class="logo-container">
            <a href="{{ route('welcome') }}" class="d-flex align-items-center gap-2">
                <img 
                    src="/build/assets/lemonade-logo.svg" 
                    alt="Lemonade Logo"
                    height="40"
                    width="40"
                />
                <img 
                    src="/build/assets/icons/lemonade-black.svg" 
                    alt="Lemonade Text"
                    class="lemonade-text"
                    height="40"
                    width="100"
                />
            </a>
        </div>
        <!-- Image Side -->
        <div class="left-side">
            <!-- Full-Screen Homepage Image -->
            <img 
                src="/build/assets/mockup-linkedin.png" 
                alt="LinkedIn Image" 
                class="homepage-img"
            />
        </div>

        <!-- Form Side -->
        <div class="d-flex flex-grow-1 flex-col align-items-center justify-content-center">
            <!-- Text -->
            <div class="text-center text-black gap-4">
                <h1 class="fw-bold text-4xl">Tu es maintenant connecté en tant <br>que {{ Auth::user()->name }}</h1>
                <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            </div>

            @if($linkedinUserList->isEmpty())
                <!-- LinkedIn Auth Button -->
                <div class="d-flex align-items-center flex-sm-column gap-2 w-100" style="max-width: 400px;">
                    <button class="social-btn">
                        <img 
                            src="/build/assets/icons/linkedin-blue.svg" 
                            alt="linkedin-icon" 
                            height="30" 
                            width="30"
                        />
                        <a href="{{ route('linkedin.auth') }}" class="text-decoration-none text-black">
                            Se Connecter avec LinkedIn
                        </a>
                    </button>
                </div>

                <p class="text-black mt-2 text-center">
                    <u>Cette étape est obligatoire pour la création de ton dashboard</u>
                </p>
            @else
                <div class="d-flex flex-col align-items-start mx-auto">
                        <div class="text-center text-black">
                            <h3 class="fw-semibold text-xl">
                                Vos comptes LinkedIn :
                            </h3>
                            @foreach($linkedinUserList as $linkedinUser)
                            <div class="flex px-3 py-2 rounded-full border cursor-pointer mt-4 linkedin-account">
                                <div class="flex align-items-center justify-content-center gap-3">
                                    <img 
                                        src="{{ $linkedinUser->linkedin_picture  ?? '/build/assets/images/default-profile.png' }}"
                                        alt="profile-picture"
                                        height="50"
                                        width="50"
                                        class="rounded-full"
                                    />
                                    <p class="mb-0 text-lg fw-semibold">
                                        {{ $linkedinUser->linkedin_firstname }} {{ $linkedinUser->linkedin_lastname }}
                                    </p>

                                    <img 
                                        src="/build/assets/icons/linkedin-blue.svg" 
                                        alt="Google Icon" 
                                        height="30" 
                                        width="30"
                                    />
                                </div>
                            </div>
                        </div>
                </div>
                @endforeach

                <!-- ""Ajouter un compte"" Button -->
                <button 
                    class="flex align-items-center px-3 py-2 rounded-full border cursor-pointer gap-2 mt-2 ajout-btn"
                >
                    <a 
                        href="{{ route('linkedin.auth') }}" 
                        class="text-decoration-none text-black flex align-items-center gap-1"
                    >
                        <img 
                            src="/build/assets/icons/add.svg"
                            alt="add-icon" 
                            height="25" 
                            width="25" 
                        />
                        Ajouter un compte LinkedIn
                    </a>
                    <img 
                        src="/build/assets/icons/linkedin-blue.svg" 
                        alt="Google Icon" 
                        height="30" 
                        width="30"
                    />
                </button>
            @endif
        </div>

        
        <!-- Display this Popup whenever a LinkedIn Error Occurs (Popup.vue) -->
        @if(session('linkedin_error'))
        <popup path="/build/assets/popups/sad-face.svg">
            <p>{{ session('linkedin_error') }}</p>
        </popup>
        @endif
    </div>
</body>
</html>