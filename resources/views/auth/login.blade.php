<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiKota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://kit.fontawesome.com/a2e0e6ad3f.js" crossorigin="anonymous"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: #e6f9f1;
            height: 100vh;
            overflow: hidden;
        }

        .login-container {
            display: flex;
            height: 100vh;
        }

        .left-section {
            flex: 1;
            background-color: #ffffff;
            padding: 0 80px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.05);
        }

        .right-section {
            flex: 1;
            background: linear-gradient(135deg, #e6f9f1 0%, #b2dfdb 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0;
        }

        .right-section img {
            width: 90%;
            height: auto;
            max-width: none;
            transform: scale(1.8);
            margin-left: 80px;
            object-fit: contain;
        }

        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .logo img {
            width: 50px;
            margin-right: 10px;
        }

        .btn-custom {
            background-color: #00695c;
            color: white;
            font-weight: 600;
            border-radius: 8px;
        }

        .btn-custom:hover {
            background-color: #004d40;
            color: white;
        }

        .btn-sso {
            background-color: white;
            color: #4a4a4a;
            font-weight: 500;
            border: 1px solid #d1d1d1;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .btn-sso:hover {
            background-color: #f3f3f3;
            border-color: #c0c0c0;
        }

        .form-control:focus {
            border-color: #00695c !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 105, 92, 0.25) !important;
        }

        .form-check-input:checked {
            background-color: #00695c !important;
            border-color: #00695c !important;
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 105, 92, 0.25) !important;
            border-color: #00695c !important;
        }

        a {
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .text-signup {
            color: #00695c;
            font-weight: 600;
            text-decoration: none;
            transition: 0.2s ease;
        }

        .text-signup:hover {
            color: #004d40;
            text-decoration: none;
        }


        @media (max-width: 992px) {
            .login-container {
                flex-direction: column;
            }

            .right-section {
                display: none;
            }

            .left-section {
                border-radius: 0;
                padding: 40px 20px;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">

        <div class="left-section">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
                <div>
                    <strong>SiKota</strong><br>
                    <small>Sistem Kalender Kota Samarinda</small>
                </div>
            </div>

            <h3 class="fw-bold text-teal">Halo, Selamat Datang!</h3>
            <p class="text-muted mb-4">Silahkan Masuk ke akun Anda!</p>

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

                <button type="button" class="btn btn-sso w-100 mt-3">
                    <i class="fa-solid fa-key me-2"></i> Sign In with SSO
                </button>

            </form>

            <p class="text-center mt-4">
                Don't have an account?
                <a href="#" class="text-signup">Sign Up</a>
            </p>
        </div>

        <div class="right-section">
            <img src="{{ asset('images/calendar (2).png') }}" alt="Calendar Illustration">
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>