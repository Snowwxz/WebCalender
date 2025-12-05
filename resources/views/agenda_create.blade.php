@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/agenda-create.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
@endpush

@section('content')
    <div class="agenda-container">
        <div class="agenda-form-card">

            <!-- Bagian header -->
            <div style="position: relative; text-align: center; margin-bottom: 8px;">
                <!-- Tombol kembali -->
                <a href="{{ url('/dashboard/bulan') }}" class="back-btn" title="Kembali ke Dashboard">
                    <i class="fas fa-arrow-left"></i>
                </a>

                <!-- Judul di tengah -->
                <div class="agenda-title" style="display:inline-block; font-weight:600; font-size:1.4rem; color:#333;">
                    <i class="fas fa-calendar-plus"></i> Sistem Pengajuan Agenda
                </div>
            </div>

            <p class="agenda-subtitle" style="text-align:center;">
                Platform untuk mengajukan dan mengelola agenda kegiatan instansi
            </p>

            <form action="{{ route('agenda.store') }}" method="POST" id="agendaForm">
                @csrf

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
                        showSuccessToast("{{ session('success') }}");
                    </script>
                @endif

                @if (session('error'))
                    <script>
                        showErrorToast("{{ session('error') }}");
                    </script>
                @endif


                <!-- Page 1: Basic Agenda Info -->
                <div class="form-page" id="page1">
                <div class="form-grid">

                    <!-- Pindahkan ke bawah sini -->
                    <div class="input-group fullwidth-group">
                        <label><i class="fas fa-file-alt"></i> Nama Agenda</label>
                        <input type="text" name="agenda_name" placeholder="Masukkan nama agenda"
                            value="{{ old('agenda_name') }}" required>
                    </div>

                    <div class="input-group fullwidth-group">
                        <label><i class="fas fa-align-left"></i> Deskripsi Agenda</label>
                        <textarea name="description" placeholder="Masukkan deskripsi agenda" required>{{ old('description') }}</textarea>
                    </div>

                    <!-- Kolom kiri -->
                    <div class="form-column">
                        <div class="input-group">
                            <label><i class="fas fa-building"></i> Pelaksana</label>
                            <input type="text" value="{{ $unitName }}" readonly>
                            <input type="hidden" name="id_unit" value="{{ Auth::user()->id_unit }}">
                        </div>

                        <div class="input-group">
                            <label><i class="fas fa-eye"></i> Kategori Agenda</label>
                            <select name="is_public">
                                <option value="1">Publik</option>
                                <option value="0">Privasi</option>
                            </select>
                        </div>

                        <div class="input-group lokasi-group">
                            <label for="lokasi"><i class="fas fa-map-marker-alt"></i> Lokasi</label>
                            <input type="text" id="location" name="location" placeholder="Masukkan lokasi kegiatan">
                        </div>
                    </div>

                    <!-- Kolom kanan -->
                    <div class="form-column">
                        <div class="input-group">
                            <label><i class="fas fa-calendar-day"></i> Tanggal</label>
                            <input type="date" name="date" required>
                        </div>

                        <div class="input-group">
                            <label><i class="fas fa-clock"></i> Waktu Mulai</label>
                            <input type="time" name="start_time" required>
                        </div>

                        <div class="input-group">
                            <label><i class="fas fa-clock"></i> Waktu Selesai</label>
                            <input type="time" name="end_time" required>
                        </div>
                    </div>


                    <!-- Catatan - Full Width -->
                    <div class="input-group fullwidth-group">
                        <label><i class="fa-solid fa-file-lines" style="color:#6b8f71;"></i> Catatan</label>
                        <textarea name="notes" placeholder="Masukkan catatan tambahan (opsional)" rows="3">{{ old('notes') }}</textarea>
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
                        Ajukan Agenda
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Toggle sidebar
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
        }

        // Switch view
        function switchView(view) {
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        // User dropdown functionality
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            const profile = document.querySelector('.user-profile');

            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
                profile.classList.remove('active');
            } else {
                dropdown.classList.add('show');
                profile.classList.add('active');
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const profileSection = document.querySelector('.user-profile-section');
            const dropdown = document.getElementById('userDropdown');
            const profile = document.querySelector('.user-profile');

            if (profileSection && !profileSection.contains(event.target)) {
                dropdown.classList.remove('show');
                profile.classList.remove('active');
            }
        });

        // Multi-page form navigation
        let currentPage = 1;
        const totalPages = 2;

        function showPage(page) {
            document.querySelectorAll('.form-page').forEach((p, idx) => {
                p.style.display = idx + 1 === page ? 'block' : 'none';
            });

            // Update navigation buttons
            document.getElementById('prevPageBtn').style.display = page > 1 ? 'block' : 'none';
            document.getElementById('nextPageBtn').style.display = page < totalPages ? 'block' : 'none';
            document.getElementById('submitBtn').style.display = page === totalPages ? 'block' : 'none';

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

        document.getElementById('nextPageBtn').addEventListener('click', function() {
            if (validatePage1()) {
                showPage(2);
            }
        });

        document.getElementById('prevPageBtn').addEventListener('click', function() {
            showPage(1);
        });

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

        // Form validation on submit - akan dipanggil sebelum konfirmasi
        function validateAndPrepareForm() {
            if (!validatePage1()) {
                showPage(1);
                return false;
            }

            // Validate session names if group mode
            const hasGroup = document.querySelector('input[name="has_group"]:checked')?.value === '1';
            if (hasGroup) {
                let isValid = true;
                const sessionItems = document.querySelectorAll('.session-item');
                sessionItems.forEach((sessionEl, index) => {
                    const sessionNameInput = sessionEl.querySelector('.session-name-input');
                    const sessionName = sessionNameInput?.value?.trim();
                    if (!sessionName) {
                        isValid = false;
                        if (sessionNameInput) {
                            sessionNameInput.style.borderColor = '#ef4444';
                            sessionNameInput.style.backgroundColor = '#fef2f2';
                        }
                    } else {
                        if (sessionNameInput) {
                            sessionNameInput.style.borderColor = '';
                            sessionNameInput.style.backgroundColor = '';
                        }
                    }
                });
                
                if (!isValid) {
                    showPage(2);
                    alert('Mohon lengkapi semua nama sesi yang wajib diisi.');
                    return false;
                }
            }

            // Collect session data
            try {
                collectSessionData();
                return true;
            } catch (error) {
                showPage(2);
                alert(error.message);
                return false;
            }
        }

        function collectSessionData() {
            const hasGroupRadio = document.querySelector('input[name="has_group"]:checked');
            if (!hasGroupRadio) {
                // Default ke normal jika tidak ada yang dipilih
                const sessions = [];
                const unitIds = [];
                document.querySelectorAll('#normalInvolvedInstansi .chip[data-unit-id]').forEach(chip => {
                    unitIds.push(chip.getAttribute('data-unit-id'));
                });
                if (unitIds.length > 0) {
                    sessions.push({
                        session_name: null,
                        invited_units: unitIds
                    });
                }
                // Add hidden input for sessions
                const existingInput = document.querySelector('input[name="sessions_data"]');
                if (existingInput) existingInput.remove();
                const sessionsInput = document.createElement('input');
                sessionsInput.type = 'hidden';
                sessionsInput.name = 'sessions_data';
                sessionsInput.value = JSON.stringify(sessions);
                const form = document.getElementById('agendaForm');
                if (form) form.appendChild(sessionsInput);
                return;
            }
            const hasGroup = hasGroupRadio.value === '1';
            const sessions = [];

            if (hasGroup) {
                // Collect from group sessions
                document.querySelectorAll('.session-item').forEach((sessionEl, index) => {
                    const sessionNameInput = sessionEl.querySelector('.session-name-input');
                    const sessionName = sessionNameInput?.value?.trim();
                    
                    // Validate session name is required
                    if (!sessionName) {
                        sessionNameInput.style.borderColor = '#ef4444';
                        sessionNameInput.style.backgroundColor = '#fef2f2';
                        throw new Error(`Nama sesi ${index + 1} wajib diisi`);
                    } else {
                        if (sessionNameInput) {
                            sessionNameInput.style.borderColor = '';
                            sessionNameInput.style.backgroundColor = '';
                        }
                    }
                    
                    const unitIds = [];
                    sessionEl.querySelectorAll('.unit-chip[data-unit-id]').forEach(chip => {
                        unitIds.push(chip.getAttribute('data-unit-id'));
                    });

                    if (unitIds.length > 0) {
                        sessions.push({
                            session_name: sessionName,
                            invited_units: unitIds
                        });
                    }
                });
            } else {
                // Collect from normal invitation
                const unitIds = [];
                document.querySelectorAll('#normalInvolvedInstansi .chip[data-unit-id]').forEach(chip => {
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
            const existingInput = document.querySelector('input[name="sessions_data"]');
            if (existingInput) {
                existingInput.remove();
            }

            const sessionsInput = document.createElement('input');
            sessionsInput.type = 'hidden';
            sessionsInput.name = 'sessions_data';
            sessionsInput.value = JSON.stringify(sessions);
            document.getElementById('agendaForm').appendChild(sessionsInput);
        }

        // Real-time validation
        document.querySelectorAll('input[required], textarea[required], select[required]').forEach(field => {
            field.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.style.borderColor = '#ef4444';
                    this.style.backgroundColor = '#fef2f2';
                } else {
                    this.style.borderColor = '#10b981';
                    this.style.backgroundColor = '#f0fdf4';
                }
            });

            field.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    this.style.borderColor = '#10b981';
                    this.style.backgroundColor = '#f0fdf4';
                }
            });
        });

        // Toggle Group functionality
        document.querySelectorAll('input[name="has_group"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const hasGroup = this.value === '1';
                document.getElementById('normalInvitation').style.display = hasGroup ? 'none' : 'block';
                document.getElementById('groupInvitation').style.display = hasGroup ? 'block' : 'none';
                document.getElementById('addSessionBtn').style.display = hasGroup ? 'block' : 'none';
                
                if (hasGroup && document.querySelectorAll('.session-item').length === 0) {
                    addSession(1);
                    addSession(2);
                }
            });
        });

        // Add session functionality
        let sessionCount = 0;
        document.getElementById('addSessionBtn').addEventListener('click', function() {
            sessionCount++;
            addSession(sessionCount + 2);
        });

        function addSession(sessionNumber) {
            const container = document.getElementById('sessionsContainer');
            const sessionDiv = document.createElement('div');
            sessionDiv.className = 'session-item';
            sessionDiv.innerHTML = `
                <div class="input-group fullwidth-group" style="margin-bottom: 16px; border: 1px solid #e5e7eb; padding: 16px; border-radius: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <label style="margin: 0;"><i class="fas fa-layer-group"></i> Dihadiri Sesi ${sessionNumber}</label>
                        ${sessionNumber > 2 ? '<button type="button" class="remove-session-btn" style="background: #fee2e2; color: #991b1b; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer;"><i class="fas fa-times"></i></button>' : ''}
                    </div>
                    <input type="text" class="session-name-input" placeholder="Nama Sesi (wajib)" required style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; margin-bottom: 12px;">
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

        // Old code removed - now using initChipsMultiselect function
            const searchInput = root.querySelector('.chips-search-input');
            const mainInput = root.querySelector('.chips-input');
            const selectedWrap = root.querySelector('.chips-selected');
            const hiddenField = document.getElementById('involvedInstitutionField');
            // Ambil list items, tapi exclude opsi "Tambah Instansi Baru"
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
                    // Tampilkan kembali opsi "Tambah Instansi Baru"
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
                    // Clear search and reset filter when opening
                    searchInput.value = '';
                    filterList('');
                    hideAddNewInput();
                    // Pastikan opsi "Tambah Instansi Baru" ditampilkan
                    const addNewInstansiOption = dropdown.querySelector('.add-new-instansi-option');
                    if (addNewInstansiOption) {
                        addNewInstansiOption.style.display = 'flex';
                    }
                    searchInput.focus();
                    // Restore all items visibility and update states
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
                // Clear search when closing
                searchInput.value = '';
                // Pastikan opsi "Tambah Instansi Baru" ditampilkan saat dropdown ditutup
                const addNewInstansiOption = dropdown.querySelector('.add-new-instansi-option');
                if (addNewInstansiOption) {
                    addNewInstansiOption.style.display = 'flex';
                }
            }

            function toggleItem(value) {
                const index = selectedValues.indexOf(value);
                if (index > -1) {
                    // Remove item
                    selectedValues.splice(index, 1);
                    removeChip(value);
                } else {
                    // Add item
                    selectedValues.push(value);
                    addChip(value);
                }
                updateItemState(value);
                updateSelectAllState();
                syncHidden();
            }

            function addChip(value) {
                // Check if chip already exists
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
                    // Deselect all
                    selectedValues = [];
                    listItems.forEach(li => {
                        const value = li.getAttribute('data-value');
                        removeChip(value);
                        updateItemState(value);
                    });
                } else {
                    // Select all
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

                // toggle input utama
                mainInput.style.display = selectedValues.length ? 'none' : 'inline';

                // toggle class empty buat ubah tampilan awal
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

                // Cegah duplikat - cek di listItems dan selectedValues
                const existsInList = listItems.some(li => li.getAttribute('data-value').toLowerCase() === cleanName.toLowerCase());
                const existsInSelected = selectedValues.some(val => val.toLowerCase() === cleanName.toLowerCase());

                if (existsInList || existsInSelected) {
                    alert('Instansi sudah ada!');
                    if (addNewInput) {
                        addNewInput.value = '';
                        addNewInput.focus();
                    }
                    return;
                }

                // Jangan tambahkan ke dropdown list, hanya tambahkan sebagai chip yang dipilih
                // Tambahkan langsung ke selected
                selectedValues.push(cleanName);
                addChip(cleanName);
                updateSelectAllState();
                syncHidden();

                // Kosongkan input dan sembunyikan container
                hideAddNewInput();

                // Filter list untuk reset tampilan
                filterList('');

                // Toast notifikasi ringan
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

                // Filter existing items
                listItems.forEach(li => {
                    const text = li.querySelector('.item-text').textContent.toLowerCase();
                    const match = !lower || text.includes(lower);
                    li.style.display = match ? 'flex' : 'none';
                });

                // Tampilkan "Pilih Semua" jika ada hasil atau tidak ada search term
                if (!lower || listItems.some(li => {
                    const text = li.querySelector('.item-text').textContent.toLowerCase();
                    return text.includes(lower);
                })) {
                    selectAllOption.style.display = 'flex';
                } else {
                    selectAllOption.style.display = 'none';
                }

                // Tampilkan opsi "Tambah Instansi Baru" kecuali sedang menampilkan input field
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

            // Event listener untuk opsi "Tambah Instansi Baru"
            const addNewInstansiOption = dropdown.querySelector('.add-new-instansi-option');
            if (addNewInstansiOption) {
                addNewInstansiOption.addEventListener('click', (e) => {
                    e.stopPropagation();
                    // Tampilkan input field
                    showAddNewInput('');
                    // Sembunyikan opsi "Tambah Instansi Baru" saat input field ditampilkan
                    addNewInstansiOption.style.display = 'none';
                    // Sembunyikan "Pilih Semua" saat input field ditampilkan
                    selectAllOption.style.display = 'none';
                });
            }

            // Event listener untuk input field baru
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

            // Event listener untuk tombol Tambahkan
            if (addConfirmBtn) {
                addConfirmBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (addNewInput && addNewInput.value.trim()) {
                        addNewItem(addNewInput.value.trim());
                        selectAllOption.style.display = 'flex';
                    }
                });
            }

            // Event listener untuk tombol Batal
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

    </script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <script>
        function showConfirmSubmit() {
            const toastContent = document.createElement('div');
            toastContent.innerHTML = `
                <div style="
                    font-family: 'Poppins', sans-serif;
                    color: #2F3E35;
                    font-weight: 500;
                    font-size: 15px;
                    margin-bottom: 12px;
                ">
                    Apakah yakin ingin mengajukan agenda?
                </div>
                <div style="display: flex; gap: 8px; justify-content: center;">
                    <button id="confirmSubmit" style="
                        background: #F7B7B7;
                        border: none;
                        padding: 7px 16px;
                        border-radius: 8px;
                        color: #7A1C1C;
                        font-weight: 600;
                        font-family: 'Poppins', sans-serif;
                        cursor: pointer;
                        transition: all 0.25s ease;
                    "
                    onmouseover="this.style.background='#F4A8A8'; this.style.color='#691414';"
                    onmouseout="this.style.background='#F7B7B7'; this.style.color='#7A1C1C';"
                    onmousedown="this.style.background='#E68D8D'; this.style.color='#5C1111';"
                    onmouseup="this.style.background='#F4A8A8'; this.style.color='#691414';">
                        Ya, ajukan
                    </button>

                    <button id="cancelSubmit" style="
                        background: #E6E7E8;
                        border: none;
                        padding: 7px 16px;
                        border-radius: 8px;
                        color: #2F3E35;
                        font-weight: 600;
                        font-family: 'Poppins', sans-serif;
                        cursor: pointer;
                        transition: all 0.2s ease;
                    "
                    onmouseover="this.style.background='#D9DADB';"
                    onmouseout="this.style.background='#E6E7E8';">
                        Batal
                    </button>
                </div>
            `;

            const toast = Toastify({
                node: toastContent,
                duration: -1,
                gravity: "top",
                position: "center",
                stopOnFocus: true,
                close: false,
                offset: {
                    x: 0,
                    y: 20
                },
                style: {
                    background: "#FFF1E6",
                    border: "1px solid #F7B7B7",
                    borderRadius: "12px",
                    padding: "18px 24px",
                    boxShadow: "0 6px 20px rgba(0, 0, 0, 0.08)",
                    textAlign: "center",
                    display: "flex",
                    flexDirection: "column",
                    alignItems: "center",
                    animation: "fadeIn 0.3s ease",
                },
            }).showToast();

            toastContent.querySelector('#confirmSubmit').addEventListener('click', () => {
                toast.hideToast(); // tutup konfirmasi
                
                // Pastikan data sudah dikumpulkan sebelum submit
                try {
                    collectSessionData();
                } catch (error) {
                    alert(error.message);
                    return;
                }
                
                // Submit form langsung menggunakan FormData dan fetch
                const form = document.getElementById('agendaForm');
                if (form) {
                    const formData = new FormData(form);
                    
                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => {
                        if (response.redirected) {
                            // Jika redirect, berarti berhasil
                            window.location.href = response.url;
                        } else if (response.ok) {
                            return response.json();
                        } else {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Gagal menyimpan agenda');
                            });
                        }
                    })
                    .then(data => {
                        if (data && data.success) {
                            window.location.href = "{{ route('agenda.notification') }}";
                        } else {
                            alert(data.message || 'Agenda berhasil diajukan!');
                            window.location.href = "{{ route('agenda.notification') }}";
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(error.message || 'Terjadi kesalahan saat menyimpan agenda');
                    });
                }
            });

            toastContent.querySelector('#cancelSubmit').addEventListener('click', () => {
                toast.hideToast();
            });
        }

        // Flag untuk menandai submit yang sudah divalidasi
        let isFormValidated = false;
        
        // intercept tombol submit bawaan form
        const formElement = document.getElementById("agendaForm");
        if (formElement) {
            formElement.addEventListener("submit", function(e) {
                // Jika sudah divalidasi, biarkan submit berjalan tanpa preventDefault
                if (isFormValidated) {
                    isFormValidated = false; // Reset flag
                    // Tidak perlu preventDefault, biarkan form submit normal
                    return; // Exit early, tidak preventDefault
                }
                
                // Jika belum divalidasi, cegah submit dan validasi dulu
                e.preventDefault();
                e.stopPropagation();
                
                // Validasi dan persiapan data terlebih dahulu
                if (!validateAndPrepareForm()) {
                    return false; // Jika validasi gagal, jangan lanjutkan
                }
                
                showConfirmSubmit(); // munculkan toast konfirmasi
                return false;
            });
        }

        // ======== TOAST SUKSES (senada tema hijau pastel) ========
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
