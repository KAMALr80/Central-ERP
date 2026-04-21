<?php
// database/migrations/2026_03_19_180000_fix_users_table_columns.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Fix status column - ensure it's string with proper length
            $table->string('status', 50)->default('active')->change();

            // Add missing columns if not exist
            if (!Schema::hasColumn('users', 'login_count')) {
                $table->integer('login_count')->default(0)->after('status');
            }

            if (!Schema::hasColumn('users', 'is_online')) {
                $table->boolean('is_online')->default(false)->after('login_count');
            }

            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_online');
            }

            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            }

            if (!Schema::hasColumn('users', 'fcm_token')) {
                $table->text('fcm_token')->nullable()->after('last_login_ip');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert changes if needed
        });
    }
};
