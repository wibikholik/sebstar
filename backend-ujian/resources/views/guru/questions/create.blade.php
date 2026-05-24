{{-- Form Pembuatan Soal Premium SEBSTAR --}}
<form action="{{ route('guru.questions.store') }}" method="POST" enctype="multipart/form-data" class="form-question-premium">
    @csrf
    {{-- Hidden Input untuk relasi --}}
    <input type="hidden" name="subject_id" value="{{ $schedule->subject_id }}">
    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">

    <div class="form-grid-two">
        {{-- Tipe Soal --}}
        <div class="form-group-premium">
            <label class="form-label-premium">Tipe Soal</label>
            <select name="type" id="add_type" onchange="handleTypeChange('add')" class="form-select-premium">
                <option value="pg">Pilihan Ganda (PG)</option>
                <option value="essay">Essay / Uraian</option>
            </select>
        </div>

        {{-- Upload Gambar (Tambahan) --}}
        <div class="form-group-premium">
            <label class="form-label-premium">Gambar (Opsional)</label>
            <div class="file-input-wrapper">
                <input type="file" name="question_image" accept="image/*" class="form-file-premium">
            </div>
        </div>
    </div>

    {{-- Pertanyaan --}}
    <div class="form-group-premium">
        <label class="form-label-premium">Pertanyaan</label>
        <textarea name="question_text" placeholder="Tuliskan pertanyaan di sini..." required class="form-textarea-premium"></textarea>
    </div>

    {{-- Kontainer Pilihan Ganda --}}
    <div id="addPgContainer" class="pg-options-container">
        <label class="container-title-premium">Opsi Jawaban & Kunci</label>
        <div class="options-grid-premium">
            @foreach(['a','b','c','d','e'] as $opt)
            <div class="option-row-premium">
                <span class="option-letter-premium">{{ strtoupper($opt) }}.</span>
                <input type="text" name="option_{{ $opt }}" id="add_option_{{ $opt }}" placeholder="Isi jawaban opsi {{ strtoupper($opt) }}" required class="form-input-premium">
            </div>
            @endforeach
        </div>
        
        <div class="correct-answer-divider">
            <label class="form-label-premium color-accent">Pilih Kunci Jawaban Benar</label>
            <select name="correct_answer_pg" id="add_correct_answer_pg" class="form-select-premium border-accent">
                @foreach(['A','B','C','D','E'] as $k) 
                    <option value="{{ $k }}">KUNCI JAWABAN: {{ $k }}</option> 
                @endforeach
            </select>
        </div>
    </div>

    {{-- Kontainer Essay --}}
    <div id="addEssayContainer" class="essay-options-container" style="display: none;">
        <label class="container-title-premium color-accent">Pedoman / Kunci Jawaban Essay</label>
        <textarea name="correct_answer_essay" id="add_correct_answer_essay" placeholder="Masukkan ringkasan jawaban benar atau kriteria penilaian..." class="form-textarea-premium border-danger"></textarea>
    </div>

    <button type="submit" class="btn-submit-question">
        <i class="fas fa-save"></i> Simpan Soal
    </button>
</form>

<script>
    /**
     * Mengatur visibilitas kontainer input berdasarkan tipe soal yang dipilih
     */
    function handleTypeChange(prefix) {
        const typeSelect = document.getElementById(`${prefix}_type`);
        const pgContainer = document.getElementById(`${prefix}_PgContainer`);
        const essayContainer = document.getElementById(`${prefix}_EssayContainer`);
        
        // Elemen Input di dalam masing-masing kontainer
        const essayTextarea = document.getElementById(`${prefix}_correct_answer_essay`);
        const pgSelect = document.getElementById(`${prefix}_correct_answer_pg`);
        const pgOptions = ['a', 'b', 'c', 'd', 'e'];

        if (typeSelect.value === 'pg') {
            // Tampilkan PG, Sembunyikan Essay
            pgContainer.style.display = 'block';
            essayContainer.style.display = 'none';
            
            // Atur status required agar validasi HTML5 akurat
            essayTextarea.removeAttribute('required');
            pgSelect.setAttribute('required', 'required');
            pgOptions.forEach(opt => {
                const input = document.getElementById(`${prefix}_option_${opt}`);
                if(input) input.setAttribute('required', 'required');
            });
        } else {
            // Tampilkan Essay, Sembunyikan PG
            pgContainer.style.display = 'none';
            essayContainer.style.display = 'block';
            
            // Atur status required agar validasi HTML5 akurat
            essayTextarea.setAttribute('required', 'required');
            pgSelect.removeAttribute('required');
            pgOptions.forEach(opt => {
                const input = document.getElementById(`${prefix}_option_${opt}`);
                if(input) input.removeAttribute('required');
            });
        }
    }

    // Jalankan inisialisasi awal saat halaman pertama kali dimuat
    document.addEventListener('DOMContentLoaded', function() {
        handleTypeChange('add');
    });
