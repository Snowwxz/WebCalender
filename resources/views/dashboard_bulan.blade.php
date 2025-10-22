@extends('layouts.main')

@section('content')
    @include("show_agenda_modal")
    @include("create_agenda_modal")
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

                for (let i=0; i<filteredAgenda.length; i++) {
                    const badge = document.createElement("div");
                    badge.setAttribute("class", "bg-gray-400 text-white mb-2 rounded-sm p-2");
                    badge.innerHTML = filteredAgenda[i].agenda_name;
                    dayElement.appendChild(badge);
                }

                            // ✅ Klik di area hari
                dayElement.addEventListener('click', (e) => {
                    // Filter agenda hari ini
                    let filteredAgenda = filterAgenda(agenda, { year, month, day });

                    // Kalau ada agenda di hari ini, jangan buka modal create
                    if (filteredAgenda.length > 0) return;

                    // Kalau user klik angka tanggal (bukan area kosong), skip modal
                    if (e.target.classList.contains('day-number')) return;

                    // Kalau lolos semua kondisi di atas → buka modal create
                    getel("date").value = `${year}-${month}-${day}`;
                    getel("createAgendaModal").hidden = false;
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

        document.addEventListener('DOMContentLoaded', generateMainCalendar);

        function openShowAgendaModal(data) {
        document.getElementById('showAgendaName').innerText = data.agenda_name ?? '-';
        document.getElementById('showAgendaDate').innerText = data.date ?? '-';

        const timeText = (data.start_time && data.end_time)
            ? `${data.start_time} - ${data.end_time}`
            : (data.start_time ?? '-');
        document.getElementById('showAgendaTime').innerText = timeText;

        document.getElementById('showAgendaLocation').innerText = data.location ?? '-';
        document.getElementById('showAgendaPIC').innerText = data.person_in_charge ?? '-';
        document.getElementById('showAgendaDesc').innerText = data.description ?? '-';

        const statusEl = document.getElementById('showAgendaStatus');
        statusEl.innerText = data.status ?? 'pending';
        statusEl.className = "badge text-white " +
            (data.status === 'approved' ? 'bg-success' :
             data.status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark');

        // ✅ Bootstrap modal init
        const modal = new bootstrap.Modal(document.getElementById('showAgendaModal'));
        modal.show();
        }

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

@endsection
