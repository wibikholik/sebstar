@extends('layouts.app')

@section('title', 'Manajemen Kelas & Jurusan')

@section('content')

@if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
        {{ session('success') }}
    </div>
@endif

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
    
    <div>
        <div class="content-box">
            <div class="action-bar" style="margin-bottom: 15px;">
                <h3 style="margin:0; font-size: 16px;">Data Jurusan</h3>
                <button class="btn-add" onclick="openMajorModal()" style="padding: 5px 10px; font-size: 12px;">+ Jurusan</button>
            </div>
            
            <table style="font-size: 13px;">
                <thead>
                    <tr>
                        <th>Singkatan</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($majors as $m)
                    <tr>
                        <td title="{{ $m->nama_jurusan }}"><strong>{{ $m->singkatan }}</strong></td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 5px; justify-content: center;">
                                <button onclick='openMajorEditModal(@json($m))' style="border:none; background:none; cursor:pointer; font-size: 14px;">✏️</button>
                                <button onclick="confirmDeleteMajor('{{ $m->id }}')" style="border:none; background:none; color:red; cursor:pointer; font-size: 14px;">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <div class="content-box">
            <div class="action-bar">
                <h3 style="margin:0;">Daftar Kelas</h3>
                <button class="btn-add" onclick="openClassModal()">+ Kelas</button>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Nama Kelas</th>
                        <th>Jurusan</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $item)
                    <tr>
                        <td style="font-weight: 700;">{{ $item->nama_kelas }}</td>
                        <td>
                            <span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; border: 1px solid #e2e8f0;">
                                {{ $item->major->singkatan ?? 'N/A' }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 5px; justify-content: center;">
                                <button onclick='openEditClassModal(@json($item))' style="background:none; border:1px solid #ddd; padding:5px; border-radius:6px; cursor:pointer;">✏️</button>
                                <button onclick="confirmDeleteClass('{{ $item->id }}')" style="background:none; border:1px solid #ddd; padding:5px; border-radius:6px; cursor:pointer; color:red;">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center; padding: 20px; color: #94a3b8;">Belum ada data kelas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<form id="delete-major-form" method="POST" style="display:none;"> @csrf @method('DELETE') </form>
<form id="delete-class-form" method="POST" style="display:none;"> @csrf @method('DELETE') </form>

@include('admin.classrooms.modal_major_create')
@include('admin.classrooms.modal_major_edit')
@include('admin.classrooms.modal_class_create')
@include('admin.classrooms.modal_class_edit')

<script>
    // --- MANAJEMEN MODAL JURUSAN ---
    function openMajorModal() { document.getElementById('modalMajor').style.display = 'block'; }
    function closeMajorModal() { document.getElementById('modalMajor').style.display = 'none'; }
    
    function openMajorEditModal(major) {
        document.getElementById('editMajorForm').action = "/admin/majors/" + major.id;
        document.getElementById('edit_major_name').value = major.nama_jurusan;
        document.getElementById('edit_major_short').value = major.singkatan;
        document.getElementById('modalMajorEdit').style.display = 'block';
    }
    function closeMajorEditModal() { document.getElementById('modalMajorEdit').style.display = 'none'; }

    function confirmDeleteMajor(id) {
        if(confirm('Hapus jurusan ini? Menghapus jurusan akan berdampak pada data kelas terkait.')) {
            let form = document.getElementById('delete-major-form');
            form.action = "/admin/majors/" + id;
            form.submit();
        }
    }

    // --- MANAJEMEN MODAL KELAS ---
    function openClassModal() { document.getElementById('modalClass').style.display = 'block'; }
    function closeClassModal() { document.getElementById('modalClass').style.display = 'none'; }

    function openEditClassModal(item) {
        document.getElementById('editClassForm').action = "/admin/classrooms/" + item.id;
        document.getElementById('edit_class_name').value = item.nama_kelas;
        // Gunakan ID major_id untuk dropdown
        document.getElementById('edit_major_id').value = item.major_id;
        document.getElementById('modalEditClass').style.display = 'block';
    }
    function closeEditClassModal() { document.getElementById('modalEditClass').style.display = 'none'; }

    function confirmDeleteClass(id) {
        if(confirm('Apakah Anda yakin ingin menghapus kelas ini?')) {
            let form = document.getElementById('delete-class-form');
            form.action = "/admin/classrooms/" + id;
            form.submit();
        }
    }

    // Close on Outside Click
    window.onclick = function(event) {
        const modalIds = ['modalMajor', 'modalMajorEdit', 'modalClass', 'modalEditClass'];
        modalIds.forEach(id => {
            const m = document.getElementById(id);
            if (m && event.target == m) m.style.display = 'none';
        });
    }
</script>

@endsection