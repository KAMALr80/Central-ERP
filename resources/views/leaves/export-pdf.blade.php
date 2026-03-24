<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Report - {{ now()->format('d M Y') }}</title>
    <style>
        /* ================= PDF OPTIMIZED STYLES ================= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Segoe UI', Arial, sans-serif;
            background: white;
            color: #333;
            line-height: 1.5;
            font-size: 11px;
        }

        .report-container {
            max-width: 100%;
            margin: 0;
            padding: 15px;
            background: white;
        }

        /* ================= HEADER ================= */
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #007bff;
        }

        .company-name {
            font-size: 24px;
            font-weight: 700;
            color: #007bff;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .report-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .report-period {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }

        .generated-date {
            font-size: 10px;
            color: #999;
        }

        /* ================= SUMMARY CARDS ================= */
        .summary-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .summary-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px;
            text-align: center;
        }

        .summary-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .summary-value {
            font-size: 18px;
            font-weight: 700;
            color: #007bff;
        }

        .summary-sub {
            font-size: 9px;
            color: #999;
            margin-top: 3px;
        }

        /* ================= FILTERS INFO ================= */
        .filters-info {
            background: #e7f3ff;
            border: 1px solid #b8daff;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 10px;
            color: #004085;
        }

        .filters-title {
            font-weight: 700;
            margin-bottom: 5px;
            font-size: 11px;
        }

        .filters-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .filter-tag {
            background: white;
            border: 1px solid #b8daff;
            border-radius: 3px;
            padding: 3px 8px;
            font-size: 9px;
        }

        /* ================= TABLE ================= */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9px;
        }

        .report-table th {
            background: #007bff;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-weight: 600;
            white-space: nowrap;
        }

        .report-table td {
            padding: 6px 5px;
            border: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .report-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .report-table tbody tr:hover {
            background: #e7f3ff;
        }

        /* ================= STATUS BADGES ================= */
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status-cancelled {
            background: #e9ecef;
            color: #495057;
            border: 1px solid #ced4da;
        }

        /* ================= LEAVE TYPE BADGES ================= */
        .type-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: 600;
        }

        .type-annual {
            background: #cce5ff;
            color: #004085;
        }

        .type-sick {
            background: #f8d7da;
            color: #721c24;
        }

        .type-casual {
            background: #d4edda;
            color: #155724;
        }

        .type-unpaid {
            background: #e2e3e5;
            color: #383d41;
        }

        /* ================= FOOTER ================= */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            font-size: 8px;
            color: #666;
            text-align: center;
        }

        .footer table {
            width: 100%;
        }

        .footer td {
            padding: 5px;
        }

        .page-number {
            text-align: right;
        }

        /* ================= WATERMARK ================= */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60px;
            opacity: 0.03;
            color: #007bff;
            font-weight: 700;
            white-space: nowrap;
            z-index: -1;
            pointer-events: none;
        }

        /* ================= SUMMARY STATISTICS ================= */
        .stats-section {
            margin-bottom: 20px;
        }

        .stats-title {
            font-size: 14px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #007bff;
        }

        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .stats-table td {
            padding: 8px;
            border: 1px solid #dee2e6;
        }

        .stats-label {
            font-weight: 600;
            background: #f8f9fa;
            width: 30%;
        }

        /* ================= CHARTS (ASCII) ================= */
        .ascii-chart {
            margin: 15px 0;
            font-family: monospace;
            font-size: 8px;
            line-height: 1.2;
        }

        .chart-bar {
            display: inline-block;
            height: 10px;
            background: #007bff;
            margin-right: 5px;
        }

        /* ================= UTILITIES ================= */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-primary {
            color: #007bff;
        }

        .text-success {
            color: #28a745;
        }

        .text-danger {
            color: #dc3545;
        }

        .font-bold {
            font-weight: 700;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .mt-20 {
            margin-top: 20px;
        }

        .p-5 {
            padding: 5px;
        }

        .bg-light {
            background: #f8f9fa;
        }

        .border {
            border: 1px solid #dee2e6;
        }

        .rounded {
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <!-- Watermark -->
        <div class="watermark">{{ config('app.name') }}</div>

        <!-- Header -->
        <div class="header">
            <div class="company-name">{{ config('app.name') }}</div>
            <div class="report-title">LEAVE REPORT</div>
            <div class="report-period">
                @if(request('from_date') && request('to_date'))
                    Period: {{ \Carbon\Carbon::parse(request('from_date'))->format('d M Y') }} - {{ \Carbon\Carbon::parse(request('to_date'))->format('d M Y') }}
                @else
                    Period: All Time
                @endif
            </div>
            <div class="generated-date">Generated on: {{ now()->format('d F Y, h:i A') }}</div>
        </div>

        <!-- Filters Applied -->
        @if(request('status') || request('leave_type') || request('employee_id'))
        <div class="filters-info">
            <div class="filters-title">📊 Filters Applied:</div>
            <div class="filters-list">
                @if(request('status') && request('status') != 'all')
                    <span class="filter-tag">Status: {{ ucfirst(request('status')) }}</span>
                @endif
                @if(request('leave_type') && request('leave_type') != 'all')
                    <span class="filter-tag">Type: {{ ucfirst(request('leave_type')) }}</span>
                @endif
                @if(request('employee_id'))
                    @php
                        $employee = \App\Models\Employee::find(request('employee_id'));
                    @endphp
                    @if($employee)
                        <span class="filter-tag">Employee: {{ $employee->name }}</span>
                    @endif
                @endif
                @if(request('from_date'))
                    <span class="filter-tag">From: {{ \Carbon\Carbon::parse(request('from_date'))->format('d M Y') }}</span>
                @endif
                @if(request('to_date'))
                    <span class="filter-tag">To: {{ \Carbon\Carbon::parse(request('to_date'))->format('d M Y') }}</span>
                @endif
            </div>
        </div>
        @endif

        <!-- Summary Statistics -->
        <div class="stats-section">
            <div class="stats-title">📈 SUMMARY STATISTICS</div>
            <table class="stats-table">
                <tr>
                    <td class="stats-label">Total Leaves</td>
                    <td>{{ count($leaves) }}</td>
                    <td class="stats-label">Total Days</td>
                    <td>{{ $leaves->sum('total_days') }}</td>
                </tr>
                <tr>
                    <td class="stats-label">Pending</td>
                    <td>{{ $leaves->where('status', 'pending')->count() }}</td>
                    <td class="stats-label">Approved</td>
                    <td>{{ $leaves->where('status', 'approved')->count() }}</td>
                </tr>
                <tr>
                    <td class="stats-label">Rejected</td>
                    <td>{{ $leaves->where('status', 'rejected')->count() }}</td>
                    <td class="stats-label">Cancelled</td>
                    <td>{{ $leaves->where('status', 'cancelled')->count() }}</td>
                </tr>
            </table>
        </div>

        <!-- Leave Type Distribution -->
        <div class="stats-section">
            <div class="stats-title">📊 LEAVE TYPE DISTRIBUTION</div>
            <table class="stats-table">
                @php
                    $annual = $leaves->where('leave_type', 'annual')->count();
                    $sick = $leaves->where('leave_type', 'sick')->count();
                    $casual = $leaves->where('leave_type', 'casual')->count();
                    $unpaid = $leaves->where('leave_type', 'unpaid')->count();
                    $total = count($leaves);
                @endphp
                <tr>
                    <td class="stats-label">Annual Leave</td>
                    <td>{{ $annual }}</td>
                    <td class="stats-label">{{ $total > 0 ? round(($annual/$total)*100) : 0 }}%</td>
                    <td rowspan="4" style="vertical-align: middle;">
                        <div class="ascii-chart">
                            @for($i = 0; $i < ($total > 0 ? ($annual/$total)*20 : 0); $i++) █ @endforce<br>
                            @for($i = 0; $i < ($total > 0 ? ($sick/$total)*20 : 0); $i++) █ @endforce<br>
                            @for($i = 0; $i < ($total > 0 ? ($casual/$total)*20 : 0); $i++) █ @endforce<br>
                            @for($i = 0; $i < ($total > 0 ? ($unpaid/$total)*20 : 0); $i++) █ @endforce
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="stats-label">Sick Leave</td>
                    <td>{{ $sick }}</td>
                    <td class="stats-label">{{ $total > 0 ? round(($sick/$total)*100) : 0 }}%</td>
                </tr>
                <tr>
                    <td class="stats-label">Casual Leave</td>
                    <td>{{ $casual }}</td>
                    <td class="stats-label">{{ $total > 0 ? round(($casual/$total)*100) : 0 }}%</td>
                </tr>
                <tr>
                    <td class="stats-label">Unpaid Leave</td>
                    <td>{{ $unpaid }}</td>
                    <td class="stats-label">{{ $total > 0 ? round(($unpaid/$total)*100) : 0 }}%</td>
                </tr>
            </table>
        </div>

        <!-- Monthly Trend -->
        <div class="stats-section">
            <div class="stats-title">📅 MONTHLY TREND</div>
            <table class="stats-table">
                <tr>
                    @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $month)
                        <th style="text-align: center; font-size: 8px;">{{ $month }}</th>
                    @endforeach
                </tr>
                <tr>
                    @foreach(range(1,12) as $month)
                        @php
                            $count = $leaves->filter(function($leave) use ($month) {
                                return \Carbon\Carbon::parse($leave->from_date)->month == $month;
                            })->count();
                        @endphp
                        <td style="text-align: center;">{{ $count }}</td>
                    @endforeach
                </tr>
            </table>
        </div>

        <!-- Leave Details Table -->
        <div class="stats-section">
            <div class="stats-title">📋 LEAVE DETAILS</div>
            <table class="report-table" repeat="1">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Leave No.</th>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Type</th>
                        <th>Duration</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Days</th>
                        <th>Status</th>
                        <th>Applied On</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @php $__col = $leaves; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $index => $leave)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $leave->leave_number ?? 'N/A' }}</td>
                        <td>
                            {{ $leave->employee->name ?? 'N/A' }}<br>
                            <span style="font-size: 7px; color: #666;">{{ $leave->employee->employee_code ?? '' }}</span>
                        </td>
                        <td>{{ $leave->employee->department ?? 'N/A' }}</td>
                        <td>
                            <span class="type-badge type-{{ $leave->leave_type }}">
                                {{ $leave->leave_type_label ?? $leave->leave_type }}
                            </span>
                        </td>
                        <td>{{ $leave->duration_label ?? $leave->duration_type }}</td>
                        <td>{{ \Carbon\Carbon::parse($leave->from_date)->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($leave->to_date)->format('d M Y') }}</td>
                        <td class="text-center">{{ $leave->total_days ?? 1 }}</td>
                        <td>
                            <span class="status-badge status-{{ $leave->status }}">
                                @if($leave->status == 'pending') ⏳
                                @elseif($leave->status == 'approved') ✅
                                @elseif($leave->status == 'rejected') ❌
                                @elseif($leave->status == 'cancelled') ↩️
                                @endif
                                {{ ucfirst($leave->status) }}
                            </span>
                        </td>
                        <td>{{ $leave->applied_on ? $leave->applied_on->format('d M Y') : 'N/A' }}</td>
                        <td>{{ \Str::limit($leave->reason, 20) }}</td>
                    </tr>
                    @endforeach
