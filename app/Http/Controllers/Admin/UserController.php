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
        $admin = auth()->user();
        $adminRole = strtolower((string) $admin->role);

        $query = User::with(['province', 'city', 'dojo']);

        // Filter keamanan berdasarkan role login
        if ($adminRole === 'pengprov') {
            $query->where('province_id', $admin->province_id);
        } elseif ($adminRole === 'pengcab') {
            $query->where('city_id', $admin->city_id);
        } elseif ($adminRole === 'admin_dojo') {
            $query->where('dojo_id', $admin->dojo_id);
        }

        // Filter pencarian
        if ($request->filled('search')) {
            $search = trim((string) $request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter provinsi
        if ($request->filled('province_id')) {
            $query->where('province_id', $request->province_id);
        }

        $users = $query->latest()->paginate(10)->appends($request->query());

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $admin = auth()->user();
        $adminRole = strtolower((string) $admin->role);

        $availableRoles = $this->getAvailableRoles($admin);
        $provinces = $this->getAvailableProvinces($admin);
        $beltLevels = BeltLevel::orderBy('id')->get();

        $cities = collect();
        $dojos = collect();

        if ($adminRole === 'pengprov' && $admin->province_id) {
            $cities = City::where('province_id', $admin->province_id)->orderBy('name')->get();
        }

        if ($adminRole === 'pengcab' && $admin->city_id) {
            $cities = City::where('id', $admin->city_id)->orderBy('name')->get();
            $dojos = Dojo::where('city_id', $admin->city_id)->orderBy('name')->get();
        }

        if ($adminRole === 'admin_dojo' && $admin->city_id) {
            $cities = City::where('id', $admin->city_id)->orderBy('name')->get();
            $dojos = Dojo::where('id', $admin->dojo_id)->orderBy('name')->get();
        }

        return view('admin.users.create_admin', compact(
            'provinces',
            'beltLevels',
            'availableRoles',
            'cities',
            'dojos'
        ));
    }

    public function store(Request $request)
    {
        $admin = auth()->user();
        $availableRoles = array_keys($this->getAvailableRoles($admin));

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['required', 'string', Rule::in($availableRoles)],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'dojo_id' => ['nullable', 'exists:dojos,id'],
            'belt_level_id' => ['nullable', 'exists:belt_levels,id'],
        ]);

        $allRoles = array_values(array_unique($request->roles));
        $primaryRole = $allRoles[0];

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $primaryRole,
            'roles' => $allRoles,
            'province_id' => $request->province_id,
            'city_id' => $request->city_id,
            'dojo_id' => $request->dojo_id,
            'belt_level_id' => $request->belt_level_id,
            'is_active' => true,
        ];

        $data = $this->sanitizeRegionData($data, $allRoles, $admin);

        User::create($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $admin = auth()->user();

        $this->authorizeUserAccess($admin, $user);

        $provinces = $this->getAvailableProvinces($admin);
        $availableRoles = $this->getAvailableRoles($admin);

        $cities = $user->province_id
            ? City::where('province_id', $user->province_id)->orderBy('name')->get()
            : collect();

        $dojos = $user->city_id
            ? Dojo::where('city_id', $user->city_id)->orderBy('name')->get()
            : collect();

        $beltLevels = BeltLevel::orderBy('id')->get();

        if (empty($user->roles)) {
            $user->roles = [$user->role];
        }

        return view('admin.users.edit', compact(
            'user',
            'provinces',
            'cities',
            'dojos',
            'availableRoles',
            'beltLevels'
        ));
    }

    public function update(Request $request, User $user)
    {
        $admin = auth()->user();

        $this->authorizeUserAccess($admin, $user);

        $availableRoles = array_keys($this->getAvailableRoles($admin));

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['required', 'string', Rule::in($availableRoles)],
            'is_active' => ['required', 'boolean'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'dojo_id' => ['nullable', 'exists:dojos,id'],
            'belt_level_id' => ['nullable', 'exists:belt_levels,id'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $allRoles = array_values(array_unique($request->roles));
        $primaryRole = $allRoles[0];

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $primaryRole,
            'roles' => $allRoles,
            'is_active' => $request->is_active,
            'province_id' => $request->province_id,
            'city_id' => $request->city_id,
            'dojo_id' => $request->dojo_id,
            'belt_level_id' => $request->belt_level_id,
        ];

        $data = $this->sanitizeRegionData($data, $allRoles, $admin);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Data user ' . $user->name . ' berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $admin = auth()->user();

        $this->authorizeUserAccess($admin, $user);

        if ($admin->id === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Daftar role yang boleh dibuat/diedit oleh user yang login
     */
    private function getAvailableRoles($admin): array
    {
        $role = strtolower((string) $admin->role);

        if (in_array($role, ['pb', 'admin', 'superadmin'], true)) {
            return [
                'pengprov' => 'Admin Provinsi (Pengprov)',
                'pengcab' => 'Admin Kota/Kab (Pengcab)',
                'admin_dojo' => 'Admin Dojo (Sensei)',
                'penguji' => 'Penguji Ujian Sabuk',
                'member' => 'Member',
            ];
        }

        if ($role === 'pengprov') {
            return [
                'pengcab' => 'Admin Kota/Kab (Pengcab)',
                'admin_dojo' => 'Admin Dojo (Sensei)',
                'penguji' => 'Penguji Ujian Sabuk',
                'member' => 'Member',
            ];
        }

        if ($role === 'pengcab') {
            return [
                'admin_dojo' => 'Admin Dojo (Sensei)',
                'penguji' => 'Penguji Ujian Sabuk',
                'member' => 'Member',
            ];
        }

        if ($role === 'admin_dojo') {
            return [
                'member' => 'Member',
            ];
        }

        return [];
    }

    /**
     * Daftar provinsi yang boleh dipilih sesuai role login
     */
    private function getAvailableProvinces($admin)
    {
        $role = strtolower((string) $admin->role);

        if (in_array($role, ['pb', 'admin', 'superadmin'], true)) {
            return Province::orderBy('name')->get();
        }

        if ($admin->province_id) {
            return Province::where('id', $admin->province_id)->orderBy('name')->get();
        }

        return collect();
    }

    /**
     * Sanitasi field wilayah berdasarkan role yang dipilih
     */
    private function sanitizeRegionData(array $data, array $allRoles, $admin): array
    {
        $primaryRole = $allRoles[0] ?? null;

        $needsProvince = count(array_intersect(['pengprov', 'pengcab', 'admin_dojo', 'penguji', 'member'], $allRoles)) > 0;
        $needsCity = count(array_intersect(['pengcab', 'admin_dojo', 'penguji', 'member'], $allRoles)) > 0;
        $needsDojo = count(array_intersect(['admin_dojo', 'member'], $allRoles)) > 0;
        $needsBeltLevel = in_array('member', $allRoles, true);

        if (in_array($primaryRole, ['pb', 'admin', 'superadmin'], true)) {
            $data['province_id'] = null;
            $data['city_id'] = null;
            $data['dojo_id'] = null;
            $data['belt_level_id'] = null;

            return $data;
        }

        $adminRole = strtolower((string) $admin->role);

        if ($adminRole === 'pengprov') {
            $data['province_id'] = $admin->province_id;
        }

        if ($adminRole === 'pengcab') {
            $data['province_id'] = $admin->province_id;
            $data['city_id'] = $admin->city_id;
        }

        if ($adminRole === 'admin_dojo') {
            $data['province_id'] = $admin->province_id;
            $data['city_id'] = $admin->city_id;
            $data['dojo_id'] = $admin->dojo_id;
        }

        if (!$needsProvince) {
            $data['province_id'] = null;
        }

        if (!$needsCity) {
            $data['city_id'] = null;
        }

        if (!$needsDojo) {
            $data['dojo_id'] = null;
        }

        if (!$needsBeltLevel) {
            $data['belt_level_id'] = null;
        }

        return $data;
    }

    /**
     * Batasi akses edit/hapus sesuai scope wilayah login
     */
    private function authorizeUserAccess($admin, User $user): void
    {
        $adminRole = strtolower((string) $admin->role);

        if ($adminRole === 'pengprov' && $user->province_id !== $admin->province_id) {
            abort(403, 'Anda tidak memiliki akses ke user ini.');
        }

        if ($adminRole === 'pengcab' && $user->city_id !== $admin->city_id) {
            abort(403, 'Anda tidak memiliki akses ke user ini.');
        }

        if ($adminRole === 'admin_dojo' && $user->dojo_id !== $admin->dojo_id) {
            abort(403, 'Anda tidak memiliki akses ke user ini.');
        }
    }
}