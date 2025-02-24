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

    <style>
        .main-content {
            position: relative;
        }

        .main-content .top-line {
            position: absolute;
            transform: translateX(-50%);
            height: 0.5px;
            width: 1000%;
            background-color: rgb(161, 159, 159);
            top: 0;
        }

        .main-content .bottom-line {
            position: absolute;
            transform: translateX(-50%);
            height: 0.5px;
            width: 1000%;
            background-color: rgb(161, 159, 159);
            bottom: 0;
        }

        .main-content .lead {
            font-family: "League Spartan", serif;
        }

        .highlight-text {
            display: inline-block;
            background: linear-gradient(to right, #ff6ec4, #ff92a5);
            color: white;
            padding: 10px 20px;
            font-size: 3rem;
            font-weight: bold;
            border-radius: 25px;
            transform: rotate(-3deg);
            margin-left: 30px;
        }

        /* Sidebar for Mobile */
        .sidebar {
            position: fixed;
            top: 0;
            right: 0; /* Move the sidebar to the right */
            width: 70%;
            height: 100vh;
            background-color: #fff;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1); /* Adjust shadow for right side */
            display: none;
            flex-direction: column;
            justify-content: center; /* Align buttons to center */
            padding: 20px;
            z-index: 1000;
        }

        .sidebar.show {
            display: flex;
        }

        .sidebar .login-btn,
        .sidebar .register-btn {
            width: 100%; /* Ensure buttons take full width */
            margin-bottom: 20px; /* Add spacing between buttons */
            padding: 12px;
            font-size: 1.2rem;
            text-align: center;
            border-radius: 25px; /* Rounded corners */
            transition: background-color 0.3s ease; /* Smooth transition on hover */
        }

        .sidebar .login-btn:hover,
        .sidebar .register-btn:hover {
            background-color: #f0f0f0; /* Light hover effect */
        }

        .sidebar .login-btn a,
        .sidebar .register-btn a {
            text-decoration: none; /* Remove underline from links */
            color: black; /* Ensure text is black */
        }

.sidebar-toggle {
    display: block;
    cursor: pointer;
    position: absolute;
    top: 15px;
    right: 15px; /* Move the button to the right */
    font-size: 24px;
    z-index: 1100;
}

        /* Hide sidebar on larger screens */
        @media (min-width: 768px) {
            .sidebar {
                display: none;
            }

            .sidebar-toggle {
                display: none;
            }
        }

        /* Main content layout for smaller screens */
        @media (max-width: 767px) {
            .main-content .d-flex {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .rocket-img {
                width: 80%;
                height: auto;
                margin-top: 20px;
            }

            .highlight-text {
                font-size: 2.5rem;
            }

            .main-content {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="min-h-screen bg-black overflow-hidden">
        <!-- Header Component -->
        <x-header />

        <!-- Main Content -->
        <main class="container main-content text-white py-5">
            <!-- Line at the Top -->
            <span class="top-line"></span>

            <div class="d-flex align-items-center justify-content-between">
                <!-- Text Content -->
                <div class="flex-grow-1">
                    <h1 class="display-4 fw-bold">Créer et organiser</h1>
                    <h2 class="highlight-text">votre communication</h2>
                    <h1 class="display-4 fw-bold">digitale.</h1>
                    <p class="lead">
                        Devenez un pro en programmant et personnalisant vos
                        prochains posts.
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
    <div class="sidebar bg-black" id="sidebar">
        @guest
            @if (Route::has('login'))
                <button class="login-btn bg-white fw-semibold">
                    <a href="{{ route('login') }}" class="text-black">Se Connecter</a>
                </button>
            @endif

            @if (Route::has('register'))
                <button class="register-btn bg-white fw-semibold">
                    <a href="{{ route('register') }}" class="text-black">S'inscrire</a>
                </button>
            @endif
        @endguest
    </div>

    <!-- Sidebar Toggle Button -->
    <div class="sidebar-toggle" id="toggleSidebar" onclick="toggleSidebar()">&#9776;</div>

    <script>
        // Toggle sidebar visibility for mobile
        const toggleSidebar = () => {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        };
    </script>
</body>
</html>
