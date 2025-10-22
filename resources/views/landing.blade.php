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
                    <div class="weekend">Sab</div>
                    <div class="weekend">Min</div>
                </div>
                <div class="calendar-days" id="calendarDays">
                    <!-- Tanggal akan di-generate lewat JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Ambil bulan dari controller, kalau tidak ada pakai bulan saat ini
        const selectedMonth = {{ $month ?? date('n') }};
        const selectedYear = {{ $year ?? date('Y') }};

        // Set tanggal awal ke tanggal 1 bulan yang dipilih
        let currentDate = new Date(selectedYear, selectedMonth - 1, 1);

        // === FUNGSI UTAMA: Generate Kalender ===
        function generateMainCalendar() {
            const monthYear = document.getElementById('currentMonthYear');
            const calendarDays = document.getElementById('calendarDays');

            // Nama-nama bulan
            const monthNames = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            // Ganti teks bulan & tahun di header
            monthYear.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;

            // Tentukan hari pertama dan mulai dari Senin
            const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - (firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1));

            // Kosongkan isi sebelumnya
            calendarDays.innerHTML = '';

            // Loop untuk 5 minggu (35 hari)
            for (let i = 0; i < 35; i++) {
                const date = new Date(startDate);
                date.setDate(startDate.getDate() + i);

        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day';

                // Tandai kalau tanggal bukan dari bulan yang sedang ditampilkan
                if (date.getMonth() !== currentDate.getMonth()) {
                    dayElement.classList.add('other-month');
                }

                // Tandai Sabtu & Minggu
                if (date.getDay() === 0 || date.getDay() === 6) {
                    dayElement.classList.add('weekend');
                }

                // Tandai tanggal hari ini
                if (date.toDateString() === new Date().toDateString()) {
                    dayElement.classList.add('today');
                }

                // Tampilkan angka tanggal
                const dayNumber = document.createElement('div');
                dayNumber.className = 'day-number';
                dayNumber.textContent = date.getDate();

                // Klik tanggal → buka halaman /hari?tanggal=yyyy-mm-dd
                dayElement.addEventListener('click', () => {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    window.location.href = `/hari?tanggal=${year}-${month}-${day}`;
                });

                dayElement.appendChild(dayNumber);
                calendarDays.appendChild(dayElement);
            }
        }

        // === FUNGSI GANTI BULAN ===
        function changeMonth(direction) {
            currentDate.setMonth(currentDate.getMonth() + direction);

            const newMonth = currentDate.getMonth() + 1;
            const newYear = currentDate.getFullYear();
            const newUrl = `/bulan?bulan=${newMonth - 1}&tahun=${newYear}`;
            window.history.pushState({}, '', newUrl);

            generateMainCalendar();
        }

        // === INISIALISASI ===
        document.addEventListener('DOMContentLoaded', function() {
            generateMainCalendar();
        });
    </script>
@endsection
