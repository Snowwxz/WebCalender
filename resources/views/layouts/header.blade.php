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
            {{-- Notification Bell --}}
            @php
                $notificationCount = 0;
                $user = Auth::user();
                if ($user) {
                    if ($user->role === 'admin') {
                        $lastSeen = $user->last_seen_approve_at;
                        $notificationCount = \App\Models\Agenda::where('status', 'pending')
                            ->when($lastSeen, function ($q) use ($lastSeen) {
                                $q->where('created_at', '>', $lastSeen);
                            })
                            ->count();
                    } elseif ($user->role !== 'superadmin') {
                        $lastSeenU = $user->last_seen_notification_at;
                        $userId = $user->id_user;
                        $notificationCount = \App\Models\Agenda::where('id_user', $userId)
                            ->whereIn('status', ['approved', 'rejected'])
                            ->when($lastSeenU, function ($q) use ($lastSeenU) {
                                $q->where('updated_at', '>', $lastSeenU);
                            }, function ($q) {
                                $q->where('updated_at', '>=', now()->startOfDay());
                            })
                            ->count();
                    }
                    $notificationRoute = ($user->role === 'admin') ? route('approve') : route('agenda.notification');
                } else {
                    $notificationRoute = route('agenda.notification');
                }
            @endphp
            @if($user)
            <div class="notification-bell-section">
                <div class="notification-bell-wrapper" onclick="toggleNotificationDropdown()">
                    <i class="fas fa-bell"></i>
                    @if($notificationCount > 0)
                        <span class="notification-badge-header">{{ $notificationCount > 99 ? '99+' : $notificationCount }}</span>
                    @endif
                </div>
                <div class="notification-dropdown" id="notificationDropdown">
                    <div class="notification-dropdown-header">
                        <h3>Notifikasi</h3>
                        <a href="{{ $notificationRoute }}" class="view-all-link">Lihat Semua</a>
                    </div>
                    <div class="notification-dropdown-content" id="notificationContent">
                        <div class="notification-loading">Memuat notifikasi...</div>
                    </div>
                </div>
            </div>
            @endif

            {{-- user profile section --}}
            @if($user)
            <div class="user-profile-section">
                <div class="user-profile" onclick="toggleDropdown()">
                    <div class="user-avatar">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Pemkot Samarinda" class="profile-image">
                    </div>
                    <div class="user-info">
                        <div class="username">{{ $user->name }}</div>
                        <div class="user-email">
                            @if ($user->role === 'superadmin')
                                Super Admin
                            @elseif ($user->role === 'admin')
                                Protokol
                            @elseif ($user->unit)
                                {{ $user->unit->unit_name }}
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
            @endif
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
    @php
        $jsUser = Auth::user();
        $jsNotificationRoute = ($jsUser && $jsUser->role === 'admin') ? route('approve') : route('agenda.notification');
    @endphp
    const notificationRoute = '{{ $jsNotificationRoute ?? route('agenda.notification') }}';

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

    // Notification Dropdown Functions
    function toggleNotificationDropdown() {
        const dropdown = document.getElementById('notificationDropdown');
        const isOpen = dropdown.classList.contains('show');

        dropdown.classList.toggle('show');

        // Jika dropdown dibuka, load notifikasi
        if (!isOpen) {
            loadNotifications();
        }
    }

    function loadNotifications() {
        const content = document.getElementById('notificationContent');
        content.innerHTML = '<div class="notification-loading">Memuat notifikasi...</div>';

        fetch('{{ route("api.notifications") }}')
            .then(response => response.json())
            .then(data => {
                if (data.notifications && data.notifications.length > 0) {
                    let html = '';
                    data.notifications.forEach(notif => {
                        const statusText = notif.status === 'approved' ? 'Disetujui' :
                                         notif.status === 'rejected' ? 'Ditolak' : 'Menunggu';
                        const statusClass = notif.status === 'approved' ? 'approved' :
                                          notif.status === 'rejected' ? 'rejected' : 'pending';
                        const icon = notif.status === 'approved' ? 'fa-check-circle' :
                                   notif.status === 'rejected' ? 'fa-times-circle' : 'fa-clock';
                        const date = new Date(notif.updated_at || notif.created_at);
                        const dateStr = date.toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        html += `
                            <div class="notification-item ${statusClass}" onclick="window.location.href='${notificationRoute}'">
                                <div class="notification-icon">
                                    <i class="fas ${icon}"></i>
                                </div>
                                <div class="notification-text">
                                    <div class="notification-title">${notif.agenda_name}</div>
                                    <div class="notification-message">Agenda ${statusText.toLowerCase()}</div>
                                    <div class="notification-time">${dateStr}</div>
                                </div>
                            </div>
                        `;
                    });
                    content.innerHTML = html;
                } else {
                    content.innerHTML = '<div class="notification-empty">Tidak ada notifikasi baru</div>';
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                content.innerHTML = '<div class="notification-error">Gagal memuat notifikasi</div>';
            });
    }

    // Tutup notification dropdown kalau klik di luar area
    window.addEventListener('click', function(e) {
        const notificationDropdown = document.getElementById('notificationDropdown');
        const notificationBell = document.querySelector('.notification-bell-wrapper');

        if (notificationDropdown && notificationBell && !notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
            notificationDropdown.classList.remove('show');
        }
    });

    // Auto refresh notification count setiap 30 detik
    setInterval(function() {
        fetch('{{ route("api.notifications") }}')
            .then(response => response.json())
            .then(data => {
                const badge = document.querySelector('.notification-badge-header');
                if (data.count > 0) {
                    if (badge) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                    } else {
                        const bellWrapper = document.querySelector('.notification-bell-wrapper');
                        if (bellWrapper) {
                            const newBadge = document.createElement('span');
                            newBadge.className = 'notification-badge-header';
                            newBadge.textContent = data.count > 99 ? '99+' : data.count;
                            bellWrapper.appendChild(newBadge);
                        }
                    }
                } else {
                    if (badge) {
                        badge.remove();
                    }
                }
            })
            .catch(error => console.error('Error refreshing notification count:', error));
    }, 30000);
