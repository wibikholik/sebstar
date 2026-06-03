<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Classroom;
use App\Models\Subject;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Abaikan baris kosong di excel jika nama atau role kosong
        if (empty($row['name']) || empty($row['role'])) {
            return null;
        }

        $nis = null;
        $nip = null;
        $classroomId = null;
        $subjectId = null;

        $role = strtolower(trim($row['role']));

        // 1. JIKA DIA SISWA, CARI ID KELASNYA OTOMATIS BERDASARKAN TEKS EXCEL
        if ($role === 'siswa') {
            $nis = $row['nomor_induk'];
            
            if (!empty($row['nama_kelas'])) {
                // Mencari data kelas di database yang nama_kelas nya cocok dengan excel
                $kelas = Classroom::where('nama_kelas', trim($row['nama_kelas']))->first();
                $classroomId = $kelas ? $kelas->id : null; 
            }
        } 
        // 2. JIKA DIA GURU, CARI ID MAPELNYA OTOMATIS BERDASARKAN TEKS EXCEL
        elseif (in_array($role, ['guru', 'pengawas', 'admin'])) {
            $nip = $row['nomor_induk'] ?? null;
            
            if ($role === 'guru' && !empty($row['nama_mapel'])) {
                // Mencari data mapel di database yang nama_mapel nya cocok dengan excel
                $mapel = Subject::where('nama_mapel', trim($row['nama_mapel']))->first();
                $subjectId = $mapel ? $mapel->id : null;
            }
        }

        return new User([
            'name'         => $row['name'],
            'email'        => $row['email'],
            'password'     => Hash::make($row['password']),
            'role'         => $role,
            'nis'          => $nis,
            'nip'          => $nip,
            'classroom_id' => $classroomId, // Menyimpan ID hasil pencarian otomatis sistem
            'subject_id'   => $subjectId,   // Menyimpan ID hasil pencarian otomatis sistem
            'is_logged_in' => 0,
        ]);
    }
}