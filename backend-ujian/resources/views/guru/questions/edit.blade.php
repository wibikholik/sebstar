<form id="formEditSoal" method="POST" enctype="multipart/form-data">
    @csrf 
    @method('PUT')
    
    {{-- Hidden Input untuk tetap di jadwal yang sama --}}
    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
        {{-- Tipe Soal --}}
        <div>
            <label style="display: block; font-weight: 700; margin-bottom: 8px; font-size: 13px; color: #475569;">Tipe Soal</label>
            <select name="type" id="edit_type" onchange="handleTypeChange('edit')" 
                style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #fff;">
                <option value="pg">Pilihan Ganda (PG)</option>
                <option value="essay">Essay / Uraian</option>
            </select>
        </div>

        {{-- Ganti Gambar --}}
        <div>
            <label style="display: block; font-weight: 700; margin-bottom: 8px; font-size: 13px; color: #475569;">Ganti Gambar (Opsional)</label>
            <input type="file" name="question_image" accept="image/*" 
                style="width: 100%; font-size: 12px; color: #64748b;">
            <small style="color: #94a3b8; font-size: 10px;">*Biarkan kosong jika tidak ingin mengubah gambar</small>
        </div>
    </div>

    {{-- Pertanyaan --}}
    <div style="margin-bottom: 15px;">
        <label style="display: block; font-weight: 700; margin-bottom: 8px; font-size: 13px; color: #475569;">Pertanyaan</label>
        <textarea name="question_text" id="edit_question_text" placeholder="Tuliskan pertanyaan di sini..." required 
            style="width: 100%; min-height: 100px; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; font-family: inherit; resize: vertical;"></textarea>
    </div>

    {{-- Kontainer Pilihan Ganda (Edit) --}}
    <div id="editPgContainer" style="background: #f8fafc; padding: 15px; border-radius: 10px; border: 1px solid #e2e8f0; margin-bottom: 15px;">
        <label style="display: block; font-weight: 700; margin-bottom: 10px; font-size: 13px; color: #1e293b;">Opsi Jawaban & Kunci</label>
        <div style="display: grid; gap: 10px; margin-bottom: 15px;">
            @foreach(['a','b','c','d','e'] as $opt)
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-weight: 800; color: #c91313; width: 20px;">{{ strtoupper($opt) }}.</span>
                <input type="text" name="option_{{ $opt }}" id="edit_option_{{ $opt }}" placeholder="Isi jawaban opsi {{ strtoupper($opt) }}" 
                    style="flex: 1; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
            </div>
            @endforeach
        </div>
        
        <div style="border-top: 1px dashed #cbd5e1; margin-top: 15px; padding-top: 10px;">
            <label style="display: block; font-weight: 700; margin-bottom: 5px; font-size: 13px; color: #c91313;">Kunci Jawaban Saat Ini</label>
            <select name="correct_answer_pg" id="edit_correct_answer_pg" style="width: 100%; padding: 10px; border: 2px solid #c91313; border-radius: 8px; font-weight: 700; color: #c91313; background: #fff;">
                @foreach(['A','B','C','D','E'] as $k) 
                    <option value="{{ $k }}">KUNCI JAWABAN: {{ $k }}</option> 
                @endforeach
            </select>
        </div>
    </div>

    {{-- Kontainer Essay (Edit) --}}
    <div id="editEssayContainer" style="display: none; background: #fdf2f2; padding: 15px; border-radius: 10px; border: 1px solid #fca5a5; margin-bottom: 15px;">
        <label style="display: block; font-weight: 700; margin-bottom: 8px; font-size: 13px; color: #b91c1c;">Pedoman / Kunci Jawaban Essay</label>
        <textarea name="correct_answer_essay" id="edit_correct_answer_essay" placeholder="Masukkan ringkasan jawaban benar..." 
            style="width: 100%; min-height: 100px; padding: 12px; border: 1px solid #fca5a5; border-radius: 8px; outline: none; font-family: inherit;"></textarea>
    </div>

    <button type="submit" style="width: 100%; padding: 14px; background: #1e293b; color: #fff; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; margin-top: 10px; transition: 0.2s;">
        🔄 Perbarui Soal
    </button>
</form>