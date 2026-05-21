@extends('layouts.app')

@section('title', 'Monitoring Pengawas - ' . $schedule->subject->nama_mapel)

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
    .monitor-wrapper {

        padding: 10px;
    }

    /* ================= ALERT ================= */
    .alert-success {

        background:
            rgba(16,185,129,0.12);

        border:
            1px solid rgba(16,185,129,0.2);

        color: #065f46;

        padding: 16px;

        border-radius: 14px;

        margin-bottom: 24px;

        font-weight: 700;

        backdrop-filter: blur(10px);
    }

    /* ================= HEADER ================= */
    .monitor-header {

        display: flex;

        justify-content: space-between;

        align-items: center;

        margin-bottom: 28px;

        gap: 20px;

        flex-wrap: wrap;
    }

    .monitor-title {

        margin: 0;

        color: #1e293b;

        font-size: 30px;

        font-weight: 900;
    }

    .monitor-subtitle {

        margin: 6px 0 0;

        color: #64748b;

        font-size: 14px;

        font-weight: 600;
    }

    .header-actions {

        display: flex;

        gap: 12px;

        flex-wrap: wrap;
    }

    .btn-header {

        display: flex;

        align-items: center;

        gap: 8px;

        padding: 12px 18px;

        border-radius: 14px;

        text-decoration: none;

        font-weight: 700;

        transition: 0.3s ease;
    }

    .btn-refresh {

        background:
            rgba(255,255,255,0.7);

        border:
            1px solid #e2e8f0;

        color: #475569;
    }

    .btn-refresh:hover {

        transform:
            translateY(-2px);

        background: #fff;
    }

    .btn-back {

        background:
            linear-gradient(
                135deg,
                #cd0000 0%,
                #950000 100%
            );

        color: white;

        box-shadow:
            0 8px 20px rgba(205,0,0,0.22);
    }

    .btn-back:hover {

        transform:
            translateY(-3px);

        box-shadow:
            0 12px 28px rgba(205,0,0,0.32);

        color: white;
    }

    /* ================= SUMMARY ================= */
    .summary-grid {

        display: grid;

        grid-template-columns:
            repeat(auto-fit, minmax(220px, 1fr));

        gap: 22px;

        margin-bottom: 30px;
    }

    .summary-card {

        background:
            rgba(255,255,255,0.78);

        backdrop-filter: blur(10px);

        border-radius: 22px;

        padding: 24px;

        border:
            1px solid rgba(255,255,255,0.7);

        box-shadow:
            0 8px 22px rgba(0,0,0,0.04);

        transition: 0.3s ease;
    }

    .summary-card:hover {

        transform:
            translateY(-5px);

        box-shadow:
            0 15px 35px rgba(205,0,0,0.10);
    }

    .summary-label {

        font-size: 12px;

        font-weight: 800;

        text-transform: uppercase;

        color: #64748b;

        margin-bottom: 10px;

        letter-spacing: 0.5px;
    }

    .summary-value {

        font-size: 34px;

        font-weight: 900;

        color: #1e293b;
    }

    .border-blue {
        border-left: 5px solid #3b82f6;
    }

    .border-green {
        border-left: 5px solid #10b981;
    }

    .border-yellow {
        border-left: 5px solid #f59e0b;
    }

    .border-red {
        border-left: 5px solid #ef4444;
    }

    /* ================= CONTROL PANEL ================= */
    .control-panel {

        background:
            rgba(255,255,255,0.75);

        backdrop-filter: blur(12px);

        border-radius: 24px;

        padding: 24px;

        margin-bottom: 30px;

        display: flex;

        flex-wrap: wrap;

        gap: 40px;

        border:
            1px solid rgba(255,255,255,0.7);

        box-shadow:
            0 8px 22px rgba(0,0,0,0.04);
    }

    .control-item span {

        display: block;
    }

    .control-label {

        font-size: 11px;

        font-weight: 800;

        text-transform: uppercase;

        margin-bottom: 6px;

        color: #64748b;

        letter-spacing: 0.5px;
    }

    .token-box {

        font-size: 24px;

        font-family: monospace;

        font-weight: 900;

        background: #1e293b;

        color: #fff;

        padding: 5px 14px;

        border-radius: 10px;

        display: inline-block;
    }

    .status-active {

        color: #15803d;

        font-size: 20px;

        font-weight: 800;
    }

    .countdown {

        font-size: 22px;

        font-weight: 900;

        color: #1e293b;
    }

    /* ================= TABLE ================= */
    .table-wrapper {

        background:
            rgba(255,255,255,0.78);

        backdrop-filter: blur(12px);

        border-radius: 24px;

        overflow: hidden;

        border:
            1px solid rgba(255,255,255,0.7);

        box-shadow:
            0 10px 28px rgba(0,0,0,0.05);
    }

    table {

        width: 100%;

        border-collapse: collapse;
    }

    thead tr {

        background:
            rgba(248,250,252,0.95);

        border-bottom:
            2px solid #e2e8f0;
    }

    th {

        padding: 18px 20px;

        color: #475569;

        font-size: 13px;

        text-transform: uppercase;

        letter-spacing: 0.5px;

        font-weight: 800;
    }

    td {

        padding: 18px 20px;

        border-bottom:
            1px solid #f1f5f9;
    }

    tbody tr {

        transition: 0.3s ease;
    }

    tbody tr:hover {

        background:
            rgba(205,0,0,0.03);
    }

    .student-name {

        font-weight: 800;

        color: #1e293b;
    }

    .student-nis {

        font-size: 11px;

        color: #94a3b8;

        margin-top: 4px;
    }

    /* ================= BADGES ================= */
    .badge {

        padding: 6px 14px;

        border-radius: 999px;

        font-size: 11px;

        font-weight: 800;

        display: inline-block;
    }

    .badge-danger {

        background: #fee2e2;

        color: #ef4444;
    }

    .badge-success {

        background: #dcfce7;

        color: #15803d;
    }

    .badge-secondary {

        background: #f1f5f9;

        color: #64748b;
    }

    /* ================= PROGRESS ================= */
    .progress-track {

        width: 110px;

        background: #e2e8f0;

        height: 8px;

        border-radius: 999px;

        margin: 0 auto 6px;
    }

    .progress-fill {

        height: 100%;

        border-radius: 999px;
    }

    .progress-text {

        font-size: 11px;

        font-weight: 700;

        color: #475569;
    }

    /* ================= ACTION ================= */
    .btn-reset {

        width: 36px;

        height: 36px;

        border-radius: 10px;

        border: none;

        background: #f59e0b;

        color: white;

        cursor: pointer;

        transition: 0.3s ease;
    }

    .btn-reset:hover {

        transform:
            translateY(-2px);

        box-shadow:
            0 8px 18px rgba(245,158,11,0.28);
    }

    /* ================= RESPONSIVE ================= */
    @media (max-width: 1024px) {

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            min-width: 1000px;
        }

    }

