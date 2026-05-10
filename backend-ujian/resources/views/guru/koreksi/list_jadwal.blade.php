@extends('layouts.app')
@section('title', 'Pilih Jadwal Koreksi')

@section('content')
<div class="content-box" style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;">
    
    {{-- Header --}}
    <div style="margin-bottom: 25px; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px;">
        <h3 style="margin: 0; color: #0f172a; font-weight: 700; font-size: 24px;">Koreksi & Penilaian Essay</h3>
        <p style="margin: 5px 0 0 0; color: #64748b; font-size: 14px;">Pilih jadwal pelaksanaan ujian untuk memulai pemeriksaan jawaban siswa.</p>
    </div>

    {{-- Table --}}
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
            <thead>
                <tr style="background: #f8fafc; text-align: left;">
                    <th style="padding: 16px; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 13px; font-weight: 700;">MATA PELAJARAN</th>
                    <th style="padding: 16px; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 13px; font-weight: 700;">TANGGAL UJIAN</th>
                    <th style="padding: 16px; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 13px; font-weight: 700; text-align: center;">DURASI</th>
                    <th style="padding: 16px; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 13px; font-weight: 700; text-align: center;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $s)
                <tr>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9;">
                        <div style="font-weight: 700; color: #0f172a; font-size: 16px;">{{ $s->subject->nama_mapel ?? 'Mapel Tidak Diketahui' }}</div>
                        <div style="font-size: 12px; color: #94a3b8; margin-top: 2px;">ID Jadwal: #{{ $s->id }}</div>
                    </td>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9;">
                        <div style="font-size: 14px; color: #1e293b; font-weight: 500;">📅 {{ \Carbon\Carbon::parse($s->tanggal_ujian)->translatedFormat('d F Y') }}</div>
                    </td>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                        <span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700;">
                            {{ $s->durasi }} Menit
                        </span>
                    </td>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                        <a href="{{ route('guru.koreksi.index', $s->id) }}" style="text-decoration: none; background: #c91313; color: white; padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(201, 19, 19, 0.2); display: inline-block;">
                            Buka Daftar Siswa →
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="padding: 40px; text-align: center; color: #64748b;">Belum ada jadwal ujian yang ditugaskan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection