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
        document.addEventListener('DOMContentLoaded', function() {
            const params = new URLSearchParams(window.location.search);
            const tanggalParam = params.get('tanggal');

            // Parse tanggal tanpa timezone shift
            function parseLocalDate(dateStr) {
                if (!dateStr) return new Date();
                const [y, m, d] = dateStr.split('-').map(Number);
                return new Date(y, m - 1, d);
            }

            // 🧠 Gunakan param kalau ada, kalau tidak gunakan tanggal hari ini
            let currentDate = parseLocalDate(tanggalParam);

            function updateDayDisplay() {
                const dayElement = document.getElementById('currentDay');
                const formatted = currentDate.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                if (dayElement)
                    dayElement.textContent = formatted.charAt(0).toUpperCase() + formatted.slice(1);
            }

            function changeDay(direction) {
                currentDate.setDate(currentDate.getDate() + direction);
                updateDayDisplay();
                renderDayEvents();

                const y = currentDate.getFullYear();
                const m = String(currentDate.getMonth() + 1).padStart(2, '0');
                const d = String(currentDate.getDate()).padStart(2, '0');
                const newUrl = `${window.location.pathname}?tanggal=${y}-${m}-${d}`;
                window.history.pushState({}, '', newUrl);
            }

            // 🔥 Ambil dan render event dari server
            async function renderDayEvents() {
                const dayColumn = document.querySelector('.day-column');
                if (!dayColumn) return;

                const dateString =
                    `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(currentDate.getDate()).padStart(2, '0')}`;

                // Bersihkan event lama
                dayColumn.querySelectorAll('.event-item').forEach(e => e.remove());

                const res = await fetch(`/dashboard/hari/data?tanggal=${dateString}`);
                const data = await res.json();

                data.forEach(event => {
                    const eventEl = document.createElement('div');
                    eventEl.classList.add('event-item');
                    eventEl.style.backgroundColor = event.color || '#3a7bd5';

                    const start = new Date(`1970-01-01T${event.start_time}`);
                    const end = new Date(`1970-01-01T${event.end_time}`);
                    const duration = (end - start) / (1000 * 60);

                    const pxPerMinute = 1;
                    const top = start.getHours() * 60 * pxPerMinute + start.getMinutes() * pxPerMinute;
                    const height = duration * pxPerMinute;

                    eventEl.style.top = `${top}px`;
                    eventEl.style.height = `${height}px`;
                    eventEl.innerHTML = `
                <strong>${event.title}</strong><br>
                <small>${event.start_time} - ${event.end_time}</small>
            `;

                    dayColumn.appendChild(eventEl);
                });
            }

            // 🟢 Pastikan render setelah semua siap
            updateDayDisplay();
            renderDayEvents();
        });
    </script>
@endsection
