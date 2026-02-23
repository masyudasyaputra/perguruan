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
    Schema::create('exam_participants', function (Blueprint $table) {
        $table->id();
        $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
        
        // KUNCI PERBAIKAN: Merujuk ke tabel 'users', bukan 'members'
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        
        $table->foreignId('current_belt_id')->constrained('belt_levels');
        $table->foreignId('target_belt_id')->constrained('belt_levels');
        $table->decimal('fee_amount', 15, 2)->default(0);
        $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
        $table->enum('result', ['pending', 'passed', 'failed'])->default('pending');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_participants');
    }
};
