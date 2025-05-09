<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/8ecf1b98e7.js" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/google-libphonenumber@3.2.30/dist/libphonenumber.js"></script>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <title>Lemonade - Additional Informations</title>
</head>
<body class="w-full h-[100vh]">
    <div class="flex flex-col items-center justify-center gap-5 h-full p-4" id="app">
        <div class="flex flex-col items-center gap-2">
            <img 
                src="/build/assets/lemonade-logo.svg" 
                alt="Lemonade Logo"
                height="70"
                width="70"
            />
            <h1 class="fw-bold">Encore une autre étape </h1>
        </div>

        <extra-info-form />
    </div>
</body>
</html>