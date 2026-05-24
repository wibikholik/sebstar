@extends('layouts.app')

@section('title', 'Jadwal & Input Soal')

@section('content')
<div class="stats-grid-premium">
    @forelse($schedules as $item)
        <div class="schedule-card-premium">
            {{-- Bagian Atas Kartu (Mata Pelajaran & Status) --}}
            <div class="card-top-premium">
                <span class="subject-tag-premium">
                    <i class="fas fa-book"></i> {{ $item->subject->name }}
                </span>
                <span class="status-badge-premium status-{{ Str::slug($item->status) }}">
                    {{ $item->status }}
                </span>
            </div>

            {{-- Nama Rombel Kelas --}}
            <h2 class="classroom-title-premium">{{ $item->classroom->name }}</h2>
            
            {{-- Detail Pelaksanaan Ujian --}}
            <div class="schedule-details-premium">
                <div class="detail-item-premium">
                    <span class="detail-icon-premium">📅</span> 
                    {{ \Carbon\Carbon::parse($item->tanggal_ujian)->translatedFormat('l, d M Y') }}
                </div>
                <div class="detail-item-premium">
                    <span class="detail-icon-premium">⏰</span> 
                    {{ $item->jam_mulai }} - {{ $item->jam_selesai }} ({{ $item->durasi }} Menit)
                </div>
                <div class="detail-item-premium font-token-premium">
                    <span class="detail-icon-premium">🔑</span> 
                    Token: <strong class="token-text-premium">{{ $item->token }}</strong>
                </div>
            </div>

            <hr class="card-divider-premium">

            {{-- Bagian Bawah Kartu (Jumlah Soal & Tombol Aksi) --}}
            <div class="card-bottom-premium">
                <div class="questions-count-premium">
                    <i class="fas fa-file-alt"></i> 
                    <strong>{{ \App\Models\Question::where('schedule_id', $item->id)->count() }}</strong> Soal Terinput
                </div>
                <a href="{{ route('guru.ujian-terpusat.manage', $item->id) }}" class="btn-manage-premium">
                    <i class="fas fa-sliders-h"></i> Kelola Soal
                </a>
            </div>
        </div>
    @empty
        {{-- Tampilan State Kosong --}}
        <div class="empty-schedule-state">
            <div class="empty-icon-wrapper">
                <i class="fas fa-calendar-times"></i>
            </div>
            <p class="empty-text-premium">Belum ada jadwal ujian untuk Anda saat ini.</p>
        </div>
    @endforelse
</div>

<style>
    /* Grid Utama Responsif */
    .stats-grid-premium {
        display: grid !important;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)) !important;
        gap: 20px !important;
        padding: 10px 0 !important;
    }

    /* Desain Kartu Jadwal Premium SEBSTAR */
    .schedule-card-premium {
        text-align: left !important;
        background: #ffffff !important;
        padding: 24px !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 12px rgba(30, 30, 47, 0.05) !important;
        border: 1px solid rgba(0, 0, 0, 0.04) !important;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
    }

    .schedule-card-premium:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 12px 24px rgba(205, 0, 0, 0.08) !important;
        border-color: rgba(205, 0, 0, 0.15) !important;
    }

    /* Komponen Atas Kartu */
    .card-top-premium {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        gap: 10px !important;
    }

    .subject-tag-premium {
        font-size: 12px !important;
        font-weight: 800 !important;
        color: #cd0000 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
    }

    /* Badges Status Dinamis */
    .status-badge-premium {
        font-size: 11px !important;
        padding: 4px 12px !important;
        border-radius: 30px !important;
        font-weight: 700 !important;
        text-transform: capitalize !important;
        background: #f1f5f9 !important;
        color: #475569 !important;
    }
    
    /* Menyelaraskan style warna badge jika status bervariasi (Opsional) */
    .status-badge-premium.status-aktif {
        background: #ecfdf5 !important;
        color: #059669 !important;
    }

    /* Judul Kelas */
    .classroom-title-premium {
        font-size: 22px !important;
        margin: 14px 0 12px 0 !important;
        font-weight: 800 !important;
        color: #1e1e2f !important;
    }

    /* Informasi Detail */
    .schedule-details-premium {
        color: #475569 !important;
        font-size: 13.5px !important;
        line-height: 1.8 !important;
        font-weight: 600 !important;
    }

    .detail-item-premium {
        display: flex !important;
        align-items: center !important;
        margin-bottom: 4px !important;
    }

    .detail-icon-premium {
        width: 24px !important;
        display: inline-block !important;
        font-size: 14px !important;
    }

    .font-token-premium {
        margin-top: 6px !important;
    }

    .token-text-premium {
        color: #1e1e2f !important;
        background: #f8fafc !important;
        padding: 2px 8px !important;
        border-radius: 6px !important;
        border: 1px dashed #cbd5e1 !important;
        font-family: monospace !important;
        font-size: 14px !important;
    }

    /* Pembatas Kartu */
    .card-divider-premium {
        border: 0 !important;
        border-top: 1px dashed #e2e8f0 !important;
        margin: 18px 0 !important;
    }

    /* Komponen Bawah Kartu */
    .card-bottom-premium {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        gap: 10px !important;
        margin-top: auto !important;
    }

    .questions-count-premium {
        font-size: 13px !important;
        font-weight: 700 !important;
        color: #64748b !important;
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
    }

    .questions-count-premium strong {
        color: #1e1e2f !important;
        font-size: 15px !important;
    }

    /* Tombol Kelola Soal SEBSTAR Premium */
    .btn-manage-premium {
        background: linear-gradient(135deg, #cd0000 0%, #950000 100%) !important;
        color: #ffffff !important;
        text-decoration: none !important;
        padding: 10px 18px !important;
        border-radius: 20px !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        box-shadow: 0 4px 12px rgba(205, 0, 0, 0.2) !important;
        transition: all 0.2s ease !important;
    }

    .btn-manage-premium:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 16px rgba(205, 0, 0, 0.3) !important;
        filter: brightness(1.1) !important;
    }

    /* State Kosong */
    .empty-schedule-state {
        text-align: center !important;
        padding: 60px 20px !important;
        background: #ffffff !important;
        border-radius: 16px !important;
        grid-column: span 3 !important;
        box-shadow: 0 4px 12px rgba(30, 30, 47, 0.03) !important;
        border: 1px dashed #cbd5e1 !important;
    }

    .empty-icon-wrapper {
        font-size: 40px !important;
        color: #94a3b8 !important;
        margin-bottom: 12px !important;
    }

    .empty-text-premium {
        color: #64748b !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        margin: 0 !important;
    }
</style>