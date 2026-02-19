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
    Schema::create('belt_levels', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Contoh: Sabuk Putih, Sabuk Kuning
        $table->integer('membership_fee'); // Biaya member berdasarkan sabuk
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('belt_levels');
    }
};
