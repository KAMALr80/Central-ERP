<?php
// database/migrations/2026_03_19_100007_update_shipment_trackings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipment_trackings', function (Blueprint $table) {
            // Additional tracking fields
            if (!Schema::hasColumn('shipment_trackings', 'city')) {
                $table->string('city', 100)->nullable()->after('location');
            }

            if (!Schema::hasColumn('shipment_trackings', 'state')) {
                $table->string('state', 100)->nullable()->after('city');
            }

            if (!Schema::hasColumn('shipment_trackings', 'country')) {
                $table->string('country', 100)->default('India')->after('state');
            }

            if (!Schema::hasColumn('shipment_trackings', 'pincode')) {
                $table->string('pincode', 20)->nullable()->after('country');
            }

            if (!Schema::hasColumn('shipment_trackings', 'accuracy')) {
                $table->integer('accuracy')->nullable()->after('longitude')->comment('Accuracy in meters');
            }

            if (!Schema::hasColumn('shipment_trackings', 'speed')) {
                $table->decimal('speed', 10, 2)->nullable()->after('accuracy')->comment('Speed in km/h');
            }

            if (!Schema::hasColumn('shipment_trackings', 'heading')) {
                $table->integer('heading')->nullable()->after('speed')->comment('Direction in degrees');
            }

            if (!Schema::hasColumn('shipment_trackings', 'altitude')) {
                $table->decimal('altitude', 10, 2)->nullable()->after('heading')->comment('Altitude in meters');
            }

            if (!Schema::hasColumn('shipment_trackings', 'event_type')) {
                $table->string('event_type', 50)->nullable()->after('remarks');
            }

            if (!Schema::hasColumn('shipment_trackings', 'is_public')) {
                $table->boolean('is_public')->default(true)->after('event_type');
            }

            if (!Schema::hasColumn('shipment_trackings', 'metadata')) {
                $table->json('metadata')->nullable()->after('is_public');
            }

            // Add index for faster queries
            $table->index(['shipment_id', 'tracked_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('shipment_trackings', function (Blueprint $table) {
            $columns = [
                'city',
                'state',
                'country',
                'pincode',
                'accuracy',
                'speed',
                'heading',
                'altitude',
                'event_type',
                'is_public',
                'metadata'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('shipment_trackings', $column)) {
                    $table->dropColumn($column);
                }
            }

            $table->dropIndex(['shipment_id', 'tracked_at', 'status']);
        });
    }
};
