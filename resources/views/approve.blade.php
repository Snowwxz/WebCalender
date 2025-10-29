<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve - SiKota</title>
    <!-- Base CSS -->
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <!-- Component CSS -->
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/approve.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="app-container">
        @include('layouts.header')

        <main class="main-content" style="margin-left: 0; width: 100%; overflow-x: hidden;">
            <div class="approval-page">
                <div class="approval-header">
                    @if (Auth::user()->role !== 'admin')
                        <div>
                            <a href="{{ route('dashboard.bulan') }}" title="Kembali ke Dashboard"
                                aria-label="Kembali ke Dashboard">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                        </div>
                    @endif

                    <div class="title-wrap">
                        <h1 class="page-title">Kelola Agenda Kegiatan</h1>
                        <p class="page-subtitle">Tinjau dan setujui proposal agenda kegiatan dari berbagai dinas dan
                            instansi.</p>
                    </div>

                    @php($status = request('status', 'all'))
                    <div class="status-tabs-wrap">
                        <div class="status-tabs">
                            <a href="{{ route('approve', ['status' => 'all']) }}"
                                class="status-tab {{ $status === 'all' ? 'active' : '' }}">
                                <span>Semua</span>
                                <span class="badge">{{ $countAll }}</span>
                            </a>
                            <a href="{{ route('approve', ['status' => 'pending']) }}"
                                class="status-tab {{ $status === 'pending' ? 'active' : '' }}">
                                <span>Menunggu</span>
                                <span class="badge">{{ $countPending }}</span>
                            </a>
                            <a href="{{ route('approve', ['status' => 'approved']) }}"
                                class="status-tab {{ $status === 'approved' ? 'active' : '' }}">
                                <span>Disetujui</span>
                                <span class="badge">{{ $countApproved }}</span>
                            </a>
                            <a href="{{ route('approve', ['status' => 'rejected']) }}"
                                class="status-tab {{ $status === 'rejected' ? 'active' : '' }}">
                                <span>Ditolak</span>
                                <span class="badge">{{ $countRejected }}</span>
                            </a>
                        </div>
                    </div>

                    <div class="search-bar">
                        <form method="GET" action="{{ route('approve') }}">
                            <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                            <div class="search-input-wrap">
                                <input type="text" name="q"
                                    placeholder="Cari agenda, instansi, atau deskripsi..." value="{{ request('q') }}">
                                <button type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                        </form>
                    </div>
                </div>
                <div class="sort-container">
                    <form method="GET" action="{{ route('approve') }}" class="sort-form">
                        <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                        <input type="hidden" name="q" value="{{ request('q') }}">
                        <select name="sort" onchange="this.form.submit()" class="sort-select">
                            <option value="newest_submitted"
                                {{ request('sort') == 'newest_submitted' ? 'selected' : '' }}>
                                📥 Paling Baru Diajukan
                            </option>
                            <option value="oldest_submitted"
                                {{ request('sort') == 'oldest_submitted' ? 'selected' : '' }}>
                                🕰️ Paling Lama Diajukan
                            </option>
                            <option value="earliest_event" {{ request('sort') == 'earliest_event' ? 'selected' : '' }}>
                                📅 Tanggal Pelaksanaan Terdekat
                            </option>
                            <option value="latest_event" {{ request('sort') == 'latest_event' ? 'selected' : '' }}>
                                📆 Tanggal Pelaksanaan Terjauh
                            </option>
                        </select>
                    </form>
                </div>


                @if ($agendas->isEmpty())
                    <div class="approval-empty">
                        <div class="empty-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h2>Belum ada pengajuan agenda</h2>
                        <p>Pengajuan agenda yang sudah dibuat akan muncul di sini untuk ditinjau.</p>
                    </div>
                @else
                    <div class="approval-list">
                        @foreach ($agendas as $agenda)
                            <div class="approval-card">
                                <!-- Card Header -->
                                <div class="card-header">
                                    <div class="card-title-section">
                                        <h3 class="card-title">{{ $agenda->agenda_name }}</h3>
                                        <p class="card-description">{{ $agenda->description ?? '-' }}</p>
                                    </div>
                                    <div class="status-badge {{ $agenda->status }}">
                                        @switch($agenda->status)
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
                                                {{ ucfirst($agenda->status) }}
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
                                                    {{ $agenda->unit->unit_name ?? '-' }}
                                                </span>
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
                                        </div>

                                        <div class="details-right">
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

                                            <div class="detail-item">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <span><strong>Lokasi:</strong> {{ $agenda->location ?? '-' }}</span>
                                            </div>
                                            <div class="detail-item participants">
                                                <i class="fas fa-people-group"></i>
                                                <span><strong>Instansi Terlibat:</strong>
                                                    {{ $agenda->involved_institution ?? '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-eye"></i>
                                            <span>
                                                <strong>Status:</strong>
                                                {{ $agenda->is_public ? 'Publik' : 'Privasi' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Footer -->
                                <div class="card-footer">
                                    <div class="submission-info">
                                        <i class="fas fa-user"></i>
                                        <span class="submission-unit">
                                            {{ $agenda->unit->unit_name ?? ($agenda->units ?? '') }}
                                        </span>

                                        @if ($agenda->unit || $agenda->units)
                                            <span class="separator">&nbsp;–&nbsp;</span>
                                        @endif

                                        <span class="submission-time">
                                            Diajukan
                                            {{ \Carbon\Carbon::parse($agenda->created_at)->locale('id')->diffForHumans() }}
                                        </span>
                                    </div>



                                    @if ($agenda->status === 'pending')
                                        <div class="approval-actions">
                                            <form action="{{ route('agenda.updateStatus', $agenda->id_agenda) }}"
                                                method="POST" class="action-form">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn-reject">
                                                    <i class="fas fa-times"></i> Tolak
                                                </button>
                                            </form>
                                            <form action="{{ route('agenda.updateStatus', $agenda->id_agenda) }}"
                                                method="POST" class="action-form">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="btn-approve">
                                                    <i class="fas fa-check"></i> Setujui
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <div class="approval-actions">
                                            <span class="validated-text">
                                                <i class="fas fa-circle-check"></i>
                                                Agenda sudah divalidasi ({{ ucfirst($agenda->status) }})
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </main>


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

        <script>
            // 🔎 Auto reload saat search bar dikosongkan
            document.querySelector('input[name="q"]').addEventListener('input', function() {
                if (this.value.trim() === '') {
                    // Ambil URL tanpa parameter 'q'
                    const url = new URL(window.location.href);
                    url.searchParams.delete('q');
                    window.location.href = url.toString();
                }
            });
        </script>

</body>

</html>
