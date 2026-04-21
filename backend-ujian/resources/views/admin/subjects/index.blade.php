@extends('layouts.app')

@section('title', 'Mata Pelajaran')

@section('content')

<div class="stats-grid">
    <div class="stat-card">
        <h2>{{ $subjects->count() }}</h2>
        <p>Total Mata Pelajaran</p>
    </div>
</div>

<div class="content-box">
    <div class="action-bar">
        <h3 style="margin: 0;">Daftar Mata Pelajaran</h3>
        <button class="btn-add" onclick="openModal()">+ Tambah Mapel</button>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Kode Mapel</th>
                <th>Nama Mata Pelajaran</th>
                <th style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subjects as $mapel)
            <tr>
                <td style="font-weight: 700; color: var(--red-sebstar);">{{ $mapel->kode_mapel }}</td>
                <td>{{ $mapel->nama_mapel }}</td>
                <td style="text-align: center;">
                    <button onclick='openEditModal(@json($mapel))' style="background:none; border:1px solid #ddd; padding:5px; border-radius:6px; cursor:pointer;">✏️</button>
                    <button onclick="confirmDelete('{{ $mapel->id }}')" style="background:none; border:1px solid #ddd; padding:5px; border-radius:6px; cursor:pointer; color:red;">🗑️</button>
                </td>
            </tr>
            @empty
            <tr><td colspan="3" style="text-align:center;">Belum ada data mapel.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<form id="delete-form" method="POST" style="display:none;">
    @csrf @method('DELETE')
</form>

@include('admin.subjects.modal_create')
@include('admin.subjects.modal_edit')

<script>
    function openModal() { document.getElementById('modalCreate').style.display = 'block'; }
    function closeModal() { document.getElementById('modalCreate').style.display = 'none'; }
    
    function openEditModal(mapel) {
        document.getElementById('editForm').action = "/admin/subjects/" + mapel.id;
        document.getElementById('edit_nama').value = mapel.nama_mapel;
        document.getElementById('edit_kode').value = mapel.kode_mapel;
        document.getElementById('modalEdit').style.display = 'block';
    }
    function closeEditModal() { document.getElementById('modalEdit').style.display = 'none'; }

    function confirmDelete(id) {
        if(confirm('Hapus mapel ini?')) {
            let form = document.getElementById('delete-form');
            form.action = "/admin/subjects/" + id;
            form.submit();
        }
    }
</script>
@endsection