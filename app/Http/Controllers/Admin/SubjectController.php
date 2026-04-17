<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::latest()->get();
        return view('admin.subjects.index', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'kode_mapel' => 'required|unique:subjects,kode_mapel'
        ]);

        Subject::create($request->all());
        return redirect()->back()->with('success', 'Mata Pelajaran berhasil ditambah!');
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'nama_mapel' => 'required',
            'kode_mapel' => 'required|unique:subjects,kode_mapel,' . $subject->id
        ]);

        $subject->update($request->all());
        return redirect()->back()->with('success', 'Mata Pelajaran berhasil diupdate!');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->back()->with('success', 'Mata Pelajaran berhasil dihapus!');
    }
}