<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

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
        // VALIDASI KETAT: Kode & nama mapel wajib diisi, minimal 3 karakter, dan harus unik
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
                'kode_mapel' => strtoupper($request->kode_mapel), // Memaksa tersimpan huruf kapital (MAT-01)
                'nama_mapel' => $request->nama_mapel,
            ]);

            return redirect()->back()->with('success', 'Mata Pelajaran "' . $request->nama_mapel . '" berhasil ditambah!');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memproses data: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui data mata pelajaran (Update)
     */
    public function update(Request $request, Subject $subject)
    {
        // VALIDASI UPDATE: Mengunci keunikan dengan mengecualikan ID mapel terpilih saat ini
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
            // Menyisipkan sesi penanda 'edit' agar modal edit tahu dia wajib mengunci diri tetap terbuka
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
            // CEK CONSTRAINT: Pastikan mapel belum terikat pada data jadwal pelaksanaan ujian manapun
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