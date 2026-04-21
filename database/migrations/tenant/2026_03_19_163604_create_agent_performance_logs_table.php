<?php
// database/migrations/2026_03_19_000003_create_agent_performance_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_performance_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id');
            $table->date('log_date');

            // Daily Stats
            $table->integer('shipments_assigned')->default(0);
            $table->integer('shipments_delivered')->default(0);
            $table->integer('shipments_failed')->default(0);
            $table->decimal('total_distance_km', 10, 2)->default(0);
            $table->integer('total_time_minutes')->default(0);
            $table->decimal('average_rating', 3, 2)->nullable();

            // Online/Offline Tracking
            $table->timestamp('first_active_at')->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->integer('active_minutes')->default(0);

            // Earnings
            $table->decimal('base_pay', 10, 2)->default(0);
            $table->decimal('commission_earned', 10, 2)->default(0);
            $table->decimal('bonus_earned', 10, 2)->default(0);
            $table->decimal('total_earnings', 10, 2)->default(0);

            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('delivery_agents')->onDelete('cascade');
            $table->unique(['agent_id', 'log_date']);
            $table->index(['agent_id', 'log_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_performance_logs');
    }
};
