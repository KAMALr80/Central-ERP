<?php
// database/migrations/2026_03_10_224935_add_shipping_fields_to_sales_table.php

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
        Schema::table('sales', function (Blueprint $table) {
            // Check if columns don't already exist before adding
            if (!Schema::hasColumn('sales', 'requires_shipping')) {
                $table->boolean('requires_shipping')
                      ->default(false)
                      ->after('paid_amount')
                      ->comment('Flag to indicate if this sale requires shipping');
            }

            if (!Schema::hasColumn('sales', 'shipping_address')) {
                $table->text('shipping_address')
                      ->nullable()
                      ->after('requires_shipping')
                      ->comment('Complete shipping address for delivery');
            }

            if (!Schema::hasColumn('sales', 'city')) {
                $table->string('city', 100)
                      ->nullable()
                      ->after('shipping_address')
                      ->comment('City for shipping');
            }

            if (!Schema::hasColumn('sales', 'state')) {
                $table->string('state', 100)
                      ->nullable()
                      ->after('city')
                      ->comment('State for shipping');
            }

            if (!Schema::hasColumn('sales', 'pincode')) {
                $table->string('pincode', 20)
                      ->nullable()
                      ->after('state')
                      ->comment('PIN code / Postal code for shipping');
            }

            if (!Schema::hasColumn('sales', 'receiver_name')) {
                $table->string('receiver_name', 255)
                      ->nullable()
                      ->after('pincode')
                      ->comment('Name of the person receiving the shipment (if different from customer)');
            }

            if (!Schema::hasColumn('sales', 'receiver_phone')) {
                $table->string('receiver_phone', 20)
                      ->nullable()
                      ->after('receiver_name')
                      ->comment('Phone number of receiver (if different from customer)');
            }

            if (!Schema::hasColumn('sales', 'delivery_instructions')) {
                $table->text('delivery_instructions')
                      ->nullable()
                      ->after('receiver_phone')
                      ->comment('Special instructions for delivery');
            }

            // Add indexes for better query performance
            $table->index('requires_shipping');
            $table->index(['city', 'state']);
            $table->index('pincode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['requires_shipping']);
            $table->dropIndex(['city', 'state']);
            $table->dropIndex(['pincode']);

            // Drop columns
            $table->dropColumn([
                'requires_shipping',
                'shipping_address',
                'city',
                'state',
                'pincode',
                'receiver_name',
                'receiver_phone',
                'delivery_instructions'
            ]);
        });
    }
};
