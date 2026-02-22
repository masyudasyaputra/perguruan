<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambahkan kolom kyu_dan jika belum ada
        if (!Schema::hasColumn('belt_levels', 'kyu_dan')) {
            Schema::table('belt_levels', function (Blueprint $table) {
                $table->string('kyu_dan', 20)->after('name')->nullable();
            });
        }

        // 2. Bersihkan data lama
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('belt_levels')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 3. Masukkan data sesuai struktur baru (Total 16 Level)
        $belts = [
            ['id' => 1,  'name' => 'Putih',       'kyu_dan' => 'Kyu 10', 'fee' => 50000],
            ['id' => 2,  'name' => 'Kuning Muda', 'kyu_dan' => 'Kyu 9',  'fee' => 60000],
            ['id' => 3,  'name' => 'Kuning Tua',  'kyu_dan' => 'Kyu 8',  'fee' => 65000],
            ['id' => 4,  'name' => 'Orange',      'kyu_dan' => 'Kyu 7',  'fee' => 70000],
            ['id' => 5,  'name' => 'Hijau',       'kyu_dan' => 'Kyu 6',  'fee' => 75000],
            ['id' => 6,  'name' => 'Biru',        'kyu_dan' => 'Kyu 5',  'fee' => 90000],
            ['id' => 7,  'name' => 'Ungu',        'kyu_dan' => 'Kyu 4',  'fee' => 100000],
            ['id' => 8,  'name' => 'Cokelat',     'kyu_dan' => 'Kyu 3',  'fee' => 110000],
            ['id' => 9,  'name' => 'Cokelat',     'kyu_dan' => 'Kyu 2',  'fee' => 110000],
            ['id' => 10, 'name' => 'Cokelat',     'kyu_dan' => 'Kyu 1',  'fee' => 110000],
            ['id' => 11, 'name' => 'Hitam',       'kyu_dan' => 'DAN I',  'fee' => 150000],
            ['id' => 12, 'name' => 'Hitam',       'kyu_dan' => 'DAN II', 'fee' => 150000],
            ['id' => 13, 'name' => 'Hitam',       'kyu_dan' => 'DAN III','fee' => 200000],
            ['id' => 14, 'name' => 'Hitam',       'kyu_dan' => 'DAN IV', 'fee' => 250000],
            ['id' => 15, 'name' => 'Hitam',       'kyu_dan' => 'DAN V',  'fee' => 300000],
            ['id' => 16, 'name' => 'Hitam',       'kyu_dan' => 'DAN VI', 'fee' => 500000],
        ];

        foreach ($belts as $belt) {
            DB::table('belt_levels')->insert([
                'id' => $belt['id'],
                'name' => $belt['name'],
                'kyu_dan' => $belt['kyu_dan'],
                'order' => $belt['id'], // ID dan Order kita samakan biar rapi
                'membership_fee' => $belt['fee'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void { }
};