</style>

@section('content')

<div class="monitor-wrapper">

    {{-- ALERT --}}
    @if(session('success'))

        <div class="alert-success">
            {{ session('success') }}
        </div>

    @endif

    {{-- HEADER --}}
    <div class="monitor-header">

        <div>

            <h2 class="monitor-title">
                🛡️ Ruang Pengawas Live:
                {{ $schedule->subject->nama_mapel }}
            </h2>

            <p class="monitor-subtitle">

                Kelas:
                <strong>
                    {{ $schedule->classroom->nama_kelas }}
                </strong>

                |

                Pengawas:
                {{ auth()->user()->name }}

            </p>

        </div>

        <div class="header-actions">

            <button
                onclick="window.location.reload()"
                class="btn-header btn-refresh"
            >

                <i class="fas fa-sync-alt"></i>

                Refresh

            </button>

            <a
                href="{{ route('pengawas.dashboard') }}"
                class="btn-header btn-back"
            >

                Kembali Dashboard

            </a>

        </div>

    </div>

    {{-- SUMMARY --}}
    <div class="summary-grid">

        <div class="summary-card border-blue">

            <div class="summary-label">
                Total Siswa
            </div>

            <div
                id="stat-total"
                class="summary-value"
            >

                {{ $students->count() }}

            </div>

        </div>

        <div class="summary-card border-green">

            <div class="summary-label">
                Sudah Selesai
            </div>

            <div
                id="stat-selesai"
                class="summary-value"
            >

                {{ $students->where('is_logged_in', 0)->where('total_dijawab', '>=', $schedule->questions_count ?? 1)->count() }}

            </div>

        </div>

        <div class="summary-card border-yellow">

            <div class="summary-label">
                Sedang Aktif
            </div>

            <div
                id="stat-aktif"
                class="summary-value"
            >

                {{ $students->where('is_logged_in', 1)->where('total_pelanggaran', 0)->count() }}

            </div>

        </div>

        <div class="summary-card border-red">

            <div class="summary-label">
                Terdeteksi Melanggar
            </div>

            <div
                id="stat-melanggar"
                class="summary-value"
                style="color:#ef4444;"
            >

                {{ $students->where('total_pelanggaran', '>', 0)->count() }}

            </div>

        </div>

    </div>

    {{-- CONTROL PANEL --}}
    <div class="control-panel">

        <div class="control-item">

            <span class="control-label">
                Token Ujian
            </span>

            <span class="token-box">
                {{ $schedule->token }}
            </span>

        </div>

        <div class="control-item">

            <span class="control-label">
                Status Server
            </span>

            <span class="status-active">

                ● {{ strtoupper($schedule->status) }}

            </span>

        </div>

        <div class="control-item">

            <span class="control-label">
                Waktu Tersisa
            </span>

            <span
                id="countdown"
                class="countdown"
            >

                --:--:--

            </span>

        </div>

    </div>

    {{-- TABLE --}}
    <div class="table-wrapper">

        <table>

            <thead>

                <tr>

                    <th>No</th>

                    <th>
                        NIS & Nama Siswa
                    </th>

                    <th style="text-align:center;">
                        Status
                    </th>

                    <th style="text-align:center;">
                        Progress
                    </th>

                    <th style="text-align:center;">
                        Pelanggaran
                    </th>

                    <th style="text-align:center;">
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody id="student-table-body">

                @foreach($students as $index => $student)

                @php

                    $totalSoal =
                        $schedule->questions_count ??
                        count($schedule->questions ?? []);

                    $totalSoal =
                        $totalSoal > 0
                        ? $totalSoal
                        : 1;

                    $persentase =
                        round(
                            ($student->total_dijawab / $totalSoal) * 100
                        );

                @endphp

                <tr
                    id="student-row-{{ $student->id }}"
                    style="
                        background:
                        {{ $student->total_pelanggaran > 0 ? '#fff5f5' : 'transparent' }}
                    "
                >

                    <td>
                        {{ $index + 1 }}
                    </td>

                    <td>

                        <div class="student-name">
                            {{ $student->name }}
                        </div>

                        <div class="student-nis">
                            NIS:
                            {{ $student->nis ?? '-' }}
                        </div>

                    </td>

                    <td
                        class="student-status-cell"
                        style="text-align:center;"
                    >

                        @if($student->total_pelanggaran > 0)

                            <span class="badge badge-danger">
                                🛑 DISKUALIFIKASI
                            </span>

                        @elseif($student->is_logged_in == 1)

                            <span class="badge badge-success">
                                ✍️ MENGERJAKAN
                            </span>

                        @else

                            <span class="badge badge-secondary">
                                💤 BELUM LOGIN
                            </span>

                        @endif

                    </td>

                    <td
                        class="student-progress-cell"
                        style="text-align:center;"
                    >

                        <div class="progress-track">

                            <div
                                class="progress-bar-fill progress-fill"
                                style="
                                    width: {{ $persentase }}%;
                                    background:
                                    {{ $persentase == 100 ? '#10b981' : '#3b82f6' }};
                                "
                            ></div>

                        </div>

                        <span class="progress-text">

                            {{ $student->total_dijawab }}
                            /
                            {{ $totalSoal }}
                            Soal
                            ({{ $persentase }}%)

                        </span>

                    </td>

                    <td
                        class="student-violation-cell"
                        style="
                            text-align:center;
                            font-weight:800;
                            color:
                            {{ $student->total_pelanggaran > 0 ? '#ef4444' : '#64748b' }}
                        "
                    >

                        {{ $student->total_pelanggaran > 0 ? '⚠️ ' . $student->total_pelanggaran . 'x Keluar App' : '-' }}

                    </td>

                    <td style="text-align:center;">

                        <form
                            action="{{ route('pengawas.monitoring.reset', [$schedule->id, $student->id]) }}"
                            method="POST"
                            onsubmit="return confirm('Yakin reset login siswa ini?')"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="btn-reset"
                            >

                                <i class="fas fa-undo"></i>

                            </button>

                        </form>

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection