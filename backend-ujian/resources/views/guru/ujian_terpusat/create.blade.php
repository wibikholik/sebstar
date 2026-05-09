<form action="{{ route('guru.ujian-terpusat.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="subject_id" value="{{ $schedule->subject_id }}">
    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">

    <div style="margin-bottom: 15px;">
        <label style="display: block; font-weight: 700; margin-bottom: 5px;">Tipe Soal</label>
        <select name="type" id="add_type" onchange="handleTypeChange('add')" class="search-input" style="width: 100%;" required>
            <option value="pg">Pilihan Ganda (PG)</option>
            <option value="essay">Essay / Uraian</option>
        </select>
    </div>

    <div style="margin-bottom: 15px;">
        <label style="display: block; font-weight: 700; margin-bottom: 5px;">Pertanyaan</label>
        <textarea name="question_text" class="search-input" style="width: 100%; min-height: 80px;" required></textarea>
    </div>

    <div id="addPgContainer">
        <div style="display: grid; gap: 8px; margin-bottom: 15px;">
            @foreach(['a','b','c','d','e'] as $opt)
            <input type="text" name="option_{{ $opt }}" class="search-input" placeholder="Opsi {{ strtoupper($opt) }}">
            @endforeach
        </div>
        <select name="correct_answer_pg" class="search-input" style="width: 100%; border-color: #c91313;">
            @foreach(['A','B','C','D','E'] as $k) <option value="{{ $k }}">Kunci: {{ $k }}</option> @endforeach
        </select>
    </div>

    <div id="addEssayContainer" style="display: none; margin-bottom: 15px;">
        <textarea name="correct_answer_essay" class="search-input" style="width: 100%; min-height: 80px;" placeholder="Pedoman jawaban essay..."></textarea>
    </div>

    <button type="submit" style="width: 100%; padding: 12px; background: #c91313; color: #fff; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; margin-top: 15px;">Simpan Soal</button>
</form>