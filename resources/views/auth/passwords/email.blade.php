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
            <!-- Lock Icon -->
            <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4">
                <img 
                    src="/build/assets/icons/envelope-white.svg"
                    alt="Lock Icon"
                    height="40"
                    width="40"
                />
            </div>

            <!-- Title -->
            <h2 class="verification-title text-center mb-4 fw-bold fs-2">
                Reset Your Password
            </h2>

            <!-- Status Alert -->
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Reset Form -->
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email Address</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary fw-semibold">
                        {{ __('Send Password Reset Link') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>