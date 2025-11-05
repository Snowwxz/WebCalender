<div class="modal fade" id="showAgendaModal" tabindex="-1" aria-labelledby="showAgendaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-sm rounded-4 overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header" style="background-color: #F6F8F7; border: none;">
                <h5 class="modal-title fw-semibold d-flex align-items-center gap-2" id="showAgendaLabel"
                    style="color: #4A7C59;">
                    <i class="fas fa-calendar-alt" style="color: #4A7C59;"></i>
                    <span id="showAgendaName">Rapat</span>
                </h5>
                <!-- Tombol Close -->
                <button type="button" class="circle-close-btn" data-bs-dismiss="modal" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="#4A7C59" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- BODY -->
            <div class="modal-body px-4 pt-3 pb-4">

                <!-- Deskripsi Agenda -->
                <div class="mb-3">
                    <label class="text-secondary small d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-align-left" style="color: #82A98D;"></i>
                        Deskripsi Agenda
                    </label>
                    <div class="border rounded-3 p-3 bg-light fw-semibold" id="showAgendaDesc"
                        style="white-space: pre-wrap; min-height: 60px;">
                        -
                    </div>
                </div>

                <!-- Informasi Agenda -->
                <div class="row g-4">
                    <div class="col-md-6">
                        <div>
                            <label class="text-secondary small d-flex align-items-center gap-2 mb-1">
                                <i class="fas fa-calendar-day" style="color: #82A98D;"></i>
                                Tanggal
                            </label>
                            <div class="fw-semibold" id="showAgendaDate">-</div>
                        </div>

                        <div class="mt-3">
                            <label class="text-secondary small d-flex align-items-center gap-2 mb-1">
                                <i class="fas fa-clock" style="color: #82A98D;"></i>
                                Waktu
                            </label>
                            <div class="fw-semibold" id="showAgendaTime">-</div>
                        </div>

                        <div class="mt-3">
                            <label class="text-secondary small d-flex align-items-center gap-2 mb-1">
                                <i class="fas fa-map-marker-alt" style="color: #82A98D;"></i>
                                Lokasi
                            </label>
                            <div class="fw-semibold" id="showAgendaLocation">-</div>
                        </div>

                        <div class="mt-3">
                            <label class="text-secondary small d-flex align-items-center gap-2 mb-1">
                                <i class="fas fa-eye" style="color: #82A98D;"></i>
                                Status
                            </label>
                            <div class="fw-semibold" id="showAgendaAccess">-</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div>
                            <label class="text-secondary small d-flex align-items-center gap-2 mb-1">
                                <i class="fas fa-building" style="color: #82A98D;"></i>
                                Instansi Pengaju
                            </label>
                            <div class="fw-semibold" id="showAgendaUnit">-</div>
                        </div>

                        <div class="mt-3">
                            <label class="text-secondary small d-flex align-items-center gap-2 mb-1">
                                <i class="fas fa-users" style="color: #82A98D;"></i>
                                Instansi yang Diundang
                            </label>
                            <div class="fw-semibold" id="showAgendaInvolved">-</div>
                        </div>

                        <div class="mt-3">
                            <label class="text-secondary small d-flex align-items-center gap-2 mb-1">
                                <i class="fas fa-user-tie" style="color: #82A98D;"></i>
                                Penanggung Jawab
                            </label>
                            <div class="fw-semibold" id="showAgendaPIC">-</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>