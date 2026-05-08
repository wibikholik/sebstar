@extends('layouts.app')

@section('title', 'Tambah Soal')

@section('content')
<div class="content-box" style="max-width: 800px; margin: 0 auto;">
    <h2 style="margin-bottom: 20px;">Tambah Soal Baru</h2>

    <form action="{{ route('guru.ujian-terpusat.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        {{-- Hidden Input Penting --}}
        <input type="hidden" name="subject_id" value="{{ request('subject_id') }}">
        <input type="hidden" name="schedule_id" value="{{ request('schedule_id') }}">

        <div style="margin-bottom: 20px;">
            <label style="display:block; font-weight:700; margin-bottom:5px;">Tipe Soal</label>
            <select name="type" id="type" class="search-input" style="width:100%" onchange="toggleType()">
                <option value="pg">Pilihan Ganda (PG)</option>
                <option value="essay">Essay</option>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display:block; font-weight:700; margin-bottom:5px;">Pertanyaan</label>
            <textarea name="question_text" class="search-input" style="width:100%; min-height:100px;" required></textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display:block; font-weight:700; margin-bottom:5px;">Gambar (Opsional)</label>
            <input type="file" name="question_image" class="search-input" style="width:100%">
        </div>

        <div id="section_pg">
            <div style="display: grid; gap: 10px; margin-bottom: 20px;">
                @foreach(['a','b','c','d','e'] as $opt)
                <input type="text" name="option_{{ $opt }}" class="search-input" placeholder="Opsi {{ strtoupper($opt) }}">
                @endforeach
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display:block; font-weight:700; color:red;">Kunci Jawaban</label>
                <select name="correct_answer_pg" class="search-input" style="width:100%">
                    @foreach(['A','B','C','D','E'] as $k) <option value="{{ $k }}">{{ $k }}</option> @endforeach
                </select>
            </div>
        </div>

        <div id="section_essay" style="display:none; margin-bottom: 20px;">
            <label style="display:block; font-weight:700;">Pedoman Jawaban Essay</label>
            <textarea name="correct_answer_essay" class="search-input" style="width:100%; min-height:80px;"></textarea>
        </div>

        <button type="submit" class="btn-add" style="width:100%; padding:15px;">Simpan Soal</button>
    </form>
</div>

<script>
    function toggleType() {
        const type = document.getElementById('type').value;
        document.getElementById('section_pg').style.display = type === 'pg' ? 'block' : 'none';
        document.getElementById('section_essay').style.display = type === 'essay' ? 'block' : 'none';
    }
</script>
@endsection