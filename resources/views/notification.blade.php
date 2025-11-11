@extends('layouts.main')

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/notification.css') }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        </push>

        @section('content')
            <div class="notification-page">
                <div class="notification-header">
                    <div>
                        <a href="{{ route('dashboard.bulan') }}" title="Kembali ke Dashboard" aria-label="Kembali ke Dashboard">
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
                                                <span><strong>Pelaksana:</strong>
                                                    {{ $item->unit->unit_name ?? '-' }}</span>
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
                                                <span><strong>Dihadiri:</strong>
                                                    {{ $item->involved_institution ?? '-' }}</span>
                                            </div>
                                            @if ($item->status === 'rejected' && !empty($item->reason))
                                                <div class="detail-item">
                                                    <i class="fas fa-comment-dots"></i>
                                                    <span><strong>Alasan Ditolak:</strong> {{ $item->reason }}</span>
                                                </div>
                                            @endif
                                            @if (!empty($item->notes))
                                                <div class="detail-item">
                                                    <i class="fas fa-sticky-note"></i>
                                                    <span><strong>Catatan:</strong> {{ $item->notes }}</span>
                                                </div>
                                            @endif
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
                                                - Diajukan
                                                {{ \Carbon\Carbon::parse($item->created_at)->locale('id')->diffForHumans() }}
                                            </span>
                                        </div>

                                        <div class="notification-actions">
                                            @if ($item->status === 'pending' || $item->status === 'rejected')
                                                <a href="{{ route('agenda.edit', $item->id_agenda) }}" class="btn-edit">
                                                    <i class="fas fa-pen"></i> Edit Agenda
                                                </a>
                                            @endif

                                            @if ($item->status !== 'approved')
                                                <form action="{{ route('agenda.destroy', $item->id_agenda) }}" method="POST"
                                                    class="action-form delete-form" style="display:inline;">
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
                        <a href="{{ $agenda->url(1) }}" class="page-control {{ $current === 1 ? 'disabled' : '' }}"
                            aria-label="First" title="Halaman pertama">«</a>
                        <a href="{{ $agenda->previousPageUrl() ?? '#' }}"
                            class="page-control {{ $current === 1 ? 'disabled' : '' }}" aria-label="Previous"
                            title="Sebelumnya">‹</a>

                        @if ($start > 1)
                            <a href="{{ $agenda->url(1) }}" class="page-number">1</a>
                            @if ($start > 2)
                                <span class="page-ellipsis">…</span>
                            @endif
                        @endif

                        @for ($i = $start; $i <= $end; $i++)
                            <a href="{{ $agenda->url($i) }}" class="page-number {{ $i === $current ? 'active' : '' }}"
                                aria-current="{{ $i === $current ? 'page' : 'false' }}">{{ $i }}</a>
                        @endfor

                        @if ($end < $last)
                            @if ($end < $last - 1)
                                <span class="page-ellipsis">…</span>
                            @endif
                            <a href="{{ $agenda->url($last) }}" class="page-number">{{ $last }}</a>
                        @endif

                        <a href="{{ $agenda->nextPageUrl() ?? '#' }}"
                            class="page-control {{ $current === $last ? 'disabled' : '' }}" aria-label="Next"
                            title="Berikutnya">›</a>
                        <a href="{{ $agenda->url($last) }}" class="page-control {{ $current === $last ? 'disabled' : '' }}"
                            aria-label="Last" title="Halaman terakhir">»</a>
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

            <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const deleteForms = document.querySelectorAll('.delete-form');

                    deleteForms.forEach(form => {
                        form.addEventListener('submit', function(e) {
                            e.preventDefault(); // cegah submit langsung

                            // Toastify konfirmasi
                            const toastContent = document.createElement('div');
                            toastContent.innerHTML = `
                        <div style="
                            font-family: 'Poppins', sans-serif;
                            color: #2F3E35;
                            font-weight: 500;
                            font-size: 15px;
                            margin-bottom: 12px;
                        ">
                            Yakin ingin menghapus agenda ini?
                        </div>
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            <button id="confirmDelete" style="
                                background: #F7B7B7;
                                border: none;
                                padding: 7px 16px;
                                border-radius: 8px;
                                color: #7A1C1C;
                                font-weight: 600;
                                font-family: 'Poppins', sans-serif;
                                cursor: pointer;
                                transition: all 0.25s ease;
                            "
                            onmouseover="this.style.background='#F4A8A8'; this.style.color='#691414';"
                            onmouseout="this.style.background='#F7B7B7'; this.style.color='#7A1C1C';"
                            onmousedown="this.style.background='#E68D8D'; this.style.color='#5C1111';"
                            onmouseup="this.style.background='#F4A8A8'; this.style.color='#691414';">
                                Ya, hapus!
                            </button>

                            <button id="cancelDelete" style="
                                background: #E6E7E8;
                                border: none;
                                padding: 7px 16px;
                                border-radius: 8px;
                                color: #2F3E35;
                                font-weight: 600;
                                font-family: 'Poppins', sans-serif;
                                cursor: pointer;
                                transition: all 0.2s ease;
                            "
                            onmouseover="this.style.background='#D9DADB';"
                            onmouseout="this.style.background='#E6E7E8';">
                                Batal
                            </button>
                        </div>
                    `;

                            const toast = Toastify({
                                node: toastContent,
                                duration: -1,
                                gravity: "top",
                                position: "center",
                                stopOnFocus: true,
                                close: false,
                                offset: {
                                    x: 0,
                                    y: 20
                                },
                                style: {
                                    background: "#FFF1E6",
                                    border: "1px solid #F7B7B7",
                                    borderRadius: "12px",
                                    padding: "18px 24px",
                                    boxShadow: "0 6px 20px rgba(0, 0, 0, 0.08)",
                                    textAlign: "center",
                                    display: "flex",
                                    flexDirection: "column",
                                    alignItems: "center",
                                    animation: "fadeIn 0.3s ease",
                                },
                            }).showToast();

                            toastContent.querySelector('#confirmDelete').addEventListener('click', () => {
                                toast.hideToast(); // tutup konfirmasi
                                form.submit(); // kirim form
                            });

                            toastContent.querySelector('#cancelDelete').addEventListener('click', () => {
                                toast.hideToast();
                            });
                        });
                    });
                });
            </script>

            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    @if (session('success'))
                        showSuccessToast("{{ session('success') }}");
                    @elseif (session('error'))
                        showErrorToast("{{ session('error') }}");
                    @endif
                });

                // Fungsi Toastify Success
                function showSuccessToast(message) {
                    const popup = document.createElement('div');
                    popup.className = 'toastify-popup toastify-success';
                    popup.innerHTML = `
                <i class="bi bi-check-circle-fill" style="color:#5FA776; font-size:16px;"></i>
                <span>${message}</span>
            `;
                    document.body.appendChild(popup);

                    popup.classList.add('toastify-popup-show');
                    setTimeout(() => {
                        popup.classList.remove('toastify-popup-show');
                        popup.classList.add('toastify-popup-hide');
                        setTimeout(() => popup.remove(), 300);
                    }, 2500);
                }

                // Fungsi Toastify Error
                function showErrorToast(message) {
                    const popup = document.createElement('div');
                    popup.className = 'toastify-popup toastify-error';
                    popup.innerHTML = `
                <i class="bi bi-x-circle-fill" style="color:#EF4444; font-size:16px;"></i>
                <span>${message}</span>
            `;
                    document.body.appendChild(popup);

                    popup.classList.add('toastify-popup-show');
                    setTimeout(() => {
                        popup.classList.remove('toastify-popup-show');
                        popup.classList.add('toastify-popup-hide');
                        setTimeout(() => popup.remove(), 300);
                    }, 3000);
                }
            </script>

            <script>
                // �� Reload otomatis saat search dikosongkan
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

        <style>
            .toastify-success {
                position: fixed;
                top: 90px;
                left: 50%;
                transform: translateX(-50%);
                background: #E9F4EC;
                color: #234B2C;
                border: 1px solid #A6C8A3;
                border-radius: 6px;
                padding: 8px 18px;
                font-size: 14px;
                font-weight: 500;
                font-family: 'Poppins', sans-serif;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
                z-index: 9999;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                width: auto;
                max-width: 300px;
                min-height: unset;
            }

            .toastify-error {
                position: fixed;
                top: 90px;
                left: 50%;
                transform: translateX(-50%);
                background: #FEE2E2;
                color: #991B1B;
                border: 1px solid #FCA5A5;
                border-radius: 6px;
                padding: 8px 18px;
                font-size: 14px;
                font-weight: 500;
                font-family: 'Poppins', sans-serif;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
                z-index: 9999;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                width: auto;
                max-width: 300px;
                min-height: unset;
            }

            .toastify-popup-show {
                animation: toastIn 0.35s ease forwards;
            }

            .toastify-popup-hide {
                animation: toastOut 0.25s ease forwards;
            }

            @keyframes toastIn {
                0% {
                    opacity: 0;
                    transform: translate(-50%, -30px) scale(0.95);
                }

                80% {
                    opacity: 1;
                    transform: translate(-50%, 8px) scale(1.03);
                }

                100% {
                    opacity: 1;
                    transform: translate(-50%, 0) scale(1);
                }
            }

            @keyframes toastOut {
                from {
                    opacity: 1;
                    transform: translate(-50%, 0) scale(1);
                }

                to {
                    opacity: 0;
                    transform: translate(-50%, -10px) scale(0.95);
                }
            }
        </style>
