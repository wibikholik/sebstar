@extends('layouts.app')
@section('title', 'Monitoring Live Guru - ' . $schedule->subject->nama_mapel)

@section('content')
{{-- LINK CDN FONTAWESOME (Memastikan Semua Icon Muncul Sempurna) --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div style="padding: 10px;">
    {{-- ALERT NOTIFIKASI SUKSES / ERROR ACTION --}}
    @if(session('success'))
        <div style="background: #d1fae5; border: 1px solid #10b981; color: #065f46; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-check-circle" style="font-size: 18px;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    
    @if(session('error'))
        <div style="background: #fee2e2; border: 1px solid #ef4444; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-exclamation-circle" style="font-size: 18px;"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- HEADER HALAMAN MONITORING --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 style="margin: 0; color: #1e293b; font-size: 24px;">🛡️ Ruang Pengawasan: {{ $schedule->subject->nama_mapel }}</h2>
            <p style="margin: 5px 0 0; color: #64748b;">Kelas: <strong>{{ $schedule->classroom->nama_kelas }}</strong> | Durasi Server: <strong>{{ $schedule->durasi }} Menit</strong></p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.location.reload()" style="background: #f1f5f9; border: 1px solid #e2e8f0; padding: 10px 15px; border-radius: 8px; cursor: pointer; font-weight: 600; color: #475569; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-rotate"></i> Refresh Data
            </button>
            <a href="{{ route('guru.monitoring.index') }}" style="background: #1e293b; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    {{-- 🛠️ SINKRONISASI LOGIKA PINTAR KALKULASI SUMMARY CARDS BERDASARKAN PROGRESS ASLI TABEL --}}
    @php
        $totalSoalJadwal = $schedule->questions_count ?? count($schedule->questions ?? []);
        $totalSoalJadwal = $totalSoalJadwal > 0 ? $totalSoalJadwal : 1;

        // Siswa Selesai: Jika dia tidak melanggar DAN jumlah soal terjawab sudah maksimal penuh
        $countSelesai = $students->filter(function($s) use ($totalSoalJadwal) {
            return $s->total_pelanggaran == 0 && $s->total_dijawab >= $totalSoalJadwal;
        })->count();

        // Siswa Sedang Aktif Mengerjakan: Jika dia tidak melanggar DAN progress pengerjaan sudah mulai diisi tapi belum penuh maksimal
        // Ditambah toleransi jika data dijawab masih 0 tetapi status login HP bernilai 1 (baru buka kuis)
        $countMengerjakan = $students->filter(function($s) use ($totalSoalJadwal) {
            return $s->total_pelanggaran == 0 && (
                ($s->total_dijawab > 0 && $s->total_dijawab < $totalSoalJadwal) || 
                ($s->total_dijawab == 0 && $s->is_logged_in == 1)
            );
        })->count();

        // Siswa Melanggar / Terkunci Diskualifikasi
        $countMelanggar = $students->where('total_pelanggaran', '>', 0)->count();
    @endphp

    {{-- CARD SUMMARY STATISTIK --}}
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.03); border-left: 4px solid #3b82f6;">
            <div style="color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase;">Total Siswa</div>
            <div style="font-size: 28px; font-weight: 800; color: #1e293b;">{{ $students->count() }}</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.03); border-left: 4px solid #10b981;">
            <div style="color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase;">Sudah Selesai</div>
            <div id="stat-selesai" style="font-size: 28px; font-weight: 800; color: #1e293b;">
                {{ $countSelesai }}
            </div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.03); border-left: 4px solid #f59e0b;">
            <div style="color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase;">Sedang Aktif</div>
            <div id="stat-mengerjakan" style="font-size: 28px; font-weight: 800; color: #1e293b;">
                {{ $countMengerjakan }}
            </div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.03); border-left: 4px solid #ef4444;">
            <div style="color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase;">Melanggar / Terkunci</div>
            <div id="stat-melanggar" style="font-size: 28px; font-weight: 800; color: #ef4444;">
                {{ $countMelanggar }}
            </div>
        </div>
    </div>

    {{-- CARD DETAIL SERVER & UTILITAS --}}
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 12px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; gap: 40px; align-items: center;">
            <div>
                <span style="display: block; font-size: 11px; color: #c91313; font-weight: 800; text-transform: uppercase;">Token Ujian Aktif</span>
                <span style="font-size: 24px; font-family: monospace; font-weight: 800; color: #cd0000; letter-spacing: 2px;">{{ $schedule->token }}</span>
            </div>
            <div>
                <span style="display: block; font-size: 11px; color: #666; font-weight: 800; text-transform: uppercase;">Status Kontrol Server</span>
                <span style="font-size: 18px; font-weight: 700; color: {{ $schedule->status == 'aktif' ? '#10b981' : '#ef4444' }}">
                    ● {{ strtoupper($schedule->status) }}
                </span>
            </div>
        </div>
        
        <div>
            <form action="{{ route('guru.monitoring.updateStatus', $schedule->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status operasional server ujian ini?')">
                @csrf
                @method('PATCH')
                @if($schedule->status == 'aktif')
                    <input type="hidden" name="status" value="selesai">
                    <button type="submit" style="background: #cd0000; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-power-off"></i> Tutup Server (Force Stop)
                    </button>
                @else
                    <input type="hidden" name="status" value="aktif">
                    <button type="submit" style="background: #10b981; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-play"></i> Buka Server Kembali
                    </button>
                @endif
            </form>
        </div>
    </div>

    {{-- TABEL MONITORING LIVE SISWA --}}
    <div class="content-box" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 30px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 15px 20px; color: #475569; font-size: 13px;">No</th>
                    <th style="padding: 15px 20px; color: #475569; font-size: 13px;">Siswa</th>
                    <th style="padding: 15px 20px; color: #475569; font-size: 13px; text-align: center;">Status</th>
                    <th style="padding: 15px 20px; color: #475569; font-size: 13px; text-align: center;">Progress</th>
                    <th style="padding: 15px 20px; color: #475569; font-size: 13px; text-align: center;">Waktu Mandiri Siswa</th>
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

                    // Oldest() mengambil rekaman paling awal di database sebagai penanda mutlak siswa mulai mengerjakan
                    $firstAnswer = \App\Models\StudentAnswer::where('schedule_id', $schedule->id)
                                    ->where('user_id', $student->id)
                                    ->oldest()
                                    ->first();
                                    
                    $waktuMulai = $firstAnswer ? $firstAnswer->created_at->toISOString() : null;
                @endphp
                <tr id="student-row-{{ $student->id }}" style="border-bottom: 1px solid #f1f5f9; background: {{ $student->total_pelanggaran > 0 ? '#fff5f5' : 'transparent' }}; transition: background 0.5s ease;">
                    <td style="padding: 15px 20px; color: #64748b;">{{ $index + 1 }}</td>
                    <td style="padding: 15px 20px;">
                        <div id="student-name-{{ $student->id }}" style="font-weight: 700; color: #1e293b;">{{ $student->name }}</div>
                        <div style="font-size: 11px; color: #94a3b8;">NIS: {{ $student->nis ?? '-' }}</div>
                    </td>
                    <td id="status-badge-{{ $student->id }}" style="padding: 15px 20px; text-align: center;">
                        @if($student->total_pelanggaran > 0)
                            <span style="background: #fee2e2; color: #ef4444; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800;">🛑 DISKUALIFIKASI</span>
                        @elseif($student->is_logged_in == 1)
                            <span style="background: #dcfce7; color: #15803d; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800;">✍️ MENGERJAKAN</span>
                        @else
                            <span style="background: #f1f5f9; color: #64748b; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800;">💤 SELESAI / OFF</span>
                        @endif
                    </td>
                    <td id="progress-container-{{ $student->id }}" style="padding: 15px 20px; text-align: center;">
                        <div style="width: 100px; background: #e2e8f0; height: 8px; border-radius: 10px; margin: 0 auto 4px;">
                            <div id="progress-bar-{{ $student->id }}" style="width: {{ $persentase }}%; background: {{ $persentase == 100 ? '#10b981' : '#3b82f6' }}; height: 100%; border-radius: 10px;"></div>
                        </div>
                        <span id="progress-text-{{ $student->id }}" style="font-size: 11px; font-weight: 700; color: #475569;">{{ $student->total_dijawab }} / {{ $totalSoal }} Soal ({{ $persentase }}%)</span>
                    </td>
                    
                    {{-- TIMER MANDIRI PER SISWA --}}
                    <td style="padding: 15px 20px; text-align: center; font-weight: bold; font-family: monospace; font-size: 14px; color: #334155;">
                        <span class="student-timer" id="timer-siswa-{{ $student->id }}" data-start="{{ $waktuMulai }}">
                            {{ $waktuMulai ? '--:--:--' : 'Belum Mulai' }}
                        </span>
                    </td>

                    <td id="pelanggaran-text-{{ $student->id }}" style="padding: 15px 20px; text-align: center; font-weight: bold;">
                        @if($student->total_pelanggaran > 0)
                            <span style="color: #ef4444;"><i class="fas fa-triangle-exclamation"></i> Terdeteksi Melanggar</span>
                        @else
                            <span style="color: #94a3b8;">-</span>
                        @endif
                    </td>
                    <td style="padding: 15px 20px; text-align: center;">
                        <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                            {{-- AKSI 1: RESET TOTAL (MULAI ULANG DARI NOL) --}}
                            <form action="{{ route('guru.monitoring.reset', [$schedule->id, $student->id]) }}" method="POST" onsubmit="return confirm('⚠️ PERINGATAN: Mereset akses siswa ini akan MENGHAPUS SEMUA JAWABAN ujiannya dan mengulang pengerjaan dari 0%. Lanjutkan?')">
                                @csrf
                                <button type="submit" style="background: #f59e0b; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600;">
                                    <i class="fas fa-arrow-rotate-left"></i> Reset & Ulang
                                </button>
                            </form>

                            {{-- AKSI 2: FORCE SUBMIT --}}
                            <div id="btn-force-container-{{ $student->id }}">
                                @if($student->is_logged_in == 1 || $student->total_pelanggaran > 0)
                                <form action="{{ route('guru.monitoring.forceSubmit', [$schedule->id, $student->id]) }}" method="POST" onsubmit="return confirm('Paksa selesaikan pengerjaan siswa ini?')">
                                    @csrf
                                    <button type="submit" style="background: #1e293b; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600;">
                                        <i class="fas fa-check-double"></i> Selesai Paksa
                                    </button>
                                </form>
                                @else
                                <button disabled style="background: #cbd5e1; color: #94a3b8; border: none; padding: 6px 12px; border-radius: 6px; cursor: not-allowed; display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600;">
                                    <i class="fas fa-check"></i> Sudah Selesai
                                </button>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- FEED LOGS NOTIFIKASI PELANGGARAN TERAKHIR --}}
    <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.03); border: 1px solid #e2e8f0;">
        <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 16px; color: #1e293b; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-clock-rotate-left" style="color: #64748b;"></i> 10 Riwayat Aktivitas/Pelanggaran Terbaru
        </h3>
        <div id="activity-feed-container" style="max-height: 250px; overflow-y: auto; padding-right: 5px;">
            @forelse($recentLogs as $log)
                <div class="feed-item" style="padding: 12px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="background: #fee2e2; color: #ef4444; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-circle-exclamation" style="font-size: 11px;"></i>
                        </span>
                        <div>
                            <strong>{{ $log->user->name }}</strong> 
                            <span style="color: #ef4444; font-weight: 600;">: {{ $log->details }}</span>
                        </div>
                    </div>
                    <span style="color: #94a3b8; font-size: 11px;">{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</span>
                </div>
            @empty
                <div id="empty-feed-placeholder" style="text-align: center; color: #94a3b8; padding: 20px 0; font-size: 13px;">
                    <i class="fas fa-shield-halved" style="font-size: 24px; display: block; margin-bottom: 8px; color: #cbd5e1;"></i>
                    Belum ada riwayat pelanggaran terdeteksi di ruangan ini. Aman terendali!
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- SCRIPT VITE & WEBSOCKET LISTENER REAL-TIME --}}
@vite(['resources/js/app.js'])

