<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BeltLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $belts = [
        ['name' => 'Putih', 'membership_fee' => 50000],
        ['name' => 'Kuning', 'membership_fee' => 60000],
        ['name' => 'Hijau', 'membership_fee' => 75000],
        ['name' => 'Biru', 'membership_fee' => 90000],
        ['name' => 'Cokelat', 'membership_fee' => 110000],
        ['name' => 'Hitam', 'membership_fee' => 150000],
    ];

    foreach ($belts as $belt) {
        \App\Models\BeltLevel::create($belt);
    }
}
}
