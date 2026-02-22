<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Kosongkan tabel dan reset auto-increment ID ke 1
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('belt_levels')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Masukkan data dengan ID dan Order yang sudah urut
        DB::table('belt_levels')->insert([
            ['id' => 1,  'name' => 'Putih',       'order' => 1,  'membership_fee' => 50000,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 2,  'name' => 'Kuning',      'order' => 2,  'membership_fee' => 60000,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 3,  'name' => 'Kuning Muda', 'order' => 3,  'membership_fee' => 65000,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 4,  'name' => 'Kuning Tua',  'order' => 4,  'membership_fee' => 70000,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 5,  'name' => 'Orange',      'order' => 5,  'membership_fee' => 80000,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 6,  'name' => 'Hijau',       'order' => 6,  'membership_fee' => 75000,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 7,  'name' => 'Biru',        'order' => 7,  'membership_fee' => 90000,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 8,  'name' => 'Ungu',        'order' => 8,  'membership_fee' => 100000, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9,  'name' => 'Cokelat',     'order' => 9,  'membership_fee' => 110000, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'name' => 'Hitam',       'order' => 10, 'membership_fee' => 150000, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        // Tidak perlu drop kolom, cukup biarkan jika ingin rollback
    }
};