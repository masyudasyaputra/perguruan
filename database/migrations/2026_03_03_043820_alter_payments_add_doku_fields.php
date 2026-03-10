<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Tambah kolom-kolom baru
        Schema::table('payments', function (Blueprint $table) {
            // jenis pembayaran: iuran | ujian
            if (!Schema::hasColumn('payments', 'type')) {
                $table->string('type', 20)->default('iuran')->after('user_id');
            }

            // relasi opsional ke entitas sumber
            if (!Schema::hasColumn('payments', 'reference')) {
                $table->string('reference', 80)->nullable()->after('type');
            }

            // status (kalau sudah ada, biarkan)
            if (!Schema::hasColumn('payments', 'status')) {
                $table->string('status', 20)->default('pending')->after('amount');
            }

            if (!Schema::hasColumn('payments', 'expires_at')) {
                // pastikan payment_url memang ada di tabel kamu
                $table->timestamp('expires_at')->nullable()->after('payment_url');
            }

            if (!Schema::hasColumn('payments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('expires_at');
            }

            if (!Schema::hasColumn('payments', 'payload')) {
                $table->json('payload')->nullable()->after('paid_at');
            }

            if (!Schema::hasColumn('payments', 'doku_request_id')) {
                $table->string('doku_request_id', 64)->nullable()->after('payload');
            }

            if (!Schema::hasColumn('payments', 'doku_request_time')) {
                $table->string('doku_request_time', 40)->nullable()->after('doku_request_id');
            }
        });

        // 2) Tambah index tambahan (aman, cek dulu)
        // NOTE: invoice_number index JANGAN ditambah lagi karena sudah ada.

        $idxUserStatus = collect(DB::select("SHOW INDEX FROM payments WHERE Key_name = 'payments_user_id_status_index'"))->isNotEmpty();
        if (!$idxUserStatus) {
            Schema::table('payments', function (Blueprint $table) {
                $table->index(['user_id', 'status']);
            });
        }

        $idxTypeStatus = collect(DB::select("SHOW INDEX FROM payments WHERE Key_name = 'payments_type_status_index'"))->isNotEmpty();
        if (!$idxTypeStatus) {
            Schema::table('payments', function (Blueprint $table) {
                $table->index(['type', 'status']);
            });
        }
    }

    public function down(): void
    {
        // Drop index yang kita buat di up()
        // (invoice_number tidak disentuh)
        $idxUserStatus = collect(DB::select("SHOW INDEX FROM payments WHERE Key_name = 'payments_user_id_status_index'"))->isNotEmpty();
        if ($idxUserStatus) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropIndex('payments_user_id_status_index');
            });
        }

        $idxTypeStatus = collect(DB::select("SHOW INDEX FROM payments WHERE Key_name = 'payments_type_status_index'"))->isNotEmpty();
        if ($idxTypeStatus) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropIndex('payments_type_status_index');
            });
        }

        // Drop kolom
        Schema::table('payments', function (Blueprint $table) {
            $drops = [
                'type',
                'reference',
                'expires_at',
                'paid_at',
                'payload',
                'doku_request_id',
                'doku_request_time',
            ];

            foreach ($drops as $col) {
                if (Schema::hasColumn('payments', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};