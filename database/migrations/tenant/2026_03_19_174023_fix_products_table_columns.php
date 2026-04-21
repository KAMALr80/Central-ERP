<?php
// database/migrations/2026_03_19_180002_fix_products_table_columns.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add missing columns if not exist
            if (!Schema::hasColumn('products', 'mrp')) {
                $table->decimal('mrp', 10, 2)->nullable()->after('price');
            }

            if (!Schema::hasColumn('products', 'cost_price')) {
                $table->decimal('cost_price', 10, 2)->nullable()->after('mrp');
            }

            if (!Schema::hasColumn('products', 'wholesale_price')) {
                $table->decimal('wholesale_price', 10, 2)->nullable()->after('cost_price');
            }

            if (!Schema::hasColumn('products', 'min_quantity')) {
                $table->integer('min_quantity')->default(0)->after('quantity');
            }

            if (!Schema::hasColumn('products', 'max_quantity')) {
                $table->integer('max_quantity')->nullable()->after('min_quantity');
            }

            if (!Schema::hasColumn('products', 'reorder_level')) {
                $table->integer('reorder_level')->default(5)->after('max_quantity');
            }

            if (!Schema::hasColumn('products', 'brand')) {
                $table->string('brand', 100)->nullable()->after('category');
            }

            if (!Schema::hasColumn('products', 'sub_category')) {
                $table->string('sub_category', 100)->nullable()->after('brand');
            }

            // Shipping attributes
            if (!Schema::hasColumn('products', 'weight')) {
                $table->decimal('weight', 10, 2)->nullable()->after('sub_category');
            }

            if (!Schema::hasColumn('products', 'length')) {
                $table->decimal('length', 10, 2)->nullable()->after('weight');
            }

            if (!Schema::hasColumn('products', 'width')) {
                $table->decimal('width', 10, 2)->nullable()->after('length');
            }

            if (!Schema::hasColumn('products', 'height')) {
                $table->decimal('height', 10, 2)->nullable()->after('width');
            }

            if (!Schema::hasColumn('products', 'dimension_unit')) {
                $table->string('dimension_unit', 10)->default('cm')->after('height');
            }

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
                $table->integer('max_temperature')->nullable()->after('requires_cold_storage');
            }

            if (!Schema::hasColumn('products', 'min_temperature')) {
                $table->integer('min_temperature')->nullable()->after('max_temperature');
            }

            if (!Schema::hasColumn('products', 'package_type')) {
                $table->string('package_type', 50)->default('box')->after('min_temperature');
            }

            if (!Schema::hasColumn('products', 'units_per_package')) {
                $table->integer('units_per_package')->default(1)->after('package_type');
            }

            if (!Schema::hasColumn('products', 'max_quantity_per_shipment')) {
                $table->integer('max_quantity_per_shipment')->nullable()->after('units_per_package');
            }

            if (!Schema::hasColumn('products', 'gallery_images')) {
                $table->json('gallery_images')->nullable()->after('image');
            }

            if (!Schema::hasColumn('products', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('gallery_images');
            }

            if (!Schema::hasColumn('products', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }

            if (!Schema::hasColumn('products', 'meta_keywords')) {
                $table->text('meta_keywords')->nullable()->after('meta_description');
            }

            if (!Schema::hasColumn('products', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('meta_keywords');
            }

            if (!Schema::hasColumn('products', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_active');
            }

            if (!Schema::hasColumn('products', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('is_featured');
            }

            if (!Schema::hasColumn('products', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = [
                'mrp', 'cost_price', 'wholesale_price', 'min_quantity', 'max_quantity',
                'reorder_level', 'brand', 'sub_category', 'weight', 'length', 'width',
                'height', 'dimension_unit', 'shipping_category', 'hsn_code', 'is_hazardous',
                'is_fragile', 'requires_cold_storage', 'max_temperature', 'min_temperature',
                'package_type', 'units_per_package', 'max_quantity_per_shipment', 'gallery_images',
                'meta_title', 'meta_description', 'meta_keywords', 'is_active', 'is_featured',
                'created_by', 'updated_by'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
