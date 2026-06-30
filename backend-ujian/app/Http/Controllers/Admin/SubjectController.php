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

            // Deteksi jika file adalah CSV murni, paksa pembacaan sebagai CSV murni
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
     * 🚀 FITUR TEMPLATE RAPI: Mengunduh Template CSV Contoh untuk Admin
     * Sudah dilengkapi instruksi pembatas kolom otomatis untuk Excel (Anti-Dempet)
     */
    public function downloadTemplate()
    {
        $namaFile = "template_import_mata_pelajaran_sebstar.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$namaFile}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0'
        ];

        return response()->streamDownload(function() {
            $file = fopen('php://output', 'w');
            
            // 🛠️ TRICK 1: Mengirimkan BOM UTF-8 agar Excel membaca karakter font Indonesia dengan pas
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // 🛠️ TRICK 2: Memaksa Microsoft Excel/WPS langsung memecah data ke kolom tersendiri (Anti-Dempet)
            fwrite($file, "sep=,\n");

            // Header yang manusiawi & jelas bagi pengguna proktor/admin sekolah
            fputcsv($file, ['Kode Mapel', 'Nama Mapel'], ',');
            
            // Contoh baris template data pengisian riil sekolah
            fputcsv($file, ['MAT-01', 'Matematika Wajib'], ',');
            fputcsv($file, ['ING-02', 'Bahasa Inggris Tingkat Lanjut'], ',');
            fputcsv($file, ['RPL-03', 'Pemrograman Berorientasi Objek'], ',');
            fputcsv($file, ['IND-04', 'Bahasa Indonesia'], ',');
            
            fclose($file);
        }, $namaFile, $headers);
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