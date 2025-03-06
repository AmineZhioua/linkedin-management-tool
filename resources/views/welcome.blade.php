<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>Accueil - Lemonade</title>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>
<body>
    <div class="min-h-screen bg-black overflow-hidden">
        <!-- Header Component -->
        <x-header />

        <!-- Main Content -->
        <main class="container main-content text-white py-2">
            <!-- Line at the Top -->
            <span class="top-line"></span>

            <div class="d-flex justify-content-between align-items-center py-3">
                <!-- Text Content -->
                <div class="text-white flex-grow-1">
                    <h1 class="fw-bold text-5xl">Créer et organiser</h1>
                    <h2 class="highlight-text my-4">votre communication</h2>
                    <h1 class="fw-bold text-5xl">digitale.</h1>
                    <p class="lead fw-semibold mt-4 text-lg">
                        Devenez un pro en programmant et personnalisant <br>vos prochains posts.
                    </p>
                </div>

                <!-- Rocket Image -->
                <img
                    src="/build/assets/Fusee.svg"
                    alt="Rocket Image"
                    class="rocket-img"
                />
            </div>
            <!-- Line at the Bottom -->
            <span class="bottom-line"></span>
        </main>

        <!-- Footer Component -->
        <x-footer />
    </div>

    <!-- Sidebar for Mobile -->
    @guest
    <div class="sidebar bg-black" id="sidebar">
            @if (Route::has('login'))
                <button class="login-btn bg-white fw-semibold">
                    <a href="{{ route('login') }}" class="text-black">Se Connecter</a>
                </button>
            @endif

            @if (Route::has('register'))""
                <button class="register-btn bg-white fw-semibold">
                    <a href="{{ route('register') }}" class="text-black">S'inscrire</a>
                </button>
            @endif
    </div>
    @endguest
</body>
</html>
