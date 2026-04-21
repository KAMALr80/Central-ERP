<?php
// database/migrations/2024_01_15_000003_create_delivery_agents_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_agents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('agent_code')->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->unique();
            $table->string('alternate_phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();

            // Vehicle Details
            $table->string('vehicle_type')->nullable(); // bike, cycle, van, truck
            $table->string('vehicle_number')->nullable();
            $table->string('license_number')->nullable();

            // Documents
            $table->string('aadhar_card')->nullable();
            $table->string('driving_license')->nullable();
            $table->string('photo')->nullable();

            // Bank Details
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('upi_id')->nullable();

            // Work Details
            $table->string('employment_type')->default('full_time'); // full_time, part_time, contract
            $table->date('joining_date');
            $table->decimal('salary', 10, 2)->nullable();
            $table->string('commission_type')->nullable(); // fixed, percentage
            $table->decimal('commission_value', 10, 2)->nullable();

            // Area of Operation
            $table->json('service_areas')->nullable(); // Array of pincodes/cities
            $table->decimal('current_latitude', 10, 8)->nullable();
            $table->decimal('current_longitude', 11, 8)->nullable();
            $table->timestamp('last_location_update')->nullable();

            $table->boolean('is_active')->default(true);
            $table->string('status')->default('available'); // available, busy, offline
            $table->integer('total_deliveries')->default(0);
            $table->integer('successful_deliveries')->default(0);
            $table->decimal('rating', 3, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['city', 'status']);
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_agents');
    }
};
