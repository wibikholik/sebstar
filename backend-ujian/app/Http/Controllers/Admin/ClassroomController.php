<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Major;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ClassMajorImport; 

class ClassroomController extends Controller
{
    /**
     * Menampilkan daftar kelas beserta data jurusan
     */
    public function index()
    {
        // Eager loading 'major' agar tidak memicu kendala N+1 query
        $classes = Classroom::with('major')->orderBy('nama_kelas', 'asc')->get();
        $majors = Major::orderBy('nama_jurusan', 'asc')->get();
        
        return view('admin.classrooms.index', compact('classes', 'majors'));
    }

    /**
     * Menyimpan data kelas baru (Create)
     */
    public function store(Request $request)
    {
        // VALIDASI KETAT: Memastikan nama kelas tidak duplikat dengan pesan bahasa Indonesia
        $request->validate([
            'nama_kelas' => 'required|string|min:2|max:255|unique:classrooms,nama_kelas',
            'major_id'   => 'required|exists:majors,id',
        ], [
            'nama_kelas.required' => 'Nama tingkatan kelas wajib diisi!',
            'nama_kelas.min'      => 'Nama kelas terlalu pendek, minimal berisi 2 karakter (Contoh: XI)!',
            'nama_kelas.unique'   => 'Nama kelas ini sudah terdaftar di dalam sistem!',
            'major_id.required'   => 'Silakan pilih kompetensi keahlian / jurusan untuk kelas ini!',
            'major_id.exists'     => 'Jurusan yang Anda pilih tidak valid atau tidak terdaftar!',
        ]);

        try {
            Classroom::create($request->only(['nama_kelas', 'major_id']));
            
            return redirect()->back()->with('success', 'Kelas "' . $request->nama_kelas . '" berhasil ditambahkan!');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memproses data kelas: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui data kelas (Update)
     */
    public function update(Request $request, Classroom $classroom)
    {
        $request->validate([
            'nama_kelas' => 'required|string|min:2|max:255|unique:classrooms,nama_kelas,' . $classroom->id,
            'major_id'   => 'required|exists:majors,id',
        ], [
            'nama_kelas.required' => 'Nama tingkatan kelas wajib diisi!',
            'nama_kelas.unique'   => 'Nama kelas ini sudah digunakan pada rombel kelas lainnya!',
            'major_id.required'   => 'Silakan tentukan jurusan pengait kelas!',
        ]);

        try {
            $classroom->update($request->only(['nama_kelas', 'major_id']));
            
            return redirect()->back()->with('success', 'Data kelas ' . $classroom->nama_kelas . ' berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error_form_type', 'edit')
                ->with('error', 'Gagal memperbarui data kelas: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data kelas dengan pengaman integritas data siswa
     */
    public function destroy(Classroom $classroom)
    {
        try {
            if ($classroom->users()->count() > 0) {
                return redirect()->back()->with('error', 'Gagal menghapus! Rombel kelas ini tidak bisa dihapus karena masih memiliki data siswa aktif di dalamnya.');
            }

            if ($classroom->schedules()->count() > 0) {
                return redirect()->back()->with('error', 'Gagal menghapus! Kelas ini sudah terikat dengan jadwal pelaksanaan ujian aktif.');
            }

            $classroom->delete();
            return redirect()->back()->with('success', 'Kelas berhasil dihapus dari sistem!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kendala sistem saat menghapus kelas: ' . $e->getMessage());
        }
    }

    /**
     * 🚀 Memproses Import Gabungan Kelas & Jurusan Massal via Excel/CSV
     */
    public function importGabungan(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ], [
            'file_excel.required' => 'Silakan pilih berkas terlebih dahulu!',
            'file_excel.mimes'    => 'Format berkas harus berupa .xlsx, .xls, atau .csv!',
        ]);

        try {
            $file = $request->file('file_excel');

            // Cek jika ekstensi file adalah csv, paksa pembacaan sebagai CSV murni
            if ($file->getClientOriginalExtension() === 'csv') {
                Excel::import(new ClassMajorImport, $file, null, \Maatwebsite\Excel\Excel::CSV);
            } else {
                Excel::import(new ClassMajorImport, $file);
            }

            return redirect()->back()->with('success', '✨ Data jurusan baru dan rombel kelas berhasil disinkronisasi massal!');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorPesan = '<strong>Gagal Import Kelas! Periksa baris data berikut:</strong><br>';
            
            foreach ($failures as $failure) {
                $errorPesan .= '• Baris ke-' . $failure->row() . ': ' . implode(', ', $failure->errors()) . '<br>';
            }
            
            return redirect()->back()->with('error', $errorPesan);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '⚠ Gagal memproses data. Pastikan format penulisan header kolom sesuai dengan template!');
        }
    }

    /**
     * 🚀 FITUR TEMPLATE RAPI: Mengunduh Template CSV Gabungan Kelas & Jurusan
     * Kolom dibuat bersih dan jelas untuk mempermudah Proktor / Admin Sekolah.
     */
    public function downloadTemplateGabungan()
    {
        $namaFile = "template_import_kelas_jurusan_sebstar.csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$namaFile}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Penggunaan Nama Kolom yang Jelas (Huruf Kapital Awalan, Tanpa Underscore Lembar Kerja)
        $columns = ['Nama Kelas', 'Singkatan Jurusan', 'Nama Jurusan'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            
            // 🛠️ TRICK 1: Kirimkan UTF-8 BOM agar Excel mengenali simbol karakter Indonesia & font dengan pas
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); 
            
            // 🛠️ TRICK 2: Memaksa Microsoft Excel langsung memecah data ke kolom tersendiri (Anti-Dempet)
            fwrite($file, "sep=,\n");
            
            // Tulis baris judul kolom (Header Resmi)
            fputcsv($file, $columns, ',');
            
            // Tulis contoh data baris demi baris yang rapi dan mudah dicontoh admin sekolah
            fputcsv($file, ['XII RPL 1', 'RPL', 'Rekayasa Perangkat Lunak'], ',');
            fputcsv($file, ['XII AKL 2', 'AKL', 'Akuntansi dan Keuangan Lembaga'], ',');
            fputcsv($file, ['X TJKT 3', 'TJKT', 'Teknik Jaringan Komputer dan Telekomunikasi'], ',');
            fputcsv($file, ['XI DKV 1', 'DKV', 'Desain Komunikasi Visual'], ',');
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}