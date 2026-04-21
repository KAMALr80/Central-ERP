<?php
// database/migrations/2026_03_19_000004_create_courier_shipments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shipment_id');
            $table->unsignedBigInteger('courier_partner_id');
            $table->string('courier_tracking_number')->nullable();
            $table->string('courier_awb_number')->nullable();

            // Labels and Documents
            $table->string('label_url')->nullable();
            $table->string('manifest_url')->nullable();
            $table->string('invoice_url')->nullable();

            // Charges
            $table->decimal('courier_charge', 10, 2)->nullable();
            $table->decimal('fuel_surcharge', 10, 2)->nullable();
            $table->decimal('cod_charge', 10, 2)->nullable();
            $table->decimal('total_courier_charge', 10, 2)->nullable();

            // Pickup Details
            $table->timestamp('pickup_scheduled_at')->nullable();
            $table->timestamp('pickup_actual_at')->nullable();
            $table->string('pickup_status')->nullable();

            // Delivery Details
            $table->timestamp('delivery_estimated_at')->nullable();
            $table->timestamp('delivery_actual_at')->nullable();
            $table->string('delivery_status')->nullable();

            // API Response
            $table->json('api_request')->nullable();
            $table->json('api_response')->nullable();

            $table->string('status')->default('pending');
            $table->timestamps();

            $table->foreign('shipment_id')->references('id')->on('shipments')->onDelete('cascade');
            $table->foreign('courier_partner_id')->references('id')->on('courier_partners')->onDelete('cascade');

            $table->index('courier_tracking_number');
            $table->index('status');
            $table->unique(['shipment_id', 'courier_partner_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_shipments');
    }
};
