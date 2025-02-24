<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <title>Lemonade - S'inscrire</title>
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

        /* Adjustments for mobile screens */
        @media (max-width: 767px) {
            .absolute.top-4.right-4 {
                position: static; /* Ensure the link is no longer positioned absolutely */
                margin-top: 20px; /* Add space above for better visibility */
                text-align: center; /* Center the link */
                padding: 10px; /* Add padding if necessary */
            }
        }
    </style>
</head>
<body>
    <div class="vh-100 overflow-hidden">
        <div class="d-flex">
            <div class="left-side">
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
                <img 
                    src="/build/assets/mockup-homepage-1.png" 
                    alt="Login Image" 
                    class="homepage-img"
                />
            </div>
            <div class="d-flex flex-grow-1 flex-col align-items-center justify-content-center">
                <div class="text-center text-black">
                    <h1 class="fw-bold">Connecte-toi !</h1>
                </div>
                <div class="d-flex flex-column gap-2 w-100 text-black" style="max-width: 400px;">
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

                <div class="divider relative mt-2">
                    <span></span>
                    <p class="bg-white fw-semibold">OU</p>
                </div>
                <!-- Updated link position -->
                <div class="absolute top-4 right-4 text-black text-sm fw-semibold">
                    <p>Pas de compte ? 
                        <a href="{{ route('register') }}" class="text-black fw-bold">Inscris toi !</a>
                    </p>
                </div>
                <form method="POST" action="{{ route('login') }}" class="w-100" style="max-width: 400px;">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            Se souvenir de moi
                        </label>
                    </div>

                    <!-- Login Button -->
                    <div class="d-grid">
                        <button type="submit" class="bg-black text-white login-btn">
                            Se connecter
                        </button>
                    </div>

                    <!-- Forgot Password -->
                    @if (Route::has('password.request'))
                        <div class="text-center mt-3">
                            <a class="btn btn-link text-black" href="{{ route('password.request') }}">
                                Mot de passe oublié ?
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</body>
</html>
