<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dojo;
use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;

class DojoController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Dojo::with(['city.province']);

        // Filter data berdasarkan role
        if ($user->role === 'pengprov') {
            $query->whereHas('city', function ($q) use ($user) {
                $q->where('province_id', $user->province_id);
            });
        } elseif ($user->role === 'pengcab') {
            $query->where('city_id', $user->city_id);
        }

        $dojos = $query->latest()->paginate(10);
        return view('admin.dojos.index', compact('dojos'));
    }

    public function create()
    {
        $provinces = Province::all();
        return view('admin.dojos.create', compact('provinces'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'address' => 'required',
            'sensei_name' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'sk_expiry_date' => 'required|date',
        ]);

        Dojo::create([
            'name' => $request->name,
            'city_id' => $request->city_id,
            'address' => $request->address,
            'sensei_name' => $request->sensei_name,
            'phone_number' => $request->phone_number,
            'is_active' => true,
        ]);

        return redirect()->route('admin.dojos.index')->with('success', 'Dojo baru berhasil didaftarkan.');
    }

    public function edit(Dojo $dojo)
    {
        $provinces = \App\Models\Province::all();
        // Kita ambil data kota berdasarkan provinsi dari dojo yang sedang diedit
        $cities = \App\Models\City::where('province_id', $dojo->city->province_id)->get();

        return view('admin.dojos.edit', compact('dojo', 'provinces', 'cities'));
    }

    public function update(Request $request, Dojo $dojo)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'address' => 'required',
            'sensei_name' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $dojo->update($request->all());

        return redirect()->route('admin.dojos.index')->with('success', 'Data Dojo berhasil diperbarui.');
    }

    // Tambahkan method edit, update, destroy sesuai kebutuhan
}