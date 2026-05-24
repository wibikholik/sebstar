{{-- Modal Create Jadwal Ujian Premium SEBSTAR --}}
<div id="createModal" class="modal-custom" style="display: {{ $errors->any() && !session('error_form_type') ? 'block' : 'none' }};">
    <div class="modal-content-custom">
        
        {{-- Header Modal Bertema Gradasi Merah Premium SEBSTAR --}}
        <div class="modal-header-premium">
            <h4 class="modal-title-text">
                <i class="fas fa-calendar-plus"></i> Buat Jadwal Ujian Mandiri Baru
            </h4>
            <span onclick="closeCreateModal()" class="modal-close-btn">&times;</span>
        </div>

        <form action="{{ route('guru.schedules.store') }}" method="POST" class="modal-form-body">
            @csrf
            
            {{-- Input Jenis Ujian --}}
            <div class="form-group-premium">
                <label class="form-label-premium">Jenis / Tipe Ujian</label>
                <select name="exam_type_id" required class="form-select-premium @error('exam_type_id') is-invalid-premium @enderror">
                    <option value="" hidden>-- Pilih Jenis --</option>
                    @foreach($examTypes as $type)
                        <option value="{{ $type->id }}" {{ old('exam_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
                @error('exam_type_id')
                    <span class="invalid-feedback-premium">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </span>
                @enderror
            </div>

            {{-- Input Mata Pelajaran --}}
            <div class="form-group-premium">
                <label class="form-label-premium">Mata Pelajaran (Tugas Mengajar Anda)</label>
                <select name="subject_id" required class="form-select-premium readonly-style @error('subject_id') is-invalid-premium @enderror">
                    @forelse($mySubjects as $sub)
                        <option value="{{ $sub->id }}" {{ old('subject_id') == $sub->id ? 'selected' : '' }}>{{ $sub->nama_mapel }}</option>
                    @empty
                        <option value="" disabled selected>Mapel belum ditugaskan (Hubungi Admin)</option>
                    @endforelse
                </select>
                @error('subject_id')
                    <span class="invalid-feedback-premium">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </span>
                @enderror
            </div>

            {{-- Input Pilihan Rombel Kelas --}}
            <div class="form-group-premium">
                <label class="form-label-premium">Target Rombel Kelas</label>
                <select name="classroom_id" required class="form-select-premium @error('classroom_id') is-invalid-premium @enderror">
                    <option value="" hidden>-- Pilih Kelas Peserta --</option>
                    @foreach($classrooms as $cls)
                        <option value="{{ $cls->id }}" {{ old('classroom_id') == $cls->id ? 'selected' : '' }}>{{ $cls->nama_kelas }}</option>
                    @endforeach
                </select>
                @error('classroom_id')
                    <span class="invalid-feedback-premium">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </span>
                @enderror
            </div>

            {{-- Grid Row: Tanggal Pelaksanaan & Durasi Menit --}}
            <div class="form-row-premium">
                <div class="form-col-premium">
                    <label class="form-label-premium">Tanggal Ujian</label>
                    {{-- DIKUNCI: Menggunakan atribut min hari ini agar tidak bisa backdate --}}
                    <input type="date" name="tanggal_ujian" value="{{ old('tanggal_ujian', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required class="form-input-premium @error('tanggal_ujian') is-invalid-premium @enderror">
                    @error('tanggal_ujian')
                        <span class="invalid-feedback-premium">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="form-col-premium">
                    <label class="form-label-premium">Durasi (Menit)</label>
                    <input type="number" name="durasi" placeholder="60" min="5" value="{{ old('durasi', 60) }}" required class="form-input-premium @error('durasi') is-invalid-premium @enderror">
                    @error('durasi')
                        <span class="invalid-feedback-premium">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            {{-- Action Buttons Bertema SEBSTAR Premium --}}
            <div class="modal-actions-premium">
                <button type="button" onclick="closeCreateModal()" class="btn-cancel-premium">Batal</button>
                <button type="submit" @disabled($mySubjects->isEmpty()) class="btn-submit-premium">
                    <i class="fas fa-save"></i> Daftarkan Sesi Ujian
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('createModal').style.display = 'block';
    }
    
    function closeCreateModal() {
        document.getElementById('createModal').style.display = 'none';
    }

    window.addEventListener('click', function(event) {
        const createModal = document.getElementById('createModal');
        if (event.target === createModal) {
            closeCreateModal();
        }
    });
</script>

<style>
    /* Backdrop Modal Efek Blur */
    .modal-custom {
        position: fixed !important;
        z-index: 2000 !important;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(30, 30, 47, 0.4) !important;
        backdrop-filter: blur(4px) !important;
    }

    /* Wadah Konten Utama Modal */
    .modal-content-custom {
        background-color: #ffffff !important;
        margin: 5% auto !important;
        padding: 0 !important;
        border-radius: 16px !important;
        width: 100% !important;
        max-width: 540px !important;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
        overflow: hidden !important;
        border: 1px solid rgba(0,0,0,0.05) !important;
        animation: modalSlideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) ease-out;
    }

    /* Header Bergradasi Merah */
    .modal-header-premium {
        background: linear-gradient(135deg, #cd0000 0%, #950000 100%) !important;
        color: #ffffff !important;
        padding: 18px 24px !important;
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
    }

    .modal-title-text {
        margin: 0 !important;
        font-weight: 700 !important;
        font-size: 15px !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        color: #ffffff !important;
    }

    .modal-close-btn {
        cursor: pointer !important;
        font-size: 24px !important;
        line-height: 1 !important;
        font-weight: 300 !important;
        color: #ffffff !important;
        opacity: 0.8 !important;
        transition: all 0.2s !important;
    }

    .modal-close-btn:hover {
        opacity: 0.5 !important;
    }

    /* Form Body */
    .modal-form-body {
        padding: 24px !important;
    }

    .form-group-premium {
        margin-bottom: 16px !important;
    }

    .form-label-premium {
        display: block !important;
        margin-bottom: 6px !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        color: #1e1e2f !important;
    }

    /* Elemen Input & Select Custom */
    .form-select-premium,
    .form-input-premium {
        width: 100% !important;
        padding: 11px 16px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 10px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        color: #1e1e2f !important;
        background: #ffffff !important;
        outline: none !important;
        transition: all 0.2s ease !important;
        box-sizing: border-box !important;
    }

    .form-select-premium:focus,
    .form-input-premium:focus {
        border-color: #cd0000 !important;
        box-shadow: 0 0 0 3px rgba(205, 0, 0, 0.1) !important;
    }

    .readonly-style {
        background: #f8fafc !important;
        cursor: not-allowed !important;
    }

    /* Row & Col untuk Tanggal & Durasi */
    .form-row-premium {
        display: flex !important;
        gap: 16px !important;
        margin-bottom: 25px !important;
    }

    .form-col-premium {
        flex: 1 !important;
    }

    /* Validasi Error */
    .is-invalid-premium {
        border-color: #cd0000 !important;
        background-color: rgba(230, 57, 70, 0.02) !important;
    }

    .invalid-feedback-premium {
        color: #cd0000 !important;
        font-size: 11px !important;
        display: block !important;
        margin-top: 5px !important;
        font-weight: 700 !important;
    }

    /* Tombol Aksi */
    .modal-actions-premium {
        display: flex !important;
        gap: 12px !important;
    }

    .btn-cancel-premium {
        flex: 1 !important;
        padding: 12px !important;
        border-radius: 30px !important;
        border: 1px solid #edf0f5 !important;
        background: #f1f5f9 !important;
        color: #475569 !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        cursor: pointer !important;
        transition: all 0.2s !important;
    }

    .btn-cancel-premium:hover {
        background: #e2e8f0 !important;
    }

    .btn-submit-premium {
        flex: 2 !important;
        padding: 12px !important;
        border-radius: 30px !important;
        border: none !important;
        background: linear-gradient(135deg, #cd0000 0%, #950000 100%) !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        cursor: pointer !important;
        box-shadow: 0 5px 15px rgba(205, 0, 0, 0.25) !important;
        transition: all 0.2s ease !important;
    }

    .btn-submit-premium:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 20px rgba(205, 0, 0, 0.35) !important;
        filter: brightness(1.1) !important;
    }

    .btn-submit-premium:disabled {
        background: #cbd5e1 !important;
        box-shadow: none !important;
        cursor: not-allowed !important;
        transform: none !important;
    }

    /* Animasi Lembut Saat Muncul */
    @keyframes modalSlideIn {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>