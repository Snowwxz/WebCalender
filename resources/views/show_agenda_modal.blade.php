<!-- Modal Show Agenda -->
<div class="modal fade" id="showAgendaModal" tabindex="-1" aria-labelledby="showAgendaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="showAgendaLabel">
                    Detail Agenda: <span id="showAgendaName">-</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="fw-semibold">Tanggal</label>
                    <p id="showAgendaDate" class="form-control-plaintext mb-0">-</p>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Waktu</label>
                    <p id="showAgendaTime" class="form-control-plaintext mb-0">-</p>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Deskripsi</label>
                    <p id="showAgendaDesc" class="form-control-plaintext mb-0">-</p>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Lokasi</label>
                    <p id="showAgendaLocation" class="form-control-plaintext mb-0">-</p>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Penanggung Jawab</label>
                    <p id="showAgendaPIC" class="form-control-plaintext mb-0">-</p>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Status</label>
                    <p id="showAgendaStatus" class="badge bg-warning text-dark">-</p>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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