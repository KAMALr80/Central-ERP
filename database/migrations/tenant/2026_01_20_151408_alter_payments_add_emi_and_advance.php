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
    Schema::table('payments', function (Blueprint $table) {

        // customer reference (advance wallet ke liye)
        $table->foreignId('customer_id')
              ->nullable()
              ->after('id')
              ->constrained()
              ->cascadeOnDelete();

        // EMI & Advance support
        $table->enum('method', [
            'cash',
            'upi',
            'card',
            'net_banking',
            'advance',
            'emi'
        ])->change();

        // sale_id nullable (advance payment invoice se pehle ho sakta hai)
        $table->foreignId('sale_id')->nullable()->change();

        $table->string('remarks')->nullable()->after('transaction_id');
    });
}

public function down(): void
{
    Schema::table('payments', function (Blueprint $table) {
        $table->dropColumn(['customer_id', 'remarks']);
    });
}

};
