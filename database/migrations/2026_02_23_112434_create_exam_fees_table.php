<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('exam_fees', function (Blueprint $table) {
        $table->id();
        $table->foreignId('belt_level_id')->constrained('belt_levels')->onDelete('cascade');
        $table->decimal('amount', 12, 2); // Menggunakan decimal untuk akurasi mata uang
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_fees');
    }
};
