<?php
// database/migrations/xxxx_xx_xx_add_wallet_balance_to_customers.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('wallet_balance', 10, 2)->default(0)->after('open_balance');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('wallet_balance');
        });
    }
};
