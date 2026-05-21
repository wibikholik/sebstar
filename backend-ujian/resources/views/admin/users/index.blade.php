@extends('layouts.app')

@section('title', 'Kelola Pengguna')

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

    /* ================= STATS CARD SYSTEM ================= */
    .stats-grid {
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
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
    }

    /* Warna Icon Menyesuaikan Fungsi Fitur */
    .icon-siswa { background: rgba(30, 144, 255, 0.1) !important; color: #1e90ff !important; }
    .icon-guru { background: rgba(46, 204, 113, 0.1) !important; color: #2ecc71 !important; }
    .icon-pengawas { background: rgba(241, 196, 15, 0.1) !important; color: #f1c40f !important; }

    .stat-card:hover .icon-siswa { background: #1e90ff !important; color: #ffffff !important; transform: scale(1.1) rotate(5deg) !important; }
    .stat-card:hover .icon-guru { background: #2ecc71 !important; color: #ffffff !important; transform: scale(1.1) rotate(-5deg) !important; }
    .stat-card:hover .icon-pengawas { background: #f1c40f !important; color: #ffffff !important; transform: scale(1.1) rotate(5deg) !important; }

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
        font-size: 18px !important;
        font-weight: 700 !important;
        color: #1e1e2f !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }

    /* Input Search Elegan */
    .search-wrapper {
        position: relative !important;
    }

    .search-input {
        background: #f4f5f9 !important;
        border: 1px solid #edf0f5 !important;
        padding: 10px 16px !important;
        border-radius: 20px !important;
        font-size: 13px !important;
        color: #1e1e2f !important;
        width: 220px !important;
        transition: all 0.3s ease !important;
    }

    .search-input:focus {
        border-color: #cd0000 !important;
        background: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(205, 0, 0, 0.1) !important;
        outline: none !important;
    }

    /* Tombol Tambah Pengguna Premium */
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

    /* Tab Switcher Gaya Navigasi Modern */
    .tab-switcher {
        display: flex !important;
        gap: 10px !important;
        margin-bottom: 25px !important;
        border-bottom: 2px solid #edf0f5 !important;
        padding-bottom: 10px !important;
    }

    .tab-item {
        padding: 8px 20px !important;
        border-radius: 20px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        color: #7a7a8a !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
    }

    .tab-item:hover {
        background: rgba(230, 57, 70, 0.05) !important;
        color: #cd0000 !important;
    }

    .tab-item.active {
        background: #cd0000 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(205, 0, 0, 0.15) !important;
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

    tr:hover td {
        background: #fcfcfd !important;
    }

    /* Badge Role Custom */
    .badge-role {
        text-transform: uppercase !important;
        padding: 5px 12px !important;
        border-radius: 20px !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        letter-spacing: 0.5px !important;
    }
    .badge-siswa { background: rgba(30, 144, 255, 0.1) !important; color: #1e90ff !important; }
    .badge-guru { background: rgba(46, 204, 113, 0.1) !important; color: #2ecc71 !important; }
    .badge-pengawas { background: rgba(241, 196, 15, 0.1) !important; color: #b79407 !important; }

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
    
    .btn-table-action.btn-delete { color: #e74c3c !important; margin-left: 4px !important; }
    .btn-table-action.btn-delete:hover { border-color: #e74c3c !important; background: rgba(231, 76, 60, 0.05) !important; }
</style>

@section('content')

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon-wrapper icon-siswa"><i class="fas fa-user-graduate"></i></div>
        <div class="stat-info">
            <h2>{{ $users->where('role', 'siswa')->count() }}</h2>
            <p>TOTAL SISWA</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon-wrapper icon-guru"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="stat-info">
            <h2>{{ $users->where('role', 'guru')->count() }}</h2>
            <p>TOTAL GURU</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon-wrapper icon-pengawas"><i class="fas fa-user-lock"></i></div>
        <div class="stat-info">
            <h2>{{ $users->where('role', 'pengawas')->count() }}</h2>
            <p>TOTAL PENGAWAS</p>
        </div>
    </div>
</div>

<div class="content-box">
    <div class="action-bar">
        <h3><i class="fas fa-users-cog header-icon"></i> Manajemen Akun Pengguna</h3>
        <div style="display: flex; gap: 15px; align-items: center;">
            <div class="search-wrapper">
                <input type="text" class="search-input" placeholder="Cari nama / nomor...">
            </div>
            <button class="btn-add" onclick="openModal()">
                <i class="fas fa-user-plus"></i> Tambah Akun
            </button>
        </div>
    </div>

    <div class="tab-switcher">
        <div class="tab-item {{ !request('role') ? 'active' : '' }}" onclick="location.href='{{ route('admin.users.index') }}'">Semua Pengguna</div>
        <div class="tab-item {{ request('role') == 'siswa' ? 'active' : '' }}" onclick="location.href='{{ route('admin.users.index', ['role' => 'siswa']) }}'">Siswa</div>
        <div class="tab-item {{ request('role') == 'guru' ? 'active' : '' }}" onclick="location.href='{{ route('admin.users.index', ['role' => 'guru']) }}'">Guru</div>
        <div class="tab-item {{ request('role') == 'pengawas' ? 'active' : '' }}" onclick="location.href='{{ route('admin.users.index', ['role' => 'pengawas']) }}'">Pengawas</div>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 14px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; border: 1px solid #c3e6cb; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: #fee2e2; color: #b91c1c; padding: 14px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; border: 1px solid #fecaca; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div style="border: 1px solid #edf0f5; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.01);">
        <table>
            <thead>
                <tr>
                    <th>NIS / NIP</th>
                    <th>Nama Lengkap</th>
                    <th>Kelas / Mata Pelajaran</th>
                    <th style="text-align: center;">Hak Akses</th>
                    <th style="text-align: center;">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td style="color: #64748b; font-weight: 500; font-size: 13px;">
                        @if($user->role == 'siswa')
                            {{ $user->nis ?? '-' }}
                        @elseif($user->role == 'guru')
                            {{ $user->nip ?? '-' }}
                        @else
                            <span style="color: #cbd5e1;">-</span>
                        @endif
                    </td>
                    <td style="font-weight: 600; color: #1e1e2f;">{{ $user->name }}</td>
                    <td>
                        @if($user->role == 'siswa')
                            <span style="color: #3b82f6; font-weight: 500;"><i class="fas fa-school" style="font-size: 12px; margin-right: 4px;"></i> {{ $user->classroom->nama_kelas ?? 'Belum Set' }}</span>
                        @elseif($user->role == 'guru')
                            <span style="color: #10b981; font-weight: 500;"><i class="fas fa-book" style="font-size: 12px; margin-right: 4px;"></i> {{ $user->subject->nama_mapel ?? 'Belum Set' }}</span>
                        @else
                            <span style="color: #94a3b8; font-size: 13px;">Akses Penuh Sistem</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <span class="badge-role badge-{{ $user->role }}">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <button onclick='openEditModal(@json($user))' class="btn-table-action btn-edit" title="Ubah Data">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" onclick="confirmDelete('{{ $user->id }}', '{{ $user->name }}')" class="btn-table-action btn-delete" title="Hapus Data">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 50px; color: #94a3b8;">
                        <i class="fas fa-folder-open" style="font-size: 32px; display: block; margin-bottom: 10px; color: #cbd5e1;"></i>
                        Data pengguna tidak ditemukan dalam database.
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

    function toggleFields(role, mode) {
        const prefix = mode === 'edit' ? 'edit_' : '';
        const siswaDiv = document.getElementById(mode === 'edit' ? 'editSiswaFields' : 'createSiswaFields');
        const guruDiv = document.getElementById(mode === 'edit' ? 'editGuruFields' : 'createGuruFields');

        if (role === 'siswa') {
            if(siswaDiv) siswaDiv.style.display = 'block';
            if(guruDiv) guruDiv.style.display = 'none';
        } else if (role === 'guru') {
            if(siswaDiv) siswaDiv.style.display = 'none';
            if(guruDiv) guruDiv.style.display = 'block';
        } else {
            if(siswaDiv) siswaDiv.style.display = 'none';
            if(guruDiv) guruDiv.style.display = 'none';
        }
    }

    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            event.target.style.display = "none";
        }
    }
</script>
@endsection