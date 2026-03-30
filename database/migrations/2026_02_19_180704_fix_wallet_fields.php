<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Add source_wallet_id column (agar nahi hai to)
        Schema::table('payments', function (Blueprint $table) {
            // Check and add source_wallet_id column
            if (!Schema::hasColumn('payments', 'source_wallet_id')) {
                $table->foreignId('source_wallet_id')
                      ->nullable()
                      ->after('wallet_id')
                      ->constrained('customer_wallets')
                      ->nullOnDelete();
            }

            // Remove old source_advance_id if exists
            if (Schema::hasColumn('payments', 'source_advance_id')) {
                // Drop foreign key first
                $table->dropForeign(['source_advance_id']);
                // Then drop the column
                $table->dropColumn('source_advance_id');
            }

            // Remove EMI fields if they exist
            if (Schema::hasColumn('payments', 'emi_months')) {
                $table->dropColumn('emi_months');
            }

            if (Schema::hasColumn('payments', 'down_payment')) {
                $table->dropColumn('down_payment');
            }

            if (Schema::hasColumn('payments', 'emi_amount')) {
                $table->dropColumn('emi_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Rollback: add back old columns (if needed)
            if (!Schema::hasColumn('payments', 'source_advance_id')) {
                $table->foreignId('source_advance_id')
                      ->nullable()
                      ->after('wallet_id')
                      ->constrained('payments')
                      ->nullOnDelete();
            }

            if (!Schema::hasColumn('payments', 'emi_months')) {
                $table->integer('emi_months')->nullable();
            }

            if (!Schema::hasColumn('payments', 'down_payment')) {
                $table->decimal('down_payment', 10, 2)->nullable();
            }

            if (!Schema::hasColumn('payments', 'emi_amount')) {
                $table->decimal('emi_amount', 10, 2)->nullable();
            }

            // Remove source_wallet_id
            if (Schema::hasColumn('payments', 'source_wallet_id')) {
                $table->dropForeign(['source_wallet_id']);
                $table->dropColumn('source_wallet_id');
            }
        });
    }
};
