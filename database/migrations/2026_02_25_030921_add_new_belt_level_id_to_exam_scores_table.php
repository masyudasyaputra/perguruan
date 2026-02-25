<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('exam_scores', function (Blueprint $table) {
            // Tambahkan kolom untuk menyimpan ID sabuk baru hasil ujian
            $table->foreignId('new_belt_level_id')->nullable()->constrained('belt_levels')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_scores', function (Blueprint $table) {
            //
        });
    }
};
