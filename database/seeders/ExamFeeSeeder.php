<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamFeeSeeder extends Seeder
{
    /**
     * Jalankan seeder berdasarkan tabel exam_fees.
     */
    public function run(): void
    {
        // Data berdasarkan gambar daftar biaya ujian
        $fees = [
            ['belt_level_id' => 2, 'amount' => 150000], // Sabuk Putih ke Kuning
            ['belt_level_id' => 4, 'amount' => 170000], // Sabuk Kuning ke Orange
            ['belt_level_id' => 5, 'amount' => 190000], // Sabuk Orange ke Hijau
            ['belt_level_id' => 6, 'amount' => 210000], // Sabuk Hijau ke Biru
            ['belt_level_id' => 7, 'amount' => 230000], // Sabuk Biru ke Ungu
            ['belt_level_id' => 8, 'amount' => 250000], // Sabuk Ungu ke Coklat
            ['belt_level_id' => 9, 'amount' => 250000],
            ['belt_level_id' => 10, 'amount' => 250000], // Sabuk Coklat Kyu 3 ke 2 & 1
        ];

        foreach ($fees as $fee) {
            DB::table('exam_fees')->updateOrInsert(
                ['belt_level_id' => $fee['belt_level_id']],
                [
                    'amount' => $fee['amount'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}