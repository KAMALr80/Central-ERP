<?php

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
            // Add tax_amount column if not exists
            if (!Schema::hasColumn('sales', 'tax_amount')) {
                $table->decimal('tax_amount', 15, 2)
                      ->default(0)
                      ->after('tax')
                      ->comment('Calculated tax amount based on tax percentage');
            }

            // Add paid_amount column if not exists (already in your code)
            if (!Schema::hasColumn('sales', 'paid_amount')) {
                $table->decimal('paid_amount', 15, 2)
                      ->default(0)
                      ->after('grand_total')
                      ->comment('Total amount paid so far');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['tax_amount', 'paid_amount']);
        });
    }
};
