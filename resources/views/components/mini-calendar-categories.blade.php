@php
    $isDashboard = (Auth::check() && request()->is('dashboard*')) || Auth::check();
@endphp

<!-- Mini Calendar -->
<div class="content-mini-calendar-section">
    <!-- Navigation Tabs -->
    <div class="content-nav-tabs">
        @if ($isDashboard)
            <a href="/dashboard/hari"
                class="content-nav-tab {{ request()->is('dashboard/hari') ? 'active' : '' }}">Hari</a>
            <a href="/dashboard/bulan"
                class="content-nav-tab {{ request()->is('dashboard/bulan') ? 'active' : '' }}">Bulan</a>
            <a href="/dashboard/tahun"
                class="content-nav-tab {{ request()->is('dashboard/tahun') ? 'active' : '' }}">Tahun</a>
        @else
            <a href="/hari" class="content-nav-tab {{ request()->is('hari') ? 'active' : '' }}">Hari</a>
            <a href="/bulan"
                class="content-nav-tab {{ request()->is('/') || request()->is('bulan') ? 'active' : '' }}">Bulan</a>
            <a href="/tahun" class="content-nav-tab {{ request()->is('tahun') ? 'active' : '' }}">Tahun</a>
        @endif
    </div>
    <div class="content-section-title">
        <i class="fas fa-calendar"></i>
        <span>Kalender</span>
    </div>

    <div class="content-mini-calendar">
        <div class="content-mini-calendar-header">
            <button class="content-mini-nav-btn" onclick="changeMiniMonth(-1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <span id="miniCalendarHeader">Okt 2025</span>
            <button class="content-mini-nav-btn" onclick="changeMiniMonth(1)">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        <div class="content-mini-calendar-weekdays">
            <div>Sen</div>
            <div>Sel</div>
            <div>Rab</div>
            <div>Kam</div>
            <div>Jum</div>
            <div>Sab</div>
            <div class="weekend">Min</div>
        </div>
        <div class="content-mini-calendar-days" id="miniCalendarDays"></div>
    </div>
</div>

<!-- Agenda Categories -->
<div class="content-agenda-section">
    <div class="content-section-title">
        <i class="fas fa-list"></i>
        <span>Kategori Agenda</span>
    </div>

    <div class="content-agenda-categories">
        <label class="content-category-item public {{ !$isDashboard ? 'disabled' : '' }}">
            <input type="checkbox" id="filterPublic" name="filterPublic" checked {{ !$isDashboard ? 'disabled' : '' }}>
            <span class="content-custom-checkbox"></span>
            <span class="content-dot"></span>
            <span class="content-text">Publik</span>
        </label>

        @auth
            <label class="content-category-item private">
                <input type="checkbox" id="filterPrivate" name="filterPrivate" checked>
                <span class="content-custom-checkbox"></span>
                <span class="content-dot"></span>
                <span class="content-text">Privasi</span>
            </label>
        @endauth
    </div>
</div>
