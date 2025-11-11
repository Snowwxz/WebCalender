@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/agenda-create.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
@endpush

@section('content')
    <div class="agenda-container">
        <div class="agenda-form-card">

            <!-- Header (samakan layout dengan create) -->
            <div style="position: relative; text-align: center; margin-bottom: 8px;">
                <a href="{{ route('agenda.notification') }}" class="back-btn" title="Kembali ke Notifikasi">
                    <i class="fas fa-arrow-left"></i>
                </a>

                <div class="agenda-title" style="display:inline-block; font-weight:600; font-size:1.4rem; color:#333;">
                    <i class="fas fa-calendar-edit"></i> Edit Agenda
                </div>
            </div>

            <p class="agenda-subtitle" style="text-align:center;">Perbarui data agenda kegiatan instansi Anda</p>

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
                        showSuccessToast("{{ session('success') }}");
                    </script>
                @endif

                @if (session('error'))
                    <script>
                        showErrorToast("{{ session('error') }}");
                    </script>
                @endif

                <div class="form-grid">
                    <!-- Full width fields (samakan posisi seperti create) -->
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
                            <label><i class="fas fa-building"></i> Nama Instansi (Pengaju)</label>
                            <input type="text" 
                                value="{{ Auth::user()->unit->unit_name ?? 'Tidak Diketahui' }}" readonly
                                style="background-color:#f2f2f2; cursor:not-allowed;">
                            <input type="hidden" name="id_unit" value="{{ Auth::user()->id_unit }}">
                        </div>

                        <div class="input-group">
                            <label><i class="fas fa-user-tie"></i> Penanggung Jawab</label>
                            <input type="text" name="person_in_charge" placeholder="Masukkan nama penanggung jawab"
                                value="{{ old('person_in_charge', $agenda->person_in_charge) }}" required>
                        </div>

                        <div class="input-group">
                            <label><i class="fas fa-eye"></i> Kategori Agenda</label>
                            <select name="is_public">
                                <option value="1" {{ old('is_public', $agenda->is_public) == 1 ? 'selected' : '' }}>Publik</option>
                                <option value="0" {{ old('is_public', $agenda->is_public) == 0 ? 'selected' : '' }}>Privasi</option>
                            </select>
                        </div>

                        <!-- Pindah ke kiri seperti create -->
                        <div class="input-group">
                            <label><i class="fas fa-people-group"></i> Instansi yang Ikut Serta</label>

                            <div class="chips-multiselect" id="involvedInstansi">
                                <div class="chips-container">
                                    <div class="chips-selected"></div>
                                    <input type="text" class="chips-input" placeholder="-- Pilih Instansi yang Ikut Serta --">
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
                                        @foreach ($units as $unit)
                                            @if ($unit->id_unit !== Auth::user()->id_unit && strtolower($unit->unit_name) !== 'protokol')
                                                <li data-value="{{ $unit->unit_name }}">{{ $unit->unit_name }}</li>
                                            @endif
                                        @endforeach
                                        <li class="add-new-instansi-option" data-action="add-new" style="padding: 10px 12px; cursor: pointer; border-top: 1px solid #e5e7eb; color: #6b8f71; font-weight: 500; display: flex; align-items: center; list-style: none;">
                                            <i class="fas fa-plus-circle" style="margin-right: 8px;"></i>
                                            <span>Tambah Instansi Baru</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <input type="hidden" name="involved_institution" id="involvedInstitutionField"
                                value="{{ old('involved_institution', $agenda->involved_institution) }}">
                        </div>
                    </div>

                    <!-- Kolom kanan -->
                    <div class="form-column">
                        <div class="input-group">
                            <label><i class="fas fa-calendar-day"></i> Tanggal</label>
                            <input type="date" name="date" value="{{ old('date', optional($agenda->date)->format('Y-m-d')) }}" required>
                        </div>

                        <div class="input-group">
                            <label><i class="fas fa-clock"></i> Waktu Mulai</label>
                            <input type="time" name="start_time" value="{{ old('start_time', optional($agenda->start_time)->format('H:i')) }}" required>
                        </div>

                        <div class="input-group">
                            <label><i class="fas fa-clock"></i> Waktu Selesai</label>
                            <input type="time" name="end_time" value="{{ old('end_time', optional($agenda->end_time)->format('H:i')) }}" required>
                        </div>

                        <div class="input-group">
                            <label><i class="fas fa-location-dot"></i> Lokasi</label>
                            <input type="text" name="location" placeholder="Masukkan lokasi kegiatan" value="{{ old('location', $agenda->location) }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-submit">
                    <button type="submit" class="btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
        // Validasi form (selaraskan dengan create)
        document.getElementById('agendaForm').addEventListener('submit', function(e) {
            const requiredFields = ['agenda_name','description','id_unit','date','start_time','end_time','location','involved_institution'];
            let isValid = true, emptyFields = [];
            requiredFields.forEach(field => {
                const el = document.querySelector(`[name="${field}"]`);
                if (el && (!el.value || el.value.trim() === '')) {
                    isValid = false; emptyFields.push(field);
                    el.style.borderColor = '#ef4444'; el.style.backgroundColor = '#fef2f2';
                } else if (el) { el.style.borderColor = ''; el.style.backgroundColor = ''; }
            });
            if (!isValid) { e.preventDefault(); alert('Mohon lengkapi semua field:\n' + emptyFields.map(f => '• ' + f.replace(/_/g,' ')).join('\n')); }
        });

        // Multiselect: pre-populate dari hidden field dan logika sama dengan create
        (() => {
            const root = document.getElementById('involvedInstansi');
            if (!root) return;
            const dropdown = root.querySelector('.chips-dropdown');
            const arrow = root.querySelector('.chips-arrow');
            const searchInput = root.querySelector('.chips-search-input');
            const mainInput = root.querySelector('.chips-input');
            const selectedWrap = root.querySelector('.chips-selected');
            const hiddenField = document.getElementById('involvedInstitutionField');
            // Ambil list items, tapi exclude opsi "Tambah Instansi Baru"
            const allListItems = Array.from(dropdown.querySelectorAll('li'));
            const listItems = allListItems.filter(li => !li.classList.contains('add-new-instansi-option'));
            const container = root.querySelector('.chips-container');
            const addNewInputContainer = dropdown.querySelector('.chips-add-new-input-container');
            const addNewInput = dropdown.querySelector('.chips-add-new-input');
            const addConfirmBtn = dropdown.querySelector('.chips-add-confirm-btn');
            const addCancelBtn = dropdown.querySelector('.chips-add-cancel-btn');
            let selectedValues = hiddenField.value ? hiddenField.value.split(',').map(v => v.trim()).filter(Boolean) : [];

            function renderInitial() {
                selectedValues.forEach(val => {
                    const chip = document.createElement('span'); chip.className = 'chip'; chip.textContent = val;
                    const btn = document.createElement('button'); btn.className = 'chip-remove'; btn.innerHTML = '&times;';
                    btn.onclick = () => { chip.remove(); selectedValues = selectedValues.filter(v => v !== val); showItem(val); syncHidden(); };
                    chip.appendChild(btn); selectedWrap.appendChild(chip); hideItem(val);
                });
                syncHidden();
            }

            function hideItem(value){ listItems.forEach(li=>{ if(li.getAttribute('data-value')===value || li.textContent.trim()===value) li.style.display='none'; }); }
            function showItem(value){ listItems.forEach(li=>{ if(li.getAttribute('data-value')===value || li.textContent.trim()===value) li.style.display='block'; }); }

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
                        addNewInstansiOption.style.display = 'block';
                    }
                }
            }

            function toggleDropdown(){ 
                const isOpen = dropdown.classList.toggle('open'); 
                root.classList.toggle('open', isOpen); 
                if(isOpen) { 
                    searchInput.value = '';
                    filterList(''); 
                    hideAddNewInput();
                    // Pastikan opsi "Tambah Instansi Baru" ditampilkan
                    const addNewInstansiOption = dropdown.querySelector('.add-new-instansi-option');
                    if (addNewInstansiOption) {
                        addNewInstansiOption.style.display = 'block';
                    }
                    searchInput.focus(); 
                } else {
                    hideAddNewInput();
                    searchInput.value = '';
                }
            }
            function closeDropdown(){ 
                dropdown.classList.remove('open'); 
                root.classList.remove('open'); 
                hideAddNewInput();
                searchInput.value = '';
                // Pastikan opsi "Tambah Instansi Baru" ditampilkan saat dropdown ditutup
                const addNewInstansiOption = dropdown.querySelector('.add-new-instansi-option');
                if (addNewInstansiOption) {
                    addNewInstansiOption.style.display = 'block';
                }
            }

            function addChip(value){ 
                if(selectedValues.includes(value)) return; 
                selectedValues.push(value); 
                const chip=document.createElement('span'); 
                chip.className='chip'; 
                chip.textContent=value; 
                const btn=document.createElement('button'); 
                btn.className='chip-remove'; 
                btn.innerHTML='&times;'; 
                btn.onclick=()=>{ 
                    chip.remove(); 
                    selectedValues = selectedValues.filter(v=>v!==value); 
                    // Hanya showItem jika item ada di listItems (untuk instansi yang ada di dropdown)
                    const itemExists = listItems.some(li => {
                        const val = li.getAttribute('data-value') || li.textContent.trim();
                        return val === value;
                    });
                    if (itemExists) {
                        showItem(value);
                    }
                    syncHidden(); 
                }; 
                chip.appendChild(btn); 
                selectedWrap.appendChild(chip); 
                // Hanya hideItem jika item ada di listItems (untuk instansi yang ada di dropdown)
                const itemExists = listItems.some(li => {
                    const val = li.getAttribute('data-value') || li.textContent.trim();
                    return val === value;
                });
                if (itemExists) {
                    hideItem(value);
                }
                syncHidden(); 
            }
            function syncHidden(){ 
                hiddenField.value = selectedValues.join(', '); 
                mainInput.style.display = selectedValues.length ? 'none' : 'inline'; 
            }
            
            function addNewItem(name) {
                const cleanName = name.trim();
                if (!cleanName) {
                    alert('Nama instansi tidak boleh kosong!');
                    if (addNewInput) addNewInput.focus();
                    return;
                }
                
                // Cegah duplikat - cek di listItems dan selectedValues
                const existsInList = listItems.some(li => {
                    const val = li.getAttribute('data-value') || li.textContent.trim();
                    return val.toLowerCase() === cleanName.toLowerCase();
                });
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
                addChip(cleanName);
                
                // Kosongkan input dan sembunyikan container
                hideAddNewInput();
                
                // Filter list untuk reset tampilan
                filterList('');
            }
            
            function filterList(term){ 
                const lower = term.toLowerCase().trim();
                const addNewInstansiOption = dropdown.querySelector('.add-new-instansi-option');
                
                // Filter list items (hanya item yang ada di listItems, bukan opsi "Tambah Instansi Baru")
                listItems.forEach(li=> {
                    const text = (li.getAttribute('data-value') || li.textContent.trim()).toLowerCase();
                    const match = !lower || text.includes(lower);
                    li.style.display = match ? 'block' : 'none';
                });
                
                // Tampilkan opsi "Tambah Instansi Baru" kecuali sedang menampilkan input field
                if (addNewInstansiOption) {
                    if (addNewInputContainer && addNewInputContainer.style.display === 'none') {
                        addNewInstansiOption.style.display = 'block';
                    } else {
                        addNewInstansiOption.style.display = 'none';
                    }
                }
            }

            // Event listener untuk search input
            searchInput.addEventListener('input', e=>filterList(e.target.value));
            
            // Event listener untuk opsi "Tambah Instansi Baru"
            const addNewInstansiOption = dropdown.querySelector('.add-new-instansi-option');
            if (addNewInstansiOption) {
                addNewInstansiOption.addEventListener('click', (e) => {
                    e.stopPropagation();
                    // Tampilkan input field
                    showAddNewInput('');
                    // Sembunyikan opsi "Tambah Instansi Baru" saat input field ditampilkan
                    addNewInstansiOption.style.display = 'none';
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
                        if (addNewInstansiOption) addNewInstansiOption.style.display = 'block';
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
                        if (addNewInstansiOption) addNewInstansiOption.style.display = 'block';
                    }
                });
            }
            
            // Event listener untuk tombol Batal
            if (addCancelBtn) {
                addCancelBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    hideAddNewInput();
                    if (addNewInstansiOption) addNewInstansiOption.style.display = 'block';
                    searchInput.focus();
                });
            }
            
            arrow.addEventListener('click', toggleDropdown);
            container.addEventListener('click', toggleDropdown);
            
            // Event listener untuk list items (kecuali opsi "Tambah Instansi Baru")
            listItems.forEach(li=> {
                // Skip jika ini adalah opsi "Tambah Instansi Baru"
                if (li.classList.contains('add-new-instansi-option')) {
                    return;
                }
                li.addEventListener('click', ()=>{ 
                    const value = li.getAttribute('data-value') || li.textContent.trim();
                    addChip(value); 
                    closeDropdown(); 
                });
            });
            
            document.addEventListener('click', e=>{ if(!root.contains(e.target)) closeDropdown(); });

            renderInitial();
        })();
    </script>
@endsection
