@extends('layouts.app')
@section('title', 'Monitoring Ujian (Guru)')
@section('content')
<div class="content-box" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
    <h3 style="margin-bottom: 20px; color: #1e293b;">🛡️ Daftar Tugas Mengawas</h3>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
        @forelse($schedules as $s)
            <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; border-top: 5px solid #cd0000; background: #fff;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                    <h4 style="margin: 0; font-size: 18px; color: #334155;">{{ $s->subject->nama_mapel }}</h4>
                    <span style="font-size: 10px; padding: 4px 8px; border-radius: 20px; font-weight: 800; 
                        {{ $s->status == 'aktif' ? 'background: #dcfce7; color: #15803d;' : 'background: #f1f5f9; color: #64748b;' }}">
                        {{ strtoupper($s->status) }}
                    </span>
                </div>
                
                <p style="margin: 0; color: #64748b; font-size: 14px;">Kelas: <strong>{{ $s->classroom->nama_kelas }}</strong></p>
                <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 15px 0;">

                <div style="font-size: 13px; color: #475569; line-height: 1.8;">
                    <div>📅 {{ date('d M Y', strtotime($s->tanggal_ujian)) }}</div>
                    <div>⏰ {{ $s->jam_mulai }} - {{ $s->jam_selesai }} ({{ $s->durasi }} Menit)</div>
                    <div>🔑 Token: <strong style="color: #cd0000; font-family: monospace;">{{ $s->token }}</strong></div>
                </div>

                <a href="{{ route('guru.monitoring.show', $s->id) }}" 
                   style="display: block; margin-top: 20px; text-align: center; background: #1e293b; color: white; padding: 12px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 14px;">
                    Monitor Kelas
                </a>
            </div>
        @empty
            <div style="grid-column: 1/-1; text-align: center; padding: 50px; background: #f8fafc; border-radius: 12px; color: #94a3b8;">
                <p>Anda tidak memiliki jadwal mengawas saat ini.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection