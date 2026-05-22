@extends('layouts.app')

@section('title', 'Dashboard Admin')

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

    /* ================= MAIN CONTENT LAYOUT ================= */
    .stats-grid {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 22px !important;
        margin-bottom: 30px !important;
    }

    /* Desain Card Statistik Utama (Kontras Tinggi) */
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
        position: relative !important;
        overflow: hidden !important;
        text-decoration: none !important;
    }

    .stat-card:hover {
        transform: translateY(-5px) !important;
        box-shadow: 0 15px 30px rgba(230, 57, 70, 0.15) !important;
        border-color: rgba(230, 57, 70, 0.3) !important;
    }

    /* Wrapper Icon Utama dengan Efek Glow Khas */
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

    /* Pewarnaan Icon Spesifik yang Kontras & Atraktif */
    .icon-siswa { background: rgba(30, 144, 255, 0.1) !important; color: #1e90ff !important; }
    .icon-guru { background: rgba(46, 204, 113, 0.1) !important; color: #2ecc71 !important; }
    .icon-pengawas { background: rgba(241, 196, 15, 0.1) !important; color: #f1c40f !important; }
    .icon-jadwal { background: rgba(230, 57, 70, 0.1) !important; color: #cd0000 !important; }

    /* Hover State: Icon Berubah Penuh & Efek Putar Halus */
    .stat-card:hover .icon-siswa { background: #1e90ff !important; color: #ffffff !important; transform: scale(1.1) rotate(5deg) !important; }
    .stat-card:hover .icon-guru { background: #2ecc71 !important; color: #ffffff !important; transform: scale(1.1) rotate(-5deg) !important; }
    .stat-card:hover .icon-pengawas { background: #f1c40f !important; color: #ffffff !important; transform: scale(1.1) rotate(5deg) !important; }
    .stat-card:hover .icon-jadwal { background: #cd0000 !important; color: #ffffff !important; transform: scale(1.1) rotate(-5deg) !important; }

    .stat-info h2 {
        font-size: 28px !important;
        font-weight: 700 !important;
        color: #1e1e2f !important;
        line-height: 1.2 !important;
        margin: 0 !important;
    }

    .stat-info p {
        font-size: 11px !important;
        font-weight: 700 !important;
        color: #6a6a7a !important;
        letter-spacing: 1px !important;
        margin: 4px 0 0 0 !important;
    }

    /* Pembungkus Konten Box Putih */
    .content-box {
        background: #ffffff !important;
        border-radius: 16px !important;
        padding: 25px !important;
        margin-bottom: 30px !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04) !important;
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
    }

    .box-header h3 {
        font-size: 16px !important;
        font-weight: 700 !important;
        color: #1e1e2f !important;
        margin-bottom: 22px !important;
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
    }

    .header-icon {
        color: #cd0000 !important;
    }

    /* Grid Sistem Informasi Mini (Diatur Menjadi 2 Kolom Proporsional) */
    .mini-stats-grid {
        display: grid !important;
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 20px !important;
    }

    .mini-card {
        padding: 20px !important;
        border: 1px solid #edf0f5 !important;
        border-radius: 12px !important;
        background: #fafafa !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        transition: all 0.3s ease !important;
        text-decoration: none !important;
    }

    .mini-card-info p {
        color: #6a6a7a !important;
        font-size: 13px !important;
        margin: 0 0 6px 0 !important;
        font-weight: 600 !important;
    }

    .mini-card-info h2 {
        font-size: 32px !important;
        font-weight: 700 !important;
        color: #1e1e2f !important;
        margin: 0 !important;
    }

    /* Icon Khusus Mini Card Fitur */
    .mini-card-icon {
        font-size: 26px !important;
        color: #a0a0b0 !important;
        transition: all 0.3s ease !important;
    }

    .mini-card:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.04) !important;
        border-color: #cd0000 !important;
        background: #ffffff !important;
    }

    .mini-card:hover .mini-card-icon {
        color: #cd0000 !important;
        transform: scale(1.1) !important;
    }

    /* ================= CHARTS SECTION LAYOUT ================= */
    .charts-grid {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 25px !important;
    }

    .chart-container-box {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        background: #fdfdfd !important;
        border: 1px solid #edf0f5 !important;
        padding: 20px !important;
        border-radius: 14px !important;
    }

    .chart-wrapper {
        position: relative !important;
        width: 100% !important;
        height: 260px !important; 
        margin-bottom: 20px !important;
    }

    /* ================= BUTTON ACTION MODERATION ================= */
    .btn-action-premium {
        background: linear-gradient(135deg, #cd0000 0%, #950000 100%) !important;
        color: #ffffff !important;
        border: none !important;
        padding: 12px 28px !important;
        border-radius: 30px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        box-shadow: 0 5px 15px rgba(205, 0, 0, 0.25) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        text-decoration: none !important;
    }

    .btn-action-premium:hover {
        transform: translateY(-3px) !important;
        box-shadow: 0 8px 22px rgba(205, 0, 0, 0.4) !important;
        filter: brightness(1.1) !important;
        color: #ffffff !important;
    }

    .btn-action-premium i {
        font-size: 14px !important;
        transition: transform 0.3s ease !important;
    }

    .btn-action-premium:hover i {
        transform: rotate(90deg) !important;
    }
</style>

@section('content')
    <!-- CARD BOX STATISTIK UTAMA - TERHUBUNG KE URL FILTER QUERY ROLE -->
    <div class="stats-grid">
        <a href="{{ route('admin.users.index', ['role' => 'siswa']) }}" class="stat-card">
            <div class="stat-icon-wrapper icon-siswa"><i class="fas fa-user-graduate"></i></div>
            <div class="stat-info">
                <h2>{{ $jml_siswa }}</h2>
                <p>TOTAL SISWA</p>
            </div>
        </a>

        <a href="{{ route('admin.users.index', ['role' => 'guru']) }}" class="stat-card">
            <div class="stat-icon-wrapper icon-guru"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="stat-info">
                <h2>{{ $jml_guru }}</h2>
                <p>TOTAL GURU</p>
            </div>
        </a>

        <a href="{{ route('admin.users.index', ['role' => 'pengawas']) }}" class="stat-card">
            <div class="stat-icon-wrapper icon-pengawas"><i class="fas fa-user-lock"></i></div>
            <div class="stat-info">
                <h2>{{ $jml_pengawas }}</h2>
                <p>TOTAL PENGAWAS</p>
            </div>
        </a>

        <a href="{{ route('admin.schedules.index') }}" class="stat-card">
            <div class="stat-icon-wrapper icon-jadwal"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-info">
                <h2>{{ $jml_schedule }}</h2>
                <p>TOTAL JADWAL UJIAN</p>
            </div>
        </a>
    </div>

    <!-- BOX STATISTIK DATA MASTER MINI -->
    <div class="content-box">
        <div class="box-header">
            <h3><i class="fas fa-chart-pie header-icon"></i> Statistik Sistem</h3>
        </div>
        <div class="mini-stats-grid">
            <a href="{{ route('admin.subjects.index') }}" class="mini-card">
                <div class="mini-card-info">
                    <p>Total Mata Pelajaran</p>
                    <h2>{{ $jml_mata_pelajaran }}</h2>
                </div>
                <i class="fas fa-book-open mini-card-icon"></i>
            </a>
            
            <a href="{{ route('admin.classrooms.index') }}" class="mini-card">
                <div class="mini-card-info">
                    <p>Total Kelas</p>
                    <h2>{{ $jml_kelas }}</h2>
                </div>
                <i class="fas fa-school mini-card-icon"></i>
            </a>
        </div>
    </div>

    <!-- BOX AREA VISUALISASI GRAFIK REAL-TIME -->
    <div class="content-box">
        <div class="box-header">
            <h3><i class="fas fa-chart-line header-icon"></i> Grafik Monitoring Aktivitas</h3>
        </div>
        <div class="charts-grid">
            
            <!-- GRAFIK BATANG DATA JURUSAN / KELAS -->
            <div class="chart-container-box">
                <h4 style="font-size: 14px; color: #1e1e2f; margin: 0 0 15px 0; font-weight: 600; width: 100%; text-align: left;">
                    <i class="fas fa-graduation-cap" style="color: #cd0000; margin-right: 5px;"></i> Distribusi Data Siswa (Per Jurusan/Kelas)
                </h4>
                <div class="chart-wrapper">
                    <canvas id="chartSiswa"></canvas>
                </div>
                <a href="{{ route('admin.schedules.index') }}" class="btn-action-premium">
                    <i class="fas fa-folder-plus"></i> Kelola Data Soal
                </a>
            </div>

            <!-- GRAFIK LINE TREN JADWAL MINGGUAN -->
            <div class="chart-container-box">
                <h4 style="font-size: 14px; color: #1e1e2f; margin: 0 0 15px 0; font-weight: 600; width: 100%; text-align: left;">
                    <i class="fas fa-history" style="color: #cd0000; margin-right: 5px;"></i> Aktivitas Sesi Ujian Seminggu Terakhir
                </h4>
                <div class="chart-wrapper">
                    <canvas id="chartKeaktifan"></canvas>
                </div>
                <a href="{{ route('admin.schedules.index') }}" class="btn-action-premium">
                    <i class="fas fa-sliders-h"></i> Atur Sesi Ujian
                </a>
            </div>

        </div>
    </div>

    <!-- SCRIPT INJEKSI CHART JS AMAN & DINAMIS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Chart.defaults.maintainAspectRatio = false;

            // Injeksi Data JSON Murni dari Controller (Bebas dari Bug Kurung Siku)
            const kelasLabels = @json($kelasLabels);
            const kelasData = @json($kelasData);
            const trenUjianData = @json($trenUjianData);

            // Chart 1: Bar Chart Data Siswa per Jurusan/Kelas otomatis database
            new Chart(document.getElementById('chartSiswa'), {
                type: 'bar',
                data: {
                    labels: kelasLabels,
                    datasets: [{ 
                        label: 'Jumlah Siswa', 
                        data: kelasData, 
                        backgroundColor: '#1e90ff',
                        borderRadius: 6,
                        barThickness: 25
                    }]
                },
                options: { 
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#eef0f4' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // Chart 2: Line Chart Sesi Ujian Seminggu Terakhir otomatis database
            new Chart(document.getElementById('chartKeaktifan'), {
                type: 'line',
                data: {
                    labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
                    datasets: [{ 
                        label: 'Jumlah Jadwal', 
                        data: trenUjianData, 
                        borderColor: '#cd0000', 
                        borderWidth: 3,
                        backgroundColor: 'rgba(205, 0, 0, 0.05)',
                        fill: true,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#cd0000',
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        tension: 0.3 
                    }]
                },
                options: { 
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#eef0f4' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>
@endsection