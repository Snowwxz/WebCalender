<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Agenda - SiKota</title>
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
                       style="color:#333;font-size:1.3rem;position:absolute;left:0;top:50%;transform:translateY(-50%);">
                        <i class="fas fa-arrow-left"></i>
                    </a>

                    <!-- Judul di tengah -->
                    <div class="agenda-title" style="display:inline-block; font-weight:600; font-size:1.4rem; color:#333;">
                        <i class="fas fa-calendar-edit"></i> Edit Agenda
                    </div>
                </div>

                <p class="agenda-subtitle" style="text-align:center;">
                    Ubah detail agenda sesuai kebutuhan
                </p>

                <form action="{{ route('agenda.update', $agenda->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-grid">
                        <!-- Kolom kiri -->
                        <div class="form-column">
                            <div class="input-group">
                                <label><i class="fas fa-file-alt"></i> Nama Agenda</label>
                                <input type="text" name="agenda_name" value="{{ old('agenda_name', $agenda->agenda_name) }}" required>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-align-left"></i> Deskripsi Agenda</label>
                                <textarea name="description">{{ old('description', $agenda->description) }}</textarea>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-building"></i> Nama Instansi</label>
                                <input type="text" name="nama_instansi" value="{{ old('nama_instansi', $agenda->involved_institution) }}">
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-user-tie"></i> Penanggung Jawab</label>
                                <input type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab', $agenda->person_in_charge) }}">
                            </div>
                        </div>

                        <!-- Kolom kanan -->
                        <div class="form-column">
                            <div class="input-group">
                                <label><i class="fas fa-calendar-day"></i> Tanggal</label>
                                <input type="date" name="tanggal" value="{{ old('tanggal', $agenda->date->format('Y-m-d')) }}" required>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-clock"></i> Waktu Pelaksanaan</label>
                                <input type="time" name="waktu_pelaksanaan" value="{{ old('waktu_pelaksanaan', $agenda->time ?? '') }}">
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-location-dot"></i> Lokasi</label>
                                <input type="text" name="lokasi" value="{{ old('lokasi', $agenda->location) }}">
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-people-group"></i> Instansi yang ikut serta</label>
                                <textarea name="instansi_ikut">{{ old('instansi_ikut', $agenda->involved_institution) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-submit">
                        <button type="submit" class="btn-primary">Update Agenda</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
