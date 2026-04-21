<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add tracking fields (keeping your existing columns)
            $table->integer('otp_attempts')->default(0)->after('otp_expires_at');
            $table->timestamp('otp_last_sent_at')->nullable()->after('otp_attempts');
            $table->timestamp('last_otp_verified_at')->nullable()->after('otp_last_sent_at');

            // For registration OTP (separate from login)
            $table->string('register_otp')->nullable()->after('last_otp_verified_at');
            $table->timestamp('register_otp_expires_at')->nullable()->after('register_otp');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'otp_attempts',
                'otp_last_sent_at',
                'last_otp_verified_at',
                'register_otp',
                'register_otp_expires_at'
            ]);
        });
    }
};
