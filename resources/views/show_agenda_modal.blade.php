<!-- Modal Show Agenda -->
<div class="modal fade" id="showAgendaModal" tabindex="-1" aria-labelledby="showAgendaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- UPGRADE WIDTH -->
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-semibold d-flex align-items-center gap-2" id="showAgendaLabel">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Detail Agenda</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4 py-4">

                <h4 class="fw-bold text-primary mb-3" id="showAgendaName">-</h4>

                <div class="row g-3 mb-2">

                    <div class="col-md-6">
                        <label class="text-muted small">Tanggal</label>
                        <div class="fw-semibold" id="showAgendaDate">-</div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small">Waktu</label>
                        <div class="fw-semibold" id="showAgendaTime">-</div>
                    </div>

                    <div class="col-12">
                        <label class="text-muted small">Deskripsi</label>
                        <div class="fw-normal" id="showAgendaDesc">-</div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small">Lokasi</label>
                        <div class="fw-semibold" id="showAgendaLocation">-</div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small">Penanggung Jawab</label>
                        <div class="fw-semibold" id="showAgendaPIC">-</div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small">Instansi Pengaju</label>
                        <div class="fw-semibold" id="showAgendaUnit">-</div>
                    </div>

                    <div class="col-12">
                        <label class="text-muted small">Instansi yang Diundang</label>
                        <div class="fw-normal" id="showAgendaInvolved">-</div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small">Visibilitas</label>
                        <div class="fw-semibold" id="showAgendaAccess">-</div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small">Status</label>
                        <span id="showAgendaStatus" class="badge bg-warning text-dark rounded-pill px-3 py-2">-</span>
                    </div>

                </div>
            </div>

            <div class="modal-footer bg-light py-3">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
            </div>

        </div>
    </div>
</div>
