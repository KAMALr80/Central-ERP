<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_security_fields_to_users.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('login_attempts')->default(0)->after('otp_expires_at');
            $table->timestamp('locked_until')->nullable()->after('login_attempts');
            $table->timestamp('password_updated_at')->nullable()->after('locked_until');
            $table->string('last_login_ip')->nullable()->after('password_updated_at');
            $table->timestamp('last_login_at')->nullable()->after('last_login_ip');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'login_attempts',
                'locked_until',
                'password_updated_at',
                'last_login_ip',
                'last_login_at'
            ]);
        });
    }
};
