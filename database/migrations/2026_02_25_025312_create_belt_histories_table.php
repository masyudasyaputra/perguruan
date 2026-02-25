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
        Schema::create('belt_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('belt_level_id')->constrained()->onDelete('cascade'); // Sabuk yang didapat
            $table->date('achieved_at'); // Tanggal kelulusan/ujian
            $table->string('instructor_name')->nullable(); // Opsional: Siapa pengujinya
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('belt_histories');
    }
};
