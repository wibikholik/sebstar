@extends('layouts.app')

@section('title', 'Mata Pelajaran')

@section('content')

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon-wrapper"><i class="fas fa-book-open"></i></div>
        <div class="stat-info">
            <h2>{{ $subjects->count() }}</h2>
            <p>TOTAL MATA PELAJARAN</p>
        </div>
    </div>
</div>

<div class="content-box">
    <div class="action-bar">
        <h3><i class="fas fa-book header-icon"></i> Daftar Mata Pelajaran</h3>
        <div style="display: flex; gap: 10px;">
            <button class="btn-import" onclick="openImportModal()">
                <i class="fas fa-file-csv"></i> Import CSV
            </button>
            <button class="btn-add" onclick="openModal()">
                <i class="fas fa-plus-circle"></i> Tambah Mapel
            </button>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 14px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; border: 1px solid #c3e6cb; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 14px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; border: 1px solid #f5c6cb; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-exclamation-circle"></i> {!! session('error') !!}
        </div>
    @endif

    <div style="border: 1px solid #edf0f5; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.01);">
        <table>
            <thead>
                <tr>
                    <th style="width: 25%;">Kode Mapel</th>
                    <th>Nama Mata Pelajaran</th>
                    <th style="text-align: center; width: 20%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subjects as $mapel)
                <tr>
                    <td style="font-weight: 700; color: #cd0000; font-size: 14px;">{{ $mapel->kode_mapel }}</td>
                    <td style="font-weight: 600; color: #1e1e2f;">{{ $mapel->nama_mapel }}</td>
                    <td style="text-align: center;">
                        <button onclick='openEditModal(@json($mapel))' class="btn-table-action btn-edit" title="Ubah Data">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" onclick="confirmDelete('{{ $mapel->id }}')" class="btn-table-action btn-delete" title="Hapus Data">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center; padding: 50px; color: #94a3b8;">
                        <i class="fas fa-folder-open" style="font-size: 32px; display: block; margin-bottom: 10px; color: #cbd5e1;"></i>
                        Belum ada data mata pelajaran dalam database.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<form id="delete-form" method="POST" style="display: none;">
    @csrf 
    @method('DELETE')
</form>

{{-- MODAL IMPORT FIX --}}
<div id="modalImport" class="custom-modal-overlay" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
    <div style="background: #ffffff; width: 450px; margin: 10% auto; padding: 25px; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); border: 1px solid rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #edf0f5; padding-bottom: 12px;">
            <h4 style="font-size: 16px; font-weight: 700; color: #1e1e2f; margin: 0;"><i class="fas fa-file-csv" style="color: #107c41; margin-right: 6px;"></i> Import Massal Mata Pelajaran</h4>
            <span style="cursor: pointer; font-size: 20px; color: #94a3b8;" onclick="closeImportModal()">&times;</span>
        </div>
        
        <div style="background: #f8fafc; border: 1px dashed #cbd5e1; padding: 12px; border-radius: 10px; margin-bottom: 20px; font-size: 12px; color: #64748b; line-height: 1.5;">
            <i class="fas fa-info-circle" style="color: #cd0000;"></i> <strong>Petunjuk:</strong><br>
            Unduh berkas template di bawah ini, isi data mata pelajaran, kemudian unggah kembali berkas hasil pengisian tersebut.
            <a href="{{ route('admin.subjects.template') }}" style="display: block; margin-top: 8px; color: #cd0000; font-weight: 700; text-decoration: none;"><i class="fas fa-download"></i> Unduh Template CSV</a>
        </div>

        <form action="{{ route('admin.subjects.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 8px; text-transform: uppercase;">Pilih Berkas CSV (.csv)</label>
                <input type="file" name="file_excel" required accept=".csv, text/csv" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #edf0f5; padding-top: 15px;">
                <button type="button" onclick="closeImportModal()" style="background: #f1f5f9; color: #475569; border: none; padding: 10px 18px; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer;">Batal</button>
                <button type="submit" style="background: linear-gradient(135deg, #107c41 0%, #0b592e 100%); color: #ffffff; border: none; padding: 10px 20px; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 124, 65, 0.2);"><i class="fas fa-upload"></i> Proses Import</button>
            </div>
        </form>
    </div>
