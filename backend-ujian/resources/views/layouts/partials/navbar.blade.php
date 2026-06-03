  <header class="header-content">
            <div class="header-title">
                <h1>@yield('title')</h1>
                <p>Selamat datang di SEBSTAR</p>
            </div>
            <div class="user-info">
                <span class="role">{{ auth()->user()->role }}</span>
                <span class="date">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </header>