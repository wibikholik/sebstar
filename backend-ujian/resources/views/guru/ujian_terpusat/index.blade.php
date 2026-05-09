@extends('layouts.app')

@section('title', 'Jadwal & Input Soal')

@section('content')
<div class="stats-grid">
    @forelse($schedules as $item)
        <div class="stat-card" style="text-align: left; background: #fff; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <span style="font-size: 12px; font-weight: 700; color: #c91313; text-transform: uppercase;">
                    {{ $item->subject->name }}
                </span>
                <span style="font-size: 11px; padding: 4px 10px; background: #f0f0f0; border-radius: 6px; font-weight: 600;">
                    {{ $item->status }}
                </span>
            </div>

            <h2 style="font-size: 24px; margin: 15px 0 10px; font-weight: 800; color: #333;">{{ $item->classroom->name }}</h2>
            
            <div style="color: #666; font-size: 14px; line-height: 1.8;">
                <div>📅 {{ \Carbon\Carbon::parse($item->tanggal_ujian)->translatedFormat('l, d M Y') }}</div>
                <div>⏰ {{ $item->jam_mulai }} - {{ $item->jam_selesai }} ({{ $item->durasi }} Menit)</div>
                <div>🔑 Token: <strong style="color: #333;">{{ $item->token }}</strong></div>
            </div>

            <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="font-size: 14px; font-weight: 600;">
                    <strong>{{ \App\Models\Question::where('schedule_id', $item->id)->count() }}</strong> Soal Terinput
                </div>
                <a href="{{ route('guru.ujian-terpusat.manage', $item->id) }}" 
                   style="background: #c91313; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; font-size: 14px;">
                    Kelola Soal
                </a>
            </div>
        </div>
    @empty
        <div style="text-align: center; padding: 50px; background: #fff; border-radius: 15px; grid-column: span 3;">
            <p style="color: #999;">Belum ada jadwal ujian untuk Anda.</p>
        </div>
    @endforelse
</div>
@endsection