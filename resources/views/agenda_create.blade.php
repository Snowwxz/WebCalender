<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Agenda - SiKota</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
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
                        style="color:#6E9579;font-size:1.3rem;position:absolute;left:0;top:50%;transform:translateY(-50%);">
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

                    <div class="form-grid">
                        <!-- Kolom kiri -->
                        <div class="form-column">
                            <div class="input-group">
                                <label><i class="fas fa-file-alt"></i> Nama Agenda</label>
                                <input type="text" name="agenda_name" placeholder="Masukkan nama agenda" required>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-align-left"></i> Deskripsi Agenda</label>
                                <textarea name="description" placeholder="Masukkan deskripsi agenda"></textarea>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-building"></i> Nama Instansi</label>
                                <input type="text" name="nama_instansi" placeholder="Masukkan nama instansi">
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-user-tie"></i> Penanggung Jawab</label>
                                <input type="text" name="penanggung_jawab"
                                    placeholder="Masukkan nama penanggung jawab">
                            </div>

                        </div>

                        <!-- Kolom kanan -->
                        <div class="form-column">
                            <div class="input-group">
                                <label><i class="fas fa-calendar-day"></i> Tanggal</label>
                                <input type="date" name="tanggal" required>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-clock"></i> Waktu Pelaksanaan</label>
                                <input type="time" name="waktu_pelaksanaan">
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-clock"></i> Waktu Selesai</label>
                                <input type="time" name="waktu_pelaksanaan">
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-location-dot"></i> Lokasi</label>
                                <input type="text" name="lokasi" placeholder="Masukkan lokasi anda">
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-people-group"></i> Instansi yang ikut serta</label>
                                <textarea name="instansi_ikut" placeholder="Masukkan instansi yang akan ikut serta"></textarea>
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

</body>

</html>
