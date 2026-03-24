@extends('layouts.app')

@section('page-title', 'Leave Dashboard')

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
        --purple: #6f42c1;
        --pink: #e83e8c;
        --text-main: #2c3e50;
        --text-muted: #6b7280;
        --border: #ddd;
        --bg-light: #f8f9fa;
        --bg-white: #ffffff;
        --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
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
        background: #f4f6f9;
        color: var(--text-main);
        line-height: 1.5;
    }

    /* ================= MAIN CONTAINER ================= */
    .dashboard-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
        padding: clamp(16px, 3vw, 30px);
        width: 100%;
    }

    .container {
        max-width: 1600px;
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

    .date-badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 10px 20px;
        border-radius: 40px;
        font-weight: 600;
        font-size: clamp(13px, 3vw, 14px);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* ================= STATS CARDS ================= */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        padding: 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--primary);
    }

    .stat-card.pending::before {
        background: var(--warning);
    }

    .stat-card.approved::before {
        background: var(--success);
    }

    .stat-card.rejected::before {
        background: var(--danger);
    }

    .stat-card.cancelled::before {
        background: #6c757d;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
        background: var(--bg-light);
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 5px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.2;
    }

    .stat-trend {
        font-size: 12px;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .trend-up {
        color: var(--success);
    }

    .trend-down {
        color: var(--danger);
    }

    /* ================= CHARTS SECTION ================= */
    .charts-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }

    .chart-card {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        padding: 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--border);
    }

    .chart-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .chart-title span {
        width: 4px;
        height: 20px;
        background: var(--primary);
        border-radius: 2px;
        display: inline-block;
    }

    .chart-actions {
        display: flex;
        gap: 10px;
    }

    .chart-action {
        padding: 5px 10px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        font-size: 12px;
        background: white;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .chart-action:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .chart-container {
        height: 300px;
        position: relative;
    }

    /* ================= LEAVE TYPE DISTRIBUTION ================= */
    .distribution-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-top: 15px;
    }

    .distribution-item {
        background: var(--bg-light);
        padding: 15px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
    }

    .distribution-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .distribution-type {
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .distribution-count {
        font-weight: 700;
        color: var(--primary);
    }

    .progress-bar {
        height: 8px;
        background: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
        margin: 8px 0;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary), var(--primary-dark));
        border-radius: 4px;
        transition: width 0.3s ease;
    }

    /* ================= RECENT ACTIVITY ================= */
    .activity-card {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        padding: 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
        margin-bottom: 30px;
    }

    .activity-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--border);
    }

    .activity-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .activity-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 12px;
        background: var(--bg-light);
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        transition: all 0.3s ease;
    }

    .activity-item:hover {
        transform: translateX(5px);
        border-color: var(--primary);
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .activity-icon.pending {
        background: #fff3cd;
        color: #856404;
    }

    .activity-icon.approved {
        background: #d4edda;
        color: #155724;
    }

    .activity-icon.rejected {
        background: #f8d7da;
        color: #721c24;
    }

    .activity-content {
        flex: 1;
    }

    .activity-text {
        font-weight: 600;
        margin-bottom: 3px;
    }

    .activity-meta {
        font-size: 12px;
        color: var(--text-muted);
        display: flex;
        gap: 15px;
    }

    .activity-time {
        display: flex;
        align-items: center;
        gap: 3px;
    }

    /* ================= TOP EMPLOYEES ================= */
    .employee-card {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        padding: 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
    }

    .employee-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-top: 15px;
    }

    .employee-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px;
        background: var(--bg-light);
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
    }

    .employee-rank {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: var(--primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        flex-shrink: 0;
    }

    .employee-rank.gold {
        background: #ffc107;
        color: #333;
    }

    .employee-rank.silver {
        background: #6c757d;
        color: white;
    }

    .employee-rank.bronze {
        background: #cd7f32;
        color: white;
    }

    .employee-avatar {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 16px;
        flex-shrink: 0;
    }

    .employee-details {
        flex: 1;
    }

    .employee-name {
        font-weight: 600;
        color: var(--text-main);
    }

    .employee-dept {
        font-size: 11px;
        color: var(--text-muted);
    }

    .employee-stats {
        font-weight: 700;
        color: var(--primary);
        font-size: 18px;
    }

    .employee-stats small {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: normal;
    }

    /* ================= QUICK ACTIONS ================= */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 30px;
    }

    .quick-action {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        padding: 20px;
        text-align: center;
        border: 2px solid var(--border);
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        color: var(--text-main);
    }

    .quick-action:hover {
        border-color: var(--primary);
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }

    .quick-icon {
        width: 60px;
        height: 60px;
        background: var(--bg-light);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin: 0 auto 15px;
        color: var(--primary);
    }

    .quick-title {
        font-weight: 700;
        margin-bottom: 5px;
    }

    .quick-desc {
        font-size: 12px;
        color: var(--text-muted);
    }

    /* ================= RESPONSIVE ================= */
    @media (max-width: 992px) {
        .charts-grid {
            grid-template-columns: 1fr;
        }

        .distribution-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .header-card {
            flex-direction: column;
            text-align: center;
        }

        .header-left {
            justify-content: center;
        }

        .stats-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .activity-item {
            flex-direction: column;
            text-align: center;
        }

        .activity-meta {
            justify-content: center;
        }
    }
