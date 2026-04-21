<?php
// database/migrations/2026_03_19_100009_add_foreign_keys.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Shipments table foreign keys
        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'pod_verified_by') && Schema::hasColumn('shipments', 'pod_verified_by')) {
                $table->foreign('pod_verified_by')->references('id')->on('users')->onDelete('set null');
            }

            if (!Schema::hasColumn('shipments', 'return_initiated_by') && Schema::hasColumn('shipments', 'return_initiated_by')) {
                $table->foreign('return_initiated_by')->references('id')->on('users')->onDelete('set null');
            }
        });

        // Delivery agents table foreign keys
        Schema::table('delivery_agents', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_agents', 'current_shift_id') && Schema::hasColumn('delivery_agents', 'current_shift_id')) {
                // Assuming you have a shifts table
                // $table->foreign('current_shift_id')->references('id')->on('shifts')->onDelete('set null');
            }
        });

        // Customers table foreign keys
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'default_address_id') && Schema::hasColumn('customers', 'default_address_id')) {
                $table->foreign('default_address_id')->references('id')->on('customer_addresses')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        // Drop foreign keys
        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'pod_verified_by')) {
                $table->dropForeign(['pod_verified_by']);
            }

            if (Schema::hasColumn('shipments', 'return_initiated_by')) {
                $table->dropForeign(['return_initiated_by']);
            }
        });

        Schema::table('delivery_agents', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_agents', 'current_shift_id')) {
                $table->dropForeign(['current_shift_id']);
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'default_address_id')) {
                $table->dropForeign(['default_address_id']);
            }
        });
    }
};
