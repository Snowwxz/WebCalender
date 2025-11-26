@php
    $isDashboard = (Auth::check() && request()->is('dashboard*')) || Auth::check();
@endphp

<style>
    /* Segmented tabs style to match the provided design */
    .content-nav-tabs {
        display: flex; /* block-level so auto margins work */
        align-items: center;
        justify-content: flex-start;
        gap: 10px;
        background: #ffffff;
        padding: 6px 8px; /* more compact */
        border-radius: 20px;
        box-shadow: 0 6px 14px rgba(2, 6, 23, 0.06);
        border: 1px solid #e9eef3;
        width: fit-content; /* intrinsic width */
        margin: 0 auto 10px; /* center horizontally */
        transform: translateX(-8px); /* slight visual nudge to the left */
    }

    .content-nav-tab {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 18px;
        border-radius: 14px;
        text-decoration: none;
        color: #475569; /* slate-600 */
        font-weight: 600;
        background: transparent;
        transition: background 150ms ease, color 150ms ease;
    }

    /* Hover/focus state for any non-active tab (Hari, Bulan, Tahun) */
    .content-nav-tab:not(.active):hover,
    .content-nav-tab:not(.active):focus-visible {
        background: #eef6f2; /* soft mint */
        color: #6aa88b;     /* desaturated green text */
    }

    /* Active (solid green) */
    .content-nav-tab.active { background: #86aa94; color: #ffffff; }

    /* No special-case needed for middle tab anymore */
</style>
<!-- Mini Agenda -->
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
        <i class="fas fa-calendar-week"></i>
        <span>Agenda Bulanan</span>
    </div>

    <div class="mini-agenda-card">
        <div class="mini-agenda-header">
            <div>
                <p class="mini-agenda-label">Ringkasan Agenda</p>
                <h4 id="miniAgendaMonthLabel">November 2025</h4>
            </div>
            <div class="mini-agenda-controls">
                <button class="content-mini-nav-btn" onclick="changeMiniAgendaMonth(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="content-mini-nav-btn" onclick="changeMiniAgendaMonth(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        <div class="mini-agenda-list" id="miniAgendaList"
             data-notification-url="{{ Auth::check() ? route('agenda.notification') : '/dashboard/notification' }}"
             data-is-dashboard="{{ $isDashboard ? '1' : '0' }}">
            <div class="mini-agenda-placeholder">
                Memuat agenda...
            </div>
        </div>
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

@once
    <style>
        .mini-agenda-card {
            background: #ffffff;
            border: 1px solid #e8edf0;
            border-radius: 20px;
            padding: 18px;
            box-shadow: 0 10px 24px rgba(3, 7, 18, 0.08);
            display: flex;
            flex-direction: column;
            gap: 16px;
            min-height: 420px;
        }

        .mini-agenda-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .mini-agenda-label {
            font-size: 13px;
            color: #94a3b8;
            margin: 0 0 4px;
        }

        #miniAgendaMonthLabel {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            color: #243746;
        }

        .mini-agenda-controls {
            display: inline-flex;
            gap: 8px;
        }

        .mini-agenda-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
            overflow-y: auto;
            max-height: 360px;
            padding-right: 6px;
        }

        .mini-agenda-list::-webkit-scrollbar {
            width: 6px;
        }

        .mini-agenda-list::-webkit-scrollbar-thumb {
            background: rgba(134, 170, 148, 0.5);
            border-radius: 6px;
        }

        .mini-agenda-item {
            border: 1px solid #edf2f7;
            border-radius: 16px;
            padding: 4px 0;
            background: #fdfdfd;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .mini-agenda-item.open {
            border-color: #cfe8d9;
            box-shadow: 0 8px 18px rgba(56, 142, 89, 0.12);
        }

        .mini-agenda-toggle {
            width: 100%;
            border: none;
            background: transparent;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            cursor: pointer;
            text-align: left;
        }

        .mini-agenda-title {
            font-weight: 600;
            color: #1f2a37;
            font-size: 15px;
            flex: 1;
        }

        .mini-agenda-chevron {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            background: #f4f7f9;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #6b7a8c;
            transition: transform 0.2s ease;
        }

        .mini-agenda-item.open .mini-agenda-chevron {
            transform: rotate(90deg);
        }

        .mini-agenda-body {
            display: none;
            padding: 0 14px 14px;
            border-top: 1px dashed #dbe3ea;
        }

        .mini-agenda-item.open .mini-agenda-body {
            display: block;
        }

        .mini-agenda-meta {
            display: flex;
            flex-direction: column;
            gap: 4px;
            color: #64748b;
            font-size: 13px;
            margin-top: 12px;
        }

        .mini-agenda-meta i {
            color: #86aa94;
            margin-right: 6px;
        }

        .mini-agenda-actions {
            margin-top: 12px;
        }

        .mini-agenda-status-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .mini-agenda-status-label {
            font-size: 13px;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .mini-agenda-status-label i {
            color: #94a3b8;
            font-size: 13px;
        }

        .mini-agenda-status {
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
            padding: 4px 10px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-width: 70px;
            justify-content: center;
            flex-shrink: 0;
            text-transform: uppercase;
        }

        .mini-agenda-status.status-public {
            color: #2f3e35;
            background: #d4f6e4;
        }

        .mini-agenda-status.status-private {
            color: #92400e;
            background: #fef3c7;
        }

        .mini-agenda-detail-wrapper {
            margin-top: 10px;
            display: flex;
            justify-content: flex-start;
        }

        .mini-agenda-detail-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: #86aa94;
            color: #ffffff;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: background 0.2s ease, transform 0.1s ease;
            white-space: nowrap;
        }

        .mini-agenda-detail-btn:hover {
            background: #6d8f7a;
            transform: translateY(-1px);
            color: #ffffff;
        }

        .mini-agenda-detail-btn:active {
            transform: translateY(0);
        }

        .mini-agenda-placeholder {
            font-size: 14px;
            color: #94a3b8;
            text-align: center;
            padding: 30px 12px;
        }

        @media (max-height: 800px) {
            .mini-agenda-card {
                min-height: 360px;
            }

            .mini-agenda-list {
                max-height: 300px;
            }
        }
    </style>
