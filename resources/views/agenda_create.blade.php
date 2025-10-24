<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Agenda - SiKota</title>
    <!-- Base CSS -->
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <!-- Component CSS -->
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/agenda-create.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="app-container">
        @include('layouts.header')

        <div class="agenda-container">
            <div class="agenda-form-card">

                <!-- Bagian header -->
                <div style="position: relative; text-align: center; margin-bottom: 8px;">
                    <!-- Tombol kembali di kiri -->
                    <a href="{{ route('dashboard') }}"
                       style="color:#333;font-size:1.3rem;position:absolute;left:0;top:50%;transform:translateY(-50%);">
                        <i class="fas fa-arrow-left"></i>
                    </a>

                    <!-- Judul di tengah -->
                    <div class="agenda-title"
                        style="display:inline-block; font-weight:600; font-size:1.4rem; color:#333;">
                        <i class="fas fa-calendar-plus"></i> Sistem Pengajuan Agenda
                    </div>
                </div>

                <p class="agenda-subtitle" style="text-align:center;">
                    Platform untuk mengajukan dan mengelola agenda kegiatan instansi
                </p>

                <form action="{{ route('agenda.store') }}" method="POST">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="alert alert-danger" style="background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                            <h4>Terjadi kesalahan:</h4>
                            <ul style="margin: 0; padding-left: 20px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success" style="background: #d1fae5; border: 1px solid #86efac; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-error" style="background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="form-grid">
                        <!-- Kolom kiri -->
                        <div class="form-column">
                            <div class="input-group">
                                <label><i class="fas fa-file-alt"></i> Nama Agenda</label>
                                <input type="text" name="agenda_name" placeholder="Masukkan nama agenda" value="{{ old('agenda_name') }}" required>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-align-left"></i> Deskripsi Agenda</label>
                                <textarea name="description" placeholder="Masukkan deskripsi agenda">{{ old('description') }}</textarea>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-building"></i> Nama Instansi (Pengaju)</label>
                                <select name="id_unit" required>
                                    <option value="">-- Pilih Instansi Pengaju --</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id_unit }}" {{ old('id_unit') == $unit->id_unit ? 'selected' : '' }}>{{ $unit->unit_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-user-tie"></i> Penanggung Jawab</label>
                                <input type="text" name="person_in_charge"
                                    placeholder="Masukkan nama penanggung jawab">
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-eye"></i> Kategori Agenda</label>
                                <select name="is_public">
                                    <option value="1">Public</option>
                                    <option value="0">Private</option>
                                </select>
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
                                <input type="time" name="start_time">
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-clock"></i> Waktu Selesai</label>
                                <input type="time" name="end_time">
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-location-dot"></i> Lokasi</label>
                                <input type="text" name="location" placeholder="Masukkan lokasi kegiatan">
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-people-group"></i> Instansi yang Ikut Serta</label>
                                <textarea name="involved_institution" placeholder="Masukkan instansi yang akan ikut serta"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-submit">
                        <button type="submit" class="btn-primary">Ajukan Agenda</button>
                    </div>
                </form>

            </div>
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
    </script>

</body>

</html>
