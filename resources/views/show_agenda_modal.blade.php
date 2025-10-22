<!-- Sidebar Modal -->
<div
    class="mt-32 fixed right-0 top-0 bg-white rounded-l-2xl shadow-xl w-full max-w-2xl p-6 overflow-y-auto max-h-screen z-50"
    id="showAgendaModal"
    hidden
>
    <!-- Header -->
    <div class="flex justify-between items-start border-b pb-4 mb-4 z-50">
        <div>
            <h2 class="text-lg font-bold" id="agendaTitle">Agenda</h2>
            <p class="text-gray-500 text-sm">Detail Informasi Agenda</p>
        </div>
        <button onclick="getel('showAgendaModal').hidden = true;" class="text-gray-500 hover:text-black text-2xl leading-none">&times;</button>
    </div>

    <!-- Badge -->
    <span id="agendaStatus" class="bg-green-200 text-green-800 text-sm px-3 py-1 rounded-full font-semibold"></span>

    <!-- Content -->
    <div class="mt-4 space-y-4">
        <p class="font-semibold" id="agendaName"></p>

        <div class="space-y-3 text-sm text-gray-700">
            <div>
                <span class="font-semibold block text-gray-900">📅 Tanggal:</span>
                <span id="agendaDate"></span>
            </div>
            <div>
                <span class="font-semibold block text-gray-900">🕒 Waktu:</span>
                <span id="agendaTime"></span>
            </div>
            <div>
                <span class="font-semibold block text-gray-900">📍 Lokasi:</span>
                <span id="agendaLocation"></span>
            </div>
            <div>
                <span class="font-semibold block text-gray-900">🏢 Instansi:</span>
                <span id="agendaInstitution"></span>
            </div>
            <div>
                <span class="font-semibold block text-gray-900">👥 Penanggung Jawab:</span>
                <span id="agendaPIC"></span>
            </div>
            <div>
                <span class="font-semibold block text-gray-900">📝 Deskripsi:</span>
                <span id="agendaDescription"></span>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-6 border-t pt-4">
        <button onclick="getel('showAgendaModal').hidden = true;" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 rounded-lg font-semibold">
            Tutup
        </button>
    </div>
</div>

<script>
    function showAgendaModal(id) {
        fetch(`/dashboard/agenda/${id}`)
            .then(response => response.json())
            .then(data => {
                getel('agendaTitle').innerText = data.agenda_name;
                getel('agendaName').innerText = data.agenda_name;
                getel('agendaDate').innerText = new Date(data.date).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                getel('agendaTime').innerText = `${data.start_time} – ${data.end_time} WITA`;
                getel('agendaLocation').innerText = data.location;
                getel('agendaInstitution').innerText = data.involved_institution;
                getel('agendaPIC').innerText = data.person_in_charge;
                getel('agendaDescription').innerText = data.description;
                getel('agendaStatus').innerText = data.status;
                getel('agendaStatus').className = `bg-${data.status === 'approved' ? 'green' : data.status === 'pending' ? 'yellow' : 'red'}-200 text-${data.status === 'approved' ? 'green' : data.status === 'pending' ? 'yellow' : 'red'}-800 text-sm px-3 py-1 rounded-full font-semibold`;
                getel('showAgendaModal').hidden = false;
            })
            .catch(error => console.error('Error fetching agenda details:', error));
    }
</script>