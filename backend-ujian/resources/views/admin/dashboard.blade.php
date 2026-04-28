@extends('layouts.app')

@section('title', 'Dashboard Utama')

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <h2>{{ $jml_guru }}</h2>
            <p>TOTAL GURU</p>
        </div>
        <div class="stat-card">
            <h2>{{ $jml_siswa }}</h2>
            <p>TOTAL SISWA</p>
        </div>
        <div class="stat-card">
            <h2>{{ $jml_pengawas }}</h2>
            <p>TOTAL PENGAWAS</p>
        </div>
    </div>

    <div class="content-box">
        <div class="header-title" style="margin-bottom: 25px;">
            <h1 style="font-size: 20px;">Statistik Data Sekolah</h1>
            <p>Visualisasi data distribusi siswa dan keaktifan sistem</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
            <div style="background: #f8f9fa; padding: 20px; border-radius: 12px; border: 1px solid var(--border-color);">
                <h3 style="font-size: 14px; font-weight: 700; margin-bottom: 15px; color: var(--text-dark);">Distribusi Siswa per Jenjang</h3>
                <div style="height: 250px; position: relative;">
                    <canvas id="chartSiswa"></canvas>
                </div>
            </div>

            <div style="background: #f8f9fa; padding: 20px; border-radius: 12px; border: 1px solid var(--border-color);">
                <h3 style="font-size: 14px; font-weight: 700; margin-bottom: 15px; color: var(--text-dark);">Statistik Keaktifan Sistem</h3>
                <div style="height: 250px; position: relative;">
                    <canvas id="chartKeaktifan"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#e1e4e8', borderDash: [5, 5] } },
                    x: { grid: { display: false } }
                }
            };

            // Chart Siswa (Bar)
            new Chart(document.getElementById('chartSiswa'), {
                type: 'bar',
                data: {
                    labels: ['Kelas 10', 'Kelas 11', 'Kelas 12'],
                    datasets: [{
                        data: [120, 150, 180], // Bisa diganti data dinamis jika ada
                        backgroundColor: '#cd0000',
                        borderRadius: 8,
                        barThickness: 35
                    }]
                },
                options: commonOptions
            });

            // Chart Keaktifan (Line)
            new Chart(document.getElementById('chartKeaktifan'), {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    datasets: [{
                        data: [30, 45, 35, 60, 50, 75],
                        borderColor: '#cd0000',
                        backgroundColor: 'rgba(205, 0, 0, 0.05)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#cd0000'
                    }]
                },
                options: commonOptions
            });
        });
    </script>
@endsection