@extends('layouts.main')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-bulan.css') }}">
@endpush

@section('content')
    @include("show_agenda_modal")
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
                    <div class="weekend">Sab</div>
                    <div class="weekend">Min</div>
                </div>
                <div class="calendar-days" id="calendarDays">
                    <!-- Tanggal akan di-generate lewat JavaScript -->
                </div>
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
                <button class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body">
                <form id="agendaForm" action="{{ route('agenda.store') }}" method="POST" onsubmit="return validateAgendaForm()">
                    @csrf

                    <div class="form-grid">
                        <!-- Kolom kiri -->
                        <div class="form-column">
                            <div class="input-group">
                                <label><i class="fas fa-file-alt"></i> Nama Agenda</label>
                                <input type="text" name="agenda_name" id="agenda_name" placeholder="Masukkan nama agenda" required>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-align-left"></i> Deskripsi Agenda</label>
                                <textarea name="description" id="description" placeholder="Masukkan deskripsi agenda" required></textarea>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-user-tie"></i> Penanggung Jawab</label>
                                <input type="text" name="person_in_charge" id="person_in_charge" placeholder="Masukkan nama penanggung jawab" required>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-people-group"></i> Instansi yang Ikut Serta</label>
                                <textarea name="involved_institution" id="involved_institution" placeholder="Masukkan instansi yang akan ikut serta" required></textarea>
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
                                <input type="time" name="start_time" id="start_time" required>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-clock"></i> Waktu Selesai</label>
                                <input type="time" name="end_time" id="end_time" required>
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-location-dot"></i> Lokasi</label>
                                <input type="text" name="location" id="location" placeholder="Masukkan lokasi agenda" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-submit">
                        <button type="button" class="btn-secondary" onclick="closeModal()">Batal</button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-paper-plane"></i> Ajukan Agenda
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // ✅ Ambil dari URL (?bulan=3&tahun=2025) biar nggak selalu Oktober
        const urlParams = new URLSearchParams(window.location.search);
        let selectedMonth = parseInt(urlParams.get('bulan')) || new Date().getMonth() + 1;
        let selectedYear = parseInt(urlParams.get('tahun')) || new Date().getFullYear();

        let currentDate = new Date(selectedYear, selectedMonth - 1, 1);

       let agenda = {!! json_encode($agenda, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!};


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
            const { year, month, day } = options;

            return agendaList.filter(item => {
                const date = new Date(item.date);

                if (isNaN(date)) return false; // jaga-jaga kalau format tanggal rusak

                const matchYear = year ? date.getFullYear() === Number(year) : true;
                const matchMonth = month ? date.getMonth() + 1 === Number(month) : true;
                const matchDay = day ? date.getDate() === Number(day) : true;

                return matchYear && matchMonth && matchDay;
            });
        }


        // Generate main calendar
        function generateMainCalendar() {
            const monthYear = document.getElementById('currentMonthYear');
            const calendarDays = document.getElementById('calendarDays');

            const monthNames = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            monthYear.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;

            const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - (firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1)); // Mulai dari Senin

            calendarDays.innerHTML = '';

            for (let i = 0; i < 35; i++) { // 5 minggu x 7 hari
                const date = new Date(startDate);
                date.setDate(startDate.getDate() + i);

                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day';

                if (date.getMonth() !== currentDate.getMonth()) {
                    dayElement.classList.add('other-month');
                }

                if (date.getDay() === 0 || date.getDay() === 6) {
                    dayElement.classList.add('weekend');
                }

                if (date.toDateString() === new Date().toDateString()) {
                    dayElement.classList.add('today');
                }

                const dayNumber = document.createElement('div');
                dayNumber.className = 'day-number';
                dayNumber.textContent = date.getDate();

                if (date.getDay() === 0 || date.getDay() === 6) {
                    dayNumber.classList.add('weekend');
                }

                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');

                let filteredAgenda = filterAgenda(agenda, {year, month, day});
                console.log(filteredAgenda.length);
                dayElement.appendChild(dayNumber);
                calendarDays.appendChild(dayElement);

                 // Buat container untuk agenda items
                 const agendaContainer = document.createElement("div");
                 agendaContainer.className = "agenda-container";

                 // Tampilkan maksimal 3 agenda
                 const maxAgenda = 3;
                 const agendaToShow = filteredAgenda.slice(0, maxAgenda);

                 for (let i=0; i<agendaToShow.length; i++) {
                     const badge = document.createElement("div");

                     // Tentukan class berdasarkan status agenda
                     let badgeClass = "text-white mb-2 rounded-sm p-2";
                     if (agendaToShow[i].status === 'approved') {
                         badgeClass = "bg-green-500 " + badgeClass; // Hijau untuk approved
                     } else if (agendaToShow[i].status === 'pending') {
                         badgeClass = "bg-yellow-500 " + badgeClass; // Kuning untuk pending
                     } else if (agendaToShow[i].status === 'rejected') {
                         badgeClass = "bg-red-500 " + badgeClass; // Merah untuk rejected
                     } else {
                         badgeClass = "bg-gray-400 " + badgeClass; // Default abu-abu
                     }

                     badge.setAttribute("class", badgeClass);

                     // Batasi panjang teks agenda (maksimal 20 karakter)
                     let agendaText = agendaToShow[i].agenda_name;
                     if (agendaText.length > 20) {
                         agendaText = agendaText.substring(0, 17) + "...";
                     }

                     badge.innerHTML = agendaText;
                     badge.title = `${agendaToShow[i].agenda_name} (Status: ${agendaToShow[i].status})`; // Tooltip dengan status
                     agendaContainer.appendChild(badge);
                 }

                 // Tambahkan indikator jika ada agenda lebih dari 3
                 if (filteredAgenda.length > maxAgenda) {
                     agendaContainer.classList.add("has-more");
                 }

                 dayElement.appendChild(agendaContainer);

                             // ✅ Klik di area hari
                 dayElement.addEventListener('click', (e) => {
                     // Filter agenda hari ini
                     let filteredAgenda = filterAgenda(agenda, { year, month, day });

                     // Kalau ada agenda di hari ini, jangan buka modal create
                     if (filteredAgenda.length > 0) return;

                     // Kalau user klik angka tanggal (bukan area kosong), skip modal
                     if (e.target.classList.contains('day-number')) return;

                     // Kalau lolos semua kondisi di atas → buka modal create
                     openModal(`${year}-${month}-${day}`);
                 });

                // ✅ Klik angka tanggal (langsung buka halaman hari)
                dayNumber.addEventListener('click', (e) => {
                    e.stopPropagation(); // Supaya event klik ini gak nyetrum ke dayElement
                    window.location.href = `/hari?tanggal=${year}-${month}-${day}`;
                });
            }
        }

        // Change month
        function changeMonth(direction) {
            currentDate.setMonth(currentDate.getMonth() + direction);
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
             const inputs = document.querySelectorAll('#createAgendaModal input[required], #createAgendaModal textarea[required]');
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
        document.addEventListener('DOMContentLoaded', generateMainCalendar);

        function openShowAgendaModal(data) {
    // Nama agenda
    document.getElementById('showAgendaName').innerText = data.agenda_name ?? '-';

    // Tanggal
    document.getElementById('showAgendaDate').innerText = data.date ?? '-';

    // Waktu (kalau ada start dan end time)
    const timeText = (data.start_time && data.end_time)
        ? `${data.start_time} - ${data.end_time}`
        : (data.start_time ?? '-');
    document.getElementById('showAgendaTime').innerText = timeText;

    // Lokasi
    document.getElementById('showAgendaLocation').innerText = data.location ?? '-';

    // Penanggung jawab
    document.getElementById('showAgendaPIC').innerText = data.person_in_charge ?? '-';

    // Deskripsi
    document.getElementById('showAgendaDesc').innerText = data.description ?? '-';

    // Status (warna disesuaikan)
    const statusEl = document.getElementById('showAgendaStatus');
    statusEl.innerText = data.status ?? 'pending';
    statusEl.className = "badge text-white " +
        (data.status === 'approved' ? 'bg-success' :
         data.status === 'rejected' ? 'bg-danger' :
         'bg-warning text-dark');

    // ✅ Tampilkan modal pakai Bootstrap
    const modal = new bootstrap.Modal(document.getElementById('showAgendaModal'));
    mzscript>
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

@endsection
