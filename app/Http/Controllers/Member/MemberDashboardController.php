<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load(['beltLevel', 'province', 'city', 'dojo']);

        // Simulasi tagihan iuran (nanti akan dihubungkan ke tabel payments)
        $unpaidBill = \App\Models\Payment::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        return view('member.dashboard', compact('user', 'unpaidBill'));
    }
}