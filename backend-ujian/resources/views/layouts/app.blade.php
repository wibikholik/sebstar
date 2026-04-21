<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Sebstar Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --red-sebstar: #cd0000;
            --bg-gray: #f0f2f5;
            --border-color: #e1e4e8;
            --text-dark: #1a1a1a;
            --text-gray: #64748b;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--bg-gray); 
            margin: 0; 
            display: flex;
            color: var(--text-dark);
        }

        /* --- SIDEBAR STYLE --- */
        .sidebar { 
            width: 260px; 
            height: 100vh; 
            background: white; 
            position: fixed; 
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--border-color);
        }

        .sidebar-header {
            padding: 30px 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .sidebar-header img { width: 45px; }
        .sidebar-header .brand { font-weight: 700; font-size: 18px; line-height: 1.2; }
        .sidebar-header .brand span { font-weight: 400; font-size: 14px; color: var(--text-gray); }

        .nav-menu { padding: 20px 15px; flex-grow: 1; }
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            font-size: 14px;
            border-radius: 10px;
            margin-bottom: 5px;
            transition: 0.3s;
        }

        .nav-link.active {
            background: #fff1f1;
            color: var(--red-sebstar);
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid var(--border-color);
        }

        .btn-logout {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
            background: none;
            border: none;
            padding: 12px;
            font-family: inherit;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            color: var(--text-dark);
        }

        /* --- MAIN CONTENT STYLE --- */
        .main-content { 
            margin-left: 260px; 
            flex: 1; 
            padding: 30px 50px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
        }

        .header-title h1 { margin: 0; font-size: 26px; font-weight: 700; }
        .header-title p { margin: 5px 0 0; color: var(--text-gray); font-size: 14px; }

        .user-info { text-align: right; font-size: 14px; }
        .user-info .role { font-weight: 700; display: block; }
        .user-info .date { color: var(--text-gray); }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid var(--border-color);
        }

        .stat-card h2 { margin: 0; font-size: 32px; font-weight: 800; }
        .stat-card p { margin: 5px 0 0; color: var(--text-gray); font-size: 13px; font-weight: 500; }

        /* Content Box (The White Container) */
        .content-box {
            background: white;
            border-radius: 15px;
            padding: 30px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }

        /* Search & Action Bar */
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .search-wrapper { position: relative; width: 300px; }
        .search-input {
            width: 100%;
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background: #f8f9fa;
            font-family: inherit;
        }

        .btn-add {
            background: var(--red-sebstar);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        /* Tab Switcher */
        .tab-switcher {
            background: #f0f2f5;
            padding: 5px;
            border-radius: 12px;
            display: flex;
            margin-bottom: 25px;
        }

        .tab-item {
            flex: 1;
            text-align: center;
            padding: 10px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-gray);
            cursor: pointer;
            border-radius: 10px;
            transition: 0.2s;
        }

        .tab-item.active {
            background: white;
            color: var(--text-dark);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        /* Table Style */
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; border-bottom: 2px solid var(--bg-gray); color: var(--text-gray); font-size: 13px; font-weight: 700; }
        td { padding: 15px; border-bottom: 1px solid var(--bg-gray); font-size: 14px; font-weight: 500; }
    </style>
</head>
<body>
@include('layouts.partials.sidebar')
    

    <main class="main-content">
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

        @yield('content')
    </main>

</body>
</html>