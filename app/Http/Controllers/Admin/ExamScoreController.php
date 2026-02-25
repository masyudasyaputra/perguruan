<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamScore;
use App\Models\User;
use App\Models\BeltHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamScoreController extends Controller
{
    /**
     * Tampilkan halaman penilaian (Scoring Grid)
     */
    public function index(Exam $exam)
    {
        // Eager load data pendaftaran (participants) dan sabuk
        $participants = $exam->participants()->with(['user', 'currentBelt'])->get();

        // Ambil skor yang sudah ada untuk ditampilkan awal, termasuk nama penguji
        $existingScores = ExamScore::where('exam_id', $exam->id)
            ->leftJoin('users', 'exam_scores.examiner_id', '=', 'users.id')
            ->select('exam_scores.*', 'users.name as examiner_name')
            ->get()
            ->keyBy('member_id');

        return view('admin.exams.scoring', compact('exam', 'participants', 'existingScores'));
    }

    /**
     * API untuk Fetch Data Scoring secara Real-time (Polling)
     * Menangani request dari JavaScript (fetchUpdates) agar data antar penguji sinkron
     */
    public function show(Exam $exam)
    {
        $scores = ExamScore::where('exam_id', $exam->id)
            ->leftJoin('users', 'exam_scores.examiner_id', '=', 'users.id')
            ->select('exam_scores.*', 'users.name as examiner_name')
            ->get()
            ->keyBy('member_id');

        return response()->json(['scores' => $scores]);
    }

    /**
     * Simpan nilai (Auto-Save via AJAX)
     * Menggunakan updateOrCreate untuk efisiensi dan mencegah error Integrity Constraint
     */
    public function store(Request $request, Exam $exam)
    {
        $scores = $request->input('scores', []);

        foreach ($scores as $memberId => $data) {
            // Validasi: Hanya simpan jika minimal salah satu field penilaian sudah berinteraksi (tidak null)
            // Ini untuk mengatasi error "Column 'kihon' cannot be null" saat auto-save pertama kali
            if (isset($data['kihon']) || isset($data['kata']) || isset($data['kumite'])) {

                ExamScore::updateOrCreate(
                    [
                        'exam_id' => $exam->id,
                        'member_id' => $memberId,
                    ],
                    [
                        'examiner_id' => $data['examiner_id'],
                        'kihon' => $data['kihon'] ?? null,
                        'kata' => $data['kata'] ?? null,
                        'kumite' => $data['kumite'] ?? null,
                        'result' => $data['result'] ?? 'Lulus',
                        'new_belt_level_id' => $data['new_belt_level_id'] ?? null,
                    ]
                );
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan otomatis'
        ]);
    }

    /**
     * Finalisasi: Update Sabuk User secara permanen & Catat di Belt History
     */
    public function finalize(Exam $exam)
    {
        DB::beginTransaction();
        try {
            $scores = ExamScore::where('exam_id', $exam->id)->get();

            foreach ($scores as $score) {
                $isLulus = strtolower(trim($score->result)) === 'lulus';

                if ($isLulus && $score->new_belt_level_id) {
                    $member = User::find($score->member_id);

                    if ($member) {
                        // 1. Update Sabuk Utama di tabel Users
                        $member->update([
                            'belt_level_id' => $score->new_belt_level_id
                        ]);

                        // 2. Catat Riwayat Kenaikan Sabuk
                        BeltHistory::updateOrCreate(
                            [
                                'user_id' => $member->id,
                                'exam_id' => $exam->id,
                            ],
                            [
                                'belt_level_id' => $score->new_belt_level_id,
                                'achieved_at' => now(),
                                'description' => "Lulus Ujian: " . $exam->name
                            ]
                        );
                    }
                }
            }

            // 3. Update Status Ujian
            $exam->update(['status' => 'completed']);

            DB::commit();

            return redirect()->route('admin.exams.index')
                ->with('success', 'Ujian telah difinalisasi. Sabuk peserta telah diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat finalisasi: ' . $e->getMessage());
        }
    }
}