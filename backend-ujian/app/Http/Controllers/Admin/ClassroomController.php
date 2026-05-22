<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Major;
use Illuminate\Http\Request;

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
            // Menyisipkan sesi penanda 'edit' agar komponen blade tahu modal edit yang harus dibuka pasca-reload
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
            // PROTEKSI INTEGRITAS DATA: Cek apakah kelas ini masih dihuni oleh user (siswa)
            // Pastikan di model Classroom kamu sudah ada fungsi relasi bernama 'users' atau 'students'
            if ($classroom->users()->count() > 0) {
                return redirect()->back()->with('error', 'Gagal menghapus! Rombel kelas ini tidak bisa dihapus karena masih memiliki data siswa aktif di dalamnya.');
            }

            // Tambahan cek jika kelas sudah masuk dalam riwayat plotting jadwal ujian
            if ($classroom->schedules()->count() > 0) {
                return redirect()->back()->with('error', 'Gagal menghapus! Kelas ini sudah terikat dengan jadwal pelaksanaan ujian aktif.');
            }

            $classroom->delete();
            return redirect()->back()->with('success', 'Kelas berhasil dihapus dari sistem!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kendala sistem saat menghapus kelas: ' . $e->getMessage());
        }
    }
}