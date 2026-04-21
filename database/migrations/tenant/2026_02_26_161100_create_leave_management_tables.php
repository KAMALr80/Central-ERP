<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ========== 1. CREATE LEAVE BALANCES TABLE ==========
        if (!Schema::hasTable('leave_balances')) {
            Schema::create('leave_balances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id');
                $table->year('year');
                $table->enum('leave_type', [
                    'annual', 'sick', 'casual', 'maternity', 'paternity', 'bereavement', 'study'
                ])->default('annual');
                $table->decimal('entitled', 5, 2)->default(0);
                $table->decimal('used', 5, 2)->default(0);
                $table->decimal('remaining', 5, 2)->default(0);
                $table->decimal('pending', 5, 2)->default(0);
                $table->decimal('carry_forward', 5, 2)->default(0);
                $table->decimal('total_available', 5, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('employee_id')
                      ->references('id')
                      ->on('employees')
                      ->onDelete('cascade');

                $table->unique(['employee_id', 'year', 'leave_type']);
                $table->index(['employee_id', 'year']);
            });
        }

        // ========== 2. CREATE LEAVE POLICIES TABLE ==========
        if (!Schema::hasTable('leave_policies')) {
            Schema::create('leave_policies', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->enum('leave_type', [
                    'annual', 'sick', 'casual', 'unpaid', 'maternity', 'paternity', 'bereavement', 'study'
                ])->unique();
                $table->integer('days_per_year')->nullable();
                $table->enum('accrual_method', ['lump_sum', 'monthly', 'bi_weekly', 'weekly'])->default('lump_sum');
                $table->boolean('carry_forward_allowed')->default(false);
                $table->integer('max_carry_forward_days')->nullable();
                $table->integer('min_service_days')->default(0);
                $table->enum('applicable_gender', ['all', 'male', 'female'])->default('all');
                $table->boolean('is_paid')->default(true);
                $table->integer('max_consecutive_days')->nullable();
                $table->integer('min_notice_days')->default(0);
                $table->boolean('requires_approval')->default(true);
                $table->boolean('requires_document')->default(false);
                $table->boolean('requires_handover')->default(false);
                $table->boolean('is_active')->default(true);
                $table->date('effective_from')->nullable();
                $table->date('effective_to')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // ========== 3. CREATE LEAVE HOLIDAYS TABLE ==========
        if (!Schema::hasTable('leave_holidays')) {
            Schema::create('leave_holidays', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->date('date');
                $table->enum('type', ['public', 'company', 'restricted'])->default('public');
                $table->boolean('repeats_annually')->default(false);
                $table->enum('applicable_to', ['all', 'office', 'field', 'specific'])->default('all');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('date');
                $table->unique(['name', 'date']);
            });
        }

        // ========== 4. ADD NEW COLUMNS TO EXISTING LEAVES TABLE ==========
        if (Schema::hasTable('leaves')) {
            Schema::table('leaves', function (Blueprint $table) {
                // Add leave_number if not exists
                if (!Schema::hasColumn('leaves', 'leave_number')) {
                    $table->string('leave_number')->unique()->nullable()->after('id');
                }

                // Add leave_type as enum if column doesn't exist or modify existing
                if (!Schema::hasColumn('leaves', 'leave_type')) {
                    $table->enum('leave_type', [
                        'annual', 'sick', 'casual', 'unpaid', 'maternity',
                        'paternity', 'bereavement', 'study', 'half_day', 'short_leave'
                    ])->default('annual')->after('employee_id');
                }

                // Add duration_type if not exists
                if (!Schema::hasColumn('leaves', 'duration_type')) {
                    $table->enum('duration_type', ['full_day', 'half_day', 'short_leave'])->default('full_day')->after('leave_type');
                }

                // Add total_days if not exists
                if (!Schema::hasColumn('leaves', 'total_days')) {
                    $table->integer('total_days')->default(1)->after('to_date');
                }

                // Add session if not exists
                if (!Schema::hasColumn('leaves', 'session')) {
                    $table->enum('session', ['first_half', 'second_half'])->nullable()->after('total_days');
                }

                // Add start_time and end_time for short leave
                if (!Schema::hasColumn('leaves', 'start_time')) {
                    $table->time('start_time')->nullable()->after('session');
                }
                if (!Schema::hasColumn('leaves', 'end_time')) {
                    $table->time('end_time')->nullable()->after('start_time');
                }

                // Add contact_number if not exists
                if (!Schema::hasColumn('leaves', 'contact_number')) {
                    $table->string('contact_number')->nullable()->after('reason');
                }

                // Add handover_notes if not exists
                if (!Schema::hasColumn('leaves', 'handover_notes')) {
                    $table->text('handover_notes')->nullable()->after('contact_number');
                }

                // Add document_path if not exists
                if (!Schema::hasColumn('leaves', 'document_path')) {
                    $table->string('document_path')->nullable()->after('handover_notes');
                }

                // Add approved_by and related fields
                if (!Schema::hasColumn('leaves', 'approved_by')) {
                    $table->unsignedBigInteger('approved_by')->nullable()->after('status');
                    $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
                }

                if (!Schema::hasColumn('leaves', 'approved_at')) {
                    $table->timestamp('approved_at')->nullable()->after('approved_by');
                }

                if (!Schema::hasColumn('leaves', 'approval_remarks')) {
                    $table->text('approval_remarks')->nullable()->after('approved_at');
                }

                // Add rejected_by and related fields
                if (!Schema::hasColumn('leaves', 'rejected_by')) {
                    $table->unsignedBigInteger('rejected_by')->nullable()->after('approval_remarks');
                    $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
                }

                if (!Schema::hasColumn('leaves', 'rejected_at')) {
                    $table->timestamp('rejected_at')->nullable()->after('rejected_by');
                }

                if (!Schema::hasColumn('leaves', 'rejection_reason')) {
                    $table->text('rejection_reason')->nullable()->after('rejected_at');
                }

                // Add cancelled_by and related fields
                if (!Schema::hasColumn('leaves', 'cancelled_by')) {
                    $table->unsignedBigInteger('cancelled_by')->nullable()->after('rejection_reason');
                    $table->foreign('cancelled_by')->references('id')->on('users')->onDelete('set null');
                }

                if (!Schema::hasColumn('leaves', 'cancelled_at')) {
                    $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
                }

                // Add leave balance tracking
                if (!Schema::hasColumn('leaves', 'leave_balance_before')) {
                    $table->decimal('leave_balance_before', 5, 2)->nullable()->after('cancelled_at');
                }

                if (!Schema::hasColumn('leaves', 'leave_balance_after')) {
                    $table->decimal('leave_balance_after', 5, 2)->nullable()->after('leave_balance_before');
                }

                // Add applied_on if not exists
                if (!Schema::hasColumn('leaves', 'applied_on')) {
                    $table->timestamp('applied_on')->useCurrent()->after('leave_balance_after');
                }

                // Add soft deletes if not exists
                if (!Schema::hasColumn('leaves', 'deleted_at')) {
                    $table->softDeletes();
                }

                // Add indexes for better performance
                $table->index(['employee_id', 'status']);
                $table->index(['from_date', 'to_date']);
                $table->index('status');
                $table->index('applied_on');
            });
        }
    }

    public function down(): void
    {
        // Remove added columns from leaves table
        if (Schema::hasTable('leaves')) {
            Schema::table('leaves', function (Blueprint $table) {
                $columns = [
                    'leave_number', 'leave_type', 'duration_type', 'total_days', 'session',
                    'start_time', 'end_time', 'contact_number', 'handover_notes',
                    'document_path', 'approved_by', 'approved_at', 'approval_remarks',
                    'rejected_by', 'rejected_at', 'rejection_reason', 'cancelled_by',
                    'cancelled_at', 'leave_balance_before', 'leave_balance_after',
                    'applied_on', 'deleted_at'
                ];

                foreach ($columns as $column) {
                    if (Schema::hasColumn('leaves', $column)) {
                        // Drop foreign keys first
                        if (in_array($column, ['approved_by', 'rejected_by', 'cancelled_by'])) {
                            $table->dropForeign([$column]);
                        }
                        $table->dropColumn($column);
                    }
                }
            });
        }

        // Drop new tables
        Schema::dropIfExists('leave_holidays');
        Schema::dropIfExists('leave_policies');
        Schema::dropIfExists('leave_balances');
    }
};
