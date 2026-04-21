<?php
// database/migrations/2026_03_20_000000_add_delivery_fields_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add delivery agent specific fields
            if (!Schema::hasColumn('users', 'rating')) {
                $table->decimal('rating', 3, 2)->default(4.5)->after('status');
            }

            if (!Schema::hasColumn('users', 'total_deliveries')) {
                $table->integer('total_deliveries')->default(0)->after('rating');
            }

            if (!Schema::hasColumn('users', 'current_latitude')) {
                $table->decimal('current_latitude', 10, 8)->nullable()->after('total_deliveries');
            }

            if (!Schema::hasColumn('users', 'current_longitude')) {
                $table->decimal('current_longitude', 11, 8)->nullable()->after('current_latitude');
            }

            if (!Schema::hasColumn('users', 'current_location')) {
                $table->string('current_location')->nullable()->after('current_longitude');
            }

            if (!Schema::hasColumn('users', 'mobile')) {
                $table->string('mobile')->nullable()->after('email');
            }

            if (!Schema::hasColumn('users', 'is_online')) {
                $table->boolean('is_online')->default(false)->after('status');
            }

            if (!Schema::hasColumn('users', 'agent_id')) {
                $table->foreignId('agent_id')->nullable()->after('role');
            }

            if (!Schema::hasColumn('users', 'login_count')) {
                $table->integer('login_count')->default(0)->after('password');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'rating', 'total_deliveries', 'current_latitude', 'current_longitude',
                'current_location', 'mobile', 'is_online', 'agent_id', 'login_count'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
