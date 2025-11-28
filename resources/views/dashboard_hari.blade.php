    @extends('layouts.main')

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard-hari.css') }}">
        <link rel="stylesheet" href="{{ asset('css/create_agenda_modal.css') }}">
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

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
        <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

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
                window.renderDayEvents = async function() {
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
                        const top = start.getHours() * 60 * pxPerMinute + start.getMinutes() *
                            pxPerMinute;
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

                    // Pass 3: Kelompokkan event yang overlap menjadi grup
                    const eventGroups = [];
                    const processedEvents = new Set();

                    events.forEach((event, index) => {
                        if (processedEvents.has(index)) return;

                        const layout = eventLayouts[index];
                        // Cari semua event yang overlap dengan event ini (termasuk yang sudah overlap dengan yang lain)
                        const overlappingEvents = [];
                        const toCheck = [event];
                        const checked = new Set([index]);

                        // BFS untuk menemukan semua event yang saling overlap
                        while (toCheck.length > 0) {
                            const current = toCheck.shift();
                            const currentIdx = events.indexOf(current);

                            events.forEach((e, idx) => {
                                if (!checked.has(idx) && eventsOverlap(current, e)) {
                                    overlappingEvents.push(e);
                                    toCheck.push(e);
                                    checked.add(idx);
                                }
                            });
                        }

                        if (overlappingEvents.length > 0) {
                            // Ada overlap, buat grup
                            const groupEvents = [event, ...overlappingEvents];
                            const groupLayouts = [layout, ...overlappingEvents.map(e => {
                                const idx = events.indexOf(e);
                                return eventLayouts[idx];
                            })];

                            // Hitung posisi dan ukuran grup (menggunakan event yang paling awal dan paling akhir)
                            const groupStart = Math.min(...groupEvents.map(e => e.startMinutes));
                            const groupEnd = Math.max(...groupEvents.map(e => e.endMinutes));
                            const groupTop = groupStart * 1; // 1px per menit
                            const groupHeight = Math.max((groupEnd - groupStart) * 1,
                            30); // minimal 30px

                            eventGroups.push({
                                events: groupEvents,
                                layouts: groupLayouts,
                                top: groupTop,
                                height: groupHeight,
                                startMinutes: groupStart,
                                endMinutes: groupEnd,
                                isGroup: true
                            });

                            // Tandai semua event dalam grup sebagai sudah diproses
                            checked.forEach(idx => processedEvents.add(idx));
                        } else {
                            // Tidak ada overlap, render individual
                            eventGroups.push({
                                events: [event],
                                layouts: [layout],
                                top: event.top,
                                height: event.height,
                                startMinutes: event.startMinutes,
                                endMinutes: event.endMinutes,
                                isGroup: false
                            });
                            processedEvents.add(index);
                        }
                    });

                    // Render event groups
                    eventGroups.forEach(group => {
                        if (group.isGroup && group.events.length > 1) {
                            // Render sebagai grup (badge dengan jumlah kegiatan)
                            const layout = group.layouts[0];
                            const isPublic = group.events.some(e => e.is_public == 1 || e.is_public ===
                                true);

                            // Gunakan warna berdasarkan kategori mayoritas
                            const publicCount = group.events.filter(e => e.is_public == 1 || e
                                .is_public === true).length;
                            const privateCount = group.events.length - publicCount;
                            const usePublicColor = publicCount >= privateCount;

                            const eventEl = document.createElement('div');
                            eventEl.classList.add('event-item');
                            eventEl.classList.add(usePublicColor ? 'green' : 'orange');
                            eventEl.classList.add('event-group');

                            // Hitung posisi dan ukuran
                            const verticalGap = 4;
                            eventEl.style.top = `${group.top + (verticalGap / 2)}px`;
                            eventEl.style.height = `${Math.max(group.height - verticalGap, 30)}px`;
                            eventEl.style.left = '0%';
                            eventEl.style.width = '100%';

                            // Simpan semua data agenda dalam grup
                            eventEl.dataset.groupData = JSON.stringify(group.events);

                            // Format waktu untuk grup
                            const startHour = Math.floor(group.startMinutes / 60);
                            const startMin = group.startMinutes % 60;
                            const endHour = Math.floor(group.endMinutes / 60);
                            const endMin = group.endMinutes % 60;
                            const startTimeStr =
                                `${String(startHour).padStart(2, '0')}:${String(startMin).padStart(2, '0')}`;
                            const endTimeStr =
                                `${String(endHour).padStart(2, '0')}:${String(endMin).padStart(2, '0')}`;

                            eventEl.innerHTML = `
                                <div class="event-content">
                                    <div class="event-title">${group.events.length} Kegiatan</div>
                                    <div class="event-time">${startTimeStr} - ${endTimeStr}</div>
                                </div>
                            `;

                            // Event listener untuk click - tampilkan semua agenda dalam grup
                            eventEl.addEventListener('click', function() {
                                // Buka sidebar atau modal dengan semua agenda dalam grup
                                showAgendaGroupModal(group.events);
                            });

                            dayColumn.appendChild(eventEl);
                        } else {
                            // Render individual event
                            const event = group.events[0];
                            const layout = group.layouts[0];
                            const eventEl = document.createElement('div');
                            eventEl.classList.add('event-item');
                            const isPublic = event.is_public == 1 || event.is_public === true;
                            eventEl.classList.add(isPublic ? 'green' : 'orange');

                            // Hitung lebar dan posisi kiri
                            const verticalGap = 4;
                            eventEl.style.top = `${event.top + (verticalGap / 2)}px`;
                            eventEl.style.height = `${Math.max(event.height - verticalGap, 20)}px`;
                            eventEl.style.left = '0%';
                            eventEl.style.width = '100%';

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
                                const agendaData = JSON.parse(eventEl.dataset.agendaData ||
                                    '{}');

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
                        }
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

                // Fungsi untuk menampilkan daftar agenda dalam grup
                function showAgendaGroupModal(agendaList) {
                    // Urutkan agenda berdasarkan waktu
                    const sortedAgendaList = [...agendaList].sort((a, b) => {
                        const timeA = a.start_time || a.end_time || '00:00:00';
                        const timeB = b.start_time || b.end_time || '00:00:00';
                        return timeA.localeCompare(timeB);
                    });

                    // Jika hanya 1 agenda, langsung buka modal detail
                    if (sortedAgendaList.length === 1) {
                        openShowAgendaModal(sortedAgendaList[0]);
                        return;
                    }

                    // Buat modal untuk menampilkan daftar agenda
                    let modalHTML = `
                        <div class="modal fade" id="agendaGroupModal" tabindex="-1" aria-labelledby="agendaGroupLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 shadow-sm rounded-4 overflow-hidden">
                                    <div class="modal-header" style="background-color: #F6F8F7; border: none;">
                                        <h5 class="modal-title fw-semibold d-flex align-items-center gap-2" style="color: #4A7C59;">
                                            <i class="fas fa-calendar-alt" style="color: #4A7C59;"></i>
                                            <span>${sortedAgendaList.length} Kegiatan</span>
                                        </h5>
                                        <button type="button" class="close-modal" data-bs-dismiss="modal" aria-label="Tutup">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                    <div class="modal-body px-4 pt-3 pb-4">
                                        <div class="list-group">
                    `;

                    sortedAgendaList.forEach((item, index) => {
                        const timeText = (item.start_time && item.end_time) ?
                            `${item.start_time.slice(0, 5)} - ${item.end_time.slice(0, 5)}` :
                            (item.end_time ? item.end_time.slice(0, 5) : (item.start_time ? item.start_time
                                .slice(0, 5) : '-'));

                        const isPublic = item.is_public == 1 || item.is_public === true;
                        const badgeColor = isPublic ? '#A8E6A3' : '#FFB67E';
                        const badgeText = isPublic ? 'Publik' : 'Privasi';

                        modalHTML += `
                            <div class="list-group-item list-group-item-action agenda-list-item" style="cursor: pointer; border: 1px solid #e9ecef; border-radius: 8px; margin-bottom: 10px; padding: 15px;" data-agenda-index="${index}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-2 fw-semibold" style="color: #2f4737;">${item.agenda_name || item.title || 'Agenda'}</h6>
                                        <div class="d-flex align-items-center gap-3 mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>${timeText}
                                            </small>
                                            <span class="badge rounded-pill" style="background-color: ${badgeColor}; color: #2F3E35; padding: 4px 10px; font-size: 0.75rem;">
                                                ${badgeText}
                                            </span>
                                        </div>
                                        ${item.location ? `<small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>${item.location}</small>` : ''}
                                    </div>
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </div>
                            </div>
                        `;
                    });

                    modalHTML += `
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    // Hapus modal lama jika ada
                    const oldModal = document.getElementById('agendaGroupModal');
                    if (oldModal) {
                        oldModal.remove();
                    }

                    // Tambahkan modal ke body
                    document.body.insertAdjacentHTML('beforeend', modalHTML);

                    // Tambahkan event listener untuk setiap item
                    document.querySelectorAll('.agenda-list-item').forEach(item => {
                        item.addEventListener('click', function() {
                            const index = parseInt(this.getAttribute('data-agenda-index'));
                            const agendaData = sortedAgendaList[index];

                            // Tutup modal grup
                            const modal = bootstrap.Modal.getInstance(document.getElementById(
                                'agendaGroupModal'));
                            if (modal) modal.hide();

                            // Buka modal detail agenda
                            setTimeout(() => {
                                openShowAgendaModal(agendaData);
                            }, 300);
                        });
                    });

                    // Tampilkan modal
                    const modal = new bootstrap.Modal(document.getElementById('agendaGroupModal'));
                    modal.show();

                    // Hapus modal dari DOM setelah ditutup
                    document.getElementById('agendaGroupModal').addEventListener('hidden.bs.modal', function() {
                        this.remove();
                    });
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
                const form = document.getElementById('createAgendaFormHari');

                modal.style.display = 'none';
                document.body.style.overflow = 'auto';

                // Reset form
                if (form) {
                    form.reset();
                }

                // Reset chips multiselect
                const chipsRoot = document.getElementById('involvedInstansiHari');
                if (chipsRoot) {
                    const selectedWrap = chipsRoot.querySelector('.chips-selected');
                    const hiddenField = document.getElementById('involvedInstitutionFieldHari');
                    const mainInput = chipsRoot.querySelector('.chips-input');
                    const dropdown = chipsRoot.querySelector('.chips-dropdown');

                    if (selectedWrap) {
                        selectedWrap.innerHTML = '';
                    }
                    if (hiddenField) {
                        hiddenField.value = '';
                    }
                    if (mainInput) {
                        mainInput.style.display = 'inline';
                    }
                    if (dropdown) {
                        dropdown.classList.remove('open');
                    }
                    chipsRoot.classList.remove('open');
                    chipsRoot.classList.add('empty');
                }
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

            // ==== HANDLE FORM SUBMIT ====
            function handleFormSubmit(e) {
                e.preventDefault();

                const form = e.target;

                // Pastikan chips multiselect sudah sync sebelum submit
                const chipsRoot = document.getElementById('involvedInstansiHari');
                if (chipsRoot) {
                    const hiddenField = document.getElementById('involvedInstitutionFieldHari');
                    const selectedChips = chipsRoot.querySelectorAll('.chip');
                    if (selectedChips.length > 0 && hiddenField) {
                        const values = Array.from(selectedChips).map(chip => chip.getAttribute('data-value'));
                        hiddenField.value = values.join(', ');
                    }
                }

                const formData = new FormData(form);
                const submitBtn = form.querySelector('button[type="submit"]');

                // Disable button to prevent double submission
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';

                fetch(form.action, {
                        method: "POST",
                        headers: {
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                        },
                        body: formData
                    })
                    .then(async res => {
                        let data;
                        try {
                            // Coba parse sebagai JSON
                            data = await res.json();
                        } catch (e) {
                            // Jika bukan JSON, throw error
                            throw new Error('Response tidak valid dari server');
                        }

                        // Jika response tidak OK (status 422 untuk validation, 500 untuk server error, dll)
                        if (!res.ok) {
                            // Handle validation errors
                            if (res.status === 422 && data.errors) {
                                const errorMessages = Object.values(data.errors).flat().join(', ');
                                throw new Error(errorMessages || 'Validasi gagal');
                            }
                            // Handle other errors
                            throw new Error(data.message || data.error || `Error: ${res.status} ${res.statusText}`);
                        }

                        return data;
                    })
                    .then(data => {
                        if (data.success) {
                            closeModal();
                            // Reset form dan chips
                            form.reset();

                            // Reset chips multiselect
                            const chipsRoot = document.getElementById('involvedInstansiHari');
                            if (chipsRoot) {
                                const selectedWrap = chipsRoot.querySelector('.chips-selected');
                                const hiddenField = document.getElementById('involvedInstitutionFieldHari');
                                const mainInput = chipsRoot.querySelector('.chips-input');

                                if (selectedWrap) {
                                    selectedWrap.innerHTML = '';
                                }
                                if (hiddenField) {
                                    hiddenField.value = '';
                                }
                                if (mainInput) {
                                    mainInput.style.display = 'inline';
                                }
                                chipsRoot.classList.add('empty');
                            }

                            // Reload events
                            renderDayEvents();

                            Toastify({
                                text: data.message || "Agenda berhasil ditambahkan!",
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#4caf50",
                                stopOnFocus: true
                            }).showToast();
                        } else {
                            Toastify({
                                text: data.message || "Gagal menambahkan agenda!",
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#f44336",
                                stopOnFocus: true
                            }).showToast();
                        }
                    })
                    .catch(err => {
                        console.error('Error submitting form:', err);

                        let errorMessage = "Terjadi kesalahan pada server.";
                        if (err.message) {
                            errorMessage = err.message;
                        }

                        Toastify({
                            text: errorMessage,
                            duration: 4000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#f44336",
                            stopOnFocus: true
                        }).showToast();
                    })
                    .finally(() => {
                        // Re-enable button
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Ajukan Agenda';
                    });

                return false;
            }
        </script>

        <script>
            // Chips Multiselect untuk Involved Institution di Dashboard Hari
            (() => {
                const root = document.getElementById('involvedInstansiHari');
                if (!root) return;

                const dropdown = root.querySelector('.chips-dropdown');
                const arrow = root.querySelector('.chips-arrow');
                const searchInput = root.querySelector('.chips-search-input');
                const mainInput = root.querySelector('.chips-input');
                const selectedWrap = root.querySelector('.chips-selected');
                const hiddenField = document.getElementById('involvedInstitutionFieldHari');
                const allListItems = Array.from(dropdown.querySelectorAll('li.dropdown-item'));
                const listItems = allListItems.filter(li => !li.classList.contains('add-new-instansi-option'));
                const selectAllOption = dropdown.querySelector('.select-all-option');
                const container = root.querySelector('.chips-container');
                const addNewInputContainer = dropdown.querySelector('.chips-add-new-input-container');
                const addNewInput = dropdown.querySelector('.chips-add-new-input');
                const addConfirmBtn = dropdown.querySelector('.chips-add-confirm-btn');
                const addCancelBtn = dropdown.querySelector('.chips-add-cancel-btn');

                let selectedValues = [];

                function showAddNewInput(initialValue = '') {
                    if (addNewInputContainer) {
                        addNewInputContainer.style.display = 'block';
                        if (addNewInput) {
                            addNewInput.value = initialValue;
                            setTimeout(() => addNewInput.focus(), 100);
                        }
                    }
                }

                function hideAddNewInput() {
                    if (addNewInputContainer) {
                        addNewInputContainer.style.display = 'none';
                        if (addNewInput) {
                            addNewInput.value = '';
                        }
                        const addNewInstansiOption = dropdown.querySelector('.add-new-instansi-option');
                        if (addNewInstansiOption) {
                            addNewInstansiOption.style.display = 'flex';
                        }
                    }
                }

                function toggleDropdown() {
                    const isOpen = dropdown.classList.toggle('open');
                    root.classList.toggle('open', isOpen);
                    if (isOpen) {
                        searchInput.value = '';
                        filterList('');
                        hideAddNewInput();
                        const addNewInstansiOption = dropdown.querySelector('.add-new-instansi-option');
                        if (addNewInstansiOption) {
                            addNewInstansiOption.style.display = 'flex';
                        }
                        searchInput.focus();
                        listItems.forEach(li => {
                            li.style.display = 'flex';
                            const value = li.getAttribute('data-value');
                            updateItemState(value);
                        });
                        selectAllOption.style.display = 'flex';
                        updateSelectAllState();
                    } else {
                        hideAddNewInput();
                    }
                }

                function closeDropdown() {
                    dropdown.classList.remove('open');
                    root.classList.remove('open');
                    hideAddNewInput();
                    searchInput.value = '';
                    const addNewInstansiOption = dropdown.querySelector('.add-new-instansi-option');
                    if (addNewInstansiOption) {
                        addNewInstansiOption.style.display = 'flex';
                    }
                }

                function toggleItem(value) {
                    const index = selectedValues.indexOf(value);
                    if (index > -1) {
                        selectedValues.splice(index, 1);
                        removeChip(value);
                    } else {
                        selectedValues.push(value);
                        addChip(value);
                    }
                    updateItemState(value);
                    updateSelectAllState();
                    syncHidden();
                }

                function addChip(value) {
                    const existingChip = selectedWrap.querySelector(`.chip[data-value="${value}"]`);
                    if (existingChip) return;

                    const chip = document.createElement('span');
                    chip.className = 'chip';
                    chip.setAttribute('data-value', value);
                    chip.textContent = value;

                    const btn = document.createElement('button');
                    btn.className = 'chip-remove';
                    btn.innerHTML = '&times;';
                    btn.onclick = (e) => {
                        e.stopPropagation();
                        toggleItem(value);
                    };

                    chip.appendChild(btn);
                    selectedWrap.appendChild(chip);
                }

                function removeChip(value) {
                    const chip = selectedWrap.querySelector(`.chip[data-value="${value}"]`);
                    if (chip) {
                        chip.remove();
                    }
                }

                function updateItemState(value) {
                    const item = listItems.find(li => li.getAttribute('data-value') === value);
                    if (item) {
                        const isSelected = selectedValues.includes(value);
                        item.classList.toggle('selected', isSelected);
                        const checkmark = item.querySelector('.checkmark-icon');
                        if (checkmark) {
                            checkmark.style.display = isSelected ? 'inline-block' : 'none';
                        }
                        const checkIcon = item.querySelector('.check-icon');
                        if (checkIcon) {
                            checkIcon.classList.toggle('checked', isSelected);
                        }
                    }
                }

                function updateSelectAllState() {
                    const allSelected = listItems.length > 0 && listItems.length === selectedValues.length;
                    selectAllOption.classList.toggle('selected', allSelected);
                    const selectAllCheckmark = selectAllOption.querySelector('.checkmark-icon');
                    if (selectAllCheckmark) {
                        selectAllCheckmark.style.display = allSelected ? 'inline-block' : 'none';
                    }
                    const selectAllCheckIcon = selectAllOption.querySelector('.check-icon');
                    if (selectAllCheckIcon) {
                        selectAllCheckIcon.classList.toggle('checked', allSelected);
                    }
                }

                function selectAll() {
                    const allValues = listItems.map(li => li.getAttribute('data-value'));
                    const allSelected = listItems.length === selectedValues.length;

                    if (allSelected) {
                        selectedValues = [];
                        listItems.forEach(li => {
                            const value = li.getAttribute('data-value');
                            removeChip(value);
                            updateItemState(value);
                        });
                    } else {
                        selectedValues = [...allValues];
                        listItems.forEach(li => {
                            const value = li.getAttribute('data-value');
                            addChip(value);
                            updateItemState(value);
                        });
                    }
                    updateSelectAllState();
                    syncHidden();
                }

                function syncHidden() {
                    hiddenField.value = selectedValues.join(', ');
                    mainInput.style.display = selectedValues.length ? 'none' : 'inline';
                    if (selectedValues.length === 0) {
                        root.classList.add('empty');
                    } else {
                        root.classList.remove('empty');
                    }
                }

                function addNewItem(name) {
                    const cleanName = name.trim();
                    if (!cleanName) {
                        alert('Nama instansi tidak boleh kosong!');
                        if (addNewInput) addNewInput.focus();
                        return;
                    }

                    const existsInList = listItems.some(li => li.getAttribute('data-value').toLowerCase() === cleanName
                        .toLowerCase());
                    const existsInSelected = selectedValues.some(val => val.toLowerCase() === cleanName.toLowerCase());

                    if (existsInList || existsInSelected) {
                        alert('Instansi sudah ada!');
                        if (addNewInput) {
                            addNewInput.value = '';
                            addNewInput.focus();
                        }
                        return;
                    }

                    selectedValues.push(cleanName);
                    addChip(cleanName);
                    updateSelectAllState();
                    syncHidden();
                    hideAddNewInput();
                    filterList('');

                    if (typeof Toastify !== 'undefined') {
                        Toastify({
                            text: `Instansi "${cleanName}" berhasil ditambahkan!`,
                            duration: 2500,
                            gravity: "top",
                            position: "center",
                            style: {
                                background: "#d1fae5",
                                color: "#065f46",
                                borderRadius: "8px",
                                fontSize: "0.9rem"
                            }
                        }).showToast();
                    }
                }

                function filterList(term) {
                    const lower = term.toLowerCase().trim();
                    const addNewInstansiOption = dropdown.querySelector('.add-new-instansi-option');

                    listItems.forEach(li => {
                        const text = li.querySelector('.item-text').textContent.toLowerCase();
                        const match = !lower || text.includes(lower);
                        li.style.display = match ? 'flex' : 'none';
                    });

                    if (!lower || listItems.some(li => {
                            const text = li.querySelector('.item-text').textContent.toLowerCase();
                            return text.includes(lower);
                        })) {
                        selectAllOption.style.display = 'flex';
                    } else {
                        selectAllOption.style.display = 'none';
                    }

                    if (addNewInstansiOption) {
                        if (addNewInputContainer && addNewInputContainer.style.display === 'none') {
                            addNewInstansiOption.style.display = 'flex';
                        } else {
                            addNewInstansiOption.style.display = 'none';
                        }
                    }
                }

                // Event listeners
                searchInput.addEventListener('input', e => filterList(e.target.value));

                const addNewInstansiOption = dropdown.querySelector('.add-new-instansi-option');
                if (addNewInstansiOption) {
                    addNewInstansiOption.addEventListener('click', (e) => {
                        e.stopPropagation();
                        showAddNewInput('');
                        addNewInstansiOption.style.display = 'none';
                        selectAllOption.style.display = 'none';
                    });
                }

                if (addNewInput) {
                    addNewInput.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            if (addConfirmBtn) addConfirmBtn.click();
                        } else if (e.key === 'Escape') {
                            e.preventDefault();
                            hideAddNewInput();
                            selectAllOption.style.display = 'flex';
                            searchInput.focus();
                        }
                    });
                }

                if (addConfirmBtn) {
                    addConfirmBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        if (addNewInput && addNewInput.value.trim()) {
                            addNewItem(addNewInput.value.trim());
                            selectAllOption.style.display = 'flex';
                        }
                    });
                }

                if (addCancelBtn) {
                    addCancelBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        hideAddNewInput();
                        selectAllOption.style.display = 'flex';
                        searchInput.focus();
                    });
                }

                arrow.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggleDropdown();
                });

                container.addEventListener('click', (e) => {
                    if (e.target !== searchInput && !e.target.closest('.chips-selected')) {
                        toggleDropdown();
                    }
                });

                selectAllOption.addEventListener('click', (e) => {
                    e.stopPropagation();
                    selectAll();
                });

                listItems.forEach(li => {
                    li.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const value = li.getAttribute('data-value');
                        toggleItem(value);
                    });
                });

                document.addEventListener('click', e => {
                    if (!root.contains(e.target)) closeDropdown();
                });

                function initializeValues() {
                    const oldValue = hiddenField.value;
                    if (oldValue && oldValue.trim() !== '') {
                        const restoredValues = oldValue.split(',').map(v => v.trim()).filter(Boolean);
                        restoredValues.forEach(value => {
                            if (!selectedValues.includes(value)) {
                                selectedValues.push(value);
                                addChip(value);
                            }
                            updateItemState(value);
                        });
                        updateSelectAllState();
                    }
                    syncHidden();
                }

                initializeValues();
            })();
        </script>
    @endsection
