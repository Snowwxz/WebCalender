<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Agenda - SiKota</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="app-container">
        @include('layouts.header')

        <main class="main-content">
            <div class="approval-page">
                <div class="approval-header">
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
                        <a href="?status=pending" class="status-tab {{ request('status') === 'pending' ? 'active' : '' }}">
                            Menunggu <span class="badge">{{ $pendingCount }}</span>
                        </a>
                        <a href="?status=approved" class="status-tab {{ request('status') === 'approved' ? 'active' : '' }}">
                            Disetujui <span class="badge">{{ $approvedCount }}</span>
                        </a>
                        <a href="?status=rejected" class="status-tab {{ request('status') === 'rejected' ? 'active' : '' }}">
                            Ditolak <span class="badge">{{ $rejectedCount }}</span>
                        </a>
                    </div>
                </div>

                @php
                    $status = request('status', 'all');
                    $filtered = $status === 'all'
                        ? $agenda
                        : $agenda->where('status', $status);
                @endphp

                @if ($filtered->isEmpty())
                    <div class="approval-empty">
                        <div class="empty-icon">
                            <i class="fas fa-bell-slash"></i>
                        </div>
                        <h2>Tidak ada notifikasi</h2>
                        <p>Agenda yang kamu buat akan muncul di sini saat menunggu persetujuan admin.</p>
                    </div>
                @else
                    <div class="approval-list">
                        @foreach ($filtered as $agenda)
                            <div class="approval-card">
                                <h3>{{ $agenda->agenda_name }}</h3>
                                <p><strong>Tanggal:</strong>
                                    {{ \Carbon\Carbon::parse($agenda->date)->format('d M Y') }}</p>
                                <p><strong>Lokasi:</strong> {{ $agenda->location ?? '-' }}</p>
                                <p><strong>Penanggung Jawab:</strong> {{ $agenda->person_in_charge ?? '-' }}</p>
                                <p><strong>Instansi Terlibat:</strong> {{ $agenda->involved_institution ?? '-' }}</p>

                                <p><strong>Status:</strong>
                                    <span class="badge {{ $agenda->status }}">
                                        {{ ucfirst($agenda->status) }}
                                    </span>
                                </p>

                                <div class="approval-actions">
                                    @if($agenda->status === 'pending' || $agenda->status === 'rejected')
                                        <a href="{{ route('agenda.edit', $agenda->id_agenda) }}" class="btn-edit">
                                            <i class="fas fa-pen"></i> Edit Agenda
                                        </a>
                                    @endif

                                    @if($agenda->status === 'pending')
                                        <form action="{{ route('agenda.destroy', $agenda->id_agenda) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus agenda ini?');" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
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
</body>

</html>
