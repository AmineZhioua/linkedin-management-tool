<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <title>Lemonade - Reset Password</title>

    <style>
        .logo-container {
            position: absolute;
            top: 15px;
            left: 15px;
            display: flex;
            align-items: center;
        }

        .logo-container img {
            height: 30px;
            width: auto;
        }

        .logo-container img:first-child {
            height: 40px;
            width: auto;
            margin-right: 5px;
        }

        .verification-card {
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            width: 100%;
            max-width: 800px;
            margin-top: -250px;
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
            width: 100%;
            max-width: 642px;
            margin-left: auto;
            margin-right: auto;
        }

        body {
            background-color: white;
        }

        /* Footer Styling */
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            position: absolute;
            bottom: 0;
            width: 100%;
        }

        .footer p {
            margin: 0;
        }

        .footer a {
            text-decoration: none;
            color: #007bff;
        }

        .footer a:hover {
            text-decoration: underline;
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

        <!-- Reset Password Card -->
        <div class="verification-card shadow-lg card-border-gradient">
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
                Reset Your Password
            </h2>

            <div class="card-body">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <!-- Email Input -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email Address</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Confirm Password Input -->
                    <div class="mb-3">
                        <label for="password-confirm" class="form-label fw-semibold">Confirm Password</label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-semibold">
                            {{ __('Reset Password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
