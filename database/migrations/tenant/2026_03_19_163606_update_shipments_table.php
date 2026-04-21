<?php
// database/migrations/2026_03_19_100001_update_shipments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            // Delivery Order & Route
            if (!Schema::hasColumn('shipments', 'delivery_order')) {
                $table->integer('delivery_order')->nullable()->after('status');
            }

            if (!Schema::hasColumn('shipments', 'distance_travelled')) {
                $table->decimal('distance_travelled', 10, 2)->nullable()->after('delivery_order');
            }

            if (!Schema::hasColumn('shipments', 'estimated_delivery_time')) {
                $table->integer('estimated_delivery_time')->nullable()->after('distance_travelled');
            }

            // Route Polyline for map
            if (!Schema::hasColumn('shipments', 'route_polyline')) {
                $table->text('route_polyline')->nullable()->after('estimated_delivery_time');
            }

            // Real-time tracking
            if (!Schema::hasColumn('shipments', 'last_ping_at')) {
                $table->timestamp('last_ping_at')->nullable()->after('route_polyline');
            }

            if (!Schema::hasColumn('shipments', 'battery_level')) {
                $table->integer('battery_level')->nullable()->after('last_ping_at');
            }

            if (!Schema::hasColumn('shipments', 'gps_signal_strength')) {
                $table->string('gps_signal_strength', 20)->nullable()->after('battery_level');
            }

            // POD Verification
            if (!Schema::hasColumn('shipments', 'pod_verified_at')) {
                $table->timestamp('pod_verified_at')->nullable()->after('gps_signal_strength');
            }

            if (!Schema::hasColumn('shipments', 'pod_verified_by')) {
                $table->unsignedBigInteger('pod_verified_by')->nullable()->after('pod_verified_at');
            }

            // Google Place ID
            if (!Schema::hasColumn('shipments', 'place_id')) {
                $table->string('place_id')->nullable()->after('pod_verified_by');
            }

            // Delivery Attempts
            if (!Schema::hasColumn('shipments', 'delivery_attempts')) {
                $table->integer('delivery_attempts')->default(0)->after('place_id');
            }

            if (!Schema::hasColumn('shipments', 'last_delivery_attempt_at')) {
                $table->timestamp('last_delivery_attempt_at')->nullable()->after('delivery_attempts');
            }

            // Return Reason
            if (!Schema::hasColumn('shipments', 'return_reason')) {
                $table->string('return_reason', 255)->nullable()->after('last_delivery_attempt_at');
            }

            if (!Schema::hasColumn('shipments', 'return_initiated_by')) {
                $table->unsignedBigInteger('return_initiated_by')->nullable()->after('return_reason');
            }

            // Customer OTP for delivery
            if (!Schema::hasColumn('shipments', 'delivery_otp')) {
                $table->string('delivery_otp', 6)->nullable()->after('return_initiated_by');
            }

            if (!Schema::hasColumn('shipments', 'otp_verified_at')) {
                $table->timestamp('otp_verified_at')->nullable()->after('delivery_otp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $columns = [
                'delivery_order',
                'distance_travelled',
                'estimated_delivery_time',
                'route_polyline',
                'last_ping_at',
                'battery_level',
                'gps_signal_strength',
                'pod_verified_at',
                'pod_verified_by',
                'place_id',
                'delivery_attempts',
                'last_delivery_attempt_at',
                'return_reason',
                'return_initiated_by',
                'delivery_otp',
                'otp_verified_at'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('shipments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
