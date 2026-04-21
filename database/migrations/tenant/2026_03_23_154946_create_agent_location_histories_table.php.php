<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('agent_location_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id');
            $table->unsignedBigInteger('shipment_id')->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('accuracy')->nullable();
            $table->decimal('speed', 5, 2)->nullable();
            $table->integer('bearing')->nullable();
            $table->integer('battery_level')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('shipment_id')->references('id')->on('shipments')->onDelete('set null');
            $table->index(['agent_id', 'recorded_at']);
            $table->index('shipment_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('agent_location_histories');
    }
};
