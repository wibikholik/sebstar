<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Classroom;
use App\Models\Subject;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, WithCustomCsvSettings, SkipsEmptyRows
{
    /**
     * Memetakan baris data menjadi Model User
     */
    public function model(array $row)
    {
        $nis = null;
        $nip = null;
        $classroomId = null;
        $subjectId = null;

        $role = strtolower(trim($row['role']));

        // 1. JIKA DIA SISWA, CARI ID KELASNYA OTOMATIS BERDASARKAN TEKS EXCEL
        if ($role === 'siswa') {
            $nis = trim($row['nomor_induk']);
            
            if (!empty($row['nama_kelas'])) {
                $kelas = Classroom::where('nama_kelas', trim($row['nama_kelas']))->first();
                $classroomId = $kelas ? $kelas->id : null; 
            }
        } 
        // 2. JIKA DIA GURU/PENGAWAS/ADMIN, SET NIP
        elseif (in_array($role, ['guru', 'pengawas', 'admin'])) {
            $nip = trim($row['nomor_induk']);
            
            if ($role === 'guru' && !empty($row['nama_mapel'])) {
                $mapel = Subject::where('nama_mapel', trim($row['nama_mapel']))->first();
                $subjectId = $mapel ? $mapel->id : null;
            }
        }

        return new User([
            'name'         => trim($row['name']),
            'email'        => trim($row['email']),
            'password'     => Hash::make($row['password'] ?? 'sebstar123'),
            'role'         => $role,
            'nis'          => $nis,
            'nip'          => $nip,
            'classroom_id' => $classroomId,
            'subject_id'   => $subjectId,
            'is_logged_in' => 0,
        ]);
    }

    /**
     * 🛡️ FILTER VALIDASI: Mencegah data rusak/duplikat masuk ke database
     */
    public function rules(): array
    {
        return [
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:users,email',
            'role'         => 'required|in:admin,guru,pengawas,siswa',
            // Gunakan 'nullable' atau pastikan 'string/numeric' agar fleksibel dibaca sistem
            'nomor_induk'  => 'required|string|max:50', 
            'nama_kelas'   => 'required_if:role,siswa',
            'nama_mapel'   => 'required_if:role,guru',
        ];
    }

    /**
     * ✍️ UBAH BAHASA ERROR DI SINI (Bahasa Indonesia murni untuk proktor)
     */
    public function customValidationMessages()
    {
        return [
            'name.required'          => 'Nama lengkap tidak boleh kosong.',
            'email.required'         => 'Alamat email wajib diisi.',
            'email.email'            => 'Format alamat email tidak valid.',
            'email.unique'           => 'Email sudah terdaftar di sistem (Duplikat).',
            'role.required'          => 'Hak akses (role) tidak boleh kosong.',
            'role.in'                => 'Pilihan role tidak valid. (Gunakan: siswa, guru, pengawas, atau admin).',
            
            // Perbaikan Pesan "Must be a string" yang kamu minta:
            'nomor_induk.required'   => 'Nomor induk (NIS/NIP) wajib diisi.',
            'nomor_induk.string'     => 'Format nomor induk (NIS/NIP) harus berupa teks literal (pastikan tidak menggunakan rumus/format khusus).',
            'nomor_induk.max'        => 'Nomor induk terlalu panjang, maksimal 50 karakter.',
            
            'nama_kelas.required_if' => 'Nama rombel kelas wajib ditentukan untuk pengguna bermutu Siswa.',
            'nama_mapel.required_if' => 'Nama mata pelajaran wajib ditentukan untuk pengguna bermutu Guru.',
        ];
    }

    /**
     * Pengaturan pembacaan jika admin mengunggah berkas .csv murni
     */
    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'UTF-8',
            'delimiter'      => ',',
        ];
    }
}