@extends('layouts.main')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-hari.css') }}">
@endpush

@section('content')
<div class="day-view-page">
    <div class="day-view-main">
        <!-- Day Header -->
        <div class="calendar-header">
            <div class="month-navigation">
                <button class="nav-btn" onclick="changeDay(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h2 class="month-year" id="currentDay">Hari ini</h2>
                <button class="nav-btn" onclick="changeDay(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <!-- Time Grid -->
        <div class="day-grid">
            <div class="time-column">
                @for ($i = 0; $i < 24; $i++)
                    <div class="time-slot">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}:00</div>
                @endfor
            </div>

            <div class="day-column">
                @for ($i = 0; $i < 24; $i++)
                    <div class="hour-slot" data-hour="{{ $i }}"></div>
                @endfor
            </div>
        </div>
    </div>
</div>

<script>
    // Ambil parameter tanggal dari URL, misalnya ?tanggal=2025-03-10
    const params = new URLSearchParams(window.location.search);
    const tanggalParam = params.get('tanggal');

// Parse tanggal tanpa terkena timezone shift
function parseLocalDate(dateStr) {
    if (!dateStr) return new Date(); // kalau tidak ada parameter, pakai hari ini
    const [year, month, day] = dateStr.split('-').map(Number);
    return new Date(year, month - 1, day); // bulan dikurangi 1 karena index 0-11
}

let currentDate = parseLocalDate(tanggalParam);

const dayEvents = {};

// Update tampilan hari
function updateDayDisplay() {
    const dayElement = document.getElementById('currentDay');
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    const formattedDate = currentDate.toLocaleDateString('id-ID', options);
    // Kapital huruf pertama
    dayElement.textContent = formattedDate.charAt(0).toUpperCase() + formattedDate.slice(1);
}

// Ganti hari (← →)
function changeDay(direction) {
    currentDate.setDate(currentDate.getDate() + direction);
    updateDayDisplay();
    renderDayEvents();

    // Update URL tanpa reload
    const year = currentDate.getFullYear();
    const month = String(currentDate.getMonth() + 1).padStart(2, '0');
    const day = String(currentDate.getDate()).padStart(2, '0');
    const newUrl = `${window.location.pathname}?tanggal=${year}-${month}-${day}`;
    window.history.pushState({}, '', newUrl);
}

// Render agenda (sementara kosong)
function renderDayEvents() {
    const dayColumn = document.querySelector('.day-column');
    const dateString = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(currentDate.getDate()).padStart(2, '0')}`;

    // Bersihkan event sebelumnya
    dayColumn.querySelectorAll('.event-item').forEach(item => item.remove());
}

// Inisialisasi
document.addEventListener('DOMContentLoaded', function() {
    updateDayDisplay();
    renderDayEvents();
});
</script>
@endsection
