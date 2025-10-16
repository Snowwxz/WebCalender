<div class="fixed inset-0 flex items-center justify-center p-4 z-10" id="createAgendaModal" hidden>
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50" onclick="getel('createAgendaModal').hidden = true;"></div>
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl p-8 overflow-y-auto max-h-[90vh] relative">

        <button
            class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 transition duration-150 ease-in-out p-1 rounded-full hover:bg-gray-100"
            onclick="getel('createAgendaModal').hidden = true;">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"></path>
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
                    <p class="text-sm text-gray-500">Platform untuk mengajukan dan mengelola agenda kegiatan instansi</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('agenda.store') }}" onsubmit="return validateAgendaForm()">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-6">
                <div class="space-y-6">
                    <div>
                        <label for="agenda_name" class="block text-md font-medium text-gray-700">Nama Agenda</label>
                        <input type="text" id="agenda_name" name="agenda_name" required
                            placeholder="Masukkan nama agenda"
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                    </div>
                    <div>
                        <label for="description" class="block text-md font-medium text-gray-700">Deskripsi Agenda</label>
                        <textarea id="description" name="description" rows="3" required
                            placeholder="Masukkan deskripsi agenda"
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-gray-50"></textarea>
                    </div>
                    <div>
                        <label for="person_in_charge" class="block text-md font-medium text-gray-700">Penanggung Jawab</label>
                        <input type="text" id="person_in_charge" name="person_in_charge" required
                            placeholder="Masukkan nama penanggung jawab"
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                    </div>
                    <div>
                        <label for="involved_institution" class="block text-md font-medium text-gray-700">Instansi yang ikut serta</label>
                        <input type="text" id="involved_institution" name="involved_institution" required
                            placeholder="Masukkan nama instansi yang akan ikut serta"
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <label for="date" class="block text-md font-medium text-gray-700">Tanggal</label>
                        <input type="date" id="date" name="date" required
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                    </div>
                    <div>
                        <label for="start_time" class="block text-md font-medium text-gray-700">Waktu Mulai</label>
                        <input type="time" id="start_time" name="start_time" required
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                    </div>
                    <div>
                        <label for="end_time" class="block text-md font-medium text-gray-700">Waktu Selesai</label>
                        <input type="time" id="end_time" name="end_time" required
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                    </div>
                    <div>
                        <label for="location" class="block text-md font-medium text-gray-700">Lokasi</label>
                        <input type="text" id="location" name="location" required placeholder="Masukkan lokasi anda"
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
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
<script>
function validateAgendaForm() {
    const inputs = document.querySelectorAll('#createAgendaModal input[required], #createAgendaModal textarea[required]');
    let valid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('border-red-500');
            valid = false;
        } else {
            input.classList.remove('border-red-500');
        }
    });

    if (!valid) {
        alert('Semua wajib diisi!');
    }

    return valid;
}
</script>
