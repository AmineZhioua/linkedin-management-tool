<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <title>Lemonade - Pricing</title>
</head>
<body class="container">
    <!-- Header Section -->
    <header class="d-flex justify-content-between align-items-center py-3">
        <!-- Lemonade Logo -->
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

        <!-- Account Button -->
        <button class="nav-item dropdown bg-pink-200 px-3 py-1 rounded-2xl">
            <a id="navbarDropdown" class="nav-link dropdown-toggle text-black" href="#" role="button"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                {{ Auth::user()->name }}
            </a>

            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <a class="dropdown-item bg-pink-200" href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                    {{ __('Logout') }}
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </button>
    </header>


    <!-- Pricing Section -->
    <main class="d-flex justify-content-center align-items-center flex-col">
        <h1 class="text-black">Des tarifs abordables mais un suivi de qualité !</h1>
        <!-- Buttons -->
        <div class="d-flex justify-content-center align-items-center gap-4 text-black">
            <button>
                Mensuel
            </button>
            <button>
                Annuel
            </button>
        </div>

        <!-- Pricing Cards -->
        <div class="flex flex-col md:flex-row justify-center items-center gap-6 w-full max-w-5xl">
            <!-- Cards -->
        </div>
    </main>
</body>
</html>