<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('customers', function (Blueprint $table) {
        $table->decimal('open_balance', 12, 2)
              ->default(0)
              ->after('address');
    });
}

public function down()
{
    Schema::table('customers', function (Blueprint $table) {
        $table->dropColumn('open_balance');
    });
}
};
