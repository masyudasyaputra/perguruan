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
use Illuminate\Validation\Rule;

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
        $provinces = Province::orderBy('name')->get();
        $beltLevels = BeltLevel::all();

        // Daftar role yang tersedia berdasarkan siapa yang login
        $availableRoles = $this->getAvailableRoles($admin);

        return view('admin.users.create_admin', compact('provinces', 'beltLevels', 'availableRoles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'roles' => 'required|array|min:1',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'nullable|exists:cities,id',
            'dojo_id' => 'nullable|exists:dojos,id',
        ]);

        // Role utama diambil dari pilihan pertama array roles
        $primaryRole = $request->roles[0];

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $primaryRole,      // String (untuk middleware/legacy)
            'roles' => $request->roles,   // Array/JSON (untuk multi-role)
            'province_id' => $request->province_id,
            'city_id' => $request->city_id,
            'dojo_id' => $request->dojo_id,
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Admin berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $admin = auth()->user();
        $provinces = Province::orderBy('name')->get();

        // Diperbaiki: Load data wilayah agar dropdown terisi saat edit dibuka
        $cities = $user->province_id ? City::where('province_id', $user->province_id)->orderBy('name')->get() : collect();
        $dojos = $user->city_id ? Dojo::where('city_id', $user->city_id)->orderBy('name')->get() : collect();

        $availableRoles = $this->getAvailableRoles($admin);

        if (is_null($user->roles)) {
            $user->roles = [$user->role];
        }

        return view('admin.users.edit', compact('user', 'provinces', 'cities', 'dojos', 'availableRoles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'roles' => ['required', 'array', 'min:1'],
            'is_active' => ['required', 'boolean'],
            'province_id' => ['required', 'exists:provinces,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'dojo_id' => ['nullable', 'exists:dojos,id'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $allRoles = $request->roles;
        $primaryRole = $allRoles[0];

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $primaryRole,
            'roles' => $allRoles,
            'is_active' => $request->is_active,
            'province_id' => $request->province_id,
            'city_id' => $request->city_id,
            'dojo_id' => $request->dojo_id, // Default ambil dari request
        ];

        // --- Logika Sanitasi Wilayah yang Diperbaiki ---
        // Kita cek apakah user memiliki role yang membutuhkan Dojo. 
        // Jika TIDAK ADA role dojo/penguji dalam array roles, baru kita set null.
        $needsDojo = count(array_intersect(['admin_dojo', 'penguji', 'member'], $allRoles)) > 0;
        $needsCity = count(array_intersect(['pengcab', 'admin_dojo', 'penguji', 'member'], $allRoles)) > 0;

        if ($primaryRole === 'pb') {
            $data['province_id'] = null;
            $data['city_id'] = null;
            $data['dojo_id'] = null;
        } else {
            if (!$needsCity) {
                $data['city_id'] = null;
            }
            if (!$needsDojo) {
                $data['dojo_id'] = null;
            }
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Data pengurus ' . $user->name . ' berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Cegah hapus diri sendiri
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Helper untuk mendapatkan daftar role yang boleh dibuat/diedit
     */
    private function getAvailableRoles($admin)
    {
        if ($admin->role === 'pb') {
            return [
                'pengprov' => 'Admin Provinsi (Pengprov)',
                'pengcab' => 'Admin Kota/Kab (Pengcab)',
                'admin_dojo' => 'Admin Dojo (Sensei)',
                'penguji' => 'Penguji Ujian Sabuk',
            ];
        }

        if ($admin->role === 'pengprov') {
            return [
                'pengcab' => 'Admin Kota/Kab (Pengcab)',
                'admin_dojo' => 'Admin Dojo (Sensei)',
                'penguji' => 'Penguji Ujian Sabuk',
            ];
        }

        return [];
    }
}