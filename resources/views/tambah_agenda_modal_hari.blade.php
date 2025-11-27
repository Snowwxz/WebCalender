<div class="modal-overlay" id="createAgendaModalHari" style="display: none;">
    <div class="modal-content">
        <h2 class="modal-title">Tambah Agenda</h2>

        <form id="createAgendaFormHari" action="{{ route('agenda.store') }}" method="POST">
            @csrf

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

                <!-- Dihadiri - Full Width -->
                <div class="input-group fullwidth-group">
                    <label><span class="label-icon-dihadiri"></span> Dihadiri</label>

                    <div class="chips-multiselect" id="involvedInstansiHari">
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

                            <div class="chips-add-new-input-container" style="display: none; padding: 10px 12px; border-top: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                                <input type="text" class="chips-add-new-input" placeholder="Ketik nama instansi baru..." style="width: 100%; padding: 8px 12px; border: 1px solid #6b8f71; border-radius: 6px; font-size: 14px; outline: none;" />
                                <div style="display: flex; gap: 8px; margin-top: 8px;">
                                    <button type="button" class="chips-add-confirm-btn" style="flex: 1; padding: 6px 12px; background: #6b8f71; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 500;">Tambahkan</button>
                                    <button type="button" class="chips-add-cancel-btn" style="flex: 1; padding: 6px 12px; background: #e5e7eb; color: #374151; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 500;">Batal</button>
                                </div>
                            </div>

                            <ul>
                                <li class="select-all-option" data-action="select-all">
                                    <span class="check-icon"></span>
                                    <span class="item-text">Pilih Semua</span>
                                    <i class="fas fa-check checkmark-icon"></i>
                                </li>
                                @foreach ($units as $unit)
                                    @if ($unit->id_unit !== Auth::user()->id_unit && strtolower($unit->unit_name) !== 'protokol')
                                        <li data-value="{{ $unit->unit_name }}" class="dropdown-item">
                                            <span class="check-icon"></span>
                                            <span class="item-text">{{ $unit->unit_name }}</span>
                                            <i class="fas fa-check checkmark-icon"></i>
                                        </li>
                                    @endif
                                @endforeach
                                <li class="add-new-instansi-option" data-action="add-new" style="padding: 10px 12px; cursor: pointer; border-top: 1px solid #e5e7eb; color: #6b8f71; font-weight: 500; display: flex; align-items: center; list-style: none;">
                                    <i class="fas fa-plus-circle" style="margin-right: 8px;"></i>
                                    <span>Lainnya...</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <input type="hidden" name="involved_institution" id="involvedInstitutionFieldHari" value="">
                </div>

                <!-- Catatan - Full Width -->
                <div class="input-group fullwidth-group">
                    <label><i class="fa-solid fa-file-lines" style="color:#6b8f71;"></i> Catatan</label>
                    <textarea name="notes" id="notes" placeholder="Masukkan catatan tambahan (opsional)" rows="3"></textarea>
                </div>
            </div>

            <div class="form-submit">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-paper-plane"></i> Ajukan Agenda
                </button>
            </div>
        </form>
    </div>
</div>
