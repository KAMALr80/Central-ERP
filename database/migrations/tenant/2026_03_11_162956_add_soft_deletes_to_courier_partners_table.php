<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courier_partners', function (Blueprint $table) {
            // Check if column doesn't already exist
            if (!Schema::hasColumn('courier_partners', 'deleted_at')) {
                $table->softDeletes(); // Add deleted_at column
            }
        });
    }

    public function down(): void
    {
        Schema::table('courier_partners', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Remove deleted_at column
        });
    }
};
