    @extends('layouts.main')

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard-hari.css') }}">
        <link rel="stylesheet" href="{{ asset('css/create_agenda_modal.css') }}">
    @endpush

    @section('content')
        @include('tambah_agenda_modal_hari')
        @include('show_agenda_modal_dashboard')
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
                <div id="agendaSidebar" class="agenda-sidebar">
                    <div class="sidebar-header">
                        <h3 id="agendaSidebarDate">Agenda Hari Ini</h3>
                        <button class="close-sidebar"
                            onclick="document.getElementById('agendaSidebar').classList.remove('active')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div id="agendaList" class="agenda-list"></div>
                </div>
            </div>
        </div>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
        <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                function showSuccessToast(message){
                    const node=document.createElement('div');
                    node.innerHTML=`
                        <div style="display:flex; align-items:center; gap:10px;">
                            <span style="display:inline-flex; align-items:center; justify-content:center; width:22px; height:22px; border-radius:50%; background:#82A98D; color:#ffffff; font-size:12px;">
                                <i class="fas fa-check"></i>
                            </span>
                            <span style="font-family:'Poppins',sans-serif; font-weight:600; font-size:15px; color:#234B2C;">
                                ${message}
                            </span>
                        </div>`;
                    Toastify({
                        node,
                        duration:2500,
                        gravity:"top",
                        position:"center",
                        offset:{x:0,y:12},
                        style:{
                            background:'#E9F4EC',
                            border:'1px solid #A6C8A3',
                            borderRadius:'8px',
                            padding:'10px 16px',
                            boxShadow:'0 6px 14px rgba(0,0,0,0.08)',
                            display:'flex',
                            justifyContent:'center',
                            alignItems:'center'
                        }
                    }).showToast();
                }
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

                    // Update ringkasan agenda di sidebar
                    if (typeof window.updateMiniAgendaForDate === 'function') {
                        window.updateMiniAgendaForDate(currentDate);
                    }
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
                    document.body.style.overflow = 'hidden';
                    
                    // Reset to page 1
                    if (typeof showPageHari === 'function') {
                        showPageHari(1);
                    }
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
                        // Normalize waktu: pastikan format HH:mm:ss
                        const normalizeTime = (timeStr) => {
                            if (!timeStr) return null;
                            // Jika sudah format HH:mm:ss, return langsung
                            if (/^\d{2}:\d{2}:\d{2}/.test(timeStr)) return timeStr;
                            // Jika format HH:mm, tambahkan :00
                            if (/^\d{2}:\d{2}$/.test(timeStr)) return timeStr + ':00';
                            return timeStr;
                        };

                        const startTimeStr = normalizeTime(event.start_time) || '08:00:00';
                        let endTimeStr = normalizeTime(event.end_time);

                        // Jika end_time kosong, hitung dari start_time + 1 jam (default durasi)
                        if (!endTimeStr) {
                            const start = new Date(`1970-01-01T${startTimeStr}`);
                            const defaultEnd = new Date(start);
                            defaultEnd.setHours(defaultEnd.getHours() + 1); // Tambah 1 jam
                            const hours = String(defaultEnd.getHours()).padStart(2, '0');
                            const minutes = String(defaultEnd.getMinutes()).padStart(2, '0');
                            endTimeStr = `${hours}:${minutes}:00`;
                        }

                        const start = new Date(`1970-01-01T${startTimeStr}`);
                        const end = new Date(`1970-01-01T${endTimeStr}`);

                        // Pastikan end_time tidak lebih kecil dari start_time
                        if (end < start) {
                            // Jika end_time lebih kecil, set ke start_time + 1 jam
                            const correctedEnd = new Date(start);
                            correctedEnd.setHours(correctedEnd.getHours() + 1);
                            const hours = String(correctedEnd.getHours()).padStart(2, '0');
                            const minutes = String(correctedEnd.getMinutes()).padStart(2, '0');
                            endTimeStr = `${hours}:${minutes}:00`;
                            end.setTime(correctedEnd.getTime());
                        }

                        let duration = (end - start) / (1000 * 60);
                        if (!isFinite(duration) || duration <= 0) duration = 60; // Default 1 jam jika tidak valid

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

                    // Fungsi helper untuk format waktu tampilan
                    function formatTimeDisplay(startStr, endStr, originalEndTime) {
                        if (!startStr) return '-';
                        const start = startStr.slice(0, 5);
                        // Jika original end_time tidak ada atau kosong, tampilkan hanya start_time
                        if (!originalEndTime || !endStr) {
                            return start;
                        }
                        // Pastikan end_time valid (tidak lebih kecil dari start_time)
                        try {
                            const startDate = new Date(`1970-01-01T${startStr}`);
                            const endDate = new Date(`1970-01-01T${endStr}`);
                            if (endDate <= startDate) {
                                return start; // Tampilkan hanya start_time jika end_time tidak valid
                            }
                            return `${start} - ${endStr.slice(0, 5)}`;
                        } catch (e) {
                            return start; // Jika parsing error, tampilkan hanya start_time
                        }
                    }

                    // Fungsi untuk cek apakah dua event overlap atau menyentuh
                    // Event dianggap overlap jika mereka saling menyentuh di waktu yang sama
                    // ATAU jika jamnya sama (start_time sama atau sangat dekat)
                    function eventsOverlap(e1, e2) {
                        // Toleransi untuk dianggap "sama waktu" (dalam menit)
                        const timeTolerance = 60; // 1 jam toleransi
                        
                        // Cek apakah waktu mulai sama atau sangat dekat (untuk menangani agenda dengan jam yang sama)
                        const startDiff = Math.abs(e1.startMinutes - e2.startMinutes);
                        if (startDiff <= timeTolerance) {
                            return true;
                        }
                        
                        // Cek apakah waktu akhir sama atau sangat dekat
                        const endDiff = Math.abs(e1.endMinutes - e2.endMinutes);
                        if (endDiff <= timeTolerance) {
                            return true;
                        }
                        
                        // Event overlap jika:
                        // - e1 dimulai sebelum e2 berakhir DAN
                        // - e1 berakhir setelah e2 dimulai
                        // Menggunakan >= untuk menangani kasus event yang berakhir tepat saat event lain dimulai
                        return e1.startMinutes < e2.endMinutes && e1.endMinutes >= e2.startMinutes;
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
                        // Jika hanya 1 agenda, selalu render sebagai individual dan langsung buka modal saat klik
                        if (group.events.length === 1) {
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
                            eventEl.style.height = 'auto';
                            eventEl.style.left = '0%';
                            eventEl.style.width = '100%';

                            // Simpan ID agenda dan data lengkap untuk modal
                            eventEl.dataset.agendaId = event.id_agenda || event.id || null;
                            eventEl.dataset.agendaData = JSON.stringify(event);

                            eventEl.innerHTML = `
                                <div class="event-content">
                                    <div class="event-title">${event.title || event.agenda_name || 'Agenda'}</div>
                                    <div class="event-time">${formatTimeDisplay(event.startTimeStr, event.endTimeStr, event.end_time)}</div>
                                </div>
                            `;

                            // Tambahkan event listener untuk click - langsung buka modal
                            eventEl.addEventListener('click', function(e) {
                                e.stopPropagation();
                                const agendaId = eventEl.dataset.agendaId;
                                let agendaData;
                                try {
                                    agendaData = JSON.parse(eventEl.dataset.agendaData || '{}');
                                } catch (err) {
                                    console.error('Error parsing agenda data:', err);
                                    agendaData = event; // fallback ke event object langsung
                                }

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
                        } else if (group.isGroup && group.events.length > 1) {
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

                            // Hitung posisi dan ukuran berdasarkan durasi waktu
                            // Pastikan tinggi mencakup seluruh durasi termasuk sampai akhir jam terakhir
                            const durationMinutes = group.endMinutes - group.startMinutes;
                            // Cek apakah endMinutes adalah awal jam (menit = 0)
                            // Jika ya, tambahkan 60 menit untuk mencakup seluruh slot jam terakhir
                            const endMinute = group.endMinutes % 60;
                            const extraHeight = (endMinute === 0) ? 60 : 0; // Tambah 60px jika end adalah awal jam
                            const calculatedHeight = Math.max(durationMinutes + extraHeight, 30); // Minimal 30px
                            eventEl.style.top = `${group.top}px`; // Gunakan top tanpa gap untuk akurasi
                            eventEl.style.height = `${calculatedHeight}px`; // Tinggi penuh sesuai durasi + slot jam terakhir jika perlu
                            eventEl.style.left = '0%';
                            eventEl.style.width = '100%';
                            eventEl.style.minHeight = '30px'; // Pastikan minimal tinggi
                            eventEl.style.boxSizing = 'border-box'; // Pastikan padding/border tidak menambah tinggi
                            eventEl.style.position = 'absolute'; // Pastikan posisi absolute untuk akurasi

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

                            // Pastikan end_time valid (tidak lebih kecil dari start_time)
                            const timeDisplay = (group.endMinutes > group.startMinutes)
                                ? `${startTimeStr} - ${endTimeStr}`
                                : startTimeStr;

                            eventEl.innerHTML = `
                                <div class="event-content">
                                    <div class="event-title">${group.events.length} Kegiatan</div>
                                    <div class="event-time">${timeDisplay}</div>
                                </div>
                            `;

                            // Event listener untuk click - tampilkan semua agenda dalam grup
                            eventEl.addEventListener('click', function(e) {
                                e.stopPropagation();
                                // Format tanggal untuk sidebar
                                const year = currentDate.getFullYear();
                                const month = String(currentDate.getMonth() + 1).padStart(2, '0');
                                const day = String(currentDate.getDate()).padStart(2, '0');
                                const dateString = `${year}-${month}-${day}`;
                                // Buka sidebar dengan semua agenda dalam grup
                                showAgendaListSidebar(group.events, dateString);
                            });

                            dayColumn.appendChild(eventEl);
                        } else {
                            // Fallback: render individual event (untuk kasus edge case)
                            const event = group.events[0];
                            const layout = group.layouts[0];
                            const eventEl = document.createElement('div');
                            eventEl.classList.add('event-item');
                            const isPublic = event.is_public == 1 || event.is_public === true;
                            eventEl.classList.add(isPublic ? 'green' : 'orange');

                            // Hitung lebar dan posisi kiri
                            const verticalGap = 4;
                            eventEl.style.top = `${event.top + (verticalGap / 2)}px`;
                            eventEl.style.height = 'auto';
                            eventEl.style.left = '0%';
                            eventEl.style.width = '100%';

                            // Simpan ID agenda dan data lengkap untuk modal
                            eventEl.dataset.agendaId = event.id_agenda || event.id || null;
                            eventEl.dataset.agendaData = JSON.stringify(event);

                            eventEl.innerHTML = `
                                <div class="event-content">
                                    <div class="event-title">${event.title || event.agenda_name || 'Agenda'}</div>
                                    <div class="event-time">${formatTimeDisplay(event.startTimeStr, event.endTimeStr, event.end_time)}</div>
                                </div>
                            `;

                            // Tambahkan event listener untuk click - langsung buka modal
                            eventEl.addEventListener('click', function(e) {
                                e.stopPropagation();
                                const agendaId = eventEl.dataset.agendaId;
                                let agendaData;
                                try {
                                    agendaData = JSON.parse(eventEl.dataset.agendaData || '{}');
                                } catch (err) {
                                    console.error('Error parsing agenda data:', err);
                                    agendaData = event; // fallback ke event object langsung
                                }

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
                    // Semua agenda diperlakukan sama - tidak ada pembedaan eksternal/lokal

                    // Gunakan fungsi global fillAgendaModal jika tersedia
                    if (typeof window.fillAgendaModal === 'function') {
                        window.fillAgendaModal(data);
                    } else {
                        // Fallback: isi manual jika fungsi global belum tersedia
                        // Format tanggal
                        function formatDate(dateString) {
                            if (!dateString) return "-";
                            const date = new Date(dateString);
                            const options = {
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric'
                            };
                            return date.toLocaleDateString('id-ID', options);
                        }

                        const nameEl = document.getElementById('showAgendaName');
                        if (nameEl) {
                            nameEl.innerHTML = (data.agenda_name ?? '-');
                        }

                        const dateEl = document.getElementById('showAgendaDate');
                        if (dateEl) dateEl.innerText = formatDate(data.date);

                        const timeText = (data.start_time && data.end_time) ?
                            `${data.start_time.slice(0, 5)} - ${data.end_time.slice(0, 5)}` :
                            (data.end_time ? data.end_time.slice(0, 5) : (data.start_time ? data.start_time.slice(0, 5) : '-'));
                        const timeEl = document.getElementById('showAgendaTime');
                        if (timeEl) timeEl.innerText = timeText;

                        const locationEl = document.getElementById('showAgendaLocation');
                        if (locationEl) locationEl.innerText = data.location ?? '-';

                        const descEl = document.getElementById('showAgendaDesc');
                        if (descEl) descEl.innerText = data.description ?? '-';

                        // Isi data instansi
                        const unitName = (data.unit && data.unit.unit_name) ? data.unit.unit_name : '-';

                        const unitEl = document.getElementById('showAgendaUnit');
                        if (unitEl) unitEl.innerText = unitName;

                        // Format Dihadiri berdasarkan invitations
                        const involvedEl = document.getElementById('showAgendaInvolved');
                        if (involvedEl) {
                            let involvedText = '-';
                            if (data.invitations && data.invitations.length > 0) {
                                const parts = [];
                                data.invitations.forEach((inv, index) => {
                                    if (inv.units && inv.units.length > 0) {
                                        const unitNames = inv.units.join(', ');
                                        // Gunakan session_name jika ada, jika tidak gunakan "Sesi X"
                                        const sessionLabel = inv.session_name || `Sesi ${index + 1}`;
                                        if (data.invitations.length > 1) {
                                            // Jika ada multiple sessions, gunakan format "Nama Sesi= opd, opd"
                                            parts.push(`${sessionLabel}= ${unitNames}`);
                                        } else {
                                            // Jika hanya satu session (normal), tampilkan langsung
                                            parts.push(unitNames);
                                        }
                                    }
                                });
                                if (parts.length > 0) {
                                    involvedText = parts.join('<br>');
                                }
                            } else if (data.involved_institution) {
                                involvedText = data.involved_institution;
                            }
                            involvedEl.innerHTML = involvedText;
                        }

                        // Isi status akses (publik/privasi)
                        const accessEl = document.getElementById('showAgendaAccess');
                        if (accessEl) {
                            const isPublic = data.is_public == 1;
                            const bg = isPublic ? '#A8E6A3' : '#FFB67E';
                            const text = isPublic ? 'Publik' : 'Privasi';
                            accessEl.innerHTML =
                                `<span class="badge rounded-pill" style="background-color:${bg}; color:#2F3E35; padding:6px 10px;">${text}</span>`;
                        }

                        const notesEl = document.getElementById('showAgendaNotes');
                        if (notesEl) notesEl.innerText = data.notes ?? '-';
                    }

                    // Tampilkan modal
                    new bootstrap.Modal(document.getElementById('showAgendaModal')).show();
                }

                // Fungsi untuk menampilkan daftar agenda dalam sidebar (seperti di dashboard_bulan)
                function showAgendaListSidebar(agendaList, date) {
                    const sidebar = document.getElementById("agendaSidebar");
                    const listContainer = document.getElementById("agendaList");
                    const title = document.getElementById("agendaSidebarDate");

                    title.textContent = `Agenda ${date}`;
                    listContainer.innerHTML = "";

                    if (agendaList.length === 0) {
                        listContainer.innerHTML = "<p>Tidak ada agenda untuk hari ini.</p>";
                        return;
                    }

                    // Urutkan agenda berdasarkan jam (start_time)
                    const sortedAgendaList = [...agendaList].sort((a, b) => {
                        // Ambil start_time, jika tidak ada gunakan end_time, jika tidak ada gunakan '00:00:00'
                        const timeA = a.start_time || a.end_time || '00:00:00';
                        const timeB = b.start_time || b.end_time || '00:00:00';

                        // Bandingkan waktu
                        return timeA.localeCompare(timeB);
                    });

                    sortedAgendaList.forEach((item, index) => {
                        const itemDiv = document.createElement("div");
                        itemDiv.className = "agenda-item";

                        const header = document.createElement("div");
                        header.className = "agenda-header";

                        // Semua agenda diperlakukan sama - tidak ada pembedaan eksternal/lokal
                        const externalBadge = "";

                        header.innerHTML = `
                            <span class="agenda-item-title">${item.agenda_name || item.title || 'Agenda'}${externalBadge}</span>
                            <span class="agenda-item-arrow"><i class="fas fa-chevron-right"></i></span>
                        `;

                        itemDiv.appendChild(header);

                        itemDiv.addEventListener("click", () => {
                            openShowAgendaModal(item);
                        });

                        listContainer.appendChild(itemDiv);
                    });

                    sidebar.classList.add("active");
                }


                // 🟢 Pastikan render setelah semua siap
                updateDayDisplay();
                renderDayEvents();

                // Update ringkasan agenda di sidebar saat pertama kali load
                if (typeof window.updateMiniAgendaForDate === 'function') {
                    // Tunggu sedikit agar komponen mini calendar sudah ter-load
                    setTimeout(() => {
                        window.updateMiniAgendaForDate(currentDate);
                    }, 100);
                }
            });

            // ==== FUNGSI BUKA & TUTUP MODAL ====
            function openModal() {
                const modal = document.getElementById('createAgendaModalHari');
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                
                // Reset to page 1
                if (typeof showPageHari === 'function') {
                    showPageHari(1);
                }
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

                // Reset to page 1
                if (typeof showPageHari === 'function') {
                    showPageHari(1);
                }

                // Clear sessions
                const sessionsContainer = document.getElementById('sessionsContainerHari');
                if (sessionsContainer) {
                    sessionsContainer.innerHTML = '';
                }

                // Reset normal invitation chips
                const normalRoot = document.getElementById('normalInvolvedInstansiHari');
                if (normalRoot) {
                    const selectedWrap = normalRoot.querySelector('.chips-selected');
                    const mainInput = normalRoot.querySelector('.chips-input');
                    const dropdown = normalRoot.querySelector('.chips-dropdown');

                    if (selectedWrap) {
                        selectedWrap.innerHTML = '';
                    }
                    if (mainInput) {
                        mainInput.style.display = 'inline';
                    }
                    if (dropdown) {
                        dropdown.classList.remove('open');
                    }
                    normalRoot.classList.remove('open');
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

            // Multi-page form navigation untuk modal hari
            let currentPageHari = 1;
            const totalPagesHari = 2;

            function showPageHari(page) {
                document.querySelectorAll('#createAgendaModalHari .form-page').forEach((p, idx) => {
                    p.style.display = idx + 1 === page ? 'block' : 'none';
                });

                // Update navigation buttons
                const prevBtn = document.getElementById('prevPageBtnHari');
                const nextBtn = document.getElementById('nextPageBtnHari');
                const submitBtn = document.getElementById('submitBtnHari');

                if (prevBtn) prevBtn.style.display = page > 1 ? 'block' : 'none';
                if (nextBtn) nextBtn.style.display = page < totalPagesHari ? 'block' : 'none';
                if (submitBtn) submitBtn.style.display = page === totalPagesHari ? 'block' : 'none';

                currentPageHari = page;
            }

            // Initialize page navigation
            document.addEventListener('DOMContentLoaded', function() {
                const nextBtn = document.getElementById('nextPageBtnHari');
                const prevBtn = document.getElementById('prevPageBtnHari');
                const submitBtn = document.getElementById('submitBtnHari');

                if (nextBtn) {
                    nextBtn.addEventListener('click', function() {
                        if (validatePage1Hari()) {
                            showPageHari(2);
                        }
                    });
                }

                if (prevBtn) {
                    prevBtn.addEventListener('click', function() {
                        showPageHari(1);
                    });
                }

                if (submitBtn) {
                    submitBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (!validatePage1Hari()) {
                            showPageHari(1);
                            return false;
                        }
                        collectSessionDataHari();
                        const form = document.getElementById('createAgendaFormHari');
                        if (form) {
                            const formEvent = new Event('submit', { bubbles: true, cancelable: true });
                            form.dispatchEvent(formEvent);
                        }
                    });
                }
            });

            function validatePage1Hari() {
                const requiredFields = ['agenda_name', 'description', 'id_unit', 'date', 'start_time', 'end_time'];
                let isValid = true;
                let emptyFields = [];

                requiredFields.forEach(fieldName => {
                    const field = document.querySelector(`#createAgendaModalHari [name="${fieldName}"]`);
                    if (field && (!field.value || field.value.trim() === '')) {
                        isValid = false;
                        emptyFields.push(fieldName);
                        field.style.borderColor = '#ef4444';
                        field.style.backgroundColor = '#fef2f2';
                    } else if (field) {
                        field.style.borderColor = '';
                        field.style.backgroundColor = '';
                    }
                });

                if (!isValid) {
                    showPageHari(1);
                    Toastify({
                        text: 'Mohon lengkapi semua field yang wajib diisi!',
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#f44336",
                        stopOnFocus: true
                    }).showToast();
                }

                return isValid;
            }

            function collectSessionDataHari() {
                const hasGroup = document.querySelector('#createAgendaModalHari input[name="has_group"]:checked')?.value === '1';
                const sessions = [];

                if (hasGroup) {
                    // Collect from group sessions
                    document.querySelectorAll('#createAgendaModalHari .session-item').forEach((sessionEl, index) => {
                        const sessionName = sessionEl.querySelector('.session-name-input')?.value || null;
                        const unitIds = [];
                        sessionEl.querySelectorAll('.unit-chip[data-unit-id]').forEach(chip => {
                            unitIds.push(chip.getAttribute('data-unit-id'));
                        });

                        if (unitIds.length > 0) {
                            sessions.push({
                                session_name: sessionName || `Sesi ${index + 1}`,
                                invited_units: unitIds
                            });
                        }
                    });
                } else {
                    // Collect from normal invitation
                    const unitIds = [];
                    document.querySelectorAll('#createAgendaModalHari #normalInvolvedInstansiHari .chip[data-unit-id]').forEach(chip => {
                        unitIds.push(chip.getAttribute('data-unit-id'));
                    });

                    if (unitIds.length > 0) {
                        sessions.push({
                            session_name: null,
                            invited_units: unitIds
                        });
                    }
                }

                // Add hidden input for sessions
                const existingInput = document.querySelector('#createAgendaModalHari input[name="sessions_data"]');
                if (existingInput) {
                    existingInput.remove();
                }

                const sessionsInput = document.createElement('input');
                sessionsInput.type = 'hidden';
                sessionsInput.name = 'sessions_data';
                sessionsInput.value = JSON.stringify(sessions);
                document.getElementById('createAgendaFormHari').appendChild(sessionsInput);
            }

            // Toggle Group functionality
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('#createAgendaModalHari input[name="has_group"]').forEach(radio => {
                    radio.addEventListener('change', function() {
                        const hasGroup = this.value === '1';
                        const normalInvitation = document.getElementById('normalInvitationHari');
                        const groupInvitation = document.getElementById('groupInvitationHari');
                        const addSessionBtn = document.getElementById('addSessionBtnHari');

                        if (normalInvitation) normalInvitation.style.display = hasGroup ? 'none' : 'block';
                        if (groupInvitation) groupInvitation.style.display = hasGroup ? 'block' : 'none';
                        if (addSessionBtn) addSessionBtn.style.display = hasGroup ? 'block' : 'none';
                        
                        if (hasGroup && document.querySelectorAll('#createAgendaModalHari .session-item').length === 0) {
                            addSessionHari(1);
                            addSessionHari(2);
                        }
                    });
                });

                // Add session functionality
                const addSessionBtn = document.getElementById('addSessionBtnHari');
                if (addSessionBtn) {
                    let sessionCount = 0;
                    addSessionBtn.addEventListener('click', function() {
                        sessionCount++;
                        addSessionHari(sessionCount + 2);
                    });
                }
            });

            function addSessionHari(sessionNumber) {
                const container = document.getElementById('sessionsContainerHari');
                if (!container) return;

                const sessionDiv = document.createElement('div');
                sessionDiv.className = 'session-item';
                sessionDiv.innerHTML = `
                    <div class="input-group fullwidth-group" style="margin-bottom: 16px; border: 1px solid #e5e7eb; padding: 16px; border-radius: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <label style="margin: 0;"><i class="fas fa-layer-group"></i> Dihadiri Sesi ${sessionNumber}</label>
                            ${sessionNumber > 2 ? '<button type="button" class="remove-session-btn" style="background: #fee2e2; color: #991b1b; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer;"><i class="fas fa-times"></i></button>' : ''}
                        </div>
                        <input type="text" class="session-name-input" placeholder="Nama Sesi (opsional)" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; margin-bottom: 12px;">
                        <div class="chips-multiselect session-units-select" data-session="${sessionNumber}">
                            <div class="chips-container">
                                <div class="chips-selected"></div>
                                <input type="text" class="chips-input" placeholder="-- Pilih Instansi yang Hadir --" readonly style="cursor: pointer;">
                            </div>
                            <span class="chips-arrow"><i class="fas fa-chevron-down"></i></span>
                            <div class="chips-dropdown">
                                <div class="chips-search">
                                    <input type="text" class="chips-search-input" placeholder="Cari instansi..." />
                                </div>
                                <ul>
                                    <li class="select-all-option" data-action="select-all">
                                        <span class="check-icon"></span>
                                        <span class="item-text">Pilih Semua</span>
                                        <i class="fas fa-check checkmark-icon"></i>
                                    </li>
                                    @foreach ($units as $unit)
                                        @if ($unit->id_unit !== Auth::user()->id_unit && strtolower($unit->unit_name) !== 'protokol')
                                            <li data-value="{{ $unit->id_unit }}" data-name="{{ $unit->unit_name }}" class="dropdown-item">
                                                <span class="check-icon"></span>
                                                <span class="item-text">{{ $unit->unit_name }}</span>
                                                <i class="fas fa-check checkmark-icon"></i>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                `;
                container.appendChild(sessionDiv);
                
                // Initialize chips for this session
                initChipsMultiselectHari(sessionDiv.querySelector('.session-units-select'));
                
                // Remove session button
                const removeBtn = sessionDiv.querySelector('.remove-session-btn');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        sessionDiv.remove();
                    });
                }
            }

            // Initialize chips multiselect for normal invitation
            function initChipsMultiselectHari(root) {
                if (!root) return;
                
                const dropdown = root.querySelector('.chips-dropdown');
                const arrow = root.querySelector('.chips-arrow');
                const searchInput = root.querySelector('.chips-search-input');
                const mainInput = root.querySelector('.chips-input');
                const selectedWrap = root.querySelector('.chips-selected');
                const allListItems = Array.from(dropdown.querySelectorAll('li.dropdown-item'));
                const listItems = allListItems.filter(li => !li.classList.contains('add-new-instansi-option'));
                const selectAllOption = dropdown.querySelector('.select-all-option');
                const container = root.querySelector('.chips-container');

                let selectedValues = [];
                let selectedUnitIds = [];

                function toggleDropdown() {
                    const isOpen = dropdown.classList.toggle('open');
                    root.classList.toggle('open', isOpen);
                    if (isOpen) {
                        searchInput.value = '';
                        filterList('');
                        searchInput.focus();
                        listItems.forEach(li => {
                            li.style.display = 'flex';
                            const value = li.getAttribute('data-value');
                            updateItemState(value);
                        });
                        selectAllOption.style.display = 'flex';
                        updateSelectAllState();
                    }
                }

                function closeDropdown() {
                    dropdown.classList.remove('open');
                    root.classList.remove('open');
                    searchInput.value = '';
                }

                function toggleItem(unitId, unitName) {
                    const index = selectedUnitIds.indexOf(unitId);
                    if (index > -1) {
                        selectedUnitIds.splice(index, 1);
                        selectedValues.splice(index, 1);
                        removeChip(unitId);
                    } else {
                        selectedUnitIds.push(unitId);
                        selectedValues.push(unitName);
                        addChip(unitId, unitName);
                    }
                    updateItemState(unitId);
                    updateSelectAllState();
                }

                function addChip(unitId, unitName) {
                    const existingChip = selectedWrap.querySelector(`.unit-chip[data-unit-id="${unitId}"]`);
                    if (existingChip) return;

                    const chip = document.createElement('span');
                    chip.className = 'chip unit-chip';
                    chip.setAttribute('data-unit-id', unitId);
                    chip.setAttribute('data-value', unitName);
                    chip.textContent = unitName;

                    const btn = document.createElement('button');
                    btn.className = 'chip-remove';
                    btn.innerHTML = '&times;';
                    btn.onclick = (e) => {
                        e.stopPropagation();
                        toggleItem(unitId, unitName);
                    };

                    chip.appendChild(btn);
                    selectedWrap.appendChild(chip);
                    mainInput.style.display = selectedUnitIds.length ? 'none' : 'inline';
                }

                function removeChip(unitId) {
                    const chip = selectedWrap.querySelector(`.unit-chip[data-unit-id="${unitId}"]`);
                    if (chip) {
                        chip.remove();
                    }
                    mainInput.style.display = selectedUnitIds.length ? 'none' : 'inline';
                }

                function updateItemState(unitId) {
                    const item = listItems.find(li => li.getAttribute('data-value') === unitId);
                    if (item) {
                        const isSelected = selectedUnitIds.includes(unitId);
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
                    const allSelected = listItems.length > 0 && listItems.length === selectedUnitIds.length;
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
                    const allSelected = listItems.length === selectedUnitIds.length;
                    if (allSelected) {
                        selectedUnitIds = [];
                        selectedValues = [];
                        listItems.forEach(li => {
                            const unitId = li.getAttribute('data-value');
                            removeChip(unitId);
                            updateItemState(unitId);
                        });
                    } else {
                        listItems.forEach(li => {
                            const unitId = li.getAttribute('data-value');
                            const unitName = li.getAttribute('data-name');
                            if (!selectedUnitIds.includes(unitId)) {
                                selectedUnitIds.push(unitId);
                                selectedValues.push(unitName);
                                addChip(unitId, unitName);
                                updateItemState(unitId);
                            }
                        });
                    }
                    updateSelectAllState();
                }

                function filterList(term) {
                    const lower = term.toLowerCase().trim();
                    listItems.forEach(li => {
                        const text = li.querySelector('.item-text').textContent.toLowerCase();
                        li.style.display = !lower || text.includes(lower) ? 'flex' : 'none';
                    });
                    selectAllOption.style.display = !lower || listItems.some(li => {
                        const text = li.querySelector('.item-text').textContent.toLowerCase();
                        return text.includes(lower);
                    }) ? 'flex' : 'none';
                }

                searchInput.addEventListener('input', e => filterList(e.target.value));
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
                        const unitId = li.getAttribute('data-value');
                        const unitName = li.getAttribute('data-name');
                        toggleItem(unitId, unitName);
                    });
                });
                document.addEventListener('click', e => {
                    if (!root.contains(e.target)) closeDropdown();
                });
            }

            // Initialize normal invitation chips
            document.addEventListener('DOMContentLoaded', function() {
                const normalRoot = document.getElementById('normalInvolvedInstansiHari');
                if (normalRoot) {
                    initChipsMultiselectHari(normalRoot);
                }
            });

            // Update handleFormSubmit untuk collect session data
            const originalHandleFormSubmit = handleFormSubmit;
            handleFormSubmit = function(e) {
                e.preventDefault();
                if (currentPageHari === 1) {
                    if (!validatePage1Hari()) {
                        showPageHari(1);
                        return false;
                    }
                    showPageHari(2);
                    return false;
                }
                // If on page 2, collect session data and submit
                collectSessionDataHari();
                return originalHandleFormSubmit.call(this, e);
            };


            // ==== HANDLE FORM SUBMIT ====
            function handleFormSubmit(e) {
                e.preventDefault();

                const form = e.target;

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
                            // Reset form
                            form.reset();

                            // Reload events
                            renderDayEvents();

                            showSuccessToast(data.message || "Agenda berhasil ditambahkan!");
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
            // ✅ INI DITARO DI LUAR
            const showAgendaModalEl = document.getElementById('showAgendaModal');
            if (showAgendaModalEl) {
                showAgendaModalEl.addEventListener('show.bs.modal', function() {
                    const agendaSidebar = document.getElementById('agendaSidebar');
                    if (agendaSidebar) {
                        agendaSidebar.classList.add('hidden');
                    }
                });

                showAgendaModalEl.addEventListener('hidden.bs.modal', function() {
                    const agendaSidebar = document.getElementById('agendaSidebar');
                    if (agendaSidebar && agendaSidebar.classList.contains('active')) {
                        agendaSidebar.classList.remove('hidden');
                    }
                });
            }
        </script>

        <style>
            /* Multi-page form styles untuk modal hari */
            #createAgendaModalHari .form-page {
                animation: fadeIn 0.3s ease;
            }

            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }

            #createAgendaModalHari .form-navigation {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-top: 32px;
                padding-top: 24px;
                border-top: 2px solid #e5e7eb;
            }

            #createAgendaModalHari .radio-option {
                transition: all 0.3s;
            }

            #createAgendaModalHari .radio-option:hover {
                border-color: #6b8f71 !important;
                background: #f0fdf4;
            }

            #createAgendaModalHari .radio-option input[type="radio"]:checked ~ span {
                color: #6b8f71;
                font-weight: 600;
            }

            #createAgendaModalHari .radio-option:has(input[type="radio"]:checked) {
                border-color: #6b8f71 !important;
                background: #f0fdf4;
            }

            #createAgendaModalHari .session-item {
                margin-bottom: 16px;
            }

            #createAgendaModalHari .btn-secondary {
                background: #e5e7eb;
                color: #374151;
                border: none;
                padding: 10px 20px;
                border-radius: 8px;
                cursor: pointer;
                font-weight: 500;
                transition: all 0.3s;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            #createAgendaModalHari .btn-secondary:hover {
                background: #d1d5db;
            }

            #createAgendaModalHari .invitation-container {
                margin-top: 16px;
            }

            #createAgendaModalHari .btn-primary {
                background: #6b8f71;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 8px;
                cursor: pointer;
                font-weight: 500;
                transition: all 0.3s;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            #createAgendaModalHari .btn-primary:hover {
                background: #5a7a5f;
            }
        </style>
    @endsection
