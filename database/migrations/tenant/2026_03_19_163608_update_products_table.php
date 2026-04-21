<?php
// database/migrations/2026_03_19_100006_update_products_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Physical attributes for shipping
            if (!Schema::hasColumn('products', 'weight')) {
                $table->decimal('weight', 10, 2)->nullable()->after('quantity')->comment('Weight in kg');
            }

            if (!Schema::hasColumn('products', 'length')) {
                $table->decimal('length', 10, 2)->nullable()->after('weight')->comment('Length in cm');
            }

            if (!Schema::hasColumn('products', 'width')) {
                $table->decimal('width', 10, 2)->nullable()->after('length')->comment('Width in cm');
            }

            if (!Schema::hasColumn('products', 'height')) {
                $table->decimal('height', 10, 2)->nullable()->after('width')->comment('Height in cm');
            }

            if (!Schema::hasColumn('products', 'dimension_unit')) {
                $table->string('dimension_unit', 10)->default('cm')->after('height');
            }

            // Shipping categories
            if (!Schema::hasColumn('products', 'shipping_category')) {
                $table->string('shipping_category', 50)->default('standard')->after('dimension_unit');
            }

            if (!Schema::hasColumn('products', 'hsn_code')) {
                $table->string('hsn_code', 50)->nullable()->after('shipping_category');
            }

            if (!Schema::hasColumn('products', 'is_hazardous')) {
                $table->boolean('is_hazardous')->default(false)->after('hsn_code');
            }

            if (!Schema::hasColumn('products', 'is_fragile')) {
                $table->boolean('is_fragile')->default(false)->after('is_hazardous');
            }

            if (!Schema::hasColumn('products', 'requires_cold_storage')) {
                $table->boolean('requires_cold_storage')->default(false)->after('is_fragile');
            }

            if (!Schema::hasColumn('products', 'max_temperature')) {
                $table->integer('max_temperature')->nullable()->after('requires_cold_storage')->comment('Maximum temperature in °C');
            }

            if (!Schema::hasColumn('products', 'min_temperature')) {
                $table->integer('min_temperature')->nullable()->after('max_temperature')->comment('Minimum temperature in °C');
            }

            // Packaging
            if (!Schema::hasColumn('products', 'package_type')) {
                $table->string('package_type', 50)->default('box')->after('min_temperature');
            }

            if (!Schema::hasColumn('products', 'units_per_package')) {
                $table->integer('units_per_package')->default(1)->after('package_type');
            }

            if (!Schema::hasColumn('products', 'max_quantity_per_shipment')) {
                $table->integer('max_quantity_per_shipment')->nullable()->after('units_per_package');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = [
                'weight',
                'length',
                'width',
                'height',
                'dimension_unit',
                'shipping_category',
                'hsn_code',
                'is_hazardous',
                'is_fragile',
                'requires_cold_storage',
                'max_temperature',
                'min_temperature',
                'package_type',
                'units_per_package',
                'max_quantity_per_shipment'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
