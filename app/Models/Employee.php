<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use Carbon\Carbon;

class Employee extends Model
{
    use SoftDeletes, HasFactory, Auditable;

    protected $fillable = [
        'user_id',
        'employee_code',
        'name',
        'email',
        'phone',
        'department',
        'position',
        'joining_date',
        'status',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /* ==================== RELATIONSHIPS ==================== */

    /**
     * Employee belongs to a User (login account)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Employee has many attendance records
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Employee has many leave requests
     */
    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    /**
     * Employee has leave balances
     */
    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    /* ==================== HELPERS (OPTIONAL BUT USEFUL) ==================== */

    /**
     * Get today's attendance
     */
    public function todayAttendance()
    {
        return $this->attendances()
            ->where('attendance_date', today())
            ->first();
    }

    /**
     * Get attendance for a specific date
     */
    public function attendanceForDate($date)
    {
        return $this->attendances()
            ->whereDate('attendance_date', $date)
            ->first();
    }

    /**
     * Get current month's attendance
     */
    public function currentMonthAttendance()
    {
        return $this->attendances()
            ->whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)
            ->get();
    }

    /**
     * Get pending leaves
     */
    public function pendingLeaves()
    {
        return $this->leaves()->where('status', 'Pending');
    }

    /**
     * Get approved leaves
     */
    public function approvedLeaves()
    {
        return $this->leaves()->where('status', 'Approved');
    }

    /* ==================== ACCESSORS ==================== */

    /**
     * Get full name with employee code
     */
    public function getDisplayNameAttribute()
    {
        return $this->name . ' (' . $this->employee_code . ')';
    }

    /**
     * Get attendance percentage for current month
     */
    public function getMonthlyAttendancePercentageAttribute()
    {
        $totalDays = now()->daysInMonth;
        $presentDays = $this->attendances()
            ->whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)
            ->whereIn('status', ['present', 'Present'])
            ->count();

        return $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;
    }

    /**
     * Get total present days this month
     */
    public function getPresentDaysThisMonthAttribute()
    {
        return $this->attendances()
            ->whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)
            ->whereIn('status', ['present', 'Present'])
            ->count();
    }

    /**
     * Get total absent days this month
     */
    public function getAbsentDaysThisMonthAttribute()
    {
        return $this->attendances()
            ->whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)
            ->whereIn('status', ['absent', 'Absent'])
            ->count();
    }

    /**
     * Get total late days this month
     */
    public function getLateDaysThisMonthAttribute()
    {
        return $this->attendances()
            ->whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)
            ->whereIn('status', ['late', 'Late'])
            ->count();
    }

    /**
     * Get joining date formatted
     */
    public function getFormattedJoiningDateAttribute()
    {
        return $this->joining_date ? $this->joining_date->format('d M, Y') : 'N/A';
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'danger',
            'suspended' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Check if employee is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Get leave balance for a specific leave type
     */
    public function getLeaveBalance($leaveType, $year = null)
    {
        $year = $year ?? now()->year;

        return $this->leaveBalances()
            ->where('leave_type', $leaveType)
            ->where('year', $year)
            ->first();
    }

    /**
     * Get total leave balance for current year
     */
    public function getTotalLeaveBalanceAttribute()
    {
        $balances = $this->leaveBalances()
            ->where('year', now()->year)
            ->get();

        return [
            'annual' => $balances->where('leave_type', 'annual')->first()?->remaining ?? 0,
            'sick' => $balances->where('leave_type', 'sick')->first()?->remaining ?? 0,
            'casual' => $balances->where('leave_type', 'casual')->first()?->remaining ?? 0,
        ];
    }

    /* ==================== SCOPES ==================== */

    /**
     * Scope active employees
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope inactive employees
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope by department
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Scope employees joined after date
     */
    public function scopeJoinedAfter($query, $date)
    {
        return $query->whereDate('joining_date', '>=', $date);
    }

    /**
     * Scope employees joined before date
     */
    public function scopeJoinedBefore($query, $date)
    {
        return $query->whereDate('joining_date', '<=', $date);
    }

    /**
     * Scope employees with pending leaves
     */
    public function scopeWithPendingLeaves($query)
    {
        return $query->whereHas('leaves', function($q) {
            $q->where('status', 'Pending');
        });
    }

    /* ==================== STATIC METHODS ==================== */

    /**
     * Generate unique employee code
     */
    public static function generateEmployeeCode()
    {
        $prefix = 'EMP';
        $year = date('Y');
        $lastEmployee = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastEmployee && $lastEmployee->employee_code) {
            $lastNumber = intval(substr($lastEmployee->employee_code, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $year . $newNumber;
    }

    /* ==================== BOOT ==================== */

    protected static function boot()
    {
        parent::boot();

        // Auto-generate employee code before creating
        static::creating(function ($employee) {
            if (empty($employee->employee_code)) {
                $employee->employee_code = self::generateEmployeeCode();
            }
        });

        // Log when employee is created
        static::created(function ($employee) {
            Log::info("New employee created: {$employee->name} ({$employee->employee_code})");
        });
    }
}
