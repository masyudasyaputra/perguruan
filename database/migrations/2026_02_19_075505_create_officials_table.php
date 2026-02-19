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
        Schema::create('officials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position'); // Jabatan: Ketua, Sekretaris, dll
            $table->string('phone_number')->nullable();
            $table->string('sk_number')->nullable();
            $table->date('sk_expiry_date')->nullable();

            // Level Pengurus: 'provinsi' atau 'kota'
            $table->enum('level', ['provinsi', 'kota']);

            // Relasi Opsional
            $table->foreignId('province_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('city_id')->nullable()->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('officials');
    }
};
