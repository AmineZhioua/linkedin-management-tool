<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Lemonade</title>
    @vite(['resources/js/app.js'])
</head>

<body class="bg-black">
    <div id="app">
        <home-page 
            :login-url="{{ json_encode(route('login')) }}" 
            :register-url="{{ json_encode(route('register')) }}"
        ></home-page>
    </div>
</body>
</html>
