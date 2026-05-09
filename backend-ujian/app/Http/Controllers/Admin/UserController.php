<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subject;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Menampilkan daftar pengguna dengan relasi lengkap
     */
    public function index(Request $request)
    {
        $query = User::with(['subject', 'classroom']);

        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->get();
        $subjects = Subject::orderBy('nama_mapel', 'asc')->get();
        $classes = Classroom::orderBy('nama_kelas', 'asc')->get();

        return view('admin.users.index', compact('users', 'subjects', 'classes'));
    }

    /**
     * Menyimpan pengguna baru (Create)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:5',
            'role'         => 'required|in:admin,guru,pengawas,siswa', // Tambah pengawas
            'nis'          => 'required_if:role,siswa|nullable|unique:users,nis',
            'nip'          => 'required_if:role,guru|nullable|unique:users,nip',
            'classroom_id' => 'required_if:role,siswa|nullable|exists:classrooms,id',
            'subject_id'   => 'required_if:role,guru|nullable|exists:subjects,id',
        ], [
            'email.unique' => 'Email ini sudah terdaftar!',
            'nis.unique'   => 'NIS ini sudah digunakan oleh siswa lain!',
            'nip.unique'   => 'NIP ini sudah digunakan oleh guru lain!',
        ]);

        try {
            $data = [
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => $request->role,
            ];

            // Logika Kolom Spesifik: Selain role terkait, set NULL
            $data['nis']          = ($request->role === 'siswa') ? $request->nis : null;
            $data['classroom_id'] = ($request->role === 'siswa') ? $request->classroom_id : null;
            $data['nip']          = ($request->role === 'guru') ? $request->nip : null;
            $data['subject_id']   = ($request->role === 'guru') ? $request->subject_id : null;

            User::create($data);

            return redirect()->route('admin.users.index')->with('success', 'User ' . $request->name . ' Berhasil Ditambahkan!');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui data pengguna (Update)
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $id,
            'role'         => 'required|in:admin,guru,pengawas,siswa',
            'nis'          => 'required_if:role,siswa|nullable|unique:users,nis,' . $id,
            'nip'          => 'required_if:role,guru|nullable|unique:users,nip,' . $id,
            'classroom_id' => 'required_if:role,siswa|nullable|exists:classrooms,id',
            'subject_id'   => 'required_if:role,guru|nullable|exists:subjects,id',
        ]);

        try {
            $data = [
                'name'  => $request->name,
                'email' => $request->email,
                'role'  => $request->role,
            ];

            // Jika role diubah ke pengawas/admin, data nis/nip sebelumnya otomatis terhapus (NULL)
            $data['nis']          = ($request->role === 'siswa') ? $request->nis : null;
            $data['classroom_id'] = ($request->role === 'siswa') ? $request->classroom_id : null;
            $data['nip']          = ($request->role === 'guru') ? $request->nip : null;
            $data['subject_id']   = ($request->role === 'guru') ? $request->subject_id : null;

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return redirect()->route('admin.users.index')->with('success', 'Data ' . $user->name . ' berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal Update: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus pengguna (Destroy)
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if(auth()->id() == $user->id) {
            return redirect()->back()->with('error', 'Anda tidak diizinkan menghapus akun sendiri!');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus!');
    }
}