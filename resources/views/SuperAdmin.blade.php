<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin - SiKota</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
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
                                <!-- Kosong: data user akan tampil di sini -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="section-space"></div>

                <div class="admin-card">
                    <div class="admin-card-header">
                        <button class="btn-chip" type="button">
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
                                    <th>Nama Organisasi Perangkat Daerah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Kosong: daftar OPD akan tampil di sini -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            const profile = document.querySelector('.user-profile');
            dropdown.classList.toggle('show');
            profile.classList.toggle('active');
        }
        document.addEventListener('click', function (event) {
            const dropdown = document.getElementById('userDropdown');
            const profile = document.querySelector('.user-profile');
            if (profile && !profile.contains(event.target)) {
                dropdown && dropdown.classList.remove('show');
                profile.classList.remove('active');
            }
        });
    </script>
</body>

</html>


