<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Official; // Pastikan ini ada
use App\Models\Province; // Tambahkan jika perlu untuk create form
use Illuminate\Http\Request;

class OfficialController extends Controller
{
    /**
     * Menampilkan daftar semua pengurus
     */
    public function index()
    {
        // Ambil semua data untuk tabel utama
        $officials = Official::with(['province', 'city'])->latest()->get();

        // Data kategori (Pastikan 'level' sesuai dengan input di form: 'provinsi' & 'pengcab')
        $provincialOfficials = Official::where('level', 'provinsi')->with('province')->get();
        $cityOfficials = Official::where('level', 'pengcab')->with('city.province')->get();

        return view('admin.officials.index', compact('officials', 'provincialOfficials', 'cityOfficials'));
    }

    /**
     * Menampilkan form tambah (Opsional jika Anda butuh data provinsi)
     */
    public function create()
    {
        $provinces = Province::orderBy('name')->get();
        return view('admin.officials.create', compact('provinces'));
    }

    /**
     * Menyimpan data pengurus baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|in:provinsi,pengcab', // Sesuaikan dengan value radio button di form
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required_if:level,pengcab|nullable|exists:cities,id',
            'position' => 'required|string',
            'phone_number' => 'nullable|string|max:20',
            'sk_number' => 'nullable|string|max:100',
            'sk_expiry_date' => 'required|date',
        ]);

        Official::create($request->all());

        return redirect()->route('admin.officials.index')
            ->with('success', 'Data Pengurus berhasil ditambahkan!');
    }
}