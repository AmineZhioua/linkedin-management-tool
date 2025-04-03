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

    <!-- FontAwesome Kit -->
    <script src="https://kit.fontawesome.com/33f8496b80.js" crossorigin="anonymous"></script>

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

        .landing-text {
            display: inline-block;
            color: white;
            padding: 10px 30px;
            font-size: 3rem;
            font-weight: bold;
            transform: rotate(-3deg);
            margin-left: 20px;
        }

        .landing-text:last-of-type {
            position: relative;
            left: 50px;
            top: -20px;
        }

        .baby-blue {
            background-color: #B4EAED;
        }

        .pastel-pink {
            background-color: #FFD6D3;
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
                    <div class="d-flex flex-column my-3 relative">
                        <h1 class="landing-text baby-blue rounded-full w-min px-8 py-3">Plateforme</h1>
                        <h1 class="landing-text pastel-pink rounded-full w-fit px-8 py-3">de marque !</h1>
                    </div>
                    <h1 class="fw-bold text-5xl">à tes pinceaux !</h1>
                    <p class="fw-semibold mt-4 text-lg">
                        Devenez un pro en programmant et personnalisant <br> vos prochains posts.
                    </p>
                </div>

                <!-- Main Image Section -->
                <img 
                    src="/build/assets/images/marque-img.svg" 
                    alt="dashboard" 
                    height="400" 
                    width="420"
                    class="dashboard-img d-none d-lg-block"
                />
            </div>
        </main>
    </div>

    <main id="app" class="vh-100 pt-8 flex-1" style="border-top: 1px solid #BBBBBB;">
        <marque-component :plateforme="{{ json_encode($existingPlateforme) }}"></marque-component>
    </main>
</body>
</html>