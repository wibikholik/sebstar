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
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = Schedule::with(['subject', 'classroom', 'proctor', 'examType'])->latest();

        // 1. Searching
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('subject', function($subQuery) use ($search) {
                    $subQuery->where('nama_mapel', 'like', "%{$search}%");
                })
                ->orWhere('token', 'like', "%{$search}%");
            });
        }

        // 2. Filter Jenis & Status
        if ($request->filled('exam_type_id')) $query->where('exam_type_id', $request->exam_type_id);
        if ($request->filled('status')) $query->where('status', $request->status);

        $schedules = $query->get();
        
        // Data Pendukung
        $subjects = Subject::orderBy('nama_mapel', 'asc')->get();
        $classes = Classroom::orderBy('nama_kelas', 'asc')->get();
        $examTypes = ExamType::all();
        $teachers = User::whereIn('role', ['guru', 'pengawas'])->orderBy('name', 'asc')->get();

        return view('admin.schedules.index', compact('schedules', 'subjects', 'classes', 'teachers', 'examTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'exam_type_id'  => 'required|exists:exam_types,id',
            'subject_id'    => 'required|exists:subjects,id',
            'classroom_ids' => 'required|array|min:1', 
            'teacher_ids'   => 'required|array|min:1',
            'proctor_id'    => 'required|exists:users,id', 
            'tanggal_ujian' => 'required|date',
            'durasi'        => 'required|integer|min:1', // Menggunakan durasi menit
        ]);

        // Looping untuk Multi-Kelas
        foreach ($request->classroom_ids as $class_id) {
            Schedule::create([
                'exam_type_id'  => $request->exam_type_id,
                'subject_id'    => $request->subject_id,
                'classroom_id'  => $class_id,
                'teacher_ids'   => $request->teacher_ids,
                'proctor_id'    => $request->proctor_id,
                'tanggal_ujian' => $request->tanggal_ujian,
                'durasi'        => $request->durasi, // Langsung simpan menit
                'token'         => strtoupper(Str::random(6)),
                'status'        => 'nonaktif',
            ]);
        }

        return redirect()->back()->with('success', 'Jadwal berhasil dibuat untuk ' . count($request->classroom_ids) . ' kelas!');
    }
    public function getTeachers($subject_id)
{
    // Ambil guru yang mengajar mata pelajaran tersebut
    $teachers = User::where('subject_id', $subject_id)
        ->where('role', 'guru')
        ->orderBy('name', 'asc')
        ->get(['id', 'name']);

    // Pastikan mengembalikan JSON
    return response()->json($teachers);
}

    public function update(Request $request, $id)
    {
        $request->validate([
            'exam_type_id'  => 'required|exists:exam_types,id',
            'subject_id'    => 'required|exists:subjects,id',
            'classroom_id'  => 'required|exists:classrooms,id',
            'teacher_ids'   => 'required|array|min:1',
            'proctor_id'    => 'required|exists:users,id',
            'tanggal_ujian' => 'required|date',
            'durasi'        => 'required|integer|min:1',
        ]);

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

        return redirect()->back()->with('success', 'Jadwal diperbarui!');
    }

    public function updateStatus(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->status = $request->status;
        $schedule->save();
        return redirect()->back()->with('success', 'Status jadwal berhasil diubah!');
    }

    public function destroy($id)
    {
        try {
            $schedule = Schedule::findOrFail($id);
            
            // Hapus data terkait secara manual untuk keamanan foreign key
            \DB::transaction(function() use ($schedule) {
                // 1. Hapus Jawaban Siswa
                \DB::table('student_answers')->where('schedule_id', $schedule->id)->delete();
                
                // 2. Hapus Soal
                \DB::table('questions')->where('schedule_id', $schedule->id)->delete();
                
                // 3. Hapus Jadwal
                $schedule->delete();
            });
            
            return redirect()->back()->with('success', 'Jadwal dan seluruh data terkait berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus: ' . $e->getMessage()]);
        }
    }
}