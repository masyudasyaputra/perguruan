<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamScore;
use App\Models\User;
use Illuminate\Http\Request;

class ExamScoreController extends Controller
{
    // Tampilkan daftar peserta untuk dinilai
    public function index(Exam $exam)
    {
        // Ambil peserta yang terdaftar di ujian ini
        $participants = $exam->participants; 
        
        // Ambil nilai yang sudah ada (untuk ditampilkan kembali di form jika ingin edit)
        $existingScores = ExamScore::where('exam_id', $exam->id)
                            ->where('examiner_id', auth()->id())
                            ->get()
                            ->keyBy('member_id');

        return view('admin.exams.scoring', compact('exam', 'participants', 'existingScores'));
    }

    // Simpan nilai secara massal
    public function store(Request $request, Exam $exam)
{
    $request->validate([
        'scores' => 'required|array',
    ]);

    foreach ($request->scores as $userId => $data) {
        // Hanya simpan jika ada salah satu nilai yang diisi
        if (isset($data['kihon']) || isset($data['kata']) || isset($data['kumite'])) {
            ExamScore::updateOrCreate(
                [
                    'exam_id'   => $exam->id,
                    'member_id' => $userId, // Ini harus sesuai dengan ID di tabel users
                ],
                [
                    'examiner_id' => auth()->id(),
                    'kihon'       => $data['kihon'] ?? null,
                    'kata'        => $data['kata'] ?? null,
                    'kumite'      => $data['kumite'] ?? null,
                    'result'      => $data['result'] ?? 'Tidak Lulus',
                ]
            );
        }
    }

    return back()->with('success', 'Semua nilai berhasil disimpan.');
}
}
