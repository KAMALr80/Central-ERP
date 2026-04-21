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
        Schema::table('shipments', function (Blueprint $table) {
            // Destination columns (already there)
            if (!Schema::hasColumn('shipments', 'destination_latitude')) {
                $table->decimal('destination_latitude', 10, 8)->nullable()->after('shipping_address');
            }

            if (!Schema::hasColumn('shipments', 'destination_longitude')) {
                $table->decimal('destination_longitude', 11, 8)->nullable()->after('destination_latitude');
            }

            // ✅ CURRENT LOCATION COLUMNS - ADD THESE
            if (!Schema::hasColumn('shipments', 'current_latitude')) {
                $table->decimal('current_latitude', 10, 8)->nullable()->after('destination_longitude');
            }

            if (!Schema::hasColumn('shipments', 'current_longitude')) {
                $table->decimal('current_longitude', 11, 8)->nullable()->after('current_latitude');
            }

            // ✅ LOCATION ACCURACY - FOR GPS PRECISION
            if (!Schema::hasColumn('shipments', 'location_accuracy')) {
                $table->integer('location_accuracy')->nullable()->after('current_longitude')->comment('Accuracy in meters');
            }

            // ✅ LAST LOCATION UPDATE
            if (!Schema::hasColumn('shipments', 'last_location_update')) {
                $table->timestamp('last_location_update')->nullable()->after('location_accuracy');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn([
                'destination_latitude',
                'destination_longitude',
                'current_latitude',
                'current_longitude',
                'location_accuracy',
                'last_location_update'
            ]);
        });
    }
};
