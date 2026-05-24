{{-- Modal Edit Jadwal Ujian Premium SEBSTAR --}}
<div id="editModal" class="modal-custom" style="display: {{ $errors->any() && session('error_form_type') === 'edit' ? 'block' : 'none' }};">
    <div class="modal-content-custom">
        
        {{-- Header Modal Bertema Gradasi Merah Premium SEBSTAR --}}
        <div class="modal-header-premium">
            <h4 class="modal-title-text">
                <i class="fas fa-calendar-edit"></i> Edit Jadwal Ujian Mandiri
            </h4>
            <span onclick="closeEditModal()" class="modal-close-btn">&times;</span>
        </div>

        <form id="editForm" method="POST" class="modal-form-body">
            @csrf
            @method('PUT')
            
            {{-- Input Edit Jenis Ujian --}}
            <div class="form-group-premium">
                <label class="form-label-premium">Jenis / Tipe Ujian</label>
                <select name="exam_type_id" id="edit_exam_type_id" required class="form-select-premium @error('exam_type_id') is-invalid-premium @enderror">
                    @foreach($examTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
                @error('exam_type_id')
                    <span class="invalid-feedback-premium">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </span>
                @enderror
            </div>

            {{-- Input Edit Target Rombel Kelas --}}
            <div class="form-group-premium">
                <label class="form-label-premium">Target Rombel Kelas</label>
                <select name="classroom_id" id="edit_classroom_id" required class="form-select-premium @error('classroom_id') is-invalid-premium @enderror">
                    @foreach($classrooms as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->nama_kelas }}</option>
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
                    {{-- DIKUNCI: Ditambahkan atribut min agar data lama yang diedit tidak bisa diubah mundur ke belakang --}}
                    <input type="date" name="tanggal_ujian" id="edit_tanggal_ujian" value="{{ old('tanggal_ujian') }}" min="{{ date('Y-m-d') }}" required class="form-input-premium @error('tanggal_ujian') is-invalid-premium @enderror">
                    @error('tanggal_ujian')
                        <span class="invalid-feedback-premium">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="form-col-premium">
                    <label class="form-label-premium">Durasi (Menit)</label>
                    <input type="number" name="durasi" id="edit_durasi" min="5" value="{{ old('durasi') }}" required class="form-input-premium @error('durasi') is-invalid-premium @enderror">
                    @error('durasi')
                        <span class="invalid-feedback-premium">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            {{-- Action Buttons Bertema SEBSTAR Premium --}}
            <div class="modal-actions-premium">
                <button type="button" onclick="closeEditModal()" class="btn-cancel-premium">Batal</button>
                <button type="submit" class="btn-submit-premium">
                    <i class="fas fa-check-circle"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    /**
     * Fungsi Utama: Dipanggil dari tag onclick tombol edit di halaman index utama
     */
    function openEditModal(schedule) {
        const modal = document.getElementById('editModal');
        const form = document.getElementById('editForm');
        
        // Mengarahkan route URL action secara dinamis sesuai ID jadwal ujian mandiri terpilih
        form.action = "{{ url('guru/schedules') }}/" + schedule.id;
        
        // Memulihkan data ketikan lama (old input) jika ditolak validasi, atau pasang nilai asli database
        document.getElementById('edit_exam_type_id').value = "{{ old('exam_type_id') }}" || schedule.exam_type_id;
        document.getElementById('edit_classroom_id').value = "{{ old('classroom_id') }}" || schedule.classroom_id;
        document.getElementById('edit_tanggal_ujian').value = "{{ old('tanggal_ujian') }}" || schedule.tanggal_ujian;
        document.getElementById('edit_durasi').value = "{{ old('durasi') }}" || schedule.durasi;
        
        modal.style.display = 'block';
    }

    // Menutup modal secara intuitif apabila guru mengklik area transparan di luar kotak putih form
    window.addEventListener('click', function(event) {
        const editModal = document.getElementById('editModal');
        if (event.target === editModal) {
            closeEditModal();
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

    /* Animasi Lembut Saat Muncul */
    @keyframes modalSlideIn {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>