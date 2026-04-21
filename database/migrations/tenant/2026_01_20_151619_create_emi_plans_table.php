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
    Schema::create('emi_plans', function (Blueprint $table) {
        $table->id();

        $table->foreignId('sale_id')->constrained()->cascadeOnDelete();

        $table->decimal('total_amount', 10, 2);
        $table->decimal('down_payment', 10, 2)->default(0);
        $table->integer('months');
        $table->decimal('emi_amount', 10, 2);

        $table->enum('status', ['running', 'completed'])->default('running');

        $table->timestamps();
        $table->softDeletes();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emi_plans');
    }
};
