@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-hari.css') }}">
@endpush

@section('content')
    @include('show_agenda_modal_landing')
    <div class="calendar-page">
        <div class="calendar-content-wrapper">
            <!-- Mini Calendar di Kiri -->
            <div class="calendar-left-sidebar">
                @include('components.mini-calendar-categories')
            </div>

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
                    <div class="time-slot">00:00</div>
                    <div class="time-slot">01:00</div>
                    <div class="time-slot">02:00</div>
                    <div class="time-slot">03:00</div>
                    <div class="time-slot">04:00</div>
                    <div class="time-slot">05:00</div>
                    <div class="time-slot">06:00</div>
                    <div class="time-slot">07:00</div>
                    <div class="time-slot">08:00</div>
                    <div class="time-slot">09:00</div>
                    <div class="time-slot">10:00</div>
                    <div class="time-slot">11:00</div>
                    <div class="time-slot">12:00</div>
                    <div class="time-slot">13:00</div>
                    <div class="time-slot">14:00</div>
                    <div class="time-slot">15:00</div>
                    <div class="time-slot">16:00</div>
                    <div class="time-slot">17:00</div>
                    <div class="time-slot">18:00</div>
                    <div class="time-slot">19:00</div>
                    <div class="time-slot">20:00</div>
                    <div class="time-slot">21:00</div>
                    <div class="time-slot">22:00</div>
                    <div class="time-slot">23:00</div>
                </div>

                <div class="day-column">
                    <div class="hour-slot" data-hour="0"></div>
                    <div class="hour-slot" data-hour="1"></div>
                    <div class="hour-slot" data-hour="2"></div>
                    <div class="hour-slot" data-hour="3"></div>
                    <div class="hour-slot" data-hour="4"></div>
                    <div class="hour-slot" data-hour="5"></div>
                    <div class="hour-slot" data-hour="6"></div>
                    <div class="hour-slot" data-hour="7"></div>
                    <div class="hour-slot" data-hour="8"></div>
                    <div class="hour-slot" data-hour="9"></div>
                    <div class="hour-slot" data-hour="10"></div>
                    <div class="hour-slot" data-hour="11"></div>
                    <div class="hour-slot" data-hour="12"></div>
                    <div class="hour-slot" data-hour="13"></div>
                    <div class="hour-slot" data-hour="14"></div>
                    <div class="hour-slot" data-hour="15"></div>
                    <div class="hour-slot" data-hour="16"></div>
                    <div class="hour-slot" data-hour="17"></div>
                    <div class="hour-slot" data-hour="18"></div>
                    <div class="hour-slot" data-hour="19"></div>
                    <div class="hour-slot" data-hour="20"></div>
                    <div class="hour-slot" data-hour="21"></div>
                    <div class="hour-slot" data-hour="22"></div>
                    <div class="hour-slot" data-hour="23"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Ambil parameter tanggal dari URL (?tanggal=YYYY-MM-DD)
        const urlParams = new URLSearchParams(window.location.search);
        const tanggalParam = urlParams.get('tanggal');

        // Jika ada parameter, pakai itu; jika tidak, pakai hari ini
        let currentDate = tanggalParam ? new Date(tanggalParam) : new Date();

        const dayEvents = {};

        // Fungsi untuk update teks hari (format Indonesia, kapital huruf pertama)
        function updateDayDisplay() {
            const dayElement = document.getElementById('currentDay');
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const formatted = currentDate.toLocaleDateString('id-ID', options);
            dayElement.textContent = formatted.charAt(0).toUpperCase() + formatted.slice(1);
        }

        // Ganti hari (panah kiri / kanan)
        function changeDay(direction) {
            currentDate.setDate(currentDate.getDate() + direction);
            updateDayDisplay();
            renderDayEvents();

            // Update URL pada path yang sama (tetap di /dashboard/hari jika itu path sekarang)
            const newDateStr =
                `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(currentDate.getDate()).padStart(2, '0')}`;
            const basePath = window.location.pathname.split('?')[0]; // tetap di /dashboard/hari
            const newUrl = `${basePath}?tanggal=${newDateStr}`;
            window.history.pushState({}, '', newUrl);
        }

        // Render event publik dari API landing
        async function renderDayEvents() {
            const dayColumn = document.querySelector('.day-column');
            if (!dayColumn) return;

            const dateString =
                `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(currentDate.getDate()).padStart(2, '0')}`;

            // Bersihkan event lama
            dayColumn.querySelectorAll('.event-item').forEach(item => item.remove());

            try {
                const res = await fetch(`/api/agenda/date/${dateString}`);
                const json = await res.json();
                const items = Array.isArray(json) ? json : (Array.isArray(json.data) ? json.data : []);

                items.forEach(event => {
                    const eventEl = document.createElement('div');
                    eventEl.classList.add('event-item');
                    eventEl.classList.add('green');

                    const startTimeStr = event.start_time && /^\d{2}:\d{2}/.test(event.start_time) ? event.start_time : '08:00:00';
                    const endTimeStr = event.end_time && /^\d{2}:\d{2}/.test(event.end_time) ? event.end_time : '09:00:00';

                    const start = new Date(`1970-01-01T${startTimeStr}`);
                    const end = new Date(`1970-01-01T${endTimeStr}`);
                    let duration = (end - start) / (1000 * 60);
                    if (!isFinite(duration) || duration <= 0) duration = 30;

                    const pxPerMinute = 1;
                    const top = start.getHours() * 60 * pxPerMinute + start.getMinutes() * pxPerMinute;
                    const height = Math.max(duration * pxPerMinute, 24);

                    eventEl.style.top = `${top}px`;
                    eventEl.style.height = `${height}px`;
                    eventEl.innerHTML = `
                        <div class="event-content">
                            <div class="event-title">${event.agenda_name || 'Agenda'}</div>
                            <div class="event-time">${startTimeStr.slice(0,5)} - ${endTimeStr.slice(0,5)}</div>
                        </div>
                    `;
                    eventEl.addEventListener('click', function() {
                        openShowAgendaModal({
                            agenda_name: event.agenda_name,
                            description: event.description,
                            date: `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(currentDate.getDate()).padStart(2, '0')}`,
                            start_time: startTimeStr,
                            end_time: endTimeStr,
                            location: event.location,
                            unit: event.unit,
                            involved_institution: event.involved_institution,
                            is_public: event.is_public,
                            notes: event.notes
                        });
                    });
                    dayColumn.appendChild(eventEl);
                });
            } catch (e) {
                // diamkan saja di landing jika API error; tampil kosong
                console.error('Gagal memuat agenda publik:', e);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateDayDisplay();
            renderDayEvents();
        });

        // Optional: handle back/forward so the page reflects the query param if user navigates history
        window.addEventListener('popstate', () => {
            const params = new URLSearchParams(window.location.search);
            const t = params.get('tanggal');
            currentDate = t ? new Date(t) : new Date();
            updateDayDisplay();
            renderDayEvents();
        });

        function openShowAgendaModal(data) {
            function formatDate(dateString) {
                if (!dateString) return "-";
                const date = new Date(dateString);
                const options = { day: 'numeric', month: 'long', year: 'numeric', weekday: 'long' };
                return date.toLocaleDateString('id-ID', options);
            }

            document.getElementById('showAgendaName').innerText = data.agenda_name || 'Agenda';
            document.getElementById('showAgendaDesc').innerText = data.description || '-';
            document.getElementById('showAgendaDate').innerText = formatDate(data.date);

            const timeText = (data.start_time && data.end_time)
                ? `${data.start_time.slice(0,5)} - ${data.end_time.slice(0,5)}`
                : (data.end_time ? data.end_time.slice(0,5) : (data.start_time ? data.start_time.slice(0,5) : '-'));
            document.getElementById('showAgendaTime').innerText = timeText;

            document.getElementById('showAgendaLocation').innerText = data.location || '-';

            const unitName = (data.unit && data.unit.unit_name) ? data.unit.unit_name : '-';
            const unitEl = document.getElementById('showAgendaUnit');
            if (unitEl) unitEl.innerText = unitName;

            const involvedEl = document.getElementById('showAgendaInvolved');
            if (involvedEl) involvedEl.innerText = data.involved_institution || '-';

            const accessEl = document.getElementById('showAgendaAccess');
            if (accessEl) {
                const isPublic = data.is_public == 1 || data.is_public === true;
                accessEl.innerText = isPublic ? 'Publik' : 'Privasi';
            }

            const notesEl = document.getElementById('showAgendaNotes');
            if (notesEl) notesEl.innerText = data.notes || '-';

            const modal = new bootstrap.Modal(document.getElementById('showAgendaModal'));
            modal.show();
        }
    </script>
@endsection
