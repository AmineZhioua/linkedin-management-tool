<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <title>Lemonade - S'inscrire</title>
    <style>
        .left-side img {
            max-width: 90%;
            height: 100%;
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
    </style>
</head>
<body>
    <div class="vh-100 overflow-hidden">
        <div class="d-flex">
            <!-- Image Side -->
            <div class="left-side relative">
                <div class="absolute flex align-items-center gap-2 top-2 left-3">
                    <img 
                        src="/build/assets/lemonade-logo.svg" 
                        alt="Lemonade Logo"
                        height="40"
                        width="40"
                    />
                    <img 
                        src="/build/assets/icons/lemonade-black.svg" 
                        alt="lemonade-text"
                        height="90"
                        width="90"
                    />
                </div>
                <img 
                    src="/build/assets/mockup-homepage.png" 
                    alt="Login Image" 
                    class="img-fluid"
                />
            </div>

            <!-- Form Side -->
            <div class="d-flex flex-grow-1 flex-col align-items-center justify-content-center">
                <!-- Text -->
                <div class="text-center">
                    <h1 class="fw-bold">Connecte-toi !</h1>
                    <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
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

                <!-- Check app.css for Styling -->
                <div class="divider relative mt-2">
                    <span></span>
                    <p class="bg-white fw-semibold">OU</p>
                </div>
                
                <!-- "Déja un compte" Section -->
                <div class="absolute top-4 right-4 text-black text-sm fw-semibold">
                    <p>Pas de compte ? 
                        <a href="{{ route('register') }}" class="text-black fw-bold">Inscris toi !</a>
                    </p>
                </div>

                <!-- Authentication Form -->
                <form method="POST" action="{{ route('login') }}" class="w-100" style="max-width: 400px;">
                    @csrf
                    
                    <!-- Email Input -->
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