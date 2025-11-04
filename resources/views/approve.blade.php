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

        <!-- Reject Confirmation Modal (UI only) -->
        <div id="rejectModal"
            style="display:none; position: fixed; inset: 0; background: rgba(0,0,0,0.35); z-index: 9999; align-items: center; justify-content: center;">
            <div
                style="width: 520px; max-width: 92vw; background: #fff; border-radius: 14px; box-shadow: 0 12px 32px rgba(0,0,0,0.18); overflow: hidden; font-family: 'Poppins', sans-serif;">
                <div
                    style="padding: 18px 22px; border-bottom: 1px solid #eef0f2; display:flex; align-items:center; justify-content: space-between;">
                    <h3 style="margin:0; font-size:18px; font-weight:600; color:#111827;">Konfirmasi Penolakan</h3>
                    <button type="button" id="rejectModalClose" aria-label="Tutup"
                        style="background:none; border:0; font-size:20px; line-height:1; cursor:pointer; color:#6b7280;">×</button>
                </div>
                <div style="padding: 18px 22px;">
                    <p style="margin:0 0 10px; color:#374151;">Yakin ingin menolak agenda ini?</p>
                    <label for="rejectReason"
                        style="display:block; margin-bottom:8px; color:#374151; font-weight:500;">Alasan
                        penolakan</label>
                    <textarea id="rejectReason" placeholder="Tuliskan alasan penolakan (opsional)"
                        style="width:100%; min-height:100px; border:1px solid #e5e7eb; border-radius:10px; padding:10px 12px; outline:none; resize: vertical; font-family: inherit;"></textarea>
                </div>
                <div
                    style="display:flex; justify-content:flex-end; gap:10px; padding: 14px 22px; border-top:1px solid #eef0f2; background:#fafafa;">
                    <button type="button" id="rejectModalCancel"
                        style="background:#ffffff; border:1px solid #e5e7eb; color:#111827; border-radius:10px; padding:10px 14px; cursor:pointer;">Batal</button>
                    <button type="button" id="rejectModalConfirm"
                        style="background:#ef4444; border:1px solid #ef4444; color:#ffffff; border-radius:10px; padding:10px 14px; cursor:pointer;">Tolak</button>
                </div>
            </div>
        </div>

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
                    rejectModal.style.display = "flex";
                };

                // 🔹 Tutup modal
                [rejectModalClose, rejectModalCancel].forEach(btn => {
                    btn.addEventListener("click", () => {
                        rejectModal.style.display = "none";
                        currentAgendaId = null;
                    });
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
                        rejectModal.style.display = "none";
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
