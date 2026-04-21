<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            // Check and add missing columns
            if (!Schema::hasColumn('purchases', 'invoice_number')) {
                $table->string('invoice_number')->unique()->after('id');
            }
            
            if (!Schema::hasColumn('purchases', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->after('product_id');
            }
            
            if (!Schema::hasColumn('purchases', 'discount')) {
                $table->decimal('discount', 5, 2)->default(0)->after('price');
            }
            
            if (!Schema::hasColumn('purchases', 'tax')) {
                $table->decimal('tax', 5, 2)->default(0)->after('discount');
            }
            
            if (!Schema::hasColumn('purchases', 'grand_total')) {
                $table->decimal('grand_total', 10, 2)->after('total');
            }
            
            if (!Schema::hasColumn('purchases', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('purchase_date');
            }
            
            if (!Schema::hasColumn('purchases', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('payment_method');
            }
            
            if (!Schema::hasColumn('purchases', 'supplier_name')) {
                $table->string('supplier_name')->nullable()->after('payment_status');
            }
            
            if (!Schema::hasColumn('purchases', 'supplier_phone')) {
                $table->string('supplier_phone')->nullable()->after('supplier_name');
            }
            
            if (!Schema::hasColumn('purchases', 'supplier_email')) {
                $table->string('supplier_email')->nullable()->after('supplier_phone');
            }
            
            if (!Schema::hasColumn('purchases', 'notes')) {
                $table->text('notes')->nullable()->after('supplier_email');
            }
            
            if (!Schema::hasColumn('purchases', 'status')) {
                $table->string('status')->default('completed')->after('notes');
            }
            
            if (!Schema::hasColumn('purchases', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $columns = [
                'invoice_number', 'user_id', 'discount', 'tax', 'grand_total',
                'payment_method', 'payment_status', 'supplier_name', 'supplier_phone',
                'supplier_email', 'notes', 'status', 'deleted_at'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('purchases', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};