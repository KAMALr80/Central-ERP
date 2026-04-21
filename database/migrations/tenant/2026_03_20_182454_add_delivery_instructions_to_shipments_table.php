<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            // Add missing columns
            if (!Schema::hasColumn('shipments', 'delivery_instructions')) {
                $table->text('delivery_instructions')->nullable()->after('place_id');
            }

            // Also check for other missing columns that might be needed
            if (!Schema::hasColumn('shipments', 'location_accuracy')) {
                $table->integer('location_accuracy')->nullable()->after('current_longitude');
            }

            if (!Schema::hasColumn('shipments', 'last_location_update')) {
                $table->timestamp('last_location_update')->nullable()->after('location_accuracy');
            }

            if (!Schema::hasColumn('shipments', 'last_ping_at')) {
                $table->timestamp('last_ping_at')->nullable()->after('last_location_update');
            }

            if (!Schema::hasColumn('shipments', 'battery_level')) {
                $table->integer('battery_level')->nullable()->after('last_ping_at');
            }

            if (!Schema::hasColumn('shipments', 'gps_signal_strength')) {
                $table->string('gps_signal_strength')->nullable()->after('battery_level');
            }

            if (!Schema::hasColumn('shipments', 'delivery_attempts')) {
                $table->integer('delivery_attempts')->default(0)->after('gps_signal_strength');
            }

            if (!Schema::hasColumn('shipments', 'last_delivery_attempt_at')) {
                $table->timestamp('last_delivery_attempt_at')->nullable()->after('delivery_attempts');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_instructions',
                'location_accuracy',
                'last_location_update',
                'last_ping_at',
                'battery_level',
                'gps_signal_strength',
                'delivery_attempts',
                'last_delivery_attempt_at'
            ]);
        });
    }
};
