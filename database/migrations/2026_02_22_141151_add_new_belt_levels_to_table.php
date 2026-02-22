<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Menambahkan data sabuk baru ke tabel belt_levels
        DB::table('belt_levels')->insert([
            [
                'name' => 'Kuning Muda',
                'membership_fee' => 65000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kuning Tua',
                'membership_fee' => 70000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Orange',
                'membership_fee' => 80000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ungu',
                'membership_fee' => 100000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Menghapus data jika migrasi di-rollback
        DB::table('belt_levels')
            ->whereIn('name', ['Kuning Muda', 'Kuning Tua', 'Orange', 'Ungu'])
            ->delete();
    }
};