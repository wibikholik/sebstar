@extends('layouts.app')
@section('title', 'Pilih Jadwal Koreksi')

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

    /* Filter Bar Area */
    .filter-panel {
        background: #fafafa !important;
        padding: 16px 20px !important;
        border-radius: 12px !important;
        border: 1px solid #edf0f5 !important;
        margin-bottom: 25px !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
    }

    /* Dropdown Filter Premium */
    .custom-select {
        padding: 10px 16px !important;
        border: 1px solid #edf0f5 !important;
        background: #ffffff !important;
        border-radius: 8px !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        color: #1e1e2f !important;
        outline: none !important;
        min-width: 220px;
        cursor: pointer;
        transition: all 0.2s !important;
    }

    .custom-select:focus {
        border-color: #cd0000 !important;
        box-shadow: 0 0 0 3px rgba(205, 0, 0, 0.1) !important;
    }

    /* ================= BUTTON DESIGN ================= */
    .btn-action-premium {
        background: linear-gradient(135deg, #cd0000 0%, #950000 100%) !important;
        color: #ffffff !important;
        border: none !important;
        padding: 10px 20px !important;
        border-radius: 30px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        box-shadow: 0 5px 15px rgba(205, 0, 0, 0.25) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        text-decoration: none !important;
        white-space: nowrap !important;
    }

    .btn-action-premium:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 22px rgba(205, 0, 0, 0.4) !important;
        filter: brightness(1.1) !important;
    }

    /* ================= TABLE DESIGN ================= */
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
        font-weight: 700 !important;
        letter-spacing: 0.5px !important;
        text-transform: uppercase !important;
    }

    .custom-table td {
        padding: 16px !important;
        border-bottom: 1px solid #edf0f5 !important;
        color: #1e1e2f !important;
        vertical-align: middle !important;
    }

    .custom-table tbody tr {
        transition: all 0.2s ease !important;
    }

    .custom-table tbody tr:hover {
        background-color: rgba(230, 57, 70, 0.01) !important;
    }

    /* Badges Status Premium */
    .badge-status-success {
        padding: 5px 12px !important;
        border-radius: 20px !important;
        font-size: 10px !important;
        font-weight: 800 !important;
        background: rgba(46, 204, 113, 0.1) !important;
        color: #2ecc71 !important;
        letter-spacing: 0.5px !important;
        display: inline-block !important;
    }

    .badge-status-pending {
        padding: 5px 12px !important;
        border-radius: 20px !important;
        font-size: 10px !important;
        font-weight: 800 !important;
        background: rgba(230, 57, 70, 0.1) !important;
        color: #cd0000 !important;
        letter-spacing: 0.5px !important;
        display: inline-block !important;
    }

    .badge-duration {
        background: #edf0f5 !important;
        color: #1e1e2f !important;
        padding: 5px 12px !important;
        border-radius: 6px !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        display: inline-block;
    }
</style>

