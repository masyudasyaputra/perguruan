<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode; // Pastikan ini ada

class MemberDashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil data user beserta relasinya
        $user = Auth::user()->load(['beltLevel', 'province', 'city', 'dojo']);

        // 2. Ambil data tagihan yang belum lunas
        $unpaidBill = \App\Models\Payment::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        // 3. Generate QR Code (Gunakan format SVG agar tidak butuh ekstensi Imagick)
        $qrCode = QrCode::format('svg')
            ->size(150)
            ->errorCorrection('H')
            ->generate($user->id); // Bisa diganti dengan URL atau Nomor Anggota
        
        $qrPngBase64 = base64_encode($qrCode);

        // 4. Kirim SEMUA variabel ke view
        return view('member.dashboard', compact('user', 'unpaidBill', 'qrPngBase64'));
    }
}