<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\User;
use Illuminate\Http\Request;

class ExamExaminerController extends Controller
{
    public function edit(Exam $exam)
{
    // Mengambil user yang punya role 'penguji' baik di kolom utama atau array JSON
    $examiners = User::where('role', 'penguji')
        ->orWhereJsonContains('roles', 'penguji')
        ->orderBy('name')
        ->get();

    // ID penguji yang sudah terpilih untuk ujian ini (dari tabel pivot)
    $selected = $exam->examiners()->pluck('users.id')->toArray();

    return view('admin.exams.examiners', compact('exam', 'examiners', 'selected'));
}

public function update(Request $request, Exam $exam)
{
    $request->validate([
        'examiner_ids' => 'nullable|array',
        'examiner_ids.*' => 'exists:users,id'
    ]);

    // Sinkronisasi tabel pivot (exam_user)
    $exam->examiners()->sync($request->examiner_ids ?? []);

    return redirect()->back()->with('success', 'Tim penguji berhasil diperbarui!');
}
}