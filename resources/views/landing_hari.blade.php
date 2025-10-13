@extends('layouts.main')

@section('content')
<div class="day-view-page">
    <div class="day-view-main">
        <!-- Day Header -->
        <div class="day-header">
            <div class="day-navigation">
                <button class="nav-btn" onclick="changeDay(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h2 class="current-day" id="currentDay">Hari ini</h2>
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

    // Kalau ada di URL, pakai itu, kalau tidak ada, pakai hari ini
    let currentDate = tanggalParam ? new Date(tanggalParam) : new Date();

    // Data agenda (sementara kosong)
    const dayEvents = {};

    // Fungsi untuk update teks hari
    function updateDayDisplay() {
        const dayElement = document.getElementById('currentDay');
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        const tanggalTeks = currentDate.toLocaleDateString('id-ID', options);
        dayElement.textContent = tanggalTeks.charAt(0).toUpperCase() + tanggalTeks.slice(1);
    }

    // Fungsi untuk mengganti hari (panah kiri/kanan)
    function changeDay(direction) {
        currentDate.setDate(currentDate.getDate() + direction);
        updateDayDisplay();
        renderDayEvents();

        // Update URL tanpa reload, supaya bisa dibookmark atau di-refresh
        const newDateStr = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(currentDate.getDate()).padStart(2, '0')}`;
        const newUrl = `/hari?tanggal=${newDateStr}`;
        window.history.pushState({}, '', newUrl);
    }

    // Render agenda (sementara kosong)
    function renderDayEvents() {
        const dayColumn = document.querySelector('.day-column');
        const dateString = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(currentDate.getDate()).padStart(2, '0')}`;

        // Hapus event sebelumnya
        dayColumn.querySelectorAll('.event-item').forEach(item => item.remove());

        // Nanti bisa isi event berdasarkan API getByDate(dateString)
    }

    // Inisialisasi tampilan saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        updateDayDisplay();
        renderDayEvents();
    });
</script>
@endsection
