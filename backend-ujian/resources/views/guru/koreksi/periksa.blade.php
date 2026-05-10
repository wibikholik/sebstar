@extends('layouts.app')
@section('title', 'Koreksi Jawaban')

@section('content')
<form action="{{ route('guru.koreksi.update', $student->id) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">

    {{-- Header Profil Siswa --}}
    <div style="background: white; padding: 25px; border-radius: 16px; border: 1px solid #f1f5f9; margin-bottom: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <h3 style="margin: 0; color: #0f172a; font-weight: 700; font-size: 22px;">{{ $student->name }}</h3>
        <p style="margin: 5px 0 0 0; color: #64748b; font-size: 14px;">Mata Pelajaran: <strong>{{ $schedule->subject->nama_mapel }}</strong></p>
    </div>

    @foreach($essayAnswers as $index => $item)
    <div style="background: white; border-radius: 16px; border: 1px solid #f1f5f9; overflow: hidden; margin-bottom: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <div style="background: #f8fafc; padding: 12px 25px; border-bottom: 1px solid #f1f5f9;">
            <span style="font-weight: 800; color: #94a3b8; font-size: 11px; letter-spacing: 1px;">SOAL NOMOR {{ $index + 1 }}</span>
        </div>
        
        <div style="padding: 25px;">
            {{-- Pertanyaan --}}
            <div style="margin-bottom: 25px;">
                <label style="display: block; color: #94a3b8; font-weight: 700; font-size: 11px; margin-bottom: 8px; text-transform: uppercase;">Pertanyaan:</label>
                <div style="font-size: 15px; color: #1e293b; background: #f8fafc; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0; line-height: 1.6;">
                    {!! $item->question->question_text !!}
                </div>
            </div>

            {{-- Jawaban Siswa --}}
            <div style="margin-bottom: 30px;">
                <label style="display: block; color: #c91313; font-weight: 700; font-size: 11px; margin-bottom: 8px; text-transform: uppercase;">Jawaban Siswa:</label>
                <div style="font-size: 16px; font-weight: 500; color: #0f172a; border: 1px solid #fee2e2; padding: 20px; border-radius: 12px; background: #fffcfc; min-height: 60px;">
                    {{ $item->answer }}
                </div>
            </div>

            {{-- Panel Penilaian (Perbaikan Grid agar tidak tumpang tindih) --}}
            <div style="display: grid; grid-template-columns: 180px 1fr; gap: 20px; background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 8px; text-transform: uppercase;">Skor (0-100)</label>
                    <input type="number" 
                           name="scores[{{ $item->id }}]" 
                           value="{{ $item->score }}" 
                           step="0.01" 
                           required 
                           min="0" 
                           max="100"
                           style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-weight: 700; font-size: 18px; outline: none; text-align: center;">
                </div>
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 8px; text-transform: uppercase;">Catatan / Feedback</label>
                    <textarea name="notes[{{ $item->id }}]" 
                              placeholder="Tulis masukan untuk siswa..." 
                              style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; font-size: 14px; min-height: 80px; resize: vertical;">{{ $item->teacher_note }}</textarea>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    {{-- Footer Bar (Perbaikan agar lebih solid di bawah) --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 40px; padding: 20px 30px; background: #1e293b; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(30, 41, 59, 0.2); position: sticky; bottom: 20px; z-index: 99;">
        <a href="{{ route('guru.koreksi.index', ['schedule_id' => $schedule->id]) }}" 
           style="color: #94a3b8; text-decoration: none; font-weight: 600; font-size: 14px; transition: 0.3s;">
           ← Batalkan
        </a>
        <button type="submit" 
                style="background: #c91313; color: white; border: none; padding: 14px 35px; border-radius: 10px; cursor: pointer; font-weight: 700; font-size: 15px; transition: 0.3s; box-shadow: 0 4px 6px -1px rgba(201, 19, 19, 0.3);">
            Simpan Seluruh Penilaian
        </button>
    </div>
</form>

<style>
    /* Tambahan agar saat hover tombol lebih interaktif */
    button:hover { background: #b01111 !important; transform: translateY(-1px); }
    input:focus, textarea:focus { border-color: #c91313 !important; box-shadow: 0 0 0 2px rgba(201, 19, 19, 0.1); }
</style>
@endsection