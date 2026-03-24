<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('delivery_agents', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_agents', 'approval_status')) {
                $table->string('approval_status')->default('approved')->after('status');
            }
            if (!Schema::hasColumn('delivery_agents', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approval_status');
            }
            if (!Schema::hasColumn('delivery_agents', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('delivery_agents', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('approved_at');
            }
        });
    }

    public function down()
    {
        Schema::table('delivery_agents', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'approved_by', 'approved_at', 'rejection_reason']);
        });
    }
};
