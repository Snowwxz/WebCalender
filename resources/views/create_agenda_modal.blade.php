<div class="fixed inset-0 flex items-center justify-center p-4 z-10" id="createAgendaModal" hidden>
    <div class="fixed inset-0 bg-gray-700 bg-opacity-40" onclick="getel('createAgendaModal').hidden = true;"></div>

    <div class="bg-white rounded-2xl shadow-lg w-full max-w-5xl p-10 overflow-y-auto max-h-[90vh] relative border border-[#dce6df]">
        <!-- Tombol Tutup -->
        <button class="absolute top-5 right-5 text-gray-400 hover:text-gray-700 transition duration-150 ease-in-out p-1 rounded-full hover:bg-gray-100"
            onclick="getel('createAgendaModal').hidden = true;">
            <i class="fas fa-times text-lg"></i>
        </button>

        <!-- Header -->
        <div class="text-center mb-8">
            <h2 class="agenda-title justify-center">
                <i class="fas fa-calendar-plus"></i> Sistem Pengajuan Agenda
            </h2>
            <p class="agenda-subtitle">
                Platform untuk mengajukan dan mengelola agenda kegiatan instansi
            </p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('agenda.store') }}" onsubmit="return validateAgendaForm()">
            @csrf

            <div class="form-grid">
                <!-- Kiri -->
                <div class="form-column">
                    <div class="input-group">
                        <label><i class="fas fa-file-alt"></i> Nama Agenda</label>
                        <input type="text" id="agenda_name" name="agenda_name" placeholder="Masukkan nama agenda" required>
                    </div>

                    <div class="input-group">
                        <label><i class="fas fa-align-left"></i> Deskripsi Agenda</label>
                        <textarea id="description" name="description" placeholder="Masukkan deskripsi agenda" required></textarea>
                    </div>

                    <div class="input-group">
                        <label><i class="fas fa-user-tie"></i> Penanggung Jawab</label>
                        <input type="text" id="person_in_charge" name="person_in_charge" placeholder="Masukkan nama penanggung jawab" required>
                    </div>

                    <div class="input-group">
                        <label><i class="fas fa-people-group"></i> Instansi yang Ikut Serta</label>
                        <textarea id="involved_institution" name="involved_institution" placeholder="Masukkan instansi yang akan ikut serta" required></textarea>
                    </div>
                </div>

                <!-- Kanan -->
                <div class="form-column">
                    <div class="input-group">
                        <label><i class="fas fa-calendar-day"></i> Tanggal</label>
                        <input type="date" id="date" name="date" required>
                    </div>

                    <div class="input-group">
                        <label><i class="fas fa-clock"></i> Waktu Mulai</label>
                        <input type="time" id="start_time" name="start_time" required>
                    </div>

                    <div class="input-group">
                        <label><i class="fas fa-clock"></i> Waktu Selesai</label>
                        <input type="time" id="end_time" name="end_time" required>
                    </div>

                    <div class="input-group">
                        <label><i class="fas fa-location-dot"></i> Lokasi</label>
                        <input type="text" id="location" name="location" placeholder="Masukkan lokasi agenda" required>
                    </div>
                </div>
            </div>

            <!-- Tombol Submit -->
            <div class="form-submit mt-8">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-paper-plane"></i> Ajukan Agenda
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Validasi Form -->
<script>
function validateAgendaForm() {
    const inputs = document.querySelectorAll('#createAgendaModal input[required], #createAgendaModal textarea[required]');
    let valid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = 'red';
            valid = false;
        } else {
            input.style.borderColor = '#d6e0d9';
        }
    });

    if (!valid) {
        alert('Semua wajib diisi!');
    }

    return valid;
}
</script>
