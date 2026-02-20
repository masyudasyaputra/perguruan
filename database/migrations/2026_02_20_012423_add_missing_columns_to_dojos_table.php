<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dojos', function (Blueprint $table) {
            // Menambahkan kolom phone_number setelah sensei_name
            if (!Schema::hasColumn('dojos', 'phone_number')) {
                $table->string('phone_number')->nullable()->after('sensei_name');
            }

            // Menambahkan kolom province_id jika belum ada (penting untuk relasi wilayah)
            if (!Schema::hasColumn('dojos', 'province_id')) {
                $table->unsignedBigInteger('province_id')->nullable()->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dojos', function (Blueprint $table) {
            $table->dropColumn(['phone_number', 'province_id']);
        });
    }
};