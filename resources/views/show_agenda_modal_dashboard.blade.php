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
                <button type="button" class="close-modal" data-bs-dismiss="modal" aria-label="Tutup">
                    <i class="fa-solid fa-xmark"></i>
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
                        <button type="button" class="copy-agenda-btn" id="copyAgendaBtn" title="Salin Agenda">
                            <i class="fas fa-copy"></i>
                            Salin
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

            function normalizeAttendees(raw) {
                const txt = (raw || '').trim();
                if (!txt || txt === '-') return { plain: '-', html: '-' };
                if (/\d+\.\s/.test(txt)) {
                    const firstIndex = txt.search(/\d+\.\s/);
                    const header = txt.slice(0, firstIndex).trim();
                    const items = txt.slice(firstIndex).split(/\d+\.\s/).map(s => s.trim()).filter(Boolean);
                    return {
                        plain: (header ? header + '\n' : '') + items.map(s => `• ${s}`).join('\n'),
                        html: (header ? esc(header) + '<br>' : '') + '<ul>' + items.map(s => `<li>${esc(s)}</li>`).join('') + '</ul>'
                    };
                }
                const parts = txt.split(/,\s+|\n+/).map(s => s.trim()).filter(Boolean);
                if (parts.length <= 1) return { plain: txt, html: esc(txt) };
                return {
                    plain: parts.map(s => `• ${s}`).join('\n'),
                    html: '<ul>' + parts.map(s => `<li>${esc(s)}</li>`).join('') + '</ul>'
                };
            }

            const dateUpper = (agendaData.date || '-').toUpperCase();
            const headerPlain = `*AGENDA ${dateUpper}*\n\n`;
            const esc = s => String(s).replace(/[&<>]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;'}[c]));
            const headerHtml = `<b>AGENDA ${esc(dateUpper)}</b><br><br>`;
            const involvedNorm = normalizeAttendees(agendaData.involved);

            const plainToCopy =
headerPlain +
`*AGENDA:* ${agendaData.name}\n\n`+
`*Deskripsi:*\n${agendaData.desc}\n\n`+
`*Tanggal:* ${agendaData.date}\n`+
`*Waktu:* ${agendaData.time}\n`+
`*Lokasi:* ${agendaData.location}\n\n`+
`*Pelaksana:* ${agendaData.unit}\n`+
`*Dihadiri:*\n${involvedNorm.plain}\n\n`+
`*Status:* ${agendaData.access}\n`+
`*Catatan:* ${agendaData.notes}`;

            const htmlToCopy =
headerHtml +
`<b>AGENDA:</b> ${esc(agendaData.name)}<br><br>`+
`<b>Deskripsi:</b><br>${esc(agendaData.desc)}<br><br>`+
`<b>Tanggal:</b> ${esc(agendaData.date)}<br>`+
`<b>Waktu:</b> ${esc(agendaData.time)}<br>`+
`<b>Lokasi:</b> ${esc(agendaData.location)}<br><br>`+
`<b>Pelaksana:</b> ${esc(agendaData.unit)}<br>`+
`<b>Dihadiri:</b>`+
 involvedNorm.html +
`<br>`+
`<b>Status:</b> ${esc(agendaData.access)}<br>`+
`<b>Catatan:</b> ${esc(agendaData.notes)}`;

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(plainToCopy).then(showSuccessFeedback).catch(() => fallbackCopy(plainToCopy));
            } else {
                fallbackCopy(plainToCopy);
            }
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

    // Fungsi global untuk mengisi modal agenda
    window.fillAgendaModal = function(data) {
        if (!data) return;

        // Format tanggal
        function formatDate(dateString) {
            if (!dateString) return "-";
            const date = new Date(dateString);
            const options = {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            };
            return date.toLocaleDateString('id-ID', options);
        }

        // Isi nama agenda
        const nameEl = document.getElementById('showAgendaName');
        if (nameEl) {
            nameEl.textContent = data.agenda_name || '-';
        }

        // Isi tanggal
        const dateEl = document.getElementById('showAgendaDate');
        if (dateEl) {
            dateEl.textContent = formatDate(data.date);
        }

        // Isi waktu
        const timeEl = document.getElementById('showAgendaTime');
        if (timeEl) {
            let timeText = '-';
            if (data.start_time && data.end_time) {
                const start = data.start_time.slice(0, 5);
                const end = data.end_time.slice(0, 5);
                timeText = `${start} - ${end}`;
            } else if (data.end_time) {
                timeText = data.end_time.slice(0, 5);
            } else if (data.start_time) {
                timeText = data.start_time.slice(0, 5);
            }
            timeEl.textContent = timeText;
        }

        // Isi lokasi
        const locationEl = document.getElementById('showAgendaLocation');
        if (locationEl) {
            locationEl.textContent = data.location || '-';
        }

        // Isi deskripsi
        const descEl = document.getElementById('showAgendaDesc');
        if (descEl) {
            descEl.textContent = data.description || '-';
        }

        // Isi pelaksana
        const unitEl = document.getElementById('showAgendaUnit');
        if (unitEl) {
            const unitName = (data.unit && data.unit.unit_name) ? data.unit.unit_name : '-';
            unitEl.textContent = unitName;
        }

        // Format Dihadiri berdasarkan invitations
        const involvedEl = document.getElementById('showAgendaInvolved');
        if (involvedEl) {
            let involvedText = '-';
            if (data.invitations && data.invitations.length > 0) {
                const parts = [];
                data.invitations.forEach((inv, index) => {
                    if (inv.units && inv.units.length > 0) {
                        const unitNames = inv.units.join(', ');
                        // Gunakan session_name jika ada, jika tidak gunakan "Sesi X"
                        const sessionLabel = inv.session_name || `Sesi ${index + 1}`;
                        if (data.invitations.length > 1) {
                            // Jika ada multiple sessions, gunakan format "Nama Sesi= opd, opd"
                            parts.push(`${sessionLabel}= ${unitNames}`);
                        } else {
                            // Jika hanya satu session (normal), tampilkan langsung
                            parts.push(unitNames);
                        }
                    }
                });
                if (parts.length > 0) {
                    involvedText = parts.join('<br>');
                }
            } else if (data.involved_institution) {
                involvedText = data.involved_institution;
            }
            involvedEl.innerHTML = involvedText;
        }

        // Isi status
        const accessEl = document.getElementById('showAgendaAccess');
        if (accessEl) {
            const isPublic = data.is_public == 1 || data.is_public === true;
            const bg = isPublic ? '#A8E6A3' : '#FFB67E';
            const text = isPublic ? 'Publik' : 'Privasi';
            accessEl.innerHTML = `<span class="badge rounded-pill" style="background-color:${bg}; color:#2F3E35; padding:6px 10px;">${text}</span>`;
        }

        // Isi catatan
        const notesEl = document.getElementById('showAgendaNotes');
        if (notesEl) {
            notesEl.textContent = data.notes || '-';
        }
    };

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
        const notes = button.data('notes');

        const modal = $(this);
        modal.find('#showAgendaName').text(name || '-');
        modal.find('#showAgendaDesc').text(desc || '-');
        modal.find('#showAgendaDate').text(date || '-');
        modal.find('#showAgendaTime').text(time || '-');
        modal.find('#showAgendaLocation').text(location || '-');
        modal.find('#showAgendaUnit').text(unit || '-');
        modal.find('#showAgendaInvolved').html(involved || '-');
        modal.find('#showAgendaAccess').text(access || '-');
        modal.find('#showAgendaNotes').text(notes || '-');
    });
});
</script>
