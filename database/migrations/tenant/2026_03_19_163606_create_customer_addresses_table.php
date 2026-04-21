<?php
// database/migrations/2026_03_19_000006_create_customer_addresses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('address_type')->default('shipping'); // home, office, other
            $table->string('name')->nullable();
            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->string('alternate_phone')->nullable();
            $table->text('address_line1');
            $table->text('address_line2')->nullable();
            $table->string('landmark')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('pincode');
            $table->string('country')->default('India');

            // Coordinates
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('place_id')->nullable();

            $table->boolean('is_default')->default(false);
            $table->text('delivery_instructions')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->index(['customer_id', 'is_default']);
            $table->index(['city', 'pincode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};