</script>

<!-- Tambahkan Bootstrap Icons untuk icon centang bulat -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

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
        const popup = document.createElement('div');
        popup.className = 'toastify-popup toastify-success';
        popup.innerHTML = `
            <i class="bi bi-check-circle-fill" style="color:#5FA776; font-size:16px;"></i>
            <span>${message}</span>
        `;
        document.body.appendChild(popup);

        popup.classList.add('toastify-popup-show');
        setTimeout(() => {
            popup.classList.remove('toastify-popup-show');
            popup.classList.add('toastify-popup-hide');
            setTimeout(() => popup.remove(), 300);
        }, 2500);
    }
</script>

<style>
    .toastify-success {
        position: fixed;
        top: 90px;
        left: 50%;
        transform: translateX(-50%);
        background: #E9F4EC;
        color: #234B2C;
        border: 1px solid #A6C8A3;
        border-radius: 6px;
        padding: 8px 18px;
        font-size: 14px;
        font-weight: 500;
        font-family: 'Poppins', sans-serif;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        z-index: 9999;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        width: auto;
        max-width: 300px;
        min-height: unset;
    }

    .toastify-popup-show {
        animation: toastIn 0.35s ease forwards;
    }

    .toastify-popup-hide {
        animation: toastOut 0.25s ease forwards;
    }

    @keyframes toastIn {
        0% {
            opacity: 0;
            transform: translate(-50%, -30px) scale(0.95);
        }

        80% {
            opacity: 1;
            transform: translate(-50%, 8px) scale(1.03);
        }

        100% {
            opacity: 1;
            transform: translate(-50%, 0) scale(1);
        }
    }

    @keyframes toastOut {
        from {
            opacity: 1;
            transform: translate(-50%, 0) scale(1);
        }

        to {
            opacity: 0;
            transform: translate(-50%, -10px) scale(0.95);
        }
    }
</style>
