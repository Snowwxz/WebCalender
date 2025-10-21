<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiKota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
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

                <h3 class="fw-bold text-teal mt-4">Halo, Selamat Datang!</h3>
                <p class="text-muted mb-5">Silahkan masuk ke akun Anda!</p>

                {{-- LOGIN FORM --}}
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <input type="email" class="form-control" name="email" placeholder="Email"
                            value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3 position-relative">
                        <input type="password" class="form-control pe-5" id="password" name="password"
                            placeholder="Password" required>
                        <i class="bi bi-eye-slash password-toggle" id="togglePassword"></i>
                        @error('password')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                            <label class="form-check-label" for="remember_me">Remember me</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-custom w-100">Sign In</button>
                </form>

                <p class="text-center mt-4 text-gray">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-signup">Register disini</a>
                </p>
            </div>
        </div>

        <div class="right-section">
            <img src="{{ asset('images/calendar (2).png') }}" alt="Calendar Illustration">
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if ($errors->any())
                Swal.fire({
                    title: 'Login Gagal',
                    text: 'Email atau password salah!',
                    icon: 'error',
                    toast: true,
                    position: 'top',
                    background: '#FFF1E6',
                    color: '#333',
                    customClass: {
                        popup: 'notif-swal-popup',
                        title: 'notif-swal-title',
                        confirmButton: 'notif-swal-confirm',
                    },
                    confirmButtonText: 'OK',
                });
            @endif
        });
    </script>

    <style>
        .notif-swal-popup {
            width: 280px !important;
            border-radius: 12px !important;
            padding: 1rem 1.2rem !important;
            font-family: 'Poppins', sans-serif;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
            border: 1px solid #f5dada;
        }

        .notif-swal-title {
            font-size: 0.95rem !important;
            font-weight: 600 !important;
            color: #444 !important;
        }

        .notif-swal-confirm {
            background-color: #F47C7C !important;
            color: white !important;
            border-radius: 6px !important;
            padding: 4px 10px !important;
            font-size: 0.75rem !important;
            border: none !important;
            transition: background-color 0.2s ease;
        }

        .notif-swal-confirm:hover {
            background-color: #ff6b6b !important;
        }
    </style>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const isPassword = password.getAttribute('type') === 'password';
            password.setAttribute('type', isPassword ? 'text' : 'password');
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    </script>
</body>

</html>