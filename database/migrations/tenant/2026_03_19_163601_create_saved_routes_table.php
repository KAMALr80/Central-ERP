<?php
// database/migrations/2026_03_19_000001_create_saved_routes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_routes', function (Blueprint $table) {
            $table->id();
            $table->string('route_code')->unique();
            $table->string('name');
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->date('route_date');

            // Route Details
            $table->json('waypoints')->nullable();
            $table->json('shipment_ids')->nullable();
            $table->decimal('total_distance', 10, 2)->nullable();
            $table->integer('total_duration')->nullable();
            $table->json('optimized_order')->nullable();
            $table->text('polyline')->nullable();

            // Start/End Points
            $table->decimal('start_lat', 10, 8)->nullable();
            $table->decimal('start_lng', 11, 8)->nullable();
            $table->string('start_address')->nullable();
            $table->decimal('end_lat', 10, 8)->nullable();
            $table->decimal('end_lng', 11, 8)->nullable();
            $table->string('end_address')->nullable();

            // Status
            $table->string('status')->default('draft'); // draft, assigned, completed, cancelled

            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('agent_id')->references('id')->on('delivery_agents')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            $table->index('route_code');
            $table->index('route_date');
            $table->index('agent_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_routes');
    }
};
