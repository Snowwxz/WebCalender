<header class="header">
    <div class="header-left">
        <div class="logo-section">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Pemkot Samarinda" class="logo-img">
            <span class="logo-text">SiKota</span>
        </div>
    </div>

    <div class="header-center">
        @if (!request()->routeIs('approve'))
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" placeholder="Search" class="search-input">
            </div>
        @endif
    </div>


    <div class="header-right">
        @auth
            {{-- Notification Bell Icon --}}
            @if (!request()->routeIs('superadmin*'))
                <div class="notification-bell">
                    @if (Auth::user()->role === 'admin')
                        <a href="{{ route('approve') }}" class="bell-link" title="Kelola Pengajuan Agenda">
                            <i class="fas fa-bell"></i>
                        </a>
                    @else
                        <a href="{{ route('agenda.notification') }}" class="bell-link" title="Notifikasi Agenda Saya">
                            <i class="fas fa-bell"></i>
                        </a>
                    @endif
                </div>
            @endif

            {{-- Tombol tambah agenda (jangan tampil di superadmin) --}}
            @if (Auth::user()->role !== 'superadmin')
                <div class="add-agenda-btn {{ request()->routeIs('agenda.create') ? 'active' : '' }}">
                    <a href="{{ route('agenda.create') }}" class="agenda-badge" title="Tambah Agenda">
                        <i class="fas fa-plus"></i>
                        <span>Tambah Agenda</span>
                    </a>
                </div>
            @endif

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
                        <div class="user-email">{{ Auth::user()->email }}</div>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

            Swal.fire({
                title: 'Konfirmasi Logout',
                text: 'Yakin ingin keluar dari akun?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, keluar',
                cancelButtonText: 'Batal',
                toast: false, // ❗ Logout dialog tidak cocok pakai toast
                position: 'center',
                background: '#FFF1E6',
                color: '#333',
                customClass: {
                    popup: 'notif-swal-popup',
                    title: 'notif-swal-title',
                    confirmButton: 'notif-swal-confirm',
                    cancelButton: 'notif-swal-cancel'
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    logoutForm.submit();
                }
            });

             showConfirm();
        });

    }); // ✅ INI YANG TADI HILANG
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

    .notif-swal-cancel {
        background-color: #f1f1f1 !important;
        color: #444 !important;
        border-radius: 6px !important;
        padding: 4px 10px !important;
        font-size: 0.75rem !important;
        border: none !important;
        transition: background-color 0.2s ease;
    }

    .notif-swal-cancel:hover {
        background-color: #e6e6e6 !important;
    }
</style>
