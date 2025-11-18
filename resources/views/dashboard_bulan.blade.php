@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-bulan.css') }}">
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
                        <button class="close-sidebar"
                            onclick="document.getElementById('agendaSidebar').classList.remove('active')">
                            <i class="fas fa-times"></i>
                        </button>
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
                    <button class="modal-close" onclick="closeModal()" style="color: #6E9579;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body form-style-scope">
                    <form id="agendaForm" action="{{ route('agenda.store') }}" method="POST"
                        onsubmit="return handleFormSubmit(event)">
                        @csrf
                        <!-- Full width: Nama & Deskripsi -->
                        <div class="input-group">
                            <label><i class="fas fa-file-alt"></i> Nama Agenda</label>
                            <input type="text" name="agenda_name" id="agenda_name"
                                placeholder="Masukkan nama agenda" required>
                        </div>

                        <div class="input-group">
                            <label><i class="fas fa-align-left"></i> Deskripsi Agenda</label>
                            <textarea name="description" id="description" placeholder="Masukkan deskripsi agenda" required></textarea>
                        </div>

                        <!-- Grid dua kolom -->
                        <div class="form-grid">
                            <!-- Kiri: Pelaksana, Kategori, Lokasi -->
                            <div class="form-column">
                                <div class="input-group">
                                    <label><i class="fas fa-building"></i> Pelaksana</label>
                                    <input type="text" value="{{ Auth::user()->unit->unit_name ?? '-' }}" readonly>
                                </div>
                                <div class="input-group">
                                    <label><i class="fas fa-eye"></i> Kategori Agenda</label>
                                    <select name="is_public" id="is_public">
                                        <option value="1">Publik</option>
                                        <option value="0">Privasi</option>
                                    </select>
                                </div>
                                <div class="input-group lokasi-group">
                                    <label><i class="fas fa-location-dot"></i> Lokasi</label>
                                    <input type="text" name="location" id="location"
                                        placeholder="Masukkan lokasi kegiatan">
                                </div>
                            </div>

                            <!-- Kanan: Tanggal, Waktu Mulai, Waktu Selesai -->
                            <div class="form-column">
                                <div class="input-group">
                                    <label><i class="fas fa-calendar-day"></i> Tanggal</label>
                                    <input type="date" name="date" id="date" required>
                                </div>
                                <div class="input-group">
                                    <label><i class="fas fa-clock"></i> Waktu Mulai</label>
                                    <input type="time" name="start_time" id="start_time">
                                </div>
                                <div class="input-group">
                                    <label><i class="fas fa-clock"></i> Waktu Selesai</label>
                                    <input type="time" name="end_time" id="end_time">
                                </div>
                            </div>
                        </div>

                        <!-- Full width: Dihadiri -->
                        <div class="input-group fullwidth-group dihadiri-group">
                            <label><i class="fas fa-users"></i> Dihadiri</label>
                            <div class="chips-multiselect" id="involvedInstansi">
                                <div class="chips-container">
                                    <div class="chips-selected"></div>
                                    <input type="text" class="chips-input" placeholder="-- Pilih Instansi yang Hadir --" readonly style="cursor: pointer;">
                                </div>
                                <span class="chips-arrow"><i class="fas fa-chevron-down"></i></span>
                                <div class="chips-dropdown">
                                    <div class="chips-search">
                                        <input type="text" class="chips-search-input" placeholder="Cari instansi..." />
                                    </div>
                                    <div class="chips-add-new-input-container" style="display: none; padding: 10px 12px; border-top: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                                        <input type="text" class="chips-add-new-input same-style-as-search" placeholder="Ketik nama instansi baru..." />
                                        <div style="display: flex; gap: 8px; margin-top: 8px;">
                                            <button type="button" class="chips-add-confirm-btn saveInstansiBtn" style="flex: 1;">Tambahkan</button>
                                            <button type="button" class="chips-add-cancel-btn" style="flex: 1; padding: 8px 14px; border-radius: 8px; background: #e5e7eb; color: #374151; border: none; font-weight: 600;">Batal</button>
                                        </div>
                                    </div>
                                    <ul>
                                        <li class="select-all-option" data-action="select-all">
                                            <span class="check-icon"></span>
                                            <span class="item-text">Pilih Semua</span>
                                            <i class="fas fa-check checkmark-icon"></i>
                                        </li>
                                        @foreach ($units as $unit)
                                            @if ($unit->id_unit !== Auth::user()->id_unit)
                                                <li data-value="{{ $unit->unit_name }}" class="dropdown-item">
                                                    <span class="check-icon"></span>
                                                    <span class="item-text">{{ $unit->unit_name }}</span>
                                                    <i class="fas fa-check checkmark-icon"></i>
                                                </li>
                                            @endif
                                        @endforeach
                                        <li class="add-new-instansi-option" data-action="add-new" style="padding: 10px 12px; cursor: pointer; color: #6b8f71; font-weight: 500; display: flex; align-items: center; list-style: none;">
                                            <i class="fas fa-plus-circle" style="margin-right: 8px;"></i>
                                            <span>Lainnya...</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <input type="hidden" name="involved_institution" id="involvedInstitutionField" value="{{ old('involved_institution') }}">
                        </div>

                        <!-- Full width: Catatan -->
                        <div class="input-group">
                            <label><i class="fas fa-sticky-note"></i> Catatan</label>
                            <textarea name="notes" id="notes" placeholder="Masukkan catatan tambahan (opsional)"></textarea>
                        </div>

                        <div class="form-submit">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-paper-plane"></i> Ajukan Agenda
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

            // 🔹 Ambil agenda berdasarkan hari + filter kategori
            function getFilteredAgendaForDay(year, month, day) {
                return agenda.filter(item => {
                    const date = new Date(item.date);
                    if (isNaN(date)) return false;

                    const matchYear = date.getFullYear() === Number(year);
                    const matchMonth = date.getMonth() + 1 === Number(month);
                    const matchDay = date.getDate() === Number(day);

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

                        const publicAgenda = filteredAgenda.filter(a => a.is_public == 1);
                        const privateAgenda = filteredAgenda.filter(a => a.is_public == 0);

                        const hasApproved = filteredAgenda.some(a => a.status === 'approved');
                        const hasPending = filteredAgenda.some(a => a.status === 'pending');
                        const hasRejected = filteredAgenda.some(a => a.status === 'rejected');

                        let badgeColor = "bg-gray-400";
                        if (hasRejected) badgeColor = "bg-red-500";
                        else if (hasPending) badgeColor = "bg-yellow-500";

                        if (hasApproved && publicAgenda.length > 0 && privateAgenda.length > 0) {
                            const publicBadge = document.createElement("div");
                            publicBadge.className = "agenda-count-badge bg-green-500";
                            publicBadge.textContent =
                                publicAgenda.length > 1 ? `${publicAgenda.length} Kegiatan` : publicAgenda[0].agenda_name;
                            publicBadge.addEventListener('click', (e) => {
                                e.stopPropagation();
                                showAgendaListSidebar(publicAgenda, `${year}-${month}-${day}`);
                            });
                            agendaContainer.appendChild(publicBadge);

                            const privateBadge = document.createElement("div");
                            privateBadge.className = "agenda-count-badge bg-orange-500";
                            privateBadge.textContent =
                                privateAgenda.length > 1 ? `${privateAgenda.length} Kegiatan` : privateAgenda[0].agenda_name;
                            privateBadge.addEventListener('click', (e) => {
                                e.stopPropagation();
                                showAgendaListSidebar(privateAgenda, `${year}-${month}-${day}`);
                            });
                            agendaContainer.appendChild(privateBadge);
                        } else if (hasApproved && filteredAgenda.length > 0) {
                            const badge = document.createElement("div");
                            const isPublic = filteredAgenda.some(a => a.is_public == 1);
                            badge.className = `agenda-count-badge ${isPublic ? "bg-green-500" : "bg-orange-500"}`;
                            badge.textContent =
                                filteredAgenda.length > 1 ? `${filteredAgenda.length} Kegiatan` : filteredAgenda[0].agenda_name;
                            badge.addEventListener('click', (e) => {
                                e.stopPropagation();
                                showAgendaListSidebar(filteredAgenda, `${year}-${month}-${day}`);
                            });
                            agendaContainer.appendChild(badge);
                        } else if (!hasApproved && filteredAgenda.length > 0) {
                            const badge = document.createElement("div");
                            badge.className = `agenda-count-badge ${badgeColor}`;
                            badge.textContent =
                                filteredAgenda.length > 1 ? `${filteredAgenda.length} Kegiatan` : filteredAgenda[0].agenda_name;
                            badge.addEventListener('click', (e) => {
                                e.stopPropagation();
                                showAgendaListSidebar(filteredAgenda, `${year}-${month}-${day}`);
                            });
                            agendaContainer.appendChild(badge);
                        }

                        dayElement.appendChild(agendaContainer);
                    }

                    dayElement.addEventListener('click', (e) => {
                        e.stopPropagation();
                        openModal(`${year}-${month}-${day}`);
                    });

                    dayNumber.addEventListener('click', (e) => {
                        e.stopPropagation();
                        openModal(`${year}-${month}-${day}`);
                    });

                    calendarDays.appendChild(dayElement);
                }
            }

            // ✅ Jalankan setelah semua script selesai dimuat
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

                if (selectedDate && dateInput) {
                    dateInput.value = selectedDate;
                }

                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                const modal = document.getElementById('createAgendaModal');
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';

                // Reset form
                document.getElementById('agendaForm').reset();
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

            // Helper function untuk get element
            function getel(id) {
                return document.getElementById(id);
            }
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const root = document.getElementById('involvedInstansi');
                if (!root) return;
                const dropdown = root.querySelector('.chips-dropdown');
                const arrow = root.querySelector('.chips-arrow');
                const searchInput = root.querySelector('.chips-search-input');
                const mainInput = root.querySelector('.chips-input');
                const selectedWrap = root.querySelector('.chips-selected');
                const hiddenField = document.getElementById('involvedInstitutionField');
                const listItems = Array.from(dropdown.querySelectorAll('li.dropdown-item'));
                const selectAllOption = dropdown.querySelector('.select-all-option');
                const addNewOption = dropdown.querySelector('.add-new-instansi-option');
                const selectedValues = [];

                function syncHidden() {
                    hiddenField.value = selectedValues.join(', ');
                    root.classList.toggle('empty', selectedValues.length === 0);
                }

                function addChip(value) {
                    if (selectedWrap.querySelector(`.chip[data-value="${value}"]`)) return;
                    const chip = document.createElement('span');
                    chip.className = 'chip';
                    chip.setAttribute('data-value', value);
                    chip.innerHTML = `${value} <button type="button" class="chip-remove">&times;</button>`;
                    chip.querySelector('.chip-remove').addEventListener('click', (e) => {
                        e.stopPropagation();
                        const idx = selectedValues.indexOf(value);
                        if (idx > -1) selectedValues.splice(idx, 1);
                        chip.remove();
                        updateItemState(value);
                        updateSelectAllState();
                        syncHidden();
                    });
                    selectedWrap.appendChild(chip);
                }

                function removeChip(value) {
                    const chip = selectedWrap.querySelector(`.chip[data-value="${value}"]`);
                    if (chip) chip.remove();
                }

                function updateItemState(value) {
                    const li = listItems.find(li => li.getAttribute('data-value') === value);
                    if (!li) return;
                    const checked = selectedValues.includes(value);
                    li.classList.toggle('selected', checked);
                    const ci = li.querySelector('.check-icon');
                    if (ci) ci.classList.toggle('checked', checked);
                }

                function updateSelectAllState() {
                    if (!selectAllOption) return;
                    const allSelected = listItems.length > 0 && listItems.every(li => selectedValues.includes(li.getAttribute('data-value')));
                    selectAllOption.classList.toggle('selected', allSelected);
                    const ci = selectAllOption.querySelector('.check-icon');
                    if (ci) ci.classList.toggle('checked', allSelected);
                }

                function filterList(q) {
                    const lower = (q || '').toLowerCase();
                    listItems.forEach(li => {
                        const text = li.querySelector('.item-text').textContent.toLowerCase();
                        li.style.display = (!lower || text.includes(lower)) ? 'flex' : 'none';
                    });
                    if (selectAllOption) selectAllOption.style.display = (!lower || listItems.some(li => li.style.display !== 'none')) ? 'flex' : 'none';
                }

                function openDropdown() {
                    dropdown.classList.add('open');
                    root.classList.add('open');
                    hideAddNewInput();
                    searchInput.value = '';
                    filterList('');
                    searchInput.focus();
                    listItems.forEach(li => {
                        li.style.display = 'flex';
                        updateItemState(li.getAttribute('data-value'));
                    });
                    updateSelectAllState();
                }

                function closeDropdown() {
                    dropdown.classList.remove('open');
                    root.classList.remove('open');
                    hideAddNewInput();
                    searchInput.value = '';
                }

                function toggleDropdown() { dropdown.classList.contains('open') ? closeDropdown() : openDropdown(); }

                arrow.addEventListener('click', (e) => { e.stopPropagation(); toggleDropdown(); });
                mainInput.addEventListener('click', (e) => { e.stopPropagation(); toggleDropdown(); });
                document.addEventListener('click', () => closeDropdown());
                dropdown.addEventListener('click', (e) => e.stopPropagation());
                searchInput.addEventListener('input', e => filterList(e.target.value));

                listItems.forEach(li => {
                    li.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const value = li.getAttribute('data-value');
                        const idx = selectedValues.indexOf(value);
                        if (idx > -1) { selectedValues.splice(idx, 1); removeChip(value); }
                        else { selectedValues.push(value); addChip(value); }
                        updateItemState(value);
                        updateSelectAllState();
                        syncHidden();
                    });
                });

                if (selectAllOption) {
                    selectAllOption.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const visibleItems = listItems.filter(li => li.style.display !== 'none');
                        const allVisibleSelected = visibleItems.every(li => selectedValues.includes(li.getAttribute('data-value')));
                        if (allVisibleSelected) {
                            visibleItems.forEach(li => {
                                const v = li.getAttribute('data-value');
                                const idx = selectedValues.indexOf(v);
                                if (idx > -1) selectedValues.splice(idx, 1);
                                removeChip(v);
                                updateItemState(v);
                            });
                        } else {
                            visibleItems.forEach(li => {
                                const v = li.getAttribute('data-value');
                                if (!selectedValues.includes(v)) { selectedValues.push(v); addChip(v); updateItemState(v); }
                            });
                        }
                        updateSelectAllState();
                        syncHidden();
                    });
                }

                const addNewInputContainer = dropdown.querySelector('.chips-add-new-input-container');
                const addNewInput = dropdown.querySelector('.chips-add-new-input');
                const addConfirmBtn = dropdown.querySelector('.chips-add-confirm-btn');
                const addCancelBtn = dropdown.querySelector('.chips-add-cancel-btn');

                function showAddNewInput(initialValue = '') {
                    if (!addNewInputContainer) return;
                    addNewInputContainer.style.display = 'block';
                    if (addNewInput) {
                        addNewInput.value = initialValue;
                        setTimeout(() => addNewInput.focus(), 100);
                    }
                    if (selectAllOption) selectAllOption.style.display = 'none';
                    if (addNewOption) addNewOption.style.display = 'none';
                }

                function hideAddNewInput() {
                    if (!addNewInputContainer) return;
                    addNewInputContainer.style.display = 'none';
                    if (addNewInput) addNewInput.value = '';
                    if (selectAllOption) selectAllOption.style.display = 'flex';
                    if (addNewOption) addNewOption.style.display = 'flex';
                }

                function addNewItem(name) {
                    const v = (name || '').trim();
                    if (!v) return;
                    const existsInList = listItems.some(li => li.getAttribute('data-value').toLowerCase() === v.toLowerCase());
                    const existsInSelected = selectedValues.some(val => val.toLowerCase() === v.toLowerCase());
                    if (existsInList || existsInSelected) { hideAddNewInput(); return; }
                    selectedValues.push(v);
                    addChip(v);
                    updateSelectAllState();
                    syncHidden();
                    hideAddNewInput();
                }

                if (addNewOption) {
                    addNewOption.addEventListener('click', (e) => {
                        e.stopPropagation();
                        showAddNewInput('');
                    });
                }

                if (addConfirmBtn) {
                    addConfirmBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        if (addNewInput && addNewInput.value.trim()) addNewItem(addNewInput.value.trim());
                    });
                }

                if (addCancelBtn) {
                    addCancelBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        hideAddNewInput();
                        searchInput.focus();
                    });
                }

                if (addNewInput) {
                    addNewInput.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            if (addNewInput.value.trim()) addNewItem(addNewInput.value.trim());
                        }
                    });
                }

                const oldValue = hiddenField.value || '';
                if (oldValue) {
                    oldValue.split(',').map(v => v.trim()).filter(Boolean).forEach(v => {
                        if (!selectedValues.includes(v)) { selectedValues.push(v); addChip(v); updateItemState(v); }
                    });
                    updateSelectAllState();
                    syncHidden();
                } else {
                    root.classList.add('empty');
                }
            });
        </script>

        <script>
            // === 🔹 MODAL DETAIL AGENDA ===
            function openShowAgendaModal(data) {
                // Gunakan fungsi global fillAgendaModal jika tersedia
                if (typeof window.fillAgendaModal === 'function') {
                    window.fillAgendaModal(data);
                } else {
                    // Fallback: isi manual jika fungsi global belum tersedia
                    document.getElementById('showAgendaName').innerText = data.agenda_name ?? '-';
                    document.getElementById('showAgendaDate').innerText = formatDate(data.date);

                    const timeText = (data.start_time && data.end_time) ?
                        `${data.start_time} - ${data.end_time}` :
                        (data.start_time ?? '-');
                    document.getElementById('showAgendaTime').innerText = timeText;

                    document.getElementById('showAgendaLocation').innerText = data.location ?? '-';
                    document.getElementById('showAgendaDesc').innerText = data.description ?? '-';

                    // Isi data instansi
                    const involved = data.involved_institution ?? '-';
                    const unitName = data.unit && data.unit.unit_name ? data.unit.unit_name : '-';

                    const unitEl = document.getElementById('showAgendaUnit');
                    if (unitEl) unitEl.innerText = unitName;

                    const involvedEl = document.getElementById('showAgendaInvolved');
                    if (involvedEl) involvedEl.innerText = involved;

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
            // ✅ INI DITARO DI LUAR
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

                agendaList.forEach((item, index) => {
                    const itemDiv = document.createElement("div");
                    itemDiv.className = "agenda-item";

                    const header = document.createElement("div");
                    header.className = "agenda-header";
                    header.innerHTML = `
                    <span class="agenda-item-title">${item.agenda_name}</span>
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
                    duration: 3000,
                    gravity: "top",
                    position: "right", // 🔥 pindahkan ke kanan biar lebih elegan
                    close: false,
                    stopOnFocus: true,
                    offset: {
                        x: 20,
                        y: 70
                    },
                    style: {
                        background: "#ECFDF5", // 💚 hijau pastel lembut (tanpa gradien)
                        border: "1px solid #A7F3D0",
                        borderRadius: "10px",
                        padding: "14px 22px",
                        boxShadow: "0 4px 12px rgba(0,0,0,0.05)",
                        display: "flex",
                        justifyContent: "center",
                        alignItems: "center",
                    }
                }).showToast();
            }
        </script>
    @endsection
