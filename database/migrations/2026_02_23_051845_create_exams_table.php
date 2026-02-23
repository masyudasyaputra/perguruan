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
    Schema::create('exams', function (Blueprint $table) {
        $table->id();
        $table->string('name'); 
        $table->date('execution_date'); // Sesuai dengan kolom di database Anda
        $table->string('location');
        $table->foreignId('province_id')->nullable()->constrained('provinces')->onDelete('cascade');
        $table->enum('status', ['draft', 'open', 'ongoing', 'completed'])->default('draft');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
