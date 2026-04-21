<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_mrp_to_products_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'mrp')) {
                $table->decimal('mrp', 10, 2)->default(0.00)->after('price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'mrp')) {
                $table->dropColumn('mrp');
            }
        });
    }
};
