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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // ROLE SESUAI STRUKTUR ORGANISASI
            $table->enum('role', [
                'pb',           // Pengurus Besar
                'pengprov',     // Pengurus Provinsi
                'pengcab',      // Pengurus Cabang
                'admin_dojo',   // Pengurus Dojo
                'member'        // Anggota
            ])->default('member');

            // RELASI HIERARKI (Dibuat nullable agar fleksibel)
            $table->foreignId('province_id')->nullable()->constrained('provinces')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('dojo_id')->nullable()->constrained('dojos')->nullOnDelete();

            // DATA ANGGOTA
            $table->foreignId('belt_level_id')->nullable()->constrained('belt_levels')->nullOnDelete();
            $table->string('phone_number')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};