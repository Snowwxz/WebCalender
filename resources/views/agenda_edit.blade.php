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

                <!-- Header -->
                <div style="position: relative; text-align: center; margin-bottom: 8px;">
                    <a href="{{ route('agenda.notification') }}"
                       style="color:#333;font-size:1.3rem;position:absolute;left:0;top:50%;transform:translateY(-50%);">
                        <i class="fas fa-arrow-left"></i>
                    </a>

                    <div class="agenda-title" style="display:inline-block; font-weight:600; font-size:1.4rem; color:#333;">
                        <i class="fas fa-calendar-edit"></i> Edit Agenda
                    </div>
                </div>

                <p class="agenda-subtitle" style="text-align:center;">
                    Ubah detail agenda sesuai kebutuhan
                </p>

                <!-- Alert success/error -->
                @if (session('success'))
                    <div style="color:green;text-align:center;margin-bottom:10px;">{{ session('success') }}</div>
                @elseif (session('error'))
                    <div style="color:red;text-align:center;margin-bottom:10px;">{{ session('error') }}</div>
                @endif

                <form action="{{ route('agenda.update', $agenda->id_agenda) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-grid">
                        <!-- Kolom kiri -->
                        <div class="form-column">
                            <div class="input-group">
                                <label><i class="fas fa-file-alt"></i> Nama Agenda</label>
                                <input type="text" name="agenda_name"
                                    value="{{ old('agenda_name', $agenda->agenda_name) }}" required>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-align-left"></i> Deskripsi Agenda</label>
                                <textarea name="description">{{ old('description', $agenda->description) }}</textarea>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-building"></i> Nama Instansi Pengajuan</label>
                                <input type="text" name="instansi_pengajuan"
                                    value="{{ old('instansi_pengajuan', $agenda->submitted_by) }}">
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-user-tie"></i> Penanggung Jawab</label>
                                <input type="text" name="penanggung_jawab"
                                    value="{{ old('penanggung_jawab', $agenda->person_in_charge) }}">
                            </div>
                        </div>

                        <!-- Kolom kanan -->
                        <div class="form-column">
                            <div class="input-group">
                                <label><i class="fas fa-calendar-day"></i> Tanggal</label>
                                <input type="date" name="tanggal"
                                    value="{{ old('tanggal', $agenda->date ? $agenda->date->format('Y-m-d') : '') }}" required>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-clock"></i> Waktu Mulai</label>
                                <input type="time" name="start_time"
                                    value="{{ old('start_time', $agenda->start_time) }}">
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-clock"></i> Waktu Selesai</label>
                                <input type="time" name="end_time"
                                    value="{{ old('end_time', $agenda->end_time) }}">
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-location-dot"></i> Lokasi</label>
                                <input type="text" name="lokasi"
                                    value="{{ old('lokasi', $agenda->location) }}">
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-people-group"></i> Instansi yang Ikut Serta</label>
                                <textarea name="instansi_ikut">{{ old('instansi_ikut', $agenda->involved_institution) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-submit">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</body>
</html>
