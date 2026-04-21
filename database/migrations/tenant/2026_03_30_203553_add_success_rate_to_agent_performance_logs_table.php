<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agent_performance_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('agent_performance_logs', 'success_rate')) {
                $table->decimal('success_rate', 5, 2)
                      ->default(0)
                      ->after('shipments_failed')
                      ->comment('Success rate percentage (delivered/assigned * 100)');
            }
        });

        // Update existing records with calculated success_rate
        $logs = DB::table('agent_performance_logs')->get();
        foreach ($logs as $log) {
            $successRate = $log->shipments_assigned > 0
                ? ($log->shipments_delivered / $log->shipments_assigned) * 100
                : 0;

            DB::table('agent_performance_logs')
                ->where('id', $log->id)
                ->update(['success_rate' => $successRate]);
        }
    }

    public function down(): void
    {
        Schema::table('agent_performance_logs', function (Blueprint $table) {
            $table->dropColumn('success_rate');
        });
    }
};
