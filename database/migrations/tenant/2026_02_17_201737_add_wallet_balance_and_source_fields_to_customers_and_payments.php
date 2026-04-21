<?php
// database/migrations/2024_01_01_000001_add_wallet_balance_and_source_fields_to_customers_and_payments.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add wallet_balance to customers table
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'wallet_balance')) {
                $table->decimal('wallet_balance', 10, 2)->default(0)->after('open_balance');
            }
        });

        // Add source_wallet_id to payments table (if not exists)
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'source_wallet_id')) {
                $table->foreignId('source_wallet_id')
                      ->nullable()
                      ->after('wallet_id')
                      ->constrained('customer_wallets')
                      ->nullOnDelete();

                $table->index('source_wallet_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('wallet_balance');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['source_wallet_id']);
            $table->dropColumn('source_wallet_id');
        });
    }
};
