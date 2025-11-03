@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/notification.css') }}">
@endpush

@section('content')
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
                    $pendingCount = $counts['pending'] ?? 0;
                    $approvedCount = $counts['approved'] ?? 0;
                    $rejectedCount = $counts['rejected'] ?? 0;
                    $allCount = $counts['all'] ?? 0;
                @endphp

                <div class="status-tabs-wrap">
                    <div class="status-tabs">
                        <a href="?status=all" class="status-tab {{ request('status', 'all') === 'all' ? 'active' : '' }}">
                            Semua <span class="badge">{{ $allCount }}</span>
                        </a>
                        <a href="?status=pending"
                            class="status-tab {{ request('status', 'all') === 'pending' ? 'active' : '' }}">
                            Menunggu <span class="badge">{{ $pendingCount }}</span>
                        </a>
                        <a href="?status=approved"
                            class="status-tab {{ request('status', 'all') === 'approved' ? 'active' : '' }}">
                            Disetujui <span class="badge">{{ $approvedCount }}</span>
                        </a>
                        <a href="?status=rejected"
                            class="status-tab {{ request('status', 'all') === 'rejected' ? 'active' : '' }}">
                            Ditolak <span class="badge">{{ $rejectedCount }}</span>
                        </a>
                    </div>
                </div>

                @php
                    // Filtering now happens in controller; keep $status and $q from controller
                @endphp

                <div class="search-bar">
                    <form method="GET" action="{{ route('agenda.notification') }}">
                        <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                        <div class="search-input-wrap">
                            <input type="text" name="q" placeholder="Cari agenda, instansi, atau deskripsi..."
                                value="{{ request('q') }}">
                            <button type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>


                @if ($agenda->count() === 0)
                    <div class="notification-empty">
                        <div class="empty-icon">
                            <i class="fas fa-bell-slash"></i>
                        </div>
                        <h2>Tidak ada notifikasi</h2>
                        <p>Agenda yang kamu buat akan muncul di sini saat menunggu persetujuan admin.</p>
                    </div>
                @else
                    <div class="notification-list">
                        @foreach ($agenda as $item)
                            <div class="notification-card">
                                <!-- Card Header -->
                                <div class="card-header">
                                    <div class="card-title-section">
                                        <h3 class="card-title">{{ $item->agenda_name }}</h3>
                                        <p class="card-description">{{ $item->description ?? '-' }}</p>
                                    </div>
                                    <div class="status-badge {{ $item->status }}">
                                        @switch($item->status)
                                            @case('pending')
                                                Menunggu
                                            @break

                                            @case('approved')
                                                Disetujui
                                            @break

                                            @case('rejected')
                                                Ditolak
                                            @break

                                            @default
                                                {{ ucfirst($item->status) }}
                                        @endswitch
                                    </div>
                                </div>

                                <!-- Card Content -->
                                <div class="card-content">
                                    <div class="details-grid">
                                        <div class="details-left">
                                            <div class="detail-item">
                                                <i class="fas fa-building"></i>
                                                <span><strong>Nama Instansi (Pengaju):</strong>
                                                    {{ $item->unit->unit_name ?? '-' }}</span>
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-user-tie"></i>
                                                <span><strong>Penanggung Jawab:</strong>
                                                    {{ $item->person_in_charge ?? '-' }}</span>
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-calendar-alt"></i>
                                                <span><strong>Tanggal:</strong>
                                                    {{ \Carbon\Carbon::parse($item->date)->locale('id')->translatedFormat('l, d F Y') }}</span>
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-eye"></i>
                                                <span>
                                                    <strong>Status:</strong>
                                                    {{ $item->is_public ? 'Publik' : 'Privasi' }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="details-right">
                                            <div class="detail-item">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <span><strong>Lokasi:</strong> {{ $item->location ?? '-' }}</span>
                                            </div>
                                            <div class="detail-item participants">
                                                <i class="fas fa-people-group"></i>
                                                <span><strong>Instansi Terlibat:</strong>
                                                    {{ $item->involved_institution ?? '-' }}</span>
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-clock"></i>
                                                <span><strong>Waktu Pelaksanaan:</strong>
                                                    @if ($item->start_time && $item->end_time)
                                                        {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }}
                                                        - {{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }}
                                                        WITA
                                                    @elseif($item->start_time)
                                                        {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }}
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
                                            <span>{{ $item->unit->unit_name ?? '-' }}</span>
                                            <span class="submission-time">
                                                -  Diajukan
                                                {{ \Carbon\Carbon::parse($item->created_at)->locale('id')->diffForHumans() }}
                                            </span>
                                        </div>

                                        <div class="notification-actions">
                                            @if ($item->status === 'pending' || $item->status === 'rejected')
                                                <a href="{{ route('agenda.edit', $item->id_agenda) }}"
                                                    class="btn-edit">
                                                    <i class="fas fa-pen"></i> Edit Agenda
                                                </a>
                                            @endif

                                            @if ($item->status !== 'approved')
                                                <form action="{{ route('agenda.destroy', $item->id_agenda) }}"
                                                    method="POST" class="action-form delete-form" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-delete">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div> <!-- tutup .card-content -->
                            </div> <!-- ✅ tutup .notification-card di sini -->
                        @endforeach
                    </div> <!-- tutup .notification-list -->

                    @php
                        $current = $agenda->currentPage();
                        $last = $agenda->lastPage();
                        $window = 2; // show 2 on each side
                        $start = max(1, $current - $window);
                        $end = min($last, $current + $window);
                    @endphp

                    <nav class="pagination-numeric" aria-label="Pagination">
                        <a href="{{ $agenda->url(1) }}" class="page-control {{ $current === 1 ? 'disabled' : '' }}" aria-label="First" title="Halaman pertama">«</a>
                        <a href="{{ $agenda->previousPageUrl() ?? '#' }}" class="page-control {{ $current === 1 ? 'disabled' : '' }}" aria-label="Previous" title="Sebelumnya">‹</a>

                        @if ($start > 1)
                            <a href="{{ $agenda->url(1) }}" class="page-number">1</a>
                            @if ($start > 2)
                                <span class="page-ellipsis">…</span>
                            @endif
                        @endif

                        @for ($i = $start; $i <= $end; $i++)
                            <a href="{{ $agenda->url($i) }}" class="page-number {{ $i === $current ? 'active' : '' }}" aria-current="{{ $i === $current ? 'page' : 'false' }}">{{ $i }}</a>
                        @endfor

                        @if ($end < $last)
                            @if ($end < $last - 1)
                                <span class="page-ellipsis">…</span>
                            @endif
                            <a href="{{ $agenda->url($last) }}" class="page-number">{{ $last }}</a>
                        @endif

                        <a href="{{ $agenda->nextPageUrl() ?? '#' }}" class="page-control {{ $current === $last ? 'disabled' : '' }}" aria-label="Next" title="Berikutnya">›</a>
                        <a href="{{ $agenda->url($last) }}" class="page-control {{ $current === $last ? 'disabled' : '' }}" aria-label="Last" title="Halaman terakhir">»</a>
                    </nav>
                @endif
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

    <script>
        // 🔁 Reload otomatis saat search dikosongkan
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="q"]');
            if (!searchInput) return;

            searchInput.addEventListener('input', function() {
                if (this.value.trim() === '') {
                    const url = new URL(window.location.href);
                    url.searchParams.delete('q');
                    window.location.href = url.toString();
                }
            });
        });
    </script>
@endsection
