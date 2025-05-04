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
    <!-- <meta name="user_id" content="{{ Auth::user()->id }}"> -->

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
    </style>
</head>
<body>
    <div id="app">
        <div class="bg-black mb-8 relative">
            <!-- Header Component Section -->
            <x-header />

            <!-- Main Content Section -->
            <main class="container min-h-[600px]">

                <!-- Landing Section -->
                <div class="d-flex justify-content-between align-items-center py-10 relative">

                    <!-- Main Text Section -->
                    <div class="text-white flex-grow-1">
                        <h1 class="fw-bold text-5xl">Bienvenue sur ton</h1>
                        <h1 class="special-text my-4">dashboard, </h1>
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
                </div>
            </main>

            <!-- Start Waves Animation Section -->
            <div class="waves">
                <div class="wave" id="wave1"></div>
                <div class="wave" id="wave2"></div>
                <div class="wave" id="wave3"></div>
                <div class="wave" id="wave4"></div>
            </div>
            <!-- End Waves Animation Section -->
        </div>

        <!-- LinkedIn Profiles Section -->
        <div class="container py-2 flex flex-col gap-8 my-10">
            <div class="flex align-items-center justify-between">
                <h1 class="fw-bold text-3xl">Vos Profils LinkedIn</h1>
                <button class="bg-black rounded-full px-4 py-2 flex items-center gap-2 hover:opacity-70 transition-all duration-300">
                    <img 
                        src="/build/assets/icons/add-white.svg" 
                        alt="Add Icon"
                    />
                    <a href="{{ route('linkedin-post') }}" class="text-decoration-none text-white fw-semibold">Ajouter un profil</a>
                </button>
            </div>

            <div class="relative">
                <div class="flex justify-content-center align-items-center gap-12 min-h-[300px]" id="profiles-container">
                    @foreach($linkedinUserList as $linkedinUser)
                    <div class="flex flex-col align-items-center justify-center gap-4 cursor-pointer relative profile transition-all duration-300">
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
        <div class="container py-4 flex flex-col gap-4 my-10">
            <!-- KPI List VueJS Component -->
            <kpi-list 
                :all-user-posts="{{ json_encode($posts) }}" 
                :user-linkedin-accounts="{{ json_encode($linkedinUserList) }}"
            />
        </div>

        <!-- Calendar & Campaigns Table Section -->
        <!-- Calendar Section -->
        <div class="container py-2 my-10 flex flex-col gap-4">
            <div class="flex justify-between align-items-center">
                <div class="flex align-items-end gap-2">
                    <img 
                        src="/build/assets/icons/calendar-black.svg" 
                        alt="Calendar Icon"
                        height="45"
                        width="45"
                    />
                    <h1 class="fw-bold text-3xl mb-0">Planning à Venir</h1>
                </div>
                <button class="text-white fw-semibold bg-black rounded-full px-4 py-2">Button</button>
            </div>

            <dashboard-calendar 
                :campaigns="{{ json_encode($campaigns) }}" 
                :posts="{{ json_encode($posts) }}"
                :initial-month="{{ now()->month - 1 }}" 
                :initial-year="{{ now()->year }}" 
            />
        </div>

        <!-- Campaigns Display Section -->
        <div class="container py-2 my-10">
            <div class="flex justify-between align-items-center">
                <div class="flex align-items-center gap-2">
                    <img 
                        src="/build/assets/icons/campaign_black.svg" 
                        alt="Campaign Icon"
                        height="50"
                        width="50"
                    />
                    <h1 class="fw-bold text-3xl mb-0 mt-1">Vos Campagnes</h1>
                </div>
                <button class="text-white flex items-center gap-2 fw-semibold bg-black rounded-full px-4 py-2">
                    <img 
                        src="/build/assets/icons/add-white.svg" 
                        alt="Add Icon"
                    />
                    <a href="{{ route('linkedin-post') }}" class="text-decoration-none text-white fw-semibold">Créer une Campagne</a>
                </button>
            </div>

            <campaign-table :campaigns="{{ json_encode($campaigns) }}" />
        </div>

        <notification-card :user-id="{{ Auth::user()->id }}" />
    </div>


    <!-- Footer Section -->
    <footer class="bg-black py-4 mt-8">
        <div class="container flex items-center justify-between">
            <div class="d-flex copyright flex-col text-white fw-semibold">
                <span>Copyright 
                    <script>
                        document.write(new Date().getFullYear());
                    </script>
                    Lemonade.
                </span>
                <span>Tous droits réservés</span>
            </div>

            <img 
                src="/build/assets/lemonade-logo.svg" 
                alt="Lemonade Logo"
                height="40"
                width="40"
            />

            <div class="flex gap-3 flex-wrap">
                <!-- LinkedIn Icon -->
                <img 
                    src="/build/assets/icons/linkedin.svg" 
                    alt="LinkedIn Logo" 
                    height="25"
                    width="25" 
                />
                <!-- Instagram Icon -->
                <img 
                    src="/build/assets/icons/instagram.svg" 
                    alt="LinkedIn Logo" 
                    height="25"
                    width="25" 
                />
                <!-- TikTok Icon -->
                <img 
                    src="/build/assets/icons/tiktok.svg" 
                    alt="LinkedIn Logo" 
                    height="25"
                    width="25" 
                />
            </div>
        </div>
    </footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profilesContainer = document.getElementById('profiles-container');
        const profiles = document.querySelectorAll('.profile');
        const leftArrow = document.getElementById('left-arrow');
        const rightArrow = document.getElementById('right-arrow');
        const paginationContainer = document.getElementById('pagination-dots');
        
        const visibleProfilesCount = 3; // Show 3 profiles at a time
        let currentIndex = 0;
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
            profiles[i].addEventListener('click', () => goToProfile(i));
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

    /* .active {
        opacity: 1;
        transform: scale(1.2);
        margin-inline: 25px;
        z-index: 10;
    } */

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

    /* Waves Animation & Styling */
    .waves>div {
        position: absolute;
        width: 100%;
        min-height: 100px;
        bottom: -10px;
        left: 0;
        background: url("/build/assets/images/wave.png");
        background-size: 1000px 100px;
    }

   .waves #wave1 {
        z-index: 10;
        animation: waveAnimation 7s linear infinite;
        animation-delay: -2s;
    }

   .waves #wave2 {
        z-index: 9;
        animation: waveAnimation 10s linear infinite;
        animation-delay: -3s;
        opacity: 80%;
    }

   .waves #wave3 {
        z-index: 8;
        animation: waveAnimation 13s linear infinite;
        animation-delay: -4s;
        opacity: 60%;
    }

   .waves #wave4 {
        z-index: 7;
        animation: waveAnimation 20s linear infinite;
        animation-delay: -5s;
        opacity: 40%;
    }

    @keyframes waveAnimation {
    0% {
        background-position-x: 0px;
    }
    100% {
        background-position-x: 1000px;
    }
}
</style>
</body>
</html>