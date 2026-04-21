<?php
// database/migrations/2024_01_15_000002_create_shipment_trackings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_trackings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shipment_id');
            $table->string('status');
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('remarks')->nullable();
            $table->string('updated_by')->nullable(); // system, delivery_boy, courier_api
            $table->timestamp('tracked_at');
            $table->timestamps();

            $table->foreign('shipment_id')->references('id')->on('shipments')->onDelete('cascade');
            $table->index(['shipment_id', 'tracked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_trackings');
    }
};
