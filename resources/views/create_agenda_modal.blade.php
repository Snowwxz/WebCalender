<div class="fixed inset-0 flex items-center justify-center p-4 z-10" id="createAgendaModal" hidden>
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50" onclick="document.getElementById('createAgendaModal').hidden = true;"></div>
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl p-8 overflow-y-auto max-h-[90vh] relative">

        <button
            class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 transition duration-150 ease-in-out p-1 rounded-full hover:bg-gray-100" onclick="document.getElementById('createAgendaModal').hidden = true;">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <div class="flex justify-between items-start border-b pb-4 mb-6 pr-10">
            <div class="flex items-center">
                <svg class="w-8 h-8 text-gray-700 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2-12H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2z"></path>
                </svg>
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800">Sistem Pengajuan Agenda</h2>
                    <p class="text-sm text-gray-500">Platform untuk mengajukan dan mengelola agenda kegiatan instansi
                    </p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('agenda.store') }}">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-6">
                <div class="space-y-6">
                    <div>
                        <label for="agenda_name" class="block text-md font-medium text-gray-700">Nama Agenda</label>
                        <input type="text" id="agenda_name" name="agenda_name" placeholder="Masukkan nama agenda"
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                    </div>
                    <div>
                        <label for="description" class="block text-md font-medium text-gray-700">Deskripsi
                            Agenda</label>
                        <textarea id="description" name="description" rows="3"
                            placeholder="Masukkan deskripsi agenda"
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-gray-50"></textarea>
                    </div>
                    <div>
                        <label for="person_in_charge" class="block text-md font-medium text-gray-700">Penanggung
                            Jawab</label>
                        <input type="text" id="person_in_charge" name="person_in_charge"
                            placeholder="Masukkan nama penanggung jawab"
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                    </div>
                    <div>
                        <label for="involved_institution" class="block text-md font-medium text-gray-700">Instansi yang
                            ikut serta</label>
                        <input type="text" id="involved_institution" name="involved_institution"
                            placeholder="Masukkan nama instansi yang akan ikut serta"
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <label for="date" class="block text-md font-medium text-gray-700">Tanggal</label>
                        <input type="date" id="date" name="date"
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Waktu Mulai -->
    <div class="relative">
        <label for="start_time" class="block text-md font-medium text-gray-700 mb-1">Waktu Mulai</label>
        <input type="text" id="start_time" name="start_time"
            placeholder="--:--" readonly
            class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-gray-50 cursor-pointer focus:outline-none focus:ring-blue-500 focus:border-blue-500">

        <div id="start_time_picker"
            class="absolute z-50 hidden bg-white border border-gray-300 rounded-lg shadow-lg mt-2 p-2 flex gap-2">
            <select id="start_hour"
                class="p-2 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @for ($h = 0; $h < 24; $h++)
                    <option value="{{ sprintf('%02d', $h) }}">{{ sprintf('%02d', $h) }}</option>
                @endfor
            </select>
            <select id="start_minute"
                class="p-2 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @for ($m = 0; $m < 60; $m += 5)
                    <option value="{{ sprintf('%02d', $m) }}">{{ sprintf('%02d', $m) }}</option>
                @endfor
            </select>
            <button type="button" id="confirm_start"
                class="px-3 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                OK
            </button>
        </div>
    </div>

    <!-- Waktu Selesai -->
    <div class="relative">
        <label for="end_time" class="block text-md font-medium text-gray-700 mb-1">Waktu Selesai</label>
        <input type="text" id="end_time" name="end_time"
            placeholder="--:--" readonly
            class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-gray-50 cursor-pointer focus:outline-none focus:ring-blue-500 focus:border-blue-500">

        <div id="end_time_picker"
            class="absolute z-50 hidden bg-white border border-gray-300 rounded-lg shadow-lg mt-2 p-2 flex gap-2">
            <select id="end_hour"
                class="p-2 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @for ($h = 0; $h < 24; $h++)
                    <option value="{{ sprintf('%02d', $h) }}">{{ sprintf('%02d', $h) }}</option>
                @endfor
            </select>
            <select id="end_minute"
                class="p-2 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @for ($m = 0; $m < 60; $m += 5)
                    <option value="{{ sprintf('%02d', $m) }}">{{ sprintf('%02d', $m) }}</option>
                @endfor
            </select>
            <button type="button" id="confirm_end"
                class="px-3 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                OK
            </button>
        </div>
    </div>
</div>

<script>
    // Helper: bikin dropdown bisa reusable
    function setupTimePicker(inputId, pickerId, hourId, minuteId, confirmId) {
        const input = document.getElementById(inputId);
        const picker = document.getElementById(pickerId);
        const hourSel = document.getElementById(hourId);
        const minSel = document.getElementById(minuteId);
        const confirmBtn = document.getElementById(confirmId);

        input.addEventListener('click', () => {
            picker.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!picker.contains(e.target) && e.target !== input) {
                picker.classList.add('hidden');
            }
        });

        confirmBtn.addEventListener('click', () => {
            const hour = hourSel.value;
            const minute = minSel.value;
            input.value = `${hour}:${minute}`;
            picker.classList.add('hidden');
        });
    }

    // aktifkan untuk dua input
    setupTimePicker('start_time', 'start_time_picker', 'start_hour', 'start_minute', 'confirm_start');
    setupTimePicker('end_time', 'end_time_picker', 'end_hour', 'end_minute', 'confirm_end');
</script>

                    <div>
                        <label for="location" class="block text-md font-medium text-gray-700">Lokasi</label>
                        <input type="text" id="location" name="location" placeholder="Masukkan lokasi anda"
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t">
                <button type="submit"
                    class="w-full py-3 px-4 bg-gray-400 text-white font-semibold rounded-lg shadow-md hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-75 transition duration-150 ease-in-out">
                    Ajukan Agenda
                </button>
            </div>
        </form>

    </div>
</div>
<script src="https://cdn.tailwindcss.com"></script>