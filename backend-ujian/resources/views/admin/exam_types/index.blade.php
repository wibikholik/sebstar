@extends('layouts.app')
@section('title', 'Manajemen Tipe Ujian')

@section('content')
<div class="content-box" style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;">
    
    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h3 style="margin: 0; color: #0f172a; font-weight: 700; font-size: 24px;">Kategori & Tipe Ujian</h3>
            <p style="margin: 5px 0 0 0; color: #64748b; font-size: 14px;">Atur jenis ujian dan kontrol hak akses pengelolaan mandiri bagi Guru.</p>
        </div>
        <button onclick="openTypeModal()" style="background: #c91313; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; box-shadow: 0 4px 6px -1px rgba(201, 19, 19, 0.2);">
            + Tambah Tipe
        </button>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div style="background: #dcfce7; border-left: 4px solid #15803d; color: #15803d; padding: 16px; border-radius: 8px; margin-bottom: 25px; font-weight: 500;">
            ✓ {{ session('success') }}
        </div>
    @endif

    {{-- Table --}}
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
            <thead>
                <tr style="background: #f8fafc; text-align: left;">
                    <th style="padding: 16px; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 13px;">NAMA TIPE UJIAN</th>
                    <th style="padding: 16px; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 13px; text-align: center;">IZIN KELOLA GURU</th>
                    <th style="padding: 16px; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 13px; text-align: center;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($examTypes as $type)
                <tr>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9;">
                        <div style="font-weight: 700; color: #0f172a; font-size: 16px;">{{ $type->name }}</div>
                    </td>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                        {{-- Status Badge dengan Switch --}}
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 5px;">
                            @if($type->is_teacher_manageable)
                                <span style="background: #dcfce7; color: #15803d; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800;">DIIZINKAN</span>
                            @else
                                <span style="background: #f1f5f9; color: #64748b; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800;">HANYA ADMIN</span>
                            @endif
                        </div>
                    </td>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            <button type="button" onclick='openEditTypeModal(@json($type))' style="background: #f1f5f9; border: 1px solid #e2e8f0; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 13px;">✏️ Edit</button>
                            
                            <form action="{{ route('admin.exam-types.destroy', $type->id) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus tipe ini?')" style="background: #fee2e2; border: 1px solid #fecaca; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 13px;">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" style="text-align: center; padding: 40px; color: #64748b;">Belum ada data tipe ujian.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@include('admin.exam_types.modal_create')
@include('admin.exam_types.modal_edit')

<script>
    function openTypeModal() { document.getElementById('typeModal').style.display = 'block'; }
    function closeTypeModal() { document.getElementById('typeModal').style.display = 'none'; }
    function closeEditTypeModal() { document.getElementById('editTypeModal').style.display = 'none'; }

    function openEditTypeModal(type) {
        const modal = document.getElementById('editTypeModal');
        const form = document.getElementById('editTypeForm');
        form.action = "/admin/exam-types/" + type.id;
        
        document.getElementById('edit_name').value = type.name;
        document.getElementById('edit_is_teacher_manageable').checked = type.is_teacher_manageable == 1;
        
        modal.style.display = 'block';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.className === 'modal-custom') {
            event.target.style.display = "none";
        }
    }
</script>

<style>
    .modal-custom {
        display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5);
    }
    .modal-content-custom {
        background: white; margin: 10% auto; padding: 25px; border-radius: 12px; width: 400px; position: relative;
    }
</style>
@endsection