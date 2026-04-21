<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Major;
use Illuminate\Http\Request;

class MajorController extends Controller
{
    public function index()
    {
        $majors = Major::latest()->get();
        return view('admin.majors.index', compact('majors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jurusan' => 'required|unique:majors,nama_jurusan',
            'singkatan'    => 'required|unique:majors,singkatan',
        ]);

        Major::create($request->all());
        return redirect()->back()->with('success', 'Jurusan berhasil ditambah!');
    }

    public function update(Request $request, Major $major)
    {
        $request->validate([
            'nama_jurusan' => 'required|unique:majors,nama_jurusan,' . $major->id,
            'singkatan'    => 'required|unique:majors,singkatan,' . $major->id,
        ]);

        $major->update($request->all());
        return redirect()->back()->with('success', 'Jurusan berhasil diupdate!');
    }

    public function destroy(Major $major)
    {
        $major->delete();
        return redirect()->back()->with('success', 'Jurusan berhasil dihapus!');
    }
}