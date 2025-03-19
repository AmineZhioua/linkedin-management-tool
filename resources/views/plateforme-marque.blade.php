<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=League+Spartan:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <title>Lemonade - Plateforme de Marque</title>
    <link rel="icon" type="image/x-icon" href="/build/assets/lemonade-logo.svg">

    <!-- FontAwesome Kit -->
    <script src="https://kit.fontawesome.com/33f8496b80.js" crossorigin="anonymous"></script>
</head>
<body class="vh-100" style="background-color: #FEF4E5;">
    <!-- Header Section -->
    <header class="d-flex align-items-center justify-content-between container">
        <!-- Lemonade Logo & Text -->
        <div class="d-flex align-items-center gap-2 flex-grow-1">
            <img 
                src="/build/assets/lemonade-logo.svg" 
                alt="Lemonade Logo"
                height="30"
                width="30"
            />
            <span class="text-black text-lg fw-bold">Lemonade.</span>
        </div>

        <!-- Auth Dropdown Menu -->
        <div class="text-black text-sm fw-semibold">
            <button class="nav-item dropdown bg-pink-200 px-3 py-1 rounded-2xl">
                <a id="navbarDropdown" class="nav-link dropdown-toggle text-black" href="#" role="button"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                    {{ Auth::user()->name }}
                </a>
                <x-login-dropdown />
            </button>
        </div>
    </header>

    <!-- Main Section -->
    <main id="app" class="vh-100 pt-8" style="border-top: 1px solid grey;">
        <questionnaire />
    </main>

    <!-- Footer Setion -->
    <footer class="container d-flex justify-content-center align-items-center gap-2 py-3">
        <span class="text-black text-sm fw-semibold">© 2021 Lemonade. All rights reserved.</span>
        <a href="#" class="text-black text-sm fw-semibold">Privacy Policy</a>
    </footer>
</body>
</html>