@extends('layouts.app')
@section('title', 'Monitoring Pengawas - ' . $schedule->subject->nama_mapel)

@section('content')
<div style="padding: 10px;">
    {{-- ALERT NOTIFIKASI --}}
    @if(session('success'))
        <div style="background: #d1fae5; border: 1px solid #10b981; color: #065f46; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 style="margin: 0; color: #1e293b; font-size: 24px;">🛡️ Ruang Pengawas Live: {{ $schedule->subject->nama_mapel }}</h2>
            <p style="margin: 5px 0 0; color: #64748b;">Kelas: <strong>{{ $schedule->classroom->nama_kelas }}</strong> | Pengawas: {{ auth()->user()->name }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.location.reload()" style="background: #f1f5f9; border: 1px solid #e2e8f0; padding: 10px 15px; border-radius: 8px; cursor: pointer; font-weight: 600; color: #475569;">
                <i class="fas fa-sync-alt"></i> Refresh Manual
            </button>
            <a href="{{ route('pengawas.dashboard') }}" style="background: #1e293b; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                Kembali ke Dashboard
            </a>
        </div>
    </div>

    {{-- CARD SUMMARY STATISTIK --}}
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.03); border-left: 4px solid #3b82f6;">
            <div style="color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase;">Total Siswa</div>
            <div id="stat-total" style="font-size: 28px; font-weight: 800; color: #1e293b;">{{ $students->count() }}</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.03); border-left: 4px solid #10b981;">
            <div style="color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase;">Sudah Selesai</div>
            <div id="stat-selesai" style="font-size: 28px; font-weight: 800; color: #1e293b;">
                {{ $students->where('is_logged_in', 0)->where('total_dijawab', '>=', $schedule->questions_count ?? 1)->count() }}
            </div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.03); border-left: 4px solid #f59e0b;">
            <div style="color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase;">Sedang Aktif</div>
            <div id="stat-aktif" style="font-size: 28px; font-weight: 800; color: #1e293b;">
                {{ $students->where('is_logged_in', 1)->where('total_pelanggaran', 0)->count() }}
            </div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.03); border-left: 4px solid #ef4444;">
            <div style="color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase;">Terdeteksi Melanggar</div>
            <div id="stat-melanggar" style="font-size: 28px; font-weight: 800; color: #ef4444;">
                {{ $students->where('total_pelanggaran', '>', 0)->count() }}
            </div>
        </div>
    </div>

    {{-- PANEL CONTROL JADWAL --}}
    <div style="background: #fff3f3; border: 1px solid #ffcccc; padding: 20px; border-radius: 12px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; gap: 40px;">
            <div>
                <span style="display: block; font-size: 11px; color: #c91313; font-weight: 800; text-transform: uppercase;">Token Ujian</span>
                <span style="font-size: 24px; font-family: monospace; font-weight: 800; color: #cyan; background: #1e293b; padding: 2px 10px; border-radius: 6px; color: #fff;">{{ $schedule->token }}</span>
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
    </div>

    {{-- TABEL MONITORING LIVE --}}
    <div class="content-box" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 15px 20px; color: #475569; font-size: 13px;">No</th>
                    <th style="padding: 15px 20px; color: #475569; font-size: 13px;">NIS & Nama Siswa</th>
                    <th style="padding: 15px 20px; color: #475569; font-size: 13px; text-align: center;">Status</th>
                    <th style="padding: 15px 20px; color: #475569; font-size: 13px; text-align: center;">Progress</th>
                    <th style="padding: 15px 20px; color: #475569; font-size: 13px; text-align: center;">Pelanggaran</th>
                    <th style="padding: 15px 20px; color: #475569; font-size: 13px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="student-table-body">
                @foreach($students as $index => $student)
                @php
                    $totalSoal = $schedule->questions_count ?? count($schedule->questions ?? []);
                    $totalSoal = $totalSoal > 0 ? $totalSoal : 1; 
                    $persentase = round(($student->total_dijawab / $totalSoal) * 100);
                @endphp
                <tr id="student-row-{{ $student->id }}" style="border-bottom: 1px solid #f1f5f9; transition: background 0.5s; background: {{ $student->total_pelanggaran > 0 ? '#fff5f5' : 'transparent' }}">
                    <td style="padding: 15px 20px; color: #64748b;">{{ $index + 1 }}</td>
                    <td style="padding: 15px 20px;">
                        <div style="font-weight: 700; color: #1e293b;">{{ $student->name }}</div>
                        <div style="font-size: 11px; color: #94a3b8;">NIS: {{ $student->nis ?? '-' }}</div>
                    </td>
                    <td class="student-status-cell" style="padding: 15px 20px; text-align: center;">
                        @if($student->total_pelanggaran > 0)
                            <span style="background: #fee2e2; color: #ef4444; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800;">🛑 DISKUALIFIKASI</span>
                        @elseif($student->is_logged_in == 1)
                            <span style="background: #dcfce7; color: #15803d; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800;">✍️ MENGERJAKAN</span>
                        @else
                            <span style="background: #f1f5f9; color: #64748b; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800;">💤 BELUM LOGIN</span>
                        @endif
                    </td>
                    <td class="student-progress-cell" style="padding: 15px 20px; text-align: center;">
                        <div style="width: 100px; background: #e2e8f0; height: 8px; border-radius: 10px; margin: 0 auto 4px;">
                            <div class="progress-bar-fill" style="width: {{ $persentase }}%; background: {{ $persentase == 100 ? '#10b981' : '#3b82f6' }}; height: 100%; border-radius: 10px;"></div>
                        </div>
                        <span class="progress-text" style="font-size: 11px; font-weight: 700; color: #475569;">{{ $student->total_dijawab }} / {{ $totalSoal }} Soal ({{ $persentase }}%)</span>
                    </td>
                    <td class="student-violation-cell" style="padding: 15px 20px; text-align: center; font-weight: bold; color: {{ $student->total_pelanggaran > 0 ? '#ef4444' : '#64748b' }}">
                        {{ $student->total_pelanggaran > 0 ? '⚠️ ' . $student->total_pelanggaran . 'x Keluar App' : '-' }}
                    </td>
                    <td style="padding: 15px 20px; text-align: center;">
                        <div style="display: flex; gap: 5px; justify-content: center;">
                            <form action="{{ route('pengawas.monitoring.reset', [$schedule->id, $student->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin mereset hak akses login siswa ini?')">
                                @csrf
                                <button type="submit" title="Reset Device / Izinkan Login Kembali" style="background: #f59e0b; color: white; border: none; width: 32px; height: 32px; border-radius: 6px; cursor: pointer;">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- SCRIPT SINKRONISASI REALTIME & COUNTDOWN --}}
