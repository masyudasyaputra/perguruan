<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Jalankan migrasi untuk menambah kolom.
     */
    public function up(): void
    {
        Schema::table('belt_histories', function (Blueprint $table) {
            // Menambahkan kolom description setelah kolom belt_id (atau sesuaikan posisinya)
            // nullable() digunakan agar data lama tidak error karena kolom ini kosong
            $table->string('description')->nullable()->after('belt_level_id');
        });
    }

    /**
     * Gulung balik (rollback) migrasi jika terjadi kesalahan.
     */
    public function down(): void
    {
        Schema::table('belt_histories', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};