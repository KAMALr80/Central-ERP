<?php
// database/migrations/xxxx_xx_xx_xxxxxx_update_attendances_table_for_advanced_features.php

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
        Schema::table('attendances', function (Blueprint $table) {
            // 1. Add leave_id foreign key (if not exists)
            if (!Schema::hasColumn('attendances', 'leave_id')) {
                $table->foreignId('leave_id')
                    ->nullable()
                    ->after('employee_id')
                    ->constrained('leaves')
                    ->nullOnDelete();
            }

            // 2. Add marked_by (user who marked the attendance)
            if (!Schema::hasColumn('attendances', 'marked_by')) {
                $table->foreignId('marked_by')
                    ->nullable()
                    ->after('remarks')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            // 3. Add is_auto_marked flag
            if (!Schema::hasColumn('attendances', 'is_auto_marked')) {
                $table->boolean('is_auto_marked')
                    ->default(false)
                    ->after('marked_by');
            }

            // 4. Convert status column to string (instead of ENUM)
            if (Schema::hasColumn('attendances', 'status')) {
                $table->string('status', 20)->default('Present')->change();
            } else {
                $table->string('status', 20)->default('Present');
            }
        });

        // 5. Add indexes for better performance
        Schema::table('attendances', function (Blueprint $table) {
            $table->index('attendance_date');
            $table->index('status');
            $table->index('marked_by');
            $table->index('is_auto_marked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['attendance_date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['marked_by']);
            $table->dropIndex(['is_auto_marked']);

            // Drop foreign keys and columns
            if (Schema::hasColumn('attendances', 'leave_id')) {
                $table->dropForeign(['leave_id']);
                $table->dropColumn('leave_id');
            }

            if (Schema::hasColumn('attendances', 'marked_by')) {
                $table->dropForeign(['marked_by']);
                $table->dropColumn('marked_by');
            }

            if (Schema::hasColumn('attendances', 'is_auto_marked')) {
                $table->dropColumn('is_auto_marked');
            }

            // Convert status back to string (or keep as string)
            if (Schema::hasColumn('attendances', 'status')) {
                $table->string('status', 20)->default('Present')->change();
            }
        });
    }
};
