<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve - SiKota</title>
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
                        <a href="{{ route('dashboard.bulan') }}" title="Kembali ke Dashboard" aria-label="Kembali ke Dashboard">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                    <div class="title-wrap">
                        <h1 class="page-title">Kelola Agenda Kegiatan</h1>
                        <p class="page-subtitle">Tinjau dan setujui proposal agenda kegiatan dari berbagai dinas dan instansi.</p>
                    </div>

                    @php($status = request('status', 'all'))
                    <div class="status-tabs-wrap">
                        <div class="status-tabs">
                            <a href="{{ route('approve', ['status' => 'all']) }}" class="status-tab {{ $status === 'all' ? 'active' : '' }}">
                                <span>Semua</span>
                                <span class="badge">0</span>
                            </a>
                            <a href="{{ route('approve', ['status' => 'pending']) }}" class="status-tab {{ $status === 'pending' ? 'active' : '' }}">
                                <span>Menunggu</span>
                                <span class="badge">0</span>
                            </a>
                            <a href="{{ route('approve', ['status' => 'approved']) }}" class="status-tab {{ $status === 'approved' ? 'active' : '' }}">
                                <span>Disetujui</span>
                                <span class="badge">0</span>
                            </a>
                            <a href="{{ route('approve', ['status' => 'rejected']) }}" class="status-tab {{ $status === 'rejected' ? 'active' : '' }}">
                                <span>Ditolak</span>
                                <span class="badge">0</span>
                            </a>
                        </div>
                    </div>

                    <form class="approval-search" method="GET" action="{{ route('approve') }}">
                        <input type="hidden" name="status" value="{{ $status }}" />
                        <i class="fas fa-search"></i>
                        <input type="text" name="q" placeholder="Search" value="{{ request('q') }}" />
                    </form>
                </div>

                <div class="approval-empty">
                    <div class="empty-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <h2>Belum ada pengajuan agenda</h2>
                    <p>Pengajuan agenda yang sudah dibuat akan muncul di sini untuk ditinjau.</p>
                </div>
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
