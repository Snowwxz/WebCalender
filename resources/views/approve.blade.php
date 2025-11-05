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

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div class="app-container">
        @include('layouts.header')

        <main class="main-content" style="margin-left: 0; width: 100%; overflow-x: hidden;">
            <div class="approval-page">
                <div class="approval-header">
                    <div>
                        <a href="{{ route('dashboard.bulan') }}" title="Kembali ke Dashboard"
                            aria-label="Kembali ke Dashboard">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

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

                                            <!-- ✅ Status dimasukkan ke dalam details-left biar sejajar -->
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

                                            @if ($agenda->status === 'rejected' && !empty($agenda->reason))
                                                <div class="detail-item">
                                                    <i class="fas fa-comment-dots"></i>
                                                    <span><strong>Alasan Ditolak:</strong> {{ $agenda->reason }}</span>
                                                </div>
                                            @endif
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
                                            <button type="button" class="btn-reject"
                                                onclick="openRejectModal({{ $agenda->id_agenda }})">
                                                <i class="fas fa-times"></i> Tolak
                                            </button>
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

        <!-- Reject Confirmation Modal (SiKota Style) -->
        <div id="rejectModal" class="reject-modal-overlay">
            <div class="reject-modal-container">
                <!-- Header -->
                <div class="reject-modal-header">
                    <div class="reject-modal-title-wrap">
                        <div class="reject-modal-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h3 class="reject-modal-title">Konfirmasi Penolakan</h3>
                    </div>
                    <button type="button" id="rejectModalClose" class="reject-modal-close close-modal"
                        aria-label="Tutup">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="reject-modal-body">
                    <p class="reject-modal-question">Yakin ingin menolak agenda ini?</p>
                    <label for="rejectReason" class="reject-modal-label">
                        <i class="fas fa-comment-dots"></i>
                        Alasan penolakan
                    </label>
                    <textarea id="rejectReason" placeholder="Tuliskan alasan penolakan (opsional)" class="reject-modal-textarea"
                        rows="4"></textarea>
                </div>

                <!-- Footer -->
                <div class="reject-modal-footer">
                    <button type="button" id="rejectModalConfirm" class="reject-btn-confirm">
                        <i class="fas fa-ban"></i>
                        Tolak
                    </button>
                </div>
            </div>
        </div>

        <style>
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: scale(0.95);
                }

                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }

            #rejectModal textarea:focus {
                border-color: #82A98D;
                box-shadow: 0 0 0 2px rgba(130, 169, 141, 0.2);
            }

            #rejectModal button:hover#rejectModalCancel {
                background: #f3f4f6;
            }

            #rejectModal button:hover#rejectModalConfirm {
                background: #b91c1c;
                border-color: #b91c1c;
            }

            .reject-modal-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 9999;
                align-items: center;
                justify-content: center;
                font-family: 'Poppins', sans-serif;
                animation: fadeInOverlay 0.3s ease;
            }

            .reject-modal-overlay.show {
                display: flex !important;
            }

            .reject-modal-container {
                width: 520px;
                max-width: 92vw;
                background: #ffffff;
                border-radius: 18px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
                overflow: hidden;
                animation: slideUpModal 0.3s ease;
                transform: translateY(20px);
                opacity: 0;
            }

            .reject-modal-overlay.show .reject-modal-container,
            .reject-modal-overlay[style*="flex"] .reject-modal-container {
                transform: translateY(0);
                opacity: 1;
            }

            .reject-modal-header {
                padding: 24px 28px;
                border-bottom: 1px solid #e9ecef;
                background: linear-gradient(135deg, #fff5f5 0%, #ffe5e5 100%);
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .reject-modal-title-wrap {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .reject-modal-icon {
                width: 40px;
                height: 40px;
                background: #fee2e2;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #ef4444;
                font-size: 20px;
                flex-shrink: 0;
            }

            .reject-modal-title {
                margin: 0;
                font-size: 20px;
                font-weight: 700;
                color: #1f2937;
            }

            .reject-modal-close.close-modal {
                background: transparent;
                border: none;
                font-size: 22px;
                color: #ef4444;
                cursor: pointer;
                transition: color 0.2s ease, transform 0.3s ease;
                margin-left: auto;
                padding: 4px 8px;
            }

            .reject-modal-close.close-modal:hover {
                color: #ef4444;
                transform: rotate(90deg);
            }

            .reject-modal-close:hover {
                background: transparent;
            }

            .reject-modal-body {
                padding: 28px;
            }

            .reject-modal-question {
                margin: 0 0 20px 0;
                color: #374151;
                font-size: 16px;
                font-weight: 500;
                line-height: 1.5;
            }

            .reject-modal-label {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 10px;
                color: #374151;
                font-weight: 600;
                font-size: 14px;
            }

            .reject-modal-label i {
                color: #6E9579;
                font-size: 14px;
            }

            .reject-modal-textarea {
                width: 100%;
                min-height: 120px;
                border: 2px solid #e5e7eb;
                border-radius: 12px;
                padding: 12px 16px;
                outline: none;
                resize: vertical;
                font-family: 'Poppins', sans-serif;
                font-size: 14px;
                color: #374151;
                transition: all 0.3s ease;
                background: #fafafa;
            }

            .reject-modal-textarea:focus {
                border-color: #6E9579;
                background: #ffffff;
                box-shadow: 0 0 0 4px rgba(110, 149, 121, 0.1);
            }

            .reject-modal-textarea::placeholder {
                color: #9ca3af;
            }

            .reject-modal-footer {
                display: flex;
                justify-content: flex-end;
                gap: 12px;
                padding: 20px 28px;
                border-top: 1px solid #e9ecef;
                background: #fafafa;
            }

            .reject-btn-confirm {
                background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                color: #ffffff;
                box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
                border: none;
                border-radius: 10px;
                font-family: 'Poppins', sans-serif;
                font-weight: 600;
                font-size: 14px;
                cursor: pointer;
                padding: 10px 22px;
                transition: background 0.25s ease, box-shadow 0.25s ease, transform 0.15s ease;
                display: flex;
                align-items: center;
                gap: 8px;
                will-change: transform, box-shadow, background;
                position: relative;
            }

            .reject-btn-confirm:hover {
                background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
                transform: translateY(-1px);
                box-shadow: 0 6px 18px rgba(239, 68, 68, 0.35);
            }

            .reject-btn-confirm:active {
                transform: translateY(0);
                box-shadow: 0 3px 8px rgba(239, 68, 68, 0.25);
            }

            .reject-btn-confirm:focus {
                outline: none;
                box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.25);
            }

            @keyframes fadeInOverlay {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            @keyframes slideUpModal {
                from {
                    transform: translateY(30px);
                    opacity: 0;
                }

                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            /* Responsive */
            @media (max-width: 640px) {
                .reject-modal-container {
                    width: 95vw;
                    margin: 20px;
                }

                .reject-modal-header,
                .reject-modal-body,
                .reject-modal-footer {
                    padding: 20px;
                }

                .reject-modal-title {
                    font-size: 18px;
                }

                .reject-btn-cancel,
                .reject-btn-confirm {
                    padding: 8px 16px;
                    font-size: 13px;
                }
            }
        </style>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const rejectModal = document.getElementById("rejectModal");
                const rejectModalClose = document.getElementById("rejectModalClose");
                const rejectModalCancel = document.getElementById("rejectModalCancel");
                const rejectModalConfirm = document.getElementById("rejectModalConfirm");
                const rejectReason = document.getElementById("rejectReason");
                let currentAgendaId = null;

                // 🔹 Fungsi untuk buka modal
                window.openRejectModal = (agendaId) => {
                    currentAgendaId = agendaId;
                    rejectReason.value = "";
                    rejectModal.classList.add('show');
                    rejectModal.style.display = 'flex'; // <-- pastikan kelihatan
                };

                // 🔹 Tutup modal (tombol close & batal)
                [rejectModalClose, rejectModalCancel].forEach(btn => {
                    btn.addEventListener("click", () => {
                        rejectModal.classList.remove('show');
                        rejectModal.style.display = 'none'; // <-- sembunyikan
                        currentAgendaId = null;
                    });
                });

                // (opsional) klik di luar kontainer untuk menutup
                rejectModal.addEventListener('click', (e) => {
                    if (e.target === rejectModal) {
                        rejectModal.classList.remove('show');
                        rejectModal.style.display = 'none';
                        currentAgendaId = null;
                    }
                });

                // 🔹 Fungsi konfirmasi penolakan
                rejectModalConfirm.addEventListener("click", async () => {
                    if (!currentAgendaId) return;

                    const reason = rejectReason.value.trim();

                    try {
                        const res = await fetch(`/dashboard/agenda/${currentAgendaId}/reject`, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                                    .content
                            },
                            body: JSON.stringify({
                                reason
                            })
                        });

                        const data = await res.json();
                        if (!res.ok || !data.success) {
                            Swal.fire("Gagal", data.message || "Gagal menolak agenda.", "error");
                            return;
                        }

                        // 🔹 Tutup modal & tampilkan notifikasi
                        rejectModal.classList.remove('show');
                        rejectModal.style.display = "none"; // backup
                        Swal.fire({
                            icon: "success",
                            title: "Agenda Ditolak",
                            text: data.message || "Agenda telah berhasil ditolak.",
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // 🔹 Update tampilan kartu secara langsung
                        const card = document.querySelector(
                            `button[onclick="openRejectModal(${currentAgendaId})"]`)?.closest(
                            ".approval-card");
                        if (card) {
                            // ubah badge status jadi Ditolak
                            const badge = card.querySelector(".status-badge");
                            if (badge) {
                                badge.className = "status-badge rejected";
                                badge.textContent = "Ditolak";
                            }

                            // tambahkan alasan di bawah jika ada
                            if (reason) {
                                let reasonItem = card.querySelector(".detail-item .fa-comment-dots");
                                if (!reasonItem) {
                                    const detailsGrid = card.querySelector(".details-grid");
                                    const reasonDiv = document.createElement("div");
                                    reasonDiv.className = "detail-item";
                                    reasonDiv.innerHTML = `
                            <i class="fas fa-comment-dots"></i>
                            <span><strong>Alasan Ditolak:</strong> ${reason}</span>
                        `;
                                    detailsGrid.appendChild(reasonDiv);
                                }
                            }

                            // ubah tombol jadi teks “Agenda sudah divalidasi (Ditolak)”
                            const footer = card.querySelector(".card-footer .approval-actions");
                            if (footer) {
                                footer.innerHTML = `
                        <span class="validated-text">
                            <i class="fas fa-circle-check"></i>
                            Agenda sudah divalidasi (Ditolak)
                        </span>
                    `;
                            }
                        }

                        currentAgendaId = null;
                    } catch (error) {
                        console.error(error);
                        Swal.fire("Error", "Terjadi kesalahan koneksi ke server.", "error");
                    }
                });
            });
        </script>


        <div id="toast" class="toastify" style="display: none;">
            <p>Agenda telah disetujui</p>
        </div>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
        <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

        <script>
            // ========== HANDLE SUBMIT ==========
            actionForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const status = form.querySelector('input[name="status"]').value;

                    fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: new URLSearchParams({
                                ...Object.fromEntries(new FormData(form)),
                                '_method': 'PUT'
                            })
                        })
                        .then(response => {
                            if (response.ok) {
                                // simpan pesan untuk ditampilkan setelah reload
                                if (status === 'approved') {
                                    sessionStorage.setItem('toastMessage',
                                        'Agenda telah disetujui');
                                    sessionStorage.setItem('toastType', 'success');
                                } else {
                                    sessionStorage.setItem('toastMessage',
                                        'Agenda telah ditolak');
                                    sessionStorage.setItem('toastType', 'error');
                                }
                                location.reload(); // reload langsung tanpa delay
                            } else {
                                response.text().then(text => {
                                    console.error('Error response:', response.status,
                                        text);
                                    alert(
                                        'Gagal memperbarui agenda. Cek console untuk detail.'
                                    );
                                });
                            }
                        })
                        .catch(err => console.error('Fetch error:', err));
                });
            });

            // ======== TOASTIFY ========
            function showToast(message, type = "success") {
                const toastNode = document.createElement('div');
                toastNode.innerHTML = `
                <div style="
                    font-family: 'Poppins', sans-serif;
                    font-weight: 500;
                    font-size: 15px;
                    color: ${type === 'success' ? '#256D43' : '#8b0000'};
                ">
                    ${message}
                </div>
            `;

                Toastify({
                    node: toastNode,
                    duration: 2500,
                    gravity: "top",
                    position: "center",
                    style: {
                        background: type === 'success' ? '#E6F9EE' : '#fde4e4',
                        border: type === 'success' ? '1px solid #C4E7D0' : '1px solid #f8b4b4',
                        borderRadius: '10px',
                        padding: '14px 28px',
                        boxShadow: '0 6px 14px rgba(0,0,0,0.08)',
                        display: 'flex',
                        justifyContent: 'center',
                        alignItems: 'center',
                    }
                }).showToast();
            }
        </script>

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
