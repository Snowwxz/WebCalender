@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-bulan.css') }}">
@endpush

@section('content')
    <div class="calendar-page">
        <div class="calendar-main">
            <div class="calendar-header">
                <div class="month-navigation">
                    <button class="nav-btn" onclick="changeMonth(-1)">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <h2 class="month-year" id="currentMonthYear">Oktober 2025</h2>
                    <button class="nav-btn" onclick="changeMonth(1)">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            <div class="calendar-grid">
                <div class="calendar-weekdays">
                    <div>Sen</div>
                    <div>Sel</div>
                    <div>Rab</div>
                    <div>Kam</div>
                    <div>Jum</div>
                    <div>Sab</div>
                    <div class="weekend">Min</div>
                </div>
                <div class="calendar-days" id="calendarDays">
                    <!-- tanggal di-generate JS -->
                </div>
            </div>

            <div id="agendaSidebar" class="agenda-sidebar">
                <div class="sidebar-header">
                    <h3 id="agendaSidebarDate">Agenda Hari Ini</h3>
                    <button class="close-sidebar"
                        onclick="document.getElementById('agendaSidebar').classList.remove('active')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="agendaList" class="agenda-list"></div>
            </div>
        </div>
    </div>

    <script>
        let agenda = {!! json_encode($agenda ?? [], JSON_UNESCAPED_UNICODE) !!};

        let urlParams = new URLSearchParams(window.location.search);
        let currentYear = parseInt(urlParams.get('tahun')) || new Date().getFullYear();
        let currentMonth = parseInt(urlParams.get('bulan')) || new Date().getMonth() + 1;
        let currentDate = new Date(currentYear, currentMonth - 1, 1);

        const monthNames = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        function filterAgenda(list, {
            year,
            month,
            day
        }) {
            return list.filter(item => {
                const date = new Date(item.date);
                return (!year || date.getFullYear() === year) &&
                    (!month || date.getMonth() + 1 === month) &&
                    (!day || date.getDate() === day);
            });
        }

        function generateMainCalendar() {
            const monthYearEl = document.getElementById('currentMonthYear');
            const calendarDays = document.getElementById('calendarDays');

            monthYearEl.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
            calendarDays.innerHTML = '';

            const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - (firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1));

            for (let i = 0; i < 35; i++) {
                const date = new Date(startDate);
                date.setDate(startDate.getDate() + i);

                const dayEl = document.createElement('div');
                dayEl.className = 'calendar-day';
                if (date.getMonth() !== currentDate.getMonth()) dayEl.classList.add('other-month');
                if (date.getDay() === 0) dayEl.classList.add('weekend');
                else if (date.getDay() === 6) dayEl.classList.add('saturday');

                const dayNum = document.createElement('div');
                dayNum.className = 'day-number';
                dayNum.textContent = date.getDate();
                dayEl.appendChild(dayNum);

                const filtered = filterAgenda(agenda, {
                    year: date.getFullYear(),
                    month: date.getMonth() + 1,
                    day: date.getDate()
                });

                if (filtered.length > 0) {
                    const badge = document.createElement('div');
                    badge.className = 'agenda-count-badge bg-green-500';
                    badge.textContent = filtered.length > 1 ? `${filtered.length} Kegiatan` : filtered[0].agenda_name;

                    badge.addEventListener('click', e => {
                        e.stopPropagation();
                        showAgendaListSidebar(filtered, date);
                    });

                    dayEl.appendChild(badge);
                }

                dayEl.addEventListener('click', () => {
                    const y = date.getFullYear();
                    const m = String(date.getMonth() + 1).padStart(2, '0');
                    const d = String(date.getDate()).padStart(2, '0');
                    window.location.href = `/hari?tanggal=${y}-${m}-${d}`;
                });

                calendarDays.appendChild(dayEl);
            }
        }

        function showAgendaListSidebar(list, date) {
            const sidebar = document.getElementById("agendaSidebar");
            const listContainer = document.getElementById("agendaList");
            const title = document.getElementById("agendaSidebarDate");

            const dateStr = date.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            title.textContent = `Agenda ${dateStr}`;
            listContainer.innerHTML = "";

            if (list.length === 0) {
                listContainer.innerHTML = "<p>Tidak ada agenda untuk hari ini.</p>";
                return;
            }

            list.forEach(item => {
                const div = document.createElement('div');
                div.className = 'agenda-item';
                div.textContent = item.agenda_name; // ← ONLY TITLE NOW
                div.addEventListener('click', () => showAgendaModal(item)); // ← OPEN MODAL WHEN CLICKED
                listContainer.appendChild(div);
            });

            sidebar.classList.add('active');
        }


        function changeMonth(direction) {
            currentDate.setMonth(currentDate.getMonth() + direction);
            const newMonth = currentDate.getMonth() + 1;
            const newYear = currentDate.getFullYear();
            const newUrl = `/bulan?bulan=${newMonth}&tahun=${newYear}`;
            window.history.pushState({}, '', newUrl);
            generateMainCalendar();
        }

        document.addEventListener('DOMContentLoaded', generateMainCalendar);

        function formatDate(dateString) {
            if (!dateString) return "-";
            const date = new Date(dateString);
            if (isNaN(date)) return dateString; // fallback kalau backend ngirim string aneh
            return date.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        }

        function showAgendaModal(item) {
            // Benerin penulisan instansi yang diundang
            let involved = item.involved_institution;
            if (!involved || involved.trim() === "" || involved === "-") {
                involved = "Tidak ada instansi yang diundang";
            }

            document.getElementById('agendaModalTitle').textContent = item.agenda_name;

            document.getElementById('agendaModalContent').innerHTML = `
        <p><i class="fas fa-calendar"></i> ${formatDate(item.date)}</p>
        <p><i class="fas fa-clock"></i> ${item.start_time || '-'} - ${item.end_time || '-'}</p>
        <p><i class="fas fa-map-marker-alt"></i> ${item.location || '-'}</p>
        <p><i class="fas fa-user-tie"></i> Penanggung jawab: ${item.person_in_charge || '-'}</p>
        <p><i class="fas fa-building"></i> Instansi pengaju: ${item.unit?.unit_name || '-'}</p>
        <p><i class="fas fa-users"></i> Instansi diundang: ${involved}</p>
        <p style="margin-top:8px;">
            <i class="fas fa-align-left"></i> <strong>Deskripsi:</strong><br>
            ${item.description && item.description.trim() !== '' ? item.description : '-'}
        </p>
    `;

            document.getElementById('agendaModal').style.display = 'flex';
        }

        function closeAgendaModal() {
            document.getElementById('agendaModal').style.display = 'none';
        }
    </script>

    <div id="agendaModal" class="agenda-modal-overlay" style="display: none;">
        <div class="agenda-modal">
            <div class="modal-header">
                <h3 id="agendaModalTitle"></h3>
                <button class="close-modal" onclick="closeAgendaModal()">&times;</button>
            </div>
            <div class="modal-content" id="agendaModalContent"></div>
        </div>
    </div>
@endsection
