<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Add net working hours (after break deduction)
            $table->string('net_working_hours')->nullable()->after('working_hours');

            // Add break minutes (default 60 minutes = 1 hour)
            $table->integer('break_minutes')->default(60)->after('net_working_hours');

            // Add overtime hours
            $table->string('overtime_hours')->nullable()->after('break_minutes');

            // Add IP tracking
            $table->string('check_in_ip')->nullable()->after('check_in');
            $table->string('check_out_ip')->nullable()->after('check_out');

            // Fix status enum (remove duplicates)
            $table->enum('status', ['Present', 'Absent', 'Late', 'Half Day', 'Leave'])
                  ->default('Present')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'net_working_hours',
                'break_minutes',
                'overtime_hours',
                'check_in_ip',
                'check_out_ip'
            ]);

            // Revert status to old enum (optional)
            $table->enum('status', ['Present', 'Absent', 'Late', 'Half Day', 'Late...', 'Unformatted'])
                  ->default('Present')
                  ->change();
        });
    }
};
