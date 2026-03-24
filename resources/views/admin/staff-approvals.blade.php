@extends('layouts.app')

@section('title', 'Staff Approval')

@section('content')
    <style>
        /* ==================== PROFESSIONAL STAFF APPROVAL STYLES ==================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .staff-approval-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
            padding: clamp(16px, 3vw, 30px);
            width: 100%;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .approval-card {
            background: #ffffff;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Header */
        .approval-header {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            padding: clamp(1.5rem, 4vw, 2rem);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .approval-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            position: relative;
            z-index: 1;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex: 1;
            min-width: 280px;
        }

        .header-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            flex-shrink: 0;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        .header-title {
            font-size: clamp(1.5rem, 5vw, 2rem);
            font-weight: 700;
            margin: 0 0 0.5rem 0;
        }

        .header-subtitle {
            opacity: 0.9;
            font-size: clamp(0.9rem, 2.5vw, 1rem);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
            padding: 1.5rem clamp(1.5rem, 4vw, 2rem);
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
        }

        .stat-card {
            background: white;
            padding: 1.25rem;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            transition: all 0.2s;
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
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        .stat-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .stat-icon.pending {
            background: #fef3c7;
            color: #f59e0b;
        }

        .stat-icon.approved {
            background: #d1fae5;
            color: #10b981;
        }

        .stat-icon.total {
            background: #dbeafe;
            color: #3b82f6;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        /* Table Section */
        .table-section {
            padding: clamp(1.5rem, 4vw, 2rem);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title i {
            color: #667eea;
            font-size: 1.2rem;
        }

        .badge {
            background: #e2e8f0;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            color: #475569;
        }

        .table-responsive {
            overflow-x: auto;
            border-radius: 20px;
            border: 1px solid #e5e7eb;
            background: white;
        }

        .staff-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        .staff-table th {
            background: #f8fafc;
            padding: 1rem 1.25rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.85rem;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e5e7eb;
        }

        .staff-table td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        .staff-table tbody tr:hover {
            background: #f8fafc;
        }

        .staff-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* User Info */
        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1rem;
        }

        .user-details .user-name {
            font-weight: 600;
            color: #1e293b;
        }

        .user-details .user-email {
            font-size: 0.8rem;
            color: #64748b;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-badge.pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge.approved {
            background: #d1fae5;
            color: #065f46;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-dot.pending {
            background: #f59e0b;
        }

        .status-dot.approved {
            background: #10b981;
        }

        /* Buttons */
        .btn-approve {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-approve:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-approved {
            background: #e2e8f0;
            color: #475569;
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: default;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
            background: #f8fafc;
            border-radius: 20px;
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
            color: #64748b;
        }

        .empty-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .empty-text {
            color: #64748b;
            font-size: 0.9rem;
        }

        /* Toast */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border-left: 4px solid;
            display: none;
            z-index: 10000;
            animation: slideIn 0.3s ease;
        }

        .toast.success {
            border-left-color: #10b981;
        }

        .toast.error {
            border-left-color: #ef4444;
        }

        .toast-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #1e293b;
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

        @media (max-width: 768px) {
            .header-left {
                flex-direction: column;
                text-align: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .staff-table {
                min-width: 500px;
            }

            .btn-approve,
            .btn-approved {
                padding: 0.4rem 1rem;
                font-size: 0.8rem;
            }
        }
    </style>

    <div class="staff-approval-page">
        <div class="container">
            <div class="approval-card">
                <!-- Header -->
                <div class="approval-header">
                    <div class="header-content">
                        <div class="header-left">
                            <div class="header-icon">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div>
                                <h1 class="header-title">Staff Approval</h1>
                                <p class="header-subtitle">
                                    <i class="fas fa-shield-alt"></i> Manage and approve new staff registrations
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon total">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <div class="stat-value">{{ $staff->count() + ($approvedCount ?? 0) }}</div>
                                <div class="stat-label">Total Staff</div>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon pending">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <div class="stat-value">{{ $staff->count() }}</div>
                                <div class="stat-label">Pending Approval</div>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon approved">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <div class="stat-value">{{ $approvedCount ?? 0 }}</div>
                                <div class="stat-label">Approved</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="table-section">
                    <div class="section-header">
                        <div class="section-title">
                            <i class="fas fa-list"></i>
                            <span>Pending Approvals</span>
                            <span class="badge">{{ $staff->count() }} pending</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="staff-table">
                            <thead>
                                
                                <th>Staff Member</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                @php $__col = $staff; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $s)
                                    <tr>
                                        <td>
                                            <div class="user-info">
                                                <div class="user-avatar">
                                                    {{ strtoupper(substr($s->name, 0, 1)) }}
                                                </div>
                                                <div class="user-details">
                                                    <div class="user-name">{{ $s->name }}</div>
                                                    <div class="user-email">{{ $s->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $s->email }}</td>
                                        <td>
                                            <span class="status-badge pending">
                                                <span class="status-dot pending"></span>
                                                Pending
                                            </span>
                                        </td>
                                        <td>
                                            @if ($s->status === 'approved')
                                                <span class="btn-approved">
                                                    <i class="fas fa-check-circle"></i> Approved
                                                </span>
                                            @else
                                                <form method="POST" action="{{ route('admin.staff.approve', $s->id) }}"
                                                    style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn-approve"
                                                        onclick="return confirmApprove('{{ $s->name }}')">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
@else
                                    <tr>
                                        <td colspan="4">
                                            <div class="empty-state">
                                                <div class="empty-icon">
                                                    <i class="fas fa-user-check"></i>
                                                </div>
                                                <h3 class="empty-title">No Pending Approvals</h3>
                                                <p class="empty-text">All staff members have been approved</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

    <script>
        // ==================== CONFIRM APPROVAL ====================
        function confirmApprove(name) {
            return confirm(`✅ Approve ${name} as staff member?\n\nThis user will be able to access the staff dashboard.`);
        }

        // ==================== TOAST NOTIFICATION ====================
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.innerHTML = `
            <div class="toast-content">
                <span class="toast-icon">${type === 'success' ? '✅' : '❌'}</span>
                <span class="toast-message">${message}</span>
            </div>
        `;
            toast.className = 'toast ' + type;
            toast.style.display = 'block';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);
        }

        // Show toast from session messages
        @if (session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if (session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
    </script>
@endsection
