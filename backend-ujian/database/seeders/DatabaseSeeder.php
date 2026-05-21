<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Panggil UserSeeder

        // Panggil UserSeeder di sini agar dijalankan
        $this->call([
            UserSeeder::class,
        ]);
    }
}