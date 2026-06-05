@extends('layouts.app')
@section('title', 'Manajemen Tipe Ujian')

<style>
    /* Latar belakang polkadot grid premium khas SEBSTAR */
    body {
        background-color: #f4f5f9 !important;
        background-image: 
            radial-gradient(rgba(230, 57, 70, 0.15) 1.5px, transparent 1.5px), 
            linear-gradient(135deg, #fceade 0%, #f4f5f9 50%, #ffffff 100%) !important;
        background-size: 24px 24px, 100% 100% !important;
        background-attachment: fixed !important;
    }

    /* Penyelarasan box utama */
    .content-box-premium {
        background: #ffffff !important;
        border-radius: 16px !important;
        padding: 25px !important;
        margin-bottom: 30px !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04) !important;
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
    }

    /* Tombol Aksi Merah Gradasi Dashboard */
    .btn-action-premium-red {
        background: linear-gradient(135deg, #cd0000 0%, #950000 100%) !important;
        color: #ffffff !important;
        border: none !important;
        padding: 11px 24px !important;
        border-radius: 30px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        box-shadow: 0 5px 15px rgba(205, 0, 0, 0.25) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    .btn-action-premium-red:hover {
        transform: translateY(-3px) !important;
        box-shadow: 0 8px 22px rgba(205, 0, 0, 0.4) !important;
        filter: brightness(1.1) !important;
    }
</style>

@section('content')
<div class="content-box-premium">
    
    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; gap: 20px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div>
                <h3 style="margin: 0; color: #1e1e2f; font-weight: 700; font-size: 18px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-tags" style="color: #cd0000;"></i> Kategori & Tipe Ujian
                </h3>
                <p style="margin: 4px 0 0 0; color: #6a6a7a; font-size: 13px; font-weight: 600;">Atur jenis ujian dan kontrol hak akses pengelolaan mandiri bagi Guru.</p>
            </div>
        </div>
        <button onclick="openTypeModal()" class="btn-action-premium-red">
            <i class="fas fa-plus"></i> Tambah Tipe
        </button>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
        <div style="background: rgba(46, 204, 113, 0.1); border-left: 4px solid #2ecc71; color: #27ae60; padding: 14px 20px; border-radius: 10px; margin-bottom: 25px; font-weight: 600; font-size: 13px;">
            ✓ {{ session('success') }}
        </div>
    @endif

    {{-- Alert Error / Gagal Proteksi Constraint --}}
    @if(session('error'))
        <div style="background: rgba(231, 76, 60, 0.1); border-left: 4px solid #e74c3c; color: #c0392b; padding: 14px 20px; border-radius: 10px; margin-bottom: 25px; font-weight: 600; font-size: 13px;">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    {{-- Table Area --}}
    <div style="overflow-x: auto; background: white; border-radius: 12px; border: 1px solid #edf0f5;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: #fafafa; border-bottom: 2px solid #edf0f5;">
                    <th style="padding: 16px 20px; color: #6a6a7a; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">NAMA TIPE UJIAN</th>
                    <th style="padding: 16px 20px; color: #6a6a7a; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; width: 250px;">IZIN KELOLA GURU</th>
                    <th style="padding: 16px 20px; color: #6a6a7a; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; width: 200px;">AKSI</th>
                </tr>
            </thead>
            <tbody style="color: #1e1e2f;">
                @forelse($examTypes as $type)
                <tr style="border-bottom: 1px solid #edf0f5;">
                    <td style="padding: 18px 20px; vertical-align: middle;">
                        <div style="font-weight: 700; font-size: 15px; display: flex; align-items: center; gap: 8px;">
                            <span style="color: #cd0000;">•</span> {{ $type->name }}
                        </div>
                    </td>
                    <td style="padding: 18px 20px; text-align: center; vertical-align: middle;">
                        <div style="display: flex; flex-direction: column; align-items: center;">
                            @if($type->is_teacher_manageable)
                                <span style="background: rgba(46, 204, 113, 0.15); color: #27ae60; padding: 5px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; letter-spacing: 0.3px;">
                                    DIIZINKAN
                                </span>
                            @else
                                <span style="background: #f1f5f9; color: #64748b; padding: 5px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; border: 1px solid #e2e8f0;">
                                    HANYA ADMIN
                                </span>
                            @endif
                        </div>
                    </td>
                    <td style="padding: 18px 20px; text-align: center; vertical-align: middle;">
                        <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                            <button type="button" onclick='openEditTypeModal(@json($type))' style="background: #fafafa; border: 1px solid #cbd5e1; padding: 7px 12px; border-radius: 8px; cursor: pointer; font-size: 12px; font-weight: 700; color: #475569;">
                                ✏️ Edit
                            </button>
                            
                            {{-- Perbaikan Form Action Menggunakan Penulisan URL Dinamis Sinkron --}}
                            <form action="{{ url('admin/exam-types/' . $type->id) }}" method="POST" style="display:inline;">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus tipe ujian ini?')" style="background: rgba(231, 76, 60, 0.08); border: 1px solid rgba(231, 76, 60, 0.2); padding: 7px 10px; border-radius: 8px; cursor: pointer; font-size: 12px; color: #e74c3c;">
                                    🗑️ Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center; padding: 50px 20px; color: #6a6a7a; font-weight: 600;">Belum ada data tipe ujian.</td>
                </tr>
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

    window.onclick = function(event) {
        if (event.target.className === 'modal-custom') {
            event.target.style.display = "none";
        }
    }
</script>

<style>
    .modal-custom {
        display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); backdrop-filter: blur(4px);
    }
    .modal-content-custom {
        background: white; margin: 10% auto; padding: 25px; border-radius: 16px; width: 400px; position: relative; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); border: 1px solid rgba(0,0,0,0.05);
    }
</style>
@endsection