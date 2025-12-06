@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/agenda-create.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')
    <div class="agenda-container">
        <div class="agenda-form-card">

            <!-- Bagian header -->
            <div style="position: relative; text-align: center; margin-bottom: 8px;">
                <!-- Tombol kembali -->
                <a href="{{ request('from') === 'approve' ? route('approve') : route('agenda.notification') }}" class="back-btn"
                    title="Kembali">
                    <i class="fas fa-arrow-left"></i>
                </a>


                <!-- Judul di tengah -->
                <div class="agenda-title" style="display:inline-block; font-weight:600; font-size:1.4rem; color:#333;">
                    <i class="fas fa-edit"></i> Edit Agenda
                </div>
            </div>

            <p class="agenda-subtitle" style="text-align:center;">
                Perbarui data agenda kegiatan instansi Anda
            </p>

            <form action="{{ route('agenda.update', $agenda->id_agenda) }}" method="POST" id="agendaForm">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="alert alert-danger"
                        style="background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                        <h4>Terjadi kesalahan:</h4>
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            showSuccessToast("{{ session('success') }}");
                        });
                    </script>
                @endif

                @if (session('error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            showErrorToast("{{ session('error') }}");
                        });
                    </script>
                @endif


                <!-- Page 1: Basic Agenda Info -->
                <div class="form-page" id="page1">
                <div class="form-grid">

                    <!-- Pindahkan ke bawah sini -->
                    <div class="input-group fullwidth-group">
                        <label><i class="fas fa-file-alt"></i> Nama Agenda</label>
                        <input type="text" name="agenda_name" placeholder="Masukkan nama agenda"
                            value="{{ old('agenda_name', $agenda->agenda_name) }}" required>
                    </div>

                    <div class="input-group fullwidth-group">
                        <label><i class="fas fa-align-left"></i> Deskripsi Agenda</label>
                        <textarea name="description" placeholder="Masukkan deskripsi agenda" required>{{ old('description', $agenda->description) }}</textarea>
                    </div>

                    <!-- Kolom kiri -->
                    <div class="form-column">
                        <div class="input-group">
                            <label><i class="fas fa-building"></i> Pelaksana</label>
                            <input type="text" value="{{ $unitName }}" readonly>
                            <input type="hidden" name="id_unit" value="{{ $agenda->id_unit }}">
                        </div>

                        <div class="input-group">
                            <label><i class="fas fa-eye"></i> Kategori Agenda</label>
                            <select name="is_public">
                                <option value="1" {{ old('is_public', $agenda->is_public) == 1 ? 'selected' : '' }}>
                                    Publik</option>
                                <option value="0" {{ old('is_public', $agenda->is_public) == 0 ? 'selected' : '' }}>
                                    Privasi</option>
                            </select>
                        </div>

                        <div class="input-group lokasi-group">
                            <label for="lokasi"><i class="fas fa-map-marker-alt"></i> Lokasi</label>
                            <input type="text" id="location" name="location" placeholder="Masukkan lokasi kegiatan"
                                value="{{ old('location', $agenda->location) }}">
                        </div>
                    </div>

                    <!-- Kolom kanan -->
                    <div class="form-column">
                        <div class="input-group">
                            <label><i class="fas fa-calendar-day"></i> Tanggal</label>
                            <input type="date" name="date" value="{{ old('date', $agenda->date) }}" required>
                        </div>

                        <div class="input-group">
                            <label><i class="fas fa-clock"></i> Waktu Mulai</label>
                            <input type="time" name="start_time" value="{{ old('start_time', $agenda->start_time) }}"
                                required>
                        </div>

                        <div class="input-group">
                            <label><i class="fas fa-clock"></i> Waktu Selesai</label>
                            <input type="time" name="end_time" value="{{ old('end_time', $agenda->end_time) }}"
                                required>
                        </div>
                    </div>

                    <!-- Catatan - Full Width -->
                    <div class="input-group fullwidth-group">
                        <label><i class="fa-solid fa-file-lines" style="color:#6b8f71;"></i> Catatan</label>
                        <textarea name="notes" placeholder="Masukkan catatan tambahan (opsional)" rows="3">{{ old('notes', $agenda->notes) }}</textarea>
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
                    <div style="flex: 1; display: flex; justify-content: center; gap: 8px;">
                        <span class="page-indicator active" data-page="1">1</span>
                        <span class="page-indicator" data-page="2">2</span>
                    </div>
                    <button type="button" id="nextPageBtn" class="btn-primary">
                        Lanjutkan <i class="fas fa-arrow-right"></i>
                    </button>
                    <button type="submit" id="submitBtn" class="btn-primary" style="display: none;">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Multi-page form navigation
        let currentPage = 1;
        const totalPages = 2;

        function showPage(page) {
            document.querySelectorAll('.form-page').forEach((p, idx) => {
                p.style.display = idx + 1 === page ? 'block' : 'none';
            });

            // Update navigation buttons
            const prevBtn = document.getElementById('prevPageBtn');
            const nextBtn = document.getElementById('nextPageBtn');
            const submitBtn = document.getElementById('submitBtn');

            if (prevBtn) prevBtn.style.display = page > 1 ? 'block' : 'none';
            if (nextBtn) nextBtn.style.display = page < totalPages ? 'block' : 'none';
            if (submitBtn) submitBtn.style.display = page === totalPages ? 'block' : 'none';

            // Update page indicators
            document.querySelectorAll('.page-indicator').forEach((indicator, idx) => {
                if (idx + 1 === page) {
                    indicator.classList.add('active');
                } else {
                    indicator.classList.remove('active');
                }
            });

            currentPage = page;
        }

        function validatePage1() {
            const requiredFields = ['agenda_name', 'description', 'id_unit', 'date', 'start_time', 'end_time'];
            let isValid = true;
            let emptyFields = [];

            requiredFields.forEach(fieldName => {
                const field = document.querySelector(`[name="${fieldName}"]`);
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
                alert('Mohon lengkapi semua field yang wajib diisi:\n\n' +
                    emptyFields.map(field => {
                        const labels = {
                            'agenda_name': 'Nama Agenda',
                            'description': 'Deskripsi Agenda',
                            'id_unit': 'Nama Instansi',
                            'date': 'Tanggal',
                            'start_time': 'Waktu Mulai',
                            'end_time': 'Waktu Selesai'
                        };
                        return '• ' + (labels[field] || field);
                    }).join('\n'));
            }

            return isValid;
        }

        // Initialize page navigation
        document.addEventListener('DOMContentLoaded', function() {
            const nextPageBtn = document.getElementById('nextPageBtn');
            const prevPageBtn = document.getElementById('prevPageBtn');

            if (nextPageBtn) {
                nextPageBtn.addEventListener('click', function() {
                    if (validatePage1()) {
                        showPage(2);
                    }
                });
            }

            if (prevPageBtn) {
                prevPageBtn.addEventListener('click', function() {
                    showPage(1);
                });
            }

            // Form validation on submit
            const form = document.getElementById('agendaForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (!validatePage1()) {
                        e.preventDefault();
                        showPage(1);
                        return;
                    }

                    // Collect session data
                    collectSessionData();
                });
            }

            // Initialize page 1
            showPage(1);
        });

        function collectSessionData() {
            const hasGroup = document.querySelector('input[name="has_group"]:checked')?.value === '1';
            const sessions = [];

            if (hasGroup) {
                // Collect from group sessions
                document.querySelectorAll('.session-item').forEach((sessionEl, index) => {
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
                const normalRoot = document.getElementById('normalInvolvedInstansi');
                if (normalRoot) {
                    normalRoot.querySelectorAll('.unit-chip[data-unit-id]').forEach(chip => {
                        unitIds.push(chip.getAttribute('data-unit-id'));
                    });
                }

                if (unitIds.length > 0) {
                    sessions.push({
                        session_name: null,
                        invited_units: unitIds
                    });
                }
            }

            // Add hidden input for sessions
            const existingInput = document.querySelector('input[name="sessions_data"]');
            if (existingInput) {
                existingInput.remove();
            }

            const sessionsInput = document.createElement('input');
            sessionsInput.type = 'hidden';
            sessionsInput.name = 'sessions_data';
            sessionsInput.value = JSON.stringify(sessions);
            const form = document.getElementById('agendaForm');
            if (form) {
                form.appendChild(sessionsInput);
            }
        }

        // Toggle Group functionality
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[name="has_group"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const hasGroup = this.value === '1';
                    const normalInv = document.getElementById('normalInvitation');
                    const groupInv = document.getElementById('groupInvitation');
                    const addSessionBtn = document.getElementById('addSessionBtn');

                    if (normalInv) normalInv.style.display = hasGroup ? 'none' : 'block';
                    if (groupInv) groupInv.style.display = hasGroup ? 'block' : 'none';
                    if (addSessionBtn) addSessionBtn.style.display = hasGroup ? 'block' : 'none';

                    if (hasGroup && document.querySelectorAll('.session-item').length === 0) {
                        addSession(1);
                        addSession(2);
                    }
                });
            });

            // Add session functionality
            let sessionCount = 0;
            const addSessionBtn = document.getElementById('addSessionBtn');
            if (addSessionBtn) {
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

        // Initialize chips multiselect function (sama seperti di agenda_create)
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

            function getUsedUnitIds(excludeRoot) {
                const used = [];
                document.querySelectorAll('.session-units-select').forEach(sel => {
                    if (excludeRoot && excludeRoot === sel) return;
                    sel.querySelectorAll('.unit-chip[data-unit-id]').forEach(chip => {
                        used.push(chip.getAttribute('data-unit-id'));
                    });
                });
                return used;
            }

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
                    if (selectAllOption) selectAllOption.style.display = 'flex';
                    updateSelectAllState();
                }
            }

            function closeDropdown() {
                dropdown.classList.remove('open');
                root.classList.remove('open');
                searchInput.value = '';
            }

            function toggleItem(unitId, unitName) {
                const usedElsewhere = getUsedUnitIds(root);
                if (usedElsewhere.includes(unitId)) {
                    return;
                }
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
                    const used = getUsedUnitIds(root);
                    const isUsedElsewhere = used.includes(unitId);
                    item.style.opacity = isUsedElsewhere ? '0.5' : '';
                    item.style.pointerEvents = isUsedElsewhere ? 'none' : '';
                }
            }

            function updateSelectAllState() {
                const allSelected = listItems.length > 0 && listItems.length === selectedUnitIds.length;
                if (selectAllOption) {
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
                    const used = getUsedUnitIds(root);
                    listItems.forEach(li => {
                        const unitId = li.getAttribute('data-value');
                        const unitName = li.getAttribute('data-name');
                        if (!selectedUnitIds.includes(unitId) && !used.includes(unitId)) {
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
                    const used = getUsedUnitIds(root);
                    const unitId = li.getAttribute('data-value');
                    const match = !lower || text.includes(lower);
                    li.style.display = match ? 'flex' : 'none';
                    const disabled = used.includes(unitId);
                    li.style.opacity = disabled ? '0.5' : '';
                    li.style.pointerEvents = disabled ? 'none' : '';
                });
                if (selectAllOption) {
                    selectAllOption.style.display = !lower || listItems.some(li => {
                        const text = li.querySelector('.item-text').textContent.toLowerCase();
                        return text.includes(lower);
                    }) ? 'flex' : 'none';
                }
            }

            searchInput.addEventListener('input', e => filterList(e.target.value));
            arrow.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleDropdown();
            });
            container.addEventListener('click', (e) => {
                if (e.target.closest('.chip-remove')) return;
                if (e.target !== searchInput) toggleDropdown();
            });
            if (selectAllOption) {
                selectAllOption.addEventListener('click', (e) => {
                    e.stopPropagation();
                    selectAll();
                });
            }
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

            // Initialize existing invitations
            @if(isset($invitations) && !empty($invitations))
                const existingInvitations = @json($invitations);

                if (existingInvitations && existingInvitations.length > 0) {
                    // Check if any invitation has session_name (group mode)
                    const hasSessions = existingInvitations.some(inv => inv.session_name !== null) || existingInvitations.length > 1;

                    if (hasSessions) {
                        // Group mode - select the radio button
                        const withGroupRadio = document.getElementById('withGroup');
                        const noGroupRadio = document.getElementById('noGroup');
                        if (withGroupRadio && noGroupRadio) {
                            withGroupRadio.checked = true;
                            noGroupRadio.checked = false;

                            const normalInv = document.getElementById('normalInvitation');
                            const groupInv = document.getElementById('groupInvitation');
                            const addSessionBtn = document.getElementById('addSessionBtn');

                            if (normalInv) normalInv.style.display = 'none';
                            if (groupInv) groupInv.style.display = 'block';
                            if (addSessionBtn) addSessionBtn.style.display = 'block';

                            // Add sessions
                            existingInvitations.forEach((inv, index) => {
                                const sessionNumber = index + 1;
                                addSession(sessionNumber);

                                // Populate immediately
                                    const sessionItems = document.querySelectorAll('.session-item');
                                    const currentSession = sessionItems[index];
                                    if (currentSession) {
                                        const sessionNameInput = currentSession.querySelector('.session-name-input');
                                        if (sessionNameInput && inv.session_name) {
                                            sessionNameInput.value = inv.session_name;
                                        }

                                        // Populate units
                                        const sessionSelect = currentSession.querySelector('.session-units-select');
                                        if (sessionSelect && inv.units && inv.units.length > 0) {
                                            inv.units.forEach(unit => {
                                                const unitItem = sessionSelect.querySelector(`li[data-value="${unit.id_unit}"]`);
                                                if (unitItem) {
                                                    unitItem.click();
                                                }
                                            });
                                        }
                                    }
                            });
                        }
                    } else {
                        // Normal mode - populate normal invitation
                        const firstInv = existingInvitations[0];
                        if (firstInv && firstInv.units && firstInv.units.length > 0) {
                            const normalRoot = document.getElementById('normalInvolvedInstansi');
                            if (normalRoot) {
                                firstInv.units.forEach(unit => {
                                    const unitItem = normalRoot.querySelector(`li[data-value="${unit.id_unit}"]`);
                                    if (unitItem) {
                                        unitItem.click();
                                    }
                                });
                            }
                        }
                    }
                }
            @endif
        });


    </script>

    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
        // Fungsi Toastify Success
        function showSuccessToast(message) {
            const popup = document.createElement('div');
            popup.className = 'toastify-popup toastify-success';
            popup.innerHTML = `
                <i class="bi bi-check-circle-fill" style="color:#5FA776; font-size:16px;"></i>
                <span>${message}</span>
            `;
            document.body.appendChild(popup);

            popup.classList.add('toastify-popup-show');
            setTimeout(() => {
                popup.classList.remove('toastify-popup-show');
                popup.classList.add('toastify-popup-hide');
                setTimeout(() => popup.remove(), 300);
            }, 2500);
        }

        // Fungsi Toastify Error
        function showErrorToast(message) {
            const popup = document.createElement('div');
            popup.className = 'toastify-popup toastify-error';
            popup.innerHTML = `
                <i class="bi bi-x-circle-fill" style="color:#EF4444; font-size:16px;"></i>
                <span>${message}</span>
            `;
            document.body.appendChild(popup);

            popup.classList.add('toastify-popup-show');
            setTimeout(() => {
                popup.classList.remove('toastify-popup-show');
                popup.classList.add('toastify-popup-hide');
                setTimeout(() => popup.remove(), 300);
            }, 3000);
        }

        function showConfirmSubmit() {
            const toast = Toastify({
                text: "",
                duration: -1,
                close: false,
                gravity: "top",
                position: "center",
                stopOnFocus: true,
                escapeMarkup: false,
                style: {
                    background: "#FFF1E6",
                    border: "1px solid #F7B7B7",
                    borderRadius: "10px",
                    boxShadow: "0 4px 10px rgba(0,0,0,0.05)",
                    padding: "20px 30px",
                    textAlign: "center",
                    color: "#333",
                    fontFamily: "Poppins, sans-serif",
                    animation: "fadeIn 0.3s ease",
                },
                onClick: function() {} // biar nggak nutup waktu diklik
            }).showToast();

            // ambil elemen toast yg baru muncul
            const toastEl = document.querySelector(".toastify");
            if (!toastEl) {
                console.error('Elemen toast tidak ditemukan');
                return;
            }

            toastEl.innerHTML = `
                        <div style="display:flex; flex-direction:column; align-items:center; gap:16px;">
                            <span style="font-size:1rem; font-weight:500;">Apakah yakin ingin mengajukan agenda?</span>
                            <div style="display:flex; gap:12px;">
                                <button id="confirmSubmit" style="
                            background:#F7B7B7;
                            border:none;
                            padding:7px 18px;
                            border-radius:8px;
                            color:#7A1C1C;
                            font-weight:600;
                            cursor:pointer;
                            transition:background 0.2s ease;
                        ">Ya, ajukan</button>
                                <button id="cancelSubmit" style="
                            background:#E5E7EB;
                            border:none;
                            padding:7px 18px;
                            border-radius:8px;
                            color:#374151;
                            font-weight:600;
                            cursor:pointer;
                            transition:background 0.2s ease;
                        ">Batal</button>
                            </div>
                        </div>
                    `;

            document.getElementById("confirmSubmit").addEventListener("click", () => {
                toast.hideToast(); // tutup konfirmasi
                document.getElementById("agendaForm").submit(); // kirim form
            });

            document.getElementById("cancelSubmit").addEventListener("click", () => {
                toast.hideToast();
            });
        }

        // intercept tombol submit bawaan form
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById("agendaForm");
            if (form) {
                form.addEventListener("submit", function(e) {
                    e.preventDefault(); // cegah kirim langsung
                    if (typeof showConfirmSubmit === 'function') {
                        showConfirmSubmit(); // munculkan toast konfirmasi
                    } else {
                        console.error('Fungsi showConfirmSubmit tidak tersedia');
                        // Jika fungsi tidak tersedia, submit form secara normal
                        this.submit();
                    }
                });
            } else {
                console.error('Form dengan ID agendaForm tidak ditemukan');
            }
        });
    </script>

    <style>
        /* Multi-page form styles */
        .form-page {
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-navigation {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 2px solid #e5e7eb;
        }

        .page-indicator {
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

        .page-indicator.active {
            background: #6b8f71;
            color: white;
        }

        .radio-option {
            transition: all 0.3s;
        }

        .radio-option:hover {
            border-color: #6b8f71 !important;
            background: #f0fdf4;
        }

        .radio-option input[type="radio"]:checked ~ span {
            color: #6b8f71;
            font-weight: 600;
        }

        .radio-option:has(input[type="radio"]:checked) {
            border-color: #6b8f71 !important;
            background: #f0fdf4;
        }

        .session-item {
            margin-bottom: 16px;
        }

        .btn-secondary {
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

        .btn-secondary:hover {
            background: #d1d5db;
        }

        .invitation-container {
            margin-top: 16px;
        }

        .toastify-success {
            position: fixed;
            top: 90px;
            left: 50%;
            transform: translateX(-50%);
            background: #E9F4EC;
            color: #234B2C;
            border: 1px solid #A6C8A3;
            border-radius: 6px;
            padding: 8px 18px;
            font-size: 14px;
            font-weight: 500;
            font-family: 'Poppins', sans-serif;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            z-index: 9999;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: auto;
            max-width: 300px;
            min-height: unset;
        }

        .toastify-error {
            position: fixed;
            top: 90px;
            left: 50%;
            transform: translateX(-50%);
            background: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FCA5A5;
            border-radius: 6px;
            padding: 8px 18px;
            font-size: 14px;
            font-weight: 500;
            font-family: 'Poppins', sans-serif;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            z-index: 9999;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: auto;
            max-width: 300px;
            min-height: unset;
        }

        .toastify-popup-show {
            animation: toastIn 0.35s ease forwards;
        }

        .toastify-popup-hide {
            animation: toastOut 0.25s ease forwards;
        }

        @keyframes toastIn {
            0% {
                opacity: 0;
                transform: translate(-50%, -30px) scale(0.95);
            }

            80% {
                opacity: 1;
                transform: translate(-50%, 8px) scale(1.03);
            }

            100% {
                opacity: 1;
                transform: translate(-50%, 0) scale(1);
            }
        }

        @keyframes toastOut {
            from {
                opacity: 1;
                transform: translate(-50%, 0) scale(1);
            }

            to {
                opacity: 0;
                transform: translate(-50%, -10px) scale(0.95);
            }
        }
    </style>

@endsection
