@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-bulan.css') }}">
@endpush

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        </div>
    </div>

    <script>
        const selectedMonth = {{ $month ?? date('n') }};
        const selectedYear = {{ $year ?? date('Y') }};
        let currentDate = new Date(selectedYear, selectedMonth - 1, 1);
        let agendaData = [];

        // === Ambil agenda publik dari backend ===
        async function fetchAgenda(year, month) {
            try {
                const response = await fetch(`/api/agenda/${year}/${month}`);
                if (!response.ok) throw new Error('Gagal memuat agenda.');
                const data = await response.json();
                agendaData = data;
            } catch (error) {
                console.error('Fetch agenda gagal:', error);
                agendaData = []; // pastikan tidak undefined
            } finally {
                // 🔥 Selalu render kalender, apapun hasil fetch-nya!
                generateMainCalendar();
            }
        }

        // === Filter agenda untuk tanggal tertentu ===
        function getAgendaForDate(dateObj) {
            const year = dateObj.getFullYear();
            const month = String(dateObj.getMonth() + 1).padStart(2, '0');
            const day = String(dateObj.getDate()).padStart(2, '0');
            const dateStr = `${year}-${month}-${day}`;
            return agendaData.filter(a => a.tanggal.startsWith(dateStr) && a.status === 'approved' && a.visibility ===
                'public');
        }

        // === Generate Kalender ===
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

            // loop 35 hari seperti aslinya
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
                if (date.toDateString() === new Date().toDateString()) dayElement.classList.add('today');

                const dayNumber = document.createElement('div');
                dayNumber.className = 'day-number';
                dayNumber.textContent = date.getDate();
                dayElement.appendChild(dayNumber);

                // badge agenda publik disetujui
                // === BADGE GENERATOR ===
                const agendaToday = getAgendaForDate(date);
                if (agendaToday.length > 0) {
                    const agendaContainer = document.createElement("div");
                    agendaContainer.className = "agenda-container";

                    const badge = document.createElement("div");
                    badge.className = "agenda-count-badge bg-green-500";
                    badge.textContent = agendaToday.length > 1 ?
                        `${agendaToday.length} Kegiatan` :
                        agendaToday[0].judul;

                    badge.title = `${agendaToday.length} agenda publik hari ini`;
                    badge.addEventListener("click", (e) => {
                        e.stopPropagation();
                        showAgendaListModal(agendaToday);
                    });

                    agendaContainer.appendChild(badge);
                    dayElement.appendChild(agendaContainer);
                }


                dayElement.addEventListener('click', () => {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    window.location.href = `/hari?tanggal=${year}-${month}-${day}`;
                });

                calendarDays.appendChild(dayElement);
            }
        }

        // === Ganti Bulan ===
        function changeMonth(direction) {
            currentDate.setMonth(currentDate.getMonth() + direction);
            const newMonth = currentDate.getMonth() + 1;
            const newYear = currentDate.getFullYear();
            fetchAgenda(newYear, newMonth);
        }

        // === Inisialisasi ===
        document.addEventListener('DOMContentLoaded', function() {
            // 🔥 tampilkan tanggal dulu biar gak kosong,
            // lalu fetch agenda dan update badge
            generateMainCalendar();
            fetchAgenda(selectedYear, selectedMonth);
        });

        // === MODAL DETAIL AGENDA (SweetAlert2) ===
        function showAgendaListModal(agendaList) {
            let html = "<ul style='text-align:left'>";
            agendaList.forEach(a => {
                html += `<li><strong>${a.judul}</strong> - ${a.lokasi ?? '-'}</li>`;
            });
            html += "</ul>";

            Swal.fire({
                title: "Agenda Publik Hari Ini",
                html,
                icon: "info",
                confirmButtonText: "Tutup",
                width: "30rem"
            });
        }
    </script>
@endsection
