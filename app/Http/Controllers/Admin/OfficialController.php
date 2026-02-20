<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Official;
use App\Models\Province;
use App\Models\City; // Tambahkan ini agar bisa pilih kota saat edit
use Illuminate\Http\Request;

class OfficialController extends Controller
{
    public function index(Request $request) // Tambahkan Request agar pencarian tetap jalan
    {
        $user = auth()->user();

        // 1. Inisialisasi query dengan eager loading yang kamu butuhkan
        $query = Official::with(['province', 'city', 'city.dojos']);

        // 2. PROTEKSI WILAYAH: Hanya tampilkan data di bawah Pengprov yang sedang login
        if ($user->role === 'pengprov') {
            $query->where('province_id', $user->province_id);
        }
        // Jika nanti ada role pengcab, ini proteksi tambahannya (opsional)
        elseif ($user->role === 'pengcab') {
            $query->where('city_id', $user->city_id);
        }

        // 3. FITUR PENCARIAN: Jangan sampai hilang agar user tidak bingung
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 4. Eksekusi query (Gunakan paginate jika data mulai banyak, atau get() sesuai kode kamu)
        $officials = $query->latest()->get();

        return view('admin.officials.index', compact('officials'));
    }

    public function create()
    {
        $provinces = Province::orderBy('name')->get();
        return view('admin.officials.create', compact('provinces'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|in:provinsi,pengcab',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required_if:level,pengcab|nullable|exists:cities,id',
            'position' => 'required|string',
            'phone_number' => 'nullable|string|max:20',
            'sk_number' => 'nullable|string|max:100',
            'sk_expiry_date' => 'required|date',
        ]);

        Official::create($validated);

        return redirect()->route('admin.officials.index')
            ->with('success', 'Data Pengurus berhasil ditambahkan!');
    }

    /**
     * INI FUNGSI YANG HILANG (Penyebab Tombol Edit Error)
     */
    public function edit(Official $official)
    {
        $provinces = Province::orderBy('name')->get();
        // Ambil data kota berdasarkan provinsi yang sedang dipilih si pengurus
        $cities = City::where('province_id', $official->province_id)->get();

        return view('admin.officials.edit', compact('official', 'provinces', 'cities'));
    }

    /**
     * FUNGSI UNTUK MENYIMPAN PERUBAHAN
     */
    public function update(Request $request, Official $official)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|in:provinsi,pengcab',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required_if:level,pengcab|nullable|exists:cities,id',
            'position' => 'required|string',
            'phone_number' => 'nullable|string|max:20',
            'sk_number' => 'nullable|string|max:100',
            'sk_expiry_date' => 'required|date',
        ]);

        $official->update($validated);

        return redirect()->route('admin.officials.index')
            ->with('success', 'Data Pengurus berhasil diperbarui!');
    }

    /**
     * FUNGSI HAPUS
     */
    public function destroy(Official $official)
    {
        $official->delete();
        return redirect()->route('admin.officials.index')
            ->with('success', 'Data Pengurus berhasil dihapus!');
    }
}