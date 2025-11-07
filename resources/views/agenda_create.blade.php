@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/agenda-create.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                            <label><i class="fas fa-building"></i> Nama Instansi (Pengaju)</label>
                            <input type="text" value="{{ $unitName }}" readonly>
                            <input type="hidden" name="id_unit" value="{{ Auth::user()->id_unit }}">
                        </div>

                        <div class="input-group">
                            <label><i class="fas fa-user-tie"></i> Penanggung Jawab</label>
                            <input type="text" name="person_in_charge" placeholder="Masukkan nama penanggung jawab"
                                required>
                        </div>

                        <div class="input-group">
                            <label><i class="fas fa-eye"></i> Kategori Agenda</label>
                            <select name="is_public">
                                <option value="1">Publik</option>
                                <option value="0">Privasi</option>
                            </select>
                        </div>

                        <!-- 🟢 Instansi yang Ikut Serta sekarang pindah ke kolom kiri -->
                        <div class="input-group">
                            <label><i class="fas fa-people-group"></i> Instansi yang Ikut Serta</label>

                            <div class="chips-multiselect" id="involvedInstansi">
                                <div class="chips-container">
                                    <div class="chips-selected"></div>
                                    <input type="text" class="chips-input"
                                        placeholder="-- Pilih Instansi yang Ikut Serta --">
                                </div>
                                <span class="chips-arrow"><i class="fas fa-chevron-down"></i></span>

                                <div class="chips-dropdown">
                                    <div class="chips-search">
                                        <input type="text" class="chips-search-input" placeholder="Cari instansi..." />
                                    </div>
                                    <ul>
                                        @foreach ($units as $unit)
                                            @if ($unit->id_unit !== Auth::user()->id_unit && strtolower($unit->unit_name) !== 'protokol')
                                                <li data-value="{{ $unit->unit_name }}">{{ $unit->unit_name }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <input type="hidden" name="involved_institution" id="involvedInstitutionField"
                                value="{{ old('involved_institution') }}">
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

                        <!-- Lokasi sekarang pindah ke kolom kanan -->
                        <div class="input-group">
                            <label><i class="fas fa-location-dot"></i> Lokasi</label>
                            <input type="text" name="location" placeholder="Masukkan lokasi kegiatan" required>
                        </div>
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
            const listItems = Array.from(dropdown.querySelectorAll('li'));
            const container = root.querySelector('.chips-container');

            let selectedValues = [];

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
                    // 🔥 tampilkan lagi item di dropdown
                    listItems.forEach(li => {
                        if (li.textContent.trim() === value) {
                            li.style.display = 'block';
                        }
                    });
                    syncHidden();
                };

                chip.appendChild(btn);
                selectedWrap.appendChild(chip);

                // 🔥 sembunyikan item yang dipilih
                listItems.forEach(li => {
                    if (li.textContent.trim() === value) {
                        li.style.display = 'none';
                    }
                });

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

            function filterList(term) {
                const lower = term.toLowerCase();
                listItems.forEach(li => {
                    const match = li.textContent.toLowerCase().includes(lower);
                    li.style.display = match ? 'block' : 'none';
                });
            }

            searchInput.addEventListener('input', e => filterList(e.target.value));
            arrow.addEventListener('click', toggleDropdown);

            // bikin seluruh area card bisa diklik
            container.addEventListener('click', () => {
                toggleDropdown();
            });

            listItems.forEach(li => {
                li.addEventListener('click', () => {
                    addChip(li.textContent.trim());
                    closeDropdown();
                });
            });

            document.addEventListener('click', e => {
                if (!root.contains(e.target)) closeDropdown();
            });

            syncHidden(); // set initial empty state 👈 TAMBAHKAN DI SINI
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
