    @extends('layouts.main')

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard-hari.css') }}">
    @endpush

    @section('content')
        @include('tambah_agenda_modal_hari')
        <div class="calendar-page">
            <div class="calendar-content-wrapper">
                <!-- Mini Calendar di Kiri -->
                <div class="calendar-left-sidebar">
                    @include('components.mini-calendar-categories')
                </div>

                <!-- Main Content Hari -->
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

                window.changeDay = function(direction) {
                    currentDate.setDate(currentDate.getDate() + direction);
                    updateDayDisplay();
                    renderDayEvents();

                    const y = currentDate.getFullYear();
                    const m = String(currentDate.getMonth() + 1).padStart(2, '0');
                    const d = String(currentDate.getDate()).padStart(2, '0');
                    const newUrl = `${window.location.pathname}?tanggal=${y}-${m}-${d}`;
                    window.history.pushState({}, '', newUrl);
                }

                // === Klik slot jam untuk buka modal create agenda ===
                document.querySelectorAll('.hour-slot').forEach(slot => {
                    slot.addEventListener('click', function(event) {

                        // Ambil jam dari data-hour
                        const hour = this.dataset.hour;

                        // Format jam ke "HH:00"
                        const formattedHour = String(hour).padStart(2, '0') + ":00";

                        // Format tanggal sesuai currentDate
                        const year = currentDate.getFullYear();
                        const month = String(currentDate.getMonth() + 1).padStart(2, '0');
                        const day = String(currentDate.getDate()).padStart(2, '0');
                        const fullDate = `${year}-${month}-${day}`;

                        // Isi field otomatis
                        document.getElementById('date').value = fullDate;
                        document.getElementById('start_time').value = formattedHour;

                        // Kosongkan end time supaya user isi sendiri
                        document.getElementById('end_time').value = "";

                        // Buka modal
                        openCreateModal();
                    });
                });

                // Fungsi buka modal tambah agenda
                function openCreateModal() {
                    document.getElementById('createAgendaModalHari').style.display = 'flex';
                }

                // Fungsi tutup modal (biar konsisten)
                function closeModal() {
                    document.getElementById('createAgendaModalHari').style.display = 'none';
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
                    const json = await res.json();
                    const items = Array.isArray(json) ? json : (Array.isArray(json.data) ? json.data : []);

                    // Parse dan siapkan data event
                    const events = items.map(event => {
                        const startTimeStr = event.start_time && /^\d{2}:\d{2}/.test(event.start_time) ?
                            event.start_time : '08:00:00';
                        const endTimeStr = event.end_time && /^\d{2}:\d{2}/.test(event.end_time) ? event
                            .end_time : '09:00:00';

                        const start = new Date(`1970-01-01T${startTimeStr}`);
                        const end = new Date(`1970-01-01T${endTimeStr}`);
                        let duration = (end - start) / (1000 * 60);
                        if (!isFinite(duration) || duration <= 0) duration = 30;

                        const pxPerMinute = 1;
                        const top = start.getHours() * 60 * pxPerMinute + start.getMinutes() * pxPerMinute;
                        const height = Math.max(duration * pxPerMinute, 24);
                        const bottom = top + height;

                        return {
                            ...event,
                            startTime: start,
                            endTime: end,
                            startMinutes: start.getHours() * 60 + start.getMinutes(),
                            endMinutes: end.getHours() * 60 + end.getMinutes(),
                            top,
                            height,
                            bottom,
                            startTimeStr,
                            endTimeStr
                        };
                    });

                    // Sort events by start time
                    events.sort((a, b) => a.startMinutes - b.startMinutes);

                    // Fungsi untuk cek apakah dua event overlap
                    function eventsOverlap(e1, e2) {
                        return e1.startMinutes < e2.endMinutes && e1.endMinutes > e2.startMinutes;
                    }

                    // Pass 1: Assign kolom untuk setiap event
                    const eventLayouts = [];
                    events.forEach(event => {
                        // Cari semua event yang sudah di-assign dan overlap dengan event ini
                        const overlappingLayouts = eventLayouts.filter(layout =>
                            eventsOverlap(layout.event, event)
                        );

                        // Cari kolom yang sudah digunakan oleh event yang overlap
                        const usedColumns = new Set(overlappingLayouts.map(l => l.column));

                        // Assign kolom pertama yang tersedia
                        let assignedColumn = 0;
                        while (usedColumns.has(assignedColumn)) {
                            assignedColumn++;
                        }

                        eventLayouts.push({
                            event,
                            column: assignedColumn,
                            totalColumns: 1 // akan di-update di pass 2
                        });
                    });

                    // Pass 2: Hitung totalColumns untuk setiap grup overlap
                    events.forEach((event, index) => {
                        const overlappingEvents = events.filter(e =>
                            e !== event && eventsOverlap(e, event)
                        );

                        if (overlappingEvents.length > 0) {
                            // Semua event dalam grup overlap ini
                            const allInGroup = [event, ...overlappingEvents];

                            // Cari kolom maksimum yang digunakan oleh grup ini
                            const maxCol = Math.max(...allInGroup.map(oe => {
                                const idx = events.indexOf(oe);
                                return idx >= 0 ? eventLayouts[idx].column : 0;
                            })) + 1;

                            // Update totalColumns untuk semua event dalam grup
                            allInGroup.forEach(oe => {
                                const idx = events.indexOf(oe);
                                if (idx >= 0) {
                                    eventLayouts[idx].totalColumns = maxCol;
                                }
                            });
                        }
                    });

                    // Render events dengan layout yang sudah dihitung
                    eventLayouts.forEach(layout => {
                        const {
                            event,
                            column,
                            totalColumns
                        } = layout;
                        const eventEl = document.createElement('div');
                        eventEl.classList.add('event-item');
                        const isPublic = event.is_public == 1 || event.is_public === true;
                        eventEl.classList.add(isPublic ? 'green' : 'orange');

                        // Hitung lebar dan posisi kiri berdasarkan kolom dengan gap yang lebih jelas
                        const gapPercent = 1.5; // gap 1.5% antar kolom untuk jarak yang lebih jelas
                        const usableWidth = 100 - (totalColumns - 1) * gapPercent;
                        const columnWidth = usableWidth / totalColumns;
                        const leftPercent = column * (columnWidth + gapPercent);
                        const widthPercent = columnWidth;

                        // Tambahkan gap vertikal 4px antar event yang berdekatan
                        const verticalGap = 4;
                        // Tambahkan offset kecil di top dan kurangi height untuk memberikan gap
                        eventEl.style.top = `${event.top + (verticalGap / 2)}px`;
                        eventEl.style.height = `${Math.max(event.height - verticalGap, 20)}px`;
                        eventEl.style.left = `${leftPercent}%`;
                        eventEl.style.width = `${widthPercent}%`;

                        // Simpan ID agenda dan data lengkap untuk modal
                        eventEl.dataset.agendaId = event.id_agenda || event.id || null;
                        eventEl.dataset.agendaData = JSON.stringify(event);

                        eventEl.innerHTML = `
                            <div class="event-content">
                                <div class="event-title">${event.title || event.agenda_name || 'Agenda'}</div>
                                <div class="event-time">${event.startTimeStr.slice(0, 5)} - ${event.endTimeStr.slice(0, 5)}</div>
                            </div>
                        `;

                        // Tambahkan event listener untuk click
                        eventEl.addEventListener('click', function() {
                            const agendaId = eventEl.dataset.agendaId;
                            const agendaData = JSON.parse(eventEl.dataset.agendaData || '{}');

                            // Jika ada ID, ambil detail dari server
                            if (agendaId && !agendaData.is_external) {
                                fetch(`/dashboard/agenda/${agendaId}`)
                                    .then(res => res.json())
                                    .then(data => {
                                        openShowAgendaModal(data);
                                    })
                                    .catch(err => {
                                        console.error('Error:', err);
                                        // Fallback: gunakan data yang sudah ada
                                        openShowAgendaModal(agendaData);
                                    });
                            } else {
                                // Untuk external agenda atau agenda tanpa ID, gunakan data yang sudah ada
                                openShowAgendaModal(agendaData);
                            }
                        });

                        dayColumn.appendChild(eventEl);
                    });
                }

                // Fungsi untuk membuka modal detail agenda (menggunakan fungsi yang sudah ada)
                function openShowAgendaModal(data) {
                    // Format tanggal
                    function formatDate(dateString) {
                        if (!dateString) return "-";
                        const date = new Date(dateString);
                        const options = {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric',
                            weekday: 'long'
                        };
                        return date.toLocaleDateString('id-ID', options);
                    }

                    // Isi data ke modal
                    document.getElementById('showAgendaName').innerText = data.agenda_name || data.title || '-';
                    document.getElementById('showAgendaDesc').innerText = data.description || data.desc || '-';
                    document.getElementById('showAgendaDate').innerText = formatDate(data.date);

                    const timeText = (data.start_time && data.end_time) ?
                        `${data.start_time.slice(0, 5)} - ${data.end_time.slice(0, 5)}` :
                        (data.end_time ? data.end_time.slice(0, 5) : (data.start_time ? data.start_time.slice(0, 5) :
                            '-'));
                    document.getElementById('showAgendaTime').innerText = timeText;

                    document.getElementById('showAgendaLocation').innerText = data.location || '-';

                    // Isi data instansi
                    const involved = data.involved_institution || '-';
                    const unitName = (data.unit && data.unit.unit_name) ? data.unit.unit_name : '-';

                    const unitEl = document.getElementById('showAgendaUnit');
                    if (unitEl) unitEl.innerText = unitName;

                    const involvedEl = document.getElementById('showAgendaInvolved');
                    if (involvedEl) involvedEl.innerText = involved;

                    // Isi status akses (publik/privasi)
                    const accessEl = document.getElementById('showAgendaAccess');
                    if (accessEl) {
                        const isPublic = data.is_public == 1 || data.is_public === true;
                        accessEl.innerText = isPublic ? 'Publik' : 'Privasi';
                    }

                    const notesEl = document.getElementById('showAgendaNotes');
                    if (notesEl) notesEl.innerText = data.notes || '-';

                    // Tampilkan modal
                    const modal = new bootstrap.Modal(document.getElementById('showAgendaModal'));
                    modal.show();
                }


                // 🟢 Pastikan render setelah semua siap
                updateDayDisplay();
                renderDayEvents();
            });

            // ==== FUNGSI BUKA & TUTUP MODAL ====
            function openModal() {
                const modal = document.getElementById('createAgendaModalHari');
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                const modal = document.getElementById('createAgendaModalHari');
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }

            // Tutup modal jika klik area luar kontainer
            document.addEventListener('click', function(e) {
                const modal = document.getElementById('createAgendaModalHari');
                if (e.target === modal) {
                    closeModal();
                }
            });

            // ==== FUNGSI AGAR KLIK JAM OTOMATIS TAMPILKAN MODAL ====
            document.addEventListener('click', function(e) {
                // Pastikan elemen jam punya class "hour-slot"
                if (e.target.classList.contains('hour-slot')) {

                    const selectedDate = e.target.getAttribute('data-date');
                    const selectedTime = e.target.getAttribute('data-time');

                    // Isi input tanggal
                    if (selectedDate) {
                        document.querySelector('input[name="date"]').value = selectedDate;
                    }

                    // Isi input waktu mulai
                    if (selectedTime) {
                        document.querySelector('input[name="start_time"]').value = selectedTime;

                        // Waktu selesai otomatis +1 jam
                        let [h, m] = selectedTime.split(':');
                        h = parseInt(h) + 1;
                        if (h < 10) h = '0' + h;
                        document.querySelector('input[name="end_time"]').value = `${h}:${m}`;
                    }

                    // Buka modal
                    openModal();
                }
            });

            // ==== AGAR FORM TIDAK SUBMIT GANDA ====
            function handleFormSubmit(event) {
                event.target.querySelector('button[type="submit"]').disabled = true;
                return true;
            }
        </script>
    @endsection