@section('content')
<div class="content-box">
    
    {{-- Header Komponen --}}
    <div style="margin-bottom: 25px; border-bottom: 2px solid #edf0f5; padding-bottom: 15px;">
        <h3 style="margin: 0; color: #1e1e2f; font-weight: 700; font-size: 22px; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-file-signature" style="color: #cd0000;"></i> Koreksi & Penilaian Essay
        </h3>
        <p style="margin: 5px 0 0 0; color: #6a6a7a; font-size: 14px;">Pilih jadwal pelaksanaan ujian untuk memulai atau memantau pemeriksaan jawaban siswa.</p>
    </div>

    {{-- Filter Panel Berdasarkan Status --}}
    <div class="filter-panel">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-filter" style="color: #6a6a7a; font-size: 14px;"></i>
            <span style="font-size: 13px; font-weight: 700; color: #1e1e2f;">Filter Status Pemeriksaan:</span>
        </div>
        <div>
            <select id="statusFilter" class="custom-select" onchange="filterTableByStatus()">
                <option value="all">✨ Tampilkan Semua Jadwal</option>
                <option value="selesai">✅ Sudah Diperiksa (Selesai)</option>
                <option value="belum">⏳ Belum Selesai / Pending</option>
            </select>
        </div>
    </div>

    {{-- Tabel Jadwal Premium --}}
    <div class="table-responsive">
        <table class="custom-table" id="scheduleTable">
            <thead>
                <tr>
                    <th style="width: 35%;">Mata Pelajaran</th>
                    <th style="width: 25%;">Tanggal Ujian</th>
                    <th style="text-align: center; width: 15%;">Durasi</th>
                    <th style="text-align: center; width: 12%;">Status</th>
                    <th style="text-align: center; width: 13%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $s)
                    @php
                        // Logika pengecekan status (Menghitung apakah semua essay milik siswa di jadwal ini sudah diperiksa)
                        // Catatan: Variabel pembantu ini dapat disesuaikan dengan struktur eager loading data dari Controller Anda
                        $is_finished = false;
                        if (isset($s->total_essay_count) && isset($s->graded_essay_count)) {
                            $is_finished = ($s->total_essay_count > 0) && ($s->total_essay_count === $s->graded_essay_count);
                        } else {
                            // Alternatif fallback jika belum disiapkan dari backend Controller
                            $is_finished = false; 
                        }
                    @endphp
                <tr data-status="{{ $is_finished ? 'selesai' : 'belum' }}">
                    <td>
                        <div style="font-weight: 700; color: #1e1e2f; font-size: 15px;">{{ $s->subject->nama_mapel ?? 'Mapel Tidak Diketahui' }}</div>
                        <div style="font-size: 11px; color: #6a6a7a; margin-top: 3px; font-weight: 600;">ID JADWAL: #{{ $s->id }}</div>
                    </td>
                    <td>
                        <div style="font-size: 14px; color: #1e1e2f; font-weight: 600; display: flex; align-items: center; gap: 6px;">
                            <i class="far fa-calendar-alt" style="color: #cd0000;"></i> {{ \Carbon\Carbon::parse($s->tanggal_ujian)->translatedFormat('d F Y') }}
                        </div>
                    </td>
                    <td style="text-align: center;">
                        <span class="badge-duration">
                            {{ $s->durasi }} Menit
                        </span>
                    </td>
                    <td style="text-align: center;">
                        @if($is_finished)
                            <span class="badge-status-success">SELESAI</span>
                        @else
                            <span class="badge-status-pending">BELUM SELESAI</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <a href="{{ route('guru.koreksi.index', $s->id) }}" class="btn-action-premium">
                            Buka <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr id="emptyRow">
                    <td colspan="5" style="padding: 50px; text-align: center; color: #6a6a7a; font-weight: 600; font-size: 14px;">
                        <i class="fas fa-folder-open" style="display: block; font-size: 30px; color: #edf0f5; margin-bottom: 10px;"></i>
                        Belum ada jadwal ujian yang ditugaskan saat ini.
                    </td>
                </tr>
                @endforelse
                
                {{-- Baris Cadangan Jika Hasil Filter Kosong --}}
                <tr id="noMatchRow" style="display: none;">
                    <td colspan="5" style="padding: 50px; text-align: center; color: #6a6a7a; font-weight: 600; font-size: 14px;">
                        <i class="fas fa-search" style="display: block; font-size: 30px; color: #edf0f5; margin-bottom: 10px;"></i>
                        Tidak ada jadwal ujian dengan status tersebut.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- JavaScript Filter Client-Side Instan Tanpa Reload Halaman --}}
<script>
    function filterTableByStatus() {
        const filterValue = document.getElementById('statusFilter').value;
        const tableRows = document.querySelectorAll('#scheduleTable tbody tr:not(#emptyRow):not(#noMatchRow)');
        const noMatchRow = document.getElementById('noMatchRow');
        let visibleCount = 0;

        tableRows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            
            if (filterValue === 'all' || rowStatus === filterValue) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Tampilkan pesan kosong jika semua baris tersembunyi karena filter
        if (tableRows.length > 0) {
            if (visibleCount === 0) {
                noMatchRow.style.display = '';
            } else {
                noMatchRow.style.display = 'none';
            }
        }
    }
</script>
@endsection