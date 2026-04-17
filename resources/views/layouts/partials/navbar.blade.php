<div class="navbar">
    <div class="breadcrumb">
        <strong>SMKN 1 Binong</strong> / @yield('title')
    </div>
    <div class="user-info">
        Halo, <strong>{{ Auth::user()->name }}</strong> ({{ ucfirst(Auth::user()->role) }})
    </div>
</div>