<?php

namespace App\Imports;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class SubjectImport implements ToModel, WithHeadingRow, WithValidation, WithCustomCsvSettings
{
    /**
     * Memetakan baris excel/csv menjadi model Subject
     */
    public function model(array $row)
    {
        // Antisipasi jika baris kosong atau salah pembacaan key akibat case-sensitive
        $kodeKey = isset($row['kode_mapel']) ? 'kode_mapel' : (isset($row['KODE_MAPEL']) ? 'KODE_MAPEL' : null);
        $namaKey = isset($row['nama_mapel']) ? 'nama_mapel' : (isset($row['NAMA_MAPEL']) ? 'NAMA_MAPEL' : null);

        if (!$kodeKey || !$namaKey) {
            return null; 
        }

        return new Subject([
            'kode_mapel' => strtoupper(trim($row[$kodeKey])), // Paksa huruf kapital (MAT-01)
            'nama_mapel' => trim($row[$namaKey]),
        ]);
    }

    /**
     * Validasi data sebelum dimasukkan ke database
     */
    public function rules(): array
    {
        return [
            '*.kode_mapel' => 'required|string|min:2|max:50|unique:subjects,kode_mapel',
            '*.nama_mapel' => 'required|string|min:3|max:255|unique:subjects,nama_mapel',
        ];
    }

    /**
     * Custom pesan error validasi
     */
    public function customValidationMessages()
    {
        return [
            '*.kode_mapel.required' => 'Ada kode mapel yang kosong di berkas!',
            '*.kode_mapel.unique'   => 'Kode mapel di berkas ada yang sudah terdaftar di sistem!',
            '*.nama_mapel.required' => 'Ada nama mapel yang kosong di berkas!',
            '*.nama_mapel.unique'   => 'Nama mapel di berkas ada yang sudah digunakan!',
        ];
    }

    /**
     * 🚀 FITUR PENYELAMAT: Menentukan pengaturan pembacaan CSV secara manual
     * Ini mengunci separator menggunakan koma (,) dan encoding UTF-8 agar file 
     * template bawaan aman dari error zip member.
     */
    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'UTF-8',
            'delimiter'      => ',',
        ];
    }
}