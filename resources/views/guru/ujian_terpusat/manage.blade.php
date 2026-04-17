@extends('layouts.app')

@section('title', 'Kelola Soal: ' . $schedule->subject->name)

@section('content')
<div class="content-box" style="margin-bottom: 25px; border-left: 5px solid var(--red-sebstar);">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3 style="margin: 0;">{{ $schedule->subject->name }} - {{ $schedule->classroom->name }}</h3>
            <p style="margin: 5px 0 0; color: var(--text-gray); font-size: 14px;">
                📅 {{ \Carbon\Carbon::parse($schedule->tanggal_ujian)->translatedFormat('d F Y') }} 
                | ⏰ {{ $schedule->jam_mulai }} - {{ $schedule->jam_selesai }} ({{ $schedule->durasi }} Menit)
            </p>
        </div>
        <div style="text-align: right;">
            <span class="role" style="color: var(--red-sebstar);">Token: {{ $schedule->token }}</span>
            <div style="font-size: 12px; color: var(--text-gray);">Status: {{ ucfirst($schedule->status) }}</div>
        </div>
    </div>
</div>

<div class="content-box">
    <div class="action-bar">
        <h4>Daftar Soal</h4>
        <a href="{{ route('guru.questions.create', ['subject_id' => $schedule->subject_id]) }}" class="btn-add" style="text-decoration: none;">
            + Tambah Soal
        </a>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Pertanyaan</th>
                <th>Tipe</th>
                <th style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($questions as $index => $q)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <div style="font-weight: 600;">{{ Str::limit($q->question_text, 100) }}</div>
                    @if($q->type == 'pg')
                        <small style="color: var(--text-gray);">Jawaban: <strong>{{ $q->correct_answer }}</strong></small>
                    @endif
                </td>
                <td>
                    <span style="font-size: 11px; padding: 3px 8px; border-radius: 12px; font-weight: bold; background: #eee;">
                        {{ strtoupper($q->type) }}
                    </span>
                </td>
                <td style="text-align: center;">
                    <a href="{{ route('guru.questions.edit', $q->id) }}" style="color: #0288d1; margin-right: 10px; text-decoration: none;">Edit</a>
                    <form action="{{ route('guru.questions.destroy', $q->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" style="color: var(--red-sebstar); background: none; border: none; cursor: pointer; font-weight: 600;">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 30px; color: var(--text-gray);">Belum ada soal untuk mata pelajaran ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="margin-top: 20px;">
        <a href="{{ route('guru.questions.index') }}" style="color: var(--text-gray); text-decoration: none; font-size: 14px;">← Kembali ke Jadwal</a>
    </div>
</div>
@endsection