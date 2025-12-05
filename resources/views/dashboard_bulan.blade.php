@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-bulan.css') }}">
    <link rel="stylesheet" href="{{ asset('css/create_agenda_modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/agenda-create.css') }}">
@endpush

@section('content')
    @include('show_agenda_modal_dashboard')
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
                            <button class="copy-sidebar" id="copySidebarAgendaBtn" aria-label="Salin agenda hari ini"
                                title="Salin agenda hari ini">
                                <i class="fas fa-copy"></i>
                                <span class="label">Salin</span>
                            </button>
                            <button class="close-sidebar" aria-label="Tutup"
                                onclick="document.getElementById('agendaSidebar').classList.remove('active')">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>
                    <div id="agendaList" class="agenda-list"></div>
                </div>
            </div>
        </div>

        <!-- Modal Create Agenda -->
        <div class="modal-overlay" id="createAgendaModal" style="display: none;">
            <div class="modal-container">
                <div class="modal-header">
                    <div class="modal-title">
                        <i class="fas fa-calendar-plus"></i>
                        <span>Buat Agenda Baru</span>
                    </div>
                    <button class="modal-close" onclick="closeModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="agendaForm" action="{{ route('agenda.store') }}" method="POST"
                        onsubmit="return handleFormSubmit(event)">
                        @csrf

                        <!-- Page 1: Basic Agenda Info -->
                        <div class="form-page" id="page1">
                        <div class="form-grid">
                            <!-- Full Width Fields -->
                            <div class="input-group fullwidth-group">
                                <label><i class="fas fa-file-alt"></i> Nama Agenda</label>
                                <input type="text" name="agenda_name" id="agenda_name" placeholder="Masukkan nama agenda"
                                    required>
                            </div>

                            <div class="input-group fullwidth-group">
                                <label><i class="fas fa-align-left"></i> Deskripsi Agenda</label>
                                <textarea name="description" id="description" placeholder="Masukkan deskripsi agenda" required></textarea>
                            </div>

                            <!-- Kolom kiri -->
                            <div class="form-column">
                                <div class="input-group">
                                    <label><i class="fas fa-building"></i> Pelaksana</label>
                                    <input type="text" value="{{ Auth::user()->unit->unit_name ?? '-' }}" readonly>
                                    <input type="hidden" name="id_unit" value="{{ Auth::user()->id_unit }}">
                                </div>

                                <div class="input-group">
                                    <label><i class="fas fa-eye"></i> Kategori Agenda</label>
                                    <select name="is_public" id="is_public">
                                        <option value="1">Publik</option>
                                        <option value="0">Privasi</option>
                                    </select>
                                </div>

                                <div class="input-group lokasi-group">
                                    <label for="lokasi"><i class="fas fa-map-marker-alt"></i> Lokasi</label>
                                    <input type="text" id="location" name="location"
                                        placeholder="Masukkan lokasi kegiatan">
                                </div>
                            </div>

                            <!-- Kolom kanan -->
                            <div class="form-column">
                                <div class="input-group">
                                    <label><i class="fas fa-calendar-day"></i> Tanggal</label>
                                    <input type="date" name="date" id="date" required>
                                </div>

                                <div class="input-group">
                                    <label><i class="fas fa-clock"></i> Waktu Mulai</label>
                                    <input type="time" name="start_time" id="start_time" required>
                                </div>

                                <div class="input-group">
                                    <label><i class="fas fa-clock"></i> Waktu Selesai</label>
                                    <input type="time" name="end_time" id="end_time" required>
                                </div>
                            </div>

                                <!-- Catatan - Full Width -->
                            <div class="input-group fullwidth-group">
                                    <label><i class="fa-solid fa-file-lines" style="color:#6b8f71;"></i> Catatan</label>
                                    <textarea name="notes" id="notes" placeholder="Masukkan catatan tambahan (opsional)" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Page 2: Session Management -->
                        <div class="form-page" id="page2" style="display: none;">
                            <!-- Toggle Group -->
                            <div class="input-group fullwidth-group" style="margin-bottom: 24px;">
                                <label style="margin-bottom: 12px; display: block;"><i class="fas fa-layer-group"></i> Mode Agenda</label>
                                <div style="display: flex; gap: 16px;">
                                    <label class="radio-option" style="display: flex; align-items: center; cursor: pointer; padding: 12px 20px; border: 2px solid #e5e7eb; border-radius: 8px; transition: all 0.3s;">
                                        <input type="radio" name="has_group" value="0" id="noGroup" checked style="margin-right: 10px; width: 18px; height: 18px; cursor: pointer;">
                                        <span>Agenda Normal</span>
                                    </label>
                                    <label class="radio-option" style="display: flex; align-items: center; cursor: pointer; padding: 12px 20px; border: 2px solid #e5e7eb; border-radius: 8px; transition: all 0.3s;">
                                        <input type="radio" name="has_group" value="1" id="withGroup" style="margin-right: 10px; width: 18px; height: 18px; cursor: pointer;">
                                        <span>Agenda dengan Sesi (Group)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Container untuk Dihadiri Normal -->
                            <div id="normalInvitation" class="invitation-container">
                                <div class="input-group fullwidth-group">
                                    <label><i class="fas fa-people-group"></i> Dihadiri</label>
                                    <div class="chips-multiselect" id="normalInvolvedInstansi">
                                    <div class="chips-container">
                                        <div class="chips-selected"></div>
                                            <input type="text" class="chips-input" placeholder="-- Pilih Instansi yang Hadir --"
                                                readonly style="cursor: pointer;">
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
                            </div>

                            <!-- Container untuk Dihadiri dengan Sesi -->
                            <div id="groupInvitation" class="invitation-container" style="display: none;">
                                <div id="sessionsContainer">
                                    <!-- Session akan ditambahkan secara dinamis -->
                                </div>
                                <button type="button" id="addSessionBtn" class="btn-secondary" style="margin-top: 16px; display: none;">
                                    <i class="fas fa-plus"></i> Tambah Sesi
                                </button>
                            </div>
                        </div>

                        <!-- Navigation & Submit -->
                        <div class="form-navigation">
                            <button type="button" id="prevPageBtn" class="btn-secondary" style="display: none;">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </button>
                            <div style="flex: 1;"></div>
                            <button type="button" id="nextPageBtn" class="btn-primary">
                                Lanjutkan <i class="fas fa-arrow-right"></i>
                            </button>
                            <button type="submit" id="submitBtn" class="btn-primary" style="display: none;">
                                Ajukan Agenda
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
        <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // ✅ Ambil dari URL (?bulan=3&tahun=2025) biar nggak selalu Oktober
            const urlParams = new URLSearchParams(window.location.search);
            let selectedMonth = parseInt(urlParams.get('bulan')) || new Date().getMonth() + 1;
            let selectedYear = parseInt(urlParams.get('tahun')) || new Date().getFullYear();

            let currentDate = new Date(selectedYear, selectedMonth - 1, 1);

            let agenda = {!! json_encode($agenda, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!};

            function handleFormSubmit(e) {
                e.preventDefault();
                if (!validateAgendaForm()) return false;

                const form = e.target;
                const formData = new FormData(form);

                fetch(form.action, {
                        method: "POST",
                        headers: {
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Tambahkan agenda baru langsung ke array lokal
                            agenda.push(data.agenda);
                            generateMainCalendar(); // re-render tampilan kalender
                            closeModal();

                            showSuccessToast("Agenda berhasil ditambahkan!");
                        } else {
                            showErrorToast("Gagal menambahkan agenda!");
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Toastify({
                            text: "Terjadi kesalahan pada server.",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#f44336",
                            stopOnFocus: true
                        }).showToast();
                    });
                return false;
            }

            /**
             * Filter data agenda berdasarkan tanggal, bulan, dan tahun.
             * @param {Array} agendaList - Array of agenda objects.
             * @param {Object} options - Filter options (year, month, day).
             * @returns {Array} - Data agenda yang sesuai filter.
             *
             * Contoh:
             * filterAgenda(agendaList, { year: 2025, month: 10, day: 16 });
             * filterAgenda(agendaList, { month: 10 }); // semua agenda bulan Oktober
             * filterAgenda(agendaList, { year: 2025 }); // semua agenda tahun 2025
             */
            function filterAgenda(agendaList, options = {}) {
                const {
                    year,
                    month,
                    day
                } = options;

                return agendaList.filter(item => {
                    const date = new Date(item.date);

                    if (isNaN(date)) return false; // jaga-jaga kalau format tanggal rusak

                    const matchYear = year ? date.getFullYear() === Number(year) : true;
                    const matchMonth = month ? date.getMonth() + 1 === Number(month) : true;
                    const matchDay = day ? date.getDate() === Number(day) : true;

                    return matchYear && matchMonth && matchDay;
                });
            }

            // generate main calender
            // === 🔹 STATUS FILTER KATEGORI ===
            let showPublic = true;
            let showPrivate = true;

            function initCategoryFilter() {
                // cari elemen dengan id ATAU class, biar fleksibel
                const publicCheckbox = document.querySelector(
                    '.category-item.public input, #filterPublic, input[name="filterPublic"]');
                const privateCheckbox = document.querySelector(
                    '.category-item.private input, #filterPrivate, input[name="filterPrivate"]');


                if (publicCheckbox) publicCheckbox.checked = true;
                if (privateCheckbox) privateCheckbox.checked = true;

                if (publicCheckbox) {
                    publicCheckbox.addEventListener('change', () => {
                        showPublic = publicCheckbox.checked;
                        generateMainCalendar();
                    });
                }

                if (privateCheckbox) {
                    privateCheckbox.addEventListener('change', () => {
                        showPrivate = privateCheckbox.checked;
                        generateMainCalendar();
                    });
                }
            }

            // 🔹 Ambil agenda berdasarkan hari + filter kategori (termasuk agenda eksternal)
            function getFilteredAgendaForDay(year, month, day) {
                return agenda.filter(item => {
                    if (!item.date) return false;

                    // Hanya tampilkan agenda yang sudah di-approve
                    if (item.status !== 'approved') return false;

                    // Parse date - handle both string and date object
                    let date;
                    if (typeof item.date === 'string') {
                        // If it's in Y-m-d format, parse it correctly
                        if (item.date.match(/^\d{4}-\d{2}-\d{2}$/)) {
                            const [y, m, d] = item.date.split('-').map(Number);
                            date = new Date(y, m - 1, d);
                        } else {
                            date = new Date(item.date);
                        }
                    } else {
                        date = new Date(item.date);
                    }

                    if (isNaN(date.getTime())) return false;

                    const matchYear = date.getFullYear() === Number(year);
                    const matchMonth = date.getMonth() + 1 === Number(month);
                    const matchDay = date.getDate() === Number(day);

                    // Semua agenda diperlakukan sama (tidak ada pembedaan eksternal/lokal)
                    const isPublic = item.is_public == 1;
                    const matchCategory = (isPublic && showPublic) || (!isPublic && showPrivate);

                    return matchYear && matchMonth && matchDay && matchCategory;
                });
            }

            // === 🔹 GENERATE MAIN CALENDAR (gabungan versi filter & versi awal)
            function generateMainCalendar() {
                const monthYear = document.getElementById('currentMonthYear');
                const calendarDays = document.getElementById('calendarDays');

                const monthNames = [
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];
                monthYear.textContent = monthNames[currentDate.getMonth()] + ' ' + currentDate.getFullYear();

                const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
                const startDate = new Date(firstDay);
                startDate.setDate(startDate.getDate() - (firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1));

                calendarDays.innerHTML = '';

                for (let i = 0; i < 35; i++) {
                    const date = new Date(startDate);
                    date.setDate(startDate.getDate() + i);

                    const dayElement = document.createElement('div');
                    dayElement.className = 'calendar-day';
                    if (date.getMonth() !== currentDate.getMonth()) dayElement.classList.add('other-month');
                    if (date.getDay() === 0) dayElement.classList.add('weekend');
                    else if (date.getDay() === 6) dayElement.classList.add('saturday');
                    if (date.toDateString() === new Date().toDateString()) dayElement.classList.add('today');

                    const dayNumber = document.createElement('div');
                    dayNumber.className = 'day-number';
                    dayNumber.textContent = date.getDate();
                    dayElement.appendChild(dayNumber);

                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');

                    // 🔸 Filter agenda dengan kategori aktif
                    const filteredAgenda = getFilteredAgendaForDay(year, month, day);

                    if (filteredAgenda.length > 0) {
                        const agendaContainer = document.createElement("div");
                        agendaContainer.className = "agenda-container";

                        // Semua agenda diperlakukan sama - tidak ada pembedaan eksternal/lokal
                        const publicAgenda = filteredAgenda.filter(a => a.is_public == 1);
                        const privateAgenda = filteredAgenda.filter(a => a.is_public == 0);

                        // Semua agenda yang ditampilkan sudah approved, jadi langsung tampilkan
                        if (publicAgenda.length > 0 && privateAgenda.length > 0) {
                            const publicBadge = document.createElement("div");
                            publicBadge.className = "agenda-count-badge bg-green-500";
                            publicBadge.textContent =
                                publicAgenda.length > 1 ? `${publicAgenda.length} Kegiatan` : publicAgenda[0].agenda_name;
                            publicBadge.addEventListener('click', (e) => {
                                e.stopPropagation();
                                handleAgendaClick(publicAgenda, `${year}-${month}-${day}`);
                            });
                            agendaContainer.appendChild(publicBadge);

                            const privateBadge = document.createElement("div");
                            privateBadge.className = "agenda-count-badge bg-orange-500";
                            privateBadge.textContent =
                                privateAgenda.length > 1 ? `${privateAgenda.length} Kegiatan` : privateAgenda[0].agenda_name;
                            privateBadge.addEventListener('click', (e) => {
                                e.stopPropagation();
                                handleAgendaClick(privateAgenda, `${year}-${month}-${day}`);
                            });
                            agendaContainer.appendChild(privateBadge);
                        } else if (publicAgenda.length > 0 || privateAgenda.length > 0) {
                            const badge = document.createElement("div");
                            const isPublic = publicAgenda.length > 0;
                            badge.className = `agenda-count-badge ${isPublic ? "bg-green-500" : "bg-orange-500"}`;
                            const agendaToShow = publicAgenda.length > 0 ? publicAgenda : privateAgenda;
                            badge.textContent =
                                agendaToShow.length > 1 ? `${agendaToShow.length} Kegiatan` : agendaToShow[0].agenda_name;
                            badge.addEventListener('click', (e) => {
                                e.stopPropagation();
                                handleAgendaClick(agendaToShow, `${year}-${month}-${day}`);
                            });
                            agendaContainer.appendChild(badge);
                        }

                        dayElement.appendChild(agendaContainer);
                    }

                    dayNumber.addEventListener('click', (e) => {
                        e.stopPropagation();
                        window.location.href = `/dashboard/hari?tanggal=${year}-${month}-${day}`;
                    });

                    // Double click untuk buka modal tambah agenda
                    dayElement.addEventListener('dblclick', (e) => {
                        e.stopPropagation();
                        const selectedDate = `${year}-${month}-${day}`;
                        openModal(selectedDate);
                    });

                    calendarDays.appendChild(dayElement);
                }
            }

            window.addEventListener('load', () => {
                initCategoryFilter();
                generateMainCalendar();
            });


            // Change month function (do NOT sync mini calendar)
            function changeMonth(direction) {
                currentDate.setMonth(currentDate.getMonth() + direction);

                // Update URL
                const newMonth = currentDate.getMonth() + 1;
                const newYear = currentDate.getFullYear();
                const newUrl = `/dashboard/bulan?bulan=${newMonth}&tahun=${newYear}`;
                window.history.pushState({}, '', newUrl);

                generateMainCalendar();
            }

            // Modal functions
            function openModal(selectedDate = null) {
                const modal = document.getElementById('createAgendaModal');
                const dateInput = document.getElementById('date');
                const agendaSidebar = document.getElementById('agendaSidebar');

                if (selectedDate && dateInput) {
                    dateInput.value = selectedDate;
                }

                // Sembunyikan sidebar saat modal terbuka
                if (agendaSidebar) {
                    agendaSidebar.classList.add('hidden');
                }

                // Reset to page 1
                if (typeof showPage === 'function') {
                    showPage(1);
                }

                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                const modal = document.getElementById('createAgendaModal');
                const agendaSidebar = document.getElementById('agendaSidebar');

                modal.style.display = 'none';
                document.body.style.overflow = 'auto';

                // Tampilkan kembali sidebar jika sebelumnya aktif
                if (agendaSidebar && agendaSidebar.classList.contains('active')) {
                    agendaSidebar.classList.remove('hidden');
                }

                // Reset form
                const form = document.getElementById('agendaForm');
                if (form) {
                    form.reset();
                }

                // Reset to page 1
                if (typeof showPage === 'function') {
                    showPage(1);
                }

                // Clear sessions
                const sessionsContainer = document.getElementById('sessionsContainer');
                if (sessionsContainer) {
                    sessionsContainer.innerHTML = '';
                }
            }

            // Validasi form
            function validateAgendaForm() {
                const inputs = document.querySelectorAll(
                    '#createAgendaModal input[required], #createAgendaModal textarea[required]');
                let valid = true;

                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        input.style.borderColor = 'red';
                        valid = false;
                    } else {
                        input.style.borderColor = '#d6e0d9';
                    }
                });

                if (!valid) {
                    showErrorToast("Semua field wajib diisi!");
                }

                return valid;
            }

            // Multi-page form navigation
            let currentPage = 1;
            const totalPages = 2;

            function showPage(page) {
                document.querySelectorAll('#createAgendaModal .form-page').forEach((p, idx) => {
                    p.style.display = idx + 1 === page ? 'block' : 'none';
                });

                // Update navigation buttons
                const prevBtn = document.getElementById('prevPageBtn');
                const nextBtn = document.getElementById('nextPageBtn');
                const submitBtn = document.getElementById('submitBtn');

                if (prevBtn) prevBtn.style.display = page > 1 ? 'block' : 'none';
                if (nextBtn) nextBtn.style.display = page < totalPages ? 'block' : 'none';
                if (submitBtn) submitBtn.style.display = page === totalPages ? 'block' : 'none';

                currentPage = page;
            }

            // Initialize page navigation
            document.addEventListener('DOMContentLoaded', function() {
                const nextBtn = document.getElementById('nextPageBtn');
                const prevBtn = document.getElementById('prevPageBtn');
                const submitBtn = document.getElementById('submitBtn');

                if (nextBtn) {
                    nextBtn.addEventListener('click', function() {
                        if (validatePage1()) {
                            showPage(2);
                        }
                    });
                }

                if (prevBtn) {
                    prevBtn.addEventListener('click', function() {
                        showPage(1);
                    });
                }

                if (submitBtn) {
                    submitBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (!validatePage1()) {
                            showPage(1);
                            return false;
                        }
                        collectSessionData();
                        const form = document.getElementById('agendaForm');
                        if (form) {
                            const formEvent = new Event('submit', { bubbles: true, cancelable: true });
                            form.dispatchEvent(formEvent);
                        }
                    });
                }
            });

            function validatePage1() {
                const requiredFields = ['agenda_name', 'description', 'id_unit', 'date', 'start_time', 'end_time'];
                let isValid = true;
                let emptyFields = [];

                requiredFields.forEach(fieldName => {
                    const field = document.querySelector(`#createAgendaModal [name="${fieldName}"]`);
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
                    showPage(1);
                    showErrorToast('Mohon lengkapi semua field yang wajib diisi!');
                }

                return isValid;
            }

            function collectSessionData() {
                const hasGroup = document.querySelector('#createAgendaModal input[name="has_group"]:checked')?.value === '1';
                const sessions = [];

                if (hasGroup) {
                    // Collect from group sessions
                    document.querySelectorAll('#createAgendaModal .session-item').forEach((sessionEl, index) => {
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
                    document.querySelectorAll('#createAgendaModal #normalInvolvedInstansi .chip[data-unit-id]').forEach(chip => {
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
                const existingInput = document.querySelector('#createAgendaModal input[name="sessions_data"]');
                if (existingInput) {
                    existingInput.remove();
                }

                const sessionsInput = document.createElement('input');
                sessionsInput.type = 'hidden';
                sessionsInput.name = 'sessions_data';
                sessionsInput.value = JSON.stringify(sessions);
                document.getElementById('agendaForm').appendChild(sessionsInput);
            }

            // Toggle Group functionality
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('#createAgendaModal input[name="has_group"]').forEach(radio => {
                    radio.addEventListener('change', function() {
                        const hasGroup = this.value === '1';
                        const normalInvitation = document.getElementById('normalInvitation');
                        const groupInvitation = document.getElementById('groupInvitation');
                        const addSessionBtn = document.getElementById('addSessionBtn');

                        if (normalInvitation) normalInvitation.style.display = hasGroup ? 'none' : 'block';
                        if (groupInvitation) groupInvitation.style.display = hasGroup ? 'block' : 'none';
                        if (addSessionBtn) addSessionBtn.style.display = hasGroup ? 'block' : 'none';

                        if (hasGroup && document.querySelectorAll('#createAgendaModal .session-item').length === 0) {
                            addSession(1);
                            addSession(2);
                        }
                    });
                });

                // Add session functionality
                const addSessionBtn = document.getElementById('addSessionBtn');
                if (addSessionBtn) {
                    let sessionCount = 0;
                    addSessionBtn.addEventListener('click', function() {
                        sessionCount++;
                        addSession(sessionCount + 2);
                    });
                }
            });

            function addSession(sessionNumber) {
                const container = document.getElementById('sessionsContainer');
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
                initChipsMultiselect(sessionDiv.querySelector('.session-units-select'));

                // Remove session button
                const removeBtn = sessionDiv.querySelector('.remove-session-btn');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        sessionDiv.remove();
                    });
                }
            }

            // Initialize chips multiselect for normal invitation
            function initChipsMultiselect(root) {
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
                const normalRoot = document.getElementById('normalInvolvedInstansi');
                if (normalRoot) {
                    initChipsMultiselect(normalRoot);
                }
            });

            // Update handleFormSubmit to collect session data
            const originalHandleFormSubmit = handleFormSubmit;
            handleFormSubmit = function(e) {
                e.preventDefault();
                if (currentPage === 1) {
                    if (!validatePage1()) {
                        showPage(1);
                        return false;
                    }
                    showPage(2);
                    return false;
                }
                // If on page 2, collect session data and submit
                collectSessionData();
                return originalHandleFormSubmit.call(this, e);
            };


            // Helper function untuk get element
            function getel(id) {
                return document.getElementById(id);
            }
        </script>

        <script>
            // === 🔹 MODAL DETAIL AGENDA ===
            function openShowAgendaModal(data) {
                // Semua agenda diperlakukan sama - tidak ada pembedaan eksternal/lokal

                // Gunakan fungsi global fillAgendaModal jika tersedia
                if (typeof window.fillAgendaModal === 'function') {
                    window.fillAgendaModal(data);
                } else {
                    // Fallback: isi manual jika fungsi global belum tersedia
                    const nameEl = document.getElementById('showAgendaName');
                    if (nameEl) {
                        nameEl.innerHTML = (data.agenda_name ?? '-');
                    }

                    document.getElementById('showAgendaDate').innerText = formatDate(data.date);

                    const timeText = (data.start_time && data.end_time) ?
                        `${data.start_time} - ${data.end_time}` :
                        (data.end_time ? data.end_time : (data.start_time ?? '-'));
                    document.getElementById('showAgendaTime').innerText = timeText;

                    document.getElementById('showAgendaLocation').innerText = data.location ?? '-';
                    document.getElementById('showAgendaDesc').innerText = data.description ?? '-';

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

                    // Isi status akses (publik/privasi)
                    const accessEl = document.getElementById('showAgendaAccess');
                    if (accessEl) {
                        const isPublic = data.is_public == 1;
                        const bg = isPublic ? '#A8E6A3' : '#FFB67E';
                        const text = isPublic ? 'Publik' : 'Privasi';
                        accessEl.innerHTML =
                            `<span class="badge rounded-pill" style="background-color:${bg}; color:#2F3E35; padding:6px 10px;">${text}</span>`;
                    }
                }

                const notesEl = document.getElementById('showAgendaNotes');
                if (notesEl) notesEl.innerText = data.notes ?? '-';


                // Tampilkan modal
                new bootstrap.Modal(document.getElementById('showAgendaModal')).show();
            }
        </script>

        <script>
            const showAgendaModalEl = document.getElementById('showAgendaModal');

            showAgendaModalEl.addEventListener('show.bs.modal', function() {
                document.getElementById('agendaSidebar').classList.add('hidden');
            });

            showAgendaModalEl.addEventListener('hidden.bs.modal', function() {
                document.getElementById('agendaSidebar').classList.remove('hidden');
            });
        </script>
        @if (session('success'))
            <script>
                showSuccessToast("{{ session('success') }}");
            </script>
        @endif

        @if (session('error'))
            <script>
                showErrorToast("{{ session('error') }}");
            </script>
        @endif

        <script>
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
                        const esc = (s) => String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g,
                            '&gt;');

                        function normalizeAttendees(raw) {
                            if (!raw || raw.trim() === '-') {
                                return {
                                    plain: '-',
                                    html: esc('-')
                                };
                            }
                            if (/\d+\.\s/.test(raw)) {
                                const firstIndex = raw.search(/\d+\.\s/);
                                const header = raw.slice(0, firstIndex).trim();
                                const items = raw.slice(firstIndex).split(/\d+\.\s/).map(s => s.trim()).filter(Boolean);
                                const plain = (header ? header + '\r\n' : '') + items.map(s => `• ${s}`).join('\r\n');
                                const html = (header ? esc(header) + '<br>' : '') + '<ul>' + items.map(s =>
                                    `<li>${esc(s)}</li>`).join('') + '</ul>';
                                return {
                                    plain,
                                    html
                                };
                            }
                            const parts = raw.split(/,\s+/).map(s => s.trim()).filter(Boolean);
                            if (parts.length <= 1) return {
                                plain: raw,
                                html: esc(raw)
                            };
                            return {
                                plain: parts.map(s => `• ${s}`).join('\r\n'),
                                html: '<ul>' + parts.map(s => `<li>${esc(s)}</li>`).join('') + '</ul>'
                            };
                        }

                        const entries = sortedAgendaList.map((item, idx) => {
                            const name = item.agenda_name || item.title || '-';
                            const desc = item.description || '-';
                            const start = item.start_time || '';
                            const end = item.end_time || '';
                            const waktu = (start && end) ? `${start} - ${end}` : (start || end || '-');
                            const lokasi = item.location || '-';
                            const pelaksana = (item.unit && item.unit.unit_name) ? item.unit.unit_name : (item
                                .unit_name || '-');
                            const dihadiriRaw = item.involved_institution || '-';
                            const dihadiriNorm = normalizeAttendees(dihadiriRaw);
                            const status = (item.is_public == 1 || item.is_public === true) ? 'Publik' : 'Privasi';
                            const catatan = item.notes || '-';
                            const nomor = idx + 1;

                            const plain =
                                `*AGENDA ${nomor}:* ${name}\r\n\r\n` +
                                `*Deskripsi:*\r\n${desc}\r\n\r\n` +
                                `*Tanggal:* ${tanggal}\r\n` +
                                `*Waktu:* ${waktu}\r\n` +
                                `*Lokasi:* ${lokasi}\r\n\r\n` +
                                `*Pelaksana:* ${pelaksana}\r\n` +
                                `*Dihadiri:*\r\n${dihadiriNorm.plain}\r\n\r\n` +
                                `*Status:* ${status}\r\n*Catatan:* ${catatan}`;

                            const html =
                                `<b>AGENDA ${nomor}:</b> ${esc(name)}<br><br>` +
                                `<b>Deskripsi:</b><br>${esc(desc)}<br><br>` +
                                `<b>Tanggal:</b> ${esc(tanggal)}<br>` +
                                `<b>Waktu:</b> ${esc(waktu)}<br>` +
                                `<b>Lokasi:</b> ${esc(lokasi)}<br><br>` +
                                `<b>Pelaksana:</b> ${esc(pelaksana)}<br>` +
                                `<b>Dihadiri:</b>` +
                                dihadiriNorm.html +
                                `<br>` +
                                `<b>Status:</b> ${esc(status)}<br><b>Catatan:</b> ${esc(catatan)}`;

                            return {
                                plain,
                                html
                            };
                        });

                        const headerText = `Agenda ${tanggal}`;
                        const headerPlain = `*${headerText.toUpperCase()}*\r\n\r\n`;
                        const headerHtml = `<b>${esc(headerText.toUpperCase())}</b><br><br>`;
                        const plainText = headerPlain + entries.map(e => e.plain).join(`\r\n\r\n`);
                        const htmlText = headerHtml + entries.map(e => e.html).join('<br><br>');

                        const doTextareaFallback = () => {
                            const ta = document.createElement('textarea');
                            ta.value = plainText;
                            ta.style.position = 'fixed';
                            ta.style.left = '-9999px';
                            document.body.appendChild(ta);
                            ta.select();
                            document.execCommand('copy');
                            document.body.removeChild(ta);
                            if (typeof showSuccessToast === 'function') showSuccessToast(
                                `Agenda ${tanggal} berhasil disalin`);
                        };

                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(plainText).then(() => {
                                if (typeof showSuccessToast === 'function') showSuccessToast(
                                    `Agenda ${tanggal} berhasil disalin`);
                            }).catch(doTextareaFallback);
                        } else {
                            doTextareaFallback();
                        }
                    };
                }

                const esc = s => String(s || '').replace(/[&<>]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;'}[c]));

                // Bar toggle di atas konten
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
                        ? `${data.start_time.slice(0,5)} - ${data.end_time.slice(0,5)}`
                        : (data.end_time ? data.end_time.slice(0,5) : (data.start_time ? data.start_time.slice(0,5) : '-'));
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
        </script>

        <script>
            function showSuccessToast(message) {
                const successToast = document.createElement('div');
                successToast.innerHTML = `
                    <div style="
                        display: flex;
                        align-items: center;
                        gap: 10px;
                        font-family: 'Poppins', sans-serif;
                        font-weight: 500;
                        font-size: 15px;
                        color: #14532D;
                    ">
                        <div style="
                            width: 20px;
                            height: 20px;
                            border-radius: 50%;
                            background: #10B981;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            flex-shrink: 0;
                        ">
                            <i class="fas fa-check" style="color: white; font-size: 11px;"></i>
                        </div>
                        <span>${message}</span>
                    </div>
                `;

                Toastify({
                    node: successToast,
                    duration: 2500,
                    gravity: "top",
                    position: "center",
                    close: false,
                    stopOnFocus: true,
                    offset: {
                        x: 0,
                        y: 20
                    },
                    zIndex: 13000,
                    style: {
                        background: "#E8F2EB",
                        border: "1px solid #B7D7C4",
                        borderRadius: "10px",
                        padding: "12px 18px",
                        boxShadow: "0 4px 12px rgba(0,0,0,0.06)",
                        display: "inline-flex",
                        alignItems: "center",
                    }
                }).showToast();
            }

            function showErrorToast(message) {
                const errorToast = document.createElement('div');
                errorToast.innerHTML = `
                    <div style="
                        display: flex;
                        align-items: center;
                        gap: 10px;
                        font-family: 'Poppins', sans-serif;
                        font-weight: 500;
                        font-size: 15px;
                        color: #991b1b;
                    ">
                        <div style="
                            width: 20px;
                            height: 20px;
                            border-radius: 50%;
                            background: #ef4444;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            flex-shrink: 0;
                        ">
                            <i class="fas fa-times" style="color: white; font-size: 11px;"></i>
                        </div>
                        <span>${message}</span>
                    </div>
                `;

                Toastify({
                    node: errorToast,
                    duration: 2500,
                    gravity: "top",
                    position: "center",
                    close: false,
                    stopOnFocus: true,
                    offset: {
                        x: 0,
                        y: 20
                    },
                    zIndex: 13000,
                    style: {
                        background: "#fee2e2",
                        border: "1px solid #fca5a5",
                        borderRadius: "10px",
                        padding: "12px 18px",
                        boxShadow: "0 4px 12px rgba(0,0,0,0.06)",
                        display: "inline-flex",
                        alignItems: "center",
                    }
                }).showToast();
            }
        </script>

        <style>
            /* Multi-page form styles */
            #createAgendaModal .form-page {
                animation: fadeIn 0.3s ease;
            }

            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }

            #createAgendaModal .form-navigation {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-top: 32px;
                padding-top: 24px;
                border-top: 2px solid #e5e7eb;
            }

            #createAgendaModal .page-indicator {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background: #e5e7eb;
                color: #6b7280;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s;
            }

            #createAgendaModal .page-indicator.active {
                background: #6b8f71;
                color: white;
            }

            #createAgendaModal .radio-option {
                transition: all 0.3s;
            }

            #createAgendaModal .radio-option:hover {
                border-color: #6b8f71 !important;
                background: #f0fdf4;
            }

            #createAgendaModal .radio-option input[type="radio"]:checked ~ span {
                color: #6b8f71;
                font-weight: 600;
            }

            #createAgendaModal .radio-option:has(input[type="radio"]:checked) {
                border-color: #6b8f71 !important;
                background: #f0fdf4;
            }

            #createAgendaModal .session-item {
                margin-bottom: 16px;
            }

            #createAgendaModal .btn-secondary {
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

            #createAgendaModal .btn-secondary:hover {
                background: #d1d5db;
            }

            #createAgendaModal .invitation-container {
                margin-top: 16px;
            }

            #createAgendaModal .btn-primary {
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

            #createAgendaModal .btn-primary:hover {
                background: #5a7a5f;
            }
        </style>
    @endsection
