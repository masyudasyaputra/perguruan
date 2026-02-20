<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dojo;
use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DojoController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Load relasi wilayah dan hitung member dari tabel users
        $query = Dojo::with(['city.province'])->withCount('members');

        // --- 1. PROTEKSI ROLE & WILAYAH (WAJIB) ---
        // Ini memastikan data di luar wilayah akun login tidak akan pernah terbuka
        if ($user->role === 'pengprov') {
            $query->whereHas('city', function ($q) use ($user) {
                $q->where('province_id', $user->province_id);
            });
        } elseif ($user->role === 'pengcab') {
            $query->where('city_id', $user->city_id);
        }

        // --- 2. LOGIKA FILTER (Berjalan di dalam batasan Role) ---
        // Filter Search Nama Dojo
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter Kota (Hanya untuk Admin & Pengprov)
        if ($request->filled('city_id') && $user->role !== 'pengcab') {
            $query->where('city_id', $request->city_id);
        }

        // Filter Provinsi (Hanya untuk Admin Pusat)
        if ($request->filled('province_id') && $user->role === 'admin') {
            $query->whereHas('city', function ($q) use ($request) {
                $q->where('province_id', $request->province_id);
            });
        }

        // --- 3. PENGAMBILAN DATA DROPDOWN FILTER ---
        // Kita batasi pilihan dropdown agar sesuai wilayah login
        if ($user->role === 'admin') {
            $provinces = Province::all();
            $cities = $request->filled('province_id')
                ? City::where('province_id', $request->province_id)->get()
                : collect();
        } elseif ($user->role === 'pengprov') {
            $provinces = Province::where('id', $user->province_id)->get();
            $cities = City::where('province_id', $user->province_id)->get();
        } else {
            $provinces = collect();
            $cities = collect();
        }

        // --- 4. EKSEKUSI DATA ---
        $warningQuery = clone $query;
        $warningDojos = $warningQuery->where('sk_expiry_date', '>', now())
            ->where('sk_expiry_date', '<=', now()->addDays(30))
            ->get();

        $dojos = $query->latest()->paginate(10)->withQueryString();

        return view('admin.dojos.index', compact('dojos', 'warningDojos', 'provinces', 'cities'));
    }

    public function create()
    {
        $provinces = Province::all();
        return view('admin.dojos.create', compact('provinces'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'required|string',
                'sensei_name' => 'nullable|string',
                'phone_number' => 'nullable|string',
                'sk_number' => 'nullable|string',
                'sk_expiry_date' => 'required|date',
                'province_id' => 'required_if:role,admin,pengprov',
                'city_id' => 'required_if:role,admin,pengprov',
            ]);

            // Tambahkan data wilayah otomatis jika pengcab
            if (auth()->user()->role === 'pengcab') {
                $validated['province_id'] = auth()->user()->province_id;
                $validated['city_id'] = auth()->user()->city_id;
            }

            // Tentukan status aktif otomatis
            $validated['is_active'] = Carbon::parse($request->sk_expiry_date)->isFuture();

            Dojo::create($validated);

            return redirect()->route('admin.dojos.index')
                ->with('success', 'Dojo berhasil disimpan!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function edit(Dojo $dojo)
    {
        // Proteksi akses: Jika pengcab, tidak boleh edit dojo luar kota dia
        if (auth()->user()->role === 'pengcab' && $dojo->city_id !== auth()->user()->city_id) {
            abort(403, 'Anda tidak memiliki akses ke data dojo ini.');
        }

        $provinces = Province::all();
        $cities = City::where('province_id', $dojo->city->province_id)->get();

        return view('admin.dojos.edit', compact('dojo', 'provinces', 'cities'));
    }

    public function update(Request $request, Dojo $dojo)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'sensei_name' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'sk_number' => 'nullable|string',
            'sk_expiry_date' => 'required|date',
            'province_id' => 'required',
            'city_id' => 'required',
        ]);

        // OTOMATIS: Update status aktif berdasarkan tanggal baru
        $validated['is_active'] = Carbon::parse($request->sk_expiry_date)->isFuture();

        $dojo->update($validated);

        return redirect()->route('admin.dojos.index')
            ->with('success', 'Data Dojo berhasil diperbarui!');
    }

    public function destroy(Dojo $dojo)
    {
        try {
            $name = $dojo->name;
            $dojo->delete();

            return redirect()
                ->route('admin.dojos.index')
                ->with('success', "Dojo {$name} berhasil dihapus dari sistem.");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.dojos.index')
                ->with('error', "Gagal menghapus data. Dojo mungkin masih memiliki data anggota.");
        }
    }
}