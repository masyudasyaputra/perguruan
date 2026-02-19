<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        // 1. Inisialisasi Query Dasar untuk Member
        $memberQuery = User::where('role', 'member');

        // 2. Logika Filter Berdasarkan Hierarki
        switch ($role) {
            case 'pb':
                $title = "Dashboard Pengurus Besar (Nasional)";
                // Tidak perlu filter, PB bisa lihat semua
                break;

            case 'pengprov':
                $title = "Dashboard Pengurus Provinsi";
                $memberQuery->where('province_id', $user->province_id);
                break;

            case 'pengcab':
                $title = "Dashboard Pengurus Cabang";
                $memberQuery->where('city_id', $user->city_id);
                break;

            case 'admin_dojo':
                $title = "Dashboard Dojo";
                $memberQuery->where('dojo_id', $user->dojo_id);
                break;

            default:
                // Jika member kesasar ke sini, batasi hanya data dirinya sendiri
                $title = "Dashboard Member";
                $memberQuery->where('id', $user->id);
                break;
        }

        // 3. Statistik Berdasarkan Scope (Cakupan) Role
        // Kita gunakan clone agar query utama tidak terpengaruh saat menghitung total
        $stats = [
            'total_members' => (clone $memberQuery)->count(),
            'active_members' => (clone $memberQuery)->where('is_active', true)->count(),
            'total_revenue' => Payment::whereIn('user_id', (clone $memberQuery)->pluck('id'))
                ->where('status', 'paid')
                ->sum('amount'),
            'pending_payments' => Payment::whereIn('user_id', (clone $memberQuery)->pluck('id'))
                ->where('status', 'pending')
                ->count(),
        ];

        // 4. Ambil Data Member dengan Relasi Lengkap
        $members = $memberQuery->with(['beltLevel', 'province', 'city', 'dojo'])
            ->latest()
            ->paginate(10);

        return view('admin.dashboard', compact('stats', 'members', 'title', 'role'));
    }
}