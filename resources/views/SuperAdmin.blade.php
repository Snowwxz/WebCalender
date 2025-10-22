<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin - SiKota</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
     <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <!-- Component CSS -->
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
                                            <button type="button" class="btn-edit" data-id="{{ $user->id_user }}"
                                                data-name="{{ $user->name }}" data-username="{{ $user->username }}"
                                                data-email="{{ $user->email }}" data-role="{{ $user->role }}"
                                                onclick="openEditModal(this)">
                                                Edit
                                            </button>

                                            <!-- Tombol Hapus -->
                                            <form action="{{ route('users.destroy', $user->id_user) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-delete"
                                                    onclick="return confirm('Yakin ingin menghapus user ini?')">
                                                    Hapus
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
                                            <!-- Tombol Edit -->
                                            <button type="button" class="btn-edit" data-id="{{ $unit->id_unit }}"
                                                data-name="{{ $unit->unit_name }}" data-address="{{ $unit->address }}"
                                                onclick="openEditUnitModal(this)">
                                                Edit
                                            </button>

                                            <!-- Tombol Hapus -->
                                            <form action="{{ route('units.destroy', $unit->id_unit) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-delete"
                                                    onclick="return confirm('Yakin ingin menghapus OPD ini?')">
                                                    Hapus
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

    <!-- Modal Edit -->
    <div id="editModal" class="modal" style="display:none;">
        <div class="modal-content">
            <h2>Edit User</h2>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')

                <label>Nama</label>
                <input type="text" name="name" id="editName" required>

                <label>Username</label>
                <input type="text" name="username" id="editUsername">

                <label>Email</label>
                <input type="email" name="email" id="editEmail" required>

                <label>Password (kosongkan jika tidak ingin ubah)</label>
                <input type="password" name="password" id="editPassword">

                <label>Role</label>
                <select name="role" id="editRole" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>

                <div class="modal-buttons">
                    <button type="submit" class="btn-save">Simpan</button>
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah OPD -->
    <div id="addUnitModal" class="modal" style="display:none;">
        <div class="modal-content">
            <h2>Tambah OPD</h2>
            <form action="{{ route('units.store') }}" method="POST">
                @csrf
                <label>Nama Instansi</label>
                <input type="text" name="unit_name" required>

                <label>Alamat</label>
                <input type="text" name="address">

                <div class="modal-buttons">
                    <button type="submit" class="btn-save">Simpan</button>
                    <button type="button" class="btn-cancel" onclick="closeAddUnitModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit OPD -->
    <div id="editUnitModal" class="modal" style="display:none;">
        <div class="modal-content">
            <h2>Edit OPD</h2>
            <form id="editUnitForm" method="POST">
                @csrf
                @method('PUT')

                <label>Nama Instansi</label>
                <input type="text" name="unit_name" id="editUnitName" required>

                <label>Alamat</label>
                <input type="text" name="address" id="editUnitAddress">

                <div class="modal-buttons">
                    <button type="submit" class="btn-save">Simpan</button>
                    <button type="button" class="btn-cancel" onclick="closeEditUnitModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>


    <script>
        function openAddUnitModal() {
            document.getElementById('addUnitModal').style.display = 'flex';
        }

        function closeAddUnitModal() {
            document.getElementById('addUnitModal').style.display = 'none';
        }

        // Tutup modal kalau klik area luar
        window.onclick = function(event) {
            const editModal = document.getElementById('editModal');
            const addUnitModal = document.getElementById('addUnitModal');

            if (event.target === editModal) {
                editModal.style.display = 'none';
            } else if (event.target === addUnitModal) {
                addUnitModal.style.display = 'none';
            }
        }
    </script>

    <script>
        // 🔹 Buka modal tambah OPD
        function openAddUnitModal() {
            document.getElementById('addUnitModal').style.display = 'flex';
        }

        // 🔹 Tutup modal tambah OPD
        function closeAddUnitModal() {
            document.getElementById('addUnitModal').style.display = 'none';
        }

        // 🔹 Buka modal edit OPD
        function openEditUnitModal(button) {
            const modal = document.getElementById('editUnitModal');
            const form = document.getElementById('editUnitForm');

            const unitId = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const address = button.getAttribute('data-address');

            form.action = `/superadmin/units/${unitId}`;
            document.getElementById('editUnitName').value = name;
            document.getElementById('editUnitAddress').value = address ?? '';

            modal.style.display = 'flex';
        }

        // 🔹 Tutup modal edit OPD
        function closeEditUnitModal() {
            document.getElementById('editUnitModal').style.display = 'none';
        }

        // 🔹 Tutup modal kalau klik area luar
        window.onclick = function(event) {
            const addModal = document.getElementById('addUnitModal');
            const editModal = document.getElementById('editUnitModal');

            if (event.target === addModal) addModal.style.display = 'none';
            if (event.target === editModal) editModal.style.display = 'none';
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


        function openEditModal(button) {
            const modal = document.getElementById('editModal');
            const form = document.getElementById('editForm');
            const userId = button.getAttribute('data-id');

            form.action = `/superadmin/users/${userId}`;
            document.getElementById('editName').value = button.getAttribute('data-name');
            document.getElementById('editUsername').value = button.getAttribute('data-username');
            document.getElementById('editEmail').value = button.getAttribute('data-email');
            document.getElementById('editRole').value = button.getAttribute('data-role');

            modal.style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>

</html>
