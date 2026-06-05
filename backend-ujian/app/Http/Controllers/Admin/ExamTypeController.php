<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamType;
use Illuminate\Http\Request;

class ExamTypeController extends Controller
{
    /**
     * Menampilkan daftar tipe ujian pelaksanaan
     */
    public function index()
    {
        $examTypes = ExamType::latest()->get();
        return view('admin.exam_types.index', compact('examTypes'));
    }

    /**
     * Menyimpan tipe ujian baru (Create)
     */
    public function store(Request $request)
    {
        // VALIDASI KETAT: Wajib diisi, unik, min 3 karakter, maks 255, dan wajib mengandung huruf alfabet
        $request->validate([
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'unique:exam_types,name',
                'regex:/[a-zA-Z]/', // Memblokir input angka murni / simbol murni
            ],
        ], [
            'name.required' => 'Nama tipe ujian wajib diisi!',
            'name.min'      => 'Nama tipe ujian terlalu pendek, minimal berisi 3 karakter!',
            'name.max'      => 'Nama tipe ujian terlalu panjang, maksimal berisi 255 karakter!',
            'name.unique'   => 'Tipe ujian dengan nama ini sudah terdaftar di sistem!',
            'name.regex'    => 'Format nama tidak valid! Nama tipe ujian wajib mengandung huruf alfabet (tidak boleh angka atau simbol semua).',
        ]);

        try {
            // PERBAIKAN: Menggunakan ->boolean() agar menyimpan angka 1 / 0 secara mutlak ke MySQL
            ExamType::create([
                'name'                  => $request->name,
                'is_teacher_manageable' => $request->boolean('is_teacher_manageable')
            ]);

            return back()->with('success', 'Tipe Ujian "' . $request->name . '" berhasil ditambahkan!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui data tipe ujian (Update)
     */
    public function update(Request $request, $id)
    {
        $type = ExamType::findOrFail($id);
        
        $request->validate([
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'unique:exam_types,name,' . $id, // Bypass keunikan untuk ID dirinya sendiri saat update
                'regex:/[a-zA-Z]/',
            ],
        ], [
            'name.required' => 'Nama tipe ujian wajib diisi!',
            'name.min'      => 'Nama tipe ujian terlalu pendek, minimal berisi 3 karakter!',
            'name.max'      => 'Nama tipe ujian terlalu panjang, maksimal berisi 255 karakter!',
            'name.unique'   => 'Nama tipe ujian ini sudah digunakan pada data pelaksanaan lain!',
            'name.regex'    => 'Format nama tidak valid! Nama tipe ujian wajib mengandung huruf alfabet.',
        ]);

        try {
            $type->update([
                'name'                  => $request->name,
                'is_teacher_manageable' => $request->boolean('is_teacher_manageable')
            ]);

            return back()->with('success', 'Tipe Ujian berhasil diperbarui!');

        } catch (\Exception $e) {
            // Memberikan tanda penampung sesi khusus 'edit' agar modal edit otomatis tetap terbuka pasca-reload
            return back()
                ->withInput()
                ->with('error_form_type', 'edit')
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus tipe ujian (Destroy)
     */
    /**
     * Menghapus tipe ujian (Destroy)
     */
    public function destroy($id)
    {
        $type = ExamType::findOrFail($id);
        
        // Proteksi Constraint: Cek apakah tipe ujian sudah terikat di dalam baris tabel schedules
        if ($type->schedules()->count() > 0) {
            return back()->with('error', 'Akses ditolak! Tipe ujian ini tidak bisa dihapus karena sudah digunakan pada jadwal pelaksanaan ujian siswa.');
        }

        $type->delete();
        return back()->with('success', 'Tipe Ujian berhasil dihapus dari sistem!');
    }
}