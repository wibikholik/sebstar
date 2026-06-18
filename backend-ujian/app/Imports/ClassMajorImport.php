<?php

namespace App\Imports;

use App\Models\Classroom;
use App\Models\Major;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ClassMajorImport implements ToModel, WithHeadingRow, WithValidation, WithCustomCsvSettings, SkipsEmptyRows
{
    /**
     * Memetakan setiap baris berkas menjadi data Jurusan dan Kelas
     */
    public function model(array $row)
    {
        // Bersihkan text dan pastikan singkatan menggunakan huruf kapital
        $singkatanJurusan = strtoupper(trim($row['singkatan_jurusan']));
        $namaKelas        = trim($row['nama_kelas']);
        $namaJurusan      = !empty($row['nama_jurusan']) ? trim($row['nama_jurusan']) : $singkatanJurusan;

        // 🚀 LOGIKA KUNCI: Cari jurusan, jika tidak ada, otomatis buat baru saat itu juga
        $jurusan = Major::firstOrCreate(
            ['singkatan' => $singkatanJurusan], // Kolom unik acuan pencarian (sesuaikan nama kolom di DB-mu)
            ['nama_jurusan' => $namaJurusan]    // Data yang diisi jika harus membuat baru
        );

        // Buat data rombel kelas baru dan kaitkan langsung dengan ID jurusan hasil pencarian/pembuatan di atas
        return new Classroom([
            'nama_kelas' => $namaKelas,
            'major_id'   => $jurusan->id, // Foreign key penghubung tabel
        ]);
    }

    /**
     * 🛡️ FILTER VALIDASI: Mencegah nama kelas yang sama masuk ganda ke database
     */
    public function rules(): array
    {
        return [
            'nama_kelas'        => 'required|string|max:255|unique:classrooms,nama_kelas',
            'singkatan_jurusan' => 'required|string|max:50',
        ];
    }

    /**
     * Custom pesan kesalahan dalam Bahasa Indonesia yang ramah bagi proktor sekolah
     */
    public function customValidationMessages()
    {
        return [
            'nama_kelas.required'        => 'Kolom nama kelas tidak boleh kosong.',
            'nama_kelas.unique'          => 'Nama kelas sudah terdaftar di dalam sistem (Duplikat).',
            'singkatan_jurusan.required' => 'Kolom singkatan jurusan wajib diisi sebagai pengait relasi kelas.',
        ];
    }

    /**
     * Pengaturan pembacaan teks jika admin mengunggah format CSV murni
     */
    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'UTF-8',
            'delimiter'      => ',',
        ];
    }
}