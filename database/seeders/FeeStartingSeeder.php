<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BeltLevel;          // Tambahkan ini
use App\Models\FeeConfiguration;   // Tambahkan ini

class FeeStartingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    $belts = \App\Models\BeltLevel::all();

    foreach ($belts as $belt) {
        \App\Models\FeeConfiguration::updateOrCreate(
            [
                'province_id' => null, // Set sebagai Pusat/PB
                'belt_level_id' => $belt->id
            ],
            [
                'amount' => $belt->membership_fee // Ambil nominal dari tabel belt_levels yang sudah ada
            ]
        );
    }
}
}
