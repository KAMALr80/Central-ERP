<?php

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
            // working_hours column add karo (agar nahi hai to)
            if (!Schema::hasColumn('attendances', 'working_hours')) {
                $table->string('working_hours')->nullable()->after('check_out');
            }

            // marked_by column (foreign key to users)
            if (!Schema::hasColumn('attendances', 'marked_by')) {
                $table->foreignId('marked_by')
                    ->nullable()
                    ->after('remarks')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            // is_auto_marked column
            if (!Schema::hasColumn('attendances', 'is_auto_marked')) {
                $table->boolean('is_auto_marked')
                    ->default(false)
                    ->after('marked_by');
            }

            // leave_id column (foreign key to leaves)
            if (!Schema::hasColumn('attendances', 'leave_id')) {
                $table->foreignId('leave_id')
                    ->nullable()
                    ->after('employee_id')
                    ->constrained('leaves')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Jo columns add kiye hain, unhe drop karo (agar exist karte hain)
            if (Schema::hasColumn('attendances', 'working_hours')) {
                $table->dropColumn('working_hours');
            }
            if (Schema::hasColumn('attendances', 'marked_by')) {
                $table->dropForeign(['marked_by']);
                $table->dropColumn('marked_by');
            }
            if (Schema::hasColumn('attendances', 'is_auto_marked')) {
                $table->dropColumn('is_auto_marked');
            }
            if (Schema::hasColumn('attendances', 'leave_id')) {
                $table->dropForeign(['leave_id']);
                $table->dropColumn('leave_id');
            }
        });
    }
};
