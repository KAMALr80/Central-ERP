<?php
// database/migrations/2026_03_19_100008_update_courier_partners_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courier_partners', function (Blueprint $table) {
            // Contact Information
            if (!Schema::hasColumn('courier_partners', 'contact_person')) {
                $table->string('contact_person')->nullable()->after('code');
            }

            if (!Schema::hasColumn('courier_partners', 'contact_email')) {
                $table->string('contact_email')->nullable()->after('contact_person');
            }

            if (!Schema::hasColumn('courier_partners', 'contact_phone')) {
                $table->string('contact_phone')->nullable()->after('contact_email');
            }

            if (!Schema::hasColumn('courier_partners', 'address')) {
                $table->text('address')->nullable()->after('contact_phone');
            }

            // Service Details
            if (!Schema::hasColumn('courier_partners', 'serviceable_cities')) {
                $table->json('serviceable_cities')->nullable()->after('serviceable_pincodes');
            }

            if (!Schema::hasColumn('courier_partners', 'delivery_days')) {
                $table->json('delivery_days')->nullable()->after('serviceable_cities')->comment('Delivery days per city/pincode');
            }

            if (!Schema::hasColumn('courier_partners', 'cutoff_time')) {
                $table->time('cutoff_time')->nullable()->after('delivery_days');
            }

            if (!Schema::hasColumn('courier_partners', 'holidays')) {
                $table->json('holidays')->nullable()->after('cutoff_time');
            }

            // Weight Slabs
            if (!Schema::hasColumn('courier_partners', 'weight_slabs')) {
                $table->json('weight_slabs')->nullable()->after('rate_card');
            }

            // Volume Based Pricing
            if (!Schema::hasColumn('courier_partners', 'volumetric_factor')) {
                $table->decimal('volumetric_factor', 10, 2)->default(5000)->after('weight_slabs');
            }

            // Tracking URL Pattern
            if (!Schema::hasColumn('courier_partners', 'tracking_url')) {
                $table->string('tracking_url')->nullable()->after('volumetric_factor');
            }

            // Label Generation
            if (!Schema::hasColumn('courier_partners', 'label_format')) {
                $table->string('label_format', 20)->default('pdf')->after('tracking_url');
            }

            if (!Schema::hasColumn('courier_partners', 'label_size')) {
                $table->string('label_size', 20)->default('a4')->after('label_format');
            }

            // Integration Type
            if (!Schema::hasColumn('courier_partners', 'integration_type')) {
                $table->string('integration_type', 50)->default('api')->after('label_size');
            }

            // Logo
            if (!Schema::hasColumn('courier_partners', 'logo')) {
                $table->string('logo')->nullable()->after('integration_type');
            }

            // Description
            if (!Schema::hasColumn('courier_partners', 'description')) {
                $table->text('description')->nullable()->after('logo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courier_partners', function (Blueprint $table) {
            $columns = [
                'contact_person',
                'contact_email',
                'contact_phone',
                'address',
                'serviceable_cities',
                'delivery_days',
                'cutoff_time',
                'holidays',
                'weight_slabs',
                'volumetric_factor',
                'tracking_url',
                'label_format',
                'label_size',
                'integration_type',
                'logo',
                'description'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('courier_partners', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
