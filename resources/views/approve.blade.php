@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/approve.css') }}">
@endpush

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="approval-page">
        <div class="approval-header">
            <div>
                <a href="{{ route('dashboard.bulan') }}" title="Kembali ke Dashboard" aria-label="Kembali ke Dashboard">
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
                        <input type="text" name="q" placeholder="Cari agenda, instansi, atau deskripsi..."
                            value="{{ request('q') }}">
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
                    <option value="newest_submitted" {{ request('sort') == 'newest_submitted' ? 'selected' : '' }}>
                        📥 Paling Baru Diajukan
                    </option>
                    <option value="oldest_submitted" {{ request('sort') == 'oldest_submitted' ? 'selected' : '' }}>
                        🕰 Paling Lama Diajukan
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
                                <h3 class="card-title"
                                    style="white-space: normal; overflow-wrap: anywhere; word-break: break-word;">
                                    {{ $agenda->agenda_name }}</h3>
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
                                        <span><strong>Pelaksana:</strong>
                                            {{ $agenda->unit->unit_name ?? '-' }}
                                        </span>
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
                                    @if (!empty($agenda->notes))
                                        <div class="detail-item">
                                            <i class="fa-solid fa-file-lines"></i>
                                            <span><strong>Catatan:</strong> {{ $agenda->notes }}</span>
                                        </div>
                                    @endif
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
                                        <span><strong>Dihadiri:</strong>
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
                                @if ($agenda->logs_count > 0)
                                    <span class="update-badge" onclick="showUpdateModal({{ $agenda->id_agenda }})"
                                        style="cursor: pointer;">
                                        <i class="fas fa-sync-alt"></i> Has been updated
                                    </span>
                                @endif
                            </div>



                            @if ($agenda->status === 'pending')
                                <div class="approval-actions">
                                    <button type="button" class="btn-reject"
                                        onclick="openRejectModal({{ $agenda->id_agenda }})">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                    <form action="{{ route('agenda.updateStatus', $agenda->id_agenda) }}" method="POST"
                                        class="action-form approve-form" data-agenda-id="{{ $agenda->id_agenda }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="button" class="btn-approve"
                                            onclick="showApproveConfirm({{ $agenda->id_agenda }})">
                                            <i class="fas fa-check"></i> Setujui
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="approval-actions">

                                    @if ($agenda->status === 'approved')
                                        <a href="{{ route('agenda.edit', ['id_agenda' => $agenda->id_agenda, 'from' => 'approve']) }}"
                                            class="btn-edit">
                                            <i class="fas fa-pen"></i> Edit Agenda
                                        </a>
                                    @else
                                        <span class="validated-text">
                                            <i class="fas fa-circle-check"></i>
                                            Agenda sudah divalidasi
                                            ({{ ucfirst($agenda->status) }})
                                        </span>
                                    @endif

                                </div>
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Update Log Modal -->
    <div id="updateLogModal" class="reject-modal-overlay">
        <div class="reject-modal-container" style="max-width: 700px;">
            <!-- Header -->
            <div class="reject-modal-header">
                <div class="reject-modal-title-wrap">
                    <div class="reject-modal-icon" style="background: #dbeafe; color: #3b82f6;">
                        <i class="fas fa-history"></i>
                    </div>
                    <h3 class="reject-modal-title">Riwayat Update Agenda</h3>
                </div>
                <button type="button" id="updateLogModalClose" class="reject-modal-close close-modal"
                    aria-label="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="reject-modal-body" id="updateLogModalBody" style="max-height: 500px; overflow-y: auto;">
                <div style="text-align: center; padding: 40px;">
                    <div class="spinner-border" role="status" style="color: #6E9579;">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p style="margin-top: 16px; color: #6b7280;">Memuat data...</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="reject-modal-footer">
                <button type="button" id="updateLogModalCloseBtn" class="reject-btn-confirm"
                    style="background: linear-gradient(135deg, #6E9579 0%, #55745f 100%);">
                    <i class="fas fa-times"></i>
                    Tutup
                </button>
            </div>
        </div>
    </div>

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
                <button type="button" id="rejectModalClose" class="reject-modal-close close-modal" aria-label="Tutup">
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

    <!-- Toastify CSS & JS harus dimuat sebelum script yang menggunakan showToast -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
        // ======== UPDATE LOG MODAL ========
        const updateLogModal = document.getElementById("updateLogModal");
        const updateLogModalClose = document.getElementById("updateLogModalClose");
        const updateLogModalCloseBtn = document.getElementById("updateLogModalCloseBtn");
        const updateLogModalBody = document.getElementById("updateLogModalBody");
        const sidebar = document.querySelector('.sidebar');

        function closeUpdateLogModal() {
            updateLogModal.classList.remove('show');
            updateLogModal.style.display = 'none';
            updateLogModal.style.opacity = '';
            if (sidebar) sidebar.classList.remove('dimmed');
        }

        [updateLogModalClose, updateLogModalCloseBtn].forEach(btn => {
            if (btn) {
                btn.addEventListener("click", closeUpdateLogModal);
            }
        });

        updateLogModal.addEventListener('click', (e) => {
            if (e.target === updateLogModal) {
                closeUpdateLogModal();
            }
        });

        window.showUpdateModal = async function(agendaId) {
            updateLogModal.classList.add('show');
            updateLogModal.style.display = 'flex';
            updateLogModal.style.opacity = '1';
            if (sidebar) sidebar.classList.add('dimmed');

            // Reset body content
            updateLogModalBody.innerHTML = `
                <div style="text-align: center; padding: 40px;">
                    <div class="spinner-border" role="status" style="color: #6E9579;">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p style="margin-top: 16px; color: #6b7280;">Memuat data...</p>
                </div>
            `;

            try {
                const response = await fetch(`/dashboard/api/agenda/${agendaId}/logs`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    updateLogModalBody.innerHTML = `
                        <div style="text-align: center; padding: 40px; color: #ef4444;">
                            <i class="fas fa-exclamation-circle" style="font-size: 48px; margin-bottom: 16px;"></i>
                            <p>Gagal memuat data log agenda.</p>
                        </div>
                    `;
                    return;
                }

                if (!data.logs || data.logs.length === 0) {
                    updateLogModalBody.innerHTML = `
                        <div style="text-align: center; padding: 40px; color: #6b7280;">
                            <i class="fas fa-info-circle" style="font-size: 48px; margin-bottom: 16px;"></i>
                            <p>Tidak ada riwayat update untuk agenda ini.</p>
                        </div>
                    `;
                    return;
                }

                // Render logs
                let html = '<div style="display: flex; flex-direction: column; gap: 20px;">';
                data.logs.forEach((log, index) => {
                    html += `
                        <div style="border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; background: #fafafa;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                                <div>
                                    <div style="font-weight: 600; color: #1f2937; margin-bottom: 4px;">
                                        Update #${data.logs.length - index}
                                    </div>
                                    <div style="font-size: 13px; color: #6b7280;">
                                        <i class="fas fa-clock" style="margin-right: 6px;"></i>
                                        ${log.updated_at} (${log.updated_at_human})
                                    </div>
                                </div>
                            </div>
                            ${log.user ? `
                                        <div style="margin-bottom: 16px; padding: 12px; background: white; border-radius: 8px; border: 1px solid #e5e7eb;">
                                            <div style="font-size: 13px; color: #6b7280; margin-bottom: 4px;">Diupdate oleh:</div>
                                            <div style="font-weight: 600; color: #1f2937;">
                                                <i class="fas fa-user" style="margin-right: 6px; color: #6E9579;"></i>
                                                ${log.user.name}
                                            </div>
                                            <div style="font-size: 12px; color: #9ca3af; margin-top: 4px;">
                                                ${log.user.email}
                                            </div>
                                        </div>
                                    ` : ''}
                            ${log.changes && log.changes.length > 0 ? `
                                        <div>
                                            <div style="font-weight: 600; color: #1f2937; margin-bottom: 12px;">
                                                <i class="fas fa-edit" style="margin-right: 6px; color: #6E9579;"></i>
                                                Perubahan Data:
                                            </div>
                                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                                ${log.changes.map(change => `
                                            <div style="padding: 12px; background: white; border-radius: 8px; border-left: 3px solid #6E9579;">
                                                <div style="font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px;">
                                                    ${change.field}
                                                </div>
                                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px;">
                                                    <div>
                                                        <div style="color: #6b7280; margin-bottom: 4px;">Sebelumnya:</div>
                                                        <div style="color: #ef4444; font-weight: 500; padding: 6px; background: #fef2f2; border-radius: 4px;">
                                                            ${change.old_value}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div style="color: #6b7280; margin-bottom: 4px;">Sekarang:</div>
                                                        <div style="color: #059669; font-weight: 500; padding: 6px; background: #d1fae5; border-radius: 4px;">
                                                            ${change.new_value}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `).join('')}
                                            </div>
                                        </div>
                                    ` : '<div style="color: #6b7280; font-size: 14px;">Tidak ada perubahan data yang tercatat.</div>'}
                        </div>
                    `;
                });
                html += '</div>';
                updateLogModalBody.innerHTML = html;
            } catch (error) {
                console.error('Error loading update log:', error);
                updateLogModalBody.innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #ef4444;">
                        <i class="fas fa-exclamation-circle" style="font-size: 48px; margin-bottom: 16px;"></i>
                        <p>Terjadi kesalahan saat memuat data.</p>
                    </div>
                `;
            }
        };

        // ======== TOASTIFY FUNCTION (harus didefinisikan dulu) ========
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

        document.addEventListener("DOMContentLoaded", () => {
            // Tampilkan toast dari sessionStorage setelah page reload selesai
            const toastMessage = sessionStorage.getItem('toastMessage');
            const toastType = sessionStorage.getItem('toastType');

            if (toastMessage && toastType) {
                // Tunggu sedikit agar DOM benar-benar siap
                setTimeout(() => {
                    showToast(toastMessage, toastType);
                    // Hapus dari sessionStorage setelah ditampilkan
                    sessionStorage.removeItem('toastMessage');
                    sessionStorage.removeItem('toastType');
                }, 300);
            }
            const rejectModal = document.getElementById("rejectModal");
            const rejectModalClose = document.getElementById("rejectModalClose");
            const rejectModalConfirm = document.getElementById("rejectModalConfirm");
            const rejectReason = document.getElementById("rejectReason");
            const sidebar = document.querySelector('.sidebar');
            let currentAgendaId = null;

            // Pastikan semua elemen ada
            if (!rejectModal || !rejectModalClose || !rejectModalConfirm || !rejectReason) {
                console.error('Reject modal elements not found');
            }

            // 🔹 Fungsi untuk buka modal
            window.openRejectModal = (agendaId) => {
                currentAgendaId = agendaId;
                if (rejectReason) rejectReason.value = "";
                if (rejectModal) {
                    rejectModal.classList.add('show');
                    rejectModal.style.display = 'flex';
                    rejectModal.style.opacity = '1';
                }
                if (sidebar) sidebar.classList.add('dimmed');
            };

            // 🔹 Tutup modal (tombol close)
            if (rejectModalClose) {
                rejectModalClose.addEventListener("click", () => {
                    if (rejectModal) {
                        rejectModal.classList.remove('show');
                        rejectModal.style.display = 'none';
                        rejectModal.style.opacity = '';
                    }
                    if (sidebar) sidebar.classList.remove('dimmed');
                    currentAgendaId = null;
                });
            }

            // (opsional) klik di luar kontainer untuk menutup
            rejectModal.addEventListener('click', (e) => {
                if (e.target === rejectModal) {
                    rejectModal.classList.remove('show');
                    rejectModal.style.display = 'none';
                    rejectModal.style.opacity = '';
                    if (sidebar) sidebar.classList.remove('dimmed');
                    currentAgendaId = null;
                }
            });

            // 🔹 Fungsi konfirmasi penolakan
            rejectModalConfirm.addEventListener("click", async () => {
                if (!currentAgendaId) return;

                const reason = rejectReason.value.trim();
                const agendaId = currentAgendaId;

                // Disable button untuk mencegah double click
                rejectModalConfirm.disabled = true;
                const originalText = rejectModalConfirm.innerHTML;
                rejectModalConfirm.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        Swal.fire("Error", "CSRF token tidak ditemukan.", "error");
                        rejectModalConfirm.disabled = false;
                        rejectModalConfirm.innerHTML = originalText;
                        return;
                    }

                    const res = await fetch(`/dashboard/agenda/${agendaId}/reject`, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": csrfToken.content
                        },
                        body: JSON.stringify({
                            reason: reason
                        })
                    });

                    let data;
                    try {
                        data = await res.json();
                    } catch (jsonError) {
                        const text = await res.text();
                        console.error('Response is not JSON:', text.substring(0, 200));
                        Swal.fire("Error", "Server mengembalikan response yang tidak valid.", "error");
                        rejectModalConfirm.disabled = false;
                        rejectModalConfirm.innerHTML = originalText;
                        return;
                    }

                    if (!res.ok || !data.success) {
                        Swal.fire("Gagal", data.message || "Gagal menolak agenda.", "error");
                        rejectModalConfirm.disabled = false;
                        rejectModalConfirm.innerHTML = originalText;
                        return;
                    }

                    // 🔹 Tutup modal & tampilkan notifikasi
                    rejectModal.classList.remove('show');
                    rejectModal.style.display = "none";
                    rejectModal.style.opacity = '';
                    if (sidebar) sidebar.classList.remove('dimmed');
                    
                    // Tampilkan notifikasi sukses
                    Swal.fire({
                        icon: "success",
                        title: "Agenda Ditolak",
                        text: data.message || "Agenda telah berhasil ditolak.",
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // Reload halaman setelah notifikasi ditutup
                        location.reload();
                    });

                    currentAgendaId = null;
                } catch (error) {
                    console.error('Error rejecting agenda:', error);
                    Swal.fire("Error", "Terjadi kesalahan koneksi ke server: " + error.message, "error");
                    rejectModalConfirm.disabled = false;
                    rejectModalConfirm.innerHTML = originalText;
                }
            });
        });
    </script>


    <div id="toast" class="toastify" style="display: none;">
        <p>Agenda telah disetujui</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ========== HANDLE SUBMIT ==========
        const actionForms = document.querySelectorAll('.action-form');
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
                            if (status === 'approved') {
                                sessionStorage.setItem('toastMessage',
                                    'Agenda telah disetujui');
                                sessionStorage.setItem('toastType', 'success');
                            } else {
                                sessionStorage.setItem('toastMessage',
                                    'Agenda telah ditolak');
                                sessionStorage.setItem('toastType', 'error');
                            }
                            location.reload();
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

        // ========== KONFIRMASI APPROVE ==========
        window.showApproveConfirm = function(agendaId) {
            const toastContent = document.createElement('div');
            toastContent.innerHTML = `
                    <div style="
                        font-family: 'Poppins', sans-serif;
                        color: #2F3E35;
                        font-weight: 500;
                        font-size: 15px;
                        margin-bottom: 12px;
                    ">
                        Yakin ingin menyetujui agenda ini?
                    </div>
                    <div style="display: flex; gap: 8px; justify-content: center;">
                        <button id="confirmApprove" style="
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
                            Ya, setujui
                        </button>

                        <button id="cancelApprove" style="
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
                },
            }).showToast();

            toastContent.querySelector('#confirmApprove').addEventListener('click', async () => {
                toast.hideToast();
                // Cari form yang sesuai dengan agendaId
                const form = document.querySelector(`.approve-form[data-agenda-id="${agendaId}"]`);
                if (!form) return;

                // Disable button untuk mencegah double click
                const confirmBtn = toastContent.querySelector('#confirmApprove');
                const originalText = confirmBtn.innerHTML;
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = 'Memproses...';
                confirmBtn.style.opacity = '0.6';
                confirmBtn.style.cursor = 'not-allowed';

                try {
                    const formData = new FormData(form);
                    formData.append('_method', 'PUT');

                    // Tunggu response dari server dengan await - PASTIKAN menunggu
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content,
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'Accept': 'application/json'
                        },
                        body: new URLSearchParams(formData)
                    });

                    // Pastikan response benar-benar selesai sebelum lanjut
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    // Parse response JSON langsung - akan throw error jika bukan JSON
                    // TUNGGU sampai benar-benar selesai di-parse
                    // Clone response untuk bisa membaca ulang jika error
                    const clonedResponse = response.clone();
                    let data;
                    try {
                        data = await response.json();
                    } catch (jsonError) {
                        // Jika response bukan JSON, coba baca sebagai text untuk debugging
                        const text = await clonedResponse.text();
                        console.error('Response is not JSON:', text.substring(0, 200));
                        throw new Error('Server mengembalikan response yang tidak valid');
                    }

                    // Validasi response - pastikan success benar-benar true
                    // TUNGGU sampai semua validasi selesai
                    if (!data || typeof data !== 'object') {
                        throw new Error('Invalid response format');
                    }

                    // Validasi lebih ketat - pastikan success adalah true (boolean, bukan truthy)
                    const isSuccess = data.success === true &&
                        response.status >= 200 &&
                        response.status < 300 &&
                        typeof data.success === 'boolean';

                    if (isSuccess) {
                        // Simpan pesan ke sessionStorage untuk ditampilkan setelah reload
                        sessionStorage.setItem('toastMessage', 'Agenda telah disetujui');
                        sessionStorage.setItem('toastType', 'success');

                        // Reload langsung setelah proses selesai
                        location.reload();
                    } else {
                        // Tampilkan error dari response
                        showToast(data?.message || 'Gagal menyetujui agenda', 'error');
                        // Reset button
                        confirmBtn.disabled = false;
                        confirmBtn.innerHTML = originalText;
                        confirmBtn.style.opacity = '1';
                        confirmBtn.style.cursor = 'pointer';
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToast('Terjadi kesalahan saat menyetujui agenda', 'error');
                    // Reset button
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = originalText;
                    confirmBtn.style.opacity = '1';
                    confirmBtn.style.cursor = 'pointer';
                }
            });

            toastContent.querySelector('#cancelApprove').addEventListener('click', () => {
                toast.hideToast();
            });
        };
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
@endsection

<style>
    .sidebar {
        transition: none !important;
    }

    /* Dim sidebar when modal is open */
    .sidebar.dimmed {
        filter: brightness(0.5);
        pointer-events: none;
        /* prevent interactions behind modal */
    }

    /* Force reject modal overlay to appear instantly (no fade) */
    #rejectModal,
    .reject-modal-overlay,
    .reject-modal-overlay.show,
    .reject-modal-container {
        transition: none !important;
        animation: none !important;
    }
</style>
