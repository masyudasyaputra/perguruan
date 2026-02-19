<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Province;
use App\Models\BeltLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $admin = auth()->user();
        $query = User::where('role', '!=', 'member')->with(['province', 'city', 'dojo']);

        if ($admin->role === 'pengprov') {
            $query->where('province_id', $admin->province_id);
        } elseif ($admin->role === 'pengcab') {
            $query->where('city_id', $admin->city_id);
        }

        $users = $query->latest()->paginate(10);
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
            'province_id' => 'required',
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
}