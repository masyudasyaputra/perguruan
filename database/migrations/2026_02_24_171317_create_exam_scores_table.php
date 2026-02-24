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
    Schema::create('exam_scores', function (Blueprint $table) {
        $table->id();
        $table->foreignId('exam_id')->constrained()->onDelete('cascade');
        $table->foreignId('member_id')->constrained('users')->onDelete('cascade'); // asumsikan member adalah user
        $table->foreignId('examiner_id')->constrained('users')->onDelete('cascade'); // penguji yang memberi nilai
        $table->enum('kihon', ['Kurang', 'Baik', 'Sangat Baik'])->default('Kurang');
        $table->enum('kata', ['Kurang', 'Baik', 'Sangat Baik'])->default('Kurang');
        $table->enum('kumite', ['Kurang', 'Baik', 'Sangat Baik'])->default('Kurang');
        $table->enum('result', ['Lulus', 'Tidak Lulus', 'Percobaan'])->default('Lulus');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_scores');
    }
};
