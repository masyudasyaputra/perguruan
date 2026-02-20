<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Province;
use App\Models\Official;
use App\Models\Dojo;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    // List statis agar tidak menulis ulang di create & edit
    protected $allProvinces = [
        'ACEH',
        'SUMATERA UTARA',
        'SUMATERA BARAT',
        'RIAU',
        'JAMBI',
        'SUMATERA SELATAN',
        'BENGKULU',
        'LAMPUNG',
        'KEPULAUAN BANGKA BELITUNG',
        'KEPULAUAN RIAU',
        'DKI JAKARTA',
        'JAWA BARAT',
        'JAWA TENGAH',
        'DI YOGYAKARTA',
        'JAWA TIMUR',
        'BANTEN',
        'BALI',
        'NUSA TENGGARA BARAT',
        'NUSA TENGGARA TIMUR',
        'KALIMANTAN BARAT',
        'KALIMANTAN TENGAH',
        'KALIMANTAN SELATAN',
        'KALIMANTAN TIMUR',
        'KALIMANTAN UTARA',
        'SULAWESI UTARA',
        'SULAWESI TENGAH',
        'SULAWESI SELATAN',
        'SULAWESI TENGGARA',
        'GORONTALO',
        'SULAWESI BARAT',
        'MALUKU',
        'MALUKU UTARA',
        'PAPUA BARAT',
        'PAPUA'
    ];

    public function index()
    {
        if (auth()->user()->role === 'pengprov') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        $provinces = Province::withCount([
            // Menghitung jumlah Dojo melalui Cities
            'dojos',

            // Menghitung jumlah Pengcab berdasarkan tabel officials
            // Gunakan DISTINCT pada city_id agar jika dalam 1 kota ada banyak pengurus, tetap dihitung 1 Pengcab
            'officials as pengcab_count' => function ($query) {
                $query->where('level', 'pengcab')
                    ->select(\DB::raw('count(distinct(city_id))'));
            }
        ])->orderBy('name')->get();

        return view('admin.provinces.index', compact('provinces'));
    }

    public function create()
    {
        $existingProvinces = Province::pluck('name')->toArray();
        $availableProvinces = array_diff($this->allProvinces, $existingProvinces);

        return view('admin.provinces.create', compact('availableProvinces'));
    }

    public function edit($id)
    {
        $province = Province::findOrFail($id);
        $existing = Province::where('id', '!=', $id)->pluck('name')->toArray();
        $availableProvinces = array_diff($this->allProvinces, $existing);

        return view('admin.provinces.edit', compact('province', 'availableProvinces'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:provinces,name',
            'leader_name' => 'required|string',
            'sk_number' => 'nullable',
            'sk_expiry_date' => 'required|date',
        ], [
            'name.unique' => 'Provinsi tersebut sudah terdaftar sebelumnya.'
        ]);

        Province::create($validated);
        return redirect()->route('admin.provinces.index')->with('success', 'Data berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $province = Province::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|unique:provinces,name,' . $id,
            'leader_name' => 'required|string',
            'sk_number' => 'nullable',
            'sk_expiry_date' => 'required|date',
        ]);

        $province->update($validated);
        return redirect()->route('admin.provinces.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function show($id)
    {
        $province = Province::with('cities')->findOrFail($id);

        $officials = Official::where('province_id', $id)
            ->where('level', 'provinsi')
            ->orderBy('position')
            ->get();

        $dojos = Dojo::whereHas('city', function ($query) use ($id) {
            $query->where('province_id', $id);
        })->with('city')->latest()->get();

        // Mengambil data Pengcab (Officials level pengcab) dan dikelompokkan per Kota
        $pengcabs = Official::where('level', 'pengcab')
            ->where('province_id', $id) // Lebih efisien langsung via province_id jika ada di tabel officials
            ->with('city')
            ->get()
            ->groupBy('city_id');

        return view('admin.provinces.show', compact('province', 'officials', 'dojos', 'pengcabs'));
    }

    public function destroy($id)
    {
        $province = Province::findOrFail($id);
        try {
            $province->delete();
            return redirect()->route('admin.provinces.index')->with('success', 'Provinsi berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.provinces.index')->with('error', 'Gagal menghapus data karena masih ada data terkait.');
        }
    }
}