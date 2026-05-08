@extends('layouts.app')

@section('title', 'Edit Soal')

@section('content')
<div class="content-box" style="max-width: 800px; margin: 0 auto;">
    <h2 style="margin-bottom: 20px;">Edit Soal</h2>

    <form action="{{ route('guru.ujian-terpusat.update', $question->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        
        {{-- Hidden Input untuk Redirect --}}
        <input type="hidden" name="schedule_id" value="{{ request('schedule_id') }}">

        <div style="margin-bottom: 20px;">
            <label style="display:block; font-weight:700; margin-bottom:5px;">Tipe Soal</label>
            <select name="type" id="type" class="search-input" style="width:100%" onchange="toggleType()">
                <option value="pg" {{ $question->type == 'pg' ? 'selected' : '' }}>Pilihan Ganda</option>
                <option value="essay" {{ $question->type == 'essay' ? 'selected' : '' }}>Essay</option>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display:block; font-weight:700; margin-bottom:5px;">Pertanyaan</label>
            <textarea name="question_text" class="search-input" style="width:100%; min-height:100px;" required>{{ $question->question_text }}</textarea>
        </div>

        <div id="section_pg" style="{{ $question->type == 'essay' ? 'display:none' : '' }}">
            <div style="display: grid; gap: 10px; margin-bottom: 20px;">
                @foreach(['a','b','c','d','e'] as $opt)
                <input type="text" name="option_{{ $opt }}" value="{{ $question->{'option_'.$opt} }}" class="search-input" placeholder="Opsi {{ strtoupper($opt) }}">
                @endforeach
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display:block; font-weight:700; color:red;">Kunci Jawaban PG</label>
                <select name="correct_answer_pg" class="search-input" style="width:100%">
                    @foreach(['A','B','C','D','E'] as $k) 
                        <option value="{{ $k }}" {{ $question->correct_answer == $k ? 'selected' : '' }}>{{ $k }}</option> 
                    @endforeach
                </select>
            </div>
        </div>

        <div id="section_essay" style="{{ $question->type == 'pg' ? 'display:none' : '' }}; margin-bottom: 20px;">
            <label style="display:block; font-weight:700;">Pedoman Jawaban Essay</label>
            <textarea name="correct_answer_essay" class="search-input" style="width:100%; min-height:80px;">{{ $question->type == 'essay' ? $question->correct_answer : '' }}</textarea>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn-add" style="flex: 1; padding: 15px;">Update Soal</button>
            <a href="{{ route('guru.ujian-terpusat.manage', request('schedule_id')) }}" class="nav-link" style="background:#eee; margin:0; padding:15px;">Batal</a>
        </div>
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