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
        // list penguji (boleh yang dojo_id null atau ada dojo_id)
        $examiners = User::query()
            ->where('role', 'penguji')
            ->orderBy('name')
            ->get(['id', 'name', 'dojo_id']);

        $selected = $exam->examiners()->pluck('users.id')->toArray();

        return view('admin.exams.examiners', compact('exam', 'examiners', 'selected'));
    }

    public function update(Request $request, Exam $exam)
    {
        $data = $request->validate([
            'examiner_ids' => ['array'],
            'examiner_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $ids = $data['examiner_ids'] ?? [];

        // (Opsional tapi aman) pastikan yang di-sync memang role penguji
        $ids = User::whereIn('id', $ids)->where('role', 'penguji')->pluck('id')->toArray();

        $exam->examiners()->sync($ids);

        return redirect()
            ->route('admin.exams.show', $exam)
            ->with('success', 'Penguji berhasil di-assign ke ujian.');
    }
}