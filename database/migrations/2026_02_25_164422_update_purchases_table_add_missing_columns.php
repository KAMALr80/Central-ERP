<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Check what columns exist using Laravel Schema methods
        Schema::table('purchases', function (Blueprint $table) {

            // Add invoice_number if missing
            if (!Schema::hasColumn('purchases', 'invoice_number')) {
                $table->string('invoice_number')->unique()->after('id');
            }

            // Add user_id if missing
            if (!Schema::hasColumn('purchases', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->after('product_id');
            }

            // Add discount if missing
            if (!Schema::hasColumn('purchases', 'discount')) {
                $table->decimal('discount', 5, 2)->default(0)->after('price');
            }

            // Add tax if missing
            if (!Schema::hasColumn('purchases', 'tax')) {
                $table->decimal('tax', 5, 2)->default(0)->after('discount');
            }

            // Add grand_total if missing
            if (!Schema::hasColumn('purchases', 'grand_total')) {
                $table->decimal('grand_total', 10, 2)->after('total');
            }

            // Add payment_method if missing
            if (!Schema::hasColumn('purchases', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('purchase_date');
            }

            // Add payment_status if missing
            if (!Schema::hasColumn('purchases', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('payment_method');
            }

            // Add supplier_name if missing
            if (!Schema::hasColumn('purchases', 'supplier_name')) {
                $table->string('supplier_name')->nullable()->after('payment_status');
            }

            // Add supplier_phone if missing
            if (!Schema::hasColumn('purchases', 'supplier_phone')) {
                $table->string('supplier_phone')->nullable()->after('supplier_name');
            }

            // Add supplier_email if missing
            if (!Schema::hasColumn('purchases', 'supplier_email')) {
                $table->string('supplier_email')->nullable()->after('supplier_phone');
            }

            // Add notes if missing
            if (!Schema::hasColumn('purchases', 'notes')) {
                $table->text('notes')->nullable()->after('supplier_email');
            }

            // Add status if missing
            if (!Schema::hasColumn('purchases', 'status')) {
                $table->string('status')->default('completed')->after('notes');
            }

            // Add deleted_at for soft deletes if missing
            if (!Schema::hasColumn('purchases', 'deleted_at')) {
                $table->softDeletes();
            }

            // Add timestamps if missing (both created_at and updated_at)
            if (!Schema::hasColumn('purchases', 'created_at')) {
                $table->timestamps();
            }
        });

        // Update existing records with default invoice numbers if the column was just added
        if (!Schema::hasColumn('purchases', 'invoice_number')) {
            // Get all purchases with null invoice_number
            $purchases = \DB::table('purchases')->whereNull('invoice_number')->get();

            foreach ($purchases as $purchase) {
                $invoiceNumber = 'INV-' . date('Y') . date('m') . '-' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT);
                \DB::table('purchases')
                    ->where('id', $purchase->id)
                    ->update(['invoice_number' => $invoiceNumber]);
            }
        }
    }

    public function down()
    {
        // Remove added columns if needed (optional)
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
