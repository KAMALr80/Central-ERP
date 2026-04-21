<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1️⃣ Create sales table
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('customer_id')->nullable();

            $table->string('invoice_no')->unique();
            $table->date('sale_date');

            $table->decimal('sub_total', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2);

            $table->timestamps();
            $table->softDeletes();
        });

        // 2️⃣ Add foreign key separately (FIX for errno 150)
        Schema::table('sales', function (Blueprint $table) {
            $table->foreign('customer_id')
                  ->references('id')
                  ->on('customers')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
