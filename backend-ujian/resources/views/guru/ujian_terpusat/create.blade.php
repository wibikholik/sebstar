@extends('layouts.app')

@section('title', 'Input Soal Baru')

@section('content')
<div class="content-box" style="max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 25px;">
        <h2 style="margin: 0;">Tambah Soal Baru</h2>
        <p style="color: var(--text-gray); font-size: 14px; margin-top: 5px;">Silakan lengkapi detail pertanyaan di bawah ini.</p>
    </div>

    <form action="{{ route('guru.ujian-terpusat.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <input type="hidden" name="subject_id" value="{{ request('subject_id') }}">
        <input type="hidden" name="schedule_id" value="{{ request('schedule_id') }}">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
            <div>
                <label style="display: block; font-weight: 700; margin-bottom: 10px;">Tipe Soal</label>
                <select name="type" id="type" class="search-input" style="width: 100%;" onchange="toggleType()">
                    <option value="pg">Pilihan Ganda (PG)</option>
                    <option value="essay">Essay / Uraian</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-weight: 700; margin-bottom: 10px;">Lampiran Gambar (Opsional)</label>
                <input type="file" name="question_image" class="search-input" style="width: 100%; padding: 8px;" accept="image/*">
                <small style="color: var(--text-gray); font-size: 11px;">Maksimal 2MB (JPG, PNG, JPEG)</small>
            </div>
        </div>

        <div style="margin-bottom: 25px;">
            <label style="display: block; font-weight: 700; margin-bottom: 10px;">Pertanyaan</label>
            <textarea name="question_text" class="search-input" style="width: 100%; min-height: 150px; line-height: 1.6;" placeholder="Tuliskan butir soal di sini..." required></textarea>
        </div>

        <div id="section_pg">
            <label style="display: block; font-weight: 700; margin-bottom: 15px; border-bottom: 2px solid var(--bg-gray); padding-bottom: 10px;">Opsi Jawaban</label>
            
            <div style="display: grid; grid-template-columns: 1fr; gap: 15px; margin-bottom: 25px;">
                @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="width: 40px; height: 40px; background: var(--bg-gray); display: flex; align-items: center; justify-content: center; border-radius: 8px; font-weight: 800; color: var(--text-dark);">
                        {{ strtoupper($opt) }}
                    </div>
                    <input type="text" name="option_{{ $opt }}" class="search-input" style="flex: 1;" placeholder="Isi pilihan {{ strtoupper($opt) }}">
                </div>
                @endforeach
            </div>

            <div style="margin-bottom: 25px; background: #fff1f1; padding: 20px; border-radius: 12px; border: 1px dashed var(--red-sebstar);">
                <label style="display: block; font-weight: 700; margin-bottom: 10px; color: var(--red-sebstar);">Kunci Jawaban Benar</label>
                <select name="correct_answer_pg" class="search-input" style="width: 100%; border-color: #ffcccc;">
                    @foreach(['A','B','C','D','E'] as $key)
                        <option value="{{ $key }}">Pilihan {{ $key }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div id="section_essay" style="display: none; margin-bottom: 25px;">
            <label style="display: block; font-weight: 700; margin-bottom: 10px; color: #7b1fa2;">Pedoman Penskoran / Jawaban</label>
            <textarea name="correct_answer_essay" class="search-input" style="width: 100%; min-height: 120px;" placeholder="Tuliskan poin-poin jawaban atau kunci essay sebagai referensi koreksi..."></textarea>
        </div>

        <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 30px 0;">

        <div style="display: flex; gap: 15px; justify-content: flex-end;">
            <a href="{{ route('guru.ujian-terpusat.manage', request('schedule_id')) }}" class="nav-link" style="background: #eee; margin: 0; padding: 12px 25px;">Batal</a>
            <button type="submit" class="btn-add" style="padding: 12px 30px;">Simpan Soal</button>
        </div>
    </form>
</div>

<script>
    function toggleType() {
        const type = document.getElementById('type').value;
        const pg = document.getElementById('section_pg');
        const essay = document.getElementById('section_essay');

        if (type === 'pg') {
            pg.style.display = 'block';
            essay.style.display = 'none';
        } else {
            pg.style.display = 'none';
            essay.style.display = 'block';
        }
    }
</script>
@endsection