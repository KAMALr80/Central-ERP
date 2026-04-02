@extends('layouts.app')

@section('page-title', 'HR Dashboard')

@section('content')
    <style>
        /* ================= PROFESSIONAL DESIGN SYSTEM ================= */
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --success: #10b981;
            --success-dark: #059669;
            --danger: #ef4444;
            --danger-dark: #dc2626;
            --warning: #f59e0b;
            --warning-dark: #d97706;
            --info: #0ea5e9;
            --purple: #8b5cf6;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --bg-light: #f9fafb;
            --bg-white: #ffffff;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;
            --radius-2xl: 24px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f3f4f6;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            color: var(--text-main);
            line-height: 1.5;
        }

        /* ================= MAIN CONTAINER ================= */
        .hr-dashboard {
            max-width: 1600px;
            margin: 0 auto;
            padding: 20px;
            width: 100%;
            min-height: 100vh;
            background: #f3f4f6;
        }

        /* ================= HEADER ================= */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-left {
            flex: 1;
            min-width: 280px;
        }

        .header-title {
            margin: 0;
            font-size: clamp(24px, 4vw, 28px);
            color: var(--text-main);
            font-weight: 800;
            word-break: break-word;
        }

        .header-subtitle {
            margin: 8px 0 0 0;
            color: var(--text-muted);
            font-size: clamp(13px, 2vw, 15px);
            word-break: break-word;
        }

        .refresh-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: clamp(13px, 2vw, 14px);
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .refresh-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* ================= STATS GRID ================= */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
            width: 100%;
        }

        .stat-card {
            background: var(--bg-white);
            border-radius: var(--radius-xl);
            padding: clamp(20px, 3vw, 25px);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border);
            transition: all 0.3s ease;
            width: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .stat-info {
            flex: 1;
        }

        .stat-label {
            font-size: clamp(12px, 2vw, 14px);
            color: var(--text-muted);
            margin-bottom: 5px;
            font-weight: 500;
            word-break: break-word;
        }

        .stat-value {
            font-size: clamp(30px, 5vw, 36px);
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.2;
            word-break: break-word;
        }

        .stat-icon {
            width: clamp(50px, 8vw, 60px);
            height: clamp(50px, 8vw, 60px);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(20px, 3vw, 24px);
            color: white;
            flex-shrink: 0;
        }

        .stat-icon.blue {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }

        .stat-icon.green {
            background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
        }

        .stat-icon.red {
            background: linear-gradient(135deg, var(--danger) 0%, var(--danger-dark) 100%);
        }

        .stat-icon.orange {
            background: linear-gradient(135deg, var(--warning) 0%, var(--warning-dark) 100%);
        }

        .stat-footer {
            font-size: clamp(12px, 2vw, 13px);
            display: flex;
            align-items: center;
            gap: 5px;
            flex-wrap: wrap;
        }

        .stat-footer.green {
            color: var(--success);
        }

        .stat-footer.red {
            color: var(--danger);
        }

        .stat-footer.orange {
            color: var(--warning);
        }

        /* ================= TWO COLUMN LAYOUT ================= */
        .two-column-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
            width: 100%;
        }

        .card {
            background: var(--bg-white);
            border-radius: var(--radius-xl);
            padding: clamp(20px, 3vw, 25px);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border);
            width: 100%;
            overflow: hidden;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .card-title {
            margin: 0;
            font-size: clamp(18px, 3vw, 20px);
            color: var(--text-main);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            word-break: break-word;
        }

        .card-action {
            color: var(--primary);
            text-decoration: none;
            font-size: clamp(12px, 2vw, 14px);
            font-weight: 600;
            padding: 6px 12px;
            border-radius: var(--radius-md);
            background: #eef2ff;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .card-action:hover {
            background: var(--primary);
            color: white;
        }

        /* ================= TABLES ================= */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        .data-table th {
            text-align: left;
            padding: 12px 0;
            color: var(--text-muted);
            font-weight: 600;
            font-size: clamp(12px, 2vw, 13px);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        .data-table td {
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: clamp(12px, 2vw, 14px);
            white-space: nowrap;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .employee-cell {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .employee-avatar {
            width: clamp(35px, 5vw, 40px);
            height: clamp(35px, 5vw, 40px);
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, var(--purple) 0%, var(--primary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: clamp(14px, 2vw, 16px);
            flex-shrink: 0;
        }

        .employee-avatar.orange {
            background: linear-gradient(135deg, var(--warning) 0%, var(--warning-dark) 100%);
        }

        .employee-name {
            font-weight: 600;
            color: var(--text-main);
            word-break: break-word;
        }

        .employee-email {
            font-size: clamp(11px, 1.5vw, 12px);
            color: var(--text-muted);
            word-break: break-word;
        }

        /* ================= BADGES ================= */
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: clamp(11px, 2vw, 12px);
            font-weight: 600;
            display: inline-block;
            white-space: nowrap;
        }

        .badge-department {
            background: #eef2ff;
            color: var(--primary-dark);
        }

        .badge-leave {
            background: #fef3c7;
            color: var(--warning-dark);
        }

        .badge-pending {
            background: #fef3c7;
            color: var(--warning-dark);
        }

        /* ================= EMPTY STATE ================= */
        .empty-state {
            text-align: center;
            padding: clamp(30px, 8vw, 40px) clamp(10px, 3vw, 20px);
        }

        .empty-icon {
            font-size: clamp(36px, 8vw, 48px);
            margin-bottom: 10px;
        }

        .empty-title {
            color: var(--text-muted);
            margin-bottom: 5px;
            font-size: clamp(14px, 2.5vw, 16px);
            word-break: break-word;
        }

        .empty-text {
            color: #9ca3af;
            font-size: clamp(12px, 2vw, 13px);
            margin-bottom: 15px;
            word-break: break-word;
        }

        .empty-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            font-size: clamp(13px, 2vw, 14px);
        }

        /* ================= DEPARTMENT STATS ================= */
        .department-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            width: 100%;
        }

        .department-card {
            border-radius: var(--radius-lg);
            padding: clamp(15px, 2.5vw, 20px);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.2s ease;
        }

        .department-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .department-icon {
            width: clamp(45px, 6vw, 50px);
            height: clamp(45px, 6vw, 50px);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(20px, 3vw, 24px);
            color: white;
            flex-shrink: 0;
        }

        .department-info {
            flex: 1;
        }

        .department-count {
            font-size: clamp(24px, 4vw, 28px);
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.2;
        }

        .department-name {
            font-size: clamp(13px, 2vw, 14px);
            color: var(--text-muted);
            font-weight: 600;
            word-break: break-word;
        }

        .department-percentage {
            font-size: clamp(11px, 1.5vw, 12px);
            color: #9ca3af;
            margin-top: 4px;
        }

        /* ================= HOLIDAYS GRID ================= */
        .holidays-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            width: 100%;
        }

        .holiday-card {
            background: #f0f9ff;
            border: 1px solid #e0f2fe;
            border-radius: var(--radius-lg);
            padding: clamp(15px, 2.5vw, 20px);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.2s ease;
        }

        .holiday-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: var(--info);
        }

        .holiday-icon {
            width: clamp(45px, 6vw, 50px);
            height: clamp(45px, 6vw, 50px);
            border-radius: var(--radius-md);
            background: var(--info);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(20px, 3vw, 24px);
            color: white;
            flex-shrink: 0;
        }

        .holiday-info {
            flex: 1;
        }

        .holiday-name {
            font-size: clamp(14px, 2vw, 16px);
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 5px;
            word-break: break-word;
        }

        .holiday-date {
            font-size: clamp(12px, 1.5vw, 13px);
            color: var(--text-muted);
            margin-bottom: 5px;
            word-break: break-word;
        }

        .holiday-days {
            font-size: clamp(11px, 1.5vw, 12px);
            color: var(--info);
            font-weight: 600;
        }

        /* ================= QUICK ACTIONS FOOTER ================= */
        .quick-actions-footer {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 100;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .quick-action-btn {
            padding: clamp(10px, 2vw, 12px) clamp(16px, 3vw, 20px);
            border-radius: var(--radius-lg);
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
            border: none;
            font-size: clamp(13px, 2vw, 14px);
            white-space: nowrap;
        }

        .quick-action-btn.primary {
            background: var(--primary);
            color: white;
        }

        .quick-action-btn.primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .quick-action-btn.secondary {
            background: white;
            color: var(--primary);
            border: 1px solid var(--border);
        }

        .quick-action-btn.secondary:hover {
            background: #f9fafb;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        /* ================= RESPONSIVE BREAKPOINTS ================= */

        /* Large Desktop (1200px and above) */
        @media (min-width: 1200px) {
            .two-column-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Desktop (992px to 1199px) */
        @media (max-width: 1199px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .department-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .holidays-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Tablet (768px to 991px) */
        @media (max-width: 991px) {
            .hr-dashboard {
                padding: 15px;
            }

            .dashboard-header {
                margin-bottom: 20px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .two-column-grid {
                grid-template-columns: 1fr;
            }

            .quick-actions-footer {
                position: static;
                margin-top: 30px;
                justify-content: center;
            }
        }

        /* Mobile Landscape (576px to 767px) */
        @media (max-width: 767px) {
            .hr-dashboard {
                padding: 12px;
            }

            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .refresh-btn {
                width: 100%;
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-card {
                padding: 18px;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .card-action {
                width: 100%;
                text-align: center;
            }

            .employee-cell {
                flex-direction: column;
                align-items: flex-start;
            }

            .employee-avatar {
                margin-bottom: 5px;
            }

            .department-grid,
            .holidays-grid {
                grid-template-columns: 1fr;
            }

            .department-card,
            .holiday-card {
                padding: 15px;
            }

            .quick-actions-footer {
                flex-direction: column;
                gap: 8px;
            }

            .quick-action-btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* Mobile Portrait (up to 575px) */
        @media (max-width: 575px) {
            .hr-dashboard {
                padding: 10px;
            }

            .data-table {
                min-width: 500px;
            }

            .data-table th,
            .data-table td {
                padding: 8px 0;
            }

            .employee-avatar {
                width: 30px;
                height: 30px;
                font-size: 14px;
            }

            .badge {
                padding: 3px 8px;
                font-size: 10px;
            }

            .department-count {
                font-size: 22px;
            }

            .holiday-name {
                font-size: 14px;
            }

            .holiday-date {
                font-size: 11px;
            }
        }

        /* Extra Small Devices (up to 360px) */
        @media (max-width: 360px) {
            .hr-dashboard {
                padding: 8px;
            }

            .header-title {
                font-size: 22px;
            }

            .stat-value {
                font-size: 28px;
            }

            .stat-icon {
                width: 45px;
                height: 45px;
                font-size: 20px;
            }

            .data-table {
                min-width: 400px;
            }

            .data-table th,
            .data-table td {
                font-size: 11px;
                padding: 6px 0;
            }

            .badge {
                padding: 2px 6px;
                font-size: 9px;
            }

            .department-icon {
                width: 35px;
                height: 35px;
                font-size: 18px;
            }

            .holiday-icon {
                width: 35px;
                height: 35px;
                font-size: 18px;
            }
        }

        /* Print Styles */
        @media print {

            .refresh-btn,
            .card-action,
            .quick-actions-footer,
            .empty-link {
                display: none !important;
            }

            .stat-card,
            .card,
            .department-card,
            .holiday-card {
                break-inside: avoid;
                box-shadow: none;
                border: 1px solid #000;
            }

            .stat-icon {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .badge {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>

    <div class="hr-dashboard">
        <!-- Page Header -->
        <div class="dashboard-header">
            <div class="header-left">
                <h1 class="header-title">HR Dashboard</h1>
                <p class="header-subtitle">
                    {{ now()->format('l, F j, Y') }} • Total Employees: {{ $totalEmployees }}
                </p>
            </div>
            <button onclick="window.location.reload()" class="refresh-btn">
                🔄 Refresh
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <!-- Total Employees -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-info">
                        <div class="stat-label">Total Employees</div>
                        <div class="stat-value">{{ $totalEmployees }}</div>
                    </div>
                    <div class="stat-icon blue">👥</div>
                </div>
                <div class="stat-footer green">
                    <span>📈</span> <span>Active workforce</span>
                </div>
            </div>

            <!-- Present Today -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-info">
                        <div class="stat-label">Present Today</div>
                        <div class="stat-value">{{ $presentToday }}</div>
                    </div>
                    <div class="stat-icon green">✅</div>
                </div>
                <div class="stat-footer green">
                    <span>🎯</span> <span>{{ $totalEmployees > 0 ? round(($presentToday / $totalEmployees) * 100, 1) : 0 }}%
                        attendance</span>
                </div>
            </div>

            <!-- Absent Today -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-info">
                        <div class="stat-label">Absent Today</div>
                        <div class="stat-value">{{ $absentToday }}</div>
                    </div>
                    <div class="stat-icon red">⚠️</div>
                </div>
                <div class="stat-footer red">
                    <span>📉</span> <span>Requires attention</span>
                </div>
            </div>

            <!-- On Leave Today -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-info">
                        <div class="stat-label">On Leave Today</div>
                        <div class="stat-value">{{ $onLeaveToday }}</div>
                    </div>
                    <div class="stat-icon orange">🏖️</div>
                </div>
                <div class="stat-footer orange">
                    <span>📅</span> <span>Approved leaves</span>
                </div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="two-column-grid">
            <!-- Left Column: Recent Employees -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">
                        <span>👥</span> Recent Employees
                    </h2>
                    <a href="{{ route('employees.index') }}" class="card-action">View All</a>
                </div>

                @if ($recentEmployees->count() > 0)
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Position</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentEmployees as $employee)
                                    <tr>
                                        <td>
                                            <div class="employee-cell">
                                                <div class="employee-avatar">
                                                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="employee-name">{{ $employee->name }}</div>
                                                    <div class="employee-email">{{ $employee->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-department">
                                                {{ $employee->department ?? 'Not Assigned' }}
                                            </span>
                                        </td>
                                        <td>{{ $employee->position ?? '-' }}</td>
                                        <td>{{ $employee->created_at->format('d M, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">👤</div>
                        <p class="empty-title">No employees found</p>
                        <a href="{{ route('employees.index') }}" class="empty-link">Add your first employee →</a>
                    </div>
                @endif
            </div>

            <!-- Right Column: Pending Leave Requests -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">
                        <span>📝</span> Pending Leave Requests ({{ $pendingLeaves }})
                    </h2>
                    <a href="{{ route('leaves.manage') }}" class="card-action">Manage</a>
                </div>

                @if ($recentLeaves->count() > 0)
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentLeaves as $leave)
                                    <tr>
                                        <td>
                                            <div class="employee-cell">
                                                <div class="employee-avatar orange">
                                                    {{ strtoupper(substr($leave->employee->name ?? 'E', 0, 1)) }}
                                                </div>
                                                <div class="employee-name">
                                                    {{ $leave->employee->name ?? 'Unknown Employee' }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-leave">
                                                {{ $leave->type ?? 'Leave' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if (isset($leave->from_date) && isset($leave->to_date))
                                                {{ \Carbon\Carbon::parse($leave->from_date)->format('d M') }} -
                                                {{ \Carbon\Carbon::parse($leave->to_date)->format('d M') }}
                                            @else
                                                {{ $leave->created_at->format('d M, Y') }}
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-pending">
                                                ⏳ Pending
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($pendingLeaves > 5)
                        <div style="text-align:center; padding-top:15px; border-top:1px solid var(--border);">
                            <a href="{{ route('leaves.manage') }}" class="empty-link">View all {{ $pendingLeaves }}
                                pending requests →</a>
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <div class="empty-icon">✅</div>
                        <p class="empty-title">No pending leave requests</p>
                        <p class="empty-text">All leave requests have been processed</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Third Row: Department Stats -->
        <div class="card" style="margin-bottom:30px;">
            <h2 class="card-title" style="margin-bottom:20px;">
                <span>📊</span> Department Statistics
            </h2>

            @if ($departmentStats->count() > 0)
                <div class="department-grid">
                    @foreach ($departmentStats as $dept)
                        <div class="department-card"
                            style="background:{{ $dept['color'] }}10; border:1px solid {{ $dept['color'] }}30;">
                            <div class="department-icon" style="background:{{ $dept['color'] }};">
                                {{ $dept['icon'] }}
                            </div>
                            <div class="department-info">
                                <div class="department-count">{{ $dept['count'] }}</div>
                                <div class="department-name">{{ $dept['name'] }}</div>
                                <div class="department-percentage">
                                    {{ $totalEmployees > 0 ? round(($dept['count'] / $totalEmployees) * 100, 1) : 0 }}% of
                                    total
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">🏢</div>
                    <p class="empty-title">No department data available</p>
                    <p class="empty-text">Add departments to employee records</p>
                </div>
            @endif
        </div>

        <div class="card">
            <h2 class="card-title" style="margin-bottom:20px;">
                <span>🎉</span> Upcoming Holidays
            </h2>

            @if (count($upcomingHolidays) > 0)
                <div class="holidays-grid">
                    @foreach ($upcomingHolidays as $holiday)
                        <div class="holiday-card"
                            @if ($holiday['is_optional']) style="background: #fff7ed; border-color: #fed7aa;" @endif>
                            <div class="holiday-icon"
                                @if ($holiday['is_optional']) style="background: var(--warning);" @endif>
                                @if ($holiday['days_until'] == 0)
                                    🎊
                                @elseif($holiday['days_until'] <= 3)
                                    🔥
                                @else
                                    📅
                                @endif
                            </div>
                            <div class="holiday-info">
                                <div class="holiday-name">
                                    {{ $holiday['name'] }}
                                    @if ($holiday['is_optional'])
                                        {{-- <span
                                            style="font-size: 10px; background: #fef3c7; padding: 2px 6px; border-radius: 12px; margin-left: 8px;">
                                            Optional
                                        </span> --}}
                                    @endif
                                </div>
                                <div class="holiday-date">
                                    {{ $holiday['date']->format('l, F j, Y') }}
                                </div>
                                <div class="holiday-days">
                                    @if ($holiday['days_until'] == 0)
                                        🟢 Today!
                                    @elseif($holiday['days_until'] == 1)
                                        🔴 Tomorrow!
                                    @else
                                        📍 In {{ $holiday['days_until'] }} days
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">📅</div>
                    <p class="empty-title">No upcoming holidays scheduled</p>
                    <p class="empty-text">Holidays will be displayed automatically based on calendar</p>
                </div>
            @endif
        </div>

        <!-- Quick Actions Footer -->
        <div class="quick-actions-footer">
            <button onclick="window.location.href='{{ route('employees.create') }}'" class="quick-action-btn secondary">
                👥 Add Employee
            </button>
            <button onclick="window.location.href='{{ route('leaves.manage') }}'" class="quick-action-btn primary">
                📝 Manage Leaves
            </button>
        </div>
    </div>
@endsection
