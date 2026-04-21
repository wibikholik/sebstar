@extends('layouts.app')

@section('title', 'Kelola Soal: ' . $schedule->subject->name)

@section('content')
<div class="content-box" style="margin-bottom: 25px; border-left: 5px solid var(--red-sebstar);">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <h3 style="margin: 0; font-size: 20px;">{{ $schedule->subject->name }}</h3>
            <p style="margin: 8px 0 0; color: var(--text-gray); font-size: 14px;">
                Kelas: <strong>{{ $schedule->classroom->name }}</strong> | 
                Tanggal: <strong>{{ \Carbon\Carbon::parse($schedule->tanggal_ujian)->translatedFormat('d F Y') }}</strong>
            </p>
            <p style="margin: 5px 0 0; color: var(--text-gray); font-size: 14px;">
                Waktu: <strong>{{ $schedule->jam_mulai }} - {{ $schedule->jam_selesai }}</strong> ({{ $schedule->durasi }} Menit)
            </p>
        </div>
        <div style="text-align: right;">
            <a href="{{ route('guru.ujian-terpusat.create', ['subject_id' => $schedule->subject_id, 'schedule_id' => $schedule->id]) }}" class="btn-add" style="text-decoration: none;">
                <span>+</span> Tambah Soal
            </a>
            <div style="margin-top: 10px; font-size: 13px; color: var(--text-gray);">
                Token Ujian: <strong style="color: var(--text-dark);">{{ $schedule->token }}</strong>
            </div>
        </div>
    </div>
</div>

<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h4 style="margin: 0;">Daftar Soal Terinput</h4>
        <span style="background: #f0f2f5; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;">
            Total: {{ $questions->count() }} Soal
        </span>
    </div>

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">No</th>
                    <th>Detail Soal</th>
                    <th style="width: 120px; text-align: center;">Tipe</th>
                    <th style="width: 150px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questions as $index => $q)
                <tr>
                    <td style="text-align: center; vertical-align: top; padding-top: 15px;">{{ $index + 1 }}</td>
                    <td>
                        <div style="font-weight: 600; color: var(--text-dark); margin-bottom: 8px; line-height: 1.5;">
                            {{ $q->question_text }}
                        </div>
                        
                        {{-- Jika Ada Gambar --}}
                        @if($q->question_image)
                        <div style="margin: 10px 0;">
                            <img src="{{ asset('storage/' . $q->question_image) }}" alt="Gambar Soal" 
                                 style="max-width: 200px; border-radius: 8px; border: 1px solid var(--border-color);">
                        </div>
                        @endif

                        {{-- Jika Tipe PG, Tampilkan Opsi Singkat --}}
                        @if($q->type == 'pg')
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 5px; font-size: 12px; color: var(--text-gray); background: #f8f9fa; padding: 10px; border-radius: 8px;">
                            <div><strong>A.</strong> {{ Str::limit($q->option_a, 30) }}</div>
                            <div><strong>B.</strong> {{ Str::limit($q->option_b, 30) }}</div>
                            <div><strong>C.</strong> {{ Str::limit($q->option_c, 30) }}</div>
                            <div><strong>D.</strong> {{ Str::limit($q->option_d, 30) }}</div>
                            <div style="grid-column: span 2; color: var(--red-sebstar); margin-top: 5px;">
                                <strong>Kunci: {{ $q->correct_answer }}</strong>
                            </div>
                        </div>
                        @else
                        <div style="font-size: 12px; color: #7b1fa2; background: #f3e5f5; padding: 10px; border-radius: 8px;">
                            <strong>Pedoman Jawaban:</strong><br>
                            {{ Str::limit($q->correct_answer, 100) }}
                        </div>
                        @endif
                    </td>
                    <td style="text-align: center; vertical-align: top; padding-top: 15px;">
                        <span style="font-size: 10px; padding: 4px 8px; border-radius: 4px; font-weight: 800; text-transform: uppercase;
                            {{ $q->type == 'pg' ? 'background: #e3f2fd; color: #1565c0;' : 'background: #f3e5f5; color: #7b1fa2;' }}">
                            {{ $q->type }}
                        </span>
                    </td>
                    <td style="text-align: center; vertical-align: top; padding-top: 15px;">
                        <div style="display: flex; gap: 10px; justify-content: center;">
                         <a href="{{ route('guru.ujian-terpusat.edit', [$q->id, 'schedule_id' => $schedule->id]) }}" class="btn-edit">
    Edit
</a>
                            <form action="{{ route('guru.ujian-terpusat.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Hapus soal ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: var(--red-sebstar); cursor: pointer; font-weight: 600; font-size: 13px; font-family: inherit;">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 50px; color: var(--text-gray);">
                        Belum ada soal untuk mata pelajaran ini. Silakan klik <strong>Tambah Soal</strong>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px; border-top: 1px solid var(--border-color); padding-top: 20px;">
        <a href="{{ route('guru.ujian-terpusat.index') }}" style="text-decoration: none; color: var(--text-gray); font-size: 14px; font-weight: 600;">
            ← Kembali ke Jadwal Ujian
        </a>
    </div>
</div>
@endsection