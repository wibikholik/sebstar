<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subject;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;

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

        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:users,email,' . $id,
            'password'     => 'nullable|string|min:6',
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

    /**
     * 🚀 Memproses Import Excel/CSV secara aman tanpa kebocoran SQL Error
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ], [
            'file_excel.required' => 'Pilih file terlebih dahulu!',
            'file_excel.mimes'    => 'Format file harus berupa .xlsx, .xls, atau .csv!',
        ]);

        try {
            $file = $request->file('file_excel');

            // Jika ekstensi file murni .csv, paksa baca sebagai CSV murni
            if ($file->getClientOriginalExtension() === 'csv') {
                Excel::import(new UsersImport, $file, null, \Maatwebsite\Excel\Excel::CSV);
            } else {
                Excel::import(new UsersImport, $file);
            }
            
            return redirect()->back()->with('success', '✨ Data pengguna berhasil diimpor massal ke database!');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorPesan = '<strong>Gagal Import Massal! Periksa baris berikut:</strong><br>';
            
            foreach ($failures as $failure) {
                $errorPesan .= '• Baris ke-' . $failure->row() . ': ' . implode(', ', $failure->errors()) . '<br>';
            }
            
            return redirect()->back()->with('error', $errorPesan);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '⚠ Gagal memproses berkas. Pastikan struktur kolom header template sesuai!');
        }
    }

    /**
     * 🚀 FITUR TEMPLATE RAPI: Mengunduh Template CSV Contoh untuk Admin
     * Desain kolom bersih, kapitalisasi rapi, & instruksi pemisah otomatis (Anti-Dempet)
     */
    public function downloadTemplate()
    {
        $namaFile = "template_import_pengguna_sebstar.csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$namaFile}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Format nama kolom yang jelas bagi pengguna manusia (Proktor/Admin)
        $columns = ['Name', 'Email', 'Password', 'Role', 'Nomor Induk', 'Nama Kelas', 'Nama Mapel'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            
            // 🛠️ TRICK 1: Mengirimkan tanda pengenal UTF-8 BOM agar Excel membaca jenis font & simbol dengan pas
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // 🛠️ TRICK 2: Kunci anti-dempet! Memaksa Excel memecah baris data langsung ke kolom masing-masing
            fwrite($file, "sep=,\n");
            
            // Tulis Header Resmi
            fputcsv($file, $columns, ',');
            
            // Contoh baris template pengisian data master riil yang mudah dipahami proktor
            fputcsv($file, ['Ahmad Siswa', 'ahmad@sebstar.com', 'rahasia123', 'siswa', '10224001', 'XII RPL 1', ''], ',');
            fputcsv($file, ['Budi Guru', 'budi@sebstar.com', 'passwordguru', 'guru', '1988010202', '', 'Pemrograman Berorientasi Objek'], ',');
            fputcsv($file, ['Siti Pengawas', 'siti@sebstar.com', 'passwordpw', 'pengawas', '1992050301', '', ''], ',');
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}