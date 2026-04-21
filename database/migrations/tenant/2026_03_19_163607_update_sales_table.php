<?php
// database/migrations/2026_03_19_100003_update_sales_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Shipping Tracking
            if (!Schema::hasColumn('sales', 'shipping_status')) {
                $table->string('shipping_status')->default('pending')->after('requires_shipping');
            }

            if (!Schema::hasColumn('sales', 'shipped_at')) {
                $table->timestamp('shipped_at')->nullable()->after('shipping_status');
            }

            if (!Schema::hasColumn('sales', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            }

            // Coordinates from Google Maps
            if (!Schema::hasColumn('sales', 'destination_latitude')) {
                $table->decimal('destination_latitude', 10, 8)->nullable()->after('delivery_instructions');
            }

            if (!Schema::hasColumn('sales', 'destination_longitude')) {
                $table->decimal('destination_longitude', 11, 8)->nullable()->after('destination_latitude');
            }

            if (!Schema::hasColumn('sales', 'place_id')) {
                $table->string('place_id')->nullable()->after('destination_longitude');
            }

            if (!Schema::hasColumn('sales', 'location_verified')) {
                $table->boolean('location_verified')->default(false)->after('place_id');
            }

            // Shipping Preferences
            if (!Schema::hasColumn('sales', 'preferred_delivery_date')) {
                $table->date('preferred_delivery_date')->nullable()->after('location_verified');
            }

            if (!Schema::hasColumn('sales', 'preferred_delivery_time')) {
                $table->string('preferred_delivery_time', 50)->nullable()->after('preferred_delivery_date');
            }

            if (!Schema::hasColumn('sales', 'allow_partial_delivery')) {
                $table->boolean('allow_partial_delivery')->default(false)->after('preferred_delivery_time');
            }

            // Shipping Charges
            if (!Schema::hasColumn('sales', 'shipping_charge')) {
                $table->decimal('shipping_charge', 10, 2)->default(0)->after('allow_partial_delivery');
            }

            if (!Schema::hasColumn('sales', 'cod_charge')) {
                $table->decimal('cod_charge', 10, 2)->default(0)->after('shipping_charge');
            }

            if (!Schema::hasColumn('sales', 'insurance_charge')) {
                $table->decimal('insurance_charge', 10, 2)->default(0)->after('cod_charge');
            }

            if (!Schema::hasColumn('sales', 'packing_charge')) {
                $table->decimal('packing_charge', 10, 2)->default(0)->after('insurance_charge');
            }

            // Total Shipping Charge
            if (!Schema::hasColumn('sales', 'total_shipping_charge')) {
                $table->decimal('total_shipping_charge', 10, 2)->default(0)->after('packing_charge');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $columns = [
                'shipping_status',
                'shipped_at',
                'delivered_at',
                'destination_latitude',
                'destination_longitude',
                'place_id',
                'location_verified',
                'preferred_delivery_date',
                'preferred_delivery_time',
                'allow_partial_delivery',
                'shipping_charge',
                'cod_charge',
                'insurance_charge',
                'packing_charge',
                'total_shipping_charge'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('sales', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
