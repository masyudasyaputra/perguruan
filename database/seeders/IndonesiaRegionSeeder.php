<?php

namespace Database\Seeders;

use App\Models\Province;
use App\Models\City;
use Illuminate\Database\Seeder;

class IndonesiaRegionSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            // --- SUMATERA ---
            'ACEH' => ['Kota Banda Aceh', 'Kota Sabang', 'Kab. Aceh Besar'],
            'SUMATERA UTARA' => ['Kota Medan', 'Kota Binjai', 'Kab. Deli Serdang', 'Kab. Karo'],
            'SUMATERA BARAT' => ['Kota Padang', 'Kota Bukittinggi', 'Kota Payakumbuh'],
            'RIAU' => ['Kota Pekanbaru', 'Kota Dumai', 'Kab. Bengkalis'],
            'JAMBI' => ['Kota Jambi', 'Kota Sungai Penuh', 'Kab. Muaro Jambi'],
            'SUMATERA SELATAN' => ['Kota Palembang', 'Kota Prabumulih', 'Kota Lubuklinggau'],
            'BENGKULU' => ['Kota Bengkulu', 'Kab. Rejang Lebong', 'Kab. Muko Muko'],
            'LAMPUNG' => ['Kota Bandar Lampung', 'Kota Metro', 'Kab. Lampung Selatan'],
            'KEPULAUAN BANGKA BELITUNG' => ['Kota Pangkal Pinang', 'Kab. Bangka', 'Kab. Belitung'],
            'KEPULAUAN RIAU' => ['Kota Tanjung Pinang', 'Kota Batam', 'Kab. Bintan'],

            // --- JAWA ---
            'DKI JAKARTA' => ['Jakarta Pusat', 'Jakarta Utara', 'Jakarta Timur', 'Jakarta Selatan', 'Jakarta Barat'],
            'JAWA BARAT' => ['Kota Bandung', 'Kota Bogor', 'Kota Bekasi', 'Kota Depok'],
            'JAWA TENGAH' => ['Kota Semarang', 'Kota Surakarta', 'Kota Magelang'],
            'DI YOGYAKARTA' => ['Kota Yogyakarta', 'Kab. Sleman', 'Kab. Bantul'],
            'JAWA TIMUR' => ['Kota Surabaya', 'Kota Malang', 'Kota Kediri'],
            'BANTEN' => ['Kota Serang', 'Kota Tangerang', 'Kota Cilegon'],

            // --- BALI & NUSA TENGGARA ---
            'BALI' => ['Kota Denpasar', 'Kab. Badung', 'Kab. Gianyar'],
            'NUSA TENGGARA BARAT' => ['Kota Mataram', 'Kota Bima', 'Kab. Lombok Barat'],
            'NUSA TENGGARA TIMUR' => ['Kota Kupang', 'Kab. Flores Timur', 'Kab. Sikka'],

            // --- KALIMANTAN ---
            'KALIMANTAN BARAT' => ['Kota Pontianak', 'Kota Singkawang', 'Kab. Kubu Raya'],
            'KALIMANTAN TENGAH' => ['Kota Palangkaraya', 'Kab. Kotawaringin Timur'],
            'KALIMANTAN SELATAN' => ['Kota Banjarmasin', 'Kota Banjarbaru', 'Kab. Banjar'],
            'KALIMANTAN TIMUR' => ['Kota Samarinda', 'Kota Balikpapan', 'Kota Bontang'],
            'KALIMANTAN UTARA' => ['Kota Tarakan', 'Kab. Bulungan', 'Kab. Nunukan'],

            // --- SULAWESI ---
            'SULAWESI UTARA' => ['Kota Manado', 'Kota Bitung', 'Kota Tomohon'],
            'SULAWESI TENGAH' => ['Kota Palu', 'Kab. Donggala', 'Kab. Banggai'],
            'SULAWESI SELATAN' => ['Kota Makassar', 'Kota Parepare', 'Kota Palopo'],
            'SULAWESI TENGGARA' => ['Kota Kendari', 'Kota Bau-Bau', 'Kab. Kolaka'],
            'GORONTALO' => ['Kota Gorontalo', 'Kab. Limboto', 'Kab. Boalemo'],
            'SULAWESI BARAT' => ['Kab. Mamuju', 'Kab. Majene', 'Kab. Polewali Mandar'],

            // --- MALUKU & PAPUA ---
            'MALUKU' => ['Kota Ambon', 'Kota Tual', 'Kab. Maluku Tengah'],
            'MALUKU UTARA' => ['Kota Ternate', 'Kota Tidore Kepulauan', 'Kab. Halmahera Utara'],
            'PAPUA' => ['Kota Jayapura', 'Kab. Jayapura', 'Kab. Biak Numfor'],
            'PAPUA BARAT' => ['Kota Sorong', 'Kab. Manokwari', 'Kab. Fakfak'],
            'PAPUA SELATAN' => ['Kab. Merauke', 'Kab. Mappi', 'Kab. Asmat'],
            'PAPUA TENGAH' => ['Kab. Mimika', 'Kab. Nabire', 'Kab. Paniai'],
            'PAPUA PEGUNUNGAN' => ['Kab. Jayawijaya', 'Kab. Lanny Jaya', 'Kab. Tolikara'],
            'PAPUA BARAT DAYA' => ['Kota Sorong', 'Kab. Raja Ampat', 'Kab. Tambrauw'],
        ];

        foreach ($regions as $provinceName => $cities) {
            // Menggunakan updateOrCreate agar data yang sudah ada tidak dobel
            $province = Province::updateOrCreate(
                ['name' => $provinceName]
            );

            foreach ($cities as $cityName) {
                City::updateOrCreate([
                    'province_id' => $province->id,
                    'name' => strtoupper($cityName)
                ]);
            }
        }

        $this->command->info('Seluruh 38 Provinsi dan sampel Kota berhasil diimpor!');
    }
}