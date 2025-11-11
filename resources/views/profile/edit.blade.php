@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@section('content')
    <div class="profile-page">
        <div class="profile-header">
            <div class="header-left">
                <a href="{{ route('dashboard.bulan') }}" class="back-btn" title="Kembali ke Dashboard">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="header-title">
                    <h1 class="page-title">Profil Saya</h1>
                    <p class="page-subtitle">Kelola informasi profil dan pengaturan akun Anda</p>
                </div>
            </div>
        </div>

        <div class="profile-content">
            <!-- Profile Information Card -->
            <div class="profile-card">
                <div class="card-header">
                    <div class="card-header-content">
                        <div class="card-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h2 class="card-title">Informasi Profil</h2>
                            <p class="card-subtitle">Perbarui informasi profil dan alamat email Anda</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status') === 'profile-updated')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <span>Profil berhasil diperbarui</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}" class="profile-form">
                        @csrf
                        @method('patch')

                        <div class="form-group">
                            <label for="name" class="form-label">
                                <i class="fas fa-user"></i>
                                Nama Lengkap
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   class="form-input @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $user->name) }}" 
                                   required 
                                   autofocus>
                            @error('name')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i>
                                Email
                            </label>
                            <input type="email" 
                                   id="email" 
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
                            <label for="username" class="form-label">
                                <i class="fas fa-at"></i>
                                Username
                            </label>
                            <input type="text" 
                                   id="username" 
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
                            <label for="contact" class="form-label">
                                <i class="fas fa-phone"></i>
                                Kontak
                            </label>
                            <input type="text" 
                                   id="contact" 
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

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-building"></i>
                                Instansi/Unit
                            </label>
                            <input type="text" 
                                   class="form-input" 
                                   value="{{ $user->unit->unit_name ?? 'Belum ada instansi' }}" 
                                   disabled>
                            <small class="form-help">Instansi tidak dapat diubah dari halaman profil</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-user-shield"></i>
                                Role
                            </label>
                            <input type="text" 
                                   class="form-input" 
                                   value="{{ ucfirst($user->role === 'superadmin' ? 'Super Admin' : ($user->role === 'admin' ? 'Protokol' : 'User')) }}" 
                                   disabled>
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

            <!-- Account Information Card -->
            <div class="profile-card">
                <div class="card-header">
                    <div class="card-header-content">
                        <div class="card-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div>
                            <h2 class="card-title">Informasi Akun</h2>
                            <p class="card-subtitle">Detail informasi akun Anda</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-calendar-alt"></i>
                                Tanggal Bergabung
                            </div>
                            <div class="info-value">
                                {{ \Carbon\Carbon::parse($user->created_at)->locale('id')->translatedFormat('d F Y') }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-clock"></i>
                                Terakhir Diperbarui
                            </div>
                            <div class="info-value">
                                {{ \Carbon\Carbon::parse($user->updated_at)->locale('id')->diffForHumans() }}
                            </div>
                        </div>

                        @if($user->email_verified_at)
                            <div class="info-item">
                                <div class="info-label">
                                    <i class="fas fa-check-circle"></i>
                                    Status Email
                                </div>
                                <div class="info-value verified">
                                    <i class="fas fa-check"></i>
                                    Terverifikasi
                                </div>
                            </div>
                        @else
                            <div class="info-item">
                                <div class="info-label">
                                    <i class="fas fa-exclamation-circle"></i>
                                    Status Email
                                </div>
                                <div class="info-value unverified">
                                    <i class="fas fa-times"></i>
                                    Belum Terverifikasi
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Delete Account Card -->
            <div class="profile-card danger-card">
                <div class="card-header">
                    <div class="card-header-content">
                        <div class="card-icon danger-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <h2 class="card-title">Hapus Akun</h2>
                            <p class="card-subtitle">Hapus akun Anda secara permanen</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <p class="danger-text">
                        Setelah akun Anda dihapus, semua sumber daya dan data Anda akan dihapus secara permanen. 
                        Sebelum menghapus akun Anda, harap unduh data atau informasi yang ingin Anda simpan.
                    </p>

                    <form method="POST" action="{{ route('profile.destroy') }}" class="delete-form" onsubmit="return confirmDelete(event)">
                        @csrf
                        @method('delete')

                        <div class="form-group">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i>
                                Konfirmasi Password
                            </label>
                            <input type="password" 
                                   id="password" 
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
    </div>

    <script>
        function confirmDelete(event) {
            event.preventDefault();
            if (confirm('Apakah Anda yakin ingin menghapus akun? Tindakan ini tidak dapat dibatalkan!')) {
                event.target.submit();
            }
            return false;
        }
    </script>
@endsection
