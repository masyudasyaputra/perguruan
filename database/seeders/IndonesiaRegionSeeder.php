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
            'Jawa Barat' => [
                'Kota Bandung',
                'Kab. Bandung',
                'Kota Bogor',
                'Kab. Bogor',
                'Kota Bekasi',
                'Kab. Bekasi',
                'Kota Depok',
                'Kota Cimahi'
            ],
            'DKI Jakarta' => [
                'Jakarta Pusat',
                'Jakarta Utara',
                'Jakarta Timur',
                'Jakarta Selatan',
                'Jakarta Barat',
                'Kepulauan Seribu'
            ],
            'Jawa Tengah' => [
                'Kota Semarang',
                'Kota Surakarta',
                'Kota Magelang',
                'Kab. Banyumas',
                'Kab. Cilacap'
            ],
            'Jawa Timur' => [
                'Kota Surabaya',
                'Kota Malang',
                'Kota Batu',
                'Kab. Sidoarjo',
                'Kab. Gresik'
            ],
            'Sumatera Utara' => [
                'Kab. Asahan',
                'Kab. Batu Bara',
                'Kab. Dairi',
                'Kab. Deli Serdang',
                'Kab. Humbang Hasundutan',
                'Kab. Karo',
                'Kab. Labuhanbatu',
                'Kab. Labuhanbatu Selatan',
                'Kab. Labuhanbatu Utara',
                'Kab. Langkat',
                'Kab. Mandailing Natal',
                'Kab. Nias',
                'Kab. Nias Barat',
                'Kab. Nias Selatan',
                'Kab. Nias Utara',
                'Kab. Padang Lawas',
                'Kab. Padang Lawas Utara',
                'Kab. Pakpak Bharat',
                'Kab. Samosir',
                'Kab. Serdang Bedagai',
                'Kab. Simalungun',
                'Kab. Tapanuli Selatan',
                'Kab. Tapanuli Tengah',
                'Kab. Tapanuli Utara',
                'Kab. Toba',
                'Kota Binjai',
                'Kota Gunungsitoli',
                'Kota Medan',
                'Kota Padangsidimpuan',
                'Kota Pematangsiantar',
                'Kota Sibolga',
                'Kota Tanjungbalai',
                'Kota Tebing Tinggi'
            ],

            // Tambahkan provinsi lainnya di sini sesuai kebutuhan
        ];

        foreach ($regions as $provinceName => $cities) {
            $province = Province::create(['name' => $provinceName]);

            foreach ($cities as $cityName) {
                City::create([
                    'province_id' => $province->id,
                    'name' => $cityName
                ]);
            }
        }

        $this->command->info('Data Provinsi dan Kota berhasil diimpor!');
    }
}