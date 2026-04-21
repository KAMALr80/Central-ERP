<?php
// database/migrations/2024_xx_xx_xxxxxx_add_image_to_products_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Agar description column exist karta hai toh uske baad add karo
            if (Schema::hasColumn('products', 'description')) {
                $table->string('image')->nullable()->after('description');
            } else {
                // Agar description column nahi hai toh category ke baad add karo
                $table->string('image')->nullable()->after('category');
            }
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
