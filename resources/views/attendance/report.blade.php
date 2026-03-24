@extends('layouts.app')

@section('page-title', 'Attendance Report')

@section('content')
<style>
    /* ================= PROFESSIONAL DESIGN SYSTEM ================= */
    :root {
        --primary: #007bff;
        --primary-dark: #0056b3;
        --success: #28a745;
        --success-dark: #218838;
        --danger: #dc3545;
        --danger-dark: #c82333;
        --warning: #ffc107;
        --warning-dark: #e0a800;
        --info: #17a2b8;
        --text-main: #2c3e50;
        --text-muted: #6b7280;
        --border: #ddd;
        --bg-light: #f8f9fa;
        --bg-white: #ffffff;
        --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
        --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
        --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
        --radius-sm: 4px;
        --radius-md: 6px;
        --radius-lg: 8px;
        --radius-xl: 12px;
        --font-sans: 'Segoe UI', Arial, -apple-system, BlinkMacSystemFont, sans-serif;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: var(--font-sans);
        background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
        color: var(--text-main);
        line-height: 1.5;
    }

    .report-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
        padding: clamp(16px, 3vw, 30px);
        width: 100%;
    }

    .container {
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
    }

    /* ================= HEADER CARD ================= */
    .header-card {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: var(--radius-xl);
        padding: clamp(20px, 4vw, 30px);
        margin-bottom: 30px;
        box-shadow: var(--shadow-lg);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .header-icon {
        width: clamp(50px, 8vw, 60px);
        height: clamp(50px, 8vw, 60px);
        background: rgba(255, 255, 255, 0.2);
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: clamp(24px, 4vw, 30px);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .header-title h1 {
        margin: 0;
        font-size: clamp(24px, 5vw, 28px);
        font-weight: 700;
        color: white;
    }

    .header-title p {
        margin: 5px 0 0 0;
        font-size: clamp(14px, 3vw, 16px);
        opacity: 0.9;
    }

    .header-actions {
        display: flex;
        gap: 10px;
    }

    .btn-header {
        padding: 8px 16px;
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 30px;
        color: white;
        font-weight: 600;
        font-size: 13px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        backdrop-filter: blur(10px);
        transition: 0.3s;
    }

    .btn-header:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-2px);
    }

    /* ================= FILTER CARD ================= */
    .filter-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border);
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: flex-end;
    }

    .filter-item {
        flex: 1 1 200px;
    }

    .filter-item label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        margin-bottom: 5px;
        display: block;
    }

    .filter-input, .filter-select {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 14px;
    }

    .filter-btn {
        padding: 10px 25px;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
        height: 42px;
    }

    .filter-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
    }

    .reset-btn {
        background: #6c757d;
    }

    .reset-btn:hover {
        background: #5a6268;
    }

    /* ================= SUMMARY CARDS ================= */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .summary-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
        text-align: center;
    }

    .summary-label {
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 8px;
        text-transform: uppercase;
    }

    .summary-value {
        font-size: 32px;
        font-weight: 700;
        color: var(--primary);
    }

    .summary-sub {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 5px;
    }

    /* ================= TABLE CARD ================= */
    .table-card {
        background: white;
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .table-header {
        padding: 20px 25px;
        background: linear-gradient(135deg, var(--bg-light), #e9ecef);
        border-bottom: 2px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .table-title {
        font-size: 18px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .table-title span {
        width: 6px;
        height: 24px;
        background: var(--primary);
        border-radius: 3px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

    .report-table th {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        padding: 15px 12px;
        font-size: 14px;
        font-weight: 600;
        text-align: left;
        border: 1px solid var(--primary-dark);
    }

    .report-table td {
        padding: 12px;
        border: 1px solid var(--border);
        font-size: 14px;
        vertical-align: middle;
    }

    .report-table tbody tr:hover td {
        background: #f1f9ff;
    }

    .employee-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .employee-avatar {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-present { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .status-absent  { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .status-late    { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
    .status-halfday { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
    .status-leave   { background: #cce5ff; color: #004085; border: 1px solid #b8daff; }

    /* ================= PAGINATION ================= */
    .pagination {
        margin-top: 20px;
        display: flex;
        justify-content: center;
        gap: 5px;
    }

    .page-link {
        padding: 8px 12px;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        color: var(--text-main);
        text-decoration: none;
        background: white;
        transition: 0.2s;
    }

    .page-link:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .active .page-link {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    /* ================= EXPORT MODAL ================= */
    .modal {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .modal.show { display: flex; }
    .modal-content {
        background: white;
        border-radius: var(--radius-lg);
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: var(--shadow-lg);
    }
    .modal-header {
        padding: 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-title { font-size: 18px; font-weight: 700; }
    .modal-close {
        background: none; border: none; font-size: 24px; cursor: pointer;
        color: var(--text-muted);
    }
    .modal-body { padding: 20px; }
    .modal-footer {
        padding: 20px;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .export-options {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-top: 15px;
    }
    .export-option {
        padding: 15px;
        border: 2px solid var(--border);
        border-radius: var(--radius-md);
        text-align: center;
        cursor: pointer;
        transition: 0.3s;
    }
    .export-option:hover {
        border-color: var(--primary);
        background: #f0f7ff;
    }
    .export-option i { font-size: 24px; display: block; margin-bottom: 8px; }
</style>

<div class="report-page">
    <div class="container">
        <!-- Header Card -->
        <div class="header-card">
            <div class="header-left">
                <div class="header-icon">📊</div>
                <div class="header-title">
                    <h1>Attendance Report</h1>
                    <p>Monthly attendance summary and details</p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('attendance.manage') }}" class="btn-header">
                    <span>📋</span> Manage
                </a>
                <button class="btn-header" onclick="openExportModal()">
                    <span>📥</span> Export
                </button>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="filter-card">
            <form method="GET" action="{{ route('attendance.report') }}" style="display: flex; flex-wrap: wrap; gap: 15px; width: 100%;">
                <div class="filter-item">
                    <label>Month</label>
                    <input type="month" name="month" class="filter-input" value="{{ $month ?? date('Y-m') }}">
                </div>
                <div class="filter-item">
                    <label>Employee</label>
                    <select name="employee_id" class="filter-select">
                        <option value="">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ ($employeeId ?? '') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }} ({{ $emp->employee_code ?? $emp->id }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; gap: 10px; align-items: flex-end;">
                    <button type="submit" class="filter-btn">Apply Filter</button>
                    <a href="{{ route('attendance.report') }}" class="filter-btn reset-btn">Reset</a>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-label">Total Days</div>
                <div class="summary-value">{{ $summary['total_days'] }}</div>
                <div class="summary-sub">in selected month</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Present</div>
                <div class="summary-value">{{ $summary['present'] }}</div>
                <div class="summary-sub">{{ $summary['total_days'] > 0 ? round(($summary['present']/$summary['total_days'])*100) : 0 }}%</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Absent</div>
                <div class="summary-value">{{ $summary['absent'] }}</div>
                <div class="summary-sub">{{ $summary['total_days'] > 0 ? round(($summary['absent']/$summary['total_days'])*100) : 0 }}%</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Late</div>
                <div class="summary-value">{{ $summary['late'] }}</div>
                <div class="summary-sub">{{ $summary['total_days'] > 0 ? round(($summary['late']/$summary['total_days'])*100) : 0 }}%</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Half Day</div>
                <div class="summary-value">{{ $summary['half_day'] }}</div>
                <div class="summary-sub">{{ $summary['total_days'] > 0 ? round(($summary['half_day']/$summary['total_days'])*100) : 0 }}%</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Leave</div>
                <div class="summary-value">{{ $summary['leave'] }}</div>
                <div class="summary-sub">{{ $summary['total_days'] > 0 ? round(($summary['leave']/$summary['total_days'])*100) : 0 }}%</div>
            </div>
        </div>

        <!-- Report Table -->
        <div class="table-card">
            <div class="table-header">
                <h3 class="table-title">
                    <span></span> Detailed Attendance for {{ \Carbon\Carbon::parse($month)->format('F Y') }}
                </h3>
                <div>Total Records: {{ $attendances->count() }}</div>
            </div>
            <div class="table-responsive">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Working Hours</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $__col = $attendances; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $att)
                            @php
                                $statusLower = strtolower($att->status);
                                $badgeClass = match($statusLower) {
                                    'present' => 'status-present',
                                    'absent'  => 'status-absent',
                                    'late'    => 'status-late',
                                    'half day'=> 'status-halfday',
                                    'leave'   => 'status-leave',
                                    default   => ''
                                };
                            @endphp
                            <tr>
                                <td>
                                    <div class="employee-info">
                                        <div class="employee-avatar">
                                            {{ strtoupper(substr($att->employee->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div>{{ $att->employee->name }}</div>
                                            <small>{{ $att->employee->employee_code ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($att->attendance_date)->format('d M Y') }}</td>
                                <td>
                                    <span class="status-badge {{ $badgeClass }}">
                                        @if($statusLower == 'present') ✅
                                        @elseif($statusLower == 'absent') ❌
                                        @elseif($statusLower == 'late') ⏰
                                        @elseif($statusLower == 'half day') ⚠️
                                        @elseif($statusLower == 'leave') 🏖️
                                        @endif
                                        {{ $att->status }}
                                    </span>
                                </td>
                                <td>{{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('h:i A') : '-' }}</td>
                                <td>{{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('h:i A') : '-' }}</td>
                                <td>{{ $att->safe_working_hours }}</td>
                                <td>{{ $att->remarks ?? '-' }}</td>
                            </tr>
                        @endforeach
@else
                            <tr><td colspan="7" style="text-align:center; padding:40px;">No attendance records for this period.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination (if needed) -->
        @if(method_exists($attendances, 'links'))
            <div class="pagination">
                {{ $attendances->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Export Modal -->
<div class="modal" id="exportModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Export Report</h3>
            <button class="modal-close" onclick="closeModal('exportModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="GET" action="{{ route('attendance.export') }}" id="exportForm">
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="employee_id" value="{{ $employeeId ?? '' }}">
                <p>Select format:</p>
                <div class="export-options">
                    <div class="export-option" onclick="setExportFormat('csv')">
                        <i>📊</i> CSV
                    </div>
                    <div class="export-option" onclick="setExportFormat('excel')">
                        <i>📈</i> Excel
                    </div>
                    <div class="export-option" onclick="setExportFormat('pdf')">
                        <i>📑</i> PDF
                    </div>
                </div>
                <input type="hidden" name="format" id="exportFormat">
            </form>
        </div>
        <div class="modal-footer">
            <button class="filter-btn reset-btn" onclick="closeModal('exportModal')">Cancel</button>
            <button class="filter-btn" onclick="submitExport()" id="exportBtn" disabled>Export</button>
        </div>
    </div>
</div>

<script>
    function openModal(id) { document.getElementById(id).classList.add('show'); }
    function closeModal(id) { document.getElementById(id).classList.remove('show'); }
    function openExportModal() { openModal('exportModal'); }

    let selectedFormat = '';
    function setExportFormat(format) {
        selectedFormat = format;
        document.getElementById('exportFormat').value = format;
        document.getElementById('exportBtn').disabled = false;
        document.querySelectorAll('.export-option').forEach(opt => {
            opt.style.borderColor = 'var(--border)';
            opt.style.background = 'white';
        });
        event.currentTarget.style.borderColor = 'var(--primary)';
        event.currentTarget.style.background = '#f0f7ff';
    }

    function submitExport() {
        if (!selectedFormat) { alert('Please select a format'); return; }
        document.getElementById('exportForm').submit();
    }
</script>
@endsection
