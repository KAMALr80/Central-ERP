@extends('layouts.app')

@section('page-title', 'Employee Report')

@section('content')
<style>
    /* ================= PROFESSIONAL EMPLOYEE REPORT STYLES ================= */
    :root {
        --primary: #2563eb;
        --primary-dark: #1d4ed8;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --info: #3b82f6;
        --purple: #8b5cf6;
        --text-main: #1f2937;
        --text-muted: #6b7280;
        --border: #e5e7eb;
        --bg-light: #f9fafb;
        --bg-white: #ffffff;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --radius-sm: 0.375rem;
        --radius-md: 0.5rem;
        --radius-lg: 0.75rem;
        --radius-xl: 1rem;
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
    }

    .report-wrapper {
        padding: 2rem 1rem;
        min-height: 100vh;
        width: 100%;
    }

    .report-container {
        max-width: 1600px;
        margin: 0 auto;
        width: 100%;
    }

    .report-header {
        background: var(--bg-white);
        border-radius: var(--radius-xl);
        padding: 1.5rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .header-title h1 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 0.25rem;
        color: var(--text-main);
    }

    .header-title p {
        color: var(--text-muted);
        font-size: 0.875rem;
        margin: 0;
    }

    .header-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .btn {
        padding: 0.5rem 1rem;
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .btn-success {
        background: var(--success);
        color: white;
    }

    .btn-success:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: var(--bg-light);
        color: var(--text-main);
        border-color: var(--border);
    }

    .btn-secondary:hover {
        background: var(--border);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        padding: 1.25rem;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        transition: all 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .stat-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 0.25rem;
    }

    .stat-sub {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .stat-value.positive {
        color: var(--success);
    }

    .stat-value.negative {
        color: var(--danger);
    }

    .stat-value.warning {
        color: var(--warning);
    }

    .filter-section {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        border: 1px solid var(--border);
    }

    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: flex-end;
    }

    .filter-group {
        flex: 1;
        min-width: 150px;
    }

    .filter-group label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
        text-transform: uppercase;
    }

    .filter-group input,
    .filter-group select {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .filter-group input:focus,
    .filter-group select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: 0.5rem;
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-active {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .table-container {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .table-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border);
        background: var(--bg-light);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .table-header h2 {
        font-size: 1rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
        min-width: 1000px;
    }

    .data-table thead th {
        background: var(--bg-light);
        padding: 0.75rem 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--text-muted);
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }

    .data-table tbody td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    .data-table tbody tr:hover {
        background: var(--bg-light);
    }

    .serial-cell {
        width: 60px;
        text-align: center;
        font-weight: 600;
        color: var(--text-muted);
    }

    .employee-code {
        font-family: monospace;
        font-weight: 600;
        color: var(--primary);
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .pagination-wrapper {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-end;
    }

    .pagination {
        display: flex;
        gap: 0.25rem;
        flex-wrap: wrap;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .page-item .page-link {
        display: inline-block;
        padding: 0.5rem 0.75rem;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        color: var(--text-main);
        text-decoration: none;
        font-size: 0.875rem;
        transition: all 0.2s;
        background: white;
    }

    .page-item .page-link:hover {
        border-color: var(--primary);
        background: var(--bg-light);
    }

    .page-item.active .page-link {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--text-muted);
    }

    .empty-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .chart-card {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        padding: 1.25rem;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
    }

    .chart-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .chart-container {
        height: 250px;
        position: relative;
    }

    .department-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .department-card {
        background: var(--bg-light);
        border-radius: var(--radius-md);
        padding: 0.75rem;
        text-align: center;
        border: 1px solid var(--border);
    }

    .department-name {
        font-weight: 600;
        color: var(--text-main);
        font-size: 0.85rem;
        word-break: break-word;
    }

    .department-count {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary);
    }

    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 0.75rem 1rem;
        border-radius: var(--radius-md);
        background: white;
        box-shadow: var(--shadow-lg);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        z-index: 1000;
        border-left: 4px solid;
        animation: slideIn 0.3s ease;
    }

    .toast-notification.success {
        border-left-color: var(--success);
    }

    .toast-notification.error {
        border-left-color: var(--danger);
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(4px);
        z-index: 10000;
        display: none;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 1rem;
    }

    .loading-overlay.active {
        display: flex;
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 3px solid var(--border);
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    @media (max-width: 768px) {
        .report-wrapper {
            padding: 1rem;
        }

        .report-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-actions {
            width: 100%;
        }

        .btn {
            flex: 1;
            justify-content: center;
        }

        .filter-form {
            flex-direction: column;
        }

        .filter-group {
            width: 100%;
        }

        .filter-actions {
            width: 100%;
        }

        .filter-actions button {
            flex: 1;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .charts-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .table-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .department-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media print {

        .header-actions,
        .filter-section,
        .pagination-wrapper,
        .btn {
            display: none !important;
        }
    }
</style>

<div class="report-wrapper">
    <div class="report-container">
        <div id="loadingOverlay" class="loading-overlay">
            <div class="spinner"></div>
            <div class="loading-text">Generating PDF...</div>
        </div>

        <div class="report-header">
            <div class="header-title">
                <h1>👥 Employee Report</h1>
                <p>Complete employee analytics and workforce summary</p>
            </div>
            <div class="header-actions">
                <button onclick="exportReport('csv')" class="btn btn-success">
                    📥 Export CSV
                </button>
                <button onclick="exportReport('pdf')" class="btn btn-primary">
                    📄 Export PDF
                </button>
                <button onclick="window.print()" class="btn btn-secondary">
                    🖨️ Print
                </button>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Employees</div>
                <div class="stat-value">{{ number_format($stats['total_employees']) }}</div>
                <div class="stat-sub">{{ $stats['active_employees'] }} active</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Active Rate</div>
                <div class="stat-value positive">{{ $stats['active_rate'] }}%</div>
                <div class="stat-sub">Active workforce</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Departments</div>
                <div class="stat-value">{{ number_format($stats['department_count']) }}</div>
                <div class="stat-sub">Unique departments</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">New Hires</div>
                <div class="stat-value warning">{{ number_format($stats['new_hires']) }}</div>
                <div class="stat-sub">This period</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Inactive</div>
                <div class="stat-value negative">{{ number_format($stats['inactive_employees']) }}</div>
                <div class="stat-sub">Inactive employees</div>
            </div>
        </div>

        <div class="filter-section">
            <form method="GET" action="{{ route('reports.employees') }}" class="filter-form" id="filterForm">
                <div class="filter-group">
                    <label>From Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}">
                </div>

                <div class="filter-group">
                    <label>To Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}">
                </div>

                <div class="filter-group">
                    <label>Department</label>
                    <select name="department">
                        <option value="all" {{ $filters['department'] == 'all' ? 'selected' : '' }}>All Departments
                        </option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept }}"
                                {{ $filters['department'] == $dept ? 'selected' : '' }}>
                                {{ $dept }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="all" {{ $filters['status'] == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="active" {{ $filters['status'] == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $filters['status'] == 'inactive' ? 'selected' : '' }}>Inactive
                        </option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Search</label>
                    <input type="text" name="search" value="{{ $filters['search'] }}"
                        placeholder="Name, Code, Email...">
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                    <a href="{{ route('reports.employees') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-title">
                    <span>📈</span> New Hires Trend
                </div>
                <div class="chart-container">
                    <canvas id="hiringTrendChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-title">
                    <span>🥧</span> Role Distribution
                </div>
                <div class="chart-container">
                    <canvas id="roleChart"></canvas>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h2>
                    <span>📋</span>
                    Employee List
                    <span style="font-weight: normal; color: var(--text-muted);">
                        ({{ $employees->total() }} records)
                    </span>
                </h2>
                <div>
                    <input type="text" id="tableSearch" placeholder="Search in table..."
                        style="padding: 0.5rem 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 0.875rem; width: 200px;">
                </div>
            </div>

            <div class="table-responsive">
                <table class="data-table" id="employeeTable">
                    <thead>
                        <tr>
                            <th class="serial-cell">#</th>
                            <th>Employee Code</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Department</th>
                            <th>Role</th>
                            <th>Joining Date</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $index => $employee)
                            @php
                                $serial = ($employees->currentPage() - 1) * $employees->perPage() + $index + 1;
                                $statusClass = $employee->status == 1 ? 'badge-active' : 'badge-inactive';
                                $statusText = $employee->status == 1 ? 'Active' : 'Inactive';
                                $role = isset($employee->user) ? ucfirst($employee->user->role) : 'Staff';
                                $phone = isset($employee->phone) ? $employee->phone : 'N/A';
                                $departmentName = isset($employee->department) ? $employee->department : 'Not Assigned';
                                $joiningDate = isset($employee->joining_date) ? \Carbon\Carbon::parse($employee->joining_date)->format('d M Y') : 'N/A';
                            @endphp
                            <tr>
                                <td class="serial-cell">{{ $serial }}</td>
                                <td><span class="employee-code">{{ $employee->employee_code }}</span></td>
                                <td><strong>{{ $employee->name }}</strong></td>
                                <td>{{ $employee->email }}</td>
                                <td>{{ $phone }}</td>
                                <td>{{ $departmentName }}</td>
                                <td>{{ $role }}</td>
                                <td>{{ $joiningDate }}</td>
                                <td><span class="badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                <td class="text-center">
                                    <div class="action-buttons" style="display: flex; gap: 0.25rem; justify-content: center;">
                                        <a href="{{ route('employees.show', $employee->id) }}" class="btn-sm"
                                            style="padding: 0.25rem 0.5rem; background: #e0f2fe; border-radius: 4px; text-decoration: none; color: #0369a1;"
                                            title="View">
                                            👁️
                                        </a>
                                        @if (auth()->user()->hasPermission('edit_employees'))
                                            <a href="{{ route('employees.edit', $employee->id) }}" class="btn-sm"
                                                style="padding: 0.25rem 0.5rem; background: #fef3c7; border-radius: 4px; text-decoration: none; color: #92400e;"
                                                title="Edit">
                                                ✏️
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">
                                    <div class="empty-state">
                                        <div class="empty-icon">👥</div>
                                        <div class="empty-title">No employees found</div>
                                        <div class="empty-text">Try adjusting your filters</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($employees->count() > 0)
                        <tfoot>
                            <tr style="background: var(--bg-light); font-weight: 600;">
                                <td colspan="9" class="text-right"><strong>Total:</strong></td>
                                <td class="text-center"><strong>{{ $employees->total() }}</strong></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            @if ($employees->hasPages())
                <div class="pagination-wrapper">
                    {{ $employees->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart data from PHP
    const monthlyTrendData = @json($monthlyTrend);
    const roleBreakdown = @json($stats['role_breakdown']);
    const departmentStats = @json($departmentStats);

    let hiringChart, roleChart;

    function initCharts() {
        // Hiring Trend Chart
        const hiringCtx = document.getElementById('hiringTrendChart');
        if (hiringCtx && monthlyTrendData && monthlyTrendData.length > 0) {
            hiringChart = new Chart(hiringCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: monthlyTrendData.map(function(d) { return d.date; }),
                    datasets: [{
                        label: 'New Hires',
                        data: monthlyTrendData.map(function(d) { return d.total; }),
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#2563eb',
                        pointBorderColor: 'white',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.raw + ' new hires';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        }

        // Role Distribution Chart
        const roleCtx = document.getElementById('roleChart');
        if (roleCtx && roleBreakdown) {
            const roleLabels = Object.keys(roleBreakdown).map(function(r) { return r.toUpperCase(); });
            const roleData = Object.values(roleBreakdown);

            hiringChart = new Chart(roleCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: roleLabels,
                    datasets: [{
                        data: roleData,
                        backgroundColor: ['#ef4444', '#f59e0b', '#10b981'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, font: { size: 10 } }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.raw + ' employees';
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
            });
        }
    }

    function getFilterParams() {
        var params = new URLSearchParams();
        var startDate = document.querySelector('input[name="start_date"]') ? document.querySelector('input[name="start_date"]').value : '';
        var endDate = document.querySelector('input[name="end_date"]') ? document.querySelector('input[name="end_date"]').value : '';
        var department = document.querySelector('select[name="department"]') ? document.querySelector('select[name="department"]').value : 'all';
        var status = document.querySelector('select[name="status"]') ? document.querySelector('select[name="status"]').value : 'all';
        var search = document.querySelector('input[name="search"]') ? document.querySelector('input[name="search"]').value : '';

        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        if (department !== 'all') params.append('department', department);
        if (status !== 'all') params.append('status', status);
        if (search) params.append('search', search);

        return params.toString();
    }

    function exportReport(type) {
        var params = getFilterParams();
        var url = '';

        if (type === 'csv') {
            url = '{{ route('reports.employees.excel') }}?' + params;
            window.location.href = url;
            showToast('CSV export started!', 'success');
        } else if (type === 'pdf') {
            var loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.classList.add('active');
            url = '{{ route('reports.employees.pdf') }}?' + params;
            window.open(url, '_blank');
            setTimeout(function() {
                loadingOverlay.classList.remove('active');
                showToast('PDF generated successfully!', 'success');
            }, 2000);
        }
    }

    var searchInput = document.getElementById('tableSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            var searchTerm = this.value.toLowerCase();
            var table = document.getElementById('employeeTable');
            var rows = table.getElementsByTagName('tbody')[0] ? table.getElementsByTagName('tbody')[0].getElementsByTagName('tr') : [];
            if (!rows) return;
            for (var i = 0; i < rows.length; i++) {
                var text = rows[i].textContent.toLowerCase();
                rows[i].style.display = text.indexOf(searchTerm) !== -1 ? '' : 'none';
            }
        });
    }

    function showToast(message, type) {
        type = type || 'success';
        var existingToast = document.querySelector('.toast-notification');
        if (existingToast) existingToast.remove();
        var toast = document.createElement('div');
        toast.className = 'toast-notification ' + type;
        var icon = type === 'success' ? '✅' : (type === 'error' ? '❌' : '⚠️');
        toast.innerHTML = '<span>' + icon + '</span><span>' + message + '</span>';
        document.body.appendChild(toast);
        setTimeout(function() { toast.remove(); }, 3000);
    }

    document.addEventListener('DOMContentLoaded', initCharts);

    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif
    @if(session('error'))
        showToast("{{ session('error') }}", 'error');
    @endif
</script>
@endsection