</script>

<style>
    /* Dasar Form */
    .form-question-premium {
        background: #ffffff;
    }

    .form-grid-two {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 16px !important;
        margin-bottom: 16px !important;
    }

    .form-group-premium {
        margin-bottom: 16px !important;
    }

    .form-label-premium {
        display: block !important;
        margin-bottom: 8px !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        color: #475569 !important;
    }

    .form-label-premium.color-accent {
        color: #cd0000 !important;
    }

    /* Elemen Input Standar SEBSTAR */
    .form-select-premium,
    .form-input-premium,
    .form-textarea-premium {
        width: 100% !important;
        padding: 11px 14px !important;
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

    .form-textarea-premium {
        min-height: 100px !important;
        font-family: inherit !important;
        resize: vertical !important;
    }

    .form-select-premium:focus,
    .form-input-premium:focus,
    .form-textarea-premium:focus {
        border-color: #cd0000 !important;
        box-shadow: 0 0 0 3px rgba(205, 0, 0, 0.1) !important;
    }

    /* Kustomisasi Khusus File Input */
    .file-input-wrapper {
        padding: 6px 0 !important;
    }

    .form-file-premium {
        width: 100% !important;
        font-size: 13px !important;
        color: #64748b !important;
        font-weight: 600 !important;
    }

    /* Kotak Opsi Pilihan Ganda (Slate Premium) */
    .pg-options-container {
        background: #f8fafc !important;
        padding: 18px !important;
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        margin-bottom: 18px !important;
    }

    .container-title-premium {
        display: block !important;
        font-weight: 800 !important;
        margin-bottom: 12px !important;
        font-size: 13px !important;
        color: #1e293b !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .options-grid-premium {
        display: grid !important;
        gap: 12px !important;
    }

    .option-row-premium {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
    }

    .option-letter-premium {
        font-weight: 800 !important;
        color: #cd0000 !important;
        width: 24px !important;
        font-size: 14px !important;
    }

    .correct-answer-divider {
        border-top: 1px dashed #cbd5e1 !important;
        margin-top: 16px !important;
        padding-top: 14px !important;
    }

    .form-select-premium.border-accent {
        border: 2px solid #cd0000 !important;
        color: #cd0000 !important;
        font-weight: 700 !important;
    }

    /* Kotak Opsi Essay (Soft Red Premium) */
    .essay-options-container {
        background: #fff5f5 !important;
        padding: 18px !important;
        border-radius: 12px !important;
        border: 1px solid #fee2e2 !important;
        margin-bottom: 18px !important;
    }

    .container-title-premium.color-accent {
        color: #950000 !important;
    }

    .form-textarea-premium.border-danger {
        border: 1px solid #fca5a5 !important;
    }

    .form-textarea-premium.border-danger:focus {
        border-color: #cd0000 !important;
        box-shadow: 0 0 0 3px rgba(205, 0, 0, 0.1) !important;
    }

    /* Tombol Submit */
    .btn-submit-question {
        width: 100% !important;
        padding: 14px !important;
        background: linear-gradient(135deg, #cd0000 0%, #950000 100%) !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 12px !important;
        font-weight: 700 !important;
        font-size: 14px !important;
        cursor: pointer !important;
        margin-top: 10px !important;
        box-shadow: 0 5px 15px rgba(205, 0, 0, 0.25) !important;
        transition: all 0.2s ease !important;
    }

    .btn-submit-question:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 7px 20px rgba(205, 0, 0, 0.35) !important;
        filter: brightness(1.05) !important;
    }
</style>