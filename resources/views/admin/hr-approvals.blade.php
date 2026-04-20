@extends('layouts.app')

@section('title', 'HR Approval')

@section('content')
    <style>
        .hr-approval-page {
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
        }

        .approval-header {
            background: linear-gradient(135deg, #4f46e5, #3730a3);
            padding: clamp(1.5rem, 4vw, 2rem);
            color: white;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-icon {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-right: 1.5rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
            padding: 1.5rem;
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
        }

        .stat-card {
            background: white;
            padding: 1.25rem;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
        }

        .table-section {
            padding: 2rem;
        }

        .hr-table {
            width: 100%;
            border-collapse: collapse;
        }

        .hr-table th {
            text-align: left;
            padding: 1rem;
            background: #f8fafc;
            border-bottom: 2px solid #e5e7eb;
            color: #64748b;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .hr-table td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: #4f46e5;
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .btn-approve {
            background: #10b981;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-reject {
            background: #ef4444;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 600;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-pending { background: #fef3c7; color: #92400e; }
        .status-active { background: #dbeafe; color: #1e40af; }
    </style>

    <div class="hr-approval-page">
        <div class="container">
            <div class="approval-card">
                <div class="approval-header">
                    <div class="header-content">
                        <div style="display: flex; align-items: center;">
                            <div class="header-icon">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div>
                                <h1 style="margin:0;">HR Approval</h1>
                                <p style="margin:0; opacity: 0.8;">Manage and verify HR department registrations</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value">{{ $hrList->count() + ($approvedCount ?? 0) }}</div>
                        <div style="color: #64748b;">Total HR</div>
                    </div>
                    <div class="stat-card" style="border-left: 4px solid #f59e0b;">
                        <div class="stat-value">{{ $hrList->count() }}</div>
                        <div style="color: #64748b;">Pending Approval</div>
                    </div>
                    <div class="stat-card" style="border-left: 4px solid #10b981;">
                        <div class="stat-value">{{ $approvedCount ?? 0 }}</div>
                        <div style="color: #64748b;">Approved</div>
                    </div>
                    <div class="stat-card" style="border-left: 4px solid #ef4444;">
                        <div class="stat-value">{{ $rejectedCount ?? 0 }}</div>
                        <div style="color: #64748b;">Rejected</div>
                    </div>
                </div>

                <div class="table-section">
                    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
                        <h3 style="margin:0;">Unapproved HR Entries</h3>
                        <span style="background: #e2e8f0; padding: 2px 10px; border-radius: 10px; font-size: 12px;">{{ $hrList->count() }} baki</span>
                    </div>

                    <div style="overflow-x: auto;">
                        <table class="hr-table">
                            <thead>
                                <tr>
                                    <th>HR Member</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hrList as $hr)
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 12px;">
                                                <div class="user-avatar">{{ strtoupper(substr($hr->name, 0, 1)) }}</div>
                                                <div>
                                                    <div style="font-weight: 600;">{{ $hr->name }}</div>
                                                    <div style="font-size: 0.8rem; color: #64748b;">{{ $hr->created_at->format('d M, Y') }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $hr->email }}</td>
                                        <td>
                                            <span class="status-badge {{ $hr->status === 'pending' ? 'status-pending' : 'status-active' }}">
                                                {{ ucfirst($hr->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 10px;">
                                                <form action="{{ route('admin.hr.approve', $hr->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn-approve" onclick="return confirm('Approve {{ $hr->name }}?')">Approve</button>
                                                </form>
                                                <form action="{{ route('admin.hr.reject', $hr->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn-reject" onclick="return confirm('Reject {{ $hr->name }}?')">Reject</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 3rem; color: #64748b;">
                                            <i class="fas fa-check-circle" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                                            <div>No HR entries awaiting approval</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
