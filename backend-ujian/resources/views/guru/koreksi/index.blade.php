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
        
        {{-- Pesan Error Validasi Bobot --}}
        @if ($errors->has('weight_error'))
            <div style="background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border: 1px solid #fecaca;">
                <i class="fas fa-exclamation-triangle"></i> {{ $errors->first('weight_error') }}
            </div>
        @endif

        <div style="display: grid; grid-template-columns: 1fr 260px; gap: 30px; align-items: start; flex-wrap: wrap;">
            
            {{-- Form Simpan Bobot --}}
            <form action="{{ route('guru.koreksi.storeWeight', $schedule->id) }}" method="POST" style="margin: 0;">
                @csrf
                <div style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 120px;">
                        <label style="display: block; font-size: 11px; font-weight: 800; color: #1e1e2f; margin-bottom: 8px; letter-spacing: 0.5px;">BOBOT PG (%)</label>
                        <input type="number" name="weight_pg" value="{{ $schedule->weight_pg ?? 60 }}" class="custom-input-number weight-input" min="0" max="100">
                    </div>
                    <div style="flex: 1; min-width: 120px;">
                        <label style="display: block; font-size: 11px; font-weight: 800; color: #1e1e2f; margin-bottom: 8px; letter-spacing: 0.5px;">BOBOT ESSAY (%)</label>
                        <input type="number" name="weight_essay" value="{{ $schedule->weight_essay ?? 40 }}" class="custom-input-number weight-input" min="0" max="100">
                    </div>
                    <button type="submit" class="btn-action-premium">
                        <i class="fas fa-save"></i> Simpan Bobot
                    </button>
                </div>
                <small id="weight-info" style="display: block; margin-top: 10px; color: #6a6a7a; font-size: 12px; font-style: italic;">
                    *Total bobot saat ini: <span id="total-weight">{{ ($schedule->weight_pg ?? 60) + ($schedule->weight_essay ?? 40) }}</span>% (Harus 100%)
                </small>
            </form>

            {{-- Tombol Ekspor Excel --}}
            <div style="border-left: 2px solid #edf0f5; padding-left: 30px;">
                <label style="display: block; font-size: 11px; font-weight: 800; color: #1e1e2f; margin-bottom: 8px; letter-spacing: 0.5px;">LAPORAN REKAPITULASI</label>
                <a href="{{ route('guru.koreksi.export', $schedule->id) }}" class="btn-excel-premium">
                    <i class="fas fa-file-excel"></i> Ekspor Rekap Excel
                </a>
            </div>
        </div>
    </div>

    {{-- Tabel Monitoring Siswa --}}
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Nama Siswa</th>
                    <th>Progres Koreksi</th>
                    <th style="text-align: center;">Skor PG</th>
                    <th style="text-align: center;">Skor Essay</th>
                    <th style="text-align: center;">NILAI AKHIR</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $bobotPg = $schedule->weight_pg ?? 60;
                    $bobotEssay = $schedule->weight_essay ?? 40;
                @endphp
                @forelse($students as $student)
                <tr>
                    <td><div style="font-weight: 700; color: #1e1e2f; font-size: 15px;">{{ $student->name }}</div></td>
                    <td>
                        @php 
                            $percent = ($student->total_essay > 0) ? ($student->graded_essay / $student->total_essay) * 100 : 0; 
                        @endphp
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="progress-container" style="flex: 1;">
                                <div style="width: {{ $percent }}%; background: {{ $percent == 100 ? '#2ecc71' : '#cd0000' }}; height: 100%; transition: width 0.5s ease;"></div>
                            </div>
                            <span style="font-size: 11px; font-weight: 700; color: #6a6a7a; width: 55px; text-align: right;">
                                {{ $student->graded_essay }}/{{ $student->total_essay }}
                            </span>
                        </div>
                    </td>
                    <td style="text-align: center; font-weight: 700; color: #475569;">
                        {{ round(($student->total_pg > 0) ? ($student->benar_pg / $student->total_pg) * 100 : 0, 2) }}
                    </td>
                    <td style="text-align: center; font-weight: 700; color: #475569;">{{ round($student->avg_skor_essay ?? 0, 2) }}</td>
                    <td style="text-align: center;">
                        @php
                            $scorePg = ($student->total_pg > 0) ? ($student->benar_pg / $student->total_pg) * 100 : 0;
                            $scoreEssay = $student->avg_skor_essay ?? 0;
                            $nilaiAkhir = ($scorePg * ($bobotPg / 100)) + ($scoreEssay * ($bobotEssay / 100));
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
                <tr><td colspan="6" style="text-align: center; padding: 40px; color: #6a6a7a;">Belum ada data siswa.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    const pgInput = document.querySelector('input[name="weight_pg"]');
    const essayInput = document.querySelector('input[name="weight_essay"]');
    const totalWeightText = document.getElementById('total-weight');
    const weightInfo = document.getElementById('weight-info');

    function checkTotal() {
        let total = parseInt(pgInput.value || 0) + parseInt(essayInput.value || 0);
        totalWeightText.innerText = total;
        
        if(total !== 100) {
            pgInput.style.borderColor = "#cd0000";
            essayInput.style.borderColor = "#cd0000";
            weightInfo.style.color = "#cd0000";
            weightInfo.style.fontWeight = "bold";
        } else {
            pgInput.style.borderColor = "#2ecc71";
            essayInput.style.borderColor = "#2ecc71";
            weightInfo.style.color = "#2ecc71";
            weightInfo.style.fontWeight = "bold";
        }
    }

    pgInput.addEventListener('input', checkTotal);
    essayInput.addEventListener('input', checkTotal);
    // Jalankan sekali saat load
    checkTotal();
