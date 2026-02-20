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
    public function index()
    {
        $user = auth()->user();
        $query = Dojo::with(['city.province']);

        // 1. Filter data berdasarkan role
        if ($user->role === 'pengprov') {
            $query->whereHas('city', function ($q) use ($user) {
                $q->where('province_id', $user->province_id);
            });
        } elseif ($user->role === 'pengcab') {
            $query->where('city_id', $user->city_id);
        }

        // 2. Clone query untuk menghitung Warning SK (agar filter role tetap berlaku)
        $warningQuery = clone $query;
        $warningDojos = $warningQuery->where('sk_expiry_date', '>', now())
            ->where('sk_expiry_date', '<=', now()->addDays(30))
            ->get();

        $dojos = $query->latest()->paginate(10);

        return view('admin.dojos.index', compact('dojos', 'warningDojos'));
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