@extends('layouts.app')
@section('title', 'Jadwal Ujian Saya')

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

    /* Pembungkus Konten Box Putih Berstandar Premium */
    .content-box {
        background: #ffffff !important;
        border-radius: 16px !important;
        padding: 25px !important;
        margin-bottom: 30px !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04) !important;
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
    }

    /* ================= HEADER ACTIONS SECTION (ANTI TUMPUK) ================= */
    .header-container {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        margin-bottom: 25px !important;
        gap: 20px !important;
        width: 100% !important;
    }

    .header-title-section {
        flex: 1 !important;
    }

    .header-action-section {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        flex-wrap: nowrap !important; /* Mengunci elemen agar selalu ke samping */
        flex-shrink: 0 !important;
    }

    /* ================= SEARCH BAR DESIGN ================= */
    .search-wrapper {
        display: flex !important;
        align-items: center !important;
        background: #fafafa !important;
        border: 1px solid #edf0f5 !important;
        border-radius: 30px !important;
        padding: 4px 6px 4px 16px !important;
        width: 280px !important; /* Dikunci agar pas berdampingan */
        transition: all 0.3s ease !important;
        flex-shrink: 0 !important;
    }

    .search-wrapper:focus-within {
        border-color: #cd0000 !important;
        background: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(205, 0, 0, 0.1) !important;
    }

    .search-input {
        border: none !important;
        background: transparent !important;
        outline: none !important;
        width: 100% !important;
        font-size: 13px !important;
        color: #1e1e2f !important;
        padding: 6px 0 !important;
    }

    .search-input::placeholder {
        color: #a0a0b0 !important;
    }

    .btn-search-submit {
        background: #1e1e2f !important;
        color: #ffffff !important;
        border: none !important;
        padding: 6px 14px !important;
        border-radius: 20px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        transition: background 0.2s !important;
        flex-shrink: 0 !important;
    }

    .btn-search-submit:hover {
        background: #cd0000 !important;
    }

    .btn-search-reset {
        color: #a0a0b0 !important;
        background: transparent !important;
        border: none !important;
        font-size: 14px !important;
        cursor: pointer !important;
        padding: 0 6px !important;
        display: flex !important;
        align-items: center !important;
        text-decoration: none !important;
    }

    .btn-search-reset:hover {
        color: #cd0000 !important;
    }

    /* ================= TABLE PREMIUM DESIGN ================= */
    .table-responsive {
        overflow-x: auto !important;
        border-radius: 12px !important;
        border: 1px solid #edf0f5 !important;
    }

    .custom-table {
        width: 100% !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        background: #ffffff !important;
    }

    .custom-table thead tr {
        background: #fafafa !important;
    }

    .custom-table th {
        padding: 16px !important;
        border-bottom: 2px solid #edf0f5 !important;
        color: #1e1e2f !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        letter-spacing: 0.5px !important;
        text-transform: uppercase !important;
    }

    .custom-table td {
        padding: 16px !important;
        border-bottom: 1px solid #edf0f5 !important;
        color: #1e1e2f !important;
        vertical-align: middle !important;
    }

    .custom-table tbody tr {
        transition: all 0.2s ease !important;
    }

    .custom-table tbody tr:hover {
        background-color: rgba(230, 57, 70, 0.01) !important;
    }

    /* ================= BUTTON ACTION MODERATION ================= */
    .btn-action-premium {
        background: linear-gradient(135deg, #cd0000 0%, #950000 100%) !important;
        color: #ffffff !important;
        border: none !important;
        padding: 10px 20px !important;
        border-radius: 30px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        box-shadow: 0 5px 15px rgba(205, 0, 0, 0.25) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        text-decoration: none !important;
        white-space: nowrap !important;
        flex-shrink: 0 !important;
    }

    .btn-action-premium:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 22px rgba(205, 0, 0, 0.4) !important;
        filter: brightness(1.1) !important;
    }

    /* Mini Action Buttons */
    .btn-table-action {
        padding: 6px 14px !important;
        border-radius: 8px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        border: none !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
        transition: all 0.2s !important;
        text-decoration: none !important;
    }

    .btn-table-action.manage { background: #1e1e2f !important; color: #ffffff !important; }
    .btn-table-action.manage:hover { background: #cd0000 !important; }
    .btn-table-action.edit { background: #fafafa !important; border: 1px solid #edf0f5 !important; color: #6a6a7a !important; }
    .btn-table-action.edit:hover { border-color: #cd0000 !important; color: #cd0000 !important; }
    .btn-table-action.delete { background: rgba(230, 57, 70, 0.1) !important; color: #cd0000 !important; }
    .btn-table-action.delete:hover { background: #cd0000 !important; color: #ffffff !important; }

    /* ================= BADGES & MISC ================= */
    .badge-type { font-size: 10px !important; background: rgba(30, 144, 255, 0.1) !important; color: #1e90ff !important; padding: 4px 10px !important; border-radius: 20px !important; font-weight: 700 !important; }
    .badge-class { background: #fafafa !important; border: 1px solid #edf0f5 !important; padding: 4px 10px !important; border-radius: 6px !important; font-size: 12px !important; font-weight: 600; color: #1e1e2f !important; }
    .badge-source-mandiri { font-size: 10px !important; color: #2ecc71 !important; border: 1px solid #2ecc71 !important; padding: 3px 8px !important; border-radius: 10px !important; font-weight: 700 !important; }
    .badge-source-pusat { font-size: 10px !important; color: #cd0000 !important; border: 1px solid #cd0000 !important; padding: 3px 8px !important; border-radius: 10px !important; font-weight: 700 !important; }
    .token-box { font-family: monospace !important; font-size: 14px !important; font-weight: 800 !important; background: #fffbeb !important; color: #b45309 !important; padding: 5px 12px !important; border-radius: 6px !important; border: 1px dashed #f59e0b !important; }

    /* ================= MODAL CUSTOM STANDARD ================= */
    .modal-custom { 
        display: none; 
        position: fixed; 
        z-index: 9999; 
        left: 0; 
        top: 0; 
        width: 100%; 
        height: 100%; 
        background: rgba(30, 30, 47, 0.4) !important;
        backdrop-filter: blur(4px) !important;
    }
    .modal-content-custom { 
        background: #ffffff !important; 
        margin: 8% auto; 
        padding: 25px !important; 
        border-radius: 16px !important; 
        width: 480px; 
        position: relative; 
        box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
        border: 1px solid rgba(0,0,0,0.05) !important;
    }
</style>

@section('content')
<div class="content-box">
    
    {{-- Header Container Utama --}}
    <div class="header-container">
        {{-- Sisi Kiri: Judul Halaman --}}
        <div class="header-title-section">
            <h3 style="margin: 0; color: #1e1e2f; font-weight: 700; font-size: 22px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-calendar-check" style="color: #cd0000;"></i> Manajemen Jadwal & Kuis
            </h3>
            <p style="margin: 5px 0 0 0; color: #6a6a7a; font-size: 14px;">Kelola ujian mandiri atau pantau jadwal ujian terpusat dari Admin.</p>
        </div>
        
        {{-- Sisi Kanan: Search Bar + Tombol Terkunci Berdampingan (Anti Tumpuk) --}}
        <div class="header-action-section">
            <form action="{{ url()->current() }}" method="GET" style="margin: 0; padding: 0; display: block;">
                <div class="search-wrapper">
                    <i class="fas fa-search" style="color: #a0a0b0; margin-right: 8px; font-size: 13px;"></i>
                    <input type="text" name="search" value="{{ request('search') }}" class="search-input" placeholder="Cari mapel atau kelas...">
                    
                    @if(request('search'))
                        <a href="{{ url()->current() }}" class="btn-search-reset" title="Hapus Pencarian">
                            <i class="fas fa-times-circle"></i>
                        </a>
                    @endif
                    
                    <button type="submit" class="btn-search-submit">Cari</button>
                </div>
            </form>

            <button onclick="openCreateModal()" class="btn-action-premium">
                <i class="fas fa-plus-circle"></i> Buat Jadwal Mandiri
            </button>
        </div>
    </div>

    {{-- Alert Notifikasi Kustom --}}
    @if(session('success'))
        <div style="background: rgba(46, 204, 113, 0.1); border-left: 4px solid #2ecc71; color: #2ecc71; padding: 15px; border-radius: 8px; margin-bottom: 25px; font-weight: 600; font-size: 14px;">
            <i class="fas fa-check-circle" style="margin-right: 5px;"></i> {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div style="background: rgba(230, 57, 70, 0.1); border-left: 4px solid #cd0000; color: #cd0000; padding: 15px; border-radius: 8px; margin-bottom: 25px; font-weight: 600; font-size: 14px;">
            <i class="fas fa-exclamation-triangle" style="margin-right: 5px;"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Konten Utama Tabel --}}
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 30%;">Tipe & Mata Pelajaran</th>
                    <th>Kelas</th>
                    <th>Waktu & Durasi</th>
                    <th style="text-align: center;">Token</th>
                    <th style="text-align: center;">Sumber</th>
                    <th style="text-align: center; width: 20%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $s)
                <tr>
                    <td>
                        <span class="badge-type">
                            {{ $s->examType->name ?? 'N/A' }}
                        </span>
                        <div style="font-weight: 700; color: #1e1e2f; margin-top: 6px; font-size: 15px;">{{ $s->subject->nama_mapel }}</div>
                    </td>
                    <td>
                        <span class="badge-class">
                            <i class="fas fa-door-open" style="color: #a0a0b0; margin-right: 4px;"></i> Kelas {{ $s->classroom->nama_kelas }}
                        </span>
                    </td>
                    <td>
                        <div style="font-size: 13px; font-weight: 700; color: #1e1e2f;">📅 {{ date('d/m/Y', strtotime($s->tanggal_ujian)) }}</div>
                        <div style="font-size: 11px; color: #cd0000; font-weight: 700; margin-top: 2px;">⏱️ {{ $s->durasi }} Menit</div>
                    </td>
                    <td style="text-align: center;">
                        <span class="token-box">{{ $s->token }}</span>
                    </td>
                    <td style="text-align: center;">
                        @if($s->created_by == auth()->id())
                            <span class="badge-source-mandiri">MANDIRI</span>
                        @else
                            <span class="badge-source-pusat">PUSAT</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                            {{-- Akses Kelola Soal --}}
                            <a href="{{ route('guru.questions.manage', $s->id) }}" class="btn-table-action manage">
                                <i class="fas fa-edit"></i> Soal
                            </a>
                            
                            {{-- Edit & Hapus Khusus Jadwal Mandiri --}}
                            @if($s->created_by == auth()->id())
                                <button type="button" onclick='openEditModal(@json($s))' class="btn-table-action edit" title="Ubah Jadwal">
                                    <i class="fas fa-pen"></i>
                                </button>

                                <form action="{{ route('guru.schedules.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal mandiri ini? Seluruh soal terkait akan tetap ada di bank soal namun jadwal akan hilang.')" style="margin: 0;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-table-action delete" title="Hapus Jadwal">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #6a6a7a; font-weight: 600;">
                        <i class="fas fa-folder-open" style="font-size: 24px; color: #a0a0b0; display: block; margin-bottom: 10px;"></i>
                        @if(request('search'))
                            Hasil pencarian untuk "{{ request('search') }}" tidak ditemukan.
                        @else
                            Belum ada jadwal ujian yang tersedia.
                        @endif
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

{{-- Script Logika Modal --}}
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
@endsection