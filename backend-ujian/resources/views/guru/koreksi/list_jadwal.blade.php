@extends('layouts.app')
@section('title', 'Pilih Jadwal Koreksi')

@section('content')
<div class="content-wrapper-sebstar" style="padding: 20px;">
    {{-- Header --}}
    <div style="margin-bottom: 25px;">
        <h3 style="margin: 0; color: #1e1e2f; font-weight: 700;">Koreksi & Penilaian Essay</h3>
    </div>

    {{-- Filter Panel --}}
    <div style="background: #fff; padding: 20px; border-radius: 12px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
        <span style="font-weight: 700; color: #1e1e2f;">Filter Status:</span>
        <select id="statusFilter" class="form-control" onchange="filterTableByStatus()" style="width: 200px; padding: 8px; border-radius: 6px;">
            <option value="all">✨ Semua Jadwal</option>
            <option value="selesai">✅ Selesai</option>
            <option value="belum">⏳ Belum Selesai</option>
        </select>
    </div>

    {{-- Tabel Jadwal --}}
    <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); overflow-x: auto;">
        <table id="scheduleTable" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #edf0f5; text-align: left;">
                    <th style="padding: 15px;">Mata Pelajaran</th>
                    <th style="padding: 15px;">Tanggal</th>
                    <th style="padding: 15px; text-align: center;">Progres</th>
                    <th style="padding: 15px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $s)
                    @php
                        $is_finished = ($s->total_essay_count > 0) && ($s->total_essay_count === $s->graded_essay_count);
                    @endphp
                    <tr data-status="{{ $is_finished ? 'selesai' : 'belum' }}" style="border-bottom: 1px solid #edf0f5;">
                        <td style="padding: 15px;">
                            <div style="font-weight: 700;">{{ $s->subject->nama_mapel ?? 'Mapel Tidak Diketahui' }}</div>
                            <div style="font-size: 12px; color: #6a6a7a;">{{ $s->class->nama_kelas ?? '-' }}</div>
                        </td>
                        <td style="padding: 15px;">{{ \Carbon\Carbon::parse($s->tanggal_ujian)->translatedFormat('d M Y') }}</td>
                        <td style="padding: 15px; text-align: center;">
                            <span style="padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 800; background: {{ $is_finished ? '#dcfce7' : '#fee2e2' }}; color: {{ $is_finished ? '#166534' : '#991b1b' }};">
                                {{ $is_finished ? 'SELESAI' : 'PENDING' }}
                            </span>
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <a href="{{ route('guru.koreksi.index', $s->id) }}" style="background: #cd0000; color: #fff; padding: 8px 15px; border-radius: 20px; text-decoration: none; font-size: 12px;">Periksa</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center; padding: 20px;">Belum ada jadwal.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- SCRIPT BIASA (Ditaruh langsung di bawah sini) --}}
<script>
    function filterTableByStatus() {
        var filterValue = document.getElementById("statusFilter").value;
        var table = document.getElementById("scheduleTable");
        var tr = table.getElementsByTagName("tr");

        for (var i = 1; i < tr.length; i++) {
            var status = tr[i].getAttribute("data-status");
            if (filterValue === "all" || status === filterValue) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
</script>
@endsection