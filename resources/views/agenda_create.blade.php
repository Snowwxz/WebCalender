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
                            <input type="text" id="lokasi" name="lokasi" placeholder="Masukkan lokasi kegiatan">
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

                    <!-- Dihadiri - Full Width -->
                    <div class="input-group fullwidth-group">
                        <label><i class="fas fa-people-group"></i> Dihadiri</label>

                        <div class="chips-multiselect" id="involvedInstansi">
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
                                <div class="chips-add-new-input-container" style="display: none; padding: 10px 12px; border-top: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                                    <input type="text" class="chips-add-new-input" placeholder="Ketik nama instansi baru..." style="width: 100%; padding: 8px 12px; border: 1px solid #6b8f71; border-radius: 6px; font-size: 14px; outline: none;" />
                                    <div style="display: flex; gap: 8px; margin-top: 8px;">
                                        <button type="button" class="chips-add-confirm-btn" style="flex: 1; padding: 6px 12px; background: #6b8f71; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 500;">Tambahkan</button>
                                        <button type="button" class="chips-add-cancel-btn" style="flex: 1; padding: 6px 12px; background: #e5e7eb; color: #374151; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 500;">Batal</button>
                                    </div>
                                </div>

                                <ul>
                                    <li class="select-all-option" data-action="select-all">
                                        <span class="check-icon"></span>
                                        <span class="item-text">Pilih Semua</span>
                                        <i class="fas fa-check checkmark-icon"></i>
                                    </li>
                                    @foreach ($units as $unit)
                                        @if ($unit->id_unit !== Auth::user()->id_unit && strtolower($unit->unit_name) !== 'protokol')
                                            <li data-value="{{ $unit->unit_name }}" class="dropdown-item">
                                                <span class="check-icon"></span>
                                                <span class="item-text">{{ $unit->unit_name }}</span>
                                                <i class="fas fa-check checkmark-icon"></i>
                                            </li>
                                        @endif
                                    @endforeach
                                    <li class="add-new-instansi-option" data-action="add-new" style="padding: 10px 12px; cursor: pointer; border-top: 1px solid #e5e7eb; color: #6b8f71; font-weight: 500; display: flex; align-items: center; list-style: none;">
                                        <i class="fas fa-plus-circle" style="margin-right: 8px;"></i>
                                        <span>Lainnya...</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <input type="hidden" name="involved_institution" id="involvedInstitutionField"
                            value="{{ old('involved_institution') }}">
                    </div>

                    <!-- Catatan - Full Width -->
                    <div class="input-group fullwidth-group">
                        <label><i class="fa-solid fa-file-lines" style="color:#6b8f71;"></i> Catatan</label>
                        <textarea name="catatan" placeholder="Masukkan catatan tambahan (opsional)" rows="3">{{ old('catatan') }}</textarea>
                    </div>

                </div>

                <div class="form-submit">
                    <button type="submit" class="btn-primary">Ajukan Agenda</button>
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

        // Form validation
        document.getElementById('agendaForm').addEventListener('submit', function(e) {
            const requiredFields = [
                'agenda_name',
                'description',
                'id_unit',
                'person_in_charge',
                'date',
                'start_time',
                'end_time',
                'location',
                'involved_institution'
            ];

            let isValid = true;
            let emptyFields = [];

            requiredFields.forEach(fieldName => {
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (field && (!field.value || field.value.trim() === '')) {
                    isValid = false;
                    emptyFields.push(fieldName);

                    // Highlight empty field
                    field.style.borderColor = '#ef4444';
                    field.style.backgroundColor = '#fef2f2';
                } else if (field) {
                    // Reset styling for filled fields
                    field.style.borderColor = '';
                    field.style.backgroundColor = '';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi:\n\n' +
                    emptyFields.map(field => {
                        const labels = {
                            'agenda_name': 'Nama Agenda',
                            'description': 'Deskripsi Agenda',
                            'id_unit': 'Nama Instansi',
                            'person_in_charge': 'Penanggung Jawab',
                            'date': 'Tanggal',
                            'start_time': 'Waktu Mulai',
                            'end_time': 'Waktu Selesai',
                            'location': 'Lokasi',
                            'involved_institution': 'Instansi yang Ikut Serta'
                        };
                        return '• ' + (labels[field] || field);
                    }).join('\n'));
            }
        });

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

        (() => {
            const root = document.getElementById('involvedInstansi');
            const dropdown = root.querySelector('.chips-dropdown');
            const arrow = root.querySelector('.chips-arrow');
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

            // Initialize - restore from old values if exists
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

            // Initialize on page load
            initializeValues();
        })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
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
                    background: "#FFF6F2",
                    border: "1px solid #FDD8D3",
                    borderRadius: "10px",
                    boxShadow: "0 4px 10px rgba(0,0,0,0.05)",
                    padding: "20px 30px",
                    textAlign: "center",
                    color: "#333",
                    fontFamily: "Poppins, sans-serif",
                },
                onClick: function() {} // biar nggak nutup waktu diklik
            }).showToast();

            // ambil elemen toast yg baru muncul
            const toastEl = document.querySelector(".toastify");

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
        document.getElementById("agendaForm").addEventListener("submit", function(e) {
            e.preventDefault(); // cegah kirim langsung
            showConfirmSubmit(); // munculkan toast konfirmasi
        });
    </script>

@endsection
