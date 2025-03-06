<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <title>Lemonade - Dashboard</title>
    <style>
        body {
            font-family: "League Spartan", Arial, sans-serif;
            height: 100vh;
            width: 100vw;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow-x: hidden;
        }

        .task-btns button:last-of-type {
            border: 1px solid black;
        }
    </style>
</head>
<body>
    <div class="bg-black">
        <!-- Header Component Section -->
        <x-header />

        <!-- Main Content Section -->
        <main class="container">

            <!-- Landing Section -->
            <div class="d-flex justify-content-between align-items-center py-4 relative">

                <!-- Main Text Section -->
                <div class="text-white flex-grow-1">
                    <h1 class="fw-bold text-5xl">Bienvenue sur ton</h1>
                    <h1 class="special-text my-2">dashboard, </h1>
                    <h1 class="fw-bold text-5xl">à toi de jouer !</h1>

                    <p class="fw-semibold mt-4 text-lg">
                        Devenez un pro en programmant et personnalisant <br> vos prochains posts.
                    </p>
                </div>

                <!-- Main Image Section -->
                <img 
                    src="/build/assets/images/dashboard-img.svg" 
                    alt="dashboard" 
                    height="400" 
                    width="420"
                    class="dashboard-img d-none d-lg-block"
                />

                <div id="circle-container" class="flex absolute bottom-[-8%] left-[-120px]" style="z-index: -1;">
                    <span id="circle" class="bg-black h-24 w-24 rounded-full"></span>
                    <!-- Script to Create more Circles -->
                    <script>
                        let blackCircle = document.getElementById('circle');
                        let circleContainer = document.getElementById('circle-container');
                        for (let i = 0; i < 15; i++) {
                            circleContainer.appendChild(blackCircle.cloneNode(true));
                        }
                    </script>
                </div>
            </div>
        </main>
    </div>

    <!-- Tasks Section -->
    <div class="container py-2">
        <!-- Text & Button Section -->
        <div class="flex justify-content-between mt-32">
            <h1 class="text-black">Taches à Réaliser</h1>
            <button class="bg-white text-black">Ajouter</button>
        </div>

        <!-- Tasks Card Section -->
        <div class="flex justify-content-between align-items-center p-4 bg-green-400 rounded-xl mt-2">
            <img 
                src="/build/assets/icons/paint.svg" 
                alt="paint-icon" 
                class="bg-white p-2 rounded-full mr-8 relative top-[-50px]" 
            />
            <div class="text-black flex-grow-1 px-2">
                <h1 class="text-3xl">Plateforme de marque</h1>
                <h1 class="text-2xl text-slate-700">75%</h1>
                <p class="text-muted fw-semibold">Plus que quelques taches!</p>
                <span>---------------</span>
            </div>

            <div class="task-btns flex flex-col align-items-center gap-2">
                <button class="bg-black text-white py-2 px-4 rounded-full">
                    Finir la Tache
                </button>

                <button class="bg-transparent text-black py-2 px-4 rounded-full flex align-items-center gap-2">
                    Tout voir
                    <img src="/build/assets/icons/arrow-down.svg" alt="arrow-down" />
                </button>
            </div>
        </div>
    </div>
</body>
</html>