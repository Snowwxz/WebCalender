@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-bulan.css') }}">
@endpush

@section('content')
    @include('show_agenda_modal_dashboard')
    <div class="calendar-page">
        <div class="calendar-main">
            <div class="calendar-header">
                <div class="month-navigation">
                    <button class="nav-btn" onclick="changeMonth(-1)">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <h2 class="month-year" id="currentMonthYear">Oktober 2025</h2>
                    <button class="nav-btn" onclick="changeMonth(1)">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            <div class="calendar-grid">
                <div class="calendar-weekdays">
                    <div>Sen</div>
                    <div>Sel</div>
                    <div>Rab</div>
                    <div>Kam</div>
                    <div>Jum</div>
                    <div>Sab</div>
                    <div class="weekend">Min</div>
                </div>
                <div class="calendar-days" id="calendarDays">
                    <!-- Tanggal akan di-generate lewat JavaScript -->
                </div>
            </div>
            <div id="agendaSidebar" class="agenda-sidebar">
                <div class="sidebar-header">
                    <h3 id="agendaSidebarDate">Agenda Hari Ini</h3>
                    <button class="close-sidebar"
                        onclick="document.getElementById('agendaSidebar').classList.remove('active')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="agendaList" class="agenda-list"></div>
            </div>
        </div>
    </div>

    <!-- Modal Create Agenda -->
    <div class="modal-overlay" id="createAgendaModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Buat Agenda Baru</span>
                </div>
                <button class="modal-close" onclick="closeModal()" style="color: #dc3545;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body">
                <form id="agendaForm" action="{{ route('agenda.store') }}" method="POST"
                    onsubmit="return handleFormSubmit(event)">
                    <div class="form-grid">
                        <!-- Kolom kiri -->
                        <div class="form-column">
                            <div class="input-group">
                                <label><i class="fas fa-file-alt"></i> Nama Agenda</label>
                                <input type="text" name="agenda_name" id="agenda_name" placeholder="Masukkan nama agenda"
                                    required>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-align-left"></i> Deskripsi Agenda</label>
                                <textarea name="description" id="description" placeholder="Masukkan deskripsi agenda" required></textarea>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-building"></i> Nama Instansi (Pengaju)</label>
                                <select name="id_unit" id="id_unit" required>
                                    <option value="">-- Pilih Instansi Pengaju --</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id_unit }}">{{ $unit->unit_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-user-tie"></i> Penanggung Jawab</label>
                                <input type="text" name="person_in_charge" id="person_in_charge"
                                    placeholder="Masukkan nama penanggung jawab">
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-eye"></i> Kategori Agenda</label>
                                <select name="is_public" id="is_public">
                                    <option value="1">Publik</option>
                                    <option value="0">Privasi</option>
                                </select>
                            </div>
                        </div>

                        <!-- Kolom kanan -->
                        <div class="form-column">
                            <div class="input-group">
                                <label><i class="fas fa-calendar-day"></i> Tanggal</label>
                                <input type="date" name="date" id="date" required>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-clock"></i> Waktu Mulai</label>
                                <input type="time" name="start_time" id="start_time">
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-clock"></i> Waktu Selesai</label>
                                <input type="time" name="end_time" id="end_time">
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-location-dot"></i> Lokasi</label>
                                <input type="text" name="location" id="location" placeholder="Masukkan lokasi kegiatan">
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-people-group"></i> Instansi yang Ikut Serta</label>
                                <textarea name="involved_institution" id="involved_institution" placeholder="Masukkan instansi yang akan ikut serta"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-submit">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-paper-plane"></i> Ajukan Agenda
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ✅ Ambil dari URL (?bulan=3&tahun=2025) biar nggak selalu Oktober
        const urlParams = new URLSearchParams(window.location.search);
        let selectedMonth = parseInt(urlParams.get('bulan')) || new Date().getMonth() + 1;
        let selectedYear = parseInt(urlParams.get('tahun')) || new Date().getFullYear();

        let currentDate = new Date(selectedYear, selectedMonth - 1, 1);

        let agenda = {!! json_encode($agenda, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!};

        function handleFormSubmit(e) {
            e.preventDefault();
            if (!validateAgendaForm()) return false;

            const form = e.target;
            const formData = new FormData(form);

            fetch(form.action, {
                    method: "POST",
                    headers: {
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Tambahkan agenda baru langsung ke array lokal
                        agenda.push(data.agenda);
                        generateMainCalendar(); // re-render tampilan kalender
                        closeModal();

                        Swal.fire({
                            icon: 'success',
                            title: 'Agenda Ditambahkan!',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.message
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan pada server.'
                    });
                });

            return false;
        }

        /**
         * Filter data agenda berdasarkan tanggal, bulan, dan tahun.
         * @param {Array} agendaList - Array of agenda objects.
         * @param {Object} options - Filter options (year, month, day).
         * @returns {Array} - Data agenda yang sesuai filter.
         *
         * Contoh:
         * filterAgenda(agendaList, { year: 2025, month: 10, day: 16 });
         * filterAgenda(agendaList, { month: 10 }); // semua agenda bulan Oktober
         * filterAgenda(agendaList, { year: 2025 }); // semua agenda tahun 2025
         */
        function filterAgenda(agendaList, options = {}) {
            const {
                year,
                month,
                day
            } = options;

            return agendaList.filter(item => {
                const date = new Date(item.date);

                if (isNaN(date)) return false; // jaga-jaga kalau format tanggal rusak

                const matchYear = year ? date.getFullYear() === Number(year) : true;
                const matchMonth = month ? date.getMonth() + 1 === Number(month) : true;
                const matchDay = day ? date.getDate() === Number(day) : true;

                return matchYear && matchMonth && matchDay;
            });
        }

        // generate main calender
        function generateMainCalendar() {
            const monthYear = document.getElementById('currentMonthYear');
            const calendarDays = document.getElementById('calendarDays');

            const monthNames = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            monthYear.textContent = monthNames[currentDate.getMonth()] + ' ' + currentDate.getFullYear();


            const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - (firstDay.getDay() === 0 ? 6 : firstDay.getDay() -
                1)); // Mulai dari Senin

            calendarDays.innerHTML = '';

            for (let i = 0; i < 35; i++) { // 5 minggu x 7 hari
                const date = new Date(startDate);
                date.setDate(startDate.getDate() + i);

                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day';

                if (date.getMonth() !== currentDate.getMonth()) {
                    dayElement.classList.add('other-month');
                }

                if (date.getDay() === 0) {
                    dayElement.classList.add('weekend');
                } else if (date.getDay() === 6) {
                    dayElement.classList.add('saturday');
                }

                if (date.toDateString() === new Date().toDateString()) {
                    dayElement.classList.add('today');
                }

                const dayNumber = document.createElement('div');
                dayNumber.className = 'day-number';
                dayNumber.textContent = date.getDate();

                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');

                const filteredAgenda = filterAgenda(agenda, {
                    year,
                    month,
                    day
                });
                dayElement.appendChild(dayNumber);

                // === BADGE GENERATOR ===
                if (filteredAgenda.length > 0) {
                    const agendaContainer = document.createElement("div");
                    agendaContainer.className = "agenda-container";

                    const badge = document.createElement("div");

                    // Cek status dan tipe agenda
                    const hasApproved = filteredAgenda.some(a => a.status === 'approved');
                    const hasPending = filteredAgenda.some(a => a.status === 'pending');
                    const hasRejected = filteredAgenda.some(a => a.status === 'rejected');

                    // Tentukan warna badge
                    let badgeColor = "bg-gray-400";

                    if (hasRejected) {
                        badgeColor = "bg-red-500"; // ditolak
                    } else if (hasPending) {
                        badgeColor = "bg-yellow-500"; // menunggu
                    } else if (hasApproved) {
                        // Jika sudah approved → cek publik atau privasi
                        const isPublic = filteredAgenda.some(a => a.is_public == 1);
                        badgeColor = isPublic ? "bg-green-500" : "bg-orange-500";
                    }

                    badge.className = `agenda-count-badge ${badgeColor}`;
                    badge.textContent = filteredAgenda.length > 1 ?
                        `${filteredAgenda.length} Kegiatan` :
                        filteredAgenda[0].agenda_name;

                    badge.title = `${filteredAgenda.length} agenda hari ini`;

                    badge.addEventListener('click', (e) => {
                        e.stopPropagation();
                        showAgendaListSidebar(filteredAgenda, `${year}-${month}-${day}`);
                    });

                    agendaContainer.appendChild(badge);
                    dayElement.appendChild(agendaContainer);
                }

                // Klik angka tanggal -> buka halaman harian
                dayNumber.addEventListener('click', (e) => {
                    e.stopPropagation();
                    window.location.href = `/hari?tanggal=${year}-${month}-${day}`;
                });

                calendarDays.appendChild(dayElement);
            }
        }

        // Change month function
        function changeMonth(direction) {
            currentDate.setMonth(currentDate.getMonth() + direction);

            // Update URL
            const newMonth = currentDate.getMonth() + 1;
            const newYear = currentDate.getFullYear();
            const newUrl = `/dashboard/bulan?bulan=${newMonth}&tahun=${newYear}`;
            window.history.pushState({}, '', newUrl);

            generateMainCalendar();
        }

        // Modal functions
        function openModal(selectedDate = null) {
            const modal = document.getElementById('createAgendaModal');
            const dateInput = document.getElementById('date');

            if (selectedDate && dateInput) {
                dateInput.value = selectedDate;
            }

            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            const modal = document.getElementById('createAgendaModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';

            // Reset form
            document.getElementById('agendaForm').reset();
        }

        // Validasi form
        function validateAgendaForm() {
            const inputs = document.querySelectorAll(
                '#createAgendaModal input[required], #createAgendaModal textarea[required]');
            let valid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.style.borderColor = 'red';
                    valid = false;
                } else {
                    input.style.borderColor = '#d6e0d9';
                }
            });

            if (!valid) {
                alert('Semua field wajib diisi!');
            }

            return valid;
        }

        // Helper function untuk get element
        function getel(id) {
            return document.getElementById(id);
        }

        document.addEventListener('DOMContentLoaded', function() {
            generateMainCalendar();

            // Close modal when clicking outside
            document.getElementById('createAgendaModal').addEventListener('click', (e) => {
                if (e.target.id === 'createAgendaModal') {
                    closeModal();
                }


            });

            // Close modal with Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeModal();
                }
            });
        });
    </script>

    <script>
        // === 🔹 STATUS FILTER KATEGORI ===
        let showPublic = true;
        let showPrivate = true;

        // Fungsi untuk update status checkbox
        function initCategoryFilter() {
            const publicCheckbox = document.querySelector('.category-item.public input');
            const privateCheckbox = document.querySelector('.category-item.private input');

            // Saat pertama kali, kedua kategori aktif
            publicCheckbox.checked = true;
            privateCheckbox.checked = true;

            // Event listener untuk ubah filter
            publicCheckbox.addEventListener('change', () => {
                showPublic = publicCheckbox.checked;
                generateMainCalendar(); // render ulang
            });

            privateCheckbox.addEventListener('change', () => {
                showPrivate = privateCheckbox.checked;
                generateMainCalendar(); // render ulang
            });
        }

        // Modifikasi sedikit fungsi filterAgenda agar cek kategori juga
        function getFilteredAgendaForDay(year, month, day) {
            return agenda.filter(item => {
                const date = new Date(item.date);
                if (isNaN(date)) return false;

                const matchYear = date.getFullYear() === Number(year);
                const matchMonth = date.getMonth() + 1 === Number(month);
                const matchDay = date.getDate() === Number(day);

                // Filter kategori publik/privasi
                const isPublic = item.is_public == 1;
                const kategoriMatch = (isPublic && showPublic) || (!isPublic && showPrivate);

                return matchYear && matchMonth && matchDay && kategoriMatch;
            });
        }

        // 🔸 Update bagian generateMainCalendar() yang ambil filteredAgenda
        const oldGenerateMainCalendar = generateMainCalendar;
        generateMainCalendar = function() {
            const monthYear = document.getElementById('currentMonthYear');
            const calendarDays = document.getElementById('calendarDays');

            const monthNames = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            monthYear.textContent = monthNames[currentDate.getMonth()] + ' ' + currentDate.getFullYear();

            const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - (firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1));

            calendarDays.innerHTML = '';

            for (let i = 0; i < 35; i++) {
                const date = new Date(startDate);
                date.setDate(startDate.getDate() + i);

                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day';

                if (date.getMonth() !== currentDate.getMonth()) {
                    dayElement.classList.add('other-month');
                }

                if (date.getDay() === 0) dayElement.classList.add('weekend');
                else if (date.getDay() === 6) dayElement.classList.add('saturday');

                if (date.toDateString() === new Date().toDateString()) {
                    dayElement.classList.add('today');
                }

                const dayNumber = document.createElement('div');
                dayNumber.className = 'day-number';
                dayNumber.textContent = date.getDate();

                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');

                // 🔹 Ganti sini pakai fungsi baru yang sudah include kategori
                const filteredAgenda = getFilteredAgendaForDay(year, month, day);
                dayElement.appendChild(dayNumber);

                if (filteredAgenda.length > 0) {
                    const agendaContainer = document.createElement("div");
                    agendaContainer.className = "agenda-container";

                    // Pisahkan agenda publik dan privasi
                    const publicAgenda = filteredAgenda.filter(a => a.is_public == 1);
                    const privateAgenda = filteredAgenda.filter(a => a.is_public == 0);

                    const hasApproved = filteredAgenda.some(a => a.status === 'approved');
                    const hasPending = filteredAgenda.some(a => a.status === 'pending');
                    const hasRejected = filteredAgenda.some(a => a.status === 'rejected');

                    let badgeColor = "bg-gray-400";
                    if (hasRejected) badgeColor = "bg-red-500";
                    else if (hasPending) badgeColor = "bg-yellow-500";

                    // Jika approved dan ada publik DAN privasi → tampilkan badge terpisah
                    if (hasApproved && publicAgenda.length > 0 && privateAgenda.length > 0) {
                        // Badge publik
                        const publicBadge = document.createElement("div");
                        publicBadge.className = `agenda-count-badge bg-green-500`;
                        publicBadge.textContent = publicAgenda.length > 1 ?
                            `${publicAgenda.length} Kegiatan` :
                            publicAgenda[0].agenda_name;
                        
                        publicBadge.addEventListener('click', (e) => {
                            e.stopPropagation();
                            showAgendaListSidebar(publicAgenda, `${year}-${month}-${day}`);
                        });
                        agendaContainer.appendChild(publicBadge);

                        // Badge privasi
                        const privateBadge = document.createElement("div");
                        privateBadge.className = `agenda-count-badge bg-orange-500`;
                        privateBadge.textContent = privateAgenda.length > 1 ?
                            `${privateAgenda.length} Kegiatan` :
                            privateAgenda[0].agenda_name;
                        
                        privateBadge.addEventListener('click', (e) => {
                            e.stopPropagation();
                            showAgendaListSidebar(privateAgenda, `${year}-${month}-${day}`);
                        });
                        agendaContainer.appendChild(privateBadge);
                    }
                    // Jika approved tapi hanya publik ATAU privasi saja
                    else if (hasApproved && filteredAgenda.length > 0) {
                        const badge = document.createElement("div");
                        const isPublic = filteredAgenda.some(a => a.is_public == 1);
                        badge.className = `agenda-count-badge ${isPublic ? "bg-green-500" : "bg-orange-500"}`;
                        badge.textContent = filteredAgenda.length > 1 ?
                            `${filteredAgenda.length} Kegiatan` :
                            filteredAgenda[0].agenda_name;

                        badge.addEventListener('click', (e) => {
                            e.stopPropagation();
                            showAgendaListSidebar(filteredAgenda, `${year}-${month}-${day}`);
                        });

                        agendaContainer.appendChild(badge);
                    }
                    // Jika tidak ada approved (pending/rejected)
                    else if (!hasApproved && filteredAgenda.length > 0) {
                        const badge = document.createElement("div");
                        badge.className = `agenda-count-badge ${badgeColor}`;
                        badge.textContent = filteredAgenda.length > 1 ?
                            `${filteredAgenda.length} Kegiatan` :
                            filteredAgenda[0].agenda_name;

                        badge.addEventListener('click', (e) => {
                            e.stopPropagation();
                            showAgendaListSidebar(filteredAgenda, `${year}-${month}-${day}`);
                        });

                        agendaContainer.appendChild(badge);
                    }

                    dayElement.appendChild(agendaContainer);
                }

                dayNumber.addEventListener('click', (e) => {
                    e.stopPropagation();
                    window.location.href = `/hari?tanggal=${year}-${month}-${day}`;
                });

                calendarDays.appendChild(dayElement);
            }
        };

        // Jalankan saat halaman siap
        document.addEventListener('DOMContentLoaded', () => {
            initCategoryFilter();
            generateMainCalendar();
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', generateMainCalendar);

        function openShowAgendaModal(data) {
            document.getElementById('showAgendaName').innerText = data.agenda_name ?? '-';
            document.getElementById('showAgendaDate').innerText = formatDate(data.date);

            const timeText = (data.start_time && data.end_time) ?
                `${data.start_time} - ${data.end_time}` :
                (data.start_time ?? '-');
            document.getElementById('showAgendaTime').innerText = timeText;

            document.getElementById('showAgendaLocation').innerText = data.location ?? '-';
            document.getElementById('showAgendaPIC').innerText = data.person_in_charge ?? '-';
            document.getElementById('showAgendaDesc').innerText = data.description ?? '-';

            const involved = data.involved_institution ?? '-';
            const unitName = data.unit && data.unit.unit_name ? data.unit.unit_name : '-';

            const unitEl = document.getElementById('showAgendaUnit');
            if (unitEl) unitEl.innerText = unitName;

            const involvedEl = document.getElementById('showAgendaInvolved');
            if (involvedEl) involvedEl.innerText = involved;

            const accessEl = document.getElementById('showAgendaAccess');
            if (accessEl) accessEl.innerText = data.is_public == 1 ? 'Publik' : 'Privasi';

            const statusEl = document.getElementById('showAgendaStatus');
            if (statusEl) {
                statusEl.innerText =
                    data.status === 'approved' ? 'Disetujui' :
                    data.status === 'pending' ? 'Menunggu' :
                    data.status === 'rejected' ? 'Ditolak' : 'Tidak Diketahui';

                statusEl.className = "badge rounded-pill px-3 py-2 text-white " +
                    (data.status === 'approved' ? 'bg-success' :
                        data.status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark');
            }

            new bootstrap.Modal(document.getElementById('showAgendaModal')).show();
        }
    </script>

    <script>
        // ✅ INI DITARO DI LUAR
        const showAgendaModalEl = document.getElementById('showAgendaModal');

        showAgendaModalEl.addEventListener('show.bs.modal', function() {
            document.getElementById('agendaSidebar').classList.add('hidden');
        });

        showAgendaModalEl.addEventListener('hidden.bs.modal', function() {
            document.getElementById('agendaSidebar').classList.remove('hidden');
        });
    </script>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2000
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error') }}',
            });
        </script>
    @endif

    <script>
        function formatDate(dateString) {
            if (!dateString) return "-";
            const date = new Date(dateString);
            const options = {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            };
            return date.toLocaleDateString('id-ID', options);
        }

        function showAgendaListSidebar(agendaList, date) {
            const sidebar = document.getElementById("agendaSidebar");
            const listContainer = document.getElementById("agendaList");
            const title = document.getElementById("agendaSidebarDate");

            title.textContent = `Agenda ${date}`;
            listContainer.innerHTML = "";

            if (agendaList.length === 0) {
                listContainer.innerHTML = "<p>Tidak ada agenda untuk hari ini.</p>";
                return;
            }

            // Bikin collapsible list
            agendaList.forEach((item, index) => {
                const itemDiv = document.createElement("div");
                itemDiv.className = "agenda-item";

                const header = document.createElement("div");
                header.className = "agenda-header";
                header.innerHTML = `
                    <span class="agenda-item-title">${item.agenda_name}</span>
                    <span class="agenda-item-arrow"><i class="fas fa-chevron-right"></i></span>
                `;

                itemDiv.appendChild(header);

                itemDiv.addEventListener("click", () => {
                    openShowAgendaModal(item);
                });

                listContainer.appendChild(itemDiv);
            });

            sidebar.classList.add("active");
        }
    </script>
@endsection
