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
</div>
