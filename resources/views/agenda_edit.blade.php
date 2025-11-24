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

            <for`m action="{{ route('agenda.update', $agenda->id_agenda) }}" method="POST" id="agendaForm">
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
                                <div class="chips-add-new-input-container"
                                    style="display: none; padding: 10px 12px; border-top: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                                    <input type="text" class="chips-add-new-input"
                                        placeholder="Ketik nama instansi baru..."
                                        style="width: 100%; padding: 8px 12px; border: 1px solid #6b8f71; border-radius: 6px; font-size: 14px; outline: none;" />
                                    <div style="display: flex; gap: 8px; margin-top: 8px;">
                                        <button type="button" class="chips-add-confirm-btn"
                                            style="flex: 1; padding: 6px 12px; background: #6b8f71; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 500;">Tambahkan</button>
                                        <button type="button" class="chips-add-cancel-btn"
                                            style="flex: 1; padding: 6px 12px; background: #e5e7eb; color: #374151; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 500;">Batal</button>
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
                                    <li class="add-new-instansi-option" data-action="add-new"
                                        style="padding: 10px 12px; cursor: pointer; border-top: 1px solid #e5e7eb; color: #6b8f71; font-weight: 500; display: flex; align-items: center; list-style: none;">
                                        <i class="fas fa-plus-circle" style="margin-right: 8px;"></i>
                                        <span>Lainnya...</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <input type="hidden" name="involved_institution" id="involvedInstitutionField"
                            value="{{ old('involved_institution', $agenda->involved_institution) }}">
                    </div>

                    <!-- Catatan - Full Width -->
                    <div class="input-group fullwidth-group">
                        <label><i class="fa-solid fa-file-lines" style="color:#6b8f71;"></i> Catatan</label>
                        <textarea name="notes" placeholder="Masukkan catatan tambahan (opsional)" rows="3">{{ old('notes', $agenda->notes) }}</textarea>
                    </div>

                </div>

                <div class="form-submit">
                    <button type="submit" class="btn-primary">Simpan Perubahan</button>
                </div>
            </for>
        </div>
    </div>

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

            // Ambil list items yang memang memiliki class dropdown-item
            const allListItems = Array.from(dropdown.querySelectorAll('li.dropdown-item'));
            // filter (tidak wajib karena query sudah memilih .dropdown-item) tetapi tetap aman
            const listItems = allListItems.slice();
            const selectAllOption = dropdown.querySelector('.select-all-option');
            const container = root.querySelector('.chips-container');
            const addNewInputContainer = dropdown.querySelector('.chips-add-new-input-container');
            const addNewInput = dropdown.querySelector('.chips-add-new-input');
            const addConfirmBtn = dropdown.querySelector('.chips-add-confirm-btn');
            const addCancelBtn = dropdown.querySelector('.chips-add-cancel-btn');
            const addNewInstansiOption = dropdown.querySelector('.add-new-instansi-option');

            let selectedValues = [];

            // Utility: render a chip
            function addChip(value) {
                if (!value) return;
                // prevent duplicate chips
                if (selectedWrap.querySelector(`.chip[data-value="${escapeSelector(value)}"]`)) return;

                const chip = document.createElement('span');
                chip.className = 'chip';
                chip.setAttribute('data-value', value);
                chip.textContent = value;

                const btn = document.createElement('button');
                btn.className = 'chip-remove';
                btn.type = 'button';
                btn.innerHTML = '&times;';
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggleItem(value);
                });

                chip.appendChild(btn);
                selectedWrap.appendChild(chip);
            }

            function removeChip(value) {
                const chip = selectedWrap.querySelector(`.chip[data-value="${escapeSelector(value)}"]`);
                if (chip) chip.remove();
            }

            function escapeSelector(str) {
                return str.replace(/["\\]/g, '\\$&');
            }

            function updateItemState(value) {
                const item = listItems.find(li => li.getAttribute('data-value') === value);
                if (!item) return;
                const isSelected = selectedValues.includes(value);

                item.classList.toggle('selected', isSelected);
                const checkmark = item.querySelector('.checkmark-icon');
                const checkIcon = item.querySelector('.check-icon');
                if (checkmark) checkmark.style.display = isSelected ? 'inline-block' : 'none';
                if (checkIcon) checkIcon.classList.toggle('checked', isSelected);
            }

            function updateSelectAllState() {
                if (!selectAllOption) return;
                const allVisibleListItems = listItems.filter(li => li.style.display !== 'none');
                const allSelected = allVisibleListItems.length > 0 &&
                    allVisibleListItems.every(li => selectedValues.includes(li.getAttribute('data-value')));

                selectAllOption.classList.toggle('selected', allSelected);
                const selectAllCheckmark = selectAllOption.querySelector('.checkmark-icon');
                const selectAllCheckIcon = selectAllOption.querySelector('.check-icon');
                if (selectAllCheckmark) selectAllCheckmark.style.display = allSelected ? 'inline-block' : 'none';
                if (selectAllCheckIcon) selectAllCheckIcon.classList.toggle('checked', allSelected);
            }

            function syncHidden() {
                hiddenField.value = selectedValues.join(', ');
                mainInput.style.display = selectedValues.length ? 'none' : 'inline';
                root.classList.toggle('empty', selectedValues.length === 0);
            }

            function toggleItem(value) {
                const index = selectedValues.findIndex(v => v === value);
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

            function selectAll() {
                const visibleItems = listItems.filter(li => li.style.display !== 'none');
                const visibleValues = visibleItems.map(li => li.getAttribute('data-value'));
                const allSelected = visibleValues.every(v => selectedValues.includes(v));

                if (allSelected) {
                    // deselect visible ones
                    visibleValues.forEach(v => {
                        const idx = selectedValues.indexOf(v);
                        if (idx > -1) {
                            selectedValues.splice(idx, 1);
                            removeChip(v);
                        }
                        updateItemState(v);
                    });
                } else {
                    // select visible ones
                    visibleValues.forEach(v => {
                        if (!selectedValues.includes(v)) {
                            selectedValues.push(v);
                            addChip(v);
                        }
                        updateItemState(v);
                    });
                }
                updateSelectAllState();
                syncHidden();
            }

            function showAddNewInput(initialValue = '') {
                if (!addNewInputContainer) return;
                addNewInputContainer.style.display = 'block';
                if (addNewInput) {
                    addNewInput.value = initialValue;
                    setTimeout(() => addNewInput.focus(), 100);
                }
                if (addNewInstansiOption) addNewInstansiOption.style.display = 'none';
                if (selectAllOption) selectAllOption.style.display = 'none';
            }

            function hideAddNewInput() {
                if (!addNewInputContainer) return;
                addNewInputContainer.style.display = 'none';
                if (addNewInput) addNewInput.value = '';
                if (addNewInstansiOption) addNewInstansiOption.style.display = 'flex';
                if (selectAllOption) selectAllOption.style.display = 'flex';
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

                // Tambahkan sebagai chip (tidak harus menambah ke dropdown)
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

                let anyVisible = false;
                listItems.forEach(li => {
                    const text = (li.querySelector('.item-text')?.textContent || '').toLowerCase();
                    const match = !lower || text.includes(lower);
                    li.style.display = match ? 'flex' : 'none';
                    if (match) anyVisible = true;
                });

                if (selectAllOption) selectAllOption.style.display = anyVisible || !lower ? 'flex' : 'none';

                if (addNewInstansiOption) {
                    if (addNewInputContainer && addNewInputContainer.style.display === 'block') {
                        addNewInstansiOption.style.display = 'none';
                    } else {
                        addNewInstansiOption.style.display = 'flex';
                    }
                }

                updateSelectAllState();
            }

            function openDropdown() {
                dropdown.classList.add('open');
                root.classList.add('open');
                searchInput.value = '';
                filterList('');
                hideAddNewInput();
                searchInput.focus();
                // restore states
                listItems.forEach(li => {
                    li.style.display = 'flex';
                    updateItemState(li.getAttribute('data-value'));
                });
                if (selectAllOption) selectAllOption.style.display = 'flex';
                updateSelectAllState();
            }

            function closeDropdown() {
                dropdown.classList.remove('open');
                root.classList.remove('open');
                hideAddNewInput();
                searchInput.value = '';
            }

            function toggleDropdown() {
                if (dropdown.classList.contains('open')) closeDropdown();
                else openDropdown();
            }

            // Event listeners
            arrow.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleDropdown();
            });

            // allow clicking the main input area to open
            mainInput.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleDropdown();
            });

            container.addEventListener('click', (e) => {
                // If user clicked inside selected chips area, ignore (chip buttons handle removal)
                if (e.target.closest('.chips-selected')) return;
                // otherwise toggle (but don't toggle when clicking search input)
                if (e.target !== searchInput) {
                    toggleDropdown();
                }
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
                    const value = li.getAttribute('data-value');
                    if (value) toggleItem(value);
                });
            });

            if (addNewInstansiOption) {
                addNewInstansiOption.addEventListener('click', (e) => {
                    e.stopPropagation();
                    showAddNewInput('');
                });
            }

            if (addNewInput) {
                addNewInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        if (addNewInput.value.trim()) addNewItem(addNewInput.value.trim());
                    } else if (e.key === 'Escape') {
                        e.preventDefault();
                        hideAddNewInput();
                        filterList('');
                        searchInput.focus();
                    }
                });
            }

            if (addConfirmBtn) {
                addConfirmBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (addNewInput && addNewInput.value.trim()) {
                        addNewItem(addNewInput.value.trim());
                    }
                });
            }

            if (addCancelBtn) {
                addCancelBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    hideAddNewInput();
                    filterList('');
                    searchInput.focus();
                });
            }

            // Search input filter
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    filterList(e.target.value);
                });
            }

            // close when clicking outside
            document.addEventListener('click', (e) => {
                if (!root.contains(e.target)) {
                    closeDropdown();
                }
            });

            // initialize from hidden field (restore)
            (function initializeValues() {
                const oldValue = hiddenField.value || '';
                if (!oldValue.trim()) {
                    syncHidden();
                    return;
                }

                const restoredValues = oldValue.split(',').map(v => v.trim()).filter(Boolean);
                restoredValues.forEach(value => {
                    // if not exist in listItems, we won't add to listItems dropdown but still create chip
                    const itemExists = listItems.some(li => li.getAttribute('data-value') === value);
                    if (!itemExists) {
                        // create a new li so it can be filtered in future
                        const newLi = document.createElement('li');
                        newLi.setAttribute('data-value', value);
                        newLi.classList.add('dropdown-item');
                        newLi.innerHTML = `
                            <span class="check-icon"></span>
                            <span class="item-text">${value}</span>
                            <i class="fas fa-check checkmark-icon" style="display:none;"></i>
                        `;
                        // insert before add-new option if present, else append
                        if (addNewInstansiOption && addNewInstansiOption.parentNode) {
                            addNewInstansiOption.parentNode.insertBefore(newLi, addNewInstansiOption);
                        } else {
                            dropdown.querySelector('ul')?.appendChild(newLi);
                        }
                        newLi.addEventListener('click', (e) => {
                            e.stopPropagation();
                            toggleItem(value);
                        });
                        listItems.push(newLi);
                    }

                    if (!selectedValues.includes(value)) {
                        selectedValues.push(value);
                        addChip(value);
                    }
                    updateItemState(value);
                });

                updateSelectAllState();
                syncHidden();
            })();
        });
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

@endsection
