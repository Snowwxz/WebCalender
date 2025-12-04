@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-bulan.css') }}">
@endpush

@section('content')
    @include('show_agenda_modal_landing')
    <div class="calendar-page">
        <div class="calendar-content-wrapper">
            <!-- Mini Calendar di Kiri -->
            <div class="calendar-left-sidebar">
                @include('components.mini-calendar-categories')
            </div>
            <!-- Main Calendar -->
            <div class="calendar-main">
                <div class="calendar-header">
                    <div class="month-navigation">
                        <button class="nav-btn" onclick="changeMonth(-1)">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <h2 class="month-year" id="currentMonthYear">Oktober 2025</h2>
                        <button class="nav-btn" onclick="changeMonth(1)">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <div class="calendar-grid">
                    <div class="calendar-weekdays">
                        <div>Sen</div>
                        <div>Sel</div>
                        <div>Rab</div>
                        <div>Kam</div>
                        <div>Jum</div>
                        <div>Sab</div>
                        <div class="weekend">Min</div>
                    </div>
                    <div class="calendar-days" id="calendarDays">
                        <!-- Tanggal akan di-generate lewat JavaScript -->
                    </div>
                </div>
                <div id="agendaSidebar" class="agenda-sidebar">
                    <div class="sidebar-header">
                        <h3 id="agendaSidebarDate">Agenda Hari Ini</h3>
                        <div class="sidebar-actions">
                            <button class="copy-sidebar" id="copySidebarAgendaBtn" aria-label="Salin agenda hari ini" title="Salin agenda hari ini">
                                <i class="fas fa-copy"></i>
                                <span class="label">Salin</span>
                            </button>
                            <button class="close-sidebar"
                                onclick="document.getElementById('agendaSidebar').classList.remove('active')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div id="agendaList" class="agenda-list"></div>
                </div>
            </div>
        </div>

        <script>
            const urlParams = new URLSearchParams(window.location.search);
            const bulanParam = urlParams.get('bulan'); // 0-11 (from year view)
            const monthParam = urlParams.get('month'); // 1-12 (direct)

            // Handle 0 correctly for Januari when using `bulan=0`.
            let selectedMonth = bulanParam !== null ?
                (parseInt(bulanParam, 10) + 1) :
                (monthParam !== null ? parseInt(monthParam, 10) : (new Date().getMonth() + 1));

            let selectedYear = (urlParams.get('tahun') ?? urlParams.get('year')) ?
                parseInt(urlParams.get('tahun') ?? urlParams.get('year'), 10) :
                new Date().getFullYear();

            let currentDate = new Date(selectedYear, selectedMonth - 1, 1);
            let agendaData = [];

            async function fetchAgenda(year, month) {
                try {
                    const response = await fetch(`/api/agenda/${year}/${month}`);
                    if (!response.ok) throw new Error('Gagal memuat agenda.');
                    const data = await response.json();
                    agendaData = data;
                } catch (error) {
                    console.error('Fetch agenda gagal:', error);
                    agendaData = [];
                } finally {
                    generateMainCalendar();
                }
            }

            function getAgendaForDate(dateObj) {
                const year = dateObj.getFullYear();
                const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                const day = String(dateObj.getDate()).padStart(2, '0');
                const dateStr = `${year}-${month}-${day}`;

                return agendaData.filter(a => {
                    // Ambil hanya bagian tanggal, buang jam dan zona
                    const agendaDateStr = a.date ? a.date.split('T')[0] : '';
                    // Semua agenda diperlakukan sama - cek is_public
                    const isPublic = Number(a.is_public) === 1;
                    return agendaDateStr === dateStr && a.status === 'approved' && isPublic;
                });
            }


            function generateMainCalendar() {
                const monthYear = document.getElementById('currentMonthYear');
                const calendarDays = document.getElementById('calendarDays');

                const monthNames = [
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];

                monthYear.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;

                const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
                const startDate = new Date(firstDay);
                startDate.setDate(startDate.getDate() - (firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1));

                calendarDays.innerHTML = '';

                for (let i = 0; i < 35; i++) {
                    const date = new Date(startDate);
                    date.setDate(startDate.getDate() + i);

                    const dayElement = document.createElement('div');
                    dayElement.className = 'calendar-day';

                    if (date.getMonth() !== currentDate.getMonth()) {
                        dayElement.classList.add('other-month');
                    }

                    if (date.getDay() === 0) dayElement.classList.add('weekend');
                    else if (date.getDay() === 6) dayElement.classList.add('saturday');

                    if (date.toDateString() === new Date().toDateString()) {
                        dayElement.classList.add('today');
                    }

                    const dayNumber = document.createElement('div');
                    dayNumber.className = 'day-number';
                    dayNumber.textContent = date.getDate();

                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');

                    const filteredAgenda = getAgendaForDate(date);
                    dayElement.appendChild(dayNumber);

                    if (filteredAgenda.length > 0) {
                        const agendaContainer = document.createElement("div");
                        agendaContainer.className = "agenda-container";

                        const badge = document.createElement("div");
                        badge.className = "agenda-count-badge bg-green-500";
                        badge.textContent = filteredAgenda.length > 1 ?
                            `${filteredAgenda.length} Kegiatan` :
                            filteredAgenda[0].agenda_name;

                        badge.addEventListener('click', (e) => {
                            e.stopPropagation();
                            handleAgendaClick(filteredAgenda, `${year}-${month}-${day}`);
                        });

                        agendaContainer.appendChild(badge);
                        dayElement.appendChild(agendaContainer);
                    }

                    dayNumber.addEventListener('click', (e) => {
                        e.stopPropagation();
                        window.location.href = `/hari?tanggal=${year}-${month}-${day}`;
                    });

                    calendarDays.appendChild(dayElement);
                }
            }

        function changeMonth(direction) {
            currentDate.setMonth(currentDate.getMonth() + direction);
            const newMonth = currentDate.getMonth() + 1;
            const newYear = currentDate.getFullYear();

            // Optimistic UI: clear old agenda to avoid stale badges then render immediately
            agendaData = [];
            generateMainCalendar();

            // Fetch new month data in background; it will re-render on completion
            fetchAgenda(newYear, newMonth);

            // Do NOT sync mini calendar on landing
        }

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

            // Fungsi helper: jika 1 agenda langsung buka modal, jika lebih dari 1 buka sidebar
            function handleAgendaClick(agendaList, date) {
                if (agendaList.length === 1) {
                    // Jika hanya 1 agenda, langsung buka modal
                    openShowAgendaModal(agendaList[0]);
                } else if (agendaList.length > 1) {
                    // Jika lebih dari 1 agenda, buka sidebar
                    showAgendaListSidebar(agendaList, date);
                }
            }

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

                const copyBtn = document.getElementById('copySidebarAgendaBtn');
                if (copyBtn) {
                    copyBtn.onclick = () => {
                        const tanggal = formatDate(date);
                        function normalizeAttendees(raw) {
                            const txt = (raw || '').trim();
                            if (!txt || txt === '-') return { plain: '-' };
                            if (/\d+\.\s/.test(txt)) {
                                const firstIndex = txt.search(/\d+\.\s/);
                                const header = txt.slice(0, firstIndex).trim();
                                const items = txt.slice(firstIndex).split(/\d+\.\s/).map(s => s.trim()).filter(Boolean);
                                const plain = (header ? header + '\r\n' : '') + items.map(s => `• ${s}`).join('\r\n');
                                return { plain };
                            }
                            const parts = txt.split(/,\s+|\n+/).map(s => s.trim()).filter(Boolean);
                            if (parts.length <= 1) return { plain: txt };
                            return { plain: parts.map(s => `• ${s}`).join('\r\n') };
                        }

                        const entries = sortedAgendaList.map((item, idx) => {
                            const name = item.agenda_name || item.title || '-';
                            const desc = item.description || '-';
                            const start = item.start_time || '';
                            const end = item.end_time || '';
                            const waktu = (start && end) ? `${start} - ${end}` : (start || end || '-');
                            const lokasi = item.location || '-';
                            const pelaksana = (item.unit && item.unit.unit_name) ? item.unit.unit_name : (item.unit_name || '-');
                            const dihadiriRaw = item.involved_institution || '-';
                            const dihadiriNorm = normalizeAttendees(dihadiriRaw);
                            const status = (item.is_public == 1 || item.is_public === true) ? 'Publik' : 'Privasi';
                            const catatan = item.notes || '-';
                            const nomor = idx + 1;

                            const plain =
`*AGENDA ${nomor}:* ${name}\r\n\r\n`+
`*Deskripsi:*\r\n${desc}\r\n\r\n`+
`*Tanggal:* ${tanggal}\r\n`+
`*Waktu:* ${waktu}\r\n`+
`*Lokasi:* ${lokasi}\r\n\r\n`+
`*Pelaksana:* ${pelaksana}\r\n`+
`*Dihadiri:*\r\n${dihadiriNorm.plain}\r\n\r\n`+
`*Status:* ${status}\r\n`+
`*Catatan:* ${catatan}`;

                            return { plain };
                        });

                        const headerText = `Agenda ${tanggal}`;
                        const headerPlain = `*${headerText.toUpperCase()}*\r\n\r\n`;
                        const plainText = headerPlain + entries.map(e => e.plain).join(`\r\n\r\n`);

                        const doTextareaFallback = () => {
                            const ta = document.createElement('textarea');
                            ta.value = plainText;
                            ta.style.position = 'fixed';
                            ta.style.left = '-9999px';
                            document.body.appendChild(ta);
                            ta.select();
                            document.execCommand('copy');
                            document.body.removeChild(ta);
                            showCopiedToast();
                        };

                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(plainText).then(showCopiedToast).catch(doTextareaFallback);
                        } else {
                            doTextareaFallback();
                        }

                        function showCopiedToast() {
                            const msg = `Agenda ${tanggal} berhasil disalin`;
                            if (typeof showSuccessToast === 'function') showSuccessToast(msg);
                            else alert(msg);
                        }
                    };
                }

                const esc = s => String(s || '').replace(/[&<>]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;'}[c]));

                const toggleRow = document.createElement('div');
                toggleRow.className = 'list-toggle';
                toggleRow.innerHTML = `<span class="view-indicator is-open">Tampilkan</span>`;
                listContainer.appendChild(toggleRow);

                sortedAgendaList.forEach((data) => {
                    const itemDiv = document.createElement("div");
                    itemDiv.className = "agenda-item";

                    const header = document.createElement("div");
                    header.className = "agenda-header";
                    header.innerHTML = `
                        <div class="agenda-item-title">${esc(data.agenda_name)}</div>
                        <button class="agenda-item-arrow" aria-label="Tampilkan/Sembunyikan">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    `;

                    const timeText = (data.start_time && data.end_time)
                        ? `${String(data.start_time).slice(0,5)} - ${String(data.end_time).slice(0,5)}`
                        : (data.end_time ? String(data.end_time).slice(0,5) : (data.start_time ? String(data.start_time).slice(0,5) : '-'));
                    const tanggalText = formatDate(data.date || date);

                    const unitName = (data.unit && data.unit.unit_name) ? data.unit.unit_name : (data.unit_name || '-');
                    const desc = (data.description || '-');
                    const involved = (data.involved_institution || '-');
                    const notes = (data.notes || '-');
                    const location = (data.location || '-');
                    const statusText = (data.is_public == 1 || data.is_public === true) ? 'Publik' : 'Privasi';
                    const statusClass = (data.is_public == 1 || data.is_public === true) ? 'public' : 'private';

                    const details = document.createElement("div");
                    details.className = "agenda-details";
                    details.innerHTML = `
                        <div class="agenda-detail-line"><i class="fas fa-align-left"></i><span>Deskripsi: ${esc(desc)}</span></div>
                        <div class="agenda-detail-line"><i class="fas fa-calendar"></i><span>Tanggal: ${esc(tanggalText)}</span></div>
                        <div class="agenda-detail-line"><i class="fas fa-clock"></i><span>Waktu: ${esc(timeText)}</span></div>
                        <div class="agenda-detail-line"><i class="fas fa-map-marker-alt"></i><span>Lokasi: ${esc(location)}</span></div>
                        <div class="agenda-detail-line"><i class="fas fa-building"></i><span>Pelaksana: ${esc(unitName)}</span></div>
                        <div class="agenda-detail-line"><i class="fas fa-users"></i><span>Dihadiri: ${esc(involved)}</span></div>
                        <div class="agenda-detail-line"><i class="fas fa-eye"></i><span>Status: <span class="status-pill ${statusClass}">${esc(statusText)}</span></span></div>
                        <div class="agenda-detail-line"><i class="fas fa-sticky-note"></i><span>Catatan: ${esc(notes)}</span></div>
                    `;

                    itemDiv.appendChild(header);
                    itemDiv.appendChild(details);

                    header.querySelector('.agenda-item-title').addEventListener('click', (e) => {
                        e.stopPropagation();
                        openShowAgendaModal(data);
                    });

                    header.querySelector('.agenda-item-arrow').addEventListener('click', (e) => {
                        e.stopPropagation();
                        details.classList.toggle('hidden');
                        const icon = e.currentTarget.querySelector('i');
                        icon.className = details.classList.contains('hidden') ? 'fas fa-chevron-right' : 'fas fa-chevron-down';
                    });

                    itemDiv.addEventListener('click', (e) => {
                        if (e.target.closest('.agenda-item-arrow')) return;
                        openShowAgendaModal(data);
                    });

                    listContainer.appendChild(itemDiv);
                });

                sidebar.classList.add("active");

                const viewIndicator = listContainer.querySelector('.view-indicator');
                if (viewIndicator) {
                    let isOpen = true;
                    const setLabel = () => {
                        viewIndicator.textContent = 'Tampilkan';
                        if (isOpen) {
                            viewIndicator.classList.add('is-open');
                            viewIndicator.classList.remove('is-closed');
                            viewIndicator.setAttribute('aria-label', 'Sembunyikan ringkasan');
                            viewIndicator.setAttribute('title', 'Sembunyikan ringkasan');
                        } else {
                            viewIndicator.classList.add('is-closed');
                            viewIndicator.classList.remove('is-open');
                            viewIndicator.setAttribute('aria-label', 'Tampilkan ringkasan');
                            viewIndicator.setAttribute('title', 'Tampilkan ringkasan');
                        }
                    };
                    setLabel();
                    viewIndicator.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const allDetails = document.querySelectorAll('#agendaSidebar .agenda-details');
                        isOpen = !isOpen;
                        allDetails.forEach(d => d.classList.toggle('hidden', !isOpen));
                        setLabel();
                    });
                }
            }

            function openShowAgendaModal(data) {
                // Semua agenda diperlakukan sama - tidak ada pembedaan eksternal/lokal

                document.getElementById('showAgendaName').innerText = data.agenda_name ?? '-';
                document.getElementById('showAgendaDate').innerText = formatDate(data.date);

                const timeText = (data.start_time && data.end_time) ?
                    `${data.start_time} - ${data.end_time}` :
                    (data.end_time ? data.end_time : (data.start_time ?? '-'));
                document.getElementById('showAgendaTime').innerText = timeText;

                document.getElementById('showAgendaLocation').innerText = data.location ?? '-';
                document.getElementById('showAgendaDesc').innerText = data.description ?? '-';

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
                    const isPublic = data.is_public == 1;
                    const bg = isPublic ? '#A8E6A3' : '#FFB67E';
                    const text = isPublic ? 'Publik' : 'Privasi';
                    accessEl.innerHTML =
                        `<span class="badge rounded-pill" style="background-color:${bg}; color:#2F3E35; padding:6px 10px;">${text}</span>`;
                }

                const notesEl = document.getElementById('showAgendaNotes');
                if (notesEl) notesEl.innerText = data.notes ?? '-';

                new bootstrap.Modal(document.getElementById('showAgendaModal')).show();
            }

            document.addEventListener('DOMContentLoaded', function() {
                generateMainCalendar();
                fetchAgenda(selectedYear, selectedMonth);

                const showAgendaModalEl = document.getElementById('showAgendaModal');
                if (showAgendaModalEl) {
                    showAgendaModalEl.addEventListener('show.bs.modal', function() {
                        const sidebar = document.getElementById('agendaSidebar');
                        if (sidebar) sidebar.classList.add('hidden');
                    });

                    showAgendaModalEl.addEventListener('hidden.bs.modal', function() {
                        const sidebar = document.getElementById('agendaSidebar');
                        if (sidebar) sidebar.classList.remove('hidden');
                    });
                }
            });
            document.addEventListener('DOMContentLoaded', function() {
                const message = localStorage.getItem('logoutSuccess');
                if (message) {
                    showSuccessToast(message);
                    localStorage.removeItem('logoutSuccess');
                }
            });
        </script>
    @endsection
