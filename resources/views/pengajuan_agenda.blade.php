@extends('layouts.main')

@section('title', 'Pengajuan Agenda - SiKota')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pengajuan.css') }}">
@endpush

@section('content')
    @include('layouts.header')

    <div class="agenda-container">
        <div class="agenda-form-card">
            <div class="agenda-title">
                <i class="fas fa-clipboard-list"></i>
                <span>Sistem Pengajuan Agenda</span>
            </div>
            <p class="agenda-subtitle">
                Platform untuk mengajukan dan mengelola agenda kegiatan instansi
            </p>

            @auth
                <form action="#" method="POST">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="nama_agenda">Nama Agenda</label>
                            <input type="text" id="nama_agenda" name="nama_agenda" placeholder="Masukkan nama agenda" required>
                        </div>

                        <div class="col-md-6">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" id="tanggal" name="tanggal" required>
                        </div>

                        <div class="col-md-12">
                            <label for="deskripsi">Deskripsi Agenda</label>
                            <textarea id="deskripsi" name="deskripsi" placeholder="Masukkan deskripsi agenda" required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label for="waktu">Waktu Pelaksanaan</label>
                            <input type="time" id="waktu" name="waktu" required>
                        </div>

                        <div class="col-md-6">
                            <label for="lokasi">Lokasi</label>
                            <input type="text" id="lokasi" name="lokasi" placeholder="Masukkan lokasi agenda" required>
                        </div>

                        <div class="col-md-6">
                            <label for="instansi">Nama Instansi</label>
                            <input type="text" id="instansi" name="instansi" placeholder="Masukkan nama instansi" required>
                        </div>

                        <div class="col-md-6">
                            <label for="instansi_ikut">Instansi yang Ikut Serta</label>
                            <input type="text" id="instansi_ikut" name="instansi_ikut" placeholder="Masukkan instansi yang ikut serta">
                        </div>

                        <div class="col-md-12">
                            <label for="penanggung_jawab">Penanggung Jawab</label>
                            <input type="text" id="penanggung_jawab" name="penanggung_jawab" placeholder="Masukkan nama penanggung jawab" required>
                        </div>

                        <div class="col-md-12 text-center mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Ajukan Agenda
                            </button>
                        </div>
                    </div>
                </form>
            @else
                <div class="text-center mt-5">
                    <p>Silakan login terlebih dahulu untuk mengajukan agenda.</p>
                    <button class="btn btn-success" onclick="window.location.href='{{ route('login.form') }}'">
                        <i class="fas fa-sign-in-alt"></i> Login Sekarang
                    </button>
                </div>
            @endauth
        </div>
    </div>
@endsection
