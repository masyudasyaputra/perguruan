<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\User;
use App\Models\Dojo;
use App\Models\City;
use App\Models\BeltLevel;
use App\Models\ExamParticipant;
use App\Models\ExamFee;
use App\Models\ExamScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    /**
     * Menampilkan daftar semua jadwal ujian.
     */
    public function index(): View
    {
        $user = Auth::user();
        $query = Exam::with(['province', 'participants']);

        if ($user->role === 'pengprov') {
            $query->where(function ($q) use ($user) {
                $q->where('province_id', $user->province_id)
                    ->orWhereNull('province_id');
            });
        }

        $exams = $query->orderBy('execution_date', 'desc')->get();
        return view('admin.exams.index', compact('exams'));
    }

    /**
     * MENYIMPAN JADWAL UJIAN BARU
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'execution_date' => 'required|date',
            'location' => 'required|string|max:255',
            'status' => 'required|in:open,draft',
        ]);

        try {
            if (Auth::user()->role === 'pengprov') {
                $validated['province_id'] = Auth::user()->province_id;
            }

            Exam::create($validated);

            return redirect()->route('admin.exams.index')
                ->with('success', 'Jadwal ujian baru berhasil diterbitkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat jadwal: ' . $e->getMessage());
        }
    }

    /**
     * UPDATE JADWAL UJIAN
     */
    public function update(Request $request, Exam $exam): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'execution_date' => 'required|date',
            'location' => 'required|string|max:255',
            'status' => 'required|in:open,draft',
        ]);

        try {
            $exam->update($validated);
            return back()->with('success', 'Jadwal ujian berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui jadwal: ' . $e->getMessage());
        }
    }

    /**
     * HAPUS JADWAL UJIAN
     */
    public function destroy(Exam $exam): RedirectResponse
    {
        try {
            if ($exam->participants()->count() > 0) {
                return back()->with('error', 'Jadwal tidak bisa dihapus karena sudah memiliki peserta terdaftar.');
            }

            $exam->delete();
            return back()->with('success', 'Jadwal ujian berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail satu ujian dan daftar pesertanya.
     */
    public function show(Request $request, Exam $exam): View
    {
        $user = Auth::user();

        $exam->load([
            'participants' => function ($query) use ($request) {
                if ($request->filled('search')) {
                    $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));
                }

                if ($request->filled('dojo_id')) {
                    $query->where('dojo_id', $request->dojo_id);
                }

                if ($request->filled('payment_status')) {
                    $query->where('payment_status', $request->payment_status);
                }
            },
            'participants.user',
            'participants.dojo',
            'participants.currentBelt',
            'participants.targetBelt'
        ]);

        $dojos = Dojo::orderBy('name')->get();
        $cities = City::orderBy('name')->get();
        $belts = BeltLevel::orderBy('order')->get();

        $memberQuery = User::where('role', 'member');
        if ($user->role === 'admin_dojo') {
            $memberQuery->where('dojo_id', $user->dojo_id);
        } elseif ($user->role === 'pengprov') {
            $memberQuery->where('province_id', $user->province_id);
        }
        $members = $memberQuery->orderBy('name')->get();

        return view('admin.exams.show', compact('exam', 'members', 'dojos', 'cities', 'belts'));
    }

    /**
     * DAFTARKAN BANYAK MEMBER KE UJIAN
     */
    public function registerMember(Request $request, $examId): RedirectResponse
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $exam = Exam::findOrFail($examId);
        $auth = Auth::user();
        $countSuccess = 0;
        $countSkipped = 0;

        DB::beginTransaction();
        try {
            foreach ($request->user_ids as $userId) {
                $user = User::findOrFail($userId);

                if ($auth->role === 'admin_dojo' && $user->dojo_id !== $auth->dojo_id) {
                    continue;
                }

                $exists = ExamParticipant::where('exam_id', $examId)
                    ->where('user_id', $user->id)
                    ->exists();

                if ($exists) {
                    $countSkipped++;
                    continue;
                }

                $currentBeltId = $user->belt_level_id ?? 1;
                $targetBeltId = $currentBeltId + 1;
                $fee = ExamFee::where('belt_level_id', $targetBeltId)->first();

                ExamParticipant::create([
                    'exam_id' => $exam->id,
                    'user_id' => $user->id,
                    'dojo_id' => $user->dojo_id,
                    'current_belt_id' => $currentBeltId,
                    'target_belt_id' => $targetBeltId,
                    'fee_amount' => $fee ? $fee->amount : 0,
                    'payment_status' => 'unpaid'
                ]);

                $countSuccess++;
            }

            DB::commit();

            $message = "Berhasil mendaftarkan $countSuccess peserta.";
            if ($countSkipped > 0) {
                $message .= " ($countSkipped anggota dilewati karena sudah terdaftar).";
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mendaftarkan peserta: ' . $e->getMessage());
        }
    }

    /**
     * PEMBAYARAN MASSAL (BULK PAYMENT)
     * Menyesuaikan request dari Blade untuk update status payment peserta
     */
    public function bulkPayment(Request $request, $examId): RedirectResponse
    {
        $request->validate([
            'participant_ids' => 'required|array',
            'participant_ids.*' => 'exists:exam_participants,id'
        ]);

        $auth = Auth::user();

        try {
            $query = ExamParticipant::where('exam_id', $examId)
                ->whereIn('id', $request->participant_ids);

            // Security check: Admin dojo hanya bisa update pembayaran anggota dojonya sendiri
            if ($auth->role === 'admin_dojo') {
                $query->where('dojo_id', $auth->dojo_id);
            }

            $count = $query->update([
                'payment_status' => 'paid',
                'updated_at' => now()
            ]);

            return back()->with('success', "$count peserta berhasil diverifikasi pembayarannya.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * UPDATE HASIL UJIAN (UPDATE RESULT)
     */
    public function updateResult(Request $request, Exam $exam): RedirectResponse
    {
        $request->validate([
            'participant_id' => 'required|exists:exam_participants,id',
            'status_result' => 'required|in:pass,fail,pending',
        ]);

        try {
            $participant = ExamParticipant::findOrFail($request->participant_id);
            $participant->update(['status_result' => $request->status_result]);

            return back()->with('success', 'Hasil ujian berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui hasil: ' . $e->getMessage());
        }
    }

    /**
     * HAPUS PESERTA DARI DAFTAR UJIAN (SINGLE)
     */
    public function removeMember(ExamParticipant $participant): RedirectResponse
    {
        $auth = Auth::user();

        if ($auth->role === 'admin_dojo' && $participant->dojo_id !== $auth->dojo_id) {
            abort(403, 'Akses ditolak.');
        }

        $participant->delete();
        return back()->with('success', 'Peserta berhasil dihapus dari daftar.');
    }

    /**
     * HAPUS BANYAK PESERTA SEKALIGUS (BULK DELETE)
     */
    public function bulkRemoveMember(Request $request): RedirectResponse
    {
        $request->validate([
            'participant_ids' => 'required|array',
            'participant_ids.*' => 'exists:exam_participants,id'
        ]);

        $auth = Auth::user();

        try {
            $query = ExamParticipant::whereIn('id', $request->participant_ids);

            if ($auth->role === 'admin_dojo') {
                $query->where('dojo_id', $auth->dojo_id);
            }

            $deletedCount = $query->delete();

            return back()->with('success', "$deletedCount peserta berhasil dihapus secara massal.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus peserta: ' . $e->getMessage());
        }
    }

    // --- MANAJEMEN MASTER BIAYA UJIAN ---

    public function feeIndex(): View
    {
        $belts = BeltLevel::orderBy('order')->get();
        $fees = ExamFee::with('beltLevel')->get();
        return view('admin.exams.fees', compact('belts', 'fees'));
    }

    public function feeStore(Request $request): RedirectResponse
    {
        $request->validate([
            'belt_level_id' => 'required|exists:belt_levels,id',
            'amount' => 'required|numeric|min:0',
        ]);

        ExamFee::updateOrCreate(
            ['belt_level_id' => $request->belt_level_id],
            ['amount' => $request->amount]
        );

        return back()->with('success', 'Master biaya ujian diperbarui!');
    }

    public function feeDestroy($id): RedirectResponse
    {
        ExamFee::findOrFail($id)->delete();
        return back()->with('success', 'Konfigurasi biaya dihapus.');
    }

    // Tambahkan di dalam class ExamController

    public function showScoring(Exam $exam, $memberId)
    {
        $score = \App\Models\ExamScore::where('exam_id', $exam->id)
            ->where('member_id', $memberId)
            ->first();

        if (!$score) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        // Mapping manual berdasarkan data gambar tabel belt_levels Anda
        $beltMapping = [
            1 => 'Putih - Kyu 10',
            2 => 'Kuning Muda - Kyu 9',
            3 => 'Kuning Tua - Kyu 8',
            4 => 'Orange - Kyu 7',
            5 => 'Hijau - Kyu 6',
            6 => 'Biru - Kyu 5',
            7 => 'Ungu - Kyu 4',
            8 => 'Cokelat - Kyu 3',
            9 => 'Cokelat - Kyu 2',
            10 => 'Cokelat - Kyu 1',
            11 => 'Hitam - DAN I',
            12 => 'Hitam - DAN II',
            13 => 'Hitam - DAN III',
            14 => 'Hitam - DAN IV',
            15 => 'Hitam - DAN V',
            16 => 'Hitam - DAN VI',
        ];

        // Ambil string berdasarkan ID yang ada di kolom new_belt_level_id
        $newBeltName = $beltMapping[$score->new_belt_level_id] ?? 'Level ' . $score->new_belt_level_id;

        return response()->json([
            'kihon' => $score->kihon,
            'kata' => $score->kata,
            'kumite' => $score->kumite,
            'result' => $score->result,
            'notes' => $score->notes ?? 'Tidak ada catatan khusus.',
            'new_belt_name' => $newBeltName // Data ini yang akan dikirim ke Modal
        ]);
    }

    public function getScoringDetail($examId, $memberId)
    {
        // Kita ambil data skor dan sertakan relasi newBeltLevel
        $score = \App\Models\ExamScore::where('exam_id', $examId)
            ->where('member_id', $memberId)
            ->with(['newBeltLevel'])
            ->first();

        if (!$score) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        // Buat string format: "Nama Sabuk (Kyu X)"
        $beltInfo = '-';
        if ($score->newBeltLevel) {
            $beltInfo = $score->newBeltLevel->name . " - Kyu " . $score->newBeltLevel->order;
        }

        return response()->json([
            'kihon' => $score->kihon,
            'kata' => $score->kata,
            'kumite' => $score->kumite,
            'result' => $score->result,
            'notes' => $score->notes,
            'new_belt_name' => $beltInfo // Ini yang akan dibaca x-text
        ]);
    }

}