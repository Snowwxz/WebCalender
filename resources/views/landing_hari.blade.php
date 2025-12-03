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

    <script>
        // Ambil parameter tanggal dari URL (?tanggal=YYYY-MM-DD)
        const urlParams = new URLSearchParams(window.location.search);
        const tanggalParam = urlParams.get('tanggal');

        // Parse tanggal tanpa timezone shift
        function parseLocalDate(dateStr) {
            if (!dateStr) return new Date();
            const [y, m, d] = dateStr.split('-').map(Number);
            return new Date(y, m - 1, d);
        }

        // 🧠 Gunakan param kalau ada, kalau tidak gunakan tanggal hari ini
        let currentDate = parseLocalDate(tanggalParam);

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
        window.changeDay = function(direction) {
            currentDate.setDate(currentDate.getDate() + direction);
            updateDayDisplay();
            renderDayEvents();

            // Update URL pada path yang sama
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

        // Render event publik dari API landing (dengan logika penggabungan seperti dashboard_hari)
        window.renderDayEvents = async function() {
            const dayColumn = document.querySelector('.day-column');
            if (!dayColumn) return;

            const dateString =
                `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(currentDate.getDate()).padStart(2, '0')}`;

            // Bersihkan event lama
            dayColumn.querySelectorAll('.event-item').forEach(e => e.remove());

            try {
                const res = await fetch(`/api/agenda/date/${dateString}`);
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
                function eventsOverlap(e1, e2) {
                    return e1.startMinutes < e2.endMinutes && e1.endMinutes >= e2.startMinutes;
                }

                // Pass 1: Assign kolom untuk setiap event
                const eventLayouts = [];
                events.forEach(event => {
                    const overlappingLayouts = eventLayouts.filter(layout =>
                        eventsOverlap(layout.event, event)
                    );
                    const usedColumns = new Set(overlappingLayouts.map(l => l.column));
                    let assignedColumn = 0;
                    while (usedColumns.has(assignedColumn)) {
                        assignedColumn++;
                    }
                    eventLayouts.push({
                        event,
                        column: assignedColumn,
                        totalColumns: 1
                    });
                });

                // Pass 2: Hitung totalColumns untuk setiap grup overlap
                events.forEach((event, index) => {
                    const overlappingEvents = events.filter(e =>
                        e !== event && eventsOverlap(e, event)
                    );
                    if (overlappingEvents.length > 0) {
                        const allInGroup = [event, ...overlappingEvents];
                        const maxCol = Math.max(...allInGroup.map(oe => {
                            const idx = events.indexOf(oe);
                            return idx >= 0 ? eventLayouts[idx].column : 0;
                        })) + 1;
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
                        const groupEvents = [event, ...overlappingEvents];
                        const groupLayouts = [layout, ...overlappingEvents.map(e => {
                            const idx = events.indexOf(e);
                            return eventLayouts[idx];
                        })];

                        const groupStart = Math.min(...groupEvents.map(e => e.startMinutes));
                        const groupEnd = Math.max(...groupEvents.map(e => e.endMinutes));
                        const groupTop = groupStart * 1;
                        const groupHeight = Math.max((groupEnd - groupStart) * 1, 30);

                        eventGroups.push({
                            events: groupEvents,
                            layouts: groupLayouts,
                            top: groupTop,
                            height: groupHeight,
                            startMinutes: groupStart,
                            endMinutes: groupEnd,
                            isGroup: true
                        });

                        checked.forEach(idx => processedEvents.add(idx));
                    } else {
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
                        const event = group.events[0];
                        const layout = group.layouts[0];
                        const eventEl = document.createElement('div');
                        eventEl.classList.add('event-item');
                        eventEl.classList.add('green');

                        const verticalGap = 4;
                        eventEl.style.top = `${event.top + (verticalGap / 2)}px`;
                        eventEl.style.height = 'auto';
                        eventEl.style.left = '0%';
                        eventEl.style.width = '100%';

                        eventEl.dataset.agendaData = JSON.stringify(event);

                    eventEl.innerHTML = `
                        <div class="event-content">
                            <div class="event-title">${event.agenda_name || 'Agenda'}</div>
                                <div class="event-time">${formatTimeDisplay(event.startTimeStr, event.endTimeStr, event.end_time)}</div>
                        </div>
                    `;

                        eventEl.addEventListener('click', function(e) {
                            e.stopPropagation();
                            let agendaData;
                            try {
                                agendaData = JSON.parse(eventEl.dataset.agendaData || '{}');
                            } catch (err) {
                                console.error('Error parsing agenda data:', err);
                                agendaData = event;
                            }

                        openShowAgendaModal({
                                agenda_name: agendaData.agenda_name,
                                description: agendaData.description,
                                date: dateString,
                                start_time: agendaData.startTimeStr,
                                end_time: agendaData.endTimeStr,
                                location: agendaData.location,
                                unit: agendaData.unit,
                                involved_institution: agendaData.involved_institution,
                                is_public: agendaData.is_public,
                                notes: agendaData.notes
                            });
                        });

                        dayColumn.appendChild(eventEl);
                    } else if (group.isGroup && group.events.length > 1) {
                        // Render sebagai grup (badge dengan jumlah kegiatan)
                        const layout = group.layouts[0];
                        const eventEl = document.createElement('div');
                        eventEl.classList.add('event-item');
                        eventEl.classList.add('green');
                        eventEl.classList.add('event-group');

                        const verticalGap = 4;
                        eventEl.style.top = `${group.top + (verticalGap / 2)}px`;
                        eventEl.style.height = 'auto';
                        eventEl.style.left = '0%';
                        eventEl.style.width = '100%';

                        eventEl.dataset.groupData = JSON.stringify(group.events);

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

                        eventEl.addEventListener('click', function(e) {
                            e.stopPropagation();
                            const dateString = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(currentDate.getDate()).padStart(2, '0')}`;
                            showAgendaListSidebar(group.events, dateString);
                        });

                        dayColumn.appendChild(eventEl);
                    } else {
                        // Fallback: render individual event
                        const event = group.events[0];
                        const layout = group.layouts[0];
                        const eventEl = document.createElement('div');
                        eventEl.classList.add('event-item');
                        eventEl.classList.add('green');

                        const verticalGap = 4;
                        eventEl.style.top = `${event.top + (verticalGap / 2)}px`;
                        eventEl.style.height = 'auto';
                        eventEl.style.left = '0%';
                        eventEl.style.width = '100%';

                        eventEl.dataset.agendaData = JSON.stringify(event);

                        eventEl.innerHTML = `
                            <div class="event-content">
                                <div class="event-title">${event.agenda_name || 'Agenda'}</div>
                                <div class="event-time">${formatTimeDisplay(event.startTimeStr, event.endTimeStr, event.end_time)}</div>
                            </div>
                        `;

                        eventEl.addEventListener('click', function(e) {
                            e.stopPropagation();
                            let agendaData;
                            try {
                                agendaData = JSON.parse(eventEl.dataset.agendaData || '{}');
                            } catch (err) {
                                console.error('Error parsing agenda data:', err);
                                agendaData = event;
                            }

                            openShowAgendaModal({
                                agenda_name: agendaData.agenda_name,
                                description: agendaData.description,
                                date: dateString,
                                start_time: agendaData.startTimeStr,
                                end_time: agendaData.endTimeStr,
                                location: agendaData.location,
                                unit: agendaData.unit,
                                involved_institution: agendaData.involved_institution,
                                is_public: agendaData.is_public,
                                notes: agendaData.notes
                            });
                        });

                        dayColumn.appendChild(eventEl);
                    }
                });
            } catch (e) {
                console.error('Gagal memuat agenda publik:', e);
            }
        }

        // Fungsi untuk menampilkan daftar agenda dalam sidebar (seperti di dashboard_hari)
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
                const timeA = a.start_time || a.end_time || '00:00:00';
                const timeB = b.start_time || b.end_time || '00:00:00';
                return timeA.localeCompare(timeB);
            });

            sortedAgendaList.forEach((item, index) => {
                const itemDiv = document.createElement("div");
                itemDiv.className = "agenda-item";

                const header = document.createElement("div");
                header.className = "agenda-header";

                header.innerHTML = `
                    <span class="agenda-item-title">${item.agenda_name || item.title || 'Agenda'}</span>
                    <span class="agenda-item-arrow"><i class="fas fa-chevron-right"></i></span>
                `;

                itemDiv.appendChild(header);

                itemDiv.addEventListener("click", () => {
                    openShowAgendaModal({
                        agenda_name: item.agenda_name,
                        description: item.description,
                        date: date,
                        start_time: item.start_time || item.startTimeStr,
                        end_time: item.end_time || item.endTimeStr,
                        location: item.location,
                        unit: item.unit,
                        involved_institution: item.involved_institution,
                        is_public: item.is_public,
                        notes: item.notes
                    });
                });

                listContainer.appendChild(itemDiv);
            });

            sidebar.classList.add("active");
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateDayDisplay();
            renderDayEvents();

            // Update ringkasan agenda di sidebar saat pertama kali load
            if (typeof window.updateMiniAgendaForDate === 'function') {
                setTimeout(() => {
                    window.updateMiniAgendaForDate(currentDate);
                }, 100);
            }
        });

        // Optional: handle back/forward so the page reflects the query param if user navigates history
        window.addEventListener('popstate', () => {
            const params = new URLSearchParams(window.location.search);
            const t = params.get('tanggal');
            currentDate = parseLocalDate(t);
            updateDayDisplay();
            renderDayEvents();

            // Update ringkasan agenda
            if (typeof window.updateMiniAgendaForDate === 'function') {
                window.updateMiniAgendaForDate(currentDate);
            }
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

            // Format Dihadiri berdasarkan invitations
            const involvedEl = document.getElementById('showAgendaInvolved');
            if (involvedEl) {
                let involvedText = '-';
                if (data.invitations && data.invitations.length > 0) {
                    const parts = [];
                    data.invitations.forEach((inv, index) => {
                        if (inv.units && inv.units.length > 0) {
                            const unitNames = inv.units.join(', ');
                            if (data.invitations.length > 1) {
                                // Jika ada multiple sessions, gunakan format "Sesi X= opd, opd"
                                parts.push(`Sesi ${index + 1}= ${unitNames}`);
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
@endsection
