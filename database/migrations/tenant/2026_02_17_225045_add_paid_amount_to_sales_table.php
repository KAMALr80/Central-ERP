<?php
// database/migrations/xxxx_xx_xx_add_paid_amount_to_sales_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'paid_amount')) {
                $table->decimal('paid_amount', 10, 2)->default(0)->after('grand_total');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('paid_amount');
        });
    }
};
