<div class="modal fade" id="showAgendaModal" tabindex="-1" aria-labelledby="showAgendaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

            <div class="modal-header bg-primary text-white py-3 position-relative">
                <h5 class="modal-title fw-semibold d-flex align-items-center gap-2" id="showAgendaLabel">
                    <i class="fas fa-calendar-alt"></i>
                    <span id="showAgendaName">Detail Agenda</span>
                </h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body px-4 py-4">

                <!-- Nama Agenda -->
                <div class="mb-4">
                    <label class="text-muted small d-block mb-1">
                        <i class="fas fa-calendar"></i> Nama Agenda
                    </label>
                    <div class="fw-semibold fs-5 text-capitalize" id="showAgendaNameDetail">-</div>
                </div>

                <!-- Deskripsi -->
                <div class="mb-4">
                    <label class="text-muted small d-block mb-1">
                        <i class="fas fa-align-left"></i> Deskripsi Agenda
                    </label>
                    <div class="fw-normal" id="showAgendaDesc" style="white-space: pre-wrap;">-</div>
                </div>

                <!-- Grid 2 Kolom -->
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="text-muted small d-block mb-1">
                                <i class="fas fa-calendar-day"></i> Tanggal
                            </label>
                            <div class="fw-semibold" id="showAgendaDate">-</div>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small d-block mb-1">
                                <i class="fas fa-clock"></i> Waktu
                            </label>
                            <div class="fw-semibold" id="showAgendaTime">-</div>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small d-block mb-1">
                                <i class="fas fa-map-marker-alt"></i> Lokasi
                            </label>
                            <div class="fw-semibold text-capitalize" id="showAgendaLocation">-</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="text-muted small d-block mb-1">
                                <i class="fas fa-building"></i> Instansi Pengaju
                            </label>
                            <div class="fw-semibold" id="showAgendaUnit">-</div>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small d-block mb-1">
                                <i class="fas fa-users"></i> Instansi yang Diundang
                            </label>
                            <div class="fw-normal" id="showAgendaInvolved">-</div>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small d-block mb-1">
                                <i class="fas fa-user-tie"></i> Penanggung Jawab
                            </label>
                            <div class="fw-semibold" id="showAgendaPIC">-</div>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small d-block mb-1">
                                <i class="fas fa-eye"></i> Visibilitas
                            </label>
                            <div class="fw-semibold" id="showAgendaAccess">-</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
