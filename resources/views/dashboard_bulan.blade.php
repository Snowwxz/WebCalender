@extends('layouts.main')

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
    // ✅ Ambil dari URL (?bulan=3&tahun=2025) biar nggak selalu Oktober
    const urlParams = new URLSearchParams(window.location.search);
    let selectedMonth = parseInt(urlParams.get('bulan')) || new Date().getMonth() + 1;
    let selectedYear = parseInt(urlParams.get('tahun')) || new Date().getFullYear();

    let currentDate = new Date(selectedYear, selectedMonth - 1, 1);

    // Generate main calendar
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
        startDate.setDate(startDate.getDate() - (firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1)); // Mulai dari Senin

        calendarDays.innerHTML = '';

        for (let i = 0; i < 35; i++) { // 5 minggu x 7 hari
            const date = new Date(startDate);
            date.setDate(startDate.getDate() + i);

            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day';

            if (date.getMonth() !== currentDate.getMonth()) {
                dayElement.classList.add('other-month');
            }

            if (date.getDay() === 0 || date.getDay() === 6) {
                dayElement.classList.add('weekend');
            }

            if (date.toDateString() === new Date().toDateString()) {
                dayElement.classList.add('today');
            }

            const dayNumber = document.createElement('div');
            dayNumber.className = 'day-number';
            dayNumber.textContent = date.getDate();

            if (date.getDay() === 0 || date.getDay() === 6) {
                dayNumber.classList.add('weekend');
            }

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

    // Change month
    function changeMonth(direction) {
        currentDate.setMonth(currentDate.getMonth() + direction);
        generateMainCalendar();
    }

    document.addEventListener('DOMContentLoaded', generateMainCalendar);
</script>
@endsection
