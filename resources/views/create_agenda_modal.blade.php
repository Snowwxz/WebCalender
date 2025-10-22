<!-- Modal Create Agenda -->
<div class="modal-overlay" id="createAgendaModal" style="display: none;">
    <div class="modal-container">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fas fa-calendar-plus"></i>
                <span>Buat Agenda Baru</span>
            </div>
            <button class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="modal-body">
            <form id="agendaForm" action="{{ route('agenda.store') }}" method="POST"
                onsubmit="return validateAgendaForm()">
                @csrf

                <div class="form-grid">
                    <!-- Kolom kiri -->
                    <div class="form-column">
                        <div class="input-group">
                            <label><i class="fas fa-file-alt"></i> Nama Agenda</label>
                            <input type="text" name="agenda_name" id="agenda_name" placeholder="Masukkan nama agenda"
                                required>
                        </div>

                        <div class="input-group">
                            <label><i class="fas fa-align-left"></i> Deskripsi Agenda</label>
                            <textarea name="description" id="description" placeholder="Masukkan deskripsi agenda"
                                required></textarea>
                        </div>

                        <div class="input-group">
                            <label><i class="fas fa-user-tie"></i> Penanggung Jawab</label>
                            <input type="text" name="person_in_charge" id="person_in_charge"
                                placeholder="Masukkan nama penanggung jawab" required>
                        </div>

                        <div class="input-group">
                            <label><i class="fas fa-people-group"></i> Instansi yang Ikut Serta</label>
                            <textarea name="involved_institution" id="involved_institution"
                                placeholder="Masukkan instansi yang akan ikut serta" required></textarea>
                        </div>
                    </div>

                    <!-- Kolom kanan -->
                    <div class="form-column">
                        <div class="input-group">
                            <label><i class="fas fa-calendar-day"></i> Tanggal</label>
                            <input type="date" name="date" id="date" required>
                        </div>

                        <div class="input-group">
                            <label><i class="fas fa-clock"></i> Waktu Mulai</label>
                            <input type="time" name="start_time" id="start_time" required>
                        </div>

                        <div class="input-group">
                            <label><i class="fas fa-clock"></i> Waktu Selesai</label>
                            <input type="time" name="end_time" id="end_time" required>
                        </div>

                        <div class="input-group">
                            <label><i class="fas fa-location-dot"></i> Lokasi</label>
                            <input type="text" name="location" id="location" placeholder="Masukkan lokasi agenda"
                                required>
                        </div>
                    </div>
                </div>

                <div class="form-submit">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-paper-plane"></i> Ajukan Agenda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>