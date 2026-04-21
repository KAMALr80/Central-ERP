<?php
// database/migrations/2026_03_20_235600_add_default_to_joining_date_in_delivery_agents.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_agents', function (Blueprint $table) {
            // Add default value to joining_date
            if (Schema::hasColumn('delivery_agents', 'joining_date')) {
                $table->date('joining_date')->default(now())->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('delivery_agents', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_agents', 'joining_date')) {
                $table->date('joining_date')->nullable()->change();
            }
        });
    }
};
