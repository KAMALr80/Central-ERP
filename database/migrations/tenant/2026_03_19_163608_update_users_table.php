<?php
// database/migrations/2026_03_19_100005_update_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Check if role column exists
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('staff')->after('password');
            }

            // Delivery agent specific fields
            if (!Schema::hasColumn('users', 'agent_id')) {
                $table->unsignedBigInteger('agent_id')->nullable()->after('role');
            }

            if (!Schema::hasColumn('users', 'is_online')) {
                $table->boolean('is_online')->default(false)->after('agent_id');
            }

            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_online');
            }

            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            }

            if (!Schema::hasColumn('users', 'login_count')) {
                $table->integer('login_count')->default(0)->after('last_login_ip');
            }

            if (!Schema::hasColumn('users', 'fcm_token')) {
                $table->text('fcm_token')->nullable()->after('login_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'role',
                'agent_id',
                'is_online',
                'last_login_at',
                'last_login_ip',
                'login_count',
                'fcm_token'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
