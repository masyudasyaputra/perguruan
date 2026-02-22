<?php

namespace App\Http\Controllers\Admin; // Pastikan namespace ini benar

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BeltLevel;
use App\Models\Province;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    
public function create()
{
    $user = auth()->user();
    $role = $user->role;

    // Ambil data sabuk (berlaku untuk semua)
    $beltLevels = \App\Models\BeltLevel::all();

    // Logika Filter Wilayah
    $provinces = \App\Models\Province::query()
        ->when($role !== 'pb', function($q) use ($user) {
            return $q->where('id', $user->province_id);
        })->get();

    // Jika pengcab, kita bisa langsung kunci kotanya
    $cities = \App\Models\City::query()
        ->when($role === 'pengprov', function($q) use ($user) {
            return $q->where('province_id', $user->province_id);
        })
        ->when($role === 'pengcab', function($q) use ($user) {
            return $q->where('id', $user->city_id);
        })->get();

    return view('admin.members.create', compact('provinces', 'beltLevels', 'cities', 'role'));
}

    public function store(Request $request)
{
    $admin = auth()->user();
    
    // PERBAIKAN: Ambil hanya kata pertama dari nama Dojo untuk password
    $fullDojoName = $admin->dojo->name ?? 'DOJO';
    $firstWord = strtoupper(explode(' ', trim($fullDojoName))[0]); 
    $defaultPass = $firstWord . '123'; // Hasil: BUSHIN2026

    // Validasi input array
    $request->validate([
        'members.*.name' => 'required|string|max:255',
        'members.*.parent_name' => 'required|string|max:255',
        'members.*.whatsapp' => 'required|numeric',
        'members.*.belt_level_id' => 'required|exists:belt_levels,id',
    ]);

    $totalAmount = 0;
    $memberIds = [];

    foreach ($request->members as $data) {
        // Email virtual jika kosong agar tidak error unique constraint
        $email = !empty($data['email']) ? $data['email'] : ($data['whatsapp'] . rand(10,99) . '@perguruan.local');

        $member = \App\Models\User::create([
            'name' => $data['name'],
            'parent_name' => $data['parent_name'],
            'email' => $email,
            'whatsapp' => $data['whatsapp'],
            'password' => \Illuminate\Support\Facades\Hash::make($defaultPass), // Password Sinkron
            'role' => 'member',
            'is_active' => false,
            'province_id' => $admin->province_id,
            'city_id' => $admin->city_id,
            'dojo_id' => $admin->dojo_id,
            'belt_level_id' => $data['belt_level_id'],
        ]);

        // Hitung biaya berdasarkan biaya sabuk masing-masing
        $belt = \App\Models\BeltLevel::find($data['belt_level_id']);
        $totalAmount += $belt ? $belt->membership_fee : 150000; // Fallback jika sabuk tak ketemu
        $memberIds[] = $member->id;
    }

    $payment = \App\Models\Payment::create([
        'user_id' => $admin->id,
        'external_id' => 'COLL-' . strtoupper(\Illuminate\Support\Str::random(10)),
        'category' => 'collective_membership_fee',
        'amount' => $totalAmount,
        'status' => 'pending',
        'details' => json_encode($memberIds),
    ]);

    return redirect()->route('payment.checkout', $payment->id);
}

public function review(Request $request)
{
    // Validasi dasar
    $request->validate([
        'members.*.name' => 'required',
        'members.*.whatsapp' => 'required',
    ]);

    $member_data = $request->members;
    
    // Tambahkan info nama sabuk agar muncul di checkout
    foreach ($member_data as &$item) {
        $belt = \App\Models\BeltLevel::find($item['belt_level_id']);
        $item['belt_name'] = $belt ? $belt->name . " - " . $belt->kyu_dan : 'N/A';
    }

    $price_per_person = 150000; // Contoh harga
    $admin_fee = 5000;
    $total = ($price_per_person * count($member_data)) + $admin_fee;

    return view('admin.members.review', compact('member_data', 'price_per_person', 'admin_fee', 'total'));
}
}