<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin - SiKota</title>
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/super-admin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="app-container">
        @include('layouts.header')

        <main class="main-content">
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
                        <input type="text" id="globalSearch" class="search-input"
                            placeholder="Cari User ataupun OPD...">
                        <button class="search-btn"><i class="fas fa-search"></i></button>
                    </div>
                </div>

                <!-- Tabel User -->
                <div class="admin-card">
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
                                        <td>
                                            <!-- Tombol Edit -->
                                            <button type="button" class="btn-icon-edit" data-id="{{ $user->id_user }}"
                                                data-name="{{ $user->name }}" data-username="{{ $user->username }}"
                                                data-email="{{ $user->email }}" data-role="{{ $user->role }}"
                                                onclick="openEditModal(this)">
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
        </main>
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
            <script>
                // === Modal Edit User ===
                function openEditModal(button) {
                    const modal = document.getElementById('editModal');
                    const form = document.getElementById('editForm');
                    const userId = button.getAttribute('data-id');

                    form.action = /superadmin/users/${userId};
                    document.getElementById('editName').value = button.getAttribute('data-name');
                    document.getElementById('editUsername').value = button.getAttribute('data-username');
                    document.getElementById('editEmail').value = button.getAttribute('data-email');
                    document.getElementById('editRole').value = button.getAttribute('data-role');

                    modal.classList.add('show');
                }

                function closeEditModal() {
                    document.getElementById('editModal').classList.remove('show');
                }

                // === Modal Tambah/Edit OPD ===
                function openAddUnitModal() {
                    document.getElementById('addUnitModal').classList.add('show');
                }

                function closeAddUnitModal() {
                    document.getElementById('addUnitModal').classList.remove('show');
                }

                function openEditUnitModal(button) {
                    const modal = document.getElementById('editUnitModal');
                    const form = document.getElementById('editUnitForm');
                    const unitId = button.getAttribute('data-id');
                    const name = button.getAttribute('data-name');
                    const address = button.getAttribute('data-address');

                    form.action = /superadmin/units/${unitId};
                    document.getElementById('editUnitName').value = name;
                    document.getElementById('editUnitAddress').value = address ?? '';
                    modal.classList.add('show');
                }

                function closeEditUnitModal() {
                    document.getElementById('editUnitModal').classList.remove('show');
                }

                // Tutup modal jika klik area luar
                window.addEventListener('click', function(e) {
                    const modals = document.querySelectorAll('.user-form-modal');
                    modals.forEach(modal => {
                        if (e.target === modal) modal.classList.remove('show');
                    });
                });

                    // === FUNGSI SEARCH BAR UNTUK USER & OPD ===
                    document.addEventListener('DOMContentLoaded', function() {
                        const searchInput = document.getElementById('searchInput');
                        const userRows = document.querySelectorAll('#userTable tbody tr');
                        const unitRows = document.querySelectorAll('#unitTable tbody tr');

                        if (searchInput) {
                            // Jalankan hanya saat tekan ENTER
                            searchInput.addEventListener('keydown', function(e) {
                                if (e.key === 'Enter') {
                                    e.preventDefault(); // biar gak reload
                                    const keyword = searchInput.value.toLowerCase().trim();

                                    // Filter tabel USER
                                    userRows.forEach(row => {
                                        const cells = row.querySelectorAll('td');
                                        const match = Array.from(cells).some(td =>
                                            td.textContent.toLowerCase().includes(keyword)
                                        );
                                        row.style.display = match ? '' : 'none';
                                    });

                                    // Filter tabel OPD
                                    unitRows.forEach(row => {
                                        const cells = row.querySelectorAll('td');
                                        const match = Array.from(cells).some(td =>
                                            td.textContent.toLowerCase().includes(keyword)
                                        );
                                        row.style.display = match ? '' : 'none';
                                    });
                                }
                            });

                            // Kalau input dikosongkan → tampilkan semua data lagi
                            searchInput.addEventListener('input', function() {
                                if (searchInput.value.trim() === '') {
                                    userRows.forEach(row => row.style.display = '');
                                    unitRows.forEach(row => row.style.display = '');
                                }
                            });
                        }
                    });
            </script>


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

    <script>
        // === Modal Edit User ===
        function openEditModal(button) {
            const modal = document.getElementById('editModal');
            const form = document.getElementById('editForm');
            const userId = button.getAttribute('data-id');

            form.action = /superadmin/users/${userId};
            document.getElementById('editName').value = button.getAttribute('data-name');
            document.getElementById('editUsername').value = button.getAttribute('data-username');
            document.getElementById('editEmail').value = button.getAttribute('data-email');
            document.getElementById('editRole').value = button.getAttribute('data-role');

            modal.classList.add('show');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('show');
        }

        // === Modal Tambah/Edit OPD ===
        function openAddUnitModal() {
            document.getElementById('addUnitModal').classList.add('show');
        }

        function closeAddUnitModal() {
            document.getElementById('addUnitModal').classList.remove('show');
        }

        function openEditUnitModal(button) {
            const modal = document.getElementById('editUnitModal');
            const form = document.getElementById('editUnitForm');
            const unitId = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const address = button.getAttribute('data-address');

            form.action = /superadmin/units/${unitId};
            document.getElementById('editUnitName').value = name;
            document.getElementById('editUnitAddress').value = address ?? '';
            modal.classList.add('show');
        }

        function closeEditUnitModal() {
            document.getElementById('editUnitModal').classList.remove('show');
        }

        // Tutup modal jika klik area luar
        window.addEventListener('click', function(e) {
            const modals = document.querySelectorAll('.user-form-modal');
            modals.forEach(modal => {
                if (e.target === modal) modal.classList.remove('show');
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
                        text: 'Yakin ingin menyimpan perubahan data user?',
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
                popup.innerHTML = <p class="toastify-title">${message}</p>;
                document.body.appendChild(popup);

                popup.classList.add('toastify-popup-show');
                setTimeout(() => {
                    popup.classList.remove('toastify-popup-show');
                    popup.classList.add('toastify-popup-hide');
                    setTimeout(() => popup.remove(), 300);
                }, 2000);
            }
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
            background: #E6F9EE;
            border: 1px solid #C4E7D0;
            color: #256D43;
            border-radius: 12px;
            padding: 0 20px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            min-height: 40px;
            line-height: 40px;
            max-width: 90%;
            white-space: nowrap;
            top: 90px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            font-size: 15px;
            font-weight: 500;
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
                transform: translate(-50%, -15px) scale(0.95);
            }

            70% {
                opacity: 1;
                transform: translate(-50%, 3px) scale(1.03);
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

</body>

</html>