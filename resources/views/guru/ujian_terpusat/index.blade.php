@extends('layouts.app')

@section('title', 'Jadwal & Input Soal')

@section('content')
<div class="stats-grid">
    @forelse($schedules as $item)
        <div class="stat-card" style="text-align: left;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <span style="font-size: 12px; font-weight: 700; color: var(--red-sebstar); text-transform: uppercase;">
                    {{ $item->subject->name }}
                </span>
                <span style="font-size: 11px; padding: 2px 8px; background: #eee; border-radius: 4px;">
                    {{ $item->status }}
                </span>
            </div>

            <h2 style="font-size: 22px; margin: 15px 0 5px;">{{ $item->classroom->name }}</h2>
            
            <div style="color: var(--text-gray); font-size: 13px; line-height: 1.6;">
                <div>📅 {{ \Carbon\Carbon::parse($item->tanggal_ujian)->translatedFormat('l, d F Y') }}</div>
                <div>⏰ {{ $item->jam_mulai }} - {{ $item->jam_selesai }} ({{ $item->durasi }} Menit)</div>
                <div>🔑 Token: <strong>{{ $item->token }}</strong></div>
            </div>

            <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 20px 0;">

            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="font-size: 12px;">
                    {{-- Opsional: Hitung jumlah soal yang sudah ada --}}
                    <strong>{{ $item->subject->questions_count ?? 0 }}</strong> Soal Terinput
                </div>
                <a href="{{ route('guru.ujian-terpusat.manage', $item->id) }}" class="btn-add" style="text-decoration: none; padding: 8px 15px; font-size: 13px;">
                    Kelola Soal
                </a>
            </div>
        </div>
    @empty
        <div class="content-box" style="grid-column: span 3; text-align: center; padding: 50px;">
            <p style="color: var(--text-gray);">Belum ada jadwal ujian terpusat yang diplotting untuk Anda.</p>
        </div>
    @endforelse
</div>
@endsection