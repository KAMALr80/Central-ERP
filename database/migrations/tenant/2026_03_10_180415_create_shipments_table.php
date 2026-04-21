<?php
// database/migrations/2024_01_15_000001_create_shipments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number')->unique();
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('assigned_to')->nullable(); // Delivery boy

            // Shipping Details
            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->string('receiver_alternate_phone')->nullable();
            $table->text('shipping_address');
            $table->string('landmark')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('pincode');
            $table->string('country')->default('India');

            // Package Details
            $table->decimal('weight', 10, 2)->nullable(); // in kg
            $table->decimal('length', 10, 2)->nullable(); // in cm
            $table->decimal('width', 10, 2)->nullable(); // in cm
            $table->decimal('height', 10, 2)->nullable(); // in cm
            $table->integer('quantity')->default(1);
            $table->decimal('declared_value', 15, 2)->default(0);
            $table->string('package_type')->default('box'); // box, envelope, pallet

            // Shipping Method
            $table->string('shipping_method'); // standard, express, overnight
            $table->string('courier_partner')->nullable(); // delhivery, bluedart, dtdc, etc.
            $table->string('tracking_number')->nullable();
            $table->string('awb_number')->nullable(); // Air Waybill Number

            // Charges
            $table->decimal('shipping_charge', 15, 2)->default(0);
            $table->decimal('cod_charge', 15, 2)->default(0); // Cash on Delivery charge
            $table->decimal('insurance_charge', 15, 2)->default(0);
            $table->decimal('total_charge', 15, 2)->default(0);
            $table->string('payment_mode')->default('prepaid'); // prepaid, cod

            // Status
            $table->string('status')->default('pending'); // pending, picked, in_transit, out_for_delivery, delivered, failed, returned
            $table->text('status_note')->nullable();
            $table->timestamp('pickup_date')->nullable();
            $table->timestamp('estimated_delivery_date')->nullable();
            $table->timestamp('actual_delivery_date')->nullable();

            // Proof of Delivery
            $table->string('pod_signature')->nullable(); // Signature image path
            $table->string('pod_photo')->nullable(); // Delivery photo
            $table->text('delivery_notes')->nullable();

            // Tracking
            $table->decimal('current_latitude', 10, 8)->nullable();
            $table->decimal('current_longitude', 11, 8)->nullable();
            $table->timestamp('last_location_update')->nullable();

            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('assigned_to')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            // Indexes
            $table->index('shipment_number');
            $table->index('tracking_number');
            $table->index('status');
            $table->index('courier_partner');
            $table->index(['city', 'state']);
            $table->index('estimated_delivery_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
