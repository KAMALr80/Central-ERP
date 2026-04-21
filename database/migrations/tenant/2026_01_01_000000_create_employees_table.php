<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->bigIncrements('id');

            // User mapping
            $table->unsignedBigInteger('user_id')->nullable();

            // Employee details
            $table->string('employee_code')->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('department')->nullable();
            $table->date('joining_date')->nullable();
            $table->boolean('status')->default(1);

            $table->timestamps();

            // Foreign key
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
