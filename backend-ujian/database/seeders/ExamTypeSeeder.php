<?php

namespace Database\Seeders;

use App\Models\ExamType;
use Illuminate\Database\Seeder;

class ExamTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['name' => 'Ulangan Harian'],
            ['name' => 'Ujian Tengah Semester (UTS)'],
            ['name' => 'Ujian Akhir Semester (UAS)'],
            ['name' => 'Ujian Sekolah'],
            ['name' => 'Simulasi / Tryout']
        ];

        ExamType::insert($types);
    }
}