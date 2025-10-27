<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Agenda - SiKota</title>
    <!-- Base CSS -->
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <!-- Component CSS -->
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notification.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="app-container">
        @include('layouts.header')

        <main class="main-content" style="margin-left: 0; width: 100%; padding: 30px 0 0 0;">
            <div class="notification-page">
                <div class="notification-header">
                    <div>
                        <a href="{{ route('dashboard.bulan') }}" title="Kembali ke Dashboard"
                            aria-label="Kembali ke Dashboard">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                    <div class="title-wrap">
                        <h1 class="page-title">Notifikasi Agenda Saya</h1>
                        <p class="page-subtitle">
                            Pantau status agenda yang telah kamu ajukan ke admin.
                        </p>
                    </div>
                </div>

                @php
                    $pendingCount = $agenda->where('status', 'pending')->count();
                    $approvedCount = $agenda->where('status', 'approved')->count();
                    $rejectedCount = $agenda->where('status', 'rejected')->count();
                @endphp

                <div class="status-tabs-wrap">
                    <div class="status-tabs">
                        <a href="?status=all" class="status-tab {{ request('status') === 'all' ? 'active' : '' }}">
                            Semua <span class="badge">{{ $agenda->count() }}</span>
                        </a>
                        <a href="?status=pending"
                            class="status-tab {{ request('status') === 'pending' ? 'active' : '' }}">
                            Menunggu <span class="badge">{{ $pendingCount }}</span>
                        </a>
                        <a href="?status=approved"
                            class="status-tab {{ request('status') === 'approved' ? 'active' : '' }}">
                            Disetujui <span class="badge">{{ $approvedCount }}</span>
                        </a>
                        <a href="?status=rejected"
                            class="status-tab {{ request('status') === 'rejected' ? 'active' : '' }}">
                            Ditolak <span class="badge">{{ $rejectedCount }}</span>
                        </a>
                    </div>
                </div>

                @php
                    $status = request('status', 'all');
                    $filtered = $status === 'all' ? $agenda : $agenda->where('status', $status);
                @endphp

                @if ($filtered->isEmpty())
                    <div class="notification-empty">
                        <div class="empty-icon">
                            <i class="fas fa-bell-slash"></i>
                        </div>
                        <h2>Tidak ada notifikasi</h2>
                        <p>Agenda yang kamu buat akan muncul di sini saat menunggu persetujuan admin.</p>
                    </div>
                @else
                    <div class="notification-list">
                        @foreach ($filtered as $agenda)
                            <div class="notification-card">
                                <!-- Card Header -->
                                <div class="card-header">
                                    <div class="card-title-section">
                                        <h3 class="card-title">{{ $agenda->agenda_name }}</h3>
                                        <p class="card-description">{{ $agenda->description ?? '-' }}</p>
                                    </div>
                                    <div class="status-badge {{ $agenda->status }}">
                                        {{ ucfirst($agenda->status) }}
                                    </div>
                                </div>

                                <!-- Card Content -->
                                <div class="card-content">
                                    <div class="details-grid">
                                        <div class="details-left">
                                            <div class="detail-item">
                                                <i class="fas fa-building"></i>
                                                <span><strong>Nama Instansi (Pengaju):</strong>
                                                    {{ $agenda->submitted_by ?? '-' }}</span>
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-user-tie"></i>
                                                <span><strong>Penanggung Jawab:</strong>
                                                    {{ $agenda->person_in_charge ?? '-' }}</span>
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-calendar-alt"></i>
                                                <span><strong>Tanggal:</strong>
                                                    {{ \Carbon\Carbon::parse($agenda->date)->locale('id')->translatedFormat('l, d F Y') }}</span>
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-eye"></i>
                                                <span>
                                                    <strong>Status:</strong>
                                                    {{ $agenda->is_public ? 'Publik' : 'Privasi' }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="details-right">
                                            <div class="detail-item">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <span><strong>Lokasi:</strong> {{ $agenda->location ?? '-' }}</span>
                                            </div>
                                            <div class="detail-item participants">
                                                <i class="fas fa-people-group"></i>
                                                <span><strong>Instansi Terlibat:</strong>
                                                    {{ $agenda->involved_institution ?? '-' }}</span>
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-clock"></i>
                                                <span><strong>Waktu Pelaksanaan:</strong>
                                                    @if ($agenda->start_time && $agenda->end_time)
                                                        {{ \Carbon\Carbon::parse($agenda->start_time)->format('H:i') }}
                                                        - {{ \Carbon\Carbon::parse($agenda->end_time)->format('H:i') }}
                                                        WITA
                                                    @elseif($agenda->start_time)
                                                        {{ \Carbon\Carbon::parse($agenda->start_time)->format('H:i') }}
                                                        WITA
                                                    @else
                                                        -
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card Footer -->
                                    <div class="card-footer">
                                        <div class="submission-info">
                                            <i class="fas fa-user"></i>
                                            <span>{{ $agenda->submitted_by ?? '-' }}</span>
                                            <span class="submission-time">
                                                Diajukan
                                                {{ \Carbon\Carbon::parse($agenda->created_at)->locale('id')->diffForHumans() }}
                                            </span>
                                        </div>

                                        <div class="notification-actions">
                                            @if ($agenda->status === 'pending' || $agenda->status === 'rejected')
                                                <a href="{{ route('agenda.edit', $agenda->id_agenda) }}"
                                                    class="btn-edit">
                                                    <i class="fas fa-pen"></i> Edit Agenda
                                                </a>
                                            @endif

                                            <form action="{{ route('agenda.destroy', $agenda->id_agenda) }}"
                                                method="POST" class="action-form" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-delete">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div> <!-- tutup .card-content -->
                            </div> <!-- ✅ tutup .notification-card di sini -->
                        @endforeach
                    </div> <!-- tutup .notification-list -->
                @endif
            </div>
        </main>
    </div>

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            const profile = document.querySelector('.user-profile');
            dropdown.classList.toggle('show');
            profile.classList.toggle('active');
        }
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const profile = document.querySelector('.user-profile');
            if (profile && !profile.contains(event.target)) {
                dropdown && dropdown.classList.remove('show');
                profile.classList.remove('active');
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('.delete-form');

            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); // cegah submit langsung

                    Swal.fire({
                        title: 'Yakin ingin menghapus agenda ini?',
                        text: "Data yang dihapus tidak bisa dikembalikan.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit(); // kirim form jika user menekan konfirmasi
                        }
                    });
                });
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            @elseif (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    showConfirmButton: true
                });
            @endif
        });
    </script>
</body>

</html>
