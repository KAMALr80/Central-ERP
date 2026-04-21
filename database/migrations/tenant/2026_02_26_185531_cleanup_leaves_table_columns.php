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
        Schema::table('leaves', function (Blueprint $table) {
            // Drop the old 'type' column if it exists
            if (Schema::hasColumn('leaves', 'type')) {
                $table->dropColumn('type');
            }

            // Make sure leave_type is properly set
            if (Schema::hasColumn('leaves', 'leave_type')) {
                $table->string('leave_type')->nullable(false)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            if (!Schema::hasColumn('leaves', 'type')) {
                $table->string('type')->after('employee_id');
            }
        });
    }
};
