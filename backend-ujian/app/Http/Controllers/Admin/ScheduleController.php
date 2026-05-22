<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\User;
use App\Models\ExamType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    /**
     * Menampilkan daftar jadwal ujian dengan filter & pencarian
     */
    public function index(Request $request)
    {
        $query = Schedule::with(['subject', 'classroom', 'proctor', 'examType'])->latest();

        // 1. Fitur Pencarian (Nama Mapel atau Token Ujian)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('subject', function($subQuery) use ($search) {
                    $subQuery->where('nama_mapel', 'like', "%{$search}%");
                })
                ->orWhere('token', 'like', "%{$search}%");
            });
        }

        // 2. Filter Berdasarkan Jenis & Status Ujian
        if ($request->filled('exam_type_id')) $query->where('exam_type_id', $request->exam_type_id);
        if ($request->filled('status')) $query->where('status', $request->status);

        $schedules = $query->get();
        
        // Data Pendukung untuk Form Modal Create & Edit
        $subjects = Subject::orderBy('nama_mapel', 'asc')->get();
        $classes = Classroom::orderBy('nama_kelas', 'asc')->get();
        $examTypes = ExamType::all();
        $teachers = User::whereIn('role', ['guru', 'pengawas'])->orderBy('name', 'asc')->get();

        return view('admin.schedules.index', compact('schedules', 'subjects', 'classes', 'teachers', 'examTypes'));
    }

    /**
     * Menyimpan Jadwal Ujian Baru (Mendukung Multi-Kelas)
     */
    public function store(Request $request)
    {
        // VALIDASI INPUT KETAT: Mematikan celah input kosong atau durasi minus
        $request->validate([
            'exam_type_id'    => 'required|exists:exam_types,id',
            'subject_id'      => 'required|exists:subjects,id',
            'classroom_ids'   => 'required|array|min:1', 
            'classroom_ids.*' => 'exists:classrooms,id',
            'teacher_ids'     => 'required|array|min:1',
            'teacher_ids.*'   => 'exists:users,id',
            'proctor_id'      => 'required|exists:users,id', 
            'tanggal_ujian'   => 'required|date|after_or_equal:today', // Cegah input tanggal masa lalu
            'durasi'          => 'required|integer|min:5|max:300', // Durasi logis 5 s/d 300 menit
        ], [
            'exam_type_id.required'  => 'Jenis / tipe pelaksanaan ujian wajib dipilih!',
            'subject_id.required'    => 'Mata pelajaran ujian wajib ditentukan!',
            'classroom_ids.required' => 'Harap tentukan minimal satu rombongan kelas peserta!',
            'teacher_ids.required'   => 'Guru pengampu tidak boleh kosong (pilih ulang mata pelajaran)!',
            'proctor_id.required'    => 'Harap plotting pengawas ruangan yang bertugas menjaga!',
            'tanggal_ujian.required' => 'Tanggal pelaksanaan ujian belum ditentukan!',
            'tanggal_ujian.after_or_equal' => 'Tanggal pelaksanaan ujian tidak boleh menggunakan tanggal masa lalu!',
            'durasi.required'        => 'Durasi waktu pengerjaan wajib diisi!',
            'durasi.min'             => 'Durasi waktu pengerjaan ujian minimal adalah 5 menit!',
            'durasi.max'             => 'Durasi waktu pengerjaan ujian maksimal adalah 300 menit!',
        ]);

        try {
            // Eksekusi Pembuatan Data Berdasarkan Perulangan Kelas Terpilih
            foreach ($request->classroom_ids as $class_id) {
                Schedule::create([
                    'exam_type_id'  => $request->exam_type_id,
                    'subject_id'    => $request->subject_id,
                    'classroom_id'  => $class_id,
                    'teacher_ids'   => $request->teacher_ids,
                    'proctor_id'    => $request->proctor_id,
                    'tanggal_ujian' => $request->tanggal_ujian,
                    'durasi'        => $request->durasi,
                    'token'         => strtoupper(Str::random(6)),
                    'status'        => 'nonaktif', // Default nonaktif saat pertama dibuat
                    'created_by'    => auth()->id(),
                ]);
            }

            return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil dibuat untuk ' . count($request->classroom_ids) . ' kelas!');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memproses jadwal: ' . $e->getMessage());
        }
    }

    /**
     * API AJAX Fetch Data Guru Pengampu Berdasarkan ID Mata Pelajaran
     */
    public function getTeachers($subject_id)
    {
        $teachers = User::where('subject_id', $subject_id)
            ->where('role', 'guru')
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        return response()->json($teachers);
    }

    /**
     * Memperbarui Data Jadwal Ujian (Single Update)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'exam_type_id'  => 'required|exists:exam_types,id',
            'subject_id'    => 'required|exists:subjects,id',
            'classroom_id'  => 'required|exists:classrooms,id',
            'teacher_ids'   => 'required|array|min:1',
            'teacher_ids.*' => 'exists:users,id',
            'proctor_id'    => 'required|exists:users,id',
            'tanggal_ujian' => 'required|date|after_or_equal:today',
            'durasi'        => 'required|integer|min:5|max:300',
        ], [
            'exam_type_id.required'  => 'Jenis / tipe pelaksanaan ujian wajib dipilih!',
            'subject_id.required'    => 'Mata pelajaran ujian wajib ditentukan!',
            'classroom_id.required'  => 'Harap tentukan kelas peserta ujian!',
            'teacher_ids.required'   => 'Guru pengampu tidak boleh kosong!',
            'proctor_id.required'    => 'Harap plotting pengawas ruangan yang bertugas menjaga!',
            'tanggal_ujian.after_or_equal' => 'Tanggal pelaksanaan ujian tidak boleh menggunakan tanggal masa lalu!',
            'durasi.min'             => 'Durasi waktu pengerjaan ujian minimal adalah 5 menit!',
            'durasi.max'             => 'Durasi waktu pengerjaan ujian maksimal adalah 300 menit!',
        ]);

        try {
            $schedule = Schedule::findOrFail($id);
            $schedule->update([
                'exam_type_id'  => $request->exam_type_id,
                'subject_id'    => $request->subject_id,
                'classroom_id'  => $request->classroom_id,
                'teacher_ids'   => $request->teacher_ids,
                'proctor_id'    => $request->proctor_id,
                'tanggal_ujian' => $request->tanggal_ujian,
                'durasi'        => $request->durasi,
            ]);

            return redirect()->route('admin.schedules.index')->with('success', 'Jadwal pelaksanaan ujian berhasil diperbarui!');

        } catch (\Exception $e) {
            // Mengembalikan penanda sesi 'edit' agar modal edit otomatis tetap mengunci terbuka
            return redirect()->back()
                ->withInput()
                ->with('error_form_type', 'edit')
                ->with('error', 'Gagal memperbarui jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui Status Akses Ujian Berbasis AJAX Live Toggle Switch
     */
    public function updateStatus(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:aktif,nonaktif'
        ]);

        $schedule->status = $request->status;
        $schedule->save();

        // JIKA DISUNTIK VIA AJAX FETCH: Respon Menggunakan Format JSON murni
        if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Akses pengerjaan ujian berhasil diubah menjadi ' . $request->status
            ], 200);
        }

        return redirect()->back()->with('success', 'Status akses jadwal pengerjaan berhasil diubah!');
    }

    /**
     * Menghapus Jadwal Pelaksanaan Beserta Seluruh Relasi Database
     */
    public function destroy($id)
    {
        try {
            $schedule = Schedule::findOrFail($id);
            
            // Menggunakan database transaksi untuk menjamin integritas penghapusan data berantai
            DB::transaction(function() use ($schedule) {
                // 1. Bersihkan seluruh rekapan lembar jawaban siswa
                DB::table('student_answers')->where('schedule_id', $schedule->id)->delete();
                
                // 2. Bersihkan butir soal yang menempel di jadwal tersebut
                DB::table('questions')->where('schedule_id', $schedule->id)->delete();
                
                // 3. Eksekusi penghapusan jadwal utama
                $schedule->delete();
            });
            
            return redirect()->route('admin.schedules.index')->with('success', 'Jadwal pelaksanaan ujian beserta seluruh relasi soal & jawaban berhasil dibersihkan!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus jadwal pengerjaan: ' . $e->getMessage());
        }
    }
}