@endonce
@once
    <script>
        (function() {
            if (window.__miniAgendaInitialized) {
                return;
            }

            window.__miniAgendaInitialized = true;

            const monthNames = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            const state = {
                currentDate: new Date(),
                cache: {}
            };

            const ids = {
                monthLabel: 'miniAgendaMonthLabel',
                list: 'miniAgendaList'
            };

            function updateMonthLabel() {
                const el = document.getElementById(ids.monthLabel);
                if (!el) return;
                el.textContent = `${monthNames[state.currentDate.getMonth()]} ${state.currentDate.getFullYear()}`;
            }

            function attachMiniAgendaAccordion(container) {
                if (!container) return;
                const toggles = container.querySelectorAll('.mini-agenda-toggle');

                toggles.forEach(toggle => {
                    toggle.addEventListener('click', function() {
                        const item = this.closest('.mini-agenda-item');
                        if (!item) return;

                        const isOpen = item.classList.contains('open');
                        container.querySelectorAll('.mini-agenda-item.open').forEach(openItem => {
                            if (openItem !== item) {
                                openItem.classList.remove('open');
                                const btn = openItem.querySelector('.mini-agenda-toggle');
                                if (btn) {
                                    btn.setAttribute('aria-expanded', 'false');
                                }
                            }
                        });

                        item.classList.toggle('open', !isOpen);
                        this.setAttribute('aria-expanded', String(!isOpen));
                    });
                });
            }

            function renderAgendaList(data = []) {
                const listEl = document.getElementById(ids.list);
                if (!listEl) {
                    return;
                }

                const isDashboard = listEl.dataset.isDashboard === '1';

                // Filter: di landing hanya publik, di dashboard publik + privasi
                const filtered = data
                    .filter(item => {
                        if (item.status !== 'approved') return false;
                        if (isDashboard) {
                            // Dashboard: tampilkan semua (publik + privasi)
                            return true;
                        } else {
                            // Landing: hanya publik
                            return Number(item.is_public) === 1;
                        }
                    })
                    .sort((a, b) => new Date(a.date) - new Date(b.date));

                if (filtered.length === 0) {
                    const message = isDashboard
                        ? 'Belum ada agenda pada bulan ini.'
                        : 'Belum ada agenda publik pada bulan ini.';
                    listEl.innerHTML = `<div class="mini-agenda-placeholder">${message}</div>`;
                    return;
                }

                // Get notification route URL from data attribute or use default
                const notificationUrl = listEl.dataset.notificationUrl || '/dashboard/notification';

                listEl.innerHTML = filtered.map(item => {
                    const timeText = item.start_time && item.end_time
                        ? `${item.start_time} - ${item.end_time}`
                        : (item.start_time ?? '-');
                    const organizer = item.organizer ?? (item.unit && item.unit.unit_name) ?? '-';
                    const participants = item.involved_institution ?? '-';
                    const location = item.location ?? '-';

                    return `
                        <div class="mini-agenda-item">
                            <button class="mini-agenda-toggle" type="button" aria-expanded="false">
                                <span class="mini-agenda-title">${item.agenda_name ?? '-'}</span>
                                <span class="mini-agenda-chevron"><i class="fas fa-chevron-right"></i></span>
                            </button>
                            <div class="mini-agenda-body">
                                <div class="mini-agenda-meta">
                                    <div><i class="far fa-clock"></i>${timeText}</div>
                                    <div><i class="fas fa-map-marker-alt"></i>${location}</div>
                                    <div><i class="fas fa-user"></i>Penyelenggara: ${organizer}</div>
                                    <div><i class="fas fa-users"></i>Peserta: ${participants}</div>
                                </div>
                                <div class="mini-agenda-actions">
                                    <div class="mini-agenda-status-wrapper">
                                        <span class="mini-agenda-status-label">
                                            <i class="fas fa-eye"></i> Status
                                        </span>
                                        <span class="mini-agenda-status ${Number(item.is_public) === 1 ? 'status-public' : 'status-private'}">
                                            ${Number(item.is_public) === 1 ? 'PUBLIK' : 'PRIVASI'}
                                        </span>
                                    </div>
                                </div>
                                <div class="mini-agenda-detail-wrapper">
                                    <a href="${notificationUrl}?agenda_id=${item.id_agenda}" class="mini-agenda-detail-btn">
                                        <i class="fas fa-info-circle"></i> Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

                attachMiniAgendaAccordion(listEl);
            }

            function fetchAgendaForMonth(year, month) {
                const key = `${year}-${month}`;
                const listEl = document.getElementById(ids.list);
                if (listEl) {
                    listEl.innerHTML = '<div class="mini-agenda-placeholder">Memuat agenda...</div>';
                }

                const isDashboard = listEl ? listEl.dataset.isDashboard === '1' : false;
                const cacheKey = `${key}_${isDashboard ? 'dashboard' : 'landing'}`;

                if (state.cache[cacheKey]) {
                    renderAgendaList(state.cache[cacheKey]);
                    return;
                }

                // Gunakan endpoint berbeda untuk dashboard dan landing
                const apiUrl = isDashboard
                    ? `/api/dashboard/agenda/${year}/${month}`
                    : `/api/agenda/${year}/${month}`;

                fetch(apiUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        state.cache[cacheKey] = data ?? [];
                        renderAgendaList(state.cache[cacheKey]);
                    })
                    .catch(() => {
                        if (listEl) {
                            listEl.innerHTML = '<div class="mini-agenda-placeholder">Gagal memuat agenda.</div>';
                        }
                    });
            }

            window.changeMiniAgendaMonth = function(direction) {
                state.currentDate.setMonth(state.currentDate.getMonth() + direction);
                const targetYear = state.currentDate.getFullYear();
                const targetMonth = state.currentDate.getMonth() + 1;
                updateMonthLabel();
                fetchAgendaForMonth(targetYear, targetMonth);
            };

            document.addEventListener('DOMContentLoaded', function() {
                updateMonthLabel();
                fetchAgendaForMonth(state.currentDate.getFullYear(), state.currentDate.getMonth() + 1);
            });
        })();
    </script>
@endonce

