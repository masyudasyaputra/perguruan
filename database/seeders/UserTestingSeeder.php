<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTestingSeeder extends Seeder
{
    /**
     * Jalankan seeder sesuai struktur tabel users di database.
     */
    public function run(): void
    {
        $pass = Hash::make('password123');

        // Data User sesuai dengan kolom dan role di database kamu
        $users = [
            [
                'name' => 'Admin PB Pusat',
                'email' => 'pb@test.com',
                'password' => $pass,
                'role' => 'pb',
                'province_id' => null,
                'city_id' => null,
                'dojo_id' => null,
            ],
            [
                'name' => 'Jelly (Pengprov)',
                'email' => 'jelly@gmail.com',
                'password' => $pass,
                'role' => 'pengprov',
                'province_id' => 5, // Mengacu pada ID yang sudah ada di DB kamu
                'city_id' => null,
                'dojo_id' => null,
            ],
            [
                'name' => 'Nadia (Pengcab)',
                'email' => 'nadia@gmail.com',
                'password' => $pass,
                'role' => 'pengcab',
                'province_id' => 5,
                'city_id' => 52,
                'dojo_id' => null,
            ],
            [
                'name' => 'Hendra (Admin Dojo)',
                'email' => 'hendra@gmail.com',
                'password' => $pass,
                'role' => 'admin_dojo',
                'province_id' => 5,
                'city_id' => 52,
                'dojo_id' => null,
            ],
            [
                'name' => 'Sensei Penguji',
                'email' => 'penguji@test.com',
                'password' => $pass,
                'role' => 'penguji',
                'province_id' => 5,
                'city_id' => 52,
                'dojo_id' => null,
            ],
            [
                'name' => 'Nasha Aleena (Member)',
                'email' => 'nasha@gmail.com',
                'password' => $pass,
                'role' => 'member',
                'province_id' => 5,
                'city_id' => 52,
                'dojo_id' => null,
                'belt_level_id' => 1, // Asumsi Sabuk Putih
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']], // Cek berdasarkan email agar tidak duplikat
                $userData
            );
        }
    }
}