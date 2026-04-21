<?php
// database/migrations/2026_03_19_100002_update_delivery_agents_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_agents', function (Blueprint $table) {
            // Online/Offline Tracking
            if (!Schema::hasColumn('delivery_agents', 'last_online_at')) {
                $table->timestamp('last_online_at')->nullable()->after('last_location_update');
            }

            if (!Schema::hasColumn('delivery_agents', 'last_offline_at')) {
                $table->timestamp('last_offline_at')->nullable()->after('last_online_at');
            }

            if (!Schema::hasColumn('delivery_agents', 'total_online_minutes')) {
                $table->integer('total_online_minutes')->default(0)->after('last_offline_at');
            }

            // Device Info
            if (!Schema::hasColumn('delivery_agents', 'device_id')) {
                $table->string('device_id')->nullable()->after('total_online_minutes');
            }

            if (!Schema::hasColumn('delivery_agents', 'device_model')) {
                $table->string('device_model')->nullable()->after('device_id');
            }

            if (!Schema::hasColumn('delivery_agents', 'app_version')) {
                $table->string('app_version')->nullable()->after('device_model');
            }

            if (!Schema::hasColumn('delivery_agents', 'fcm_token')) {
                $table->text('fcm_token')->nullable()->after('app_version');
            }

            // Performance Metrics
            if (!Schema::hasColumn('delivery_agents', 'avg_delivery_time')) {
                $table->integer('avg_delivery_time')->nullable()->after('fcm_token');
            }

            if (!Schema::hasColumn('delivery_agents', 'on_time_delivery_rate')) {
                $table->decimal('on_time_delivery_rate', 5, 2)->default(0)->after('avg_delivery_time');
            }

            if (!Schema::hasColumn('delivery_agents', 'customer_feedback_count')) {
                $table->integer('customer_feedback_count')->default(0)->after('on_time_delivery_rate');
            }

            // Shift Details
            if (!Schema::hasColumn('delivery_agents', 'shift_start_time')) {
                $table->time('shift_start_time')->nullable()->after('customer_feedback_count');
            }

            if (!Schema::hasColumn('delivery_agents', 'shift_end_time')) {
                $table->time('shift_end_time')->nullable()->after('shift_start_time');
            }

            if (!Schema::hasColumn('delivery_agents', 'current_shift_id')) {
                $table->unsignedBigInteger('current_shift_id')->nullable()->after('shift_end_time');
            }

            // Emergency Contact
            if (!Schema::hasColumn('delivery_agents', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable()->after('current_shift_id');
            }

            if (!Schema::hasColumn('delivery_agents', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            }

            if (!Schema::hasColumn('delivery_agents', 'blood_group')) {
                $table->string('blood_group', 5)->nullable()->after('emergency_contact_phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('delivery_agents', function (Blueprint $table) {
            $columns = [
                'last_online_at',
                'last_offline_at',
                'total_online_minutes',
                'device_id',
                'device_model',
                'app_version',
                'fcm_token',
                'avg_delivery_time',
                'on_time_delivery_rate',
                'customer_feedback_count',
                'shift_start_time',
                'shift_end_time',
                'current_shift_id',
                'emergency_contact_name',
                'emergency_contact_phone',
                'blood_group'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('delivery_agents', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
