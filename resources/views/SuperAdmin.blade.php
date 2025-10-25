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
                <div class="approval-header">
                    <div class="title-wrap">
                        <h1 class="page-title">Daftar User</h1>
                        <p class="page-subtitle">Kelola akun pengguna dan organisasi perangkat daerah</p>
                    </div>
                </div>

                <!-- Tabel User -->
                <div class="admin-card">
                    <div class="admin-table-wrap">
                        <table class="admin-table">
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
                                                <button type="submit" class="btn-icon-delete"
                                                    onclick="return confirm('Yakin ingin menghapus user ini?')">
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
                        <table class="admin-table">
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
                                                <button type="submit" class="btn-icon-delete"
                                                    onclick="return confirm('Yakin ingin menghapus OPD ini?')">
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
        // === Modal Edit User ===
        function openEditModal(button) {
            const modal = document.getElementById('editModal');
            const form = document.getElementById('editForm');
            const userId = button.getAttribute('data-id');

            form.action = `/superadmin/users/${userId}`;
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

            form.action = `/superadmin/units/${unitId}`;
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

</body>

</html>