<script>
    const totalSoalServer = parseInt("{{ $totalSoalJadwal }}");
    const durasiMenitServer = parseInt("{{ $schedule->durasi ?? 90 }}");

    window.addEventListener('DOMContentLoaded', () => {
        if (window.Echo) {
            window.Echo.channel("exam-monitoring.{{ $schedule->id }}")
                .listen('.ExamAktivitas', (data) => {
                    console.log("Sinyal Real-time Masuk:", data);
                    
                    const row = document.getElementById(`student-row-${data.studentId}`);
                    const badge = document.getElementById(`status-badge-${data.studentId}`);
                    const txtPelanggaran = document.getElementById(`pelanggaran-text-${data.studentId}`);
                    const btnContainer = document.getElementById(`btn-force-container-${data.studentId}`);
                    const studentTimerElem = document.getElementById(`timer-siswa-${data.studentId}`);
                    
                    const statMengerjakan = document.getElementById('stat-mengerjakan');
                    const statMelanggar = document.getElementById('stat-melanggar');
                    const statSelesai = document.getElementById('stat-selesai');

                    const sNameElem = document.getElementById(`student-name-${data.studentId}`);
                    const studentName = sNameElem ? sNameElem.innerText : `Siswa ID ${data.studentId}`;
                    
                    // 🔄 ACTION 1: RESET TOTAL (Siswa Mulai Ulang dari 0%)
                    if (data.actionType === 'RESET_AKSES') {
                        if(row) row.style.background = 'transparent';
                        if(badge) badge.innerHTML = `<span style="background: #dcfce7; color: #15803d; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800;">✍️ MENGERJAKAN</span>`;
                        if(txtPelanggaran) txtPelanggaran.innerHTML = `<span style="color: #94a3b8;">-</span>`;
                        
                        // Kembalikan UI Progress ke 0% murni
                        const progressBar = document.getElementById(`progress-bar-${data.studentId}`);
                        const progressText = document.getElementById(`progress-text-${data.studentId}`);
                        if(progressBar) progressBar.style.width = "0%";
                        if(progressText) progressText.innerText = `0 / ${totalSoalServer} Soal (0%)`;
                        
                        // Bersihkan penanda waktu mulai individual
                        if(studentTimerElem) {
                            studentTimerElem.removeAttribute('data-start');
                            studentTimerElem.innerText = 'Belum Mulai';
                            studentTimerElem.style.color = '#334155';
                        }
                        
                        // Sinkronisasi live update kotak statistik summary atas
                        if(statMelanggar) statMelanggar.innerText = Math.max(0, parseInt(statMelanggar.innerText) - 1);
                        if(statMengerjakan) statMengerjakan.innerText = parseInt(statMengerjakan.innerText) + 1;
                    } 
                    
                    // 🛑 ACTION 2: SELESAI PAKSA (FORCE SUBMIT)
                    else if (data.actionType === 'FORCE_SUBMIT') {
                        if(row) row.style.background = 'transparent';
                        if(badge) badge.innerHTML = `<span style="background: #f1f5f9; color: #64748b; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800;">💤 SELESAI / OFF</span>`;
                        if(txtPelanggaran) txtPelanggaran.innerHTML = `<span style="color: #94a3b8;">-</span>`;
                        if(btnContainer) btnContainer.innerHTML = `<button disabled style="background: #cbd5e1; color: #94a3b8; border: none; padding: 6px 12px; border-radius: 6px; cursor: not-allowed; display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600;"><i class="fas fa-check"></i> Sudah Selesai</button>`;
                        
                        if(studentTimerElem) {
                            studentTimerElem.innerText = 'Dikunci';
                            studentTimerElem.style.color = '#64748b';
                        }

                        // Sinkronisasi live update kotak statistik summary atas
                        if(statMengerjakan) statMengerjakan.innerText = Math.max(0, parseInt(statMengerjakan.innerText) - 1);
                        if(statSelesai) statSelesai.innerText = parseInt(statSelesai.innerText) + 1;
                    } 
                    
                    // 🛑 ACTION 3: PELANGGARAN MASUK (Fokus Layar Belah / Laci Notifikasi di HP)
                    else if (data.actionType === 'PELANGGARAN') {
                        if(row) row.style.background = '#fff5f5';
                        if(badge) badge.innerHTML = `<span style="background: #fee2e2; color: #ef4444; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800;">🛑 DISKUALIFIKASI</span>`;
                        if(txtPelanggaran) txtPelanggaran.innerHTML = `<span style="color: #ef4444;"><i class="fas fa-triangle-exclamation"></i> Terdeteksi Melanggar</span>`;
                        
                        // Sinkronisasi live update kotak statistik summary atas
                        if(statMengerjakan) statMengerjakan.innerText = Math.max(0, parseInt(statMengerjakan.innerText) - 1);
                        if(statMelanggar) statMelanggar.innerText = parseInt(statMelanggar.innerText) + 1;

                        // Injeksi teks laporan live log aktivitas bawah murni dari HP siswa
                        const feedContainer = document.getElementById('activity-feed-container');
                        const placeholder = document.getElementById('empty-feed-placeholder');
                        if(placeholder) placeholder.remove();

                        const newItem = document.createElement('div');
                        newItem.className = "feed-item";
                        newItem.style = "padding: 12px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; font-size: 13px; background: #fff5f5;";
                        newItem.innerHTML = `
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="background: #fee2e2; color: #ef4444; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-circle-exclamation" style="font-size: 11px;"></i>
                                </span>
                                <div>
                                    <strong>${studentName}</strong> 
                                    <span style="color: #ef4444; font-weight: 600;">: ${data.message || 'Membuka Split Screen / Jendela Aplikasi Melayang.'}</span>
                                </div>
                            </div>
                            <span style="color: #94a3b8; font-size: 11px;">Baru Saja</span>
                        `;
                        feedContainer.insertBefore(newItem, feedContainer.firstChild);
                    }
                });
        }
    });

    // =========================================================================
    // ⏳ ENGINE JAVASCRIPT: LOGIKA HITUNG MUNDUR MANDIRI PER INDIVIDU SISWA
    // =========================================================================
    setInterval(function() {
        const now = new Date().getTime();
        const timerElements = document.querySelectorAll('.student-timer');

        timerElements.forEach(elem => {
            const startIsoString = elem.getAttribute('data-start');
            
            // Kondisi jika siswa baru masuk token dan belum mengisi jawaban soal nomor satu
            if (!startIsoString || startIsoString.trim() === "") {
                return; 
            }

            // Kondisi jika status pengerjaan siswa sudah dibekukan/dikunci paksa oleh pengawas
            if (elem.innerText === 'Dikunci' || elem.innerText === 'WAKTU HABIS') {
                return;
            }

            const waktuMulaiSiswa = new Date(startIsoString).getTime();
            const waktuSelesaiSiswa = waktuMulaiSiswa + (durasiMenitServer * 60 * 1000);
            const sisaWaktu = waktuSelesaiSiswa - now;

            if (sisaWaktu <= 0) {
                elem.innerHTML = "WAKTU HABIS";
                elem.style.color = "#ef4444";
                return;
            }

            const hours = Math.floor((sisaWaktu % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((sisaWaktu % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((sSafeWaktu = sisaWaktu % (1000 * 60)) / 1000);

            elem.innerHTML = 
                String(hours).padStart(2, '0') + ":" + 
                String(minutes).padStart(2, '0') + ":" + 
                String(seconds).padStart(2, '0');
                
            // Transisi teks berubah kuning orange jika sisa waktu kuis siswa menipis di bawah 5 menit
            if (sisaWaktu < 5 * 60 * 1000) {
                elem.style.color = "#f59e0b";
            } else {
                elem.style.color = "#10b981";
            }
        });
    }, 1000);
</script>
@endsection