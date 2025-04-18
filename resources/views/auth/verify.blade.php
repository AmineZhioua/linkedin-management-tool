<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <title>Lemonade - Vérification Email</title>
</head>
<body>
    <div class="vh-100 d-flex flex-column align-items-center p-3 justify-content-center position-relative verify">
        <!-- Lemonade Logo -->
        <div class="logo-container">
            <a href="{{ route('welcome') }}" class="d-flex align-items-center gap-2">
                <img 
                    src="/build/assets/lemonade-logo.svg" 
                    alt="Lemonade Logo"
                    height="40"
                    width="40"
                />
                <img 
                    src="/build/assets/icons/Lemonade-black.svg" 
                    alt="Lemonade Text"
                    class="lemonade-text"
                    height="40"
                    width="100"
                />
            </a>
        </div>
        <button class="nav-item dropdown bg-pink-200 px-3 py-1 rounded-2xl position-absolute top-[10px] right-4">
            <a id="navbarDropdown" class="nav-link dropdown-toggle text-black" href="#" role="button"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                {{ Auth::user()->name }}
            </a>

            <x-login-dropdown />
        </button>


        <!-- Verification Card -->
        <div class="verification-card shadow-lg card-border-gradient text-center">
            <!-- Email Icon -->
            <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                <img 
                    src="/build/assets/icons/envelope-white.svg"
                    alt="Email Icon"
                    height="40"
                    width="40"
                />
            </div>

            <!-- Title -->
            <h2 class="fw-bold fs-4 mb-4">
                Vérifiez votre Email
            </h2>

            <!-- Main Text -->
            <div class="text-muted">
                Un nouveau lien de vérification a été envoyé à votre adresse e-mail. Veuillez vérifier votre e-mail. 
                <form method="POST" action="{{ route('verification.resend') }}">Si vous ne l'avez pas reçu,
                    @csrf
                    <button type="submit" class="verification-link">
                        <u class="text-black">
                            <strong>clique ici</strong>
                        </u>
                    </button> pour en demander un autre.
                </form>
            </div>


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