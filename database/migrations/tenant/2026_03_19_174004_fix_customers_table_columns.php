<?php
// database/migrations/2026_03_19_180001_fix_customers_table_columns.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Add missing columns if not exist
            if (!Schema::hasColumn('customers', 'country')) {
                $table->string('country', 100)->default('India')->after('pincode');
            }

            if (!Schema::hasColumn('customers', 'allow_sms_notifications')) {
                $table->boolean('allow_sms_notifications')->default(true)->after('notes');
            }

            if (!Schema::hasColumn('customers', 'allow_email_notifications')) {
                $table->boolean('allow_email_notifications')->default(true)->after('allow_sms_notifications');
            }

            if (!Schema::hasColumn('customers', 'allow_whatsapp_notifications')) {
                $table->boolean('allow_whatsapp_notifications')->default(true)->after('allow_email_notifications');
            }

            if (!Schema::hasColumn('customers', 'default_latitude')) {
                $table->decimal('default_latitude', 10, 8)->nullable()->after('allow_whatsapp_notifications');
            }

            if (!Schema::hasColumn('customers', 'default_longitude')) {
                $table->decimal('default_longitude', 11, 8)->nullable()->after('default_latitude');
            }

            if (!Schema::hasColumn('customers', 'default_place_id')) {
                $table->string('default_place_id')->nullable()->after('default_longitude');
            }

            if (!Schema::hasColumn('customers', 'default_address_id')) {
                $table->unsignedBigInteger('default_address_id')->nullable()->after('default_place_id');
            }

            if (!Schema::hasColumn('customers', 'preferred_delivery_time')) {
                $table->string('preferred_delivery_time', 50)->nullable()->after('default_address_id');
            }

            if (!Schema::hasColumn('customers', 'delivery_instructions')) {
                $table->text('delivery_instructions')->nullable()->after('preferred_delivery_time');
            }

            if (!Schema::hasColumn('customers', 'tags')) {
                $table->json('tags')->nullable()->after('delivery_instructions');
            }

            if (!Schema::hasColumn('customers', 'notes')) {
                $table->text('notes')->nullable()->after('tags');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $columns = [
                'country',
                'allow_sms_notifications',
                'allow_email_notifications',
                'allow_whatsapp_notifications',
                'default_latitude',
                'default_longitude',
                'default_place_id',
                'default_address_id',
                'preferred_delivery_time',
                'delivery_instructions',
                'tags',
                'notes'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('customers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
