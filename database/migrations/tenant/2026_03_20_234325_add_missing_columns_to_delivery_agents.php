<?php
// database/migrations/2026_03_20_235500_add_missing_columns_to_delivery_agents.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_agents', function (Blueprint $table) {
            // Check and add missing columns
            if (!Schema::hasColumn('delivery_agents', 'current_location')) {
                $table->string('current_location')->nullable()->after('current_longitude');
            }

            if (!Schema::hasColumn('delivery_agents', 'rating')) {
                $table->decimal('rating', 3, 2)->default(4.5)->after('status');
            }

            if (!Schema::hasColumn('delivery_agents', 'total_deliveries')) {
                $table->integer('total_deliveries')->default(0)->after('rating');
            }

            if (!Schema::hasColumn('delivery_agents', 'successful_deliveries')) {
                $table->integer('successful_deliveries')->default(0)->after('total_deliveries');
            }

            if (!Schema::hasColumn('delivery_agents', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('successful_deliveries');
            }

            if (!Schema::hasColumn('delivery_agents', 'is_online')) {
                $table->boolean('is_online')->default(false)->after('is_active');
            }

            if (!Schema::hasColumn('delivery_agents', 'last_active_at')) {
                $table->timestamp('last_active_at')->nullable()->after('is_online');
            }
        });
    }

    public function down(): void
    {
        Schema::table('delivery_agents', function (Blueprint $table) {
            $columns = ['current_location', 'rating', 'total_deliveries', 'successful_deliveries', 'is_active', 'is_online', 'last_active_at'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('delivery_agents', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
