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

        <div class="year-grid">
            <div class="month-card">
                <div class="month-header">Januari</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>M</div><div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div>
                    </div>
                    <div class="month-days">
                    </div>
                </div>
            </div>

            <div class="month-card">
                <div class="month-header">Februari</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>M</div><div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div>
                    </div>
                    <div class="month-days">
                    </div>
                </div>
            </div>

            <div class="month-card">
                <div class="month-header">Maret</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>M</div><div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div>
                    </div>
                    <div class="month-days">
                    </div>
                </div>
            </div>

            <div class="month-card">
                <div class="month-header">April</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>M</div><div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div>
                    </div>
                    <div class="month-days">
                    </div>
                </div>
            </div>

            <div class="month-card">
                <div class="month-header">Mei</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>M</div><div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div>
                    </div>
                    <div class="month-days">
                    </div>
                </div>
            </div>

            <div class="month-card">
                <div class="month-header">Juni</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>M</div><div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div>
                    </div>
                    <div class="month-days">
                    </div>
                </div>
            </div>

            <div class="month-card">
                <div class="month-header">Juli</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>M</div><div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div>
                    </div>
                    <div class="month-days">
                    </div>
                </div>
            </div>

            <div class="month-card">
                <div class="month-header">Agustus</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>M</div><div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div>
                    </div>
                    <div class="month-days">
                    </div>
                </div>
            </div>

            <div class="month-card">
                <div class="month-header">September</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>M</div><div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div>
                    </div>
                    <div class="month-days">
                    </div>
                </div>
            </div>

            <div class="month-card">
                <div class="month-header">Oktober</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>M</div><div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div>
                    </div>
                    <div class="month-days">
                    </div>
                </div>
            </div>

            <div class="month-card">
                <div class="month-header">November</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>M</div><div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div>
                    </div>
                    <div class="month-days">
                    </div>
                </div>
            </div>

            <div class="month-card">
                <div class="month-header">Desember</div>
                <div class="month-grid">
                    <div class="month-weekdays">
                        <div>M</div><div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div>
                    </div>
                    <div class="month-days">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentYear = 2025;

const yearEvents = {};

function generateMonthDays(monthIndex, year) {
    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const monthCards = document.querySelectorAll('.month-card');
    const monthCard = monthCards[monthIndex];
    const monthDaysContainer = monthCard.querySelector('.month-days');

    const firstDay = new Date(year, monthIndex, 1);
    const lastDay = new Date(year, monthIndex + 1, 0);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay());

    monthDaysContainer.innerHTML = '';

    for (let i = 0; i < 42; i++) {
        const date = new Date(startDate);
        date.setDate(startDate.getDate() + i);

        const dayElement = document.createElement('div');
        dayElement.className = 'month-day';
        dayElement.textContent = date.getDate();

        if (date.getMonth() !== monthIndex) {
            dayElement.classList.add('other-month');
        }

        if (date.getDay() === 0 || date.getDay() === 6) {
            dayElement.classList.add('weekend');
        }

        if (
            date.toDateString() === new Date().toDateString() &&
            date.getMonth() === monthIndex
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
