<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // Gunakan if (!Schema::hasColumn...) untuk setiap kolom baru
        if (!Schema::hasColumn('users', 'role')) {
            $table->string('role')->default('member')->after('password');
        }
        if (!Schema::hasColumn('users', 'is_active')) {
            $table->boolean('is_active')->default(false)->after('role');
        }
        if (!Schema::hasColumn('users', 'parent_name')) {
            $table->string('parent_name')->nullable()->after('name');
        }
        if (!Schema::hasColumn('users', 'whatsapp')) {
            $table->string('whatsapp')->nullable()->after('email');
        }
        if (!Schema::hasColumn('users', 'province_id')) {
            $table->foreignId('province_id')->nullable()->constrained();
        }
        if (!Schema::hasColumn('users', 'city_id')) {
            $table->foreignId('city_id')->nullable()->constrained();
        }
        if (!Schema::hasColumn('users', 'dojo_id')) {
            $table->foreignId('dojo_id')->nullable()->constrained();
        }
        if (!Schema::hasColumn('users', 'belt_level_id')) {
            $table->foreignId('belt_level_id')->nullable()->constrained();
        }
        if (!Schema::hasColumn('users', 'expired_at')) {
            $table->timestamp('expired_at')->nullable();
        }

        // Penting: Ubah email agar boleh kosong
        $table->string('email')->nullable()->change();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
