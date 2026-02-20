<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Province;
use App\Models\City;
use App\Models\Dojo;
use App\Models\BeltLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; // Ditambahkan agar Rule::unique bekerja

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = User::with(['province', 'city', 'dojo']);

        // --- FILTER KEAMANAN ROLE ---
        if ($user->role === 'pengprov') {
            $query->where('province_id', $user->province_id);
        } elseif ($user->role === 'pengcab') {
            $query->where('city_id', $user->city_id);
        }

        // --- FILTER PENCARIAN (SEARCH) ---
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // --- FILTER ROLE ---
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // --- FILTER WILAYAH (PROVINSI) ---
        if ($request->filled('province_id')) {
            $query->where('province_id', $request->province_id);
        }

        $users = $query->latest()->paginate(10)->appends($request->query());

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $admin = auth()->user();
        $provinces = Province::all();
        $beltLevels = BeltLevel::all();

        $availableRoles = [];
        if ($admin->role === 'pb') {
            $availableRoles = [
                'pengprov' => 'Admin Provinsi (Pengprov)',
                'pengcab' => 'Admin Kota/Kab (Pengcab)',
                'admin_dojo' => 'Admin Dojo (Sensei)',
            ];
        } elseif ($admin->role === 'pengprov') {
            $availableRoles = [
                'pengcab' => 'Admin Kota/Kab (Pengcab)',
                'admin_dojo' => 'Admin Dojo (Sensei)',
            ];
        }

        return view('admin.users.create_admin', compact('provinces', 'beltLevels', 'availableRoles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required',
            'province_id' => 'required_unless:role,pb', // PB tidak wajib provinsi
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'province_id' => $request->province_id,
            'city_id' => $request->city_id,
            'dojo_id' => $request->dojo_id,
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Admin berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $provinces = Province::orderBy('name')->get();

        // Ambil kota berdasarkan provinsi user agar dropdown kota tidak kosong saat edit
        $cities = City::where('province_id', $user->province_id)->orderBy('name')->get();

        // Ambil dojo berdasarkan provinsi/kota user
        $dojos = Dojo::where('province_id', $user->province_id)->orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'provinces', 'cities', 'dojos'));
    }

    public function update(Request $request, User $user)
    {
        // Validasi input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string'],
            'is_active' => ['required', 'boolean'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'dojo_id' => ['nullable', 'exists:dojos,id'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        // Data dasar
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'is_active' => $request->is_active,
            'province_id' => $request->province_id,
        ];

        // Logika pembersihan Wilayah berdasarkan Role agar data konsisten
        if ($request->role === 'pb') {
            $data['province_id'] = null;
            $data['city_id'] = null;
            $data['dojo_id'] = null;
        } elseif ($request->role === 'pengprov') {
            $data['city_id'] = null;
            $data['dojo_id'] = null;
        } elseif ($request->role === 'pengcab') {
            $data['city_id'] = $request->city_id;
            $data['dojo_id'] = null;
        } elseif (in_array($request->role, ['admin_dojo', 'member'])) {
            // Member/Admin Dojo biasanya butuh City ID juga sebagai hirarki
            $data['city_id'] = $request->city_id;
            $data['dojo_id'] = $request->dojo_id;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Data user ' . $user->name . ' berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}