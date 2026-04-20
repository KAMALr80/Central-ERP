@extends('layouts.app')

@section('page-title', 'Mark Attendance')

@section('content')
<style>
    :root {
        --primary: #4f46e5;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --info: #8b5cf6;
        --gray-light: #f9fafb;
        --gray-border: #e5e7eb;
    }

    .mark-page {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid var(--gray-border);
        transition: transform 0.2s;
    }

    .stat-card:hover { transform: translateY(-2px); }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 12px;
    }

    .stat-label { font-size: 13px; color: #6b7280; margin-bottom: 5px; }
    .stat-value { font-size: 32px; font-weight: 800; color: #1f2937; }
    .stat-sub { font-size: 12px; color: #6b7280; margin-top: 5px; }

    /* Work Timing Info */
    .timing-info {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        border-radius: 16px;
        padding: 20px 25px;
        margin-bottom: 25px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .timing-item { display: flex; align-items: center; gap: 12px; }
    .timing-icon { font-size: 24px; }
    .timing-text small { display: block; opacity: 0.8; font-size: 11px; }
    .timing-text strong { font-size: 16px; }

    /* Table Styles */
    .table-container {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow-x: auto;
    }

    .attendance-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 800px;
    }

    .attendance-table th {
        text-align: left;
        padding: 15px 12px;
        background: var(--gray-light);
        font-weight: 600;
        font-size: 13px;
        color: #4b5563;
        border-bottom: 2px solid var(--gray-border);
    }

    .attendance-table td {
        padding: 15px 12px;
        border-bottom: 1px solid var(--gray-border);
        vertical-align: middle;
    }

    .employee-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .employee-avatar {
        width: 40px;
        height: 40px;
        background: var(--primary);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-present { background: #d1fae5; color: #065f46; }
    .status-absent { background: #fee2e2; color: #991b1b; }
    .status-late { background: #fed7aa; color: #92400e; }
    .status-leave { background: #e0e7ff; color: #3730a3; }

    .status-select {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid var(--gray-border);
        font-size: 13px;
        cursor: pointer;
        width: 130px;
    }

    .remarks-input {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid var(--gray-border);
        font-size: 13px;
        width: 100%;
        min-width: 150px;
    }

    .btn-primary, .btn-secondary {
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-primary {
        background: var(--success);
        color: white;
    }

    .btn-primary:hover {
        background: #059669;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: #6b7280;
        color: white;
    }

    .btn-secondary:hover {
        background: #4b5563;
    }

    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr; }
        .timing-info { flex-direction: column; align-items: flex-start; }
    }
</style>

<div class="mark-page">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 style="margin: 0; font-size: 28px;">📝 Mark Attendance</h1>
            <p style="margin: 5px 0 0; color: #6b7280;">{{ now()->format('l, F j, Y') }}</p>
        </div>
        <div style="display: flex; gap: 12px;">
            @if (auth()->user()->hasPermission('view_attendance'))
                <a href="{{ route('attendance.manage') }}" class="btn-secondary">
                    📋 Manage Records
                </a>
            @endif
            @if (auth()->user()->hasPermission('mark_attendance'))
                <button onclick="submitBulkAttendance()" class="btn-primary" id="saveBtn">
                    💾 Save All Changes
                </button>
            @endif
        </div>
    </div>

    <!-- Work Timing Info -->
    <div class="timing-info">
        <div class="timing-item">
            <span class="timing-icon">⏰</span>
            <div class="timing-text">
                <small>Work Hours</small>
                <strong>3:00 PM → 12:00 AM</strong>
            </div>
        </div>
        <div class="timing-item">
            <span class="timing-icon">🍽️</span>
            <div class="timing-text">
                <small>Break (Dinner + Tea)</small>
                <strong>45 min + 15 min = 1 hour</strong>
            </div>
        </div>
        <div class="timing-item">
            <span class="timing-icon">🎯</span>
            <div class="timing-text">
                <small>Net Working Hours</small>
                <strong>9 hours required</strong>
            </div>
        </div>
        <div class="timing-item">
            <span class="timing-icon">⏱️</span>
            <div class="timing-text">
                <small>Grace Period</small>
                <strong>15 minutes (until 3:15 PM)</strong>
            </div>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #d1fae5;">✅</div>
            <div class="stat-label">Present</div>
            <div class="stat-value">{{ $attendanceCounts['present'] }}</div>
            <div class="stat-sub">On time</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #fed7aa;">⏰</div>
            <div class="stat-label">Late</div>
            <div class="stat-value">{{ $attendanceCounts['late'] }}</div>
            <div class="stat-sub">After 3:15 PM</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #fee2e2;">❌</div>
            <div class="stat-label">Absent</div>
            <div class="stat-value">{{ $attendanceCounts['absent'] }}</div>
            <div class="stat-sub">No check-in</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #e0e7ff;">🏖️</div>
            <div class="stat-label">Leave</div>
            <div class="stat-value">{{ $attendanceCounts['leave'] ?? 0 }}</div>
            <div class="stat-sub">Approved leave</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #fef3c7;">⏳</div>
            <div class="stat-label">Pending</div>
            <div class="stat-value">{{ $attendanceCounts['pending'] }}</div>
            <div class="stat-sub">Not marked yet</div>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="table-container">
        <form id="bulkAttendanceForm">
            @csrf
            <input type="hidden" name="attendance_date" value="{{ now()->format('Y-m-d') }}">

            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Current Status</th>
                        <th>Mark Status</th>
                        <th>Check In Time</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $index => $employee)
                        @php
                            $todayAttendance = $todayAttendance[$employee->id] ?? null;
                            $currentStatus = $todayAttendance ? $todayAttendance->status : 'Not Marked';
                            $currentCheckIn = $todayAttendance ? $todayAttendance->check_in : null;

                            $statusClass = match(strtolower($currentStatus)) {
                                'present' => 'status-present',
                                'absent' => 'status-absent',
                                'late' => 'status-late',
                                'leave' => 'status-leave',
                                default => ''
                            };
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="employee-cell">
                                    <div class="employee-avatar">
                                        {{ strtoupper(substr($employee->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 600;">{{ $employee->name }}</div>
                                        <div style="font-size: 12px; color: #6b7280;">{{ $employee->employee_code ?? 'EMP-'.$employee->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="background: #f3f4f6; padding: 4px 10px; border-radius: 12px; font-size: 12px;">
                                    {{ $employee->department ?? 'General' }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge {{ $statusClass }}">
                                    {{ $currentStatus }}
                                </span>
                            </td>
                            <td>
                                <select name="attendances[{{ $index }}][status]" class="status-select" data-employee="{{ $employee->name }}">
                                    <option value="present" {{ $currentStatus == 'Present' ? 'selected' : '' }}>✅ Present</option>
                                    <option value="absent" {{ $currentStatus == 'Absent' ? 'selected' : '' }}>❌ Absent</option>
                                    <option value="late" {{ $currentStatus == 'Late' ? 'selected' : '' }}>⏰ Late</option>
                                    <option value="leave" {{ $currentStatus == 'Leave' ? 'selected' : '' }}>🏖️ Leave</option>
                                </select>
                                <input type="hidden" name="attendances[{{ $index }}][employee_id]" value="{{ $employee->id }}">
                            </td>
                            <td>
                                <input type="time" name="attendances[{{ $index }}][check_in]" class="remarks-input"
                                       style="width: 100px;" value="{{ $currentCheckIn ? \Carbon\Carbon::parse($currentCheckIn)->format('H:i') : '' }}"
                                       placeholder="15:00">
                            </td>
                            <td>
                                <input type="text" name="attendances[{{ $index }}][remarks]" class="remarks-input"
                                       placeholder="Optional remarks" value="{{ $todayAttendance->remarks ?? '' }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
    </div>
</div>

<script>
function submitBulkAttendance() {
    const form = document.getElementById('bulkAttendanceForm');
    const formData = new FormData(form);
    const saveBtn = document.getElementById('saveBtn');

    // Collect attendance data
    const attendances = [];
    const employeeIds = document.querySelectorAll('input[name*="[employee_id]"]');

    employeeIds.forEach((input, idx) => {
        const statusSelect = document.querySelector(`select[name="attendances[${idx}][status]"]`);
        const checkInInput = document.querySelector(`input[name="attendances[${idx}][check_in]"]`);
        const remarksInput = document.querySelector(`input[name="attendances[${idx}][remarks]"]`);

        if (statusSelect) {
            attendances.push({
                employee_id: input.value,
                status: statusSelect.value,
                check_in: checkInInput ? checkInInput.value : null,
                remarks: remarksInput ? remarksInput.value : null
            });
        }
    });

    const data = {
        attendances: attendances,
        attendance_date: document.querySelector('input[name="attendance_date"]').value,
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };

    // Disable button and show loading
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '⏳ Saving...';
    saveBtn.disabled = true;

    // Submit via AJAX
    fetch('{{ route('attendance.bulk') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': data._token,
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showToast('✅ ' + result.message, 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast('❌ Error: ' + (result.message || 'Failed to save'), 'error');
        }
    })
    .catch(error => {
        showToast('❌ Network error: ' + error.message, 'error');
    })
    .finally(() => {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    });
}

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed; bottom: 20px; right: 20px;
        padding: 12px 20px; border-radius: 10px;
        background: ${type === 'success' ? '#10b981' : '#ef4444'};
        color: white; font-weight: 600; z-index: 9999;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease;
    `;
    toast.innerHTML = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Add animation style
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
`;
document.head.appendChild(style);
</script>
@endsection
