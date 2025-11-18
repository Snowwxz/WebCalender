@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard-bulan.css') }}">
@endpush

@section('content')
    <div class="profile-page">
        <div class="profile-header">
            <div class="header-left">
                <a href="{{ url('/dashboard/bulan') }}" class="back-btn" title="Kembali ke Dashboard">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="header-title">
                    <h1 class="page-title">Profil Saya</h1>
                    <p class="page-subtitle">Kelola informasi profil dan pengaturan akun Anda</p>
                </div>
            </div>
        </div>

        <div class="profile-content">
            @if (session('status') === 'profile-updated')
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>Profil berhasil diperbarui</span>
                </div>
            @endif

            @if (session('status') === 'password-updated')
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>Password berhasil diperbarui</span>
                </div>
            @endif

            <!-- User Profile Header -->
            <div class="profile-header-card">
                <div class="profile-avatar-section">
                    <div class="profile-avatar-large">
                        @php
                            $initials = '';
                            $nameParts = explode(' ', $user->name);
                            if (count($nameParts) > 0) {
                                $initials = strtoupper(substr($nameParts[0], 0, 1));
                                if (count($nameParts) > 1) {
                                    $initials .= strtoupper(substr($nameParts[count($nameParts) - 1], 0, 1));
                                }
                            } else {
                                $initials = strtoupper(substr($user->name, 0, 1));
                            }
                        @endphp
                        <span class="avatar-initials">{{ $initials }}</span>
                    </div>
                </div>
                <div class="profile-info-section">
                    <h2 class="profile-name">{{ $user->name }}</h2>
                    <p class="profile-email">{{ $user->email }}</p>
                    <p class="profile-institution">{{ $user->unit->unit_name ?? 'Belum ada instansi' }}</p>
                    <button type="button" class="btn-edit-profile" onclick="openEditProfileModal()">
                        <i class="fas fa-pencil-alt"></i>
                        Edit Profile
                    </button>
                </div>
            </div>

            <!-- User's Agendas Card -->
            <div class="profile-card">
                <div class="card-header">
                    <div class="card-header-content">
                        <div class="card-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <h2 class="card-title">Daftar Agenda Saya</h2>
                            <p class="card-subtitle">Semua agenda yang telah Anda buat</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if($agendas->count() > 0)
                        <div class="agenda-list">
                            @foreach($agendas as $agenda)
                                <div class="agenda-item-card">
                                    <div class="agenda-item-header">
                                        <h3 class="agenda-item-title">{{ $agenda->agenda_name }}</h3>
                                        <span class="agenda-status status-{{ $agenda->status }}">
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
                                        </span>
                                    </div>
                                    <div class="agenda-item-details">
                                        <div class="agenda-detail">
                                            <i class="fas fa-calendar"></i>
                                            <span>{{ \Carbon\Carbon::parse($agenda->date)->locale('id')->translatedFormat('d F Y') }}</span>
                                        </div>
                                        @if($agenda->start_time && $agenda->end_time)
                                            <div class="agenda-detail">
                                                <i class="fas fa-clock"></i>
                                                <span>{{ \Carbon\Carbon::parse($agenda->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($agenda->end_time)->format('H:i') }}</span>
                                            </div>
                                        @endif
                                        @if($agenda->location)
                                            <div class="agenda-detail">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <span>{{ $agenda->location }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="agenda-item-actions">
                                        <a href="javascript:void(0)" class="btn-link"
                                           onclick="openAgendaDetailFromLink(this)"
                                           data-name="{{ $agenda->agenda_name }}"
                                           data-date="{{ \Carbon\Carbon::parse($agenda->date)->locale('id')->translatedFormat('d F Y') }}"
                                           data-start="{{ $agenda->start_time ? \Carbon\Carbon::parse($agenda->start_time)->format('H:i') : '' }}"
                                           data-end="{{ $agenda->end_time ? \Carbon\Carbon::parse($agenda->end_time)->format('H:i') : '' }}"
                                           data-location="{{ $agenda->location ?? '' }}"
                                           data-description="{{ $agenda->description ?? '' }}"
                                           data-status="{{ $agenda->status }}"
                                           data-notes="{{ $agenda->notes ?? '' }}"
                                           data-unit="{{ $agenda->unit->unit_name ?? ($agenda->units ?? '') }}"
                                           data-involved="{{ $agenda->involved_institution ?? '' }}"
                                           data-visibility="{{ (int) ($agenda->is_public ?? 0) }}">
                                            <i class="fas fa-eye"></i> Lihat Detail
                                        </a>
                                        @if($agenda->status === 'pending' || $agenda->status === 'rejected')
                                            <a href="{{ route('agenda.edit', $agenda->id_agenda) }}" class="btn-link">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <p>Anda belum memiliki agenda</p>
                            <a href="{{ route('agenda.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Buat Agenda Baru
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Account Actions Card -->
            <div class="profile-card">
                <div class="card-header">
                    <div class="card-header-content">
                        <div class="card-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <div>
                            <h2 class="card-title">Pengaturan Akun</h2>
                            <p class="card-subtitle">Kelola pengaturan akun Anda</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="account-actions">
                        <button type="button" class="btn btn-secondary" onclick="openPasswordModal()">
                            <i class="fas fa-key"></i>
                            Ubah Password
                        </button>
                        <button type="button" class="btn btn-danger" onclick="openDeleteModal()">
                            <i class="fas fa-trash-alt"></i>
                            Hapus Akun
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="showAgendaModal" tabindex="-1" aria-labelledby="showAgendaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" style="--bs-modal-width: 860px; max-width: calc(100vw - 200px);">
            <div class="modal-content border-0 shadow-sm rounded-4 overflow-hidden" style="width:100%; max-width:none;">
                <div class="modal-header" style="background-color: #F6F8F7; border: none;">
                    <h5 class="modal-title fw-semibold d-flex align-items-center gap-2" id="showAgendaLabel" style="color: #4A7C59;">
                        <i class="fas fa-calendar-alt" style="color: #4A7C59;"></i>
                        <span id="showAgendaName" style="font-weight:700; font-size:1.4rem;">-</span>
                    </h5>
                    <button type="button" class="close-modal" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body px-4 pt-3 pb-4">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="text-secondary small d-flex align-items-center gap-2 mb-0">
                                <i class="fas fa-align-left" style="color: #82A98D;"></i>
                                Deskripsi Agenda
                            </label>
                            <button type="button" class="btn btn-sm copy-agenda-btn" id="copyAgendaBtn" title="Salin Agenda" style="border: none; color: #82A98D; background: none; padding: 4px 8px; transition: all 0.3s ease; outline: none; box-shadow: none;" onmouseover="this.style.color='black'; this.style.backgroundColor='rgba(130, 169, 141, 0.1)';" onmouseout="this.style.color='#82A98D'; this.style.backgroundColor='';">
                                <i class="fas fa-copy"></i> Salin
                            </button>
                        </div>
                        <div class="border rounded-3 p-3 bg-light fw-semibold" id="showAgendaDesc" style="white-space: pre-line; overflow-wrap: anywhere; word-break: break-word; min-height: 60px;">-</div>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div>
                                <label class="text-secondary small d-flex align-items-center gap-2 mb-1">
                                    <i class="fas fa-calendar-day" style="color: #82A98D;"></i>
                                    Tanggal
                                </label>
                                <div class="fw-semibold" id="showAgendaDate">-</div>
                            </div>
                            <div class="mt-3">
                                <label class="text-secondary small d-flex align-items-center gap-2 mb-1">
                                    <i class="fas fa-clock" style="color: #82A98D;"></i>
                                    Waktu
                                </label>
                                <div class="fw-semibold" id="showAgendaTime">-</div>
                            </div>
                            <div class="mt-3">
                                <label class="text-secondary small d-flex align-items-center gap-2 mb-1">
                                    <i class="fas fa-map-marker-alt" style="color: #82A98D;"></i>
                                    Lokasi
                                </label>
                                <div class="fw-semibold" id="showAgendaLocation">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label class="text-secondary small d-flex align-items-center gap-2 mb-1">
                                    <i class="fas fa-building" style="color: #82A98D;"></i>
                                    Pelaksana
                                </label>
                                <div class="fw-semibold" id="showAgendaUnit">-</div>
                            </div>
                            <div class="mt-3">
                                <label class="text-secondary small d-flex align-items-center gap-2 mb-1">
                                    <i class="fas fa-users" style="color: #82A98D;"></i>
                                    Dihadiri
                                </label>
                                <div class="fw-semibold" id="showAgendaInvolved">-</div>
                            </div>
                            <div class="mt-3">
                                <label class="text-secondary small d-flex align-items-center gap-2 mb-1">
                                    <i class="fas fa-eye" style="color: #82A98D;"></i>
                                    Status
                                </label>
                                <span id="showAgendaAccess" class="badge-status badge-default">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="text-secondary small d-flex align-items-center gap-2 mb-1">
                            <i class="fa-solid fa-file-lines" style="color: #82A98D;"></i>
                            Catatan
                        </label>
                        <div class="fw-semibold border rounded-3 p-3 bg-light" id="showAgendaNotes" style="white-space: pre-wrap; min-height: 60px;">-</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div id="editProfileModal" class="modal">
        <div class="modal-content" style="width: 860px; max-width: 95vw; border-radius: 16px;">
            <div class="modal-header" style="background-color: #F6F8F7; border: none; padding: 16px 20px; align-items: center;">
                <h2 style="margin:0; font-size:1.25rem; color:#2F3E35;">Edit Profile</h2>
                <button type="button" class="modal-close" onclick="closeEditProfileModal()" style="background:transparent; border:none; color:#6b8f71; font-size:20px; cursor:pointer;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('profile.update') }}" class="profile-form" style="padding: 16px 20px;">
                @csrf
                @method('patch')

                <div class="form-group">
                    <label for="edit_name" class="form-label">
                        <i class="fas fa-user"></i>
                        Nama Lengkap
                    </label>
                    <input type="text"
                           id="edit_name"
                           name="name"
                           class="form-input @error('name') is-invalid @enderror"
                           value="{{ old('name', $user->name) }}"
                           required>
                    @error('name')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="edit_email" class="form-label">
                        <i class="fas fa-envelope"></i>
                        Email
                    </label>
                    <input type="email"
                           id="edit_email"
                           name="email"
                           class="form-input @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}"
                           required>
                    @error('email')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="edit_username" class="form-label">
                        <i class="fas fa-at"></i>
                        Username
                    </label>
                    <input type="text"
                           id="edit_username"
                           name="username"
                           class="form-input @error('username') is-invalid @enderror"
                           value="{{ old('username', $user->username) }}">
                    @error('username')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="edit_contact" class="form-label">
                        <i class="fas fa-phone"></i>
                        Kontak
                    </label>
                    <input type="text"
                           id="edit_contact"
                           name="contact"
                           class="form-input @error('contact') is-invalid @enderror"
                           value="{{ old('contact', $user->contact) }}"
                           placeholder="Nomor telepon atau kontak lainnya">
                    @error('contact')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Password Update Modal -->
    <div id="passwordModal" class="modal">
        <div class="modal-content" style="width: 860px; max-width: 95vw; border-radius: 16px;">
            <div class="modal-header" style="background-color: #F6F8F7; border: none; padding: 16px 20px; align-items: center;">
                <h2 style="margin:0; font-size:1.25rem; color:#2F3E35;">Ubah Password</h2>
                <button type="button" class="modal-close" onclick="closePasswordModal()" style="background:transparent; border:none; color:#6b8f71; font-size:20px; cursor:pointer;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('password.update') }}" class="password-form" style="padding: 16px 20px;">
                @csrf
                @method('put')

                <div class="form-group">
                    <label for="current_password" class="form-label">
                        <i class="fas fa-lock"></i>
                        Password Saat Ini
                    </label>
                    <input type="password"
                           id="current_password"
                           name="current_password"
                           class="form-input @error('current_password', 'updatePassword') is-invalid @enderror"
                           placeholder="Masukkan password saat ini"
                           required>
                    @error('current_password', 'updatePassword')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-key"></i>
                        Password Baru
                    </label>
                    <input type="password"
                           id="password"
                           name="password"
                           class="form-input @error('password', 'updatePassword') is-invalid @enderror"
                           placeholder="Masukkan password baru"
                           required>
                    @error('password', 'updatePassword')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">
                        <i class="fas fa-key"></i>
                        Konfirmasi Password Baru
                    </label>
                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           class="form-input"
                           placeholder="Konfirmasi password baru"
                           required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Simpan Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Hapus Akun</h2>
                <button type="button" class="modal-close" onclick="closeDeleteModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="danger-text">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>
                        Setelah akun Anda dihapus, semua sumber daya dan data Anda akan dihapus secara permanen.
                        Sebelum menghapus akun Anda, harap unduh data atau informasi yang ingin Anda simpan.
                    </p>
                </div>

                <form method="POST" action="{{ route('profile.destroy') }}" class="delete-form" onsubmit="return confirmDelete(event)">
                    @csrf
                    @method('delete')

                    <div class="form-group">
                        <label for="delete_password" class="form-label">
                            <i class="fas fa-lock"></i>
                            Konfirmasi Password
                        </label>
                        <input type="password"
                               id="delete_password"
                               name="password"
                               class="form-input @error('password', 'userDeletion') is-invalid @enderror"
                               placeholder="Masukkan password untuk konfirmasi"
                               required>
                        @error('password', 'userDeletion')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt"></i>
                            Hapus Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openEditProfileModal() {
            document.getElementById('editProfileModal').style.display = 'flex';
        }

        function closeEditProfileModal() {
            document.getElementById('editProfileModal').style.display = 'none';
        }

        function openPasswordModal() {
            document.getElementById('passwordModal').style.display = 'flex';
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').style.display = 'none';
            // Reset form
            document.querySelector('.password-form').reset();
        }

        function openDeleteModal() {
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            // Reset form
            document.querySelector('.delete-form').reset();
        }

        function openAgendaDetailFromLink(el) {
            const name = el.getAttribute('data-name') || '';
            const unit = el.getAttribute('data-unit') || '';
            const date = el.getAttribute('data-date') || '';
            const start = el.getAttribute('data-start') || '';
            const end = el.getAttribute('data-end') || '';
            const location = el.getAttribute('data-location') || '';
            const description = el.getAttribute('data-description') || '';
            const notes = el.getAttribute('data-notes') || '';
            const involved = el.getAttribute('data-involved') || '';
            const visibility = parseInt(el.getAttribute('data-visibility') || '0', 10);

            document.getElementById('showAgendaName').textContent = name || '-';
            document.getElementById('showAgendaDesc').textContent = description || '-';
            document.getElementById('showAgendaDate').textContent = date || '-';
            document.getElementById('showAgendaTime').textContent = start && end ? (start + ' - ' + end) : (start || end || '-');
            document.getElementById('showAgendaLocation').textContent = location || '-';
            document.getElementById('showAgendaUnit').textContent = unit || '-';
            document.getElementById('showAgendaInvolved').textContent = involved || '-';
            const accessEl = document.getElementById('showAgendaAccess');
            const isPublic = visibility === 1;
            accessEl.textContent = isPublic ? 'PUBLIK' : 'PRIVASI';
            accessEl.className = 'badge-status ' + (isPublic ? 'badge-publik' : 'badge-privasi');
            document.getElementById('showAgendaNotes').textContent = notes || '-';

            const copyBtn = document.getElementById('copyAgendaBtn');
            if (copyBtn) {
                const text = [
                    'AGENDA: ' + (name || '-'),
                    '',
                    'Deskripsi: ' + (description || '-'),
                    '',
                    'Tanggal: ' + (date || '-'),
                    'Waktu: ' + (start && end ? (start + ' - ' + end) : (start || end || '-')),
                    'Lokasi: ' + (location || '-'),
                    '',
                    'Pelaksana: ' + (unit || '-'),
                    'Dihadiri: ' + (involved || '-'),
                    'Status: ' + (visibility === 1 ? 'Publik' : 'Privasi'),
                    '',
                    'Catatan: ' + (notes || '-')
                ].join('\n');
                copyBtn.onclick = function() {
                    navigator.clipboard.writeText(text).then(function(){
                        const original = copyBtn.innerHTML;
                        copyBtn.innerHTML = '<i class="fas fa-check"></i> Tersalin!';
                        copyBtn.style.color = 'white';
                        copyBtn.style.backgroundColor = '#82A98D';
                        setTimeout(function(){
                            copyBtn.innerHTML = original;
                            copyBtn.style.color = '#82A98D';
                            copyBtn.style.backgroundColor = '';
                        }, 2000);
                    });
                };
            }

            const modalEl = document.getElementById('showAgendaModal');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }

        function closeAgendaDetailModal() {
            const modalEl = document.getElementById('showAgendaModal');
            const instance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            instance.hide();
        }

        function confirmDelete(event) {
            event.preventDefault();
            if (confirm('Apakah Anda yakin ingin menghapus akun? Tindakan ini tidak dapat dibatalkan!')) {
                event.target.submit();
            }
            return false;
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const editProfileModal = document.getElementById('editProfileModal');
            const passwordModal = document.getElementById('passwordModal');
            const deleteModal = document.getElementById('deleteModal');
            const agendaDetailModal = document.getElementById('showAgendaModal');

            if (event.target === editProfileModal) {
                closeEditProfileModal();
            }
            if (event.target === passwordModal) {
                closePasswordModal();
            }
            if (event.target === deleteModal) {
                closeDeleteModal();
            }
            if (event.target === agendaDetailModal) {
                closeAgendaDetailModal();
            }
        }
        document.addEventListener('keydown', function(e){
            if (e.key === 'Escape') {
                closeAgendaDetailModal();
            }
        });
    </script>
@endsection
