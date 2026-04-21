<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('employee_id');

            $table->date('attendance_date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();

            $table->enum('status', ['Present', 'Absent', 'Leave'])
                  ->default('Present');

            $table->timestamps();

            $table->unique(['employee_id', 'attendance_date']);

            // FK added AFTER column + index
            $table->foreign('employee_id')
                  ->references('id')
                  ->on('employees')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
