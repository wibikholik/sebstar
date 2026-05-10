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

class ScheduleController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $user = Auth::user();

        // 1. Ambil jadwal milik guru ini (Mandiri) atau dimana guru ini terlibat (Pusat)
        $schedules = Schedule::with(['examType', 'subject', 'classroom'])
            ->where('created_by', $userId)
            ->orWhereJsonContains('teacher_ids', (string)$userId)
            ->latest()
            ->get();

        // 2. LOGIKA BERDASARKAN GAMBAR: Ambil mapel dari subject_id yang ada di tabel users
        // Atau ambil mapel yang teacher_id nya adalah user ini
        $mySubjects = Subject::where('id', $user->subject_id)
                             ->orWhere('teacher_id', $userId)
                             ->get();

        $examTypes = ExamType::where('is_teacher_manageable', true)->get();
        $classrooms = Classroom::all();

        return view('guru.schedules.index', compact(
            'schedules', 'examTypes', 'mySubjects', 'classrooms'
        ));
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();

        $request->validate([
            'exam_type_id' => 'required|exists:exam_types,id',
            'subject_id'   => 'required|exists:subjects,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'tanggal_ujian'=> 'required|date',
            'durasi'       => 'required|integer|min:1',
        ]);

        // VALIDASI: Pastikan guru hanya buat jadwal untuk mapel yang ditugaskan (berdasarkan ID)
        if ($request->subject_id != $user->subject_id && !Subject::where('id', $request->subject_id)->where('teacher_id', $userId)->exists()) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk mata pelajaran ini.');
        }

        Schedule::create([
            'exam_type_id' => $request->exam_type_id,
            'subject_id'   => $request->subject_id,
            'classroom_id' => $request->classroom_id,
            'teacher_ids'  => [$userId],
            'proctor_id'   => $userId,
            'tanggal_ujian'=> $request->tanggal_ujian,
            'durasi'       => $request->durasi,
            'token'        => Str::upper(Str::random(6)),
            'status'       => 'aktif',
            'created_by'   => $userId,
            'weight_pg'    => 70,
            'weight_essay' => 30,
        ]);

        return redirect()->route('guru.schedules.index')->with('success', 'Jadwal Mandiri berhasil dibuat!');
    }

    public function update(Request $request, $id)
    {
        $schedule = Schedule::where('id', $id)->where('created_by', Auth::id())->firstOrFail();
        
        $request->validate([
            'exam_type_id' => 'required|exists:exam_types,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'tanggal_ujian'=> 'required|date',
            'durasi'       => 'required|integer|min:1',
        ]);

        $schedule->update($request->only(['exam_type_id', 'classroom_id', 'tanggal_ujian', 'durasi']));
        return back()->with('success', 'Jadwal berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $schedule = Schedule::where('id', $id)->where('created_by', Auth::id())->firstOrFail();
        if ($schedule->studentAnswers()->count() > 0) {
            return back()->with('error', 'Jadwal tidak bisa dihapus karena sudah ada jawaban siswa.');
        }
        $schedule->delete();
        return back()->with('success', 'Jadwal berhasil dihapus.');
    }
}