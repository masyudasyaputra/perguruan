<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Province;
use App\Models\City;
use App\Models\Dojo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTestingSeeder extends Seeder
{
    public function run(): void
    {
        // Gunakan firstOrCreate agar jika sudah ada tidak error, jika belum ada dibuatkan
        $prov = \App\Models\Province::firstOrCreate(['name' => 'Jawa Barat']);
        $city = \App\Models\City::firstOrCreate(['name' => 'Kota Bandung'], ['province_id' => $prov->id]);
        $dojo = \App\Models\Dojo::firstOrCreate(['name' => 'Dojo Rajawali'], ['city_id' => $city->id, 'address' => 'Jl. Merdeka']);

        $pass = \Illuminate\Support\Facades\Hash::make('password123');

        // Buat User PB
        \App\Models\User::updateOrCreate(['email' => 'pb@test.com'], [
            'name' => 'Admin PB',
            'password' => $pass,
            'role' => 'pb'
        ]);

        // Buat User Dojo
        \App\Models\User::updateOrCreate(['email' => 'dojo@test.com'], [
            'name' => 'Admin Dojo',
            'password' => $pass,
            'role' => 'admin_dojo',
            'province_id' => $prov->id,
            'city_id' => $city->id,
            'dojo_id' => $dojo->id
        ]);
    }
}