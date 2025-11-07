@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/super-admin.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')
    <div class="approval-page">
        <div class="superadmin-header">
            <a href="{{ url('/dashboard/bulan') }}" class="back-btn" title="Kembali ke Dashboard">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="title-wrap">
                <h1 class="page-title">Daftar User dan OPD</h1>
                <p class="page-subtitle">Kelola akun pengguna dan organisasi perangkat daerah</p>
            </div>
        </div>

        <div class="search-section">
            <div class="search-box">
                <input type="text" id="globalSearch" class="search-input" placeholder="Cari User ataupun OPD...">
                <button class="search-btn"><i class="fas fa-search"></i></button>
            </div>
        </div>

        <!-- Tabel User -->
        <div class="admin-card">
            <!-- 🆕 Tambah User -->
            <div class="admin-card-header">
                <button class="btn-chip" type="button" onclick="openAddUserModal()">
                    <i class="fas fa-plus"></i>
                    Tambah User
                </button>
            </div>
            <div class="section-title">
                <i class="fas fa-users"></i>
                <span>Daftar User</span>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table" id="userTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Role</th>
                            <th>OPD</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->username ?? '-' }}</td>
                                <td>{{ $user->email }}</td>
                                <td>••••••••</td>
                                <td>{{ ucfirst($user->role) }}</td>
                                <td>{{ optional($user->unit)->unit_name ?? '-' }}</td>
                                <td>
                                    <div class="table-action-stack">
                                        <!-- Tombol Edit -->
                                        <button type="button" class="btn-icon-edit" data-id="{{ $user->id_user }}"
                                            data-name="{{ $user->name }}" data-username="{{ $user->username }}"
                                            data-email="{{ $user->email }}" data-role="{{ $user->role }}"
                                            data-unit="{{ $user->id_unit ?? '' }}" onclick="openEditModal(this)">
                                            <i class="fas fa-pen"></i>
                                        </button>

                                        <!-- Tombol Hapus -->
                                        <form action="{{ route('users.destroy', $user->id_user) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon-delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="section-space"></div>

        <!-- Tabel OPD -->
        <div class="admin-card">
            <div class="admin-card-header">
                <button class="btn-chip" type="button" onclick="openAddUnitModal()">
                    <i class="fas fa-plus"></i>
                    Tambah OPD
                </button>
            </div>
            <div class="section-title" style="margin-bottom: 8px;">
                <i class="fas fa-sitemap"></i>
                <span>Daftar OPD</span>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table" id="unitTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Instansi</th>
                            <th>Alamat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($units as $index => $unit)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $unit->unit_name }}</td>
                                <td>{{ $unit->address ?? '-' }}</td>
                                <td>
                                    <div class="table-action-stack">
                                        <button type="button" class="btn-icon-edit" data-id="{{ $unit->id_unit }}"
                                            data-name="{{ $unit->unit_name }}" data-address="{{ $unit->address }}"
                                            onclick="openEditUnitModal(this)">
                                            <i class="fas fa-pen"></i>
                                        </button>

                                        <form action="{{ route('units.destroy', $unit->id_unit) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon-delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center;">Belum ada data OPD.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Edit User -->
    <div id="editModal" class="user-form-modal">
        <div class="user-form-content">
            <div class="user-form-header">
                <h2 class="user-form-title">
                    <i class="fas fa-pen" style="margin-right: 8px; color:#82A98D;"></i>
                    Edit User
                </h2>
                <button class="close-modal" onclick="closeEditModal()">&times;</button>
            </div>

            <form id="editForm" method="POST">
                @csrf
                @method('PUT')

                <div class="user-form-group">
                    <label>Nama</label>
                    <input type="text" name="name" id="editName" required>
                </div>

                <div class="user-form-group">
                    <label>Username</label>
                    <input type="text" name="username" id="editUsername">
                </div>

                <div class="user-form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="editEmail" required>
                </div>

                <div class="user-form-group">
                    <label>Password (kosongkan jika tidak ingin ubah)</label>
                    <input type="password" name="password" id="editPassword">
                </div>

                <div class="user-form-group">
                    <label>OPD</label>
                    <select name="id_unit" id="editOpd" required>
                        <option value="">-- Pilih OPD --</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id_unit }}">{{ $unit->unit_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="user-form-group">
                    <label>Role</label>
                    <select name="role" id="editRole" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="user-form-actions">
                    <button type="submit" class="btn-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 🆕 Modal Tambah User -->
    <div id="addUserModal" class="user-form-modal">
        <div class="user-form-content">
            <div class="user-form-header">
                <h2 class="user-form-title">
                    <i class="fas fa-plus" style="margin-right: 8px; color:#82A98D;"></i>
                    Tambah User
                </h2>
                <button class="close-modal" onclick="closeAddUserModal()">&times;</button>
            </div>

            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="user-form-group">
                    <label>Nama</label>
                    <input type="text" name="name" required>
                </div>

                <div class="user-form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>

                <div class="user-form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="user-form-group">
                    <label>Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="addPassword" required>
                        <i class="bi bi-eye-slash password-toggle" id="togglePassword"></i>
                    </div>
                </div>

                <div class="user-form-group">
                    <label>OPD</label>
                    <select name="id_unit" required>
                        <option value="">-- Pilih OPD --</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id_unit }}">{{ $unit->unit_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="user-form-group">
                    <label>Role</label>
                    <select name="role" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="user-form-actions">
                    <button type="submit" class="btn-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // === FUNGSI KONFIRMASI ===
            function showConfirmation({
                title,
                text,
                confirmText,
                form
            }) {
                const popup = document.createElement('div');
                popup.className = 'toastify-popup';
                popup.innerHTML = `
                    <p class="toastify-title">${text}</p>
                    <div class="toastify-btn-group">
                        <button class="btn-confirm">${confirmText}</button>
                        <button class="btn-cancel">Batal</button>
                    </div>
                `;
                document.body.appendChild(popup);
                popup.classList.add('toastify-popup-show');

                popup.querySelector('.btn-cancel').addEventListener('click', () => {
                    popup.classList.remove('toastify-popup-show');
                    popup.classList.add('toastify-popup-hide');
                    setTimeout(() => popup.remove(), 250);
                });

                popup.querySelector('.btn-confirm').addEventListener('click', () => {
                    popup.classList.remove('toastify-popup-show');
                    popup.classList.add('toastify-popup-hide');
                    setTimeout(() => {
                        popup.remove();
                        form.submit();
                    }, 200);
                });
            }

            // === MODAL TAMBAH USER ===
            window.openAddUserModal = function() {
                document.getElementById('addUserModal').classList.add('show');
            }

            window.closeAddUserModal = function() {
                document.getElementById('addUserModal').classList.remove('show');
            }

            // === EVENT SUBMIT TAMBAH USER ===
            const addUserForm = document.querySelector('#addUserModal form');
            if (addUserForm) {
                addUserForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    showConfirmation({
                        title: 'Konfirmasi Tambah User',
                        text: 'Yakin ingin menambahkan data baru?',
                        confirmText: 'Ya, simpan',
                        form: addUserForm
                    });
                });
            }

            // === 🆕 FUNGSI AUTO-SELECT OPD SAAT ROLE = ADMIN ===
            const roleSelect = document.querySelector('#addUserModal select[name="role"]');
            const opdSelect = document.querySelector('#addUserModal select[name="id_unit"]');

            if (roleSelect && opdSelect) {
                roleSelect.addEventListener('change', function() {
                    const selectedRole = this.value.toLowerCase();

                    if (selectedRole === 'admin') {
                        const protokolOption = Array.from(opdSelect.options).find(
                            opt => opt.text.trim().toLowerCase() === 'protokol'
                        );

                        if (protokolOption) {
                            opdSelect.value = protokolOption.value;
                            opdSelect.setAttribute('readonly', true);
                            opdSelect.classList.add('readonly');
                        }
                    } else if (selectedRole === 'user') {
                        opdSelect.value = '';
                        opdSelect.disabled = true;
                    } else {
                        opdSelect.disabled = false;
                        opdSelect.removeAttribute('readonly');
                        opdSelect.classList.remove('readonly');
                        opdSelect.value = '';
                    }
                });
            }

        }); // ✅ penutup DOMContentLoaded
    </script>

    <!-- Modal Tambah OPD -->
    <div id="addUnitModal" class="user-form-modal">
        <div class="user-form-content">
            <div class="user-form-header">
                <h2 class="user-form-title">
                    <i class="fas fa-plus" style="margin-right: 8px; color:#82A98D;"></i>
                    Tambah OPD
                </h2>
                <button class="close-modal" onclick="closeAddUnitModal()">&times;</button>
            </div>
            <form action="{{ route('units.store') }}" method="POST">
                @csrf
                <div class="user-form-group">
                    <label>Nama Instansi</label>
                    <input type="text" name="unit_name" required>
                </div>

                <div class="user-form-group">
                    <label>Alamat</label>
                    <input type="text" name="address">
                </div>

                <div class="user-form-actions">
                    <button type="submit" class="btn-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit OPD -->
    <div id="editUnitModal" class="user-form-modal">
        <div class="user-form-content">
            <div class="user-form-header">
                <h2 class="user-form-title">
                    <i class="fas fa-pen" style="margin-right: 8px; color:#82A98D;"></i>
                    Edit OPD
                </h2>
                <button class="close-modal" onclick="closeEditUnitModal()">&times;</button>
            </div>

            <form id="editUnitForm" method="POST">
                @csrf
                @method('PUT')

                <div class="user-form-group">
                    <label>Nama Instansi</label>
                    <input type="text" name="unit_name" id="editUnitName" required>
                </div>

                <div class="user-form-group">
                    <label>Alamat</label>
                    <input type="text" name="address" id="editUnitAddress">
                </div>

                <div class="user-form-actions">
                    <button type="submit" class="btn-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const globalSearch = document.getElementById('globalSearch');

            globalSearch.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') { // cuma jalan saat tekan Enter
                    e.preventDefault(); // biar gak submit form
                    const query = globalSearch.value.toLowerCase();

                    // Semua tabel yang ingin difilter
                    const allTables = document.querySelectorAll('table');

                    allTables.forEach(table => {
                        const rows = table.querySelectorAll('tbody tr');
                        rows.forEach(row => {
                            const text = row.textContent.toLowerCase();
                            row.style.display = text.includes(query) ? '' : 'none';
                        });
                    });
                }
            });
        });
    </script>


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ==== FUNGSI KONFIRMASI ====
            function showConfirmation({
                title,
                text,
                confirmText,
                form
            }) {
                const popup = document.createElement('div');
                popup.className = 'toastify-popup';
                popup.innerHTML = `
                    <p class="toastify-title">${text}</p>
                    <div class="toastify-btn-group">
                        <button class="btn-confirm">${confirmText}</button>
                        <button class="btn-cancel">Batal</button>
                    </div>
                `;
                document.body.appendChild(popup);
                popup.classList.add('toastify-popup-show');

                popup.querySelector('.btn-cancel').addEventListener('click', () => {
                    popup.classList.remove('toastify-popup-show');
                    popup.classList.add('toastify-popup-hide');
                    setTimeout(() => popup.remove(), 250);
                });

                popup.querySelector('.btn-confirm').addEventListener('click', () => {
                    popup.classList.remove('toastify-popup-show');
                    popup.classList.add('toastify-popup-hide');
                    setTimeout(() => {
                        popup.remove();
                        form.submit();
                    }, 200);
                });
            }

            // ==== KONFIRMASI TAMBAH/EDIT ====
            const addUnitForm = document.querySelector('#addUnitModal form');
            if (addUnitForm) {
                addUnitForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    showConfirmation({
                        title: 'Konfirmasi Tambah OPD',
                        text: 'Yakin ingin menambahkan data OPD?',
                        confirmText: 'Ya, simpan',
                        form: addUnitForm
                    });
                });
            }

            const editUserForm = document.getElementById('editForm');
            if (editUserForm) {
                editUserForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    showConfirmation({
                        title: 'Konfirmasi Edit User',
                        text: 'Yakin ingin menyimpan perubahan data?',
                        confirmText: 'Ya, simpan',
                        form: editUserForm
                    });
                });
            }

            const editUnitForm = document.getElementById('editUnitForm');
            if (editUnitForm) {
                editUnitForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    showConfirmation({
                        title: 'Konfirmasi Edit OPD',
                        text: 'Yakin ingin menyimpan perubahan data OPD?',
                        confirmText: 'Ya, simpan',
                        form: editUnitForm
                    });
                });
            }

            // ==== KONFIRMASI HAPUS ====
            document.querySelectorAll('form').forEach(form => {
                const action = form.getAttribute('action') || '';
                const isDelete = form.querySelector('input[name="_method"][value="DELETE"]');

                if (isDelete && action.includes('/superadmin/users/')) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        showConfirmation({
                            title: 'Konfirmasi Hapus User',
                            text: 'Yakin ingin menghapus user ini?',
                            confirmText: 'Ya, hapus',
                            form: form
                        });
                    });
                }

                if (isDelete && action.includes('/superadmin/units/')) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        showConfirmation({
                            title: 'Konfirmasi Hapus OPD',
                            text: 'Yakin ingin menghapus data OPD ini?',
                            confirmText: 'Ya, hapus',
                            form: form
                        });
                    });
                }
            });

            // ==== POPUP SUKSES ====
            @if (session('success'))
                showSuccessToast("{{ session('success') }}");
            @endif

            function showSuccessToast(message) {
                const popup = document.createElement('div');
                popup.className = 'toastify-popup toastify-success';
                popup.innerHTML = `
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
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // === Elemen modal Tambah ===
            const addRoleSelect = document.querySelector('#addUserModal select[name="role"]');
            const addOpdSelect = document.querySelector('#addUserModal select[name="id_unit"]');

            // === Elemen modal Edit ===
            const editRoleSelect = document.querySelector('#editRole');
            const editOpdSelect = document.querySelector('#editOpd');

            const adminOpdName = "Protokol";

            // 🔸 Fungsi: pilih dan kunci OPD = Protokol
            function setOpdToProtokol(selectOpd) {
                for (let option of selectOpd.options) {
                    if (option.text.trim().toLowerCase() === adminOpdName.toLowerCase()) {
                        selectOpd.value = option.value;
                        break;
                    }
                }

                // jangan pakai disabled
                selectOpd.setAttribute('readonly', true);
                selectOpd.classList.add('readonly');
                toggleProtokolOption(selectOpd, true);
            }


            // 🔸 Fungsi: aktifkan kembali dropdown
            function enableOpd(selectOpd) {
                selectOpd.removeAttribute('readonly');
                selectOpd.classList.remove('readonly');
            }

            // 🔸 Fungsi: sembunyikan/tampilkan opsi Protokol
            function toggleProtokolOption(selectOpd, show) {
                for (let option of selectOpd.options) {
                    if (option.text.trim().toLowerCase() === adminOpdName.toLowerCase()) {
                        option.hidden = !show;
                    }
                }
            }

            // === Edit user ===
            if (editRoleSelect && editOpdSelect) {
                editRoleSelect.addEventListener('change', function() {
                    if (this.value === 'admin') {
                        toggleProtokolOption(editOpdSelect, true);
                        setOpdToProtokol(editOpdSelect);
                    } else {
                        enableOpd(editOpdSelect);
                        toggleProtokolOption(editOpdSelect, false);
                    }
                });
            }

            // === Saat modal Edit dibuka ===
            window.openEditModal = function(button) {
                const modal = document.getElementById('editModal');
                const form = document.getElementById('editForm');
                const userId = button.getAttribute('data-id');

                form.action = `/superadmin/users/${userId}`;
                document.getElementById('editName').value = button.getAttribute('data-name');
                document.getElementById('editUsername').value = button.getAttribute('data-username');
                document.getElementById('editEmail').value = button.getAttribute('data-email');
                document.getElementById('editPassword').value = ''; // kosongin password edit
                document.getElementById('editRole').value = button.getAttribute('data-role');
                document.getElementById('editOpd').value = button.getAttribute('data-unit');

                // ✅ Jalankan logika role langsung
                const currentRole = button.getAttribute('data-role');
                if (currentRole === 'admin') {
                    toggleProtokolOption(editOpdSelect, true);
                    setOpdToProtokol(editOpdSelect);
                } else {
                    enableOpd(editOpdSelect);
                    toggleProtokolOption(editOpdSelect, false);
                }

                modal.classList.add('show');
            };
        });
    </script>

    <style>
        .toastify-popup {
            position: fixed;
            top: 70px;
            left: 50%;
            transform: translateX(-50%);
            background: #FFF1E6;
            border: 1px solid #F7B7B7;
            border-radius: 12px;
            padding: 14px 18px;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
            font-family: 'Poppins', sans-serif;
            color: #333;
            text-align: center;
            width: 260px;
            opacity: 0;
            z-index: 9999;
        }

        .toastify-title {
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 12px;
        }

        .toastify-btn-group {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .toastify-btn-group button {
            border: none;
            border-radius: 8px;
            padding: 6px 14px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .toastify-btn-group .btn-confirm {
            background: #F47C7C;
            color: white;
        }

        .toastify-btn-group .btn-confirm:hover {
            background: #ff6b6b;
            transform: scale(1.05);
        }

        .toastify-btn-group .btn-cancel {
            background: #f8f8f8;
            color: #444;
        }

        .toastify-btn-group .btn-cancel:hover {
            background: #ededed;
            transform: scale(1.03);
        }

        .toastify-popup-show {
            animation: toastIn 0.35s ease forwards;
        }

        .toastify-popup-hide {
            animation: toastOut 0.25s ease forwards;
        }

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
            animation: toastIn 0.35s ease forwards;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: auto;
            max-width: 300px;
            min-height: unset;
        }

        select.readonly {
            background-color: #f8f8f8;
            pointer-events: none;
            opacity: 0.7;
        }

        body,
        .toastify-popup,
        .toastify-title,
        .toastify-btn-group button {
            font-family: 'Poppins', sans-serif !important;
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordField = document.getElementById('addPassword');

            if (togglePassword && passwordField) {
                togglePassword.addEventListener('click', function() {
                    const isPassword = passwordField.getAttribute('type') === 'password';
                    passwordField.setAttribute('type', isPassword ? 'text' : 'password');

                    // Ganti ikon Bootstrap
                    this.classList.toggle('bi-eye');
                    this.classList.toggle('bi-eye-slash');
                });
            }
        });
    </script>

    <script>
        // Fungsi untuk membuka modal Edit OPD dan mengisi data
        window.openEditUnitModal = function(button) {
            const modal = document.getElementById('editUnitModal');
            const form = document.getElementById('editUnitForm');

            if (!modal || !form) return;

            const unitId = button.getAttribute('data-id');
            const unitName = button.getAttribute('data-name') || '';
            const unitAddress = button.getAttribute('data-address') || '';

            // Set action form ke endpoint update OPD
            form.action = `/superadmin/units/${unitId}`;

            // Isi field
            const nameInput = document.getElementById('editUnitName');
            const addrInput = document.getElementById('editUnitAddress');
            if (nameInput) nameInput.value = unitName;
            if (addrInput) addrInput.value = unitAddress;

            // Tampilkan modal
            modal.classList.add('show');
        }

        // Fungsi untuk menutup modal Edit OPD
        window.closeEditUnitModal = function() {
            const modal = document.getElementById('editUnitModal');
            if (modal) modal.classList.remove('show');
        }

        // Buka modal Tambah OPD
        window.openAddUnitModal = function() {
            const modal = document.getElementById('addUnitModal');
            if (!modal) return;

            // Reset field input
            const nameInput = modal.querySelector('input[name="unit_name"]');
            const addrInput = modal.querySelector('input[name="address"]');
            if (nameInput) nameInput.value = '';
            if (addrInput) addrInput.value = '';

            modal.classList.add('show');
        }

        // Tutup modal Tambah OPD
        window.closeAddUnitModal = function() {
            const modal = document.getElementById('addUnitModal');
            if (modal) modal.classList.remove('show');
        }
    </script>
@endsection