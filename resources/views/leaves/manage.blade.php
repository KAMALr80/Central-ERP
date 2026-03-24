@extends('layouts.app')

@section('page-title', 'Manage Leave Requests')

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
        background: #f4f6f9;
        color: var(--text-main);
        line-height: 1.5;
    }

    /* ================= MAIN CONTAINER ================= */
    .leave-page {
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

    .stats-badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 12px 24px;
        border-radius: 40px;
        font-weight: 600;
        font-size: clamp(14px, 3vw, 16px);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        white-space: nowrap;
    }

    /* ================= STATS CARDS ================= */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        padding: clamp(16px, 3vw, 20px);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary);
    }

    .stat-icon {
        width: clamp(45px, 7vw, 50px);
        height: clamp(45px, 7vw, 50px);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: clamp(20px, 4vw, 24px);
        flex-shrink: 0;
    }

    .stat-icon.pending {
        background: #fff3cd;
        color: #856404;
    }

    .stat-icon.approved {
        background: #d4edda;
        color: #155724;
    }

    .stat-icon.rejected {
        background: #f8d7da;
        color: #721c24;
    }

    .stat-icon.cancelled {
        background: #e9ecef;
        color: #495057;
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        font-size: clamp(12px, 2.5vw, 13px);
        color: var(--text-muted);
        margin-bottom: 5px;
        font-weight: 500;
    }

    .stat-value {
        font-size: clamp(24px, 5vw, 28px);
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.2;
    }

    /* ================= FILTERS SECTION ================= */
    .filters-card {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        padding: clamp(16px, 3vw, 20px);
        margin-bottom: 30px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
    }

    .filters-wrapper {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
    }

    .filters-left {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        flex: 1;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 10px;
        background: var(--bg-light);
        padding: 5px 10px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
    }

    .filter-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
    }

    .filter-select {
        padding: 8px 12px;
        border: none;
        background: transparent;
        font-size: 14px;
        color: var(--text-main);
        cursor: pointer;
        outline: none;
        min-width: 130px;
    }

    .search-box {
        position: relative;
        min-width: 250px;
    }

    .search-input {
        width: 100%;
        padding: 10px 40px 10px 15px;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 14px;
        transition: all 0.3s ease;
        outline: none;
    }

    .search-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }

    .search-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        pointer-events: none;
    }

    /* ================= TABLE CARD ================= */
    .table-card {
        background: var(--bg-white);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border);
        overflow: hidden;
        width: 100%;
    }

    .table-header {
        padding: clamp(16px, 3vw, 20px) clamp(20px, 4vw, 25px);
        background: linear-gradient(135deg, var(--bg-light) 0%, #e9ecef 100%);
        border-bottom: 2px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .table-title {
        margin: 0;
        font-size: clamp(18px, 4vw, 20px);
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .table-title span {
        background: var(--primary);
        width: 6px;
        height: 24px;
        border-radius: 3px;
        display: inline-block;
    }

    .entries-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .entries-select {
        padding: 8px 12px;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 14px;
        outline: none;
        background: white;
        cursor: pointer;
    }

    /* ================= TABLE ================= */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        width: 100%;
    }

    .leave-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1200px;
    }

    .leave-table thead tr {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
    }

    .leave-table th {
        padding: 15px 12px;
        font-size: 14px;
        font-weight: 600;
        text-align: left;
        border: 1px solid var(--primary-dark);
        white-space: nowrap;
    }

    .leave-table td {
        padding: 15px 12px;
        border: 1px solid var(--border);
        font-size: 14px;
        vertical-align: middle;
        transition: background 0.3s ease;
    }

    .leave-table tbody tr:hover td {
        background: #f1f9ff;
    }

    /* ================= EMPLOYEE INFO ================= */
    .employee-info {
        display: flex;
        align-items: center;
        gap: 12px;
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
        line-height: 1.4;
    }

    .employee-name {
        font-weight: 600;
        color: var(--text-main);
    }

    .employee-code {
        font-size: 11px;
        color: var(--text-muted);
    }

    .employee-department {
        font-size: 11px;
        color: var(--info);
        background: #e3f2fd;
        padding: 2px 6px;
        border-radius: 12px;
        display: inline-block;
        margin-top: 2px;
    }

    /* ================= LEAVE NUMBER ================= */
    .leave-number {
        font-family: monospace;
        font-weight: 600;
        color: var(--primary);
        background: #e3f2fd;
        padding: 4px 8px;
        border-radius: var(--radius-sm);
        font-size: 12px;
        display: inline-block;
    }

    /* ================= DATE BADGE ================= */
    .date-badge {
        background: var(--bg-light);
        padding: 4px 10px;
        border-radius: 16px;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border: 1px solid var(--border);
    }

    .date-badge i {
        font-size: 12px;
        color: var(--primary);
    }

    /* ================= STATUS BADGES ================= */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 12px;
        white-space: nowrap;
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

    /* ================= ACTION BUTTONS ================= */
    .action-buttons {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        border-radius: var(--radius-sm);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-action.view {
        background: var(--info);
    }

    .btn-action.approve {
        background: var(--success);
    }

    .btn-action.reject {
        background: var(--danger);
    }

    .btn-action.download {
        background: var(--primary);
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    /* ================= PAGINATION ================= */
    .pagination-wrapper {
        margin-top: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .pagination-info {
        font-size: 14px;
        color: var(--text-muted);
    }

    .pagination {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }

    .page-link {
        padding: 8px 12px;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        color: var(--text-main);
        text-decoration: none;
        transition: all 0.3s ease;
        background: white;
    }

    .page-link:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .page-item.active .page-link {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .page-item.disabled .page-link {
        opacity: 0.5;
        pointer-events: none;
    }

    /* ================= ALERTS ================= */
    .alert {
        padding: 15px 20px;
        border-radius: var(--radius-md);
        margin-bottom: 25px;
        font-weight: 500;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .alert-warning {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }

    /* ================= EMPTY STATE ================= */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-icon {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    .empty-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 5px;
    }

    .empty-text {
        color: #999;
        font-size: 14px;
    }

    /* ================= RESPONSIVE ================= */
    @media (max-width: 768px) {
        .filters-wrapper {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-group {
            width: 100%;
        }

        .search-box {
            width: 100%;
        }

        .stats-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="leave-page">
    <div class="container">
        <!-- Header Card -->
        <div class="header-card">
            <div class="header-left">
                <div class="header-icon">📊</div>
                <div class="header-title">
                    <h1>Leave Management</h1>
                    <p>Manage and review all leave requests</p>
                </div>
            </div>
            <div class="stats-badge">
                Total: {{ $leaves->total() ?? 0 }}
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success">
                <span>✅</span> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <span>❌</span> {{ session('error') }}
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon pending">⏳</div>
                <div class="stat-info">
                    <div class="stat-label">Pending</div>
                    <div class="stat-value">{{ $statistics['pending'] ?? 0 }}</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon approved">✅</div>
                <div class="stat-info">
                    <div class="stat-label">Approved</div>
                    <div class="stat-value">{{ $statistics['approved'] ?? 0 }}</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon rejected">❌</div>
                <div class="stat-info">
                    <div class="stat-label">Rejected</div>
                    <div class="stat-value">{{ $statistics['rejected'] ?? 0 }}</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon cancelled">↩️</div>
                <div class="stat-info">
                    <div class="stat-label">Cancelled</div>
                    <div class="stat-value">{{ $statistics['cancelled'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filters-card">
            <div class="filters-wrapper">
                <div class="filters-left">
                    <div class="filter-group">
                        <span class="filter-label">Status:</span>
                        <select name="status" class="filter-select" onchange="applyFilter('status', this.value)">
                            <option value="all">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <span class="filter-label">Type:</span>
                        <select name="leave_type" class="filter-select" onchange="applyFilter('leave_type', this.value)">
                            <option value="all">All Types</option>
                            <option value="annual" {{ request('leave_type') == 'annual' ? 'selected' : '' }}>Annual</option>
                            <option value="sick" {{ request('leave_type') == 'sick' ? 'selected' : '' }}>Sick</option>
                            <option value="casual" {{ request('leave_type') == 'casual' ? 'selected' : '' }}>Casual</option>
                            <option value="unpaid" {{ request('leave_type') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        </select>
                    </div>

                    <div class="search-box">
                        <input type="text" class="search-input" placeholder="Search leave number..."
                               value="{{ request('search') }}" onkeypress="if(event.key=='Enter') applyFilter('search', this.value)">
                        <span class="search-icon">🔍</span>
                    </div>
                </div>

                <div class="entries-wrapper">
                    <span class="entries-label">Show:</span>
                    <select class="entries-select" onchange="applyFilter('per_page', this.value)">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Leave Requests Table -->
        <div class="table-card">
            <div class="table-header">
                <h3 class="table-title">
                    <span></span> Leave Requests
                </h3>
                <div class="entries-wrapper">
                    <span>Total: {{ $leaves->total() }}</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="leave-table">
                    <thead>
                        <tr>
                            <th>Leave #</th>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Leave Type</th>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th>Applied On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $__col = $leaves; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $leave)
                        <tr>
                            <td>
                                <span class="leave-number">{{ $leave->leave_number }}</span>
                            </td>
                            <td>
                                <div class="employee-info">
                                    <div class="employee-avatar">
                                        {{ strtoupper(substr($leave->employee->name, 0, 1)) }}
                                    </div>
                                    <div class="employee-details">
                                        <div class="employee-name">{{ $leave->employee->name }}</div>
                                        <div class="employee-code">{{ $leave->employee->employee_code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="employee-department">{{ $leave->employee->department ?? 'N/A' }}</span>
                            </td>
                            <td>{{ ucfirst($leave->leave_type) }}</td>
                            <td>
                                <span class="date-badge">
                                    <i>📅</i> {{ \Carbon\Carbon::parse($leave->from_date)->format('d M Y') }}
                                </span>
                            </td>
                            <td>
                                <span class="date-badge">
                                    <i>📅</i> {{ \Carbon\Carbon::parse($leave->to_date)->format('d M Y') }}
                                </span>
                            </td>
                            <td><strong>{{ $leave->total_days }}</strong></td>
                            <td>
                                <span class="status-badge status-{{ strtolower($leave->status) }}">
                                    @if(strtolower($leave->status) == 'pending') ⏳
                                    @elseif(strtolower($leave->status) == 'approved') ✅
                                    @elseif(strtolower($leave->status) == 'rejected') ❌
                                    @elseif(strtolower($leave->status) == 'cancelled') ↩️
                                    @endif
                                    {{ ucfirst($leave->status) }}
                                </span>
                            </td>
                            <td>{{ Str::limit($leave->reason, 30) }}</td>
                            <td>{{ $leave->created_at->format('d M Y') }}</td>
                            <td>
    <div class="action-buttons">
        <!-- View button - use admin route for manage page -->
        <a href="{{ route('leaves.admin-show', $leave->id) }}" class="btn-action view" title="View">👁️</a>

        @if(strtolower($leave->status) == 'pending')
            <button onclick="openApproveModal({{ $leave->id }})" class="btn-action approve" title="Approve">✅</button>
            <button onclick="openRejectModal({{ $leave->id }})" class="btn-action reject" title="Reject">❌</button>
        @endif

        @if($leave->document_path)
            <a href="{{ route('leaves.download', $leave->id) }}" class="btn-action download" title="Download">📎</a>
        @endif
    </div>
</td>
                        </tr>
                        @endforeach
@else
                        <tr>
                            <td colspan="11">
                                <div class="empty-state">
                                    <div class="empty-icon">📭</div>
                                    <div class="empty-title">No leave requests found</div>
                                    <div class="empty-text">Try adjusting your filters</div>
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($leaves->hasPages())
        <div class="pagination-wrapper">
            <div class="pagination-info">
                Showing {{ $leaves->firstItem() ?? 0 }} to {{ $leaves->lastItem() ?? 0 }} of {{ $leaves->total() }} entries
            </div>
            <div class="pagination">
                {{ $leaves->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Approve Modal -->
<div class="modal" id="approveModal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Approve Leave Request</h3>
            <button class="modal-close" onclick="closeModal('approveModal')">&times;</button>
        </div>
        <form method="POST" action="" id="approveForm">
            @csrf
            <div class="modal-body">
                <p>Are you sure you want to approve this leave request?</p>
                <div style="margin-top: 15px;">
                    <label style="font-weight: 600; display: block; margin-bottom: 8px;">Remarks (Optional):</label>
                    <textarea name="remarks" rows="3" style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: var(--radius-md);" placeholder="Add any remarks..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-action" onclick="closeModal('approveModal')" style="background:#6c757d;">Cancel</button>
                <button type="submit" class="btn-action approve" style="width: auto; padding: 0 20px;">Approve</button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal" id="rejectModal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Reject Leave Request</h3>
            <button class="modal-close" onclick="closeModal('rejectModal')">&times;</button>
        </div>
        <form method="POST" action="" id="rejectForm">
            @csrf
            <div class="modal-body">
                <div style="margin-bottom: 15px;">
                    <label style="font-weight: 600; display: block; margin-bottom: 8px;">Rejection Reason <span style="color: var(--danger);">*</span></label>
                    <textarea name="rejection_reason" rows="3" style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: var(--radius-md);" placeholder="Please provide reason for rejection..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-action" onclick="closeModal('rejectModal')" style="background:#6c757d;">Cancel</button>
                <button type="submit" class="btn-action reject" style="width: auto; padding: 0 20px;">Reject</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Filter functions
    function applyFilter(name, value) {
        const url = new URL(window.location.href);
        if (value === 'all' || value === '') {
            url.searchParams.delete(name);
        } else {
            url.searchParams.set(name, value);
        }
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    // Modal functions
    function openModal(id) {
        document.getElementById(id).style.display = 'flex';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    function openApproveModal(leaveId) {
        const form = document.getElementById('approveForm');
        form.action = '{{ url("admin/leaves") }}/' + leaveId + '/approve';
        openModal('approveModal');
    }

    function openRejectModal(leaveId) {
        const form = document.getElementById('rejectForm');
        form.action = '{{ url("admin/leaves") }}/' + leaveId + '/reject';
        openModal('rejectModal');
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }
</script>

<style>
    /* Modal styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

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

    .modal-footer {
        padding: 20px;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
</style>
@endsection
