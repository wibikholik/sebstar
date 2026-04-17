@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1>Dashboard Utama</h1>
    <p>Ringkasan data SMKN 1 Binong hari ini.</p>

    <div class="cards">
        <div class="card">
            <h3>Total Guru</h3>
            <p>{{ $jml_guru }}</p>
        </div>
        <div class="card">
            <h3>Total Siswa</h3>
            <p>{{ $jml_siswa }}</p>
        </div>
        <div class="card">
            <h3>Total Pengawas</h3>
            <p>{{ $jml_pengawas }}</p>
        </div>
    </div>
@endsection