@extends('layouts.main')

@section('content')
<div class="year-view-page">
    <div class="year-view-main">
        <!-- Year Header -->
        <div class="year-header">
            <div class="year-navigation">
                <button class="nav-btn" onclick="changeYear(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h2 class="current-year" id="currentYear">2025</h2>
                <button class="nav-btn" onclick="changeYear(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <!-- Year Grid -->
        <div class="year-grid" id="yearGrid">
            <!-- Semua bulan akan di-generate otomatis lewat JavaScript -->
        </div>
    </div>
</div>

<script>
    let currentYear = new Date().getFullYear();

    const monthNames = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    // Generate seluruh tampilan bulan di halaman tahun
    function generateYearGrid() {
        const grid = document.getElementById('yearGrid');
        grid.innerHTML = '';

        for (let i = 0; i < 12; i++) {
            const card = document.createElement('div');
            card.className = 'month-card';
            card.innerHTML = `
                <div class="month-header">${monthNames[i]}</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div><div>M</div>
                    </div>
                    <div class="month-days" id="monthDays-${i}"></div>
                </div>
            `;

            // Klik kartu bulan → buka tampilan bulan itu
            card.addEventListener('click', () => {
                window.location.href = "{{ url('bulan') }}?bulan=" + i + "&tahun=" + currentYear;
            });

            grid.appendChild(card);
        }

        // Setelah buat semua, generate tanggal di tiap bulan
        generateYearCalendar();
    }

    // Generate hari-hari dalam 1 bulan
    function generateMonthDays(monthIndex, year) {
        const monthDaysContainer = document.getElementById(`monthDays-${monthIndex}`);
        const firstDay = new Date(year, monthIndex, 1);
        const lastDay = new Date(year, monthIndex + 1, 0);
        const startDate = new Date(firstDay);
        const dayOffset = firstDay.getDay() === 0 ? -6 : -(firstDay.getDay() - 1);
        startDate.setDate(startDate.getDate() + dayOffset);

        monthDaysContainer.innerHTML = '';

        for (let i = 0; i < 42; i++) {
            const date = new Date(startDate);
            date.setDate(startDate.getDate() + i);

            const dayElement = document.createElement('div');
            dayElement.className = 'month-day';
            dayElement.textContent = date.getDate();

            if (date.getMonth() !== monthIndex) dayElement.classList.add('other-month');
            if (date.getDay() === 0 || date.getDay() === 6) dayElement.classList.add('weekend');
            if (date.toDateString() === new Date().toDateString() && date.getMonth() === monthIndex)
                dayElement.classList.add('today');

            monthDaysContainer.appendChild(dayElement);
        }
    }

    // Generate semua bulan di tahun berjalan
    function generateYearCalendar() {
        for (let month = 0; month < 12; month++) {
            generateMonthDays(month, currentYear);
        }
    }

    // Ubah tahun
    function changeYear(direction) {
        currentYear += direction;
        document.getElementById('currentYear').textContent = currentYear;
        generateYearCalendar();
    }

    // Jalankan saat halaman dimuat
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('currentYear').textContent = currentYear;
        generateYearGrid();
    });
</script>
@endsection
