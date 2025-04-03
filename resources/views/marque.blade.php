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
        .special-text {
    display: inline-block;
    font-weight: bold;
    font-size: 4rem; /* Adjust as needed */
}

.special-text span {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 25px;
    color: black;
    font-weight: bold;
}

.blue-bg {
    background-color: #BCECFB; /* Light blue */
}

.pink-bg {
    background-color: #FFDDDD; /* Light pink */
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
                    <h1 class="special-text my-2">
                        <span class="blue-bg">
                        Plateforme
                        </span> </h1>
                    <h1 class="special-text my-2">
                        <span class="pink-bg">de marque !</span></h1>

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


 


        </div>
    </div>
        <main id="app" class="vh-100 pt-8 flex-1" style="border-top: 1px solid #BBBBBB;">
        <marque-component :plateforme="{{ json_encode($existingPlateforme) }}"></marque-component>
    </main>
</body>
</html>