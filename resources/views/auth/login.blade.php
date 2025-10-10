<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiKota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a2e0e6ad3f.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>

    <div class="login-container">

        <div class="left-section">
            <div class="back-btn">
                <a href="{{ url('/') }}" class="back-link">
                    <i class="bi bi-arrow-left-short"></i>
                    <span>Kembali</span>
                </a>
            </div>

            <div class="form-wrapper">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
                <div>
                    <strong>SiKota</strong><br>
                    <small>Sistem Kalender Kota Samarinda</small>
                </div>
            </div>
        </div>

            <h3 class="fw-bold text-teal">Halo, Selamat Datang!</h3>
            <p class="text-muted mb-5">Silahkan Masuk ke akun Anda!</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <input type="email" class="form-control" name="email" placeholder="Email"
                        value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                    @error('password')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                        <label class="form-check-label" for="remember_me">Remember me</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="text-secondary">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-custom w-100">Sign In</button>

            </form>

            <p class="text-center mt-4 text-gray">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-signup">Register disini</a>
            </p>
        </div>

        <div class="right-section">
            <img src="{{ asset('images/calendar (2).png') }}" alt="Calendar Illustration">
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
