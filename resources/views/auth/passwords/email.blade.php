<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <title>Lemonade - Reset Password</title>
</head>
<body class="bg-black">
    <div class="min-h-screen overflow-hidden">
        <!-- Header -->
        <x-header />

        <!-- Main Content -->
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card verification-card shadow-lg card-border-gradient">
                    <div class="card-body p-4 p-md-5">
                        <!-- Lock Icon -->
                        <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4">
                            <img 
                                src="/build/assets/icons/envelope-white.svg"
                                alt="Email Icon"
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
            </div>
        </div>
    </div>
</body>
</html>
