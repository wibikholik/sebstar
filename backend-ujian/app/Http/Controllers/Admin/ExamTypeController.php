<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamType;
use Illuminate\Http\Request;

class ExamTypeController extends Controller
{
    public function index()
    {
        $examTypes = ExamType::latest()->get();
        return view('admin.exam_types.index', compact('examTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:exam_types,name',
        ]);

        ExamType::create([
            'name' => $request->name,
            'is_teacher_manageable' => $request->has('is_teacher_manageable')
        ]);

        return back()->with('success', 'Tipe Ujian berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $type = ExamType::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:exam_types,name,'.$id,
        ]);

        $type->update([
            'name' => $request->name,
            'is_teacher_manageable' => $request->has('is_teacher_manageable')
        ]);

        return back()->with('success', 'Tipe Ujian berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $type = ExamType::findOrFail($id);
        
        // Cek apakah sudah dipakai di jadwal, biar tidak error constraint
        if($type->schedules()->count() > 0) {
            return back()->with('error', 'Tidak bisa dihapus karena sudah digunakan di jadwal ujian!');
        }

        $type->delete();
        return back()->with('success', 'Tipe Ujian berhasil dihapus!');
    }
    
}