</style>

<div class="dashboard-page">
    <div class="container">
        <!-- Header Card -->
        <div class="header-card">
            <div class="header-left">
                <div class="header-icon">📊</div>
                <div class="header-title">
                    <h1>Leave Dashboard</h1>
                    <p>Comprehensive leave analytics and insights</p>
                </div>
            </div>
            <div class="date-badge">
                <span>📅</span>
                {{ now()->format('l, F j, Y') }}
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card pending">
                <div class="stat-icon">⏳</div>
                <div class="stat-info">
                    <div class="stat-label">Pending Requests</div>
                    <div class="stat-value">{{ $statistics['total_pending'] ?? 0 }}</div>
                    <div class="stat-trend">
                        <span class="trend-up">↑ 12%</span> from last month
                    </div>
                </div>
            </div>

            <div class="stat-card approved">
                <div class="stat-icon">✅</div>
                <div class="stat-info">
                    <div class="stat-label">Approved</div>
                    <div class="stat-value">{{ $statistics['total_approved'] ?? 0 }}</div>
                    <div class="stat-trend">
                        <span class="trend-up">↑ 8%</span> from last month
                    </div>
                </div>
            </div>

            <div class="stat-card rejected">
                <div class="stat-icon">❌</div>
                <div class="stat-info">
                    <div class="stat-label">Rejected</div>
                    <div class="stat-value">{{ $statistics['total_rejected'] ?? 0 }}</div>
                    <div class="stat-trend">
                        <span class="trend-down">↓ 5%</span> from last month
                    </div>
                </div>
            </div>

            <div class="stat-card cancelled">
                <div class="stat-icon">↩️</div>
                <div class="stat-info">
                    <div class="stat-label">Cancelled</div>
                    <div class="stat-value">{{ $statistics['total_cancelled'] ?? 0 }}</div>
                    <div class="stat-trend">
                        <span>0%</span> change
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-info">
                    <div class="stat-label">Total Leaves</div>
                    <div class="stat-value">{{ $statistics['total_leaves'] ?? 0 }}</div>
                    <div class="stat-trend">
                        <span>All time</span>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-info">
                    <div class="stat-label">On Leave Today</div>
                    <div class="stat-value">{{ $statistics['employees_on_leave_today'] ?? 0 }}</div>
                    <div class="stat-trend">
                        <span>{{ now()->format('d M') }}</span>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-info">
                    <div class="stat-label">This Month</div>
                    <div class="stat-value">{{ $statistics['leaves_this_month'] ?? 0 }}</div>
                    <div class="stat-trend">
                        <span>{{ date('F Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">⏰</div>
                <div class="stat-info">
                    <div class="stat-label">Upcoming</div>
                    <div class="stat-value">{{ $statistics['upcoming_leaves'] ?? 0 }}</div>
                    <div class="stat-trend">
                        <span>Next 7 days</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-grid">
            <!-- Monthly Trend Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <div class="chart-title">
                        <span></span> Monthly Leave Trend - {{ $currentYear ?? date('Y') }}
                    </div>
                    <div class="chart-actions">
                        <button class="chart-action" onclick="changeChartType('bar')">Bar</button>
                        <button class="chart-action" onclick="changeChartType('line')">Line</button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>

            <!-- Leave Distribution -->
            <div class="chart-card">
                <div class="chart-header">
                    <div class="chart-title">
                        <span></span> Leave Distribution
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="distributionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Leave Type Distribution Details -->
        <div class="chart-card" style="margin-bottom: 30px;">
            <div class="chart-header">
                <div class="chart-title">
                    <span></span> Leave Type Breakdown - {{ $currentYear ?? date('Y') }}
                </div>
            </div>
            <div class="distribution-grid">
                @foreach ($leaveByType ?: [] as $type => $count)
                    @php
                        $total = array_sum($leaveByType ?? []);
                        $percentage = $total > 0 ? round(($count / $total) * 100) : 0;
                        $colors = [
                            'annual' => ['#007bff', 'Annual Leave'],
                            'sick' => ['#dc3545', 'Sick Leave'],
                            'casual' => ['#28a745', 'Casual Leave'],
                            'unpaid' => ['#6c757d', 'Unpaid Leave'],
                        ];
                        $color = isset($colors[$type]) ? $colors[$type][0] : '#17a2b8';
                        $label = isset($colors[$type]) ? $colors[$type][1] : ucfirst($type) . ' Leave';
                    @endphp
                    <div class="distribution-item">
                        <div class="distribution-header">
                            <span class="distribution-type">
                                <span style="color: {{ $color }};">●</span> {{ $label }}
                            </span>
                            <span class="distribution-count">{{ $count }}</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill"
                                style="width: {{ $percentage }}%; background: {{ $color }};"></div>
                        </div>
                        <div
                            style="display: flex; justify-content: space-between; font-size: 11px; color: var(--text-muted);">
                            <span>{{ $percentage }}% of total</span>
                            <span>{{ $count }} leaves</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Activity & Top Employees -->
        <div class="charts-grid">
            <!-- Recent Activity -->
            <div class="activity-card">
                <div class="activity-header">
                    <div class="activity-title">
                        <span></span> Recent Leave Activity
                    </div>
                    <a href="{{ route('leaves.manage') }}" style="color: var(--primary); font-size: 13px;">View All
                        →</a>
                </div>
                <div class="activity-list">
                    @php $__col = $recentLeaves ?: []; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $activity)
                        <div class="activity-item">
                            <div class="activity-icon {{ $activity->status }}">
                                @if ($activity->status == 'pending')
                                    ⏳
                                @elseif($activity->status == 'approved')
                                    ✅
                                @elseif($activity->status == 'rejected')
                                    ❌
                                @else
                                    ↩️
                                @endif
                            </div>
                            <div class="activity-content">
                                <div class="activity-text">
                                    <strong>{{ $activity->employee->name ?? 'Unknown' }}</strong>
                                    applied for {{ $activity->leave_type_label ?? $activity->leave_type }}
                                </div>
                                <div class="activity-meta">
                                    <span class="activity-time">
                                        <span>⏱️</span> {{ $activity->created_at->diffForHumans() }}
                                    </span>
                                    <span>📅 {{ $activity->from_date->format('d M') }} -
                                        {{ $activity->to_date->format('d M') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
@else
                        <div style="text-align: center; padding: 40px;">
                            <div style="font-size: 48px; margin-bottom: 15px;">📭</div>
                            <div style="font-weight: 600;">No recent activity</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Top Leave Takers -->
            <div class="employee-card">
                <div class="activity-header">
                    <div class="activity-title">
                        <span></span> Top Leave Takers - {{ $currentYear ?? date('Y') }}
                    </div>
                </div>
                <div class="employee-list">
                    @php $__col = $topLeaveTakers ?: []; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $index => $employee)
                        <div class="employee-row">
                            <div
                                class="employee-rank
                                @if ($index == 0) gold
                                @elseif($index == 1) silver
                                @elseif($index == 2) bronze @endif">
                                {{ $index + 1 }}
                            </div>
                            <div class="employee-avatar">
                                {{ strtoupper(substr($employee->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="employee-details">
                                <div class="employee-name">{{ $employee->name ?? 'Unknown' }}</div>
                                <div class="employee-dept">{{ $employee->department ?? 'N/A' }}</div>
                            </div>
                            <div class="employee-stats">
                                {{ $employee->leaves_count ?? 0 }}
                                <small>days</small>
                            </div>
                        </div>
                    @endforeach
@else
                        <div style="text-align: center; padding: 40px;">
                            <div style="font-size: 48px; margin-bottom: 15px;">📊</div>
                            <div style="font-weight: 600;">No data available</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="{{ route('leaves.manage') }}" class="quick-action">
                <div class="quick-icon">📋</div>
                <div class="quick-title">Manage Leaves</div>
                <div class="quick-desc">View and process leave requests</div>
            </a>

            <!-- FIXED: Changed from 'leaves.calendar' to 'leaves.calendar-data' to match your route -->
            <a href="{{ route('leaves.calendar-data') }}" class="quick-action" target="_blank">
                <div class="quick-icon">📅</div>
                <div class="quick-title">Leave Calendar</div>
                <div class="quick-desc">View leaves in calendar view</div>
            </a>

            <a href="#" onclick="openExportModal()" class="quick-action">
                <div class="quick-icon">📊</div>
                <div class="quick-title">Export Report</div>
                <div class="quick-desc">Download leave reports</div>
            </a>

            <a href="{{ route('leaves.create') }}" class="quick-action">
                <div class="quick-icon">➕</div>
                <div class="quick-title">Apply Leave</div>
                <div class="quick-desc">Create new leave request</div>
            </a>

            <a href="{{ route('employees.index') }}" class="quick-action">
                <div class="quick-icon">👥</div>
                <div class="quick-title">Employees</div>
                <div class="quick-desc">Manage employee records</div>
            </a>

            <!-- FIXED: Replaced undefined 'settings.leaves' with a placeholder -->
            <a href="#" onclick="alert('Settings page coming soon!')" class="quick-action">
                <div class="quick-icon">⚙️</div>
                <div class="quick-title">Settings</div>
                <div class="quick-desc">Leave policy settings (Coming Soon)</div>
            </a>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal" id="exportModal"
    style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="modal-content"
        style="background: white; border-radius: var(--radius-lg); max-width: 500px; width: 90%;">
        <div class="modal-header"
            style="padding: 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between;">
            <h3 style="font-size: 18px;">Export Report</h3>
            <button onclick="closeExportModal()"
                style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        <form method="GET" action="{{ route('leaves.export') }}" style="padding: 20px;">
            <div style="margin-bottom: 20px;">
                <label style="font-weight: 600; display: block; margin-bottom: 8px;">Select Format</label>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                    <label
                        style="border: 2px solid var(--border); border-radius: var(--radius-md); padding: 15px; text-align: center; cursor: pointer;">
                        <input type="radio" name="format" value="csv" style="display: none;"
                            onchange="this.parentElement.style.borderColor='var(--primary)'">
                        <div style="font-size: 24px; margin-bottom: 5px;">📊</div>
                        <div>CSV</div>
                    </label>
                    <label
                        style="border: 2px solid var(--border); border-radius: var(--radius-md); padding: 15px; text-align: center; cursor: pointer;">
                        <input type="radio" name="format" value="excel" style="display: none;"
                            onchange="this.parentElement.style.borderColor='var(--primary)'">
                        <div style="font-size: 24px; margin-bottom: 5px;">📈</div>
                        <div>Excel</div>
                    </label>
                    <label
                        style="border: 2px solid var(--border); border-radius: var(--radius-md); padding: 15px; text-align: center; cursor: pointer;">
                        <input type="radio" name="format" value="pdf" style="display: none;"
                            onchange="this.parentElement.style.borderColor='var(--primary)'">
                        <div style="font-size: 24px; margin-bottom: 5px;">📑</div>
                        <div>PDF</div>
                    </label>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="font-weight: 600; display: block; margin-bottom: 8px;">Date Range</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <input type="date" name="from_date" class="filter-input"
                        style="padding: 10px; border: 1px solid var(--border); border-radius: var(--radius-md);">
                    <input type="date" name="to_date" class="filter-input"
                        style="padding: 10px; border: 1px solid var(--border); border-radius: var(--radius-md);">
                </div>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeExportModal()"
                    style="padding: 10px 20px; border: 1px solid var(--border); border-radius: var(--radius-md); background: white; cursor: pointer;">Cancel</button>
                <button type="submit"
                    style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: var(--radius-md); cursor: pointer;">Export</button>
            </div>
        </form>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Monthly Chart Data
    const monthlyData = {!! json_encode($monthlyData ?? [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]) !!};
    const currentYear = {{ $currentYear ?? date('Y') }};

    let chartType = 'bar';
    let monthlyChart;

    // Initialize Monthly Chart
    function initMonthlyChart() {
        const ctx = document.getElementById('monthlyChart').getContext('2d');

        if (monthlyChart) {
            monthlyChart.destroy();
        }

        monthlyChart = new Chart(ctx, {
            type: chartType,
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Number of Leaves',
                    data: monthlyData,
                    backgroundColor: 'rgba(0, 123, 255, 0.5)',
                    borderColor: '#007bff',
                    borderWidth: 2,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Change Chart Type
    function changeChartType(type) {
        chartType = type;
        initMonthlyChart();
    }

    // Distribution Chart
    const distCtx = document.getElementById('distributionChart').getContext('2d');
    new Chart(distCtx, {
        type: 'doughnut',
        data: {
            labels: ['Annual Leave', 'Sick Leave', 'Casual Leave', 'Unpaid Leave'],
            datasets: [{
                data: [
                    {{ $leaveByType['annual'] ?? 0 }},
                    {{ $leaveByType['sick'] ?? 0 }},
                    {{ $leaveByType['casual'] ?? 0 }},
                    {{ $leaveByType['unpaid'] ?? 0 }}
                ],
                backgroundColor: [
                    '#007bff',
                    '#dc3545',
                    '#28a745',
                    '#6c757d'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Initialize charts on load
    document.addEventListener('DOMContentLoaded', function() {
        initMonthlyChart();
    });

    // Export Modal Functions
    function openExportModal() {
        document.getElementById('exportModal').style.display = 'flex';
    }

    function closeExportModal() {
        document.getElementById('exportModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('exportModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    // Auto-refresh dashboard every 5 minutes
    setTimeout(function() {
        location.reload();
    }, 300000); // 5 minutes
</script>
@endsection