@else
                    <tr>
                        <td colspan="12" style="text-align: center; padding: 30px;">
                            <div style="font-size: 12px; color: #999;">No leave records found</div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Department-wise Summary -->
        <div class="stats-section">
            <div class="stats-title">🏢 DEPARTMENT WISE SUMMARY</div>
            <table class="stats-table">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th>Total Leaves</th>
                        <th>Total Days</th>
                        <th>Pending</th>
                        <th>Approved</th>
                        <th>Rejected</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $departments = $leaves->groupBy(function($leave) {
                            return $leave->employee->department ?? 'N/A';
                        });
                    @endphp
                    @foreach($departments as $dept => $deptLeaves)
                    <tr>
                        <td><strong>{{ $dept }}</strong></td>
                        <td class="text-center">{{ $deptLeaves->count() }}</td>
                        <td class="text-center">{{ $deptLeaves->sum('total_days') }}</td>
                        <td class="text-center">{{ $deptLeaves->where('status', 'pending')->count() }}</td>
                        <td class="text-center">{{ $deptLeaves->where('status', 'approved')->count() }}</td>
                        <td class="text-center">{{ $deptLeaves->where('status', 'rejected')->count() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Employee-wise Summary -->
        <div class="stats-section">
            <div class="stats-title">👥 EMPLOYEE WISE SUMMARY</div>
            <table class="stats-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Total Leaves</th>
                        <th>Total Days</th>
                        <th>Approved</th>
                        <th>Pending</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $employees = $leaves->groupBy('employee_id');
                    @endphp
                    @foreach($employees as $empId => $empLeaves)
                        @php
                            $employee = $empLeaves->first()->employee;
                        @endphp
                        @if($employee)
                        <tr>
                            <td>
                                {{ $employee->name }}<br>
                                <span style="font-size: 7px; color: #666;">{{ $employee->employee_code }}</span>
                            </td>
                            <td class="text-center">{{ $empLeaves->count() }}</td>
                            <td class="text-center">{{ $empLeaves->sum('total_days') }}</td>
                            <td class="text-center">{{ $empLeaves->where('status', 'approved')->count() }}</td>
                            <td class="text-center">{{ $empLeaves->where('status', 'pending')->count() }}</td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <table>
                <tr>
                    <td style="text-align: left;">
                        {{ config('app.name') }} - Leave Management System
                    </td>
                    <td style="text-align: center;">
                        Generated by: {{ Auth::user()->name ?? 'System' }}
                    </td>
                    <td style="text-align: right;">
                        Page {PAGE_NUM} of {PAGE_COUNT}
                    </td>
                </tr>
            </table>
            <div style="margin-top: 5px;">
                <small>This is a computer generated report. No signature is required.</small>
            </div>
        </div>
    </div>
</body>
</html>
