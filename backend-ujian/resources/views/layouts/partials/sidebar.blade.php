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
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                Kelola Pengguna
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
    <a href="{{ route('guru.ujian-terpusat.index') }}" class="nav-link {{ request()->routeIs('guru.questions.*') ? 'active' : '' }}">
       Ujian Terpusat
    </a>
    <a href="" class="nav-link {{ request()->routeIs('guru.bank-soal.*') ? 'active' : '' }}">
       Bank Soal Mandiri 
    </a>
    <a href="" class="nav-link {{ request()->is('guru/results*') ? 'active' : '' }}">
        Koreksi & Nilai
    </a>
@endif
    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout">
                Keluar
            </button>
        </form>
    </div>
</aside>