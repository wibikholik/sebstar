<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// Pastikan nama modelnya adalah 'User' (standar Laravel) 
// bukan 'Users' kecuali kamu mengubah nama class di modelnya
use App\Models\User; 
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat Akun Admin
        User::create([
            'name' => 'Admin SMKN 1 Binong',
            'email' => 'admin@mail.com', // Sesuaikan dengan kolom 'email' di gambar
            'password' => Hash::make('password123'),
            'role' => 'admin', // Sesuai dengan kolom 'role' ENUM di gambar
        ]);

        // Buat Akun Guru
        User::create([
            'name' => 'Guru Mapel',
            'email' => 'guru@mail.com',
            'password' => Hash::make('password123'),
            'role' => 'guru',
        ]);

        // Buat Akun Pengawas
        User::create([
            'name' => 'Pengawas Ujian',
            'email' => 'pengawas@mail.com',
            'password' => Hash::make('password123'),
            'role' => 'pengawas',
        ]);

        // Buat Akun Siswa (untuk testing mobile nanti)
        User::create([
            'name' => 'Siswa Percobaan',
            'email' => 'siswa@mail.com',
            'password' => Hash::make('password123'),
            'role' => 'siswa',
        ]);
    }
}