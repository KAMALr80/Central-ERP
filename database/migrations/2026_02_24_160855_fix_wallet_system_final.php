<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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

        // ========== 2. ADD MISSING INDEXES ==========
        Schema::table('payments', function (Blueprint $table) {
            // Add indexes - Laravel automatically handles duplicates
            if (Schema::hasColumn('payments', 'source_wallet_id')) {
                $table->index('source_wallet_id');
            }

            if (Schema::hasColumn('payments', 'customer_id') && Schema::hasColumn('payments', 'remarks')) {
                $table->index(['customer_id', 'remarks']);
            }

            if (Schema::hasColumn('payments', 'created_at') && Schema::hasColumn('payments', 'status')) {
                $table->index(['created_at', 'status']);
            }
        });

        Schema::table('customer_wallets', function (Blueprint $table) {
            // Add indexes - Laravel automatically handles duplicates
            if (Schema::hasColumn('customer_wallets', 'type')) {
                $table->index('type');
            }

            if (Schema::hasColumn('customer_wallets', 'created_at')) {
                $table->index('created_at');
            }
        });

        // ========== 3. FIX METHOD COLUMN - Convert to string instead of ENUM ==========
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'method')) {
                // Convert enum to string for better compatibility
                $table->string('method', 50)->default('cash')->change();
            } else {
                $table->string('method', 50)->default('cash');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop indexes if they exist
            $table->dropIndex(['source_wallet_id']);
            $table->dropIndex(['customer_id', 'remarks']);
            $table->dropIndex(['created_at', 'status']);
        });

        Schema::table('customer_wallets', function (Blueprint $table) {
            // Drop indexes if they exist
            $table->dropIndex(['type']);
            $table->dropIndex(['created_at']);
        });

        // Revert method column back to string (or keep as string)
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'method')) {
                $table->string('method', 50)->default('cash')->change();
            }
        });
    }
};
