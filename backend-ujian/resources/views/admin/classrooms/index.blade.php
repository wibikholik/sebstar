@extends('layouts.app')

@section('title', 'Manajemen Kelas & Jurusan')

<style>
    /* Background dengan Gradasi Merah-Putih Tegas + Efek Polkadot Grid Modern */
    body {
        background-color: #f4f5f9 !important;
        background-image: 
            radial-gradient(rgba(230, 57, 70, 0.15) 1.5px, transparent 1.5px), 
            linear-gradient(135deg, #fceade 0%, #f4f5f9 50%, #ffffff 100%) !important;
        background-size: 24px 24px, 100% 100% !important;
        background-attachment: fixed !important;
    }

    /* ================= CONTENT BOX & BAR MULTIPURPOSE ================= */
    .content-box {
        background: #ffffff !important;
        border-radius: 16px !important;
        padding: 25px !important;
        margin-bottom: 30px !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04) !important;
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
    }

    .action-bar {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        margin-bottom: 25px !important;
    }

    .action-bar h3 {
        font-size: 16px !important;
        font-weight: 700 !important;
        color: #1e1e2f !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }

    /* Tombol Tambah Elegan */
    .btn-add {
        background: linear-gradient(135deg, #cd0000 0%, #950000 100%) !important;
        color: #ffffff !important;
        border: none !important;
        padding: 10px 20px !important;
        border-radius: 20px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        box-shadow: 0 4px 12px rgba(205, 0, 0, 0.2) !important;
        transition: all 0.3s ease !important;
    }

    .btn-add:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 18px rgba(205, 0, 0, 0.3) !important;
        filter: brightness(1.1) !important;
    }

    /* ================= TABLE PREMIUM REVOLUTION ================= */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin-top: 10px !important;
        background: #ffffff !important;
    }

    th {
        background: #fafafa !important;
        color: #1e1e2f !important;
        font-weight: 700 !important;
        font-size: 12px !important;
        padding: 14px 18px !important;
        border-bottom: 2px solid #edf0f5 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
    }

    td {
        padding: 14px 18px !important;
        border-bottom: 1px solid #edf0f5 !important;
        color: #2d3748 !important;
        font-size: 14px !important;
        vertical-align: middle !important;
    }

    tr:hover td {
        background: #fcfcfd !important;
    }

    /* Badge Jurusan */
    .badge-major {
        background: rgba(230, 57, 70, 0.08) !important; 
        color: #cd0000 !important; 
        padding: 5px 12px !important; 
        border-radius: 20px !important; 
        font-size: 11px !important; 
        font-weight: 700 !important; 
        border: 1px solid rgba(230, 57, 70, 0.15) !important;
        letter-spacing: 0.5px !important;
    }

    /* Tombol Aksi Mini */
    .btn-table-action {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        padding: 6px 10px !important;
        border-radius: 8px !important;
        cursor: pointer !important;
        font-size: 13px !important;
        transition: all 0.2s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .btn-table-action.btn-edit { color: #64748b !important; }
    .btn-table-action.btn-edit:hover { border-color: #3b82f6 !important; color: #3b82f6 !important; background: rgba(59, 130, 246, 0.05) !important; }
    
    .btn-table-action.btn-delete { color: #e74c3c !important; }
    .btn-table-action.btn-delete:hover { border-color: #e74c3c !important; background: rgba(231, 76, 60, 0.05) !important; }
</style>

@section('content')

{{-- Alert Notifikasi Sukses --}}
@if(session('success'))
    <div style="background: rgba(46, 204, 113, 0.1); color: #27ae60; padding: 14px 20px; border-radius: 10px; margin-bottom: 25px; font-size: 13px; font-weight: 600; border-left: 4px solid #2ecc71; display: flex; align-items: center; gap: 8px;">
        ✓ {{ session('success') }}
    </div>
@endif

{{-- Alert Notifikasi Gagal / Pelanggaran Relasi --}}
@if(session('error'))
    <div style="background: rgba(231, 76, 60, 0.1); color: #c0392b; padding: 14px 20px; border-radius: 10px; margin-bottom: 25px; font-size: 13px; font-weight: 600; border-left: 4px solid #e74c3c; display: flex; align-items: center; gap: 8px;">
        ⚠️ {{ session('error') }}
    </div>
@endif

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
    
    {{-- BLOK KIRI: MANAJEMEN JURUSAN --}}
    <div>
        <div class="content-box">
            <div class="action-bar" style="margin-bottom: 15px;">
                <h3><i class="fas fa-graduation-cap" style="color: #cd0000;"></i> Jurusan</h3>
                <button class="btn-add" onclick="openMajorModal()" style="padding: 6px 14px; font-size: 12px; border-radius: 15px;">
                    <i class="fas fa-plus" style="font-size: 10px;"></i> Jurusan
                </button>
            </div>
            
            <div style="border: 1px solid #edf0f5; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.01);">
                <table style="font-size: 13px;">
                    <thead>
                        <tr>
                            <th>Singkatan</th>
                            <th style="text-align: center; width: 35%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($majors as $m)
                        <tr>
                            <td title="{{ $m->nama_jurusan }}"><strong style="color: #1e1e2f; font-size: 14px;">{{ $m->singkatan }}</strong></td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    <button onclick='openMajorEditModal(@json($m))' class="btn-table-action btn-edit" title="Ubah Jurusan">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="confirmDeleteMajor('{{ $m->id }}')" class="btn-table-action btn-delete" title="Hapus Jurusan">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- BLOK KANAN: MANAJEMEN DAFTAR KELAS --}}
    <div>
        <div class="content-box">
            <div class="action-bar">
                <h3><i class="fas fa-school" style="color: #cd0000;"></i> Daftar Kelas</h3>
                <button class="btn-add" onclick="openClassModal()">
                    <i class="fas fa-plus-circle"></i> Tambah Kelas
                </button>
            </div>

            <div style="border: 1px solid #edf0f5; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.01);">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Kelas</th>
                            <th>Jurusan Terkait</th>
                            <th style="text-align: center; width: 25%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classes as $item)
                        <tr>
                            <td style="font-weight: 700; color: #1e1e2f;">{{ $item->nama_kelas }}</td>
                            <td>
                                <span class="badge-major">
                                    <i class="fas fa-tags" style="font-size: 10px; margin-right: 4px;"></i> {{ $item->major->singkatan ?? 'N/A' }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    <button onclick='openEditClassModal(@json($item))' class="btn-table-action btn-edit" title="Ubah Kelas">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="confirmDeleteClass('{{ $item->id }}')" class="btn-table-action btn-delete" title="Hapus Kelas">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 50px; color: #94a3b8;">
                                <i class="fas fa-folder-open" style="font-size: 32px; display: block; margin-bottom: 10px; color: #cbd5e1;"></i>
                                Belum ada data kelas dalam database.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Form Hidden untuk Eksekusi Hapus Data Route --}}
<form id="delete-major-form" method="POST" style="display:none;"> @csrf @method('DELETE') </form>
<form id="delete-class-form" method="POST" style="display:none;"> @csrf @method('DELETE') </form>

{{-- Menyertakan Komponen File Modal --}}
@include('admin.classrooms.modal_major_create')
@include('admin.classrooms.modal_major_edit')
@include('admin.classrooms.modal_class_create')
@include('admin.classrooms.modal_class_edit')

<script>
    // --- MANAJEMEN MODAL JURUSAN (MAJOR) ---
    function openMajorModal() { 
        document.getElementById('modalMajor').style.display = 'block'; 
    }
    function closeMajorModal() { 
        document.getElementById('modalMajor').style.display = 'none'; 
    }
    
    function openMajorEditModal(major) {
        document.getElementById('editMajorForm').action = "/admin/majors/" + major.id;
        document.getElementById('edit_major_name').value = major.nama_jurusan;
        document.getElementById('edit_major_short').value = major.singkatan;
        document.getElementById('modalMajorEdit').style.display = 'block';
    }
    function closeMajorEditModal() { 
        document.getElementById('modalMajorEdit').style.display = 'none'; 
    }

    function confirmDeleteMajor(id) {
        if(confirm('Hapus jurusan ini? Menghapus jurusan akan berdampak pada data kelas terkait.')) {
            let form = document.getElementById('delete-major-form');
            form.action = "/admin/majors/" + id;
            form.submit();
        }
    }

    // --- SINKRONISASI MANAJEMEN MODAL KELAS (CLASSROOM) ---
    // PERBAIKAN: Menyesuaikan target pemicu ID modalCreate kelas agar sinkron dengan file modal baru
    function openClassModal() { 
        document.getElementById('modalCreate').style.display = 'block'; 
    }
    function closeModal() { 
        document.getElementById('modalCreate').style.display = 'none'; 
    }

    function openEditClassModal(item) {
        document.getElementById('editClassForm').action = "/admin/classrooms/" + item.id;
        document.getElementById('edit_class_name').value = item.nama_kelas;
        document.getElementById('edit_major_id').value = item.major_id;
        document.getElementById('modalEditClass').style.display = 'block';
    }
    function closeEditClassModal() { 
        document.getElementById('modalEditClass').style.display = 'none'; 
    }

    function confirmDeleteClass(id) {
        if(confirm('Apakah Anda yakin ingin menghapus kelas ini?')) {
            let form = document.getElementById('delete-class-form');
            form.action = "/admin/classrooms/" + id;
            form.submit();
        }
    }

    // Auto close modal jika admin tidak sengaja mengklik area abu-abu di luar box modal
    window.onclick = function(event) {
        // PERBAIKAN: Memetakan ulang susunan array ID pembungkus modal yang valid
        const modalIds = ['modalMajor', 'modalMajorEdit', 'modalCreate', 'modalEditClass'];
        modalIds.forEach(id => {
            const m = document.getElementById(id);
            if (m && event.target == m) {
                m.style.display = 'none';
            }
        });
    }
</script>

@endsection