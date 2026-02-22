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
    Schema::table('belt_levels', function (Blueprint $table) {
        $table->integer('order')->default(0)->after('name');
    });

    // Berikan urutan spesifik berdasarkan nama sabuk
    $orderMap = [
        'Putih'       => 1,
        'Kuning'      => 2,
        'Kuning Muda' => 3,
        'Kuning Tua'  => 4,
        'Orange'      => 5,
        'Hijau'       => 6,
        'Biru'        => 7,
        'Ungu'        => 8,
        'Coklat'      => 9,
        'Hitam'       => 10,
    ];

    foreach ($orderMap as $name => $order) {
        DB::table('belt_levels')->where('name', $name)->update(['order' => $order]);
    }
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('belt_levels', function (Blueprint $table) {
            //
        });
    }
};
