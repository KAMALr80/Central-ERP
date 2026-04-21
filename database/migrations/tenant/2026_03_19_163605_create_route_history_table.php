<?php
// database/migrations/2026_03_19_000005_create_route_history_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('route_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('route_id');
            $table->unsignedBigInteger('agent_id');
            $table->unsignedBigInteger('shipment_id');
            $table->integer('stop_order');

            // Location at stop
            $table->decimal('arrival_lat', 10, 8)->nullable();
            $table->decimal('arrival_lng', 11, 8)->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('departed_at')->nullable();

            // Status at stop
            $table->string('status')->default('pending'); // pending, arrived, completed, skipped
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->foreign('route_id')->references('id')->on('saved_routes')->onDelete('cascade');
            $table->foreign('agent_id')->references('id')->on('delivery_agents')->onDelete('cascade');
            $table->foreign('shipment_id')->references('id')->on('shipments')->onDelete('cascade');

            $table->index(['route_id', 'stop_order']);
            $table->index(['agent_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_history');
    }
};
