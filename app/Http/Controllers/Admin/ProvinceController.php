<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Province;
use App\Models\Official;
use App\Models\Dojo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProvinceController extends Controller
{
    public function index()
    {
        $provinces = Province::withCount([
            // Menghitung jumlah Dojo yang ada di setiap Provinsi melalui tabel Cities
            'cities as dojos_count' => function ($query) {
                $query->join('dojos', 'cities.id', '=', 'dojos.city_id');
            },
            // Menghitung jumlah Kota (Pengcab) yang sudah memiliki data pengurus
            'cities as pengcab_count' => function ($query) {
                $query->whereIn('id', function ($subQuery) {
                    $subQuery->select('city_id')
                        ->from('officials')
                        ->where('level', 'pengcab')
                        ->whereNotNull('city_id');
                });
            }
        ])->orderBy('name')->get();

        return view('admin.provinces.index', compact('provinces'));
    }

    /**
     * Menampilkan detail satu provinsi (Dashboard Wilayah)
     */
    public function show($id)
    {
        // 1. Ambil data provinsi beserta kota-kotanya
        $province = Province::with('cities')->findOrFail($id);

        // 2. Ambil pengurus level PROVINSI (Pengprov)
        $officials = Official::where('province_id', $id)
            ->where('level', 'provinsi')
            ->orderBy('position')
            ->get();

        // 3. Ambil semua Dojo di provinsi ini
        $dojos = Dojo::whereHas('city', function ($query) use ($id) {
            $query->where('province_id', $id);
        })->with('city')->latest()->get();

        // 4. Ambil semua Pengurus Cabang (Pengcab) yang ada di provinsi ini
        // Kita kelompokkan berdasarkan city_id agar bisa ditampilkan per Kota/Kabupaten
        $pengcabs = Official::where('level', 'pengcab')
            ->whereHas('city', function ($query) use ($id) {
                $query->where('province_id', $id);
            })
            ->with('city')
            ->get()
            ->groupBy('city_id');

        // Satukan semua data dalam satu return view
        return view('admin.provinces.show', compact('province', 'officials', 'dojos', 'pengcabs'));
    }
}