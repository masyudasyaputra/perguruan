<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payment;
use App\Models\Province;
use App\Models\City;
use App\Models\Dojo;
use App\Models\BeltLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Gabungkan role utama dan roles tambahan (roles bisa array/json)
        $primaryRole = $user->role;
        $additionalRoles = is_array($user->roles) ? $user->roles : (json_decode($user->roles, true) ?? []);
        $allRoles = array_unique(array_merge([$primaryRole], $additionalRoles));

        // 2. Inisialisasi Query Dasar (Hanya mengambil user dengan role member)
        $memberQuery = User::query()->where('role', 'member');

        /**
         * 3. Logika Scope Hierarki Role
         * IF-ELSEIF agar role level tertinggi (PB) didahulukan meskipun user punya banyak role.
         * (Scope ini HARUS tetap aman: filter request tidak boleh "melebar" melebihi scope role.)
         */
        if (in_array('pb', $allRoles, true)) {
            $role = 'pb';
            $title = 'Dashboard Pengurus Besar (Nasional)';
            // Akses penuh (tanpa scope wilayah)
        } elseif (count(array_intersect(['pengprov', 'admin_pengprov'], $allRoles)) > 0) {
            $role = 'pengprov';
            $title = 'Dashboard Pengurus Provinsi';
            $memberQuery->where('province_id', $user->province_id);
        } elseif (count(array_intersect(['pengcab', 'admin_pengcab'], $allRoles)) > 0) {
            $role = 'pengcab';
            $title = 'Dashboard Pengurus Cabang';
            $memberQuery->where('city_id', $user->city_id);
        } elseif (in_array('admin_dojo', $allRoles, true)) {
            $role = 'admin_dojo';
            $title = 'Dashboard Dojo';
            $memberQuery->where('dojo_id', $user->dojo_id);
        } else {
            $role = 'member';
            $title = 'Dashboard Member';
            $memberQuery->where('id', $user->id);
        }

        /**
         * 4. Logika Filter (Search, Wilayah, Dojo, Sabuk & Status)
         * Catatan penting:
         * - Filter harus tetap tunduk pada scope role yang sudah diterapkan di atas.
         * - Untuk role non-PB, kita batasi "akses filter wilayah" agar tidak bisa melebar.
         */

        // Search (selalu boleh)
        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $memberQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }

        // Filter Sabuk (INI yang sebelumnya belum ada)
        if ($request->filled('belt_level_id')) {
            $memberQuery->where('belt_level_id', $request->belt_level_id);
        }

        // Filter Status (selalu boleh)
        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $memberQuery->where('is_active', (int) $request->status);
        }

        // Filter wilayah/dojo: dibatasi berdasarkan role
        if ($role === 'pb') {
            // PB boleh bebas filter province/city/dojo
            if ($request->filled('province_id')) {
                $memberQuery->where('province_id', $request->province_id);
            }
            if ($request->filled('city_id')) {
                $memberQuery->where('city_id', $request->city_id);
            }
            if ($request->filled('dojo_id')) {
                $memberQuery->where('dojo_id', $request->dojo_id);
            }
        } elseif ($role === 'pengprov') {
            // Pengprov hanya boleh filter city/dojo dalam provinsi dia
            if ($request->filled('city_id')) {
                $memberQuery->where('city_id', $request->city_id);
            }
            if ($request->filled('dojo_id')) {
                $memberQuery->where('dojo_id', $request->dojo_id);
            }
            // province_id tidak dipakai karena sudah di-scope ke $user->province_id
        } elseif ($role === 'pengcab') {
            // Pengcab hanya boleh filter dojo dalam city dia
            if ($request->filled('dojo_id')) {
                $memberQuery->where('dojo_id', $request->dojo_id);
            }
            // province_id/city_id tidak dipakai karena sudah di-scope ke $user->city_id
        } elseif ($role === 'admin_dojo') {
            // Admin dojo tidak perlu filter wilayah; sudah fixed dojo_id
            // (kalau tetap dikirim via querystring, biarkan saja—tidak akan melebar karena sudah ada where dojo_id = user dojo)
        } else {
            // member: sudah fixed id
        }

        // 5. Hitung Statistik (gunakan clone agar tidak mengganggu query utama)
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

        /**
         * 6. Data Dropdown untuk Form Filter
         * Tetap mengikuti scope akses role.
         */
        $beltLevels = BeltLevel::orderBy('order')->get();

        $provinces = ($role === 'pb')
            ? Province::orderBy('name')->get()
            : Province::where('id', $user->province_id)->orderBy('name')->get();

        $cities = City::query()
            ->when($role === 'pengprov', function ($q) use ($user) {
                return $q->where('province_id', $user->province_id);
            })
            ->when($role === 'pb' && $request->filled('province_id'), function ($q) use ($request) {
                return $q->where('province_id', $request->province_id);
            })
            ->when($role === 'pb' && !$request->filled('province_id'), function ($q) {
                // PB: opsional, bisa tampilkan semua kota, tapi biasanya berat.
                // Jika mau semua kota untuk PB, hapus blok ini.
                return $q->limit(0);
            })
            ->orderBy('name')
            ->get();

        $dojos = Dojo::query()
            ->when($role === 'pengprov', function ($q) use ($user) {
                return $q->whereHas('city', fn($c) => $c->where('province_id', $user->province_id));
            })
            ->when($role === 'pengcab', function ($q) use ($user) {
                return $q->where('city_id', $user->city_id);
            })
            ->when($role === 'pb' && $request->filled('city_id'), function ($q) use ($request) {
                return $q->where('city_id', $request->city_id);
            })
            ->when($role === 'pb' && !$request->filled('city_id'), function ($q) {
                // PB: opsional, sama seperti cities, biar tidak berat kalau datanya besar.
                return $q->limit(0);
            })
            ->orderBy('name')
            ->get();

        // 7. Ambil Data Akhir
        $members = $memberQuery
            ->with(['province', 'city', 'dojo', 'beltLevel'])
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        return view('admin.dashboard', compact(
            'stats',
            'members',
            'title',
            'role',
            'cities',
            'provinces',
            'dojos',
            'beltLevels'
        ));
    }
}