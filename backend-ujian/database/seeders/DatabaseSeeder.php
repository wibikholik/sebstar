<?php

namespace Database\Seeders;

<<<<<<< HEAD
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
=======
>>>>>>> b09a755e7c1e7af2110f23b931a7ba643f021298
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
<<<<<<< HEAD
        // Panggil UserSeeder
=======
        // Panggil UserSeeder di sini agar dijalankan
>>>>>>> b09a755e7c1e7af2110f23b931a7ba643f021298
        $this->call([
            UserSeeder::class,
        ]);
    }
}