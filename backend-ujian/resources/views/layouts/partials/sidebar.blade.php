<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
    /* Kerangka Utama Sidebar Transparan Mewah */
    .sidebar {
        width: 260px !important;
        height: 100vh !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        /* Efek Kaca Tembus Pandang ke Polkadot */
        background: rgba(255, 255, 255, 0.45) !important;
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
        /* Batasan Nyata Kiri Kanan */
        border-right: 1px solid rgba(230, 57, 70, 0.15) !important;
        display: flex !important;
        flex-direction: column !important;
        z-index: 1000 !important;
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.02) !important;
    }

    /* Header Brand */
    .sidebar-header {
        padding: 24px 20px !important;
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        border-bottom: 1px solid rgba(230, 57, 70, 0.08) !important;
    }

    .sidebar-header img {
        width: 42px !important;
        height: 42px !important;
        border-radius: 10px !important;
        object-fit: cover !important;
        box-shadow: 0 4px 10px rgba(230, 57, 70, 0.2) !important;
    }

    .brand {
        font-size: 16px !important;
        font-weight: 800 !important;
        color: #1e1e2f !important;
        letter-spacing: 1px !important;
        line-height: 1.2 !important;
    }

    .brand span {
        font-size: 10px !important;
        font-weight: 700 !important;
        color: #cd0000 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
    }

    /* Menu List */
    .nav-menu {
        flex: 1 !important;
        padding: 20px 14px !important;
        overflow-y: auto !important;
    }

    .menu-label {
        font-size: 10px !important;
        font-weight: 800 !important;
        letter-spacing: 1px !important;
        margin-top: 15px !important;
        margin-bottom: 5px !important;
    }

    /* Link Menu Navigasi */
    .nav-link {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        padding: 12px 16px !important;
        color: #4a5568 !important;
        font-weight: 600 !important;
        font-size: 13.5px !important;
        text-decoration: none !important;
        border-radius: 12px !important;
        margin-bottom: 4px !important;
        transition: all 0.25s ease !important;
    }

    /* Efek Sorotan (Hover) */
    .nav-link:hover {
        background: rgba(230, 57, 70, 0.06) !important;
        color: #cd0000 !important;
        padding-left: 20px !important;
    }

    /* Menu yang sedang Aktif (Menyala Merah Premium) */
    .nav-link.active {
        background: linear-gradient(135deg, #cd0000 0%, #950000 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 8px 16px rgba(205, 0, 0, 0.25) !important;
    }

    .nav-link i {
        font-size: 16px !important;
        width: 20px !important;
        text-align: center !important;
        transition: transform 0.3s ease !important;
    }

    .nav-link:hover i {
        transform: scale(1.1) !important;
    }

    /* Footer / Tombol Keluar */
    .sidebar-footer {
        padding: 20px !important;
        border-top: 1px solid rgba(230, 57, 70, 0.08) !important;
    }

    .btn-logout {
        background: rgba(230, 57, 70, 0.08) !important;
        color: #e74c3c !important;
        padding: 12px !important;
        border-radius: 12px !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        transition: all 0.2s ease !important;
    }

    .btn-logout:hover {
        background: #e74c3c !important;
        color: #ffffff !important;
        box-shadow: 0 5px 12px rgba(231, 76, 60, 0.2) !important;
    }
</style>

<aside class="sidebar">
    <div class="sidebar-header">
        <img src="{{ asset('images/LOGO.png') }}" alt="Logo"> 
        <div class="brand">
            SEBSTAR<br>
            <span>{{ ucfirst(auth()->user()->role) }}</span>
        </div>
    </div>

    <nav class="nav-menu">
        {{-- MENU DASHBOARD (SEMUA ROLE) --}}
        <a href="{{ route(auth()->user()->role . '.dashboard') }}" 
           class="nav-link {{ request()->routeIs('*.dashboard') ? 'active' : '' }}">
           <i class="fas fa-th-large"></i> Dashboard
        </a>

        {{-- MENU KHUSUS ADMIN --}}
        @if(auth()->user()->role == 'admin')
            <div class="menu-label" style="color: #94a3b8;">ADMINISTRATOR</div>
            
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i> Kelola Pengguna
            </a>
            <a href="{{ route('admin.exam-types.index') }}" class="nav-link {{ request()->routeIs('admin.exam-types.*') ? 'active' : '' }}">
                <i class="fas fa-layer-group"></i> Tipe Ujian
            </a>
            <a href="{{ route('admin.schedules.index') }}" class="nav-link {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i> Jadwal Ujian
            </a>
            <a href="{{ route('admin.subjects.index') }}" class="nav-link {{ request()->is('admin/subjects*') ? 'active' : '' }}">
                <i class="fas fa-book"></i> Mata Pelajaran
            </a>
            <a href="{{ route('admin.classrooms.index') }}" class="nav-link {{ request()->is('admin/classrooms*') ? 'active' : '' }}">
                <i class="fas fa-school"></i> Kelas
            </a>
        @endif

        {{-- MENU KHUSUS GURU --}}
        @if(auth()->user()->role == 'guru')
            <div class="menu-label" style="color: #94a3b8;">INSTRUKSIONAL</div>
            
            <a href="{{ route('guru.schedules.index') }}" class="nav-link {{ request()->routeIs('guru.schedules.*') ? 'active' : '' }}">
                <i class="fas fa-folder-open"></i> Jadwal & Bank Soal
            </a>
            <a href="{{ route('guru.koreksi.list') }}" class="nav-link {{ request()->routeIs('guru.koreksi.*') ? 'active' : '' }}">
                <i class="fas fa-pen-nib"></i> Koreksi & Nilai
            </a>

            {{-- Menu Monitoring untuk Guru --}}
            <div class="menu-label" style="color: #cd0000;">PENGAWASAN</div>
            <a href="{{ route('guru.monitoring.index') }}" class="nav-link {{ request()->routeIs('guru.monitoring.*') ? 'active' : '' }}">
                <i class="fas fa-desktop" style="color: #cd0000;"></i> Monitoring Ujian
            </a>
        @endif

        {{-- MENU KHUSUS PENGAWAS MURNI --}}
        @if(auth()->user()->role == 'pengawas')
            <div class="menu-label" style="color: #cd0000;">PENGAWASAN</div>
            <a href="{{ route('pengawas.monitoring.index') }}" class="nav-link {{ request()->routeIs('pengawas.monitoring.*') ? 'active' : '' }}">
                <i class="fas fa-desktop" style="color: #cd0000;"></i> Monitoring Ujian
            </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout" style="width: 100%; border: none; cursor: pointer;">
                <i class="fas fa-sign-out-alt"></i> Keluar Aplikasi
            </button>
        </form>
    </div>
</aside>