@extends('layouts.app')
@section('title', 'Tugas Pengawas')

@section('content')
<div class="content-box" style="background: white; padding: 25px; border-radius: 15px; border-top: 5px solid #cd0000;">
    <h3 style="margin-bottom: 20px;">🛡️ Penugasan Pengawasan Ruang</h3>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        @forelse($schedules as $s)
            <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; background: #fff; transition: 0.3s;">
                <h4 style="margin: 0; color: #1e293b; font-size: 18px;">{{ $s->subject->nama_mapel }}</h4>
                <p style="color: #64748b; font-size: 14px; margin-top: 5px;">Kelas: <strong>{{ $s->classroom->nama_kelas }}</strong></p>
                
                <div style="margin-top: 15px; background: #f8fafc; padding: 10px; border-radius: 8px; font-size: 12px; color: #475569;">
                    <div>📅 {{ date('d M Y', strtotime($s->tanggal_ujian)) }}</div>
                    <div>⏰ {{ $s->jam_mulai }} - {{ $s->jam_selesai }}</div>
                </div>

                <a href="{{ route('pengawas.monitoring.show', $s->id) }}" 
                   style="display: block; margin-top: 20px; text-align: center; background: #cd0000; color: white; padding: 12px; border-radius: 8px; text-decoration: none; font-weight: 700;">
                    Masuk Ruang Monitor
                </a>
            </div>
        @empty
            <div style="grid-column: 1/-1; text-align: center; padding: 50px; color: #94a3b8;">
                <p>Belum ada jadwal yang harus Anda awasi hari ini.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection