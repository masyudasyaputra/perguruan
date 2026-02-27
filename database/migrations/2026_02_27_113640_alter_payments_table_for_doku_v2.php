<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            // 1) Pastikan invoice_number unik / terindex (kalau belum)
            // Kalau sudah ada unique di DB, bagian ini aman di-skip manual.
            // Kita pakai index saja untuk aman.
            if (!Schema::hasColumn('payments', 'type')) {
                $table->string('type')->default('membership_fee')->after('user_id');
                // membership_fee | exam_fee
            }

            if (!Schema::hasColumn('payments', 'meta')) {
                $table->json('meta')->nullable()->after('payment_url');
            }

            if (!Schema::hasColumn('payments', 'callback_payload')) {
                $table->json('callback_payload')->nullable()->after('meta');
            }

            if (!Schema::hasColumn('payments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('callback_payload');
            }

            if (!Schema::hasColumn('payments', 'expired_at')) {
                $table->timestamp('expired_at')->nullable()->after('paid_at');
            }

            // Index biar query cepat
            $table->index(['type', 'status']);
            $table->index('invoice_number');
            $table->index('doku_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            // drop indexes (nama index otomatis bisa beda di beberapa DB)
            // jadi kita drop by columns, Laravel akan cari default name.
            $table->dropIndex(['type', 'status']);
            $table->dropIndex(['invoice_number']);
            $table->dropIndex(['doku_transaction_id']);

            if (Schema::hasColumn('payments', 'expired_at'))
                $table->dropColumn('expired_at');
            if (Schema::hasColumn('payments', 'paid_at'))
                $table->dropColumn('paid_at');
            if (Schema::hasColumn('payments', 'callback_payload'))
                $table->dropColumn('callback_payload');
            if (Schema::hasColumn('payments', 'meta'))
                $table->dropColumn('meta');
            if (Schema::hasColumn('payments', 'type'))
                $table->dropColumn('type');
        });
    }
};