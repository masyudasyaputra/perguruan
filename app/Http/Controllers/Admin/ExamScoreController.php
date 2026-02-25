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
    // Tampilkan daftar peserta untuk dinilai
    public function index(Exam $exam)
    {
        // Eager load data pendaftaran (participants) dan sabuk
        $participants = $exam->participants()->with(['user', 'currentBelt'])->get();

        $existingScores = ExamScore::where('exam_id', $exam->id)
            ->get()
            ->keyBy('member_id');

        return view('admin.exams.scoring', compact('exam', 'participants', 'existingScores'));
    }

    // Simpan nilai (Auto-Save)
    public function store(Request $request, Exam $exam)
    {
        $request->validate([
            'scores' => 'required|array',
        ]);

        try {
            foreach ($request->scores as $userId => $data) {
                // Filter hanya menyimpan jika ada data yang diinputkan
                if (collect($data)->filter()->isNotEmpty()) {

                    // Logic: Jika status bukan 'Lulus', pastikan new_belt_level_id tetap null 
                    // atau sesuai sabuk saat ini jika sistem Anda mengharuskannya.
                    $resultStatus = $data['result'] ?? 'Lulus';
                    $newBeltId = ($resultStatus === 'Lulus') ? ($data['new_belt_level_id'] ?? null) : null;

                    ExamScore::updateOrCreate(
                        [
                            'exam_id' => $exam->id,
                            'member_id' => $userId,
                        ],
                        [
                            'examiner_id' => auth()->id(),
                            'kihon' => $data['kihon'] ?? null,
                            'kata' => $data['kata'] ?? null,
                            'kumite' => $data['kumite'] ?? null,
                            'result' => $resultStatus,
                            'new_belt_level_id' => $data['new_belt_level_id'] ?? null, // Simpan saja id yang dikirim dari select
                        ]
                    );
                }
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => 'Data tersimpan otomatis.']);
            }

            return back()->with('success', 'Semua nilai berhasil disimpan.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    // Finalisasi: Update User & Masuk ke Belt History
    public function finalize(Exam $exam)
    {
        DB::beginTransaction();
        try {
            // Ambil semua skor yang sudah masuk untuk ujian ini
            $scores = ExamScore::where('exam_id', $exam->id)->get();

            foreach ($scores as $score) {
                $isLulus = strtolower(trim($score->result)) === 'lulus';

                // Hanya proses yang Lulus dan memiliki Target Sabuk Baru
                if ($isLulus && $score->new_belt_level_id) {
                    $member = User::find($score->member_id);

                    if ($member) {
                        // 1. Update Sabuk Utama di tabel Users
                        $member->update([
                            'belt_level_id' => $score->new_belt_level_id
                        ]);

                        // 2. Catat di riwayat kenaikan sabuk (Belt History)
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

            // Tandai ujian selesai agar tidak bisa diedit sembarangan lagi
            $exam->update(['status' => 'completed']);

            DB::commit();

            return redirect()->route('admin.exams.index')->with('success', 'Ujian telah difinalisasi. Data peserta telah diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat finalisasi: ' . $e->getMessage());
        }
    }
}