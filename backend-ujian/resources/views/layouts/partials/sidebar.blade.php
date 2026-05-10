<aside class="sidebar">
    <div class="sidebar-header">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTsLYUed8N60icvusuoALXWZe8DCFuiw3iCxg&s" alt="Logo"> 
        <div class="brand" style="height: auto;">
            SEBSTAR<br>
            <span>{{ ucfirst(auth()->user()->role) }}</span>
        </div>
    </div>

    <nav class="nav-menu">
        {{-- MENU DASHBOARD (SEMUA ROLE) --}}
        <a href="{{ route(auth()->user()->role . '.dashboard') }}" 
           class="nav-link {{ request()->routeIs('*.dashboard') ? 'active' : '' }}">
           Dashboard
        </a>

        {{-- MENU KHUSUS ADMIN --}}
        @if(auth()->user()->role == 'admin')
            <div class="menu-label" style="font-size: 10px; color: #94a3b8; padding: 10px 20px 5px; font-weight: 800;">ADMINISTRATOR</div>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                Kelola Pengguna
            </a>
                <a href="{{ route('admin.exam-types.index') }}" class="nav-link {{ request()->routeIs('admin.exam-types.*') ? 'active' : '' }}">
            Tipe Ujian
        </a>
            <a href="{{ route('admin.schedules.index') }}" class="nav-link {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
                Jadwal Ujian
            </a>
            <a href="{{ route('admin.subjects.index') }}" class="nav-link {{ request()->is('admin/subjects*') ? 'active' : '' }}">
                Mata Pelajaran
            </a>
            <a href="{{ route('admin.classrooms.index') }}" class="nav-link {{ request()->is('admin/classrooms*') ? 'active' : '' }}">
                Kelas
            </a>
        @endif

        {{-- MENU KHUSUS GURU --}}
        @if(auth()->user()->role == 'guru')
            <div class="menu-label" style="font-size: 10px; color: #94a3b8; padding: 10px 20px 5px; font-weight: 800;">INSTRUKSIONAL</div>
            
            <a href="{{ route('guru.schedules.index') }}" class="nav-link {{ request()->routeIs('guru.schedules.*') ? 'active' : '' }}">
                Jadwal Ujian & Bank Soal
            </a>
            <a href="{{ route('guru.koreksi.list') }}" class="nav-link {{ request()->routeIs('guru.koreksi.*') ? 'active' : '' }}">
                Koreksi & Nilai
            </a>

            {{-- Menu Monitoring untuk Guru --}}
            <div class="menu-label" style="font-size: 10px; color: #cd0000; padding: 10px 20px 5px; font-weight: 800;">PENGAWASAN</div>
            <a href="{{ route('guru.monitoring.index') }}" class="nav-link {{ request()->routeIs('guru.monitoring.*') ? 'active' : '' }}" style="border-left: 3px solid #cd0000;">
                Monitoring Ujian
            </a>
        @endif

        {{-- MENU KHUSUS PENGAWAS MURNI --}}
        @if(auth()->user()->role == 'pengawas')
            <div class="menu-label" style="font-size: 10px; color: #cd0000; padding: 10px 20px 5px; font-weight: 800;">PENGAWASAN</div>
            <a href="{{ route('pengawas.monitoring.index') }}" class="nav-link {{ request()->routeIs('pengawas.monitoring.*') ? 'active' : '' }}" style="border-left: 3px solid #cd0000;">
                Monitoring Ujian
            </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout" style="width: 100%; border: none; cursor: pointer;">
                Keluar
            </button>
        </form>
    </div>
</aside>