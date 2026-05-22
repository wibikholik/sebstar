<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\ExamType;
use App\Models\Subject;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Menampilkan daftar jadwal ujian mandiri dan pusat untuk guru terkait
     */
    public function index()
    {
        $userId = Auth::id();
        $user = Auth::user();

        // 1. Ambil jadwal milik guru ini (Mandiri) atau dimana guru ini terlibat (Pusat)
        $schedules = Schedule::with(['examType', 'subject', 'classroom'])
            ->where('created_by', $userId)
            ->orWhereJsonContains('teacher_ids', (string)$userId)
            ->orWhereJsonContains('teacher_ids', (int)$userId) // Antisipasi casting integer pada array JSON
            ->latest()
            ->get();

        // 2. Ambil mapel dari subject_id yang ditugaskan di profil bapak/ibu guru terpilih
        $mySubjects = Subject::where('id', $user->subject_id)
                             ->orWhere('teacher_id', $userId)
                             ->get();

        // Mengambil tipe ujian yang diizinkan oleh admin (is_teacher_manageable = 1) untuk pembuatan jadwal baru
        $examTypes = ExamType::where('is_teacher_manageable', 1)->get();
        $classrooms = Classroom::orderBy('nama_kelas', 'asc')->get();

        return view('guru.schedules.index', compact(
            'schedules', 'examTypes', 'mySubjects', 'classrooms'
        ));
    }

    /**
     * Menyimpan Jadwal Ujian Mandiri Baru
     */
    public function store(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();

        // VALIDASI INPUT KETAT: Memastikan penulisan waktu logis dan anti tanggal masa lalu
        $request->validate([
            'exam_type_id' => 'required|exists:exam_types,id',
            'subject_id'   => 'required|exists:subjects,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'tanggal_ujian'=> 'required|date|after_or_equal:today', // Menghalangi pemilihan tanggal masa lalu
            'durasi'       => 'required|integer|min:5|max:300', // Durasi pengerjaan logis 5 s/d 300 menit
        ], [
            'exam_type_id.required' => 'Jenis / tipe pelaksanaan ujian wajib ditentukan!',
            'subject_id.required'   => 'Mata pelajaran yang akan diujikan belum dipilih!',
            'classroom_id.required' => 'Target kelas peserta ujian wajib ditentukan!',
            'tanggal_ujian.required' => 'Tanggal pelaksanaan ujian belum ditentukan!',
            'tanggal_ujian.after_or_equal' => 'Akses ditolak! Tanggal pelaksanaan ujian tidak boleh menggunakan tanggal masa lalu.',
            'durasi.required'       => 'Durasi waktu pengerjaan wajib diisi!',
            'durasi.min'            => 'Durasi pengerjaan ujian mandiri minimal adalah 5 menit!',
            'durasi.max'            => 'Durasi pengerjaan ujian mandiri maksimal adalah 300 menit!',
        ]);

        try {
            // INTEGRITAS LEVEL ADMIN: Pastikan jenis ujian memang diizinkan dikelola secara mandiri oleh guru
            $examType = ExamType::findOrFail($request->exam_type_id);
            if (!$examType->is_teacher_manageable) {
                return back()->withInput()->with('error', 'Akses ditolak! Tipe ujian ini bersifat terpusat dan hanya boleh dijadwalkan oleh pihak Admin.');
            }

            // INTEGRITAS KEPEMILIKAN MAPEL: Memastikan guru tidak menembak mapel milik guru lain
            if ($request->subject_id != $user->subject_id && !Subject::where('id', $request->subject_id)->where('teacher_id', $userId)->exists()) {
                return back()->withInput()->with('error', 'Otoritas ditolak! Anda tidak memiliki hak akses mengajar untuk mata pelajaran terpilih.');
            }

            Schedule::create([
                'exam_type_id' => $request->exam_type_id,
                'subject_id'   => $request->subject_id,
                'classroom_id' => $request->classroom_id,
                'teacher_ids'  => [$userId], // Menyimpan ID guru ke dalam array format JSON
                'proctor_id'   => $userId,   // Otomatis guru pengampu bertindak sebagai pengawas ruangan awal
                'tanggal_ujian'=> $request->tanggal_ujian,
                'durasi'       => $request->durasi,
                'token'        => Str::upper(Str::random(6)),
                'status'       => 'nonaktif', // Default nonaktif demi keamanan token kebocoran soal
                'created_by'   => $userId,
                'weight_pg'    => 70,        // Bobot otomatis standar SEBSTAR
                'weight_essay' => 30,
            ]);

            return redirect()->route('guru.schedules.index')->with('success', 'Jadwal pelaksanaan Ujian Mandiri berhasil dibuat!');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memproses pembuatan jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui Data Jadwal Ujian Mandiri (Update)
     */
    public function update(Request $request, $id)
    {
        // Mencari jadwal dan memastikan murni dibuat oleh guru yang bersangkutan
        $schedule = Schedule::where('id', $id)->where('created_by', Auth::id())->firstOrFail();
        
        $request->validate([
            'exam_type_id' => 'required|exists:exam_types,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'tanggal_ujian'=> 'required|date|after_or_equal:today',
            'durasi'       => 'required|integer|min:5|max:300',
        ], [
            'exam_type_id.required' => 'Jenis / tipe ujian wajib dipilih!',
            'classroom_id.required' => 'Target rombel kelas belum ditentukan!',
            'tanggal_ujian.after_or_equal' => 'Tanggal pelaksanaan ujian tidak boleh menggunakan tanggal masa lalu!',
            'durasi.min'            => 'Durasi pengerjaan ujian minimal adalah 5 menit!',
            'durasi.max'            => 'Durasi pengerjaan ujian maksimal adalah 300 menit!',
        ]);

        try {
            // Cegah perubahan ke tipe ujian terpusat milik admin secara ilegal
            $examType = ExamType::findOrFail($request->exam_type_id);
            if (!$examType->is_teacher_manageable) {
                return back()->withInput()->with('error_form_type', 'edit')->with('error', 'Akses ditolak. Tipe ujian tujuan dikunci secara sepihak oleh pusat/Admin.');
            }

            $schedule->update($request->only(['exam_type_id', 'classroom_id', 'tanggal_ujian', 'durasi']));
            return back()->with('success', 'Jadwal ujian mandiri berhasil diperbarui!');

        } catch (\Exception $e) {
            // Mengirimkan penanda sesi 'edit' agar modal edit di blade otomatis mengunci terbuka pasca-reload
            return redirect()->back()
                ->withInput()
                ->with('error_form_type', 'edit')
                ->with('error', 'Gagal memperbarui jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus Jadwal Ujian Mandiri dengan Pengaman Riwayat Jawaban Siswa
     */
    public function destroy($id)
    {
        try {
            $schedule = Schedule::where('id', $id)->where('created_by', Auth::id())->firstOrFail();
            
            // CEK INTEGRITAS: Tolak aksi hapus jika lembar jawaban siswa sudah masuk ke database Laragon
            // Menggunakan penamaan method dinamis studentAnswers atau riwayat sesuai model pengait jadwal
            if ($schedule->studentAnswers()->count() > 0) {
                return back()->with('error', 'Akses hapus diblokir! Jadwal tidak bisa dihapus karena sudah ada rekapan jawaban lembar ujian dari siswa.');
            }

            $schedule->delete();
            return back()->with('success', 'Jadwal pelaksanaan ujian mandiri berhasil dihapus dari daftar.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kendala sistem: ' . $e->getMessage());
        }
    }

    /**
     * LIVE AJAX TOGGLE STATUS BERDASARKAN KUNCI IZIN KHUSUS ADMIN
     */
    public function toggleStatus(Request $request, $id)
    {
        // Cari jadwal beserta relasi tipe ujian pemicu hak izin
        $schedule = Schedule::with('examType')->findOrFail($id);
        $userId = Auth::id();

        // Validasi kepemilikan otoritas pengawasan jadwal ujian
        $isCreator = $schedule->created_by == $userId;
        $isTeacherInvolved = is_array($schedule->teacher_ids) && in_array($userId, $schedule->teacher_ids);
        
        if (!$isCreator && !$isTeacherInvolved) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Akses ditolak! Anda tidak memiliki otoritas atas pengawasan jadwal ujian ini.'
            ], 403);
        }

        // VALIDASI KUNCI MASTER ADMIN: Cek status is_teacher_manageable
        if (!$schedule->examType || (int)$schedule->examType->is_teacher_manageable !== 1) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengubah status! Tipe ujian ini bersifat terpusat/nasional dan dikunci penuh oleh Admin.'
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:aktif,nonaktif'
        ]);

        $schedule->status = $request->status;
        $schedule->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Akses pengerjaan sesi ujian berhasil diubah menjadi ' . strtoupper($request->status)
        ], 200);
    }
}