<div class="modal-overlay" id="createAgendaModalHari" style="display: none;">
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
            <form id="createAgendaFormHari" action="{{ route('agenda.store') }}" method="POST"
                onsubmit="return handleFormSubmit(event)">
                @csrf

                <!-- Page 1: Basic Agenda Info -->
                <div class="form-page" id="page1Hari">
                    <div class="form-grid">
                        <!-- Full Width Fields -->
                        <div class="input-group fullwidth-group">
                            <label><i class="fas fa-file-alt"></i> Nama Agenda</label>
                            <input type="text" name="agenda_name" id="agenda_name"
                                placeholder="Masukkan nama agenda" required>
                        </div>

                        <div class="input-group fullwidth-group">
                            <label><i class="fas fa-align-left"></i> Deskripsi Agenda</label>
                            <textarea name="description" id="description" placeholder="Masukkan deskripsi agenda" required></textarea>
                        </div>

                        <!-- Kolom kiri -->
                        <div class="form-column">
                            <div class="input-group">
                                <label><i class="fas fa-building"></i> Pelaksana</label>
                                <input type="text" value="{{ Auth::user()->unit->unit_name ?? '-' }}" readonly>
                                <input type="hidden" name="id_unit" value="{{ Auth::user()->id_unit }}">
                            </div>

                            <div class="input-group">
                                <label><i class="fas fa-eye"></i> Kategori Agenda</label>
                                <select name="is_public" id="is_public">
                                    <option value="1">Publik</option>
                                    <option value="0">Privasi</option>
                                </select>
                            </div>

                            <div class="input-group lokasi-group">
                                <label for="lokasi"><i class="fas fa-map-marker-alt"></i> Lokasi</label>
                                <input type="text" id="location" name="location" placeholder="Masukkan lokasi kegiatan">
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
                        </div>

                        <!-- Catatan - Full Width -->
                        <div class="input-group fullwidth-group">
                            <label><i class="fa-solid fa-file-lines" style="color:#6b8f71;"></i> Catatan</label>
                            <textarea name="notes" id="notes" placeholder="Masukkan catatan tambahan (opsional)" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Page 2: Session Management -->
                <div class="form-page" id="page2Hari" style="display: none;">
                    <!-- Toggle Group -->
                    <div class="input-group fullwidth-group" style="margin-bottom: 24px;">
                        <label style="margin-bottom: 12px; display: block;"><i class="fas fa-layer-group"></i> Mode Agenda</label>
                        <div style="display: flex; gap: 16px;">
                            <label class="radio-option" style="display: flex; align-items: center; cursor: pointer; padding: 12px 20px; border: 2px solid #e5e7eb; border-radius: 8px; transition: all 0.3s;">
                                <input type="radio" name="has_group" value="0" id="noGroupHari" checked style="margin-right: 10px; width: 18px; height: 18px; cursor: pointer;">
                                <span>Agenda Normal</span>
                            </label>
                            <label class="radio-option" style="display: flex; align-items: center; cursor: pointer; padding: 12px 20px; border: 2px solid #e5e7eb; border-radius: 8px; transition: all 0.3s;">
                                <input type="radio" name="has_group" value="1" id="withGroupHari" style="margin-right: 10px; width: 18px; height: 18px; cursor: pointer;">
                                <span>Agenda dengan Sesi (Group)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Container untuk Dihadiri Normal -->
                    <div id="normalInvitationHari" class="invitation-container">
                        <div class="input-group fullwidth-group">
                            <label><i class="fas fa-people-group"></i> Dihadiri</label>
                            <div class="chips-multiselect" id="normalInvolvedInstansiHari">
                                <div class="chips-container">
                                    <div class="chips-selected"></div>
                                    <input type="text" class="chips-input" placeholder="-- Pilih Instansi yang Hadir --"
                                        readonly style="cursor: pointer;">
                                </div>
                                <span class="chips-arrow"><i class="fas fa-chevron-down"></i></span>
                                <div class="chips-dropdown">
                                    <div class="chips-search">
                                        <input type="text" class="chips-search-input" placeholder="Cari instansi..." />
                                    </div>
                                    <ul>
                                        <li class="select-all-option" data-action="select-all">
                                            <span class="check-icon"></span>
                                            <span class="item-text">Pilih Semua</span>
                                            <i class="fas fa-check checkmark-icon"></i>
                                        </li>
                                        @foreach ($units as $unit)
                                            @if ($unit->id_unit !== Auth::user()->id_unit && strtolower($unit->unit_name) !== 'protokol')
                                                <li data-value="{{ $unit->id_unit }}" data-name="{{ $unit->unit_name }}" class="dropdown-item">
                                                    <span class="check-icon"></span>
                                                    <span class="item-text">{{ $unit->unit_name }}</span>
                                                    <i class="fas fa-check checkmark-icon"></i>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Container untuk Dihadiri dengan Sesi -->
                    <div id="groupInvitationHari" class="invitation-container" style="display: none;">
                        <div id="sessionsContainerHari">
                            <!-- Session akan ditambahkan secara dinamis -->
                        </div>
                        <button type="button" id="addSessionBtnHari" class="btn-secondary" style="margin-top: 16px; display: none;">
                            <i class="fas fa-plus"></i> Tambah Sesi
                        </button>
                    </div>
                </div>

                <!-- Navigation & Submit -->
                <div class="form-navigation">
                    <button type="button" id="prevPageBtnHari" class="btn-secondary" style="display: none;">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </button>
                    <div style="flex: 1;"></div>
                    <button type="button" id="nextPageBtnHari" class="btn-primary">
                        Lanjutkan <i class="fas fa-arrow-right"></i>
                    </button>
                    <button type="submit" id="submitBtnHari" class="btn-primary" style="display: none;">
                        Ajukan Agenda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
