@extends('layouts.app')
@section('title', 'Jadwal Ujian Saya')

@section('content')
<div class="content-box" style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;">
    
    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h3 style="margin: 0; color: #0f172a; font-weight: 700; font-size: 24px;">Manajemen Jadwal & Kuis</h3>
            <p style="margin: 5px 0 0 0; color: #64748b; font-size: 14px;">Kelola ujian mandiri atau pantau jadwal ujian terpusat dari Admin.</p>
        </div>
        <button onclick="openCreateModal()" style="background: #c91313; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; box-shadow: 0 4px 6px -1px rgba(201, 19, 19, 0.2);">
            + Buat Jadwal Mandiri
        </button>
    </div>

    {{-- Alert Notifikasi --}}
    @if(session('success'))
        <div style="background: #dcfce7; border-left: 4px solid #15803d; color: #15803d; padding: 16px; border-radius: 8px; margin-bottom: 25px; font-weight: 500;">
            ✓ {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div style="background: #fee2e2; border-left: 4px solid #b91c1c; color: #b91c1c; padding: 16px; border-radius: 8px; margin-bottom: 25px; font-weight: 500;">
            ⚠ {{ session('error') }}
        </div>
    @endif

    {{-- Table --}}
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
            <thead>
                <tr style="background: #f8fafc; text-align: left;">
                    <th style="padding: 16px; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 13px;">TIPE & MAPEL</th>
                    <th style="padding: 16px; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 13px;">KELAS</th>
                    <th style="padding: 16px; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 13px;">WAKTU</th>
                    <th style="padding: 16px; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 13px; text-align: center;">TOKEN</th>
                    <th style="padding: 16px; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 13px; text-align: center;">SUMBER</th>
                    <th style="padding: 16px; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 13px; text-align: center;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $s)
                <tr>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9;">
                        <span style="font-size: 10px; background: #e0e7ff; color: #3730a3; padding: 3px 8px; border-radius: 12px; font-weight: 700;">
                            {{ $s->examType->name ?? 'N/A' }}
                        </span>
                        <div style="font-weight: 700; color: #0f172a; margin-top: 5px;">{{ $s->subject->nama_mapel }}</div>
                    </td>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9;">
                        <span style="background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-size: 13px; font-weight: 600;">
                            Kelas {{ $s->classroom->nama_kelas }}
                        </span>
                    </td>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9;">
                        <div style="font-size: 13px; font-weight: 600;">📅 {{ date('d/m/Y', strtotime($s->tanggal_ujian)) }}</div>
                        <div style="font-size: 11px; color: #c91313; font-weight: 700;">⏱️ {{ $s->durasi }} Menit</div>
                    </td>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                        <span style="font-family: monospace; font-size: 14px; font-weight: 800; background: #fffbeb; color: #92400e; padding: 5px 10px; border-radius: 6px; border: 1px dashed #f59e0b;">
                            {{ $s->token }}
                        </span>
                    </td>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                        @if($s->created_by == auth()->id())
                            <span style="font-size: 10px; color: #15803d; border: 1px solid #15803d; padding: 2px 8px; border-radius: 10px; font-weight: 700;">MANDIRI</span>
                        @else
                            <span style="font-size: 10px; color: #c91313; border: 1px solid #c91313; padding: 2px 8px; border-radius: 10px; font-weight: 700;">PUSAT</span>
                        @endif
                    </td>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            {{-- Akses Kelola Soal (Link ke QuestionController) --}}
                            <a href="{{ route('guru.questions.manage', $s->id) }}" style="text-decoration: none; background: #1e293b; color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">📝 Soal</a>
                            
                            {{-- Edit & Hapus Hanya Muncul Jika Jadwal Buatan Sendiri (Mandiri) --}}
                            @if($s->created_by == auth()->id())
                                <button type="button" onclick='openEditModal(@json($s))' style="background: #f1f5f9; border: 1px solid #e2e8f0; padding: 6px 10px; border-radius: 6px; cursor: pointer;">✏️</button>

                                <form action="{{ route('guru.schedules.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal mandiri ini? Seluruh soal terkait akan tetap ada di bank soal namun jadwal akan hilang.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background: #fee2e2; border: 1px solid #fecaca; padding: 6px 10px; border-radius: 6px; cursor: pointer;">🗑️</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #64748b;">
                        Belum ada jadwal ujian yang tersedia.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Import Modal-Modal --}}
@include('guru.schedules.modal_create')
@include('guru.schedules.modal_edit')

{{-- Script & Style Khusus --}}
<script>
    function openCreateModal() { 
        document.getElementById('createModal').style.display = 'block'; 
    }
    function closeCreateModal() { 
        document.getElementById('createModal').style.display = 'none'; 
    }
    
    function openEditModal(schedule) {
        const modal = document.getElementById('editModal');
        const form = document.getElementById('editForm');
        
        form.action = "{{ url('guru/schedules') }}/" + schedule.id;
        
        document.getElementById('edit_exam_type_id').value = schedule.exam_type_id;
        document.getElementById('edit_classroom_id').value = schedule.classroom_id;
        document.getElementById('edit_tanggal_ujian').value = schedule.tanggal_ujian;
        document.getElementById('edit_durasi').value = schedule.durasi;

        modal.style.display = 'block';
    }

    function closeEditModal() { 
        document.getElementById('editModal').style.display = 'none'; 
    }

    window.onclick = function(event) {
        if (event.target.className === 'modal-custom') {
            event.target.style.display = "none";
        }
    }
</script>

<style>
    .modal-custom { 
        display: none; 
        position: fixed; 
        z-index: 9999; 
        left: 0; 
        top: 0; 
        width: 100%; 
        height: 100%; 
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
    }
    .modal-content-custom { 
        background: white; 
        margin: 5% auto; 
        padding: 30px; 
        border-radius: 16px; 
        width: 480px; 
        position: relative; 
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    table tbody tr:hover {
        background-color: #f8fafc;
        transition: background 0.2s;
    }
</style>
@endsection