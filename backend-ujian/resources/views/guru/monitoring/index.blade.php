@extends('layouts.app')
@section('title', 'Monitoring Ujian (Guru)')

<style>
    /* Background Premium dengan Efek Polkadot Grid Modern */
    body {
        background-color: #f4f5f9 !important;
        background-image: 
            radial-gradient(rgba(230, 57, 70, 0.15) 1.5px, transparent 1.5px), 
            linear-gradient(135deg, #fceade 0%, #f4f5f9 50%, #ffffff 100%) !important;
        background-size: 24px 24px, 100% 100% !important;
        background-attachment: fixed !important;
    }

    /* Pembungkus Konten Utama */
    .content-box {
        background: #ffffff !important;
        border-radius: 16px !important;
        padding: 25px !important;
        margin-bottom: 30px !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04) !important;
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
    }

    /* Grid Layout Kartu Tugas */
    .task-grid {
        display: grid !important;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)) !important;
        gap: 20px !important;
    }

    /* Desain Kartu Tugas Premium */
    .task-card {
        border: 1px solid #edf0f5 !important;
        border-radius: 14px !important;
        padding: 22px !important;
        border-top: 5px solid #cd0000 !important;
        background: #ffffff !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01) !important;
        transition: all 0.3s ease !important;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .task-card:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 10px 20px rgba(230, 57, 70, 0.06) !important;
        border-color: rgba(230, 57, 70, 0.2) !important;
    }

    /* Badges Status Premium */
    .badge-status-active {
        padding: 5px 12px !important;
        border-radius: 20px !important;
        font-size: 10px !important;
        font-weight: 800 !important;
        background: rgba(46, 204, 113, 0.1) !important;
        color: #2ecc71 !important;
        letter-spacing: 0.5px !important;
    }

    .badge-status-inactive {
        padding: 5px 12px !important;
        border-radius: 20px !important;
        font-size: 10px !important;
        font-weight: 800 !important;
        background: #edf0f5 !important;
        color: #6a6a7a !important;
        letter-spacing: 0.5px !important;
    }

    .badge-classroom {
        background: #fafafa !important;
        border: 1px solid #edf0f5 !important;
        padding: 4px 10px !important;
        border-radius: 6px !important;
        font-size: 12px !important;
        font-weight: 700;
        color: #1e1e2f !important;
        display: inline-block;
    }

    /* Desain Kotak Token */
    .token-display {
        font-family: monospace !important;
        font-size: 14px !important;
        font-weight: 800 !important;
        color: #cd0000 !important;
        background: rgba(230, 57, 70, 0.04) !important;
        padding: 2px 8px !important;
        border-radius: 4px !important;
        border: 1px dashed rgba(230, 57, 70, 0.3) !important;
    }

    /* Tombol Monitor Aksi */
    .btn-monitor-premium {
        background: #1e1e2f !important;
        color: #ffffff !important;
        border: none !important;
        padding: 12px !important;
        border-radius: 8px !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        cursor: pointer !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        transition: all 0.2s ease !important;
        text-decoration: none !important;
        margin-top: 20px !important;
    }

    .btn-monitor-premium:hover {
        background: linear-gradient(135deg, #cd0000 0%, #950000 100%) !important;
        box-shadow: 0 5px 15px rgba(205, 0, 0, 0.3) !important;
    }
</style>

@section('content')
<div class="content-box">
    
    {{-- Header Komponen --}}
    <div style="margin-bottom: 25px; border-bottom: 2px solid #edf0f5; padding-bottom: 15px;">
        <h3 style="margin: 0; color: #1e1e2f; font-weight: 700; font-size: 22px; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-shield-alt" style="color: #cd0000;"></i> Daftar Tugas Mengawas
        </h3>
        <p style="margin: 5px 0 0 0; color: #6a6a7a; font-size: 14px;">Pilih ruang atau jadwal ujian aktif untuk memantau aktivitas dan integritas pengerjaan siswa secara langsung.</p>
    </div>

    {{-- Grid Kartu Konten --}}
    <div class="task-grid">
        @forelse($schedules as $s)
            <div class="task-card">
                <div>
                    {{-- Judul Mapel & Status --}}
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; margin-bottom: 12px;">
                        <h4 style="margin: 0; font-size: 16px; color: #1e1e2f; font-weight: 700; line-height: 1.4;">
                            {{ $s->subject->nama_mapel }}
                        </h4>
                        @if(($s->status ?? '') == 'aktif')
                            <span class="badge-status-active">AKTIF</span>
                        @else
                            <span class="badge-status-inactive">{{ strtoupper($s->status ?? 'NONAKTIF') }}</span>
                        @endif
                    </div>
                    
                    {{-- Identitas Kelas --}}
                    <div style="margin-bottom: 15px;">
                        <span class="badge-classroom">
                            <i class="fas fa-door-open" style="color: #a0a0b0; margin-right: 4px;"></i> Kelas {{ $s->classroom->nama_kelas }}
                        </span>
                    </div>
                    
                    <hr style="border: 0; border-top: 1px solid #edf0f5; margin: 15px 0;">

                    {{-- Informasi Detail Waktu & Token --}}
                    <div style="font-size: 13px; color: #1e1e2f; line-height: 1.8; font-weight: 600;">
                        <div style="margin-bottom: 4px; color: #6a6a7a;">
                            <i class="far fa-calendar-alt" style="color: #cd0000; width: 18px;"></i> {{ date('d M Y', strtotime($s->tanggal_ujian)) }}
                        </div>
                        <div style="margin-bottom: 4px; color: #6a6a7a;">
                            <i class="far fa-clock" style="color: #cd0000; width: 18px;"></i> {{ $s->jam_mulai }} - {{ $s->jam_selesai }} <span style="font-weight: 700; color: #1e1e2f;">({{ $s->durasi }} mnt)</span>
                        </div>
                        <div style="margin-top: 8px;">
                            <i class="fas fa-key" style="color: #cd0000; width: 18px;"></i> Token Akses: <span class="token-display">{{ $s->token }}</span>
                        </div>
                    </div>
                </div>

                {{-- Link Aksi Navigasi --}}
                <a href="{{ route('guru.monitoring.show', $s->id) }}" class="btn-monitor-premium">
                    <i class="fas fa-desktop"></i> Monitor Kelas Sekarang
                </a>
            </div>
        @empty
            {{-- Tampilan Kosong --}}
            <div style="grid-column: 1/-1; text-align: center; padding: 50px; background: #fafafa; border-radius: 12px; border: 1px dashed #edf0f5; color: #6a6a7a; font-weight: 600;">
                <i class="fas fa-folder-open" style="display: block; font-size: 34px; color: #a0a0b0; margin-bottom: 12px;"></i>
                Anda tidak memiliki jadwal atau tugas mengawas ujian saat ini.
            </div>
        @endforelse
    </div>
</div>
@endsection