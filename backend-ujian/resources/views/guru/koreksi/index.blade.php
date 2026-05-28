@extends('layouts.app')
@section('title', 'Manajemen Nilai Akhir')

@section('content')
<div class="content-box">
    
    {{-- Header Terintegrasi --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <div>
            <h3 style="margin: 0; color: #1e1e2f; font-weight: 700; font-size: 22px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-calculator" style="color: #cd0000;"></i> Manajemen Nilai Akhir
            </h3>
            <p style="margin: 5px 0 0 0; color: #6a6a7a; font-size: 14px;">
                Mata Pelajaran: <span style="font-weight: 700; color: #1e1e2f;">{{ $schedule->subject->nama_mapel ?? $schedule->subject->name }}</span>
            </p>
        </div>
        <a href="{{ route('guru.koreksi.list') }}" class="btn-back-link">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Panel Pengaturan Bobot & Ekspor --}}
    <div class="weight-panel">
        <div style="display: grid; grid-template-columns: 1fr 260px; gap: 30px; align-items: start; flex-wrap: wrap;">
            
            {{-- Form Simpan Bobot --}}
            <form action="{{ route('guru.koreksi.storeWeight', $schedule->id) }}" method="POST" style="margin: 0;">
                @csrf
                <div style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 120px;">
                        <label style="display: block; font-size: 11px; font-weight: 800; color: #1e1e2f; margin-bottom: 8px; letter-spacing: 0.5px;">BOBOT PG (%)</label>
                        <input type="number" name="weight_pg" value="{{ $schedule->weight_pg ?? 60 }}" class="custom-input-number" min="0" max="100">
                    </div>
                    <div style="flex: 1; min-width: 120px;">
                        <label style="display: block; font-size: 11px; font-weight: 800; color: #1e1e2f; margin-bottom: 8px; letter-spacing: 0.5px;">BOBOT ESSAY (%)</label>
                        <input type="number" name="weight_essay" value="{{ $schedule->weight_essay ?? 40 }}" class="custom-input-number" min="0" max="100">
                    </div>
                    <button type="submit" class="btn-action-premium">
                        <i class="fas fa-save"></i> Simpan Bobot
                    </button>
                </div>
                <small style="display: block; margin-top: 10px; color: #6a6a7a; font-size: 12px; font-style: italic;">
                    *Sistem rumus disesuaikan murni dengan kalkulasi dokumen Excel.
                </small>
            </form>

            {{-- Tombol Ekspor Excel Laporan --}}
            <div style="border-left: 2px solid #edf0f5; padding-left: 30px;">
                <label style="display: block; font-size: 11px; font-weight: 800; color: #1e1e2f; margin-bottom: 8px; letter-spacing: 0.5px;">LAPORAN REKAPITULASI</label>
                <a href="{{ route('guru.koreksi.export', $schedule->id) }}" class="btn-excel-premium">
                    <i class="fas fa-file-excel"></i> Ekspor Rekap Excel
                </a>
            </div>
        </div>
    </div>

    {{-- Tabel Monitoring Siswa Premium --}}
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Nama Siswa</th>
                    <th style="width: 20%;">Progres Koreksi</th>
                    <th style="text-align: center; width: 12%;">Skor PG</th>
                    <th style="text-align: center; width: 12%;">Skor Essay</th>
                    <th style="text-align: center; width: 16%;">NILAI AKHIR</th>
                    <th style="text-align: center; width: 15%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $bobotPg = $schedule->weight_pg ?? 60;
                    $bobotEssay = $schedule->weight_essay ?? 40;
                @endphp
                @forelse($students as $student)
                <tr>
                    <td>
                        <div style="font-weight: 700; color: #1e1e2f; font-size: 15px;">{{ $student->name }}</div>
                    </td>
                    <td>
                        @php 
                            $percent = ($student->total_essay > 0) ? ($student->graded_essay / $student->total_essay) * 100 : 0; 
                        @endphp
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="progress-container" style="flex: 1;">
                                <div style="width: {{ $percent }}%; background: {{ $percent == 100 ? '#2ecc71' : '#cd0000' }}; height: 100%; transition: width 0.5s ease;"></div>
                            </div>
                            <span style="font-size: 11px; font-weight: 700; color: #6a6a7a; width: 55px; text-align: right;">
                                {{ $student->graded_essay }}/{{ $student->total_essay }} Soal
                            </span>
                        </div>
                    </td>
                    
                    {{-- NILAI PG MURNI: (Benar / Total) * 100 --}}
                    <td style="text-align: center; font-weight: 700; color: #475569;">
                        @php
                            $scorePgMurni = ($student->total_pg > 0) ? ($student->benar_pg / $student->total_pg) * 100 : 0;
                        @endphp
                        {{ round($scorePgMurni, 2) }}
                        <div style="font-size: 10px; color: #94a3b8; font-weight: 600; margin-top: 2px;">(B: {{ $student->benar_pg }}/{{ $student->total_pg }})</div>
                    </td>
                    
                    {{-- NILAI ESSAY MURNI: Rata-rata Skor (Skala 0-100) --}}
                    <td style="text-align: center; font-weight: 700; color: #475569;">
                        @php
                            $scoreEssayMurni = $student->avg_skor_essay ?? 0;
                        @endphp
                        {{ round($scoreEssayMurni, 2) }}
                    </td>
                    
                    {{-- KALKULASI NILAI AKHIR BERDASARKAN BOBOT DENGAN ADIL --}}
                    <td style="text-align: center;">
                        @php
                            $nilaiAkhir = ($scorePgMurni * ($bobotPg / 100)) + ($scoreEssayMurni * ($bobotEssay / 100));
                        @endphp
                        <div class="final-score-badge {{ $percent == 100 ? 'score-complete' : 'score-progress' }}">
                            {{ round($nilaiAkhir, 2) }}
                        </div>
                    </td>
                    <td style="text-align: center;">
                        <a href="{{ route('guru.koreksi.show', $student->id) }}?schedule_id={{ $schedule->id }}" class="btn-table-action-manage">
                            <i class="fas fa-search"></i> Periksa
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #6a6a7a;">
                        <i class="fas fa-users-slash" style="font-size: 24px; display: block; margin-bottom: 10px;"></i>
                        Belum ada data lembar pengerjaan siswa pada jadwal ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    body {
        background-color: #f4f5f9 !important;
        background-image: 
            radial-gradient(rgba(230, 57, 70, 0.15) 1.5px, transparent 1.5px), 
            linear-gradient(135deg, #fceade 0%, #f4f5f9 50%, #ffffff 100%) !important;
        background-size: 24px 24px, 100% 100% !important;
        background-attachment: fixed !important;
    }
    .content-box {
        background: #ffffff !important;
        border-radius: 16px !important;
        padding: 25px !important;
        margin-bottom: 30px !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04) !important;
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
    }
    .weight-panel {
        background: #fafafa !important;
        padding: 22px !important;
        border-radius: 12px !important;
        border: 1px solid #edf0f5 !important;
        margin-bottom: 30px !important;
    }
    .custom-input-number {
        width: 100% !important;
        padding: 10px 14px !important;
        border: 1px solid #edf0f5 !important;
        background: #ffffff !important;
        border-radius: 8px !important;
        font-weight: 700 !important;
        font-size: 14px !important;
        color: #1e1e2f !important;
        outline: none !important;
        transition: all 0.2s !important;
    }
    .custom-input-number:focus {
        border-color: #cd0000 !important;
        box-shadow: 0 0 0 3px rgba(205, 0, 0, 0.1) !important;
    }
    .btn-action-premium {
        background: linear-gradient(135deg, #cd0000 0%, #950000 100%) !important;
        color: #ffffff !important;
        border: none !important;
        padding: 11px 22px !important;
        border-radius: 30px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        box-shadow: 0 5px 15px rgba(205, 0, 0, 0.25) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        white-space: nowrap !important;
    }
    .btn-action-premium:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 22px rgba(205, 0, 0, 0.4) !important;
    }
    .btn-excel-premium {
        background: linear-gradient(135deg, #15803d 0%, #166534 100%) !important;
        color: #ffffff !important;
        border: none !important;
        padding: 11px 22px !important;
        border-radius: 30px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        box-shadow: 0 5px 15px rgba(21, 128, 61, 0.2) !important;
        transition: all 0.3s ease !important;
        text-decoration: none !important;
        white-space: nowrap !important;
        width: 100% !important;
    }
    .btn-excel-premium:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 22px rgba(21, 128, 61, 0.3) !important;
    }
    .btn-table-action-manage {
        background: #1e1e2f !important;
        color: #ffffff !important;
        padding: 7px 16px !important;
        border-radius: 8px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
        transition: background 0.2s !important;
    }
    .btn-table-action-manage:hover {
        background: #cd0000 !important;
    }
    .btn-back-link {
        text-decoration: none !important;
        color: #6a6a7a !important;
        font-weight: 700 !important;
        font-size: 14px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
    }
    .btn-back-link:hover {
        color: #cd0000 !important;
    }
    .table-responsive {
        overflow-x: auto !important;
        border-radius: 12px !important;
        border: 1px solid #edf0f5 !important;
    }
    .custom-table {
        width: 100% !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        background: #ffffff !important;
    }
    .custom-table thead tr {
        background: #fafafa !important;
    }
    .custom-table th {
        padding: 16px !important;
        border-bottom: 2px solid #edf0f5 !important;
        color: #1e1e2f !important;
        font-size: 12px !important;
        font-weight: 700;
        text-transform: uppercase !important;
    }
    .custom-table td {
        padding: 16px !important;
        border-bottom: 1px solid #edf0f5 !important;
        color: #1e1e2f !important;
        vertical-align: middle !important;
    }
    .progress-container {
        width: 100% !important;
        background: #edf0f5 !important;
        height: 8px !important;
        border-radius: 10px !important;
        overflow: hidden !important;
    }
    .final-score-badge {
        display: inline-block !important;
        padding: 6px 16px !important;
        border-radius: 8px !important;
        font-size: 15px !important;
        font-weight: 800 !important;
        text-align: center !important;
        min-width: 65px !important;
    }
    .score-complete {
        background: rgba(46, 204, 113, 0.15) !important;
        color: #27ae60 !important;
        border: 1px solid rgba(46, 204, 113, 0.3) !important;
    }
    .score-progress {
        background: rgba(241, 196, 15, 0.15) !important;
        color: #d35400 !important;
        border: 1px solid rgba(241, 196, 15, 0.3) !important;
    }
</style>
@endsection