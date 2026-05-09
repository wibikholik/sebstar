@extends('layouts.app')
@section('title', 'Monitoring - ' . $schedule->subject->nama_mapel)

@section('content')
<div style="padding: 10px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 style="margin: 0; color: #1e293b; font-size: 24px;">🛡️ Ruang Pengawas: {{ $schedule->subject->nama_mapel }}</h2>
            <p style="margin: 5px 0 0; color: #64748b;">Kelas: <strong>{{ $schedule->classroom->nama_kelas }}</strong> | Pengawas: {{ auth()->user()->name }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.location.reload()" style="background: #f1f5f9; border: 1px solid #e2e8f0; padding: 10px 15px; border-radius: 8px; cursor: pointer; font-weight: 600; color: #475569;">
                <i class="fas fa-sync-alt"></i> Refresh Data
            </button>
            <a href="{{ route('guru.monitoring.index') }}" style="background: #1e293b; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                Kembali
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.03); border-left: 4px solid #3b82f6;">
            <div style="color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase;">Total Siswa</div>
            <div style="font-size: 28px; font-weight: 800; color: #1e293b;">{{ $students->count() }}</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.03); border-left: 4px solid #10b981;">
            <div style="color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase;">Sudah Selesai</div>
            <div style="font-size: 28px; font-weight: 800; color: #1e293b;">0</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.03); border-left: 4px solid #f59e0b;">
            <div style="color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase;">Sedang Mengerjakan</div>
            <div style="font-size: 28px; font-weight: 800; color: #1e293b;">0</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.03); border-left: 4px solid #ef4444;">
            <div style="color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase;">Melanggar (Keluar App)</div>
            <div style="font-size: 28px; font-weight: 800; color: #ef4444;">0</div>
        </div>
    </div>

    <div style="background: #fff3f3; border: 1px solid #ffcccc; padding: 20px; border-radius: 12px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; gap: 40px;">
            <div>
                <span style="display: block; font-size: 11px; color: #c91313; font-weight: 800; text-transform: uppercase;">Token Ujian</span>
                <span style="font-size: 24px; font-family: monospace; font-weight: 800; color: #cd0000; letter-spacing: 2px;">{{ $schedule->token }}</span>
            </div>
            <div>
                <span style="display: block; font-size: 11px; color: #666; font-weight: 800; text-transform: uppercase;">Status Server</span>
                <span style="font-size: 18px; font-weight: 700; color: {{ $schedule->status == 'aktif' ? '#15803d' : '#991b1b' }}">
                    ● {{ strtoupper($schedule->status) }}
                </span>
            </div>
            <div>
                <span style="display: block; font-size: 11px; color: #666; font-weight: 800; text-transform: uppercase;">Waktu Tersisa</span>
                <span id="countdown" style="font-size: 18px; font-weight: 700; color: #1e293b;">--:--:--</span>
            </div>
        </div>
        <button style="background: #cd0000; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer;">
            Hentikan Ujian (Force Stop)
        </button>
    </div>

    <div class="content-box" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 15px 20px; color: #475569; font-size: 13px;">No</th>
                    <th style="padding: 15px 20px; color: #475569; font-size: 13px;">NIS & Nama Siswa</th>
                    <th style="padding: 15px 20px; color: #475569; font-size: 13px; text-align: center;">Status</th>
                    <th style="padding: 15px 20px; color: #475569; font-size: 13px; text-align: center;">Progress</th>
                    <th style="padding: 15px 20px; color: #475569; font-size: 13px;">Aktivitas Terakhir</th>
                    <th style="padding: 15px 20px; color: #475569; font-size: 13px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $index => $student)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 15px 20px; color: #64748b;">{{ $index + 1 }}</td>
                    <td style="padding: 15px 20px;">
                        <div style="font-weight: 700; color: #1e293b;">{{ $student->name }}</div>
                        <div style="font-size: 11px; color: #94a3b8;">NIS: {{ $student->nis ?? '-' }}</div>
                    </td>
                    <td style="padding: 15px 20px; text-align: center;">
                        <span style="background: #f1f5f9; color: #64748b; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800;">
                            BELUM LOGIN
                        </span>
                    </td>
                    <td style="padding: 15px 20px; text-align: center;">
                        <div style="width: 100px; background: #e2e8f0; height: 8px; border-radius: 10px; margin: 0 auto;">
                            <div style="width: 0%; background: #3b82f6; height: 100%; border-radius: 10px;"></div>
                        </div>
                        <span style="font-size: 10px; color: #94a3b8;">0%</span>
                    </td>
                    <td style="padding: 15px 20px; color: #64748b; font-size: 12px;">
                        -
                    </td>
                    <td style="padding: 15px 20px; text-align: center;">
                        <div style="display: flex; gap: 5px; justify-content: center;">
                            <button title="Reset Device" style="background: #f59e0b; color: white; border: none; width: 32px; height: 32px; border-radius: 6px; cursor: pointer;">
                                <i class="fas fa-undo"></i>
                            </button>
                            <button title="Selesaikan Paksa" style="background: #1e293b; color: white; border: none; width: 32px; height: 32px; border-radius: 6px; cursor: pointer;">
                                <i class="fas fa-check-double"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    // Contoh Sederhana Script Hitung Mundur (Bisa dikembangkan lagi)
    const endTime = new Date("{{ $schedule->tanggal_ujian }} {{ $schedule->jam_selesai }}").getTime();
    
    setInterval(function() {
        const now = new Date().getTime();
        const distance = endTime - now;
        
        if (distance < 0) {
            document.getElementById("countdown").innerHTML = "WAKTU HABIS";
            return;
        }

        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        document.getElementById("countdown").innerHTML = hours + "j " + minutes + "m " + seconds + "s";
    }, 1000);
</script>
@endsection