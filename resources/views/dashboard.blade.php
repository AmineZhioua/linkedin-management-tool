<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
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

                <!-- <div id="circle-container" class="flex absolute bottom-[-8%] left-[-120px]" style="z-index: -1;">
                    <span id="circle" class="bg-black h-24 w-24 rounded-full"></span>
                    <script>
                        let blackCircle = document.getElementById('circle');
                        let circleContainer = document.getElementById('circle-container');
                        for (let i = 0; i < 15; i++) {
                            circleContainer.appendChild(blackCircle.cloneNode(true));
                        }
                    </script>
                </div> -->
            </div>
        </main>
    </div>

    <!-- LinkedIn Profiles Section -->
    <div class="container py-2 flex flex-col gap-8 my-10">
        <div class="flex align-items-center justify-between">
            <h1 class="fw-bold text-3xl">Vos Profils LinkedIn</h1>
            <button class="text-white fw-semibold bg-black rounded-full px-4 py-2">Ajouter un profil</button>
        </div>

        <div class="relative">
            <div class="flex justify-content-center align-items-center gap-12 min-h-[300px]" id="profiles-container">
                @foreach($linkedinUserList as $linkedinUser)
                <div class="flex flex-col align-items-center justify-center gap-4 relative profile transition-all duration-300">
                    <img 
                        src="{{ $linkedinUser->linkedin_picture ?? '/build/assets/images/default-profile.png' }}"
                        alt="profile-picture" 
                        height="200"
                        width="200"
                        class="rounded-full"
                    />
                    <h3 class="text-xl fw-bold">{{ $linkedinUser->linkedin_firstname }} {{ $linkedinUser->linkedin_lastname }}</h3>
                    <img 
                        src="/build/assets/icons/linkedin-blue.svg" 
                        alt="LinkedIn Icon" 
                        class="absolute bottom-[20%] right-[5%] bg-white" 
                        style="border: 5px solid white; border-radius: 20px;"
                    />
                </div>
                @endforeach
            </div>
            
            <button id="left-arrow" class="absolute top-1/2 left-0 transform -translate-y-1/2 bg-black text-white rounded-full w-10 h-10 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
            <button id="right-arrow" class="absolute top-1/2 right-0 transform -translate-y-1/2 bg-black text-white rounded-full w-10 h-10 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
        </div>
    
        <div class="flex justify-center gap-2" id="pagination-dots"></div>
    </div>

    <!-- KPIs Section -->
    <div class="container py-2 flex flex-col gap-4 my-10">
        <div class="flex justify-between align-items-center">
            <h1 class="fw-bold text-3xl">Tes KPIs</h1>
            <button class="text-white fw-semibold bg-black rounded-full px-4 py-2">Trier</button>
        </div>
        <div class="grid grid-cols-4 gap-4">
            <div class="bg-red-500 flex flex-col align-items-center py-8 px-3 rounded-xl">
                <h1>10K</h1>
                <h3>Likes</h3>
                <p class="text-muted">Hausse de XX likes</p>
                <button class="text-white fw-semibold bg-black rounded-full px-4 py-2">En savoir plus</button>
            </div>
            <div class="bg-red-500 flex flex-col align-items-center py-8 px-3 rounded-xl">
                <h1>10K</h1>
                <h3>Likes</h3>
                <p class="text-muted">Hausse de XX likes</p>
                <button class="text-white fw-semibold bg-black rounded-full px-4 py-2">En savoir plus</button>
            </div>
            <div class="bg-red-500 flex flex-col align-items-center py-8 px-3 rounded-xl">
                <h1>10K</h1>
                <h3>Likes</h3>
                <p class="text-muted">Hausse de XX likes</p>
                <button class="text-white fw-semibold bg-black rounded-full px-4 py-2">En savoir plus</button>
            </div>
            <div class="bg-red-500 flex flex-col align-items-center py-8 px-3 rounded-xl">
                <h1>10K</h1>
                <h3>Likes</h3>
                <p class="text-muted">Hausse de XX likes</p>
                <button class="text-white fw-semibold bg-black rounded-full px-4 py-2">En savoir plus</button>
            </div>
            
        </div>
    </div>

    <!-- Calendar Section -->
    <div class="container py-2 my-10 flex flex-col gap-4" id="app">
        <div class="flex justify-between align-items-center">
            <h1 class="fw-bold text-3xl">Planning à Venir</h1>
            <button class="text-white fw-semibold bg-black rounded-full px-4 py-2">Button</button>
        </div>

        <dashboard-calendar 
            :campaigns="{{ json_encode($campaigns) }}" 
            :posts="{{ json_encode($posts) }}"
            :initial-month="{{ now()->month - 1 }}" 
            :initial-year="{{ now()->year }}" 
        />
    </div>

    <!-- Tasks Section -->
    <!-- <div class="container py-2">
        <div class="flex justify-content-between mt-32">
            <h1 class="text-black">Taches à Réaliser</h1>
            <button class="bg-white text-black">Ajouter</button>
        </div>

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
    </div> -->

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const profilesContainer = document.getElementById('profiles-container');
        const profiles = document.querySelectorAll('.profile');
        const leftArrow = document.getElementById('left-arrow');
        const rightArrow = document.getElementById('right-arrow');
        const paginationContainer = document.getElementById('pagination-dots');
        
        const visibleProfilesCount = 3; // Show 3 profiles at a time
        let currentIndex = 0; // Start with the first profile
        const totalProfiles = profiles.length;
        
        // Hide navigation arrows if there are 3 or fewer profiles
        if (totalProfiles <= 1) {
            leftArrow.style.display = 'none';
            rightArrow.style.display = 'none';
        }
        
        // Create pagination dots based on total profiles
        for (let i = 0; i < totalProfiles; i++) {
            const dot = document.createElement('div');
            dot.classList.add('w-3', 'h-3', 'rounded-full', 'cursor-pointer', 'transition-all');
            dot.classList.add(i === 0 ? 'bg-black' : 'bg-gray-300');
            dot.dataset.index = i;
            dot.addEventListener('click', () => goToProfile(i));
            paginationContainer.appendChild(dot);
        }
        
        // Initialize the display
        updateProfilesVisibility();
        
        // Navigate to a specific profile index
        function goToProfile(index) {
            currentIndex = index;
            updateProfilesVisibility();
            updatePaginationDots();
        }
        
        // Update which profiles are visible
        function updateProfilesVisibility() {
            // Hide all profiles and remove active class
            profiles.forEach((profile, index) => {
                profile.style.display = 'none';
                profile.classList.remove('active');
                profile.classList.add('profile');
            });
            
            // Calculate indices for the three profiles to display
            const leftIndex = ((currentIndex - 1) + totalProfiles) % totalProfiles; // Previous profile
            const centerIndex = currentIndex % totalProfiles; // Current (center) profile
            const rightIndex = (currentIndex + 1) % totalProfiles; // Next profile
            
            // Show the three profiles
            profiles[leftIndex].style.display = 'flex';
            profiles[centerIndex].style.display = 'flex';
            profiles[rightIndex].style.display = 'flex';
            
            // Apply active class to the center profile
            profiles[centerIndex].classList.add('active');
            profiles[centerIndex].classList.remove('profile');
        }
        
        // Update pagination dots to show current profile
        function updatePaginationDots() {
            const dots = paginationContainer.querySelectorAll('div');
            dots.forEach((dot, index) => {
                dot.classList.toggle('bg-black', index === currentIndex);
                dot.classList.toggle('bg-gray-300', index !== currentIndex);
            });
        }
        
        // Navigate to previous profile
        function prevProfile() {
            currentIndex = (currentIndex - 1 + totalProfiles) % totalProfiles; // Move left, wrap to end if needed
            updateProfilesVisibility();
            updatePaginationDots();
        }
        
        // Navigate to next profile
        function nextProfile() {
            currentIndex = (currentIndex + 1) % totalProfiles; // Move right, wrap to start if needed
            updateProfilesVisibility();
            updatePaginationDots();
        }
        
        // Set up event listeners for arrows
        leftArrow.addEventListener('click', prevProfile);
        rightArrow.addEventListener('click', nextProfile);
    });
    </script>

    <style>
    .profile, .active {
        transition: all 0.3s ease-in-out;
    }

    .profile {
        opacity: 0.5;
        transform: scale(0.8);
    }

    .active {
        opacity: 1;
        transform: scale(1.2);
        margin-inline: 25px;
        z-index: 10;
    }

    #left-arrow, #right-arrow {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    #left-arrow:hover, #right-arrow:hover {
        background-color: #333;
        transform: translateY(-50%) scale(1.1);
    }

    #pagination-dots div {
        transition: all 0.3s ease;
    }

    #pagination-dots div:hover {
        transform: scale(1.2);
    }
    </style>
</body>
</html>