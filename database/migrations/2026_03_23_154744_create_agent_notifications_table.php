<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('agent_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id');
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('system');
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['agent_id', 'is_read']);
            $table->index('sent_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('agent_notifications');
    }
};
