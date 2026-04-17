<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['subject', 'classroom'])->latest()->get();
        $subjects = Subject::orderBy('nama_mapel', 'asc')->get();
        $classes = Classroom::orderBy('nama_kelas', 'asc')->get();

        return view('admin.schedules.index', compact('schedules', 'subjects', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id'    => 'required|exists:subjects,id',
            'classroom_id'  => 'required|exists:classrooms,id',
            'teacher_ids'   => 'required|array|min:1',
            'tanggal_ujian' => 'required|date',
            'jam_mulai'     => 'required',
            'jam_selesai'   => 'required',
            'durasi'        => 'required|integer',
        ]);

        Schedule::create([
            'subject_id'    => $request->subject_id,
            'classroom_id'  => $request->classroom_id,
            'teacher_ids'   => $request->teacher_ids,
            'tanggal_ujian' => $request->tanggal_ujian,
            'jam_mulai'     => $request->jam_mulai,
            'jam_selesai'   => $request->jam_selesai,
            'durasi'        => $request->durasi,
            'token'         => strtoupper(Str::random(6)),
            'status'        => 'nonaktif',
        ]);

        return redirect()->back()->with('success', 'Jadwal berhasil dibuat!');
    }

    // --- INI FUNCTION YANG TADI HILANG ---
    public function update(Request $request, $id)
    {
        $request->validate([
            'subject_id'    => 'required|exists:subjects,id',
            'classroom_id'  => 'required|exists:classrooms,id',
            'teacher_ids'   => 'required|array|min:1',
            'tanggal_ujian' => 'required|date',
            'jam_mulai'     => 'required',
            'jam_selesai'   => 'required',
            'durasi'        => 'required|integer',
        ]);

        $schedule = Schedule::findOrFail($id);
        $schedule->update([
            'subject_id'    => $request->subject_id,
            'classroom_id'  => $request->classroom_id,
            'teacher_ids'   => $request->teacher_ids,
            'tanggal_ujian' => $request->tanggal_ujian,
            'jam_mulai'     => $request->jam_mulai,
            'jam_selesai'   => $request->jam_selesai,
            'durasi'        => $request->durasi,
        ]);

        return redirect()->back()->with('success', 'Jadwal berhasil diperbarui!');
    }

    public function updateStatus(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->status = $request->status;
        $schedule->save();

        return redirect()->back()->with('success', 'Status berhasil diubah!');
    }

    public function destroy($id)
    {
        Schedule::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Jadwal berhasil dihapus!');
    }
}