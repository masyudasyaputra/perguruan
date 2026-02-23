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
    Schema::create('fee_configurations', function (Blueprint $table) {
        $table->id();
        // province_id NULL = Harga Nasional (PB)
        // province_id Terisi = Harga Khusus Provinsi tersebut (Pengprov)
        $table->foreignId('province_id')->nullable()->constrained()->onDelete('cascade');
        $table->foreignId('belt_level_id')->constrained()->onDelete('cascade');
        $table->decimal('amount', 12, 2); 
        $table->timestamps();

        // Mencegah duplikasi harga untuk sabuk yang sama di wilayah yang sama
        $table->unique(['province_id', 'belt_level_id'], 'fee_rule_unique');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_configurations');
    }
};
