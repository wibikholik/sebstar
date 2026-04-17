@extends('layouts.app')

@section('title', 'Kelola Pengguna')

@section('content')

<div class="stats-grid">
    <div class="stat-card">
        <h2>{{ $users->where('role', 'siswa')->count() }}</h2>
        <p>Total Siswa</p>
    </div>
    <div class="stat-card">
        <h2>{{ $users->where('role', 'guru')->count() }}</h2>
        <p>Total Guru</p>
    </div>
    <div class="stat-card">
        <h2>{{ $users->where('role', 'pengawas')->count() }}</h2>
        <p>Total Pengawas</p>
    </div>
</div>

<div class="content-box">
    <div class="action-bar">
        <h3 style="margin: 0;">Kelola Pengguna</h3>
        <div style="display: flex; gap: 15px;">
            <div class="search-wrapper">
                <input type="text" class="search-input" placeholder="Cari Pengguna...">
            </div>
            <button class="btn-add" onclick="openModal()">
                <span style="font-size: 18px;">+</span> Pengguna
            </button>
        </div>
    </div>



    <div class="tab-switcher">
        <div class="tab-item {{ !request('role') ? 'active' : '' }}" onclick="location.href='{{ route('admin.users.index') }}'">Semua</div>
        <div class="tab-item {{ request('role') == 'siswa' ? 'active' : '' }}" onclick="location.href='{{ route('admin.users.index', ['role' => 'siswa']) }}'">Siswa</div>
        <div class="tab-item {{ request('role') == 'guru' ? 'active' : '' }}" onclick="location.href='{{ route('admin.users.index', ['role' => 'guru']) }}'">Guru</div>
        <div class="tab-item {{ request('role') == 'pengawas' ? 'active' : '' }}" onclick="location.href='{{ route('admin.users.index', ['role' => 'pengawas']) }}'">Pengawas</div>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif
@if(session('error'))
    <div style="background: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #fecaca;">
        {{ session('error') }}
    </div>
@endif
    <table style="border: 1px solid #f0f2f5; border-radius: 12px; overflow: hidden;">
        <thead>
            <tr>
                <th>NIS/NIP</th>
                <th>Nama</th>
                <th>Kelas/Mapel</th>
                <th style="text-align: center;">Role</th>
                <th style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td style="color: #64748b; font-size: 13px;">
                    @if($user->role == 'siswa')
                        {{ $user->nis ?? '-' }}
                    @elseif($user->role == 'guru')
                        {{ $user->nip ?? '-' }}
                    @else
                        -
                    @endif
                </td>
                <td style="font-weight: 600;">{{ $user->name }}</td>
                <td>
                    @if($user->role == 'siswa')
                        <span style="color: #3b82f6;">{{ $user->classroom->nama_kelas ?? 'Belum Set' }}</span>
                    @elseif($user->role == 'guru')
                        <span style="color: #10b981;">{{ $user->subject->nama_mapel ?? 'Belum Set' }}</span>
                    @else
                        -
                    @endif
                </td>
                <td style="text-align: center;">
                    <span style="text-transform: uppercase; padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; background: #f1f5f9; color: #475569;">
                        {{ $user->role }}
                    </span>
                </td>
                <td style="text-align: center;">
                    <button onclick='openEditModal(@json($user))' title="Edit" style="background: none; border: 1px solid #ddd; padding: 5px 8px; border-radius: 6px; cursor: pointer; color: #64748b;">✏️</button>
                    <button type="button" onclick="confirmDelete('{{ $user->id }}', '{{ $user->name }}')" title="Hapus" style="background: none; border: 1px solid #ddd; padding: 5px 8px; border-radius: 6px; cursor: pointer; color: #e74c3c;">🗑️</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 40px; color: #94a3b8;">Data tidak ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@include('admin.users.create')
@include('admin.users.edit')

<script>
    function openModal() {
        document.getElementById("userModal").style.display = "block";
    }
    function closeModal() {
        document.getElementById("userModal").style.display = "none";
    }

    function openEditModal(user) {
        document.getElementById('editForm').action = "/admin/users/" + user.id;
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_role').value = user.role;
        
        // Panggil toggle fungsi untuk menyesuaikan field yang muncul
        toggleFields(user.role, 'edit');

        if (user.role === 'siswa') {
            document.getElementById('edit_nis').value = user.nis || '';
            document.getElementById('edit_classroom_id').value = user.classroom_id || '';
        } else if (user.role === 'guru') {
            document.getElementById('edit_nip').value = user.nip || '';
            document.getElementById('edit_subject_id').value = user.subject_id || '';
        }

        document.getElementById("editUserModal").style.display = "block";
    }

    function closeEditModal() {
        document.getElementById("editUserModal").style.display = "none";
    }

    function confirmDelete(id, name) {
        if (confirm("Apakah Anda yakin ingin menghapus user '" + name + "'?")) {
            let form = document.getElementById('delete-form');
            form.action = "/admin/users/" + id;
            form.submit();
        }
    }

    // Fungsi untuk menyembunyikan/menampilkan field berdasarkan Role
    function toggleFields(role, mode) {
        const prefix = mode === 'edit' ? 'edit_' : '';
        const siswaDiv = document.getElementById(mode === 'edit' ? 'editSiswaFields' : 'createSiswaFields');
        const guruDiv = document.getElementById(mode === 'edit' ? 'editGuruFields' : 'createGuruFields');

        if (role === 'siswa') {
            siswaDiv.style.display = 'block';
            guruDiv.style.display = 'none';
        } else if (role === 'guru') {
            siswaDiv.style.display = 'none';
            guruDiv.style.display = 'block';
        } else {
            siswaDiv.style.display = 'none';
            guruDiv.style.display = 'none';
        }
    }

    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            event.target.style.display = "none";
        }
    }
</script>
@endsection