</script>

<style>
    /* Tambahkan style yang sudah ada sebelumnya di sini... */
    body { background-color: #f4f5f9 !important; }
    .content-box { background: #ffffff !important; border-radius: 16px !important; padding: 25px !important; margin-bottom: 30px !important; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04) !important; }
    .weight-panel { background: #fafafa !important; padding: 22px !important; border-radius: 12px !important; border: 1px solid #edf0f5 !important; margin-bottom: 30px !important; }
    .custom-input-number { width: 100% !important; padding: 10px 14px !important; border: 2px solid #edf0f5 !important; border-radius: 8px !important; font-weight: 700 !important; font-size: 14px !important; outline: none !important; transition: all 0.2s !important; }
    .btn-action-premium { background: linear-gradient(135deg, #cd0000 0%, #950000 100%) !important; color: #ffffff !important; border: none !important; padding: 11px 22px !important; border-radius: 30px !important; font-size: 13px !important; font-weight: 600 !important; cursor: pointer !important; }
    .btn-excel-premium { background: linear-gradient(135deg, #15803d 0%, #166534 100%) !important; color: #ffffff !important; padding: 11px 22px !important; border-radius: 30px !important; text-decoration: none !important; display: inline-flex; align-items: center; gap: 8px; }
    .btn-table-action-manage { background: #1e1e2f !important; color: #ffffff !important; padding: 7px 16px !important; border-radius: 8px !important; text-decoration: none !important; }
    .btn-back-link { text-decoration: none !important; color: #6a6a7a !important; font-weight: 700 !important; }
    .custom-table { width: 100% !important; border-collapse: separate !important; border-spacing: 0 !important; }
    .custom-table th { padding: 16px !important; border-bottom: 2px solid #edf0f5 !important; font-size: 12px !important; font-weight: 700; text-transform: uppercase !important; }
    .custom-table td { padding: 16px !important; border-bottom: 1px solid #edf0f5 !important; }
    .progress-container { width: 100% !important; background: #edf0f5 !important; height: 8px !important; border-radius: 10px !important; overflow: hidden !important; }
    .final-score-badge { display: inline-block !important; padding: 6px 16px !important; border-radius: 8px !important; font-size: 15px !important; font-weight: 800 !important; }
    .score-complete { background: rgba(46, 204, 113, 0.15) !important; color: #27ae60 !important; border: 1px solid rgba(46, 204, 113, 0.3) !important; }
    .score-progress { background: rgba(241, 196, 15, 0.15) !important; color: #d35400 !important; border: 1px solid rgba(241, 196, 15, 0.3) !important; }
</style>
@endsection