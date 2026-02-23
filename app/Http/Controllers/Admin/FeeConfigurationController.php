<?php

namespace App\Http\Controllers\Admin; // Namespace disesuaikan dengan folder Admin

use App\Http\Controllers\Controller; // Wajib diimport karena berada di sub-folder
use App\Models\BeltLevel;
use App\Models\FeeConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeeConfigurationController extends Controller
{
    /**
     * Menampilkan halaman konfigurasi biaya.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Ambil semua tingkatan sabuk urut berdasarkan level terendah
        $beltLevels = BeltLevel::orderBy('order', 'asc')->get();
        
        // Ambil konfigurasi yang sudah ada untuk wilayah user saat ini
        // Jika PB, province_id akan null. Jika Pengprov, akan berisi ID Provinsinya.
        $fees = FeeConfiguration::where('province_id', $user->province_id)
                ->get()
                ->keyBy('belt_level_id');

        return view('admin.fees.index', compact('beltLevels', 'fees'));
    }

    /**
     * Menyimpan atau memperbarui konfigurasi biaya.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Validasi input agar hanya menerima angka
        $request->validate([
            'amounts.*' => 'required|numeric|min:0',
        ]);

        try {
            foreach ($request->amounts as $beltId => $amount) {
                // Gunakan updateOrCreate agar data yang sudah ada diperbarui, 
                // dan data yang belum ada dibuat baru.
                FeeConfiguration::updateOrCreate(
                    [
                        'province_id' => $user->province_id,
                        'belt_level_id' => $beltId,
                    ],
                    [
                        'amount' => $amount,
                    ]
                );
            }

            // Pesan sukses menyesuaikan apakah dia PB atau Pengprov
            $wilayah = $user->province ? $user->province->name : 'Nasional';
            
            return back()->with('success', "Konfigurasi biaya berhasil diperbarui untuk wilayah $wilayah");
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}