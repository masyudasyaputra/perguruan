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

        // 1. Gabungkan role utama dan roles tambahan (asumsi roles adalah array/json)
        $primaryRole = $user->role;
        $additionalRoles = is_array($user->roles) ? $user->roles : json_decode($user->roles, true) ?? [];
        
        // Satukan semua role dalam satu array unik
        $allRoles = array_unique(array_merge([$primaryRole], $additionalRoles));

        // 2. Inisialisasi Query Dasar (Hanya mengambil user dengan role member)
        $memberQuery = User::where('role', 'member');

        /**
         * 3. Logika Scope Hierarki Role
         * Kita gunakan IF-ELSEIF agar role dengan level tertinggi (PB) 
         * didahulukan meskipun user punya banyak role lainnya.
         */
        if (in_array('pb', $allRoles)) {
            $role = 'pb';
            $title = "Dashboard Pengurus Besar (Nasional)";
            // Tanpa filter wilayah (akses penuh)
        } 
        elseif (count(array_intersect(['pengprov', 'admin_pengprov'], $allRoles)) > 0) {
            $role = 'pengprov';
            $title = "Dashboard Pengurus Provinsi";
            $memberQuery->where('province_id', $user->province_id);
        } 
        elseif (count(array_intersect(['pengcab', 'admin_pengcab'], $allRoles)) > 0) {
            $role = 'pengcab';
            $title = "Dashboard Pengurus Cabang";
            $memberQuery->where('city_id', $user->city_id);
        } 
        elseif (in_array('admin_dojo', $allRoles)) {
            $role = 'admin_dojo';
            $title = "Dashboard Dojo";
            $memberQuery->where('dojo_id', $user->dojo_id);
        } 
        else {
            $role = 'member';
            $title = "Dashboard Member";
            $memberQuery->where('id', $user->id);
        }

        // 4. Logika Filter (Search, Wilayah, Dojo, & Status)
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

        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $memberQuery->where('is_active', $request->status);
        }

        // 5. Hitung Statistik (Menggunakan subquery select agar lebih cepat daripada pluck)
        $stats = [
            'total_members' => (clone $memberQuery)->count(),
            'active_members' => (clone $memberQuery)->where('is_active', true)->count(),
            'total_revenue' => Payment::whereIn('user_id', (clone $memberQuery)->select('id'))
                ->where('status', 'paid')
                ->sum('amount'),
            'pending_payments' => Payment::whereIn('user_id', (clone $memberQuery)->select('id'))
                ->where('status', 'pending')
                ->count(),
        ];

        // 6. Data Dropdown untuk Form Filter
        $provinces = ($role === 'pb') ? Province::all() : Province::where('id', $user->province_id)->get();
        
        $cities = City::query()
            ->when(in_array($role, ['pengprov']), function($q) use ($user) {
                return $q->where('province_id', $user->province_id);
            })
            ->when($role === 'pb' && $request->filled('province_id'), function($q) use ($request) {
                return $q->where('province_id', $request->province_id);
            })->get();

        $dojos = Dojo::query()
            ->when(in_array($role, ['pengprov']), function($q) use ($user) {
                return $q->whereHas('city', fn($c) => $c->where('province_id', $user->province_id));
            })
            ->when(in_array($role, ['pengcab']), function($q) use ($user) {
                return $q->where('city_id', $user->city_id);
            })->get();

        // 7. Ambil Data Akhir
        $members = $memberQuery->with(['province', 'city', 'dojo'])
            ->latest()
            ->paginate(10);

        return view('admin.dashboard', compact('stats', 'members', 'title', 'role', 'cities', 'provinces', 'dojos'));
    }
}