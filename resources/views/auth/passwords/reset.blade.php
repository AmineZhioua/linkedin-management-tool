<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <title>Lemonade - Reset Password</title>
</head>
<body>
    <div class="vh-100 d-flex flex-column align-items-center justify-content-center position-relative p-3 reset">
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
                    src="/build/assets/icons/lemonade-black.svg" 
                    alt="Lemonade Text"
                    class="lemonade-text"
                    height="40"
                    width="100"
                />
            </a>
        </div>
        

        <!-- Reset Password Card -->
        <div class="verification-card shadow-lg card-border-gradient mt-1">
            <!-- Lock Icon -->
            <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4">
                <img 
                    src="/build/assets/icons/envelope-white.svg"
                    alt="Lock Icon"
                    height="40"
                    width="40"
                />
            </div>

            <h2 class="verification-title text-center mb-4 fw-bold fs-2">
                Réinitialiser votre mot de passe
            </h2>

            <div class="card-body">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <!-- Email Input -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Addresse Email :</label>
                        <input 
                            id="email" 
                            type="email" 
                            class="form-control @error('email') is-invalid @enderror" 
                            name="email" value="{{ $email ?? old('email') }}" 
                            required autocomplete="email" 
                            autofocus
                        />

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Nouveau Mot de Passe :</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Confirm Password Input -->
                    <div class="mb-3">
                        <label for="password-confirm" class="form-label fw-semibold">Confirmer votre Mot de Passe :</label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-semibold">
                            {{ __('Réinitialiser') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
