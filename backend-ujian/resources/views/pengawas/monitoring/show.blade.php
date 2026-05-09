@extends('layouts.app')
@section('title', 'Monitor Ruang - ' . $schedule->classroom->nama_kelas)

@section('content')
<div style="padding: 10px;">
    {{-- Header Monitor --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="background: #cd0000; color: white; padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 800;">STAFF MONITOR</span>
                <h2 style="margin: 0; color: #1e293b; font-size: 24px;">{{ $schedule->subject->nama_mapel }}</h2>
            </div>
            <p style="margin: 5px 0 0; color: #64748b;">
                Ruang: <strong>{{ $schedule->classroom->nama_kelas }}</strong> | 
                Sesi: {{ $schedule->jam_mulai }} - {{ $schedule->jam_selesai }}
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="location.reload()" style="background: #fff; border: 1px solid #d1d5db; padding: 10px 15px; border-radius: 8px; cursor: pointer; font-weight: 600; color: #4b5563; display: flex; align-items: center; gap: 8px;">
                🔄 Refresh Status
            </button>
            <a href="{{ route('pengawas.monitoring.index') }}" style="background: #1e293b; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: flex; align-items: center;">
                ← Kembali
            </a>
        </div>
    </div>

    {{-- Statistik Ringkas --}}
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 25px;">
        <div style="background: white; padding: 15px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border-bottom: 3px solid #3b82f6;">
            <div style="color: #94a3b8; font-size: 11px; font-weight: 800; letter-spacing: 1px;">TOTAL PESERTA</div>
            <div style="font-size: 24px; font-weight: 800; color: #1e293b;">{{ $students->count() }}</div>
        </div>
        <div style="background: white; padding: 15px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border-bottom: 3px solid #10b981;">
            <div style="color: #94a3b8; font-size: 11px; font-weight: 800; letter-spacing: 1px;">HADIR/LOGIN</div>
            <div style="font-size: 24px; font-weight: 800; color: #10b981;">0</div>
        </div>
        <div style="background: white; padding: 15px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border-bottom: 3px solid #f59e0b;">
            <div style="color: #94a3b8; font-size: 11px; font-weight: 800; letter-spacing: 1px;">SEDANG UJIAN</div>
            <div style="font-size: 24px; font-weight: 800; color: #f59e0b;">0</div>
        </div>
        <div style="background: white; padding: 15px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border-bottom: 3px solid #ef4444;">
            <div style="color: #94a3b8; font-size: 11px; font-weight: 800; letter-spacing: 1px;">PELANGGARAN</div>
            <div style="font-size: 24px; font-weight: 800; color: #ef4444;">0</div>
        </div>
    </div>

    {{-- Control Bar & Token --}}
    <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 15px 25px; border-radius: 12px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; gap: 30px; align-items: center;">
            <div>
                <span style="display: block; font-size: 10px; color: #64748b; font-weight: 800;">TOKEN LOGIN</span>
                <span style="font-size: 22px; font-family: 'Courier New', Courier, monospace; font-weight: 900; color: #cd0000;">{{ $schedule->token }}</span>
            </div>
            <div style="height: 40px; width: 1px; background: #e2e8f0;"></div>
            <div>
                <span style="display: block; font-size: 10px; color: #64748b; font-weight: 800;">WAKTU TERSISA</span>
                <span id="timer" style="font-size: 20px; font-weight: 700; color: #1e293b;">--:--:--</span>
            </div>
        </div>
        <div style="display: flex; gap: 10px;">
            <button style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; padding: 10px 15px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 13px;">
                ⚠️ Tutup Ujian Sekarang
            </button>
        </div>
    </div>

    {{-- Tabel Monitoring --}}
    <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #f1f5f9;">
                    <th style="padding: 15px; text-align: center; width: 50px; font-size: 12px; color: #64748b;">NO</th>
                    <th style="padding: 15px; text-align: left; font-size: 12px; color: #64748b;">NAMA SISWA</th>
                    <th style="padding: 15px; text-align: center; font-size: 12px; color: #64748b;">STATUS SEBSTAR</th>
                    <th style="padding: 15px; text-align: center; font-size: 12px; color: #64748b;">PROGRES</th>
                    <th style="padding: 15px; text-align: center; font-size: 12px; color: #64748b;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $index => $student)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 15px; text-align: center; color: #94a3b8; font-size: 13px;">{{ $index + 1 }}</td>
                    <td style="padding: 15px;">
                        <div style="font-weight: 700; color: #1e293b; font-size: 14px;">{{ $student->name }}</div>
                        <div style="font-size: 11px; color: #94a3b8;">NIS: {{ $student->nis ?? '-' }}</div>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <span style="background: #f1f5f9; color: #94a3b8; padding: 4px 10px; border-radius: 6px; font-size: 10px; font-weight: 800;">
                            OFFLINE
                        </span>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <div style="width: 80px; background: #f1f5f9; height: 6px; border-radius: 10px; margin: 0 auto 5px;">
                            <div style="width: 0%; background: #cd0000; height: 100%; border-radius: 10px;"></div>
                        </div>
                        <span style="font-size: 10px; color: #94a3b8;">0% Terjawab</span>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            {{-- Button Reset Login (Sangat penting buat SEBSTAR) --}}
                            <form action="#" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" title="Reset Login Siswa" style="background: #fff; border: 1px solid #f59e0b; color: #f59e0b; width: 32px; height: 32px; border-radius: 6px; cursor: pointer;">
                                    <i class="fas fa-key"></i>
                                </button>
                            </form>
                            {{-- Button Paksa Selesai --}}
                            <button title="Selesaikan Paksa" style="background: #fff; border: 1px solid #1e293b; color: #1e293b; width: 32px; height: 32px; border-radius: 6px; cursor: pointer;">
                                <i class="fas fa-check"></i>
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
    // Timer Real-time
    const targetWaktu = new Date("{{ $schedule->tanggal_ujian }} {{ $schedule->jam_selesai }}").getTime();
    
    const x = setInterval(function() {
        const skrg = new Date().getTime();
        const selisih = targetWaktu - skrg;
        
        if (selisih < 0) {
            clearInterval(x);
            document.getElementById("timer").innerHTML = "WAKTU HABIS";
            return;
        }

        const h = Math.floor((selisih % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const m = Math.floor((selisih % (1000 * 60 * 60)) / (1000 * 60));
        const s = Math.floor((selisih % (1000 * 60)) / 1000);
        
        document.getElementById("timer").innerHTML = h + "j " + m + "m " + s + "s";
    }, 1000);
</script>
@endsection