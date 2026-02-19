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
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        $table->string('invoice_number')->unique();
        $table->integer('amount');
        $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
        $table->string('doku_transaction_id')->nullable(); // ID dari DOKU
        $table->string('payment_url')->nullable(); // Link bayar dari DOKU
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
