<?php
// database/migrations/xxxx_xx_xx_add_wallet_fields_to_payments.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Add wallet relationship
            $table->foreignId('wallet_id')
                  ->nullable()
                  ->after('customer_id')
                  ->constrained('customer_wallets')
                  ->nullOnDelete();

            // Add source advance tracking
            $table->foreignId('source_advance_id')
                  ->nullable()
                  ->after('wallet_id')
                  ->constrained('payments')
                  ->nullOnDelete();

            // Add EMI fields
            $table->integer('emi_months')->nullable()->after('remarks');
            $table->decimal('down_payment', 10, 2)->nullable()->after('emi_months');
            $table->decimal('emi_amount', 10, 2)->nullable()->after('down_payment');

            // Add indexes for better performance
            $table->index('wallet_id');
            $table->index('source_advance_id');
            $table->index('remarks');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['wallet_id']);
            $table->dropForeign(['source_advance_id']);
            $table->dropColumn([
                'wallet_id',
                'source_advance_id',
                'emi_months',
                'down_payment',
                'emi_amount'
            ]);
        });
    }
};
