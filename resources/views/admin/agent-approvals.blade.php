@extends('layouts.app')

@section('title', 'Agent Approval')

@section('content')
    <style>
        .agent-approval-page {
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
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            flex-shrink: 0;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4);
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
            background: linear-gradient(135deg, #10b981, #059669);
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

        .stat-icon.rejected {
            background: #fee2e2;
            color: #ef4444;
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
            color: #10b981;
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

        .agent-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        .agent-table th {
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

        .agent-table td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        .agent-table tbody tr:hover {
            background: #f8fafc;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .user-details .user-name {
            font-weight: 600;
            color: #1e293b;
        }

        .user-details .user-email {
            font-size: 0.8rem;
            color: #64748b;
        }

        .agent-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            background: #f1f5f9;
            border-radius: 20px;
            font-size: 0.75rem;
            color: #475569;
            margin: 0.25rem 0;
        }

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

        .status-badge.rejected {
            background: #fee2e2;
            color: #991b1b;
        }

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
            margin-right: 0.5rem;
        }

        .btn-approve:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-reject {
            background: linear-gradient(135deg, #ef4444, #dc2626);
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

        .btn-reject:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(239, 68, 68, 0.3);
        }

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

        @media (max-width: 768px) {
            .agent-table {
                min-width: 600px;
            }

            .btn-approve,
            .btn-reject {
                padding: 0.4rem 1rem;
                font-size: 0.8rem;
            }

            .action-buttons {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>

    <div class="agent-approval-page">
        <div class="container">
            <div class="approval-card">
                <div class="approval-header">
                    <div class="header-content">
                        <div class="header-left">
                            <div class="header-icon">
                                <i class="fas fa-motorcycle"></i>
                            </div>
                            <div>
                                <h1 class="header-title">Delivery Agent Approval</h1>
                                <p class="header-subtitle">
                                    <i class="fas fa-shield-alt"></i> Manage and approve new delivery agent registrations
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon total"><i class="fas fa-users"></i></div>
                            <div>
                                <div class="stat-value">{{ $totalAgents ?? 0 }}</div>
                                <div class="stat-label">Total Agents</div>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon pending"><i class="fas fa-clock"></i></div>
                            <div>
                                <div class="stat-value">{{ $pendingAgents->count() ?? 0 }}</div>
                                <div class="stat-label">Pending Approval</div>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon approved"><i class="fas fa-check-circle"></i></div>
                            <div>
                                <div class="stat-value">{{ $approvedCount ?? 0 }}</div>
                                <div class="stat-label">Approved</div>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon rejected"><i class="fas fa-times-circle"></i></div>
                            <div>
                                <div class="stat-value">{{ $rejectedCount ?? 0 }}</div>
                                <div class="stat-label">Rejected</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-section">
                    <div class="section-header">
                        <div class="section-title">
                            <i class="fas fa-list"></i>
                            <span>Pending Agent Approvals</span>
                            <span class="badge">{{ $pendingAgents->count() ?? 0 }} pending</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="agent-table">
                            <thead>
                                
                                <th>Agent</th>
                                <th>Contact</th>
                                <th>Vehicle</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                @php $__col = $pendingAgents ?: []; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $agent)
                                    <tr>
                                        <td>
                                            <div class="user-info">
                                                <div class="user-avatar">{{ strtoupper(substr($agent->name, 0, 1)) }}</div>
                                                <div class="user-details">
                                                    <div class="user-name">{{ $agent->name }}</div>
                                                    <div class="user-email">{{ $agent->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="agent-badge"><i class="fas fa-phone"></i>
                                                {{ $agent->phone ?? 'N/A' }}</div>
                                        </td>
                                        <td>
                                            <div class="agent-badge"><i class="fas fa-motorcycle"></i>
                                                {{ ucfirst($agent->vehicle_type ?? 'Not specified') }}</div>
                                            @if ($agent->vehicle_number)
                                                <div class="agent-badge"><i class="fas fa-hashtag"></i>
                                                    {{ $agent->vehicle_number }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($agent->city)
                                                <div class="agent-badge"><i class="fas fa-city"></i> {{ $agent->city }}
                                                </div>
                                            @endif
                                            @if ($agent->pincode)
                                                <div class="agent-badge"><i class="fas fa-map-pin"></i>
                                                    {{ $agent->pincode }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="status-badge pending"><span class="status-dot pending"></span>
                                                Pending</span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <form method="POST"
                                                    action="{{ route('admin.agent.approve', $agent->id) }}"
                                                    style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn-approve"
                                                        onclick="return confirm('✅ Approve {{ $agent->name }} as delivery agent?')">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                </form>
                                                <button type="button" class="btn-reject"
                                                    onclick="rejectAgent({{ $agent->id }}, '{{ $agent->name }}')">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
@else
                                    <tr>
                                        <td colspan="6">
                                            <div class="empty-state">
                                                <div class="empty-icon"><i class="fas fa-check-circle"></i></div>
                                                <h3>No Pending Approvals</h3>
                                                <p>All delivery agents have been processed</p>
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

    <div id="rejectModal"
        style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:10000; align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:20px; max-width:500px; width:90%; padding:25px;">
            <h3 style="margin-bottom:15px;">Reject Agent</h3>
            <p id="rejectAgentName" style="margin-bottom:15px;"></p>
            <textarea id="rejectReason" placeholder="Reason for rejection (optional)"
                style="width:100%; padding:12px; border:1px solid #e5e7eb; border-radius:12px; margin-bottom:20px; resize:vertical;"
                rows="3"></textarea>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button onclick="closeRejectModal()"
                    style="padding:10px 20px; background:#f1f5f9; border:none; border-radius:10px; cursor:pointer;">Cancel</button>
                <button id="confirmRejectBtn"
                    style="padding:10px 20px; background:#ef4444; color:white; border:none; border-radius:10px; cursor:pointer;">Reject</button>
            </div>
        </div>
    </div>

    <script>
        let currentRejectId = null;

        function rejectAgent(id, name) {
            currentRejectId = id;
            document.getElementById('rejectAgentName').innerHTML =
                `Are you sure you want to reject <strong>${name}</strong>?`;
            document.getElementById('rejectReason').value = '';
            document.getElementById('rejectModal').style.display = 'flex';
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
            currentRejectId = null;
        }

        document.getElementById('confirmRejectBtn').addEventListener('click', function() {
            if (currentRejectId) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/agent-reject/${currentRejectId}`;
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);
                const reason = document.createElement('input');
                reason.type = 'hidden';
                reason.name = 'reason';
                reason.value = document.getElementById('rejectReason').value;
                form.appendChild(reason);
                document.body.appendChild(form);
                form.submit();
            }
        });

        @if (session('success'))
            setTimeout(() => {
                alert('{{ session('success') }}');
            }, 100);
        @endif
        @if (session('error'))
            setTimeout(() => {
                alert('❌ {{ session('error') }}');
            }, 100);
        @endif
    </script>
@endsection
