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
    Schema::table('payments', function (Blueprint $table) {
        // Kita letakkan setelah user_id agar rapi
        $table->foreignId('belt_level_id')->after('user_id')->nullable()->constrained();
    });
}

public function down()
{
    Schema::table('payments', function (Blueprint $table) {
        $table->dropForeign(['belt_level_id']);
        $table->dropColumn('belt_level_id');
    });
}
};
