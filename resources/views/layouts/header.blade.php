<header class="header">
    <div class="header-left">
        <button class="sidebar-toggle" onclick="toggleSidebar()" title="Toggle Sidebar">
            <i class="fas fa-bars"></i>
        </button>
        <a href="{{ route('dashboard') }}" class="logo-section"
            style="text-decoration: none; color: inherit; display: flex; align-items: center;">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Pemkot Samarinda" class="logo-img">
            <span class="logo-text" style="font-weight: 600; margin-left: 6px;">SiKota</span>
        </a>
    </div>

    <div class="header-right">
        @auth
            {{-- user profile section --}}
            <div class="user-profile-section">
                <div class="user-profile" onclick="toggleDropdown()">
                    <div class="user-avatar">
                        @if (Auth::user()->profile_photo_path)
                            <img src="{{ Auth::user()->profile_photo_path }}" alt="Profile" class="profile-image">
                        @else
                            <div class="profile-initials">{{ substr(Auth::user()->name, 0, 2) }}</div>
                        @endif
                    </div>
                    <div class="user-info">
                        <div class="username">{{ Auth::user()->name }}</div>
                        <div class="user-email">
                            @if (Auth::user()->role === 'superadmin')
                                Super Admin
                            @elseif (Auth::user()->role === 'admin')
                                Protokol
                            @elseif (Auth::user()->unit)
                                {{ Auth::user()->unit->unit_name }}
                            @else
                                Belum Ada Instansi
                            @endif
                        </div>
                    </div>
                    <div class="dropdown-arrow">
                        <i class="fas fa-chevron-up"></i>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>

                {{-- Dropdown Menu --}}
                <div class="user-dropdown" id="userDropdown">
                    <div class="dropdown-item" onclick="window.location.href='{{ route('profile.edit') }}'">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
                    </div>
                    <div class="dropdown-item" onclick="window.location.href='{{ route('profile.edit') }}'">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </div>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" class="dropdown-form">
                        @csrf
                        <button type="submit" class="dropdown-item logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="login-section">
                <button class="login-btn" onclick="window.location.href='/login'">
                    <i class="fas fa-sign-in-alt"></i>
                    Login
                </button>
            </div>
        @endauth
    </div>

</header>

<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('show');
    }

    // Tutup dropdown kalau klik di luar area
    window.addEventListener('click', function(e) {
        const dropdown = document.getElementById('userDropdown');
        const userProfile = document.querySelector('.user-profile');

        if (dropdown && !userProfile.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });
</script>

<!-- Tambahkan Toastify CSS & JS -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const logoutForm = document.querySelector('.dropdown-form');

        // ✅ Safety check
        if (!logoutForm) {
            console.warn("dropdown-form tidak ditemukan di halaman ini.");
            return;
        }

        logoutForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // ======== ISI TOAST CUSTOM ========
            const toastContent = document.createElement('div');
            toastContent.innerHTML = `
                <div style="
                    font-family: 'Poppins', sans-serif;
                    color: #2F3E35;
                    font-weight: 500;
                    font-size: 15px;
                    margin-bottom: 12px;
                ">
                    Yakin ingin keluar dari akun?
                </div>
                <div style="display: flex; gap: 8px; justify-content: center;">
                    <button id="confirmLogout" style="
                        background: #F7B7B7;
                        border: none;
                        padding: 7px 16px;
                        border-radius: 8px;
                        color: #7A1C1C;
                        font-weight: 600;
                        font-family: 'Poppins', sans-serif;
                        cursor: pointer;
                        transition: all 0.25s ease;
                    "
                    onmouseover="this.style.background='#F4A8A8'; this.style.color='#691414';"
                    onmouseout="this.style.background='#F7B7B7'; this.style.color='#7A1C1C';"
                    onmousedown="this.style.background='#E68D8D'; this.style.color='#5C1111';"
                    onmouseup="this.style.background='#F4A8A8'; this.style.color='#691414';">
                        Ya, keluar
                    </button>

                    <button id="cancelLogout" style="
                        background: #E6E7E8;
                        border: none;
                        padding: 7px 16px;
                        border-radius: 8px;
                        color: #2F3E35;
                        font-weight: 600;
                        font-family: 'Poppins', sans-serif;
                        cursor: pointer;
                        transition: all 0.2s ease;
                    "
                    onmouseover="this.style.background='#D9DADB';"
                    onmouseout="this.style.background='#E6E7E8';">
                        Batal
                    </button>

                </div>
            `;

            const toast = Toastify({
                node: toastContent,
                duration: -1,
                gravity: "top",
                position: "center",
                stopOnFocus: true,
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
                    boxShadow: "0 6px 20px rgba(0, 0, 0, 0.08)",
                    textAlign: "center",
                    display: "flex",
                    flexDirection: "column",
                    alignItems: "center",
                    animation: "fadeIn 0.3s ease",
                },
            }).showToast();

            toastContent.querySelector('#confirmLogout').addEventListener('click', () => {
                toast.hideToast();

                // Simpan pesan ke localStorage biar bisa ditampilkan di halaman berikutnya
                localStorage.setItem('logoutSuccess', 'Berhasil keluar dari akun!');

                // Kirim form logout (redirect ke halaman login)
                logoutForm.submit();
            });


            toastContent.querySelector('#cancelLogout').addEventListener('click', () => {
                toast.hideToast();
            });

            showConfirm();
        });
    });

    // ======== TOAST SUKSES (senada tema hijau pastel) ========
    function showSuccessToast(message) {
        const successToast = document.createElement('div');
        successToast.innerHTML = `
            <div style="
                font-family: 'Poppins', sans-serif;
                font-weight: 500;
                font-size: 15px;
                color: #2F3E35;
            ">
                ${message}
            </div>
        `;

        Toastify({
            node: successToast,
            duration: 2500,
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
                borderRadius: "10px",
                padding: "14px 28px",
                boxShadow: "0 6px 14px rgba(0,0,0,0.08)",
                display: "flex",
                justifyContent: "center",
                alignItems: "center",
                animation: "fadeIn 0.3s ease",
            }
        }).showToast();
    }
</script>
