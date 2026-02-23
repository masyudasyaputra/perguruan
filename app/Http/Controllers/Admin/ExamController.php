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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Exam::with(['province', 'participants']);

        if ($user->role === 'pengprov') {
            $query->where(function($q) use ($user) {
                $q->where('province_id', $user->province_id)->orWhereNull('province_id');
            });
        }

        $exams = $query->orderBy('execution_date', 'desc')->get();
        return view('admin.exams.index', compact('exams'));
    }

    public function show(Request $request, Exam $exam)
    {
        $user = Auth::user();
        
        $exam->load(['participants' => function($query) use ($request) {
            if ($request->filled('search')) {
                $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));
            }

            if ($request->filled('dojo_id')) {
                $query->where('dojo_id', $request->dojo_id);
            }

            if ($request->filled('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }
        }, 'participants.user', 'participants.dojo', 'participants.currentBelt', 'participants.targetBelt']);

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
     * DAFTARKAN MEMBER & TARIK BIAYA DARI EXAM_FEES
     */
    public function registerMember(Request $request, $examId)
    {
        $exam = Exam::findOrFail($examId);
        $user = User::findOrFail($request->user_id);
        
        // 1. Cek apakah user sudah terdaftar di ujian ini
        $exists = ExamParticipant::where('exam_id', $examId)
                                 ->where('user_id', $user->id)
                                 ->exists();
        if ($exists) {
            return back()->with('error', 'Member ini sudah terdaftar dalam sesi ujian ini.');
        }

        // 2. Tentukan target sabuk (naik 1 level dari level saat ini)
        $targetBeltId = $user->belt_level_id + 1; 

        // 3. AMBIL BIAYA BERDASARKAN belt_level_id (SESUAI DATABASE ANDA)
        $fee = ExamFee::where('belt_level_id', $targetBeltId)->first();

        // 4. Eksekusi simpan
        $exam->participants()->create([
            'user_id'         => $user->id,
            'dojo_id'         => $user->dojo_id,
            'current_belt_id' => $user->belt_level_id,
            'target_belt_id'  => $targetBeltId,
            'fee_amount'      => $fee ? $fee->amount : 0, 
            'payment_status'  => 'unpaid'
        ]);

        return back()->with('success', 'Peserta berhasil didaftarkan dengan biaya ujian yang sesuai.');
    }

    public function removeMember(ExamParticipant $participant)
    {
        $participant->delete();
        return back()->with('success', 'Peserta berhasil dihapus.');
    }

    // --- MANAJEMEN MASTER BIAYA UJIAN ---

    public function feeIndex()
    {
        $belts = BeltLevel::orderBy('order')->get();
        $fees = ExamFee::with('beltLevel')->get();
        return view('admin.exams.fees', compact('belts', 'fees'));
    }

    public function feeStore(Request $request)
    {
        $request->validate([
            'belt_level_id' => 'required|exists:belt_levels,id', // Diubah dari belt_id
            'amount'        => 'required|numeric|min:0',
        ]);

        // Gunakan belt_level_id sesuai kolom di tabel MySQL Anda
        ExamFee::updateOrCreate(
            ['belt_level_id' => $request->belt_level_id],
            ['amount'        => $request->amount]
        );

        return redirect()->back()->with('success', 'Master biaya ujian diperbarui!');
    }

    public function feeDestroy($id)
    {
        ExamFee::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Konfigurasi biaya dihapus.');
    }
}