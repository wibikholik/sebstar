@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 25px;">
        <div class="stat-card">
            <h2 style="font-size: 28px;">{{ $jml_siswa }}</h2>
            <p>TOTAL SISWA</p>
        </div>
        <div class="stat-card">
            <h2 style="font-size: 28px;">{{ $jml_guru }}</h2>
            <p>TOTAL GURU</p>
        </div>
        <div class="stat-card">
            <h2 style="font-size: 28px;">{{ $jml_pengawas }}</h2>
            <p>TOTAL PENGAWAS</p>
        </div>
        <div class="stat-card">
            <h2 style="font-size: 28px;">{{ $jml_schedule }}</h2> <p>TOTAL JADWAL UJIAN</p>
        </div>
    </div>

    <div class="content-box" style="margin-bottom: 25px;">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px;">Statistik Sistem</h3>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <div style="padding: 20px; border: 1px solid var(--border-color); border-radius: 12px; background: #fafafa;">
                <p style="color: var(--text-gray); font-size: 13px; margin: 0;">Total Mata Pelajaran</p>
                <h2 style="margin: 5px 0 0; font-size: 32px;">{{ $jml_mata_pelajaran }}</h2>
            </div>
            <div style="padding: 20px; border: 1px solid var(--border-color); border-radius: 12px; background: #fafafa;">
                <p style="color: var(--text-gray); font-size: 13px; margin: 0;">Total Kelas</p>
                <h2 style="margin: 5px 0 0; font-size: 32px;">{{ $jml_kelas }}</h2>
            </div>
            <div style="padding: 20px; border: 1px solid var(--border-color); border-radius: 12px; background: #fafafa;">
                <p style="color: var(--text-gray); font-size: 13px; margin: 0;">Ujian Hari Ini</p>
                <h2 style="margin: 5px 0 0; font-size: 32px; color: var(--red-sebstar);">{{ $jml_ujian_hari_ini }}</h2>
            </div>
        </div>
    </div>

    <div class="content-box">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px;">Grafik Statistik</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
            
            <div style="text-align: center;">
                <div style="height: 200px; background: #f8f9fa; border-radius: 12px; margin-bottom: 15px; display: flex; align-items: center; justify-content: center;">
                    <canvas id="chartSiswa"></canvas>
                </div>
                <button class="btn-add" style="margin: 0 auto; font-size: 13px;">+ Buat Soal Baru</button>
            </div>

            <div style="text-align: center;">
                <div style="height: 200px; background: #f8f9fa; border-radius: 12px; margin-bottom: 15px; display: flex; align-items: center; justify-content: center;">
                    <canvas id="chartKeaktifan"></canvas>
                </div>
                <button class="btn-add" style="margin: 0 auto; font-size: 13px;">+ Buat Ujian Baru</button>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart 1
            new Chart(document.getElementById('chartSiswa'), {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar'],
                    datasets: [{ label: 'Soal', data: [{{ $jml_schedule }}], backgroundColor: '#cd0000', borderRadius: 5 }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // Chart 2
            new Chart(document.getElementById('chartKeaktifan'), {
                type: 'line',
                data: {
                    labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum'],
                    datasets: [{ label: 'Ujian', data: [2, 5, 3, 8, 4], borderColor: '#cd0000', tension: 0.4 }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        });
    </script>
@endsection