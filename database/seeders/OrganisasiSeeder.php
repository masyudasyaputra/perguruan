<?php

namespace Database\Seeders;

use App\Models\Province;
use App\Models\City;
use App\Models\Dojo;
use Illuminate\Database\Seeder;

class OrganisasiSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Provinsi (Pengprov)
        $provinsi = Province::create(['name' => 'Jawa Barat']);

        // 2. Buat Kota/Kabupaten (Pengcab) di bawah Provinsi tersebut
        $kota = City::create([
            'province_id' => $provinsi->id,
            'name' => 'Kota Bandung'
        ]);

        // 3. Buat Dojo di bawah Kota tersebut
        Dojo::create([
            'city_id' => $kota->id,
            'name' => 'Dojo Rajawali Pusat',
            'address' => 'Jl. Merdeka No. 123, Bandung'
        ]);

        Dojo::create([
            'city_id' => $kota->id,
            'name' => 'Dojo Harimau Timur',
            'address' => 'Jl. Gatot Subroto No. 45, Bandung'
        ]);

        $this->command->info('Data Organisasi (Provinsi, Kota, Dojo) berhasil diisi!');
    }
}