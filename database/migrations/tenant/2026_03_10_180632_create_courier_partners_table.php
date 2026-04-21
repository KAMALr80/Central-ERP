<?php
// database/migrations/2024_01_15_000004_create_courier_partners_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Delhivery, BlueDart, DTDC, etc.
            $table->string('code')->unique();
            $table->string('api_url')->nullable();
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->json('api_config')->nullable(); // Additional config

            // Rate Card
            $table->json('rate_card')->nullable(); // Weight based pricing
            $table->json('cod_charges')->nullable(); // COD charges
            $table->json('serviceable_pincodes')->nullable(); // List of pincodes

            $table->json('supported_services')->nullable(); // ['standard', 'express', 'overnight']
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // For auto-selection

            $table->timestamps();
            $table->softDeletes(); // ✅ YEH LINE ADD KARO - for soft delete support

            $table->index('code');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_partners');
    }
};
