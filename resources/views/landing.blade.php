@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-bulan.css') }}">
@endpush

@section('content')
    @include('show_agenda_modal_landing')
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
                    <!-- Tanggal akan di-generate lewat JavaScript -->
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

        const urlParams = new URLSearchParams(window.location.search);
        const bulanParam = urlParams.get('bulan'); // 0-11 (from year view)
        const monthParam = urlParams.get('month'); // 1-12 (direct)

        // Handle 0 correctly for Januari when using `bulan=0`.
        let selectedMonth = bulanParam !== null
            ? (parseInt(bulanParam, 10) + 1)
            : (monthParam !== null ? parseInt(monthParam, 10) : (new Date().getMonth() + 1));

        let selectedYear = (urlParams.get('tahun') ?? urlParams.get('year'))
            ? parseInt(urlParams.get('tahun') ?? urlParams.get('year'), 10)
            : new Date().getFullYear();

        let currentDate = new Date(selectedYear, selectedMonth - 1, 1);
        let agendaData = [];

        async function fetchAgenda(year, month) {
            try {
                const response = await fetch(`/api/agenda/${year}/${month}`);
                if (!response.ok) throw new Error('Gagal memuat agenda.');
                const data = await response.json();
                agendaData = data;
            } catch (error) {
                console.error('Fetch agenda gagal:', error);
                agendaData = [];
            } finally {
                generateMainCalendar();
            }
        }

        function getAgendaForDate(dateObj) {
            const year = dateObj.getFullYear();
            const month = String(dateObj.getMonth() + 1).padStart(2, '0');
            const day = String(dateObj.getDate()).padStart(2, '0');
            const dateStr = `${year}-${month}-${day}`;
            return agendaData.filter(a => {
                const agendaDate = new Date(a.date);
                const agendaYear = agendaDate.getFullYear();
                const agendaMonth = String(agendaDate.getMonth() + 1).padStart(2, '0');
                const agendaDay = String(agendaDate.getDate()).padStart(2, '0');
                const agendaDateStr = `${agendaYear}-${agendaMonth}-${agendaDay}`;
                return agendaDateStr === dateStr && a.status === 'approved' && a.is_public == 1;
            });
        }

        function generateMainCalendar() {
            const monthYear = document.getElementById('currentMonthYear');
            const calendarDays = document.getElementById('calendarDays');

            const monthNames = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            monthYear.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;

            const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - (firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1));

            calendarDays.innerHTML = '';

            for (let i = 0; i < 35; i++) {
                const date = new Date(startDate);
                date.setDate(startDate.getDate() + i);

                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day';

                if (date.getMonth() !== currentDate.getMonth()) {
                    dayElement.classList.add('other-month');
                }

                if (date.getDay() === 0) dayElement.classList.add('weekend');
                else if (date.getDay() === 6) dayElement.classList.add('saturday');

                if (date.toDateString() === new Date().toDateString()) {
                    dayElement.classList.add('today');
                }

                const dayNumber = document.createElement('div');
                dayNumber.className = 'day-number';
                dayNumber.textContent = date.getDate();

                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');

                const filteredAgenda = getAgendaForDate(date);
                dayElement.appendChild(dayNumber);

                if (filteredAgenda.length > 0) {
                    const agendaContainer = document.createElement("div");
                    agendaContainer.className = "agenda-container";

                    const badge = document.createElement("div");
                    badge.className = "agenda-count-badge bg-green-500";
                    badge.textContent = filteredAgenda.length > 1 ?
                        `${filteredAgenda.length} Kegiatan` :
                        filteredAgenda[0].agenda_name;

                    badge.addEventListener('click', (e) => {
                        e.stopPropagation();
                        showAgendaListSidebar(filteredAgenda, `${year}-${month}-${day}`);
                    });

                    agendaContainer.appendChild(badge);
                    dayElement.appendChild(agendaContainer);
                }

                dayNumber.addEventListener('click', (e) => {
                    e.stopPropagation();
                    window.location.href = `/hari?tanggal=${year}-${month}-${day}`;
                });

                calendarDays.appendChild(dayElement);
            }
        }

        function changeMonth(direction) {
            currentDate.setMonth(currentDate.getMonth() + direction);
            const newMonth = currentDate.getMonth() + 1;
            const newYear = currentDate.getFullYear();
            fetchAgenda(newYear, newMonth);

            // Update mini calendar in sidebar
            if (window.updateMiniCalendar) {
                window.updateMiniCalendar(currentDate);
            }
        }

        function formatDate(dateString) {
            if (!dateString) return "-";
            const date = new Date(dateString);
            const options = {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            };
            return date.toLocaleDateString('id-ID', options);
        }

        function showAgendaListSidebar(agendaList, date) {
            const sidebar = document.getElementById("agendaSidebar");
            const listContainer = document.getElementById("agendaList");
            const title = document.getElementById("agendaSidebarDate");

            title.textContent = `Agenda ${date}`;
            listContainer.innerHTML = "";

            if (agendaList.length === 0) {
                listContainer.innerHTML = "<p>Tidak ada agenda untuk hari ini.</p>";
                return;
            }

            agendaList.forEach((item, index) => {
                const itemDiv = document.createElement("div");
                itemDiv.className = "agenda-item";

                const header = document.createElement("div");
                header.className = "agenda-header";
                header.innerHTML = `
                    <span class="agenda-item-title">${item.agenda_name}</span>
                    <span class="agenda-item-arrow"><i class="fas fa-chevron-right"></i></span>
                `;

                itemDiv.appendChild(header);
                itemDiv.addEventListener("click", () => {
                    openShowAgendaModal(item);
                });

                listContainer.appendChild(itemDiv);
            });

            sidebar.classList.add("active");
        }

        function openShowAgendaModal(data) {
            document.getElementById('showAgendaName').innerText = data.agenda_name ?? '-';
            document.getElementById('showAgendaDate').innerText = formatDate(data.date);

            const timeText = (data.start_time && data.end_time) ?
                `${data.start_time} - ${data.end_time}` :
                (data.start_time ?? '-');
            document.getElementById('showAgendaTime').innerText = timeText;

            document.getElementById('showAgendaLocation').innerText = data.location ?? '-';
            document.getElementById('showAgendaPIC').innerText = data.person_in_charge ?? '-';
            document.getElementById('showAgendaDesc').innerText = data.description ?? '-';

            const involved = data.involved_institution ?? '-';
            const unitName = data.unit && data.unit.unit_name ? data.unit.unit_name : '-';

            const unitEl = document.getElementById('showAgendaUnit');
            if (unitEl) unitEl.innerText = unitName;

            const involvedEl = document.getElementById('showAgendaInvolved');
            if (involvedEl) involvedEl.innerText = involved;

            new bootstrap.Modal(document.getElementById('showAgendaModal')).show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            generateMainCalendar();
            fetchAgenda(selectedYear, selectedMonth);

            const showAgendaModalEl = document.getElementById('showAgendaModal');
            if (showAgendaModalEl) {
                showAgendaModalEl.addEventListener('show.bs.modal', function() {
                    const sidebar = document.getElementById('agendaSidebar');
                    if (sidebar) sidebar.classList.add('hidden');
                });

                showAgendaModalEl.addEventListener('hidden.bs.modal', function() {
                    const sidebar = document.getElementById('agendaSidebar');
                    if (sidebar) sidebar.classList.remove('hidden');
                });
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
    const message = localStorage.getItem('logoutSuccess');
    if (message) {
        showSuccessToast(message);
        localStorage.removeItem('logoutSuccess'); // hapus supaya nggak muncul lagi nanti
    }
});
    </script>
@endsection