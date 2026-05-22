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

        // Filter dinamis dari klik Dashboard Admin (?role=guru)
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
            'email'        => 'required|email|max:255|unique:users,email',
            'password'     => 'required|min:6',
            'role'         => 'required|in:admin,guru,pengawas,siswa',
            'nis'          => 'required_if:role,siswa|nullable|string|max:50|unique:users,nis',
            'nip'          => 'required_if:role,guru|nullable|string|max:50|unique:users,nip',
            'classroom_id' => 'required_if:role,siswa|nullable|exists:classrooms,id',
            'subject_id'   => 'required_if:role,guru|nullable|exists:subjects,id',
        ], [
            'name.required'            => 'Nama lengkap wajib diisi!',
            'email.required'           => 'Alamat email wajib diisi!',
            'email.email'              => 'Format alamat email tidak valid!',
            'email.unique'             => 'Alamat email ini sudah terdaftar di sistem!',
            'password.required'        => 'Password wajib ditentukan!',
            'password.min'             => 'Password minimal harus berisi 6 karakter!',
            'role.required'            => 'Silakan pilih salah satu role pengguna!',
            'nis.required_if'          => 'Nomor Induk Siswa (NIS) wajib diisi jika memilih role Siswa!',
            'nis.unique'               => 'NIS ini sudah digunakan oleh siswa lain!',
            'nip.required_if'          => 'Nomor Induk Pegawai (NIP) wajib diisi jika memilih role Guru!',
            'nip.unique'               => 'NIP ini sudah digunakan oleh guru lain!',
            'classroom_id.required_if' => 'Kelas wajib ditentukan untuk pengguna role Siswa!',
            'subject_id.required_if'   => 'Mata pelajaran diampu wajib ditentukan untuk pengguna role Guru!',
        ]);

        try {
            $data = [
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => $request->role,
            ];

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

        // VALIDASI PASSING PASSWORD EDIT (Wajib min 6 jika diisi oleh admin)
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:users,email,' . $id,
            'password'     => 'nullable|string|min:6', // Terkunci aman minimal 6 karakter
            'role'         => 'required|in:admin,guru,pengawas,siswa',
            'nis'          => 'required_if:role,siswa|nullable|string|max:50|unique:users,nis,' . $id,
            'nip'          => 'required_if:role,guru|nullable|string|max:50|unique:users,nip,' . $id,
            'classroom_id' => 'required_if:role,siswa|nullable|exists:classrooms,id',
            'subject_id'   => 'required_if:role,guru|nullable|exists:subjects,id',
        ], [
            'name.required'            => 'Nama lengkap wajib diisi!',
            'email.required'           => 'Alamat email wajib diisi!',
            'email.email'              => 'Format alamat email tidak valid!',
            'email.unique'             => 'Alamat email ini sudah terdaftar oleh pengguna lain!',
            'password.min'             => 'Password baru minimal harus berisi 6 karakter!',
            'role.required'            => 'Silakan pilih salah satu role pengguna!',
            'nis.required_if'          => 'NIS wajib diisi jika role diubah menjadi Siswa!',
            'nis.unique'               => 'NIS ini sudah digunakan oleh siswa lain!',
            'nip.required_if'          => 'NIP wajib diisi jika role diubah menjadi Guru!',
            'nip.unique'               => 'NIP ini sudah digunakan oleh guru lain!',
            'classroom_id.required_if' => 'Kelas wajib ditentukan untuk pengguna role Siswa!',
            'subject_id.required_if'   => 'Mata pelajaran diampu wajib ditentukan untuk pengguna role Guru!',
        ]);

        try {
            $data = [
                'name'  => $request->name,
                'email' => $request->email,
                'role'  => $request->role,
            ];

            // Set null kolom relasi jika admin memutar balik role user saat di-edit
            $data['nis']          = ($request->role === 'siswa') ? $request->nis : null;
            $data['classroom_id'] = ($request->role === 'siswa') ? $request->classroom_id : null;
            $data['nip']          = ($request->role === 'guru') ? $request->nip : null;
            $data['subject_id']   = ($request->role === 'guru') ? $request->subject_id : null;

            // Enkripsi password baru hanya jika kolom password diisi admin
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return redirect()->route('admin.users.index')->with('success', 'Data ' . $user->name . ' berhasil diperbarui!');
        } catch (\Exception $e) {
            // Memberikan tanda penampung sesi khusus 'edit' agar modal otomatis terkunci terbuka pasca-reload
            return redirect()->back()
                ->withInput()
                ->with('error_form_type', 'edit')
                ->with('error', 'Gagal Update: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus pengguna (Destroy)
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if (auth()->id() == $user->id) {
            return redirect()->back()->with('error', 'Anda tidak diizinkan menghapus akun sendiri!');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus!');
    }
}