@vite(['resources/js/app.js']) {{-- Memastikan bundling Laravel Echo dipanggil --}}
<script>
    // 1. Logika Countdown Penutup Ujian
    const endTime = new Date("{{ $schedule->tanggal_ujian }} {{ $schedule->jam_selesai }}").getTime();
    const countdownInterval = setInterval(function() {
        const now = new Date().getTime();
        const distance = endTime - now;
        if (distance < 0) {
            clearInterval(countdownInterval);
            document.getElementById("countdown").innerHTML = "WAKTU HABIS";
            document.getElementById("countdown").style.color = "#ef4444";
            return;
        }
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        document.getElementById("countdown").innerHTML = String(hours).padStart(2, '0') + ":" + String(minutes).padStart(2, '0') + ":" + String(seconds).padStart(2, '0');
    }, 1000);

    // 2. Logika Realtime Monitoring via Laravel Echo & Reverb
    document.addEventListener('DOMContentLoaded', function () {
        const scheduleId = "{{ $schedule->id }}";
        const totalSoalGlobal = parseInt("{{ $totalSoal }}") || 1;

        if (window.Echo) {
            window.Echo.channel(`exam-monitoring.${scheduleId}`)
                .listen('.ExamStateUpdated', (e) => {
                    // Cari baris tabel siswa berdasarkan ID siswa yang dikirim WebSocket
                    const row = document.getElementById(`student-row-${e.student_id}`);
                    if (!row) return;

                    // Efek flash warna kuning penanda data masuk
                    row.style.background = '#fef08a';
                    setTimeout(() => {
                        row.style.background = e.total_pelanggaran > 0 ? '#fff5f5' : 'transparent';
                    }, 1000);

                    // Update Kolom Status
                    const statusCell = row.querySelector('.student-status-cell');
                    if (e.total_pelanggaran > 0) {
                        statusCell.innerHTML = `<span style="background: #fee2e2; color: #ef4444; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800;">🛑 DISKUALIFIKASI</span>`;
                    } else if (e.is_logged_in == 1) {
                        statusCell.innerHTML = `<span style="background: #dcfce7; color: #15803d; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800;">✍️ MENGERJAKAN</span>`;
                    } else {
                        statusCell.innerHTML = `<span style="background: #f1f5f9; color: #64748b; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800;">💤 BELUM LOGIN</span>`;
                    }

                    // Update Kolom Progress Jawaban
                    const hitungPersen = Math.round((e.total_dijawab / totalSoalGlobal) * 100);
                    row.querySelector('.progress-bar-fill').style.width = `${hitungPersen}%`;
                    row.querySelector('.progress-bar-fill').style.background = hitungPersen === 100 ? '#10b981' : '#3b82f6';
                    row.querySelector('.progress-text').innerText = `${e.total_dijawab} / ${totalSoalGlobal} Soal (${hitungPersen}%)`;

                    // Update Kolom Pelanggaran
                    const violationCell = row.querySelector('.student-violation-cell');
                    if (e.total_pelanggaran > 0) {
                        violationCell.innerText = `⚠️ ${e.total_pelanggaran}x Keluar App`;
                        violationCell.style.color = '#ef4444';
                    } else {
                        violationCell.innerText = '-';
                        violationCell.style.color = '#64748b';
                    }

                    // Re-calculate ringkasan statistik di atas card
                    updateSummaryCards();
                });
        }

        function updateSummaryCards() {
            // Logika client-side counting ringkas berdasarkan perubahan baris tabel
            const totalSiswa = document.querySelectorAll('#student-table-body tr').length;
            let totalSelesai = 0, totalAktif = 0, totalMelanggar = 0;

            document.querySelectorAll('#student-table-body tr').forEach(row => {
                const textPelanggaran = row.querySelector('.student-violation-cell').innerText;
                const textStatus = row.querySelector('.student-status-cell').innerText;

                if (textPelanggaran.includes('Keluar App')) {
                    totalMelanggar++;
                }
                if (textStatus.includes('MENGERJAKAN') && !textPelanggaran.includes('Keluar App')) {
                    totalAktif++;
                }
                if (textStatus.includes('BELUM LOGIN') && row.querySelector('.progress-text').innerText.includes('100%')) {
                    totalSelesai++;
                }
            });

            document.getElementById('stat-aktif').innerText = totalAktif;
            document.getElementById('stat-melanggar').innerText = totalMelanggar;
        }
    });
</script>
@endsection