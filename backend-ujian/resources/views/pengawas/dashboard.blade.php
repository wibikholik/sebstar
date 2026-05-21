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
    .pengawas-wrapper {

        background:
            rgba(255,255,255,0.72);

        backdrop-filter: blur(12px);

        -webkit-backdrop-filter: blur(12px);

        padding: 28px;

        border-radius: 24px;

        border: 1px solid rgba(255,255,255,0.7);

        box-shadow:
            0 10px 30px rgba(0,0,0,0.05);

        overflow: hidden;
    }

    /* ================= TITLE ================= */
    .page-title {

        font-size: 24px;

        font-weight: 800;

        color: #1e293b;

        margin-bottom: 25px;

        display: flex;

        align-items: center;

        gap: 10px;
    }

    .page-title i {
        color: #cd0000;
    }

    /* ================= GRID ================= */
    .monitor-grid {

        display: grid;

        grid-template-columns:
            repeat(auto-fill, minmax(320px, 1fr));

        gap: 24px;
    }

    /* ================= CARD ================= */
    .monitor-card {

        background:
            rgba(255,255,255,0.88);

        border-radius: 22px;

        padding: 22px;

        border: 1px solid #edf0f5;

        position: relative;

        overflow: hidden;

        transition:
            all 0.35s ease;

        box-shadow:
            0 6px 18px rgba(0,0,0,0.04);
    }

    /* STRIP MERAH */
    .monitor-card::before {

        content: '';

        position: absolute;

        left: 0;
        top: 0;

        width: 6px;
        height: 100%;

        background: #cd0000;
    }

    .monitor-card:hover {

        transform:
            translateY(-6px);

        box-shadow:
            0 15px 35px rgba(205,0,0,0.12);

        border-color:
            rgba(205,0,0,0.25);
    }

    /* ================= SUBJECT ================= */
    .monitor-subject {

        font-size: 22px;

        font-weight: 800;

        color: #1e293b;

        margin: 0;
    }

    .monitor-class {

        margin-top: 6px;

        font-size: 14px;

        color: #64748b;
    }

    .monitor-class strong {
        color: #1e293b;
    }

    /* ================= INFO ================= */
    .monitor-info {

        margin-top: 18px;

        background:
            rgba(248,250,252,0.9);

        border-radius: 14px;

        padding: 14px;

        display: flex;

        flex-direction: column;

        gap: 10px;

        border: 1px solid #edf0f5;
    }

    .info-item {

        display: flex;

        align-items: center;

        gap: 10px;

        color: #475569;

        font-size: 14px;

        font-weight: 600;
    }

    .info-item i {
        color: #cd0000;
        width: 18px;
    }

    /* ================= BUTTON ================= */
    .btn-monitor {

        display: flex;

        justify-content: center;

        align-items: center;

        gap: 8px;

        margin-top: 22px;

        width: 100%;

        box-sizing: border-box;

        text-align: center;

        background:
            linear-gradient(
                135deg,
                #cd0000 0%,
                #950000 100%
            );

        color: white;

        padding: 15px 18px;

        border-radius: 16px;

        text-decoration: none;

        font-weight: 700;

        font-size: 13px;

        letter-spacing: 0.3px;

        box-shadow:
            0 8px 20px rgba(205,0,0,0.25);

        transition:
            all 0.3s ease;

        overflow: hidden;

        white-space: nowrap;
    }

    .btn-monitor:hover {

        transform:
            translateY(-3px);

        box-shadow:
            0 12px 28px rgba(205,0,0,0.35);

        filter: brightness(1.05);

        color: white;
    }

    .btn-monitor i {

        flex-shrink: 0;

        transition:
            transform 0.3s ease;
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

        background:
            rgba(255,255,255,0.7);

        border-radius: 24px;

        border: 1px dashed #d1d5db;
    }

    .empty-state i {

        font-size: 55px;

        color: #cbd5e1;

        margin-bottom: 15px;
    }

    .empty-state p {

        margin: 0;

        color: #64748b;

        font-size: 16px;

        font-weight: 600;
    }

    /* ================= RESPONSIVE ================= */
    @media (max-width: 768px) {

        .pengawas-wrapper {
            padding: 20px;
        }

        .monitor-grid {
            grid-template-columns: 1fr;
        }

        .monitor-card {
            padding: 20px;
        }

        .monitor-subject {
            font-size: 20px;
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

<div class="pengawas-wrapper">

    <h3 class="page-title">
        <i class="fas fa-shield-alt"></i>
        Penugasan Pengawasan Ruang
    </h3>

    <div class="monitor-grid">

        @forelse($schedules as $s)

            <div class="monitor-card">

                <h4 class="monitor-subject">
                    {{ $s->subject->nama_mapel }}
                </h4>

                <p class="monitor-class">
                    Kelas:
                    <strong>
                        {{ $s->classroom->nama_kelas }}
                    </strong>
                </p>

                <div class="monitor-info">

                    <div class="info-item">

                        <i class="fas fa-calendar-alt"></i>

                        {{ date('d M Y', strtotime($s->tanggal_ujian)) }}

                    </div>

                    <div class="info-item">

                        <i class="fas fa-clock"></i>

                        {{ \Carbon\Carbon::parse($s->jam_mulai)->format('H:i') }}
                        -
                        {{ \Carbon\Carbon::parse($s->jam_selesai)->format('H:i') }}
                        WIB

                    </div>

                </div>

                <a
                    href="{{ route('pengawas.monitoring.show', $s->id) }}"
                    class="btn-monitor"
                >

                    <span>
                        Masuk Ruang Monitor Live
                    </span>

                    <i class="fas fa-arrow-right"></i>

                </a>

            </div>

        @empty

            <div class="empty-state">

                <i class="fas fa-folder-open"></i>

                <p>
                    Belum ada jadwal ujian aktif
                    yang harus diawasi hari ini.
                </p>

            </div>

        @endforelse

    </div>

</div>

@endsection