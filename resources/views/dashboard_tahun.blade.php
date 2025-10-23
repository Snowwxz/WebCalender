@extends('layouts.main')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-tahun.css') }}">
@endpush

@section('content')
<div class="year-view-page">
    <div class="year-view-main">
        <!-- Year Header -->
        <div class="calendar-header">
            <div class="month-navigation">
                <button class="nav-btn" onclick="changeYear(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h2 class="month-year" id="currentYear">2025</h2>
                <button class="nav-btn" onclick="changeYear(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <!-- Year Grid -->
        <div class="year-grid">
            <div class="month-card" onclick="goToMonth(1)">
                <div class="month-header">Januari</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div><div class="weekend">M</div>
                    </div>
                    <div class="month-days">
                    </div>
                </div>
            </div>

            <div class="month-card" onclick="goToMonth(2)">
                <div class="month-header">Februari</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div><div class="weekend">M</div>
                    </div>
                    <div class="month-days">
                    </div>
                </div>
            </div>

            <div class="month-card" onclick="goToMonth(3)">
                <div class="month-header">Maret</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div><div class="weekend">M</div>
                    </div>
                    <div class="month-days">
                    </div>
                </div>
            </div>

            <div class="month-card" onclick="goToMonth(4)">
                <div class="month-header">April</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div><div class="weekend">M</div>
                    </div>
                    <div class="month-days">
                    </div>
                </div>
            </div>

            <div class="month-card" onclick="goToMonth(5)">
                <div class="month-header">Mei</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div><div class="weekend">M</div>
                    </div>
                    <div class="month-days">
                    </div>
                </div>
            </div>

            <div class="month-card" onclick="goToMonth(6)">
                <div class="month-header">Juni</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div><div class="weekend">M</div>
                    </div>
                    <div class="month-days">
                    </div>
                </div>
            </div>

            <div class="month-card" onclick="goToMonth(7)">
                <div class="month-header">Juli</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div><div class="weekend">M</div>
                    </div>
                    <div class="month-days">
                    </div>
                </div>
            </div>

            <div class="month-card" onclick="goToMonth(8)">
                <div class="month-header">Agustus</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div><div class="weekend">M</div>
                    </div>
                    <div class="month-days">
                    </div>
                </div>
            </div>

            <div class="month-card" onclick="goToMonth(9)">
                <div class="month-header">September</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div><div class="weekend">M</div>
                    </div>
                    <div class="month-days">
                    </div>
                </div>
            </div>

            <div class="month-card" onclick="goToMonth(10)">
                <div class="month-header">Oktober</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div><div class="weekend">M</div>
                    </div>
                    <div class="month-days">
                    </div>
                </div>
            </div>

            <div class="month-card" onclick="goToMonth(11)">
                <div class="month-header">November</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div><div class="weekend">M</div>
                    </div>
                    <div class="month-days">
                    </div>
                </div>
            </div>

            <div class="month-card" onclick="goToMonth(12)">
                <div class="month-header">Desember</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div><div class="weekend">M</div>
                    </div>
                    <div class="month-days">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentYear = new Date().getFullYear();
const today = new Date(); // simpan tanggal hari ini sekali aja

function generateMonthDays(monthIndex, year) {
    const monthCards = document.querySelectorAll('.month-card');
    const monthCard = monthCards[monthIndex];
    if (!monthCard) return;
    const monthDaysContainer = monthCard.querySelector('.month-days');

    const firstDay = new Date(year, monthIndex, 1);
    const lastDay = new Date(year, monthIndex + 1, 0);

    // mulai dari Senin
    const dayOffset = firstDay.getDay() === 0 ? -6 : -(firstDay.getDay() - 1);
    const startDate = new Date(firstDay);
    startDate.setDate(firstDay.getDate() + dayOffset);

    monthDaysContainer.innerHTML = '';

    // Hitung berapa hari yang perlu ditampilkan
    const daysInMonth = new Date(year, monthIndex + 1, 0).getDate();
    const firstDayOfMonth = new Date(year, monthIndex, 1).getDay();
    const startDay = firstDayOfMonth === 0 ? 6 : firstDayOfMonth - 1; // Konversi ke Senin = 0
    const totalDays = startDay + daysInMonth;
    const weeksNeeded = Math.ceil(totalDays / 7);
    const daysToShow = weeksNeeded * 7;

    for (let i = 0; i < daysToShow; i++) {
        const date = new Date(startDate);
        date.setDate(startDate.getDate() + i);

        const dayElement = document.createElement('div');
        dayElement.className = 'month-day';
        dayElement.textContent = date.getDate();

        if (date.getMonth() !== monthIndex) {
            dayElement.classList.add('other-month');
        }

        // Tandai weekend (hanya Minggu) dan Sabtu
        if (date.getDay() === 0) {
            dayElement.classList.add('weekend');
        } else if (date.getDay() === 6) {
            dayElement.classList.add('saturday');
        }

        // Highlight hari ini
        if (
            date.getDate() === today.getDate() &&
            date.getMonth() === today.getMonth() &&
            date.getFullYear() === today.getFullYear()
        ) {
            dayElement.classList.add('today');
        }

        monthDaysContainer.appendChild(dayElement);
    }
}

function generateYearCalendar() {
    for (let month = 0; month < 12; month++) {
        generateMonthDays(month, currentYear);
    }
}

function updateYearDisplay() {
    document.getElementById('currentYear').textContent = currentYear;
}

function goToMonth(month) {
    // tambahkan +1 karena bulan JS dimulai dari 0
    window.location.href = `/dashboard/bulan?bulan=${month}&tahun=${currentYear}`;
}

function changeYear(direction) {
    currentYear += direction;
    updateYearDisplay();
    generateYearCalendar();
}

document.addEventListener('DOMContentLoaded', function() {
    updateYearDisplay();
    generateYearCalendar();
});
</script>
@endsection
