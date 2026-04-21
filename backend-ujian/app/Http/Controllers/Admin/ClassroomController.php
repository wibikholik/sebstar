<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Major;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index()
    {
        // Eager loading 'major' agar tidak N+1 query
        $classes = Classroom::with('major')->orderBy('nama_kelas', 'asc')->get();
        $majors = Major::orderBy('nama_jurusan', 'asc')->get();
        
        return view('admin.classrooms.index', compact('classes', 'majors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255|unique:classrooms,nama_kelas',
            'major_id'   => 'required|exists:majors,id',
        ]);

        // Laravel akan otomatis mencocokkan input 'major_id' ke kolom 'major_id'
        Classroom::create($request->only(['nama_kelas', 'major_id']));
        
        return redirect()->back()->with('success', 'Kelas berhasil ditambahkan!');
    }

    public function update(Request $request, Classroom $classroom)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:classrooms,nama_kelas,' . $classroom->id,
            'major_id'   => 'required|exists:majors,id',
        ]);

        // Kita buang input 'jurusan' jika ada, hanya update field yang benar
        $classroom->update($request->only(['nama_kelas', 'major_id']));
        
        return redirect()->back()->with('success', 'Data kelas berhasil diperbarui!');
    }

    public function destroy(Classroom $classroom)
    {
        $classroom->delete();
        return redirect()->back()->with('success', 'Kelas berhasil dihapus!');
    }
}