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

    /* Panel Import Excel Kontainer Khusus */
    .import-panel {
        background: #fafafa !important;
        border: 1px dashed #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 16px 20px !important;
        margin-bottom: 25px !important;
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        gap: 15px !important;
    }

    .btn-download-template {
        background: #f1f5f9 !important;
        color: #475569 !important;
        border: 1px solid #cbd5e1 !important;
        padding: 9px 18px !important;
        border-radius: 20px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        transition: all 0.2s ease !important;
    }

    .btn-download-template:hover {
        background: #e2e8f0 !important;
        color: #1e293b !important;
    }

    .btn-process-import {
        background: linear-gradient(135deg, #107c41 0%, #0b592e 100%) !important;
        color: #ffffff !important;
        border: none !important;
        padding: 10px 20px !important;
        border-radius: 20px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        box-shadow: 0 4px 12px rgba(16, 124, 65, 0.2) !important;
        transition: all 0.3s ease !important;
    }

    .btn-process-import:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 18px rgba(16, 124, 65, 0.3) !important;
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
    <div style="background: rgba(231, 76, 60, 0.1); color: #c0392b; padding: 14px 20px; border-radius: 10px; margin-bottom: 25px; font-size: 13px; font-weight: 600; border-left: 4px solid #e74c3c; display: flex; align-items: flex-start; gap: 8px; line-height: 1.6;">
        ⚠️ <div>{!! session('error') !!}</div>
    </div>
@endif

{{-- 🚀 FITUR GABUNGAN: Panel Unduh Template & Pemicu Modal Import Massal --}}
<div class="import-panel">
    <div style="display: flex; flex-direction: column; gap: 2px;">
        <span style="font-size: 14px; font-weight: 700; color: #1e1e2f;">Import Sinkronisasi Kelas & Jurusan</span>
        <span style="font-size: 12px; color: #64748b;">Unggah satu file untuk mendaftarkan struktur kompetensi jurusan baru sekaligus rombel kelas.</span>
    </div>
    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
        <a href="{{ route('admin.classrooms.download_template') }}" class="btn-download-template">
            <i class="fas fa-file-download"></i> Unduh Template CSV
        </a>
        <button class="btn-process-import" onclick="openImportModal()">
            <i class="fas fa-file-import"></i> Jalankan Import Massal
        </button>
    </div>
</div>

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

{{-- 🚀 MODAL INTERAKTIF BARU: Pemrosesan Unggah Berkas Gabungan Kelas & Jurusan --}}
<div id="modalImport" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
    <div style="background: #ffffff; width: 450px; margin: 10% auto; padding: 25px; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); border: 1px solid rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #edf0f5; padding-bottom: 12px;">
            <h4 style="font-size: 16px; font-weight: 700; color: #1e1e2f; margin: 0;"><i class="fas fa-file-csv" style="color: #107c41; margin-right: 6px;"></i> Import Gabungan Kelas & Jurusan</h4>
            <span style="cursor: pointer; font-size: 20px; color: #94a3b8;" onclick="closeImportModal()">&times;</span>
        </div>
        
        <div style="background: #f8fafc; border: 1px dashed #cbd5e1; padding: 12px; border-radius: 10px; margin-bottom: 20px; font-size: 12px; color: #64748b; line-height: 1.5;">
            <i class="fas fa-info-circle" style="color: #cd0000;"></i> <strong>Sistem Otomatisasi SEBSTAR:</strong><br>
            Sistem akan mencocokkan singkatan jurusan. Jika singkatan belum ada, sistem mendaftarkan kompetensi jurusan baru terlebih dahulu, lalu rombel kelas terkait akan langsung tersambung otomatis.
        </div>

        <form action="{{ route('admin.classrooms.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 8px; text-transform: uppercase;">Pilih File Template (.csv / .xlsx)</label>
                <input type="file" name="file_excel" required accept=".csv, .xlsx, .xls" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #edf0f5; padding-top: 15px;">
                <button type="button" onclick="closeImportModal()" style="background: #f1f5f9; color: #475569; border: none; padding: 10px 18px; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer;">Batal</button>
                <button type="submit" style="background: linear-gradient(135deg, #107c41 0%, #0b592e 100%); color: #ffffff; border: none; padding: 10px 20px; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 124, 65, 0.2);"><i class="fas fa-upload"></i> Jalankan Sinkronisasi</button>
            </div>
        </form>
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
    function openClassModal() { 
        document.getElementById('modalCreate').style.display = 'block'; 
    }
    function closeModal() { 
        document.getElementById('modalCreate').style.display = 'none'; 
    }

    // --- MANAJEMEN PANDUAN MODAL IMPORT GABUNGAN ---
    function openImportModal() {
        document.getElementById('modalImport').style.display = 'block';
    }
    function closeImportModal() {
        document.getElementById('modalImport').style.display = 'none';
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
        const modalIds = ['modalMajor', 'modalMajorEdit', 'modalCreate', 'modalEditClass', 'modalImport'];
        modalIds.forEach(id => {
            const m = document.getElementById(id);
            if (m && event.target == m) {
                m.style.display = 'none';
            }
        });
    }
</script>

@endsection