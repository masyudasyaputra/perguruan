<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\User;
use App\Models\Dojo;             // TAMBAHKAN INI
use App\Models\City;             // TAMBAHKAN INI
use App\Models\BeltLevel;
use App\Models\ExamParticipant;
use App\Models\FeeConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Exam::with(['province', 'participants']);

        // Filter berdasarkan kolom role di database
        if ($user->role === 'pengprov') {
            $query->where(function($q) use ($user) {
                $q->where('province_id', $user->province_id)->orWhereNull('province_id');
            });
        }

        // FIX: Gunakan execution_date sesuai error di gambar 1
        $exams = $query->orderBy('execution_date', 'desc')->get();
        return view('admin.exams.index', compact('exams'));
    }

// Tambahkan fungsi ini di ExamController
public function removeMember(ExamParticipant $participant)
{
    $participant->delete();
    return back()->with('success', 'Peserta berhasil dihapus dari daftar ujian.');
}

public function show(Request $request, Exam $exam)
{
    $user = Auth::user();
    
    // 1. Eager Loading dengan Filter Kompleks
    $exam->load(['participants' => function($query) use ($request) {
        // Filter Pencarian Nama
        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));
        }

        // Filter Dojo
        if ($request->filled('dojo_id')) {
            $query->where('dojo_id', $request->dojo_id);
        }

        // Filter Pengcab (Kota) melalui relasi Dojo
        if ($request->filled('city_id')) {
            $query->whereHas('dojo', fn($q) => $q->where('city_id', $request->city_id));
        }

        // Filter Status Pembayaran
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter Sabuk Target
        if ($request->filled('target_belt_id')) {
            $query->where('target_belt_id', $request->target_belt_id);
        }
    }, 'participants.user', 'participants.dojo.city', 'participants.currentBelt', 'participants.targetBelt']);

    // 2. Data untuk Dropdown Filter
    $dojos = Dojo::orderBy('name')->get();
    $cities = \App\Models\City::orderBy('name')->get(); // Sesuaikan namespace model City
    $belts = \App\Models\BeltLevel::orderBy('order')->get();
    
    // 3. Data Member untuk Form Pendaftaran (Kiri)
    $memberQuery = User::where('role', 'member');
    if ($user->role === 'admin_dojo') {
        $memberQuery->where('dojo_id', $user->dojo_id);
    } elseif ($user->role === 'pengprov') {
        $memberQuery->where('province_id', $user->province_id);
    }
    $members = $memberQuery->orderBy('name')->get();

    return view('admin.exams.show', compact('exam', 'members', 'dojos', 'cities', 'belts'));
}

    // FIX: Nama method harus 'addMember' sesuai panggilannya atau sesuaikan Route
    public function registerMember(Request $request, Exam $exam)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);

        $member = User::findOrFail($request->user_id);
        
        // Cek duplikasi
        if (ExamParticipant::where('exam_id', $exam->id)->where('user_id', $member->id)->exists()) {
            return back()->with('error', 'Member sudah terdaftar.');
        }

        $currentBelt = BeltLevel::find($member->belt_level_id);
        $targetBelt = BeltLevel::where('order', '>', ($currentBelt->order ?? 0))
                                ->orderBy('order', 'asc')->first();

        if (!$targetBelt) return back()->with('error', 'Tingkatan maksimal tercapai.');

        // Cari Biaya
        $fee = FeeConfiguration::where('province_id', $member->province_id)
                ->where('belt_level_id', $targetBelt->id)->first() 
                ?? FeeConfiguration::whereNull('province_id')
                ->where('belt_level_id', $targetBelt->id)->first();

        ExamParticipant::create([
            'exam_id' => $exam->id,
            'user_id' => $member->id,
            'dojo_id' => $member->dojo_id,
            'current_belt_id' => $currentBelt->id ?? null,
            'target_belt_id' => $targetBelt->id,
            'fee_amount' => $fee->amount ?? 0,
            'payment_status' => 'unpaid'
        ]);

        return back()->with('success', "{$member->name} berhasil didaftarkan.");
    }

    
}