</div>

@include('admin.subjects.modal_create')
@include('admin.subjects.modal_edit')

<style>
    body {
        background-color: #f4f5f9 !important;
        background-image: 
            radial-gradient(rgba(230, 57, 70, 0.15) 1.5px, transparent 1.5px), 
            linear-gradient(135deg, #fceade 0%, #f4f5f9 50%, #ffffff 100%) !important;
        background-size: 24px 24px, 100% 100% !important;
        background-attachment: fixed !important;
    }

    .stats-grid {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 22px !important;
        margin-bottom: 30px !important;
    }

    .stat-card {
        background: #ffffff !important;
        padding: 24px !important;
        border-radius: 16px !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05) !important;
        display: flex !important;
        align-items: center !important;
        gap: 18px !important;
        border: 2px solid #ffffff !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        grid-column: span 1 !important;
    }

    @media (min-width: 992px) {
        .stats-grid { grid-template-columns: repeat(4, 1fr) !important; }
        .stat-card { grid-column: span 1 !important; }
    }

    .stat-card:hover {
        transform: translateY(-5px) !important;
        box-shadow: 0 15px 30px rgba(230, 57, 70, 0.15) !important;
        border-color: rgba(230, 57, 70, 0.3) !important;
    }

    .stat-icon-wrapper {
        width: 58px !important;
        height: 58px !important;
        border-radius: 14px !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        font-size: 24px !important;
        transition: all 0.4s ease !important;
        background: rgba(230, 57, 70, 0.1) !important; 
        color: #cd0000 !important;
    }

    .stat-card:hover .stat-icon-wrapper { 
        background: #cd0000 !important; 
        color: #ffffff !important; 
        transform: scale(1.1) rotate(-5deg) !important; 
    }

    .stat-info h2 {
        font-size: 32px !important;
        font-weight: 700 !important;
        color: #1e1e2f !important;
        margin: 0 !important;
        line-height: 1.2 !important;
    }

    .stat-info p {
        font-size: 12px !important;
        font-weight: 700 !important;
        color: #6a6a7a !important;
        letter-spacing: 0.5px !important;
        margin: 4px 0 0 0 !important;
    }

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
        font-size: 18px !important;
        font-weight: 700 !important;
        color: #1e1e2f !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }

    .btn-add {
        background: linear-gradient(135deg, #cd0000 0%, #950000 100%) !important;
        color: #ffffff !important;
        border: none !important;
        padding: 10px 24px !important;
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

    .btn-import {
        background: linear-gradient(135deg, #107c41 0%, #0b592e 100%) !important;
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
        box-shadow: 0 4px 12px rgba(16, 124, 65, 0.2) !important;
        transition: all 0.3s ease !important;
    }

    .btn-import:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 18px rgba(16, 124, 65, 0.3) !important;
        filter: brightness(1.1) !important;
    }

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
        font-size: 13px !important;
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

    tr:hover td { background: #fcfcfd !important; }

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
    
    .btn-table-action.btn-delete { color: #e74c3c !important; margin-left: 4px !important; }
    .btn-table-action.btn-delete:hover { border-color: #e74c3c !important; background: rgba(231, 76, 60, 0.05) !important; }
</style>

<script>
    function openModal() { 
        document.getElementById('modalCreate').style.display = 'block'; 
    }
    function closeModal() { 
        document.getElementById('modalCreate').style.display = 'none'; 
    }

    function openImportModal() {
        document.getElementById('modalImport').style.display = 'block';
    }
    function closeImportModal() {
        document.getElementById('modalImport').style.display = 'none';
    }
    
    function openEditModal(mapel) {
        document.getElementById('editForm').action = "/admin/subjects/" + mapel.id;
        document.getElementById('edit_nama').value = mapel.nama_mapel;
        document.getElementById('edit_kode').value = mapel.kode_mapel;
        document.getElementById('modalEdit').style.display = 'block';
    }
    function closeEditModal() { 
        document.getElementById('modalEdit').style.display = 'none'; 
    }

    function confirmDelete(id) {
        if(confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini?')) {
            let form = document.getElementById('delete-form');
            form.action = "/admin/subjects/" + id;
            form.submit();
        }
    }
</script>
@endsection