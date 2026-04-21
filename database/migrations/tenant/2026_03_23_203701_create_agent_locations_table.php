<?php
// database/migrations/2026_03_23_203500_create_agent_locations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('agent_locations')) {
            Schema::create('agent_locations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('agent_id');
                $table->unsignedBigInteger('shipment_id')->nullable();
                $table->decimal('latitude', 10, 8);
                $table->decimal('longitude', 11, 8);
                $table->decimal('accuracy', 8, 2)->nullable();
                $table->decimal('speed', 8, 2)->nullable();
                $table->integer('heading')->nullable();
                $table->integer('battery_level')->nullable();
                $table->timestamp('recorded_at');
                $table->timestamps();

                $table->foreign('agent_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('shipment_id')->references('id')->on('shipments')->onDelete('set null');
                $table->index(['agent_id', 'recorded_at']);
                $table->index('shipment_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('agent_locations');
    }
};
