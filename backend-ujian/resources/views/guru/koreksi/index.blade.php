@extends('layouts.app')

@section('content')
<div class="content-box" style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;">
    
    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h3 style="margin: 0; color: #0f172a; font-weight: 700; font-size: 24px;">Manajemen Nilai Akhir</h3>
            <p style="margin: 5px 0 0 0; color: #64748b; font-size: 14px;">Mata Pelajaran: <strong>{{ $schedule->subject->nama_mapel }}</strong></p>
        </div>
        <a href="{{ route('guru.koreksi.list') }}" style="text-decoration: none; color: #475569; font-weight: 600;">← Kembali</a>
    </div>

    {{-- Panel Pengaturan Bobot & Ekspor --}}
    <div style="background: #f8fafc; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 30px;">
        <div style="display: grid; grid-template-columns: 1fr 250px; gap: 30px; align-items: start;">
            
            {{-- Form Simpan Bobot (Untuk Database/Siswa Mobile) --}}
            <form action="{{ route('guru.koreksi.storeWeight', $schedule->id) }}" method="POST">
                @csrf
                <div style="display: flex; gap: 15px; align-items: flex-end;">
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 11px; font-weight: 800; color: #475569; margin-bottom: 8px;">BOBOT PG (%)</label>
                        <input type="number" name="weight_pg" value="{{ $schedule->weight_pg ?? 60 }}" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-weight: 700;">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 11px; font-weight: 800; color: #475569; margin-bottom: 8px;">BOBOT ESSAY (%)</label>
                        <input type="number" name="weight_essay" value="{{ $schedule->weight_essay ?? 40 }}" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-weight: 700;">
                    </div>
                    <button type="submit" style="background: #1e293b; color: white; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 13px;">
                        💾 Simpan Bobot
                    </button>
                </div>
                <small style="display: block; margin-top: 10px; color: #64748b;">*Bobot ini akan digunakan untuk menghitung nilai akhir di aplikasi mobile siswa.</small>
            </form>

            {{-- Tombol Ekspor (Berdasarkan bobot yang tersimpan) --}}
            <div style="border-left: 2px solid #e2e8f0; padding-left: 30px;">
                <label style="display: block; font-size: 11px; font-weight: 800; color: #475569; margin-bottom: 8px;">LAPORAN</label>
                <a href="{{ route('guru.koreksi.export', $schedule->id) }}" style="display: flex; align-items: center; justify-content: center; gap: 10px; background: #15803d; color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: 700; text-decoration: none; box-shadow: 0 4px 6px -1px rgba(21, 128, 61, 0.2);">
                    <i class="fas fa-file-excel"></i> Ekspor Rekap Excel
                </a>
            </div>
        </div>
    </div>

    {{-- Tabel Monitoring Siswa --}}
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
            <thead>
                <tr style="background: #f8fafc; text-align: left;">
                    <th style="padding: 16px; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 13px; font-weight: 700;">SISWA</th>
                    <th style="padding: 16px; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 13px; font-weight: 700;">PROGRES KOREKSI</th>
                    <th style="padding: 16px; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 13px; text-align: center; font-weight: 700;">STATUS</th>
                    <th style="padding: 16px; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 13px; text-align: center; font-weight: 700;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr>
                    {{-- Konten tabel sama seperti sebelumnya --}}
                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; font-weight: 700;">{{ $student->name }}</td>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9;">
                        @php $percent = ($student->total_essay > 0) ? ($student->graded_essay / $student->total_essay) * 100 : 0; @endphp
                        <div style="width: 100%; background: #f1f5f9; height: 8px; border-radius: 10px; overflow: hidden;">
                            <div style="width: {{ $percent }}%; background: {{ $percent == 100 ? '#15803d' : '#c91313' }}; height: 100%;"></div>
                        </div>
                    </td>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                        <span style="padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; {{ $percent == 100 ? 'background: #dcfce7; color: #15803d;' : 'background: #fff1f2; color: #c91313;' }}">
                            {{ $percent == 100 ? 'SELESAI' : 'PENDING' }}
                        </span>
                    </td>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                        <a href="{{ route('guru.koreksi.show', $student->id) }}?schedule_id={{ $schedule->id }}" style="text-decoration: none; background: #1e293b; color: white; padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 600;">Periksa</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection