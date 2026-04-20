@extends('layouts.app')

@section('page-title', 'User Access Details: ' . $user->name)

@section('content')
<style>
    .detail-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .detail-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        padding: 30px;
        margin-bottom: 30px;
    }

    .detail-header {
        display: flex;
        align-items: center;
        gap: 25px;
        margin-bottom: 40px;
        padding-bottom: 25px;
        border-bottom: 1px solid #f1f5f9;
    }

    .detail-avatar {
        width: 100px;
        height: 100px;
        border-radius: 20px;
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 40px;
        font-weight: 700;
        box-shadow: 0 10px 15px rgba(99, 102, 241, 0.2);
    }

    .detail-title h1 {
        font-size: 28px;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
    }

    .detail-title p {
        color: #64748b;
        margin: 5px 0 0 0;
        font-size: 16px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 30px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .info-label {
        font-size: 12px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .info-value {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
    }

    .stat-card {
        background: #f8fafc;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        border: 1px solid #e2e8f0;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 800;
        color: #6366f1;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #64748b;
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 20px;
        transition: color 0.2s;
    }

    .btn-back:hover {
        color: #1e293b;
    }
</style>

<div class="detail-container">
    <a href="{{ route('admin.users.access') }}" class="btn-back">
        <span>⬅️</span> Back to Login Details
    </a>

    <div class="detail-card">
        <div class="detail-header">
            <div class="detail-avatar">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div class="detail-title">
                <h1>{{ $user->name }}</h1>
                <p>{{ $user->role_display }} • {{ $user->status_display }}</p>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Email Address</span>
                <span class="info-value">{{ $user->email }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">User Role</span>
                <span class="info-value">{{ ucfirst($user->role) }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Account Status</span>
                <span class="info-value">{{ ucfirst($user->status) }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Online Status</span>
                <span class="info-value">{{ $user->online_status }}</span>
            </div>
        </div>

        <div style="margin-top: 40px; border-top: 1px solid #f1f5f9; padding-top: 40px;">
            <h3 style="font-size: 18px; margin-bottom: 20px; color: #1e293b;">Access & Login History</h3>
            <div class="info-grid" style="grid-template-columns: repeat(3, 1fr);">
                <div class="stat-card">
                    <div class="stat-value">{{ $user->login_count ?? 0 }}</div>
                    <div class="stat-label">Total Logins</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="font-size: 16px;">{{ $user->last_login_at ? $user->last_login_at->format('d M, Y') : 'Never' }}</div>
                    <div class="stat-label">Last Login Date</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="font-size: 16px;">{{ $user->last_login_ip ?? 'Unknown' }}</div>
                    <div class="stat-label">Last Login IP</div>
                </div>
            </div>
        </div>

        @if($user->employee)
        <div style="margin-top: 40px; background: #f1f5f9; border-radius: 12px; padding: 20px;">
            <h3 style="font-size: 16px; margin-bottom: 10px;">Linked Employee Profile</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Employee Code</span>
                    <span class="info-value">{{ $user->employee->employee_code }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Department</span>
                    <span class="info-value">{{ $user->employee->department }}</span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
