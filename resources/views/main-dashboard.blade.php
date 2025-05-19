<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/8ecf1b98e7.js" crossorigin="anonymous"></script>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <title>Lemonade - Main Dashboard</title>
    <style>
        body {
            height: 100vh;
            width: 100%;
            overflow: auto;
            font-family: "Inter", sans-serif !important;
        }
        body::-webkit-scrollbar {
            width: 6px;
        }

        body::-webkit-scrollbar-track {
            background: #ddd;
        }

        body::-webkit-scrollbar-thumb {
            /* background: linear-gradient(135deg, rgb(255 16 185) 0%, rgb(255 125 82) 100%); */
            background: gray;
            border-radius: 1px;
        }
    </style>
</head>
<body id="app">
    <!-- Header Section Start -->
    <div class="flex items-center justify-between py-3 px-3 bg-black">
        <!-- Logo Section -->
        <div class="flex items-center gap-2">
            <img 
                src="/build/assets/lemonade-logo.svg" 
                alt="Lemonade Logo" 
                height="35"
                width="35"
            />
            <img 
                src="/build/assets/Lemonade.png" 
                alt="Lemonade Text" 
                height="50"
                width="120"
            />
        </div>

        <div class="flex flex-row-reverse items-center gap-5">
            <!-- For Profile Dropdown -->
            <div class="h-[40px] w-[40px] cursor-pointer">
                <img 
                    src="/build/assets/images/default-profile.png" 
                    alt="Default Profile" 
                    class="rounded-full object-fill" 
                />
            </div>

            <!-- Button for the Notifications -->
            <button>
                <img 
                    src="/build/assets/icons/notification-white.svg" 
                    alt="Notification Icon"
                    height="30"
                    width="30"
                />
            </button>

            <!-- Button for the Tasks & Missions -->
            <button>
                <img 
                    src="/build/assets/icons/tasks-white.svg" 
                    alt="Notification Icon"
                    height="30"
                    width="30"
                />
            </button>
        </div>
    </div>
    <!-- Header Section End -->


    <!-- Main Section Start -->
    <main-section
        :user-linkedin-accounts="{{ json_encode($userLinkedinAccounts) }}" 
        :user-linkedin-posts="{{ json_encode($userLinkedinPosts) }}"
        :campaigns="{{ json_encode($userCampaigns) }}"
    />
    <!-- Main Section End -->
</body>
</html>