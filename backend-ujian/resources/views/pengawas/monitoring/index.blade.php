@extends('layouts.app')

@section('title', 'Tugas Pengawas')

<style>

    /* ================= BACKGROUND ================= */
    body {

        background-color: #f4f5f9 !important;

        background-image:
            radial-gradient(
                rgba(230, 57, 70, 0.12) 1.5px,
                transparent 1.5px
            ),

            linear-gradient(
                135deg,
                #fceade 0%,
                #f4f5f9 50%,
                #ffffff 100%
            ) !important;

        background-size:
            24px 24px,
            100% 100% !important;

        background-attachment: fixed !important;
    }

    /* ================= WRAPPER ================= */
    .content-box {

        background:
            rgba(255,255,255,0.72) !important;

        backdrop-filter: blur(12px);

        -webkit-backdrop-filter: blur(12px);

        padding: 30px !important;

        border-radius: 24px !important;

        border: 1px solid rgba(255,255,255,0.7) !important;

        box-shadow:
            0 10px 30px rgba(0,0,0,0.05) !important;

        overflow: hidden;
    }

    /* ================= HEADER ================= */
    .page-header {

        display: flex;

        align-items: center;

        margin-bottom: 28px;

        gap: 12px;
    }

    .page-icon {

        width: 48px;
        height: 48px;

        border-radius: 14px;

        display: flex;

        align-items: center;

        justify-content: center;

        background:
            rgba(205,0,0,0.10);

        color: #cd0000;

        font-size: 22px;

        box-shadow:
            0 6px 18px rgba(205,0,0,0.10);
    }

    .page-title {

        margin: 0;

        color: #1e1e2f;

        font-weight: 800;

        font-size: 24px;

        letter-spacing: 0.5px;
    }

    /* ================= GRID ================= */
    .schedule-grid {

        display: grid;

        grid-template-columns:
            repeat(auto-fill, minmax(320px, 1fr));

        gap: 24px;
    }

    /* ================= CARD ================= */
    .schedule-card {

        position: relative;

        border:
            1px solid #edf0f5;

        border-radius: 22px;

        padding: 24px;

        background:
            rgba(255,255,255,0.88);

        transition:
            all 0.35s ease;

        box-shadow:
            0 6px 18px rgba(0,0,0,0.04);

        overflow: hidden;
    }

    /* STRIP MERAH */
    .schedule-card::before {

        content: '';

        position: absolute;

        left: 0;
        top: 0;

        width: 6px;
        height: 100%;

        background: #cd0000;
    }

    .schedule-card:hover {

        transform:
            translateY(-6px);

        box-shadow:
            0 15px 35px rgba(205,0,0,0.12) !important;

        border-color:
            rgba(205,0,0,0.25) !important;
    }

    /* ================= CARD HEADER ================= */
    .card-top {

        display: flex;

        justify-content: space-between;

        align-items: flex-start;

        gap: 10px;
    }

    .mapel-title {

        margin: 0;

        color: #1e1e2f;

        font-size: 21px;

        font-weight: 800;

        flex: 1;
    }

    .badge-active {

        background:
            rgba(205,0,0,0.10);

        color: #cd0000;

        padding: 5px 12px;

        border-radius: 999px;

        font-size: 11px;

        font-weight: 800;

        text-transform: uppercase;

        letter-spacing: 0.5px;

        white-space: nowrap;
    }

    /* ================= KELAS ================= */
    .kelas-text {

        color: #6a6a7a;

        font-size: 14px;

        margin-top: 10px;

        font-weight: 500;
    }

    .kelas-text strong {

        color: #1e1e2f;

        font-weight: 700;
    }

    /* ================= INFO BOX ================= */
    .info-box {

        margin-top: 20px;

        background:
            rgba(248,250,252,0.92);

        padding: 15px;

        border-radius: 14px;

        border:
            1px solid #edf0f5;

        display: flex;

        flex-direction: column;

        gap: 10px;
    }

    .info-item {

        display: flex;

        align-items: center;

        gap: 10px;

        font-size: 14px;

        font-weight: 600;

        color: #475569;
    }

    .info-item i {

        color: #cd0000;

        width: 18px;
    }

    /* ================= BUTTON ================= */
    .btn-monitor {

        display: flex !important;

        justify-content: center;

        align-items: center;

        gap: 8px;

        margin-top: 24px;

        width: 100%;

        box-sizing: border-box;

        text-align: center;

        background:
            linear-gradient(
                135deg,
                #cd0000 0%,
                #950000 100%
            );

        color: white !important;

        padding: 15px 18px;

        border-radius: 16px;

        text-decoration: none;

        font-weight: 700;

        font-size: 13px;

        letter-spacing: 0.4px;

        transition:
            all 0.3s ease;

        box-shadow:
            0 8px 20px rgba(205,0,0,0.25);

        overflow: hidden;

        white-space: nowrap;
    }

    .btn-monitor:hover {

        background:
            linear-gradient(
                135deg,
                #b00000 0%,
                #7a0000 100%
            ) !important;

        transform:
            translateY(-3px);

        box-shadow:
            0 12px 28px rgba(205,0,0,0.35) !important;

        color: white !important;
    }

    .btn-monitor i {

        transition:
            transform 0.3s ease;

        flex-shrink: 0;
    }

    .btn-monitor:hover i {

        transform:
            translateX(4px);
    }

    /* ================= EMPTY ================= */
    .empty-state {

        grid-column: 1/-1;

        text-align: center;

        padding: 70px 20px;

        color: #94a3b8;

        background:
            rgba(255,255,255,0.7);

        border-radius: 22px;

        border:
            2px dashed #e2e8f0;
    }

    .empty-icon {

        font-size: 50px;

        display: block;

        margin-bottom: 12px;
    }

    .empty-text {

        margin: 0;

        font-size: 15px;

        font-weight: 600;

        color: #64748b;
    }

    /* ================= RESPONSIVE ================= */
    @media (max-width: 768px) {

        .content-box {
            padding: 22px !important;
        }

        .schedule-grid {
            grid-template-columns: 1fr;
        }

        .mapel-title {
            font-size: 19px;
        }

    }

    @media (max-width: 480px) {

        .page-title {
            font-size: 20px;
        }

        .btn-monitor {

            font-size: 12px;

            padding: 14px 12px;
        }

    }

</style>

@section('content')

<div class="content-box">

    <!-- HEADER -->
    <div class="page-header">

        <div class="page-icon">
            🛡️
        </div>

        <h3 class="page-title">
            Penugasan Pengawasan Ruang
        </h3>

    </div>

    <!-- GRID -->
    <div class="schedule-grid">

        @forelse($schedules as $s)

            <div class="schedule-card">

                <div class="card-top">

                    <h4 class="mapel-title">
                        {{ $s->subject->nama_mapel }}
                    </h4>

                    <span class="badge-active">
                        Aktif
                    </span>

                </div>

                <p class="kelas-text">

                    Kelas:
                    <strong>
                        {{ $s->classroom->nama_kelas }}
                    </strong>

                </p>

                <!-- INFO -->
                <div class="info-box">

                    <div class="info-item">

                        <i class="fas fa-calendar-alt"></i>

                        {{ date('d M Y', strtotime($s->tanggal_ujian)) }}

                    </div>

                    <div class="info-item">

                        <i class="fas fa-clock"></i>

                        Jam:
                        {{ date('H:i', strtotime($s->jam_mulai)) }}
                        -
                        {{ date('H:i', strtotime($s->jam_selesai)) }}
                        WIB

                    </div>

                </div>

                <!-- BUTTON -->
                <a
                    href="{{ route('pengawas.monitoring.show', $s->id) }}"
                    class="btn-monitor"
                >

                    <span>
                        Masuk Ruang Monitor
                    </span>

                    <i class="fas fa-arrow-right"></i>

                </a>

            </div>

        @empty

            <div class="empty-state">

                <span class="empty-icon">
                    📭
                </span>

                <p class="empty-text">

                    Belum ada jadwal yang harus
                    Anda awasi hari ini.

                </p>

            </div>

        @endforelse

    </div>

</div>

@endsection