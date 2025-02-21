<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <title>Lemonade - Inscription</title>
    <style>
        .left-side {
            position: relative;
            width: 40vw;
            height: 100vh;
            overflow: hidden;
        }

        .homepage-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .logo-container {
            position: absolute;
            top: 10px;
            right: 480px;
            display: flex;
            align-items: center;
            margin: 5px;
            gap: 8px;
        }

        .logo-container img {
            height: 40px;
            width: 200px;
        }

        /* Right Side (Form Section) */
        .right-side {
            padding: 50px;
        }

        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 10px;
            border: 1px solid black;
            border-radius: 20px;
            background-color: #fff;
            width: 100%;
            margin-bottom: 10px;
        }

        .social-btn:hover {
            background-color: #f5f5f5;
        }
        .login-btn {
            border-radius: 20px;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="vh-100 overflow-hidden">
        <div class="d-flex">
            <!-- Image Side -->
            <div class="left-side">

                <!-- Logo Positioned Inside -->
                <div class="logo-container">
                    <img 
                        src="/build/assets/lemonade-logo.svg" 
                        alt="Lemonade Logo"
                    />
                    <img 
                        src="/build/assets/icons/lemonade-black.svg" 
                        alt="Lemonade Text"
                    />
                </div>

                <!-- Full-Screen Homepage Image -->
                <img 
                    src="/build/assets/mockup-homepage.png" 
                    alt="Login Image" 
                    class="homepage-img"
                />
            </div>

            <!-- Form Side -->
            <div class="d-flex flex-grow-1 flex-col align-items-center justify-content-center text-black">
                <!-- Text -->
                <div class="text-center">
                    <h1 class="fw-bold">Crée un compte !</h1>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                </div>

                <!-- Google & Apple Buttons -->
                <div class="d-flex flex-column gap-2 w-100" style="max-width: 400px;">
                    <button class="social-btn">
                        <img 
                            src="/build/assets/icons/google-icon.svg" 
                            alt="Google Icon" 
                            height="20" 
                            width="20"
                        />
                        Se Connecter avec Google
                    </button>
                    <button class="social-btn">
                        <img 
                            src="/build/assets/icons/apple-black.svg" 
                            alt="Apple Icon" 
                            height="20" 
                            width="20"
                        />
                        Se Connecter avec Apple
                    </button>
                </div>

                <!-- Divider: Check app.css for Styling -->
                <div class="divider relative mt-2">
                    <span></span>
                    <p class="bg-white fw-semibold">OU</p>
                </div>
                

                <!-- "Déja un compte" Section -->
                <div class="absolute top-4 right-4 text-black text-sm fw-semibold">
                    <p>Déjà un compte ? 
                        <a href="{{ route('login') }}" class="text-black fw-bold">Connecte toi !</a>
                    </p>
                </div>


                <!-- Authentication Form -->
                 <form method="POST" action="{{ route('register') }}" class="w-100" style="max-width: 400px;">
                    @csrf

                    <!-- Name Input -->
                    <div class="mb-2">
                        <label for="nome" class="form-label">Nom</label>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <!-- Email Input -->
                    <div class="mb-2">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div class="mb-2">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Confirm Password Input -->
                    <div class="mb-3">
                        <label for="password-confirm" class="form-label">Confimer Mot de Passe</label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="bg-black text-white login-btn">
                            Créer mon compte
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
