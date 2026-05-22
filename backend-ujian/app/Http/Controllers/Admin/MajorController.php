<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Major;
use Illuminate\Http\Request;

class MajorController extends Controller
{
    /**
     * Menampilkan daftar kompetensi keahlian / jurusan
     */
    public function index()
    {
        $majors = Major::latest()->get();
        return view('admin.majors.index', compact('majors'));
    }

    /**
     * Menyimpan data jurusan baru (Create)
     */
    public function store(Request $request)
    {
        // VALIDASI KETAT: Nama & singkatan wajib diisi, unik, dan minimal 2-3 karakter
        $request->validate([
            'nama_jurusan' => 'required|string|min:3|max:255|unique:majors,nama_jurusan',
            'singkatan'    => 'required|string|min:2|max:10|unique:majors,singkatan',
        ], [
            'nama_jurusan.required' => 'Nama lengkap jurusan wajib diisi!',
            'nama_jurusan.min'      => 'Nama jurusan terlalu pendek, minimal berisi 3 karakter!',
            'nama_jurusan.unique'   => 'Nama jurusan ini sudah terdaftar di sistem!',
            'singkatan.required'    => 'Singkatan jurusan wajib diisi!',
            'singkatan.min'         => 'Singkatan terlalu pendek, minimal berisi 2 karakter (Misal: RPL)!',
            'singkatan.unique'      => 'Singkatan jurusan ini sudah digunakan oleh jurusan lain!',
        ]);

        try {
            Major::create([
                'nama_jurusan' => $request->nama_jurusan,
                'singkatan'    => strtoupper($request->singkatan), // Otomatis memaksa huruf kapital (RPL, TKJ, TRPL)
            ]);

            return redirect()->back()->with('success', 'Jurusan "' . $request->nama_jurusan . '" berhasil ditambah!');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memproses data: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui data jurusan (Update)
     */
    public function update(Request $request, Major $major)
    {
        $request->validate([
            'nama_jurusan' => 'required|string|min:3|max:255|unique:majors,nama_jurusan,' . $major->id,
            'singkatan'    => 'required|string|min:2|max:10|unique:majors,singkatan,' . $major->id,
        ], [
            'nama_jurusan.required' => 'Nama lengkap jurusan wajib diisi!',
            'nama_jurusan.unique'   => 'Nama jurusan ini sudah terdaftar pada data keahlian lain!',
            'singkatan.required'    => 'Singkatan jurusan wajib diisi!',
            'singkatan.unique'      => 'Singkatan ini sudah digunakan oleh jurusan lain!',
        ]);

        try {
            $major->update([
                'nama_jurusan' => $request->nama_jurusan,
                'singkatan'    => strtoupper($request->singkatan), // Menjaga konsistensi kapitalisasi saat di-edit
            ]);

            return redirect()->back()->with('success', 'Jurusan berhasil diupdate!');

        } catch (\Exception $e) {
            // Menyisipkan sesi penanda 'edit' agar modal edit tahu dia wajib mengunci diri tetap terbuka
            return redirect()->back()
                ->withInput()
                ->with('error_form_type', 'edit')
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data jurusan (Destroy)
     */
    public function destroy(Major $major)
    {
        try {
            // PROTEKSI CONSTRAINT: Cek apakah jurusan ini sudah terikat di data Kelas (classrooms) siswa
            if ($major->classrooms()->count() > 0) {
                return redirect()->back()->with('error', 'Gagal menghapus! Jurusan ini tidak bisa dihapus karena memiliki relasi kelas aktif di dalam sistem.');
            }

            $major->delete();
            return redirect()->back()->with('success', 'Jurusan berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kendala saat menghapus data: ' . $e->getMessage());
        }
    }
}