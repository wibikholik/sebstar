@extends('layouts.app')

@section('title', 'Edit Soal')

@section('content')
<div class="content-box" style="max-width: 900px; margin: 0 auto;">
    
    @if ($errors->any())
        <div style="background: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <ul style="margin: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div style="background: #dcfce7; color: #15803d; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('guru.ujian-terpusat.update', $question->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT') {{-- Pastikan ini tertulis dengan benar --}}
        <input type="hidden" name="schedule_id" value="{{ request('schedule_id') }}">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
            <div>
                <label style="display: block; font-weight: 700; margin-bottom: 10px;">Tipe Soal</label>
                <select name="type" id="type" class="search-input" style="width: 100%;" onchange="toggleType()">
                    <option value="pg" {{ $question->type == 'pg' ? 'selected' : '' }}>Pilihan Ganda</option>
                    <option value="essay" {{ $question->type == 'essay' ? 'selected' : '' }}>Essay</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-weight: 700; margin-bottom: 10px;">Ganti Gambar</label>
                <input type="file" name="question_image" class="search-input" style="width: 100%; padding: 8px;">
            </div>
        </div>

        <div style="margin-bottom: 25px;">
            <label style="display: block; font-weight: 700; margin-bottom: 10px;">Pertanyaan</label>
            <textarea name="question_text" class="search-input" style="width: 100%; min-height: 120px;" required>{{ old('question_text', $question->question_text) }}</textarea>
        </div>

        {{-- Seksi PG --}}
        <div id="section_pg" style="{{ $question->type == 'essay' ? 'display:none' : '' }}">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
                @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-weight: 700;">{{ strtoupper($opt) }}.</span>
                    <input type="text" name="option_{{ $opt }}" value="{{ old('option_'.$opt, $question->{'option_'.$opt}) }}" class="search-input" style="flex: 1;">
                </div>
                @endforeach
                <div>
                    <label style="font-weight: 700; color: var(--red-sebstar);">Kunci PG</label>
                    <select name="correct_answer_pg" class="search-input" style="width: 100%;">
                        @foreach(['A','B','C','D','E'] as $key)
                            <option value="{{ $key }}" {{ $question->correct_answer == $key ? 'selected' : '' }}>Pilihan {{ $key }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Seksi Essay --}}
        <div id="section_essay" style="{{ $question->type == 'pg' ? 'display:none' : '' }}; margin-bottom: 25px;">
            <label style="display: block; font-weight: 700; margin-bottom: 10px;">Pedoman Jawaban</label>
            <textarea name="correct_answer_essay" class="search-input" style="width: 100%; min-height: 100px;">{{ $question->type == 'essay' ? $question->correct_answer : '' }}</textarea>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 30px;">
            <button type="submit" class="btn-add" style="padding: 12px 25px;">Update & Simpan</button>
            <a href="{{ url()->previous() }}" class="nav-link" style="background: #eee; border: 1px solid #ddd;">Batal</a>
        </div>
    </form>
</div>
@endsection