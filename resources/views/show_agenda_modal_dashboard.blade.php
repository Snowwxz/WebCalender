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
                <button type="button" class="close-modal" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- BODY -->
            <div class="modal-body px-4 pt-3 pb-4">

                <!-- Deskripsi Agenda -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="text-secondary small d-flex align-items-center gap-2 mb-0">
                            <i class="fas fa-align-left" style="color: #82A98D;"></i>
                            Deskripsi Agenda
                        </label>
                        <button type="button" class="btn btn-sm copy-agenda-btn" id="copyAgendaBtn"
                            title="Salin Agenda"
                            style="border: none; color: #82A98D; background: none; padding: 4px 8px; transition: all 0.3s ease; outline: none; box-shadow: none;"
                            onmouseover="this.style.color='black'; this.style.backgroundColor='rgba(130, 169, 141, 0.1)';"
                            onmouseout="this.style.color='#82A98D'; this.style.backgroundColor='';">
                            <i class="fas fa-copy"></i> Salin
                        </button>
                    </div>
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
                    </div>

                    <div class="col-md-6">
                        <div>
                            <label class="text-secondary small d-flex align-items-center gap-2 mb-1">
                                <i class="fas fa-building" style="color: #82A98D;"></i>
                                Pelaksana
                            </label>
                            <div class="fw-semibold" id="showAgendaUnit">-</div>
                        </div>

                        <div class="mt-3">
                            <label class="text-secondary small d-flex align-items-center gap-2 mb-1">
                                <i class="fas fa-users" style="color: #82A98D;"></i>
                                Dihadiri
                            </label>
                            <div class="fw-semibold" id="showAgendaInvolved">-</div>
                        </div>

                        <div class="mt-3">
                            <label class="text-secondary small d-flex align-items-center gap-2 mb-1">
                                <i class="fas fa-eye" style="color: #82A98D;"></i>
                                Status
                            </label>
                            <div class="fw-semibold" id="showAgendaAccess">-</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="text-secondary small d-flex align-items-center gap-2 mb-1">
                        <i class="fa-solid fa-file-lines" style="color: #82A98D;"></i>
                        Catatan
                    </label>
                    <div class="fw-semibold border rounded-3 p-3 bg-light" id="showAgendaNotes"
                        style="white-space: pre-wrap; min-height: 60px;">
                        -
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const copyBtn = document.getElementById('copyAgendaBtn');
    if (!copyBtn) return;

    copyBtn.addEventListener('click', function() {
        try {
            const get = id => document.getElementById(id)?.textContent.trim() || "-";

            const agendaData = {
                name: get('showAgendaName'),
                desc: get('showAgendaDesc'),
                date: get('showAgendaDate'),
                time: get('showAgendaTime'),
                location: get('showAgendaLocation'),
                unit: get('showAgendaUnit'),
                involved: get('showAgendaInvolved'),
                access: get('showAgendaAccess'),
                notes: get('showAgendaNotes')
            };

            const textToCopy =
`AGENDA: ${agendaData.name}

Deskripsi: ${agendaData.desc}

Tanggal: ${agendaData.date}
Waktu: ${agendaData.time}
Lokasi: ${agendaData.location}

Pelaksana: ${agendaData.unit}
Dihadiri: ${agendaData.involved}
Status: ${agendaData.access}

Catatan: ${agendaData.notes}`;

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(textToCopy).then(showSuccessFeedback).catch(() => fallbackCopy(textToCopy));
            } else fallbackCopy(textToCopy);
        } catch (err) {
            console.error('Error saat menyalin agenda:', err);
            alert('Terjadi kesalahan saat menyalin agenda');
        }
    });

    function fallbackCopy(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-9999px';
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        textArea.remove();
        showSuccessFeedback();
    }

    function showSuccessFeedback() {
        const original = copyBtn.innerHTML;
        copyBtn.innerHTML = '<i class="fas fa-check"></i> Tersalin!';
        copyBtn.style.color = 'white';
        copyBtn.style.backgroundColor = '#82A98D';
        setTimeout(() => {
            copyBtn.innerHTML = original;
            copyBtn.style.color = '#82A98D';
            copyBtn.style.backgroundColor = '';
        }, 2000);
    }

    // 🔽 TAMBAHKAN BAGIAN INI 🔽
    $('#showAgendaModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const name = button.data('name');
        const desc = button.data('desc');
        const date = button.data('date');
        const time = button.data('time');
        const location = button.data('location');
        const unit = button.data('unit');
        const involved = button.data('involved');
        const access = button.data('access');
        const notes = button.data('notes'); // penting!

        const modal = $(this);
        modal.find('#showAgendaName').text(name || '-');
        modal.find('#showAgendaDesc').text(desc || '-');
        modal.find('#showAgendaDate').text(date || '-');
        modal.find('#showAgendaTime').text(time || '-');
        modal.find('#showAgendaLocation').text(location || '-');
        modal.find('#showAgendaUnit').text(unit || '-');
        modal.find('#showAgendaInvolved').text(involved || '-');
        modal.find('#showAgendaAccess').text(access || '-');
        modal.find('#showAgendaNotes').text(notes || '-'); // supaya catatan muncul
    });
});
</script>