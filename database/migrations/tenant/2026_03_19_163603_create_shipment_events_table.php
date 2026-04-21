<?php
// database/migrations/2026_03_19_000002_create_shipment_events_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shipment_id');
            $table->string('event_type'); // created, assigned, picked, in_transit, out_for_delivery, delivered, failed, returned, cancelled
            $table->string('status_from')->nullable();
            $table->string('status_to');
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Additional data like agent_id, courier_info etc.
            $table->string('triggered_by')->nullable(); // system, user, agent, courier_api
            $table->unsignedBigInteger('triggered_by_id')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->foreign('shipment_id')->references('id')->on('shipments')->onDelete('cascade');
            $table->index(['shipment_id', 'occurred_at']);
            $table->index('event_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_events');
    }
};
