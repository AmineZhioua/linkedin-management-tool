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
</head>
<body>
    <div class="vh-100 overflow-hidden d-flex login">
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

        <!-- Display this Popup whenever a LinkedIn Error Occurs (Popup.vue) -->
        @if(session('linkedin_error'))
        <popup path="/build/assets/popups/sad-face.svg">
            <p>{{ session('linkedin_error') }}</p>
        </popup>
        @endif

        <!-- Form Side -->
        <div class="d-flex flex-grow-1 flex-col align-items-center justify-content-center form-side">
            <!-- Text -->
            <div class="text-center text-black gap-4">
                <h1 class="fw-bold text-4xl">Tu es maintenant connecté en tant <br>que {{ Auth::user()->name }}</h1>
                <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            </div>

            <!-- Google & Apple Buttons -->
            <div class="d-flex align-items-center flex-sm-column gap-2 w-100" style="max-width: 400px;">
                <button class="social-btn">
                    <img 
                        src="/build/assets/icons/linkedin-blue.svg" 
                        alt="Google Icon" 
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
        </div>
    </div>
</body>
</html>