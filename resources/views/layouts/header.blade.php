<header class="header">
    <div class="header-left">
        <div class="logo-section">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Pemkot Samarinda" class="logo-img">
            <span class="logo-text">SiKota</span>
        </div>
    </div>

    <div class="header-center">
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" placeholder="Search" class="search-input">
        </div>
    </div>

    <div class="header-right">
        @auth
            {{-- User Profile Section for authenticated users --}}
             {{-- Tombol Tambah Agenda (hanya tampil kalau login) --}}
            <div class="add-agenda-btn" style="margin-right: 1rem;">
                <button
                    class="btn-create-agenda"
                    title="Tambah Agenda"
                    onclick="window.location.href='{{ route('agenda.create') }}'">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            {{-- user profile section --}}
            <div class="user-profile-section">
                <div class="user-profile" onclick="toggleDropdown()">
                    <div class="user-avatar">
                        @if(Auth::user()->profile_photo_path)
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
            {{-- Login Button for guests --}}
            <div class="login-section">
                <button class="login-btn" onclick="window.location.href='/login'">
                    <i class="fas fa-sign-in-alt"></i>
                    Login
                </button>
            </div>
        @endauth
    </div>

</header>
