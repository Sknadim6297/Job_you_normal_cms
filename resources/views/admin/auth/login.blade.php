<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - JobYou</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: #f5f7fb;
            font-family: Poppins, sans-serif;
            color: #172033
        }

        .login-wrap {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px
        }

        .login-card {
            width: min(420px, 100%);
            background: #fff;
            border: 1px solid #e7ebf2;
            border-radius: 18px;
            padding: 36px;
            box-shadow: 0 20px 60px rgba(23, 32, 51, .08)
        }

        .login-logo {
            display: block;
            width: 170px;
            height: 52px;
            object-fit: contain;
            object-position: left center
        }

        .form-control {
            padding: .75rem;
            border-radius: 9px
        }

        .btn {
            border-radius: 9px;
            padding: .7rem
        }
    </style>
</head>

<body>
    <div class="login-wrap">
        <div class="login-card">
            @php
                $loginLogoPath = \App\Models\SiteSetting::values()['logo_path'] ?? 'assets/img/logo.png';
            @endphp
            <div class="mb-4">
                <img
                    class="login-logo"
                    src="{{ \App\Support\ImageResolver::resolve($loginLogoPath, asset('assets/img/logo.png')) }}"
                    data-fallback-image="{{ asset('assets/img/logo.png') }}"
                    alt="JobYou logo"
                >
            </div>
            <h4 class="mb-0">JobYou</h4>
            <small class="text-muted d-block mb-4">Admin workspace</small>

            <h2 class="h4 mb-1">Welcome back</h2>
            <p class="text-muted mb-4">Sign in to manage your website.</p>

            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('admin.login.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="email">Email address</label>
                    <input class="form-control" type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email address" required autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-control" type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
                    <label class="form-check-label text-muted" for="remember">Remember me</label>
                </div>

                <button class="btn btn-primary w-100">Sign in <i class="bi bi-arrow-right ms-1"></i></button>
            </form>
        </div>
    </div>
    <script src="{{ asset('assets/image-fallback.js') }}" defer></script>
</body>
</html>