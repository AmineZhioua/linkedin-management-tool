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
        .top-line {
            position: absolute;
            height: 1px;
            width: 200%;
            background-color: rgb(161 159 159 / 60%);
            top: -40px;
        }
    </style>
</head>
<body class="container overflow-hidden">
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
    <main class="d-flex justify-content-center align-items-center flex-col mt-4 relative">
        <span class="top-line"></span>

        <!-- Title -->
        <h1 class="text-black text-3xl font-bold">Des tarifs abordables mais un suivi de qualité !</h1>

        <!-- Buttons -->
        <div class="d-flex justify-content-center align-items-center gap-2 mt-3 text-black price-btns">
            <button class="text-white bg-purple-800 px-5 py-2 rounded-3xl">
                Mensuel
            </button>
            <button class="text-white bg-orange-500 px-5 py-2 rounded-3xl">
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