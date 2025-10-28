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

    <!-- Toastify -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if ($errors->any())
                showErrorToast("Email atau password salah!");
            @endif
        });

        // ====== TOAST ERROR DENGAN BUTTON ======
        function showErrorToast(message) {
            const toastContent = document.createElement('div');
            toastContent.style.display = "flex";
            toastContent.style.flexDirection = "column";
            toastContent.style.alignItems = "center";
            toastContent.style.gap = "8px";

            // Judul & pesan
            const title = document.createElement('div');
            title.innerText = "Login Gagal";
            title.style.fontFamily = "Poppins, sans-serif";
            title.style.fontWeight = "600";
            title.style.fontSize = "15px";
            title.style.color = "#7A1C1C";

            const text = document.createElement('div');
            text.innerText = message;
            text.style.fontFamily = "Poppins, sans-serif";
            text.style.fontSize = "14px";
            text.style.color = "#2F3E35";
            text.style.textAlign = "center";

            // Tombol OK
            const button = document.createElement('button');
            button.innerText = "Ya";
            button.style.fontFamily = "Poppins, sans-serif";
            button.style.fontSize = "13px";
            button.style.fontWeight = "500";
            button.style.backgroundColor = "#F47C7C";
            button.style.color = "white";
            button.style.border = "none";
            button.style.borderRadius = "6px";
            button.style.padding = "5px 14px";
            button.style.cursor = "pointer";
            button.style.transition = "background 0.2s ease";

            button.addEventListener('mouseenter', () => {
                button.style.backgroundColor = "#ff6b6b";
            });
            button.addEventListener('mouseleave', () => {
                button.style.backgroundColor = "#F47C7C";
            });

            // Buat Toastify
            const toast = Toastify({
                node: toastContent,
                duration: -1, // biar gak auto hilang
                gravity: "top",
                position: "center",
                close: false,
                offset: {
                    x: 0,
                    y: 20
                },
                style: {
                    background: "#FFF1E6",
                    border: "1px solid #F7B7B7",
                    borderRadius: "12px",
                    padding: "18px 24px",
                    boxShadow: "0 6px 14px rgba(0,0,0,0.08)",
                    textAlign: "center",
                },
            });

            // Tutup kalau tombol diklik
            button.addEventListener('click', () => {
                toast.hideToast();
            });

            // Masukkan elemen ke dalam toast
            toastContent.appendChild(title);
            toastContent.appendChild(text);
            toastContent.appendChild(button);

            // Tampilkan
            toast.showToast();
        }
    </script>

</body>

</html>
