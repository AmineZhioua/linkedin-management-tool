<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <title>Lemonade - Vérification Email</title>
    <style>
        .logo-container {
            position: absolute;
            top: 15px;
            left: 15px;
            display: flex;
            align-items: center;
        }

.logo-container img {
    height: 30px; /* Increased size slightly */
    width: auto; /* Maintain aspect ratio */
}

.logo-container img:first-child {
    height: 40px; /* Ensure both images match */
    width: auto;
    margin-right: 5px; /* Adjust spacing */
}

        .verification-card {
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            width: 100%;
            max-width: 800px;
            margin-top: -250px /* Slightly reduced width */
            
        }

        .icon-circle {
            background: black;
            width: 50px;
            height: 50px;
        }

        .verification-link {
            border: none;
            background: none;
            color: black;
            font-weight: bold;
            cursor: pointer;
        }

        .verification-link:hover {
            text-decoration: underline;
        }

.alert {
    width: 100%;  /* Take up full available width */
    max-width: 642px;  /* Set a maximum width to prevent it from getting too large */
    margin-left: auto;  /* Center the alert horizontally */
    margin-right: auto;  /* Center the alert horizontally */
}
    </style>
</head>
<body>
    <div class="vh-100 d-flex flex-column align-items-center justify-content-center position-relative">
        <!-- Logo (Top Left) -->
        <div class="logo-container">
            <img class="logo" 
                src="/build/assets/lemonade-logo.svg" 
                alt="Lemonade Logo"
            />
            <img 
                src="/build/assets/icons/lemonade-black.svg" 
                alt="Lemonade Text"
            />
        </div>

        <!-- Verification Card -->
        <div class="verification-card shadow-lg card-border-gradient">
            <!-- Email Icon -->
            <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                <img 
                    src="/build/assets/icons/envelope-white.svg"
                    alt="Email Icon"
                    height="30"
                    width="30"
                />
            </div>

            <!-- Title -->
            <h2 class="fw-bold fs-4">
                Vérifiez votre email
            </h2>

            <!-- Main Text -->
            <p class="text-muted mb-3">
                Un nouveau lien de vérification a été envoyé à votre adresse e-mail. Avant de continuer, veuillez vérifier votre e-mail. 
                Si vous ne l'avez pas reçu,
            </p>

            <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" class="verification-link">clique ici</button> pour en demander un autre.
            </form>

            <!-- Status Alert (Wider) -->
            @if (session('resent'))
                <div class="alert alert-primary d-flex align-items-center gap-2 mt-3 w-100">
                    <div class="bg-primary rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 20px; height: 20px;">
                        <img 
                            src="/build/assets/icons/check.svg"
                            alt="Check Icon"
                            height="24"
                            width="24"
                        />
                    </div>
                    <div class="small">
                        Email de vérification envoyé avec succès ! Vérifiez votre boîte de réception.
                    </div>
                </div>
            @endif
        </div>
    </div>
</body>
</html>