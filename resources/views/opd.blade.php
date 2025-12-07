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
                <h1 class="page-title">Daftar OPD</h1>
                <p class="page-subtitle">Kelola organisasi perangkat daerah</p>
            </div>
        </div>

        <div class="search-section">
            <div class="search-box">
                <input type="text" id="globalSearch" class="search-input" placeholder="Cari OPD...">
                <button class="search-btn"><i class="fas fa-search"></i></button>
            </div>
        </div>

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
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const query = globalSearch.value.toLowerCase();

                    const table = document.getElementById('unitTable');
                    const rows = table.querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(query) ? '' : 'none';
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

            @if (session('error'))
                showErrorToast("{{ session('error') }}");
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

            function showErrorToast(message) {
                Toastify({
                    text: message,
                    duration: 3000,
                    gravity: 'top',
                    position: 'center',
                    style: {
                        background: '#F47C7C',
                        color: '#ffffff',
                        borderRadius: '6px',
                        boxShadow: '0 6px 14px rgba(0,0,0,0.08)'
                    }
                }).showToast();
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

    <style>
        .toastify {
            z-index: 999999 !important;
        }

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

        .toastify-popup {
            z-index: 999999 !important;
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
@endsection

