<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Exam;
use App\Models\Participant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExamController extends Controller
{
    /**
     * Menampilkan detail ujian dan daftar peserta.
     */
    public function show(Exam $exam): View
    {
        $user = Auth::user();

        // 1. Ambil Peserta dengan Eager Loading
        $participants = $exam->participants()
            ->with(['user', 'targetBelt', 'currentBelt', 'dojo'])
            ->when($user->role === 'admin_dojo', function ($query) use ($user) {
                return $query->where('dojo_id', $user->dojo_id);
            })
            ->get();

        // 2. Ambil Member yang tersedia untuk didaftarkan
        $members = User::where('role', 'member')
            ->when($user->role === 'admin_dojo', function ($query) use ($user) {
                return $query->where('dojo_id', $user->dojo_id);
            })
            ->get();

        return view('admin.exams.show', compact('exam', 'participants', 'members'));
    }

    /**
     * Mendaftarkan member ke sesi ujian.
     */
    public function registerMember(Request $request, Exam $exam): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $auth = Auth::user();
        $member = User::findOrFail($request->user_id);

        // Security Check: Admin Dojo dilarang mendaftarkan member dari dojo lain
        if ($auth->role === 'admin_dojo' && $member->dojo_id !== $auth->dojo_id) {
            return back()->with('error', 'Otoritas tidak valid untuk mendaftarkan member ini.');
        }

        // Cek apakah member sudah terdaftar di ujian ini
        $isAlreadyRegistered = $exam->participants()
            ->where('user_id', $member->id)
            ->exists();

        if ($isAlreadyRegistered) {
            return back()->with('error', 'Peserta ini sudah terdaftar dalam sesi ujian ini.');
        }

        // Eksekusi Pendaftaran
        Participant::create([
            'exam_id'         => $exam->id,
            'user_id'         => $member->id,
            'dojo_id'         => $member->dojo_id,
            'current_belt_id' => $member->belt_level_id,
            'target_belt_id'  => $member->belt_level_id + 1,
            'payment_status'  => 'pending',
            'fee_amount'      => 150000, 
        ]);

        return back()->with('success', "{$member->name} berhasil ditambahkan ke daftar ujian.");
    }

    /**
     * Menghapus peserta dari daftar ujian.
     */
    public function removeMember(Participant $participant): RedirectResponse
    {
        $auth = Auth::user();

        // Security Check: Cegah Admin Dojo menghapus peserta dari dojo lain
        if ($auth->role === 'admin_dojo' && $participant->dojo_id !== $auth->dojo_id) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus peserta ini.');
        }

        $participant->delete();

        return back()->with('success', 'Peserta berhasil dihapus dari daftar.');
    }
}