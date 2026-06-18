<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Imports\SubjectImport;
use Maatwebsite\Excel\Facades\Excel;

class SubjectController extends Controller
{
    /**
     * Menampilkan daftar mata pelajaran
     */
    public function index()
    {
        $subjects = Subject::latest()->get();
        return view('admin.subjects.index', compact('subjects'));
    }

    /**
     * Menyimpan mata pelajaran baru (Create)
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_mapel' => 'required|string|min:3|max:50|unique:subjects,kode_mapel',
            'nama_mapel' => 'required|string|min:3|max:255|unique:subjects,nama_mapel',
        ], [
            'kode_mapel.required' => 'Kode mata pelajaran wajib diisi!',
            'kode_mapel.min'      => 'Kode mata pelajaran terlalu pendek, minimal berisi 3 karakter!',
            'kode_mapel.max'      => 'Kode mata pelajaran terlalu panjang, maksimal berisi 50 karakter!',
            'kode_mapel.unique'   => 'Kode mata pelajaran ini sudah terdaftar di sistem!',
            'nama_mapel.required' => 'Nama mata pelajaran wajib diisi!',
            'nama_mapel.min'      => 'Nama mata pelajaran terlalu pendek, minimal berisi 3 karakter!',
            'nama_mapel.max'      => 'Nama mata pelajaran terlalu panjang, maksimal berisi 255 karakter!',
            'nama_mapel.unique'   => 'Nama mata pelajaran ini sudah digunakan!',
        ]);

        try {
            Subject::create([
                'kode_mapel' => strtoupper($request->kode_mapel), 
                'nama_mapel' => $request->nama_mapel,
            ]);

            return redirect()->back()->with('success', 'Mata Pelajaran "' . $request->nama_mapel . '" berhasil ditambah!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memproses data: ' . $e->getMessage());
        }
    }

    /**
     * Memproses Import Excel/CSV Massal
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ], [
            'file_excel.required' => 'Pilih file terlebih dahulu!',
            'file_excel.mimes'    => 'Format file harus berupa .xlsx, .xls, atau .csv!',
            'file_excel.max'      => 'Ukuran file maksimal adalah 5MB!',
        ]);

        try {
            $file = $request->file('file_excel');

            // 🛠️ PERBAIKAN 1: Deteksi jika file adalah CSV murni, paksa pembacaan sebagai CSV murni
            if ($file->getClientOriginalExtension() === 'csv') {
                Excel::import(new SubjectImport, $file, null, \Maatwebsite\Excel\Excel::CSV);
            } else {
                Excel::import(new SubjectImport, $file);
            }

            return redirect()->back()->with('success', 'Semua data mata pelajaran berhasil diimport massal!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorPesan = 'Gagal import! ';
            foreach ($failures as $failure) {
                $errorPesan .= 'Baris ke-' . $failure->row() . ': ' . implode(', ', $failure->errors()) . ' | ';
            }
            return redirect()->back()->with('error', $errorPesan);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat membaca file: ' . $e->getMessage());
        }
    }

    /**
     * Mengunduh Template CSV Contoh untuk Admin
     */
    public function downloadTemplate()
    {
        // 🛠️ PERBAIKAN 2: Ubah Header dan Extention menjadi .csv agar sesuai dengan isi fputcsv
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_mata_pelajaran.csv"',
        ];

        return response()->streamDownload(function() {
            $file = fopen('php://output', 'w');
            
            // Mengirimkan BOM UTF-8 agar Excel tidak berantakan saat membuka tanda baca/karakter unik
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Baris 1: Header Excel (Sesuai dengan key $row di SubjectImport)
            fputcsv($file, ['kode_mapel', 'nama_mapel']);
            
            // Baris 2 & 3: Contoh data pengisian
            fputcsv($file, ['MAT-01', 'Matematika Wajib']);
            fputcsv($file, ['ING-02', 'Bahasa Inggris Tingkat Lanjut']);
            
            fclose($file);
        }, 'template_mata_pelajaran.csv', $headers);
    }

    /**
     * Memperbarui data mata pelajaran (Update)
     */
    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'kode_mapel' => 'required|string|min:3|max:50|unique:subjects,kode_mapel,' . $subject->id,
            'nama_mapel' => 'required|string|min:3|max:255|unique:subjects,nama_mapel,' . $subject->id,
        ], [
            'kode_mapel.required' => 'Kode mata pelajaran wajib diisi!',
            'kode_mapel.min'      => 'Kode mata pelajaran terlalu pendek, minimal berisi 3 karakter!',
            'kode_mapel.unique'   => 'Kode mata pelajaran ini sudah digunakan pada data mapel lain!',
            'nama_mapel.required' => 'Nama mata pelajaran wajib diisi!',
            'nama_mapel.min'      => 'Nama mata pelajaran terlalu pendek, minimal berisi 3 karakter!',
            'nama_mapel.unique'   => 'Nama mata pelajaran ini sudah digunakan pada data mapel lain!',
        ]);

        try {
            $subject->update([
                'kode_mapel' => strtoupper($request->kode_mapel),
                'nama_mapel' => $request->nama_mapel,
            ]);

            return redirect()->back()->with('success', 'Mata Pelajaran berhasil diupdate!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error_form_type', 'edit')
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data mata pelajaran (Destroy)
     */
    public function destroy(Subject $subject)
    {
        try {
            if ($subject->schedules()->count() > 0) {
                return redirect()->back()->with('error', 'Gagal menghapus! Mata pelajaran ini tidak bisa dihapus karena sudah digunakan pada jadwal pelaksanaan ujian.');
            }

            $subject->delete();
            return redirect()->back()->with('success', 'Mata Pelajaran berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kendala saat menghapus data: ' . $e->getMessage());
        }
    }
}