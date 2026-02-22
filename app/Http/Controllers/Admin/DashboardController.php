<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payment;
use App\Models\Province;
use App\Models\City;
use App\Models\Dojo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;

        // 1. Inisialisasi Query Dasar (Hanya mengambil user dengan role member)
        $memberQuery = User::where('role', 'member');

        // 2. Logika Scope Hierarki Role (Membatasi data apa yang boleh dilihat)
        switch ($role) {
            case 'pb':
                $title = "Dashboard Pengurus Besar (Nasional)";
                break;

            case 'pengprov':
            case 'admin_pengprov':
                $title = "Dashboard Pengurus Provinsi";
                $memberQuery->where('province_id', $user->province_id);
                break;

            case 'pengcab':
            case 'admin_pengcab':
                $title = "Dashboard Pengurus Cabang";
                $memberQuery->where('city_id', $user->city_id);
                break;

            case 'admin_dojo':
                $title = "Dashboard Dojo";
                $memberQuery->where('dojo_id', $user->dojo_id);
                break;

            default:
                $title = "Dashboard Member";
                $memberQuery->where('id', $user->id);
                break;
        }

        // 3. Logika Filter (Search, Wilayah, Dojo, & Status)
        if ($request->filled('search')) {
            $memberQuery->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('id', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('province_id')) {
            $memberQuery->where('province_id', $request->province_id);
        }

        if ($request->filled('city_id')) {
            $memberQuery->where('city_id', $request->city_id);
        }

        if ($request->filled('dojo_id')) {
            $memberQuery->where('dojo_id', $request->dojo_id);
        }

        // Filter Status Aktif/Non-Aktif
        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $memberQuery->where('is_active', $request->status);
        }

        // 4. Hitung Statistik (Berdasarkan cakupan data setelah di-filter)
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

        // 5. Data Dropdown untuk Form Filter
        $provinces = ($role === 'pb') ? Province::all() : [];
        
        $cities = City::query()
            ->when(in_array($role, ['pengprov', 'admin_pengprov']), function($q) use ($user) {
                return $q->where('province_id', $user->province_id);
            })
            ->when($role === 'pb' && $request->filled('province_id'), function($q) use ($request) {
                return $q->where('province_id', $request->province_id);
            })->get();

        $dojos = Dojo::query()
            ->when(in_array($role, ['pengprov', 'admin_pengprov']), function($q) use ($user) {
                return $q->whereHas('city', fn($c) => $c->where('province_id', $user->province_id));
            })
            ->when(in_array($role, ['pengcab', 'admin_pengcab']), function($q) use ($user) {
                return $q->where('city_id', $user->city_id);
            })->get();

        // 6. Ambil Data Akhir dengan Relasi
        $members = $memberQuery->with(['beltLevel', 'province', 'city', 'dojo'])
            ->latest()
            ->paginate(10);

        return view('admin.dashboard', compact('stats', 'members', 'title', 'role', 'cities', 'provinces', 'dojos'));
    }
}