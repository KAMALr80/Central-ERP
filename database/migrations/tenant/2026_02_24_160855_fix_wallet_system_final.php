<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ========== 1. FIX PAYMENTS TABLE ==========
        Schema::table('payments', function (Blueprint $table) {
            // Check aur add missing columns
            if (!Schema::hasColumn('payments', 'source_wallet_id')) {
                $table->foreignId('source_wallet_id')
                      ->nullable()
                      ->after('wallet_id')
                      ->constrained('customer_wallets')
                      ->nullOnDelete();
            }

            if (!Schema::hasColumn('payments', 'remarks')) {
                $table->string('remarks', 100)->nullable()->after('transaction_id');
            }
        });

        // ========== 2. SKIP INDEXES - Already exist ==========
        // Indexes pehle se exist karte hain, isliye indexes add nahi kar rahe

        // ========== 3. FIX METHOD COLUMN ==========
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'method')) {
                $table->string('method', 50)->default('cash')->change();
            }
        });

        // ========== 4. CUSTOMER WALLETS INDEXES ==========
        // Sirf customer_wallets table mein indexes check karo
        Schema::table('customer_wallets', function (Blueprint $table) {
            // Check if indexes exist before adding
            $indexes = DB::select('SHOW INDEX FROM customer_wallets');
            $indexNames = array_column($indexes, 'Key_name');

            if (!in_array('customer_wallets_type_index', $indexNames)) {
                $table->index('type');
            }

            if (!in_array('customer_wallets_created_at_index', $indexNames)) {
                $table->index('created_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop columns only, not indexes
            if (Schema::hasColumn('payments', 'source_wallet_id')) {
                $table->dropForeign(['source_wallet_id']);
                $table->dropColumn('source_wallet_id');
            }

            if (Schema::hasColumn('payments', 'remarks')) {
                $table->dropColumn('remarks');
            }
        });

        // Revert method column
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'method')) {
                $table->string('method', 50)->default('cash')->change();
            }
        });

        // Drop customer_wallets indexes
        Schema::table('customer_wallets', function (Blueprint $table) {
            try {
                $table->dropIndex('customer_wallets_type_index');
            } catch (\Exception $e) {}

            try {
                $table->dropIndex('customer_wallets_created_at_index');
            } catch (\Exception $e) {}
        });
    }
};
