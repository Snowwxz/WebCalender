<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Agenda - SiKota</title>
    <!-- Base CSS -->
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <!-- Component CSS -->
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/agenda-create.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>

<body>
    <div class="app-container">
        @include('layouts.header')

        <div class="agenda-container">
            <div class="agenda-form-card">

                <!-- Header -->
                <div style="position: relative; text-align: center; margin-bottom: 8px;">
                    <a href="{{ url('/dashboard/bulan') }}" class="back-btn" title="Kembali ke Dashboard">
                        <i class="fas fa-arrow-left"></i>
                    </a>

                    <div class="agenda-title"
                        style="display:inline-block; font-weight:600; font-size:1.4rem; color:#333;">
                        <i class="fas fa-calendar-edit"></i> Edit Agenda
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
                            Toastify({
                                text: "{{ session('success') }}",
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#4CAF50",
                                stopOnFocus: true,
                                style: {
                                    borderRadius: "8px",
                                    boxShadow: "0 3px 8px rgba(0,0,0,0.1)",
                                    fontWeight: "500"
                                }
                            }).showToast();
                        </script>
                    @endif

                    @if (session('error'))
                        <script>
                            Toastify({
                                text: "{{ session('error') }}",
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#f44336",
                                stopOnFocus: true,
                                style: {
                                    borderRadius: "8px",
                                    boxShadow: "0 3px 8px rgba(0,0,0,0.1)",
                                    fontWeight: "500"
                                }
                            }).showToast();
                        </script>
                    @endif

                    <div class="form-grid">
                        <!-- Kolom kiri -->
                        <div class="form-column">
                            <div class="input-group">
                                <label><i class="fas fa-file-alt"></i> Nama Agenda</label>
                                <input type="text" name="agenda_name" placeholder="Masukkan nama agenda"
                                    value="{{ old('agenda_name', $agenda->agenda_name) }}" required>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-align-left"></i> Deskripsi Agenda</label>
                                <textarea name="description" placeholder="Masukkan deskripsi agenda" required>{{ old('description', $agenda->description) }}</textarea>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-building"></i> Nama Instansi (Pengaju)</label>
                                <input type="text" name="id_unit"
                                    value="{{ Auth::user()->unit->unit_name ?? 'Tidak Diketahui' }}" readonly
                                    style="background-color:#f2f2f2; cursor:not-allowed;">
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-user-tie"></i> Penanggung Jawab</label>
                                <input type="text" name="person_in_charge"
                                    placeholder="Masukkan nama penanggung jawab"
                                    value="{{ old('person_in_charge', $agenda->person_in_charge) }}" required>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-eye"></i> Kategori Agenda</label>
                                <select name="is_public">
                                    <option value="1"
                                        {{ old('is_public', $agenda->is_public) == 1 ? 'selected' : '' }}>Publik
                                    </option>
                                    <option value="0"
                                        {{ old('is_public', $agenda->is_public) == 0 ? 'selected' : '' }}>Privasi
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Kolom kanan -->
                        <div class="form-column">
                            <div class="input-group">
                                <label><i class="fas fa-calendar-day"></i> Tanggal</label>
                                <input type="date" name="date"
                                    value="{{ old('date', optional($agenda->date)->format('Y-m-d')) }}" required>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-clock"></i> Waktu Mulai</label>
                                <input type="time" name="start_time"
                                    value="{{ old('start_time', optional($agenda->start_time)->format('H:i')) }}"
                                    required>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-clock"></i> Waktu Selesai</label>
                                <input type="time" name="end_time"
                                    value="{{ old('end_time', optional($agenda->end_time)->format('H:i')) }}" required>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-location-dot"></i> Lokasi</label>
                                <input type="text" name="location" placeholder="Masukkan lokasi kegiatan"
                                    value="{{ old('location', $agenda->location) }}" required>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-people-group"></i> Instansi yang Ikut Serta</label>

                                <div class="chips-multiselect" id="involvedInstansi">
                                    <div class="chips-container">
                                        <div class="chips-selected">
                                            @if ($agenda->involved_institution)
                                                @foreach (explode(',', $agenda->involved_institution) as $instansi)
                                                    <span class="chip">
                                                        {{ trim($instansi) }}
                                                        <button type="button" class="chip-remove">&times;</button>
                                                    </span>
                                                @endforeach
                                            @endif
                                        </div>
                                        <input type="text" class="chips-input"
                                            placeholder="-- Pilih Instansi yang Ikut Serta --">
                                    </div>
                                    <span class="chips-arrow"><i class="fas fa-chevron-down"></i></span>

                                    <div class="chips-dropdown">
                                        <div class="chips-search">
                                            <input type="text" class="chips-search-input"
                                                placeholder="Cari instansi..." />
                                        </div>
                                        <ul>
                                            @foreach ($units as $unit)
                                                <li data-value="{{ $unit->unit_name }}">{{ $unit->unit_name }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                                <input type="hidden" name="involved_institution" id="involvedInstitutionField"
                                    value="{{ old('involved_institution', $agenda->involved_institution) }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-submit">
                        <button type="submit" class="btn-primary">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // validasi form sama seperti agenda_create
        document.getElementById('agendaForm').addEventListener('submit', function(e) {
            const requiredFields = [
                'agenda_name', 'description', 'id_unit', 'person_in_charge',
                'date', 'start_time', 'end_time', 'location', 'involved_institution'
            ];
            let isValid = true,
                emptyFields = [];
            requiredFields.forEach(field => {
                const el = document.querySelector(`[name="${field}"]`);
                if (el && (!el.value || el.value.trim() === '')) {
                    isValid = false;
                    emptyFields.push(field);
                    el.style.borderColor = '#ef4444';
                    el.style.backgroundColor = '#fef2f2';
                } else if (el) {
                    el.style.borderColor = '';
                    el.style.backgroundColor = '';
                }
            });
            if (!isValid) {
                e.preventDefault();
                alert('Mohon lengkapi semua field:\n' +
                    emptyFields.map(f => '• ' + f.replace(/_/g, ' ')).join('\n'));
            }
        });

        // multi-select logic (disalin dari create)
        (() => {
            const root = document.getElementById('involvedInstansi');
            if (!root) return;
            const dropdown = root.querySelector('.chips-dropdown');
            const arrow = root.querySelector('.chips-arrow');
            const searchInput = root.querySelector('.chips-search-input');
            const mainInput = root.querySelector('.chips-input');
            const selectedWrap = root.querySelector('.chips-selected');
            const hiddenField = document.getElementById('involvedInstitutionField');
            const listItems = Array.from(dropdown.querySelectorAll('li'));
            const container = root.querySelector('.chips-container');
            let selectedValues = hiddenField.value ? hiddenField.value.split(',').map(v => v.trim()) : [];

            function toggleDropdown() {
                const isOpen = dropdown.classList.toggle('open');
                root.classList.toggle('open', isOpen);
                if (isOpen) searchInput.focus();
            }

            function closeDropdown() {
                dropdown.classList.remove('open');
                root.classList.remove('open');
            }

            function addChip(value) {
                if (selectedValues.includes(value)) return;
                selectedValues.push(value);
                const chip = document.createElement('span');
                chip.className = 'chip';
                chip.textContent = value;
                const btn = document.createElement('button');
                btn.className = 'chip-remove';
                btn.innerHTML = '&times;';
                btn.onclick = () => {
                    chip.remove();
                    selectedValues = selectedValues.filter(v => v !== value);
                    listItems.forEach(li => {
                        if (li.textContent.trim() === value) li.style.display = 'block';
                    });
                    syncHidden();
                };
                chip.appendChild(btn);
                selectedWrap.appendChild(chip);
                listItems.forEach(li => {
                    if (li.textContent.trim() === value) li.style.display = 'none';
                });
                syncHidden();
            }

            function syncHidden() {
                hiddenField.value = selectedValues.join(', ');
                mainInput.style.display = selectedValues.length ? 'none' : 'inline';
            }

            function filterList(term) {
                const lower = term.toLowerCase();
                listItems.forEach(li => li.style.display = li.textContent.toLowerCase().includes(lower) ? 'block' :
                    'none');
            }

            searchInput.addEventListener('input', e => filterList(e.target.value));
            arrow.addEventListener('click', toggleDropdown);
            container.addEventListener('click', toggleDropdown);
            listItems.forEach(li => li.addEventListener('click', () => {
                addChip(li.textContent.trim());
                closeDropdown();
            }));
            document.addEventListener('click', e => {
                if (!root.contains(e.target)) closeDropdown();
            });
        })();
    </script>
</body>

</html>
