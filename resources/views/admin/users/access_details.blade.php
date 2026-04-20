@extends('layouts.app')

@section('page-title', 'User Login Details')

@section('content')
<style>
    .access-container {
        max-width: 1200px;
        margin: 0 auto;
    }
.w-5.h-5{
    width:20px;
}
    .filter-section {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-select {
        padding: 8px 15px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        outline: none;
        min-width: 150px;
    }

    .user-table-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .access-table {
        width: 100%;
        border-collapse: collapse;
    }

    .access-table th {
        background: #f8fafc;
        padding: 15px 20px;
        text-align: left;
        font-size: 13px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #edf2f7;
    }

    .access-table td {
        padding: 15px 20px;
        border-bottom: 1px solid #edf2f7;
        font-size: 14px;
        vertical-align: middle;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 16px;
    }

    .user-name {
        font-weight: 600;
        color: #1e293b;
    }

    .user-email {
        font-size: 12px;
        color: #64748b;
    }

    .role-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .role-admin { background: #fee2e2; color: #b91c1c; }
    .role-hr { background: #dcfce7; color: #15803d; }
    .role-staff { background: #dbeafe; color: #1d4ed8; }
    .role-delivery_agent { background: #fef3c7; color: #b45309; }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-weight: 600;
        font-size: 13px;
    }

    .btn-view {
        background: #f1f5f9;
        color: #475569;
        padding: 8px 16px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-view:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    .login-stats {
        font-size: 12px;
        color: #64748b;
        line-height: 1.4;
    }

    .password-field {
        font-family: monospace;
        color: #94a3b8;
        letter-spacing: 2px;
    }
</style>

<div class="access-container">
    <div class="filter-section">
        <h2 style="font-size: 20px; font-weight: 700; color: #1e293b; margin: 0;">Login Details Overview</h2>
        
        <form action="{{ route('admin.users.access') }}" method="GET" class="filter-group">
            <select name="role" class="form-select" onchange="this.form.submit()">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->slug }}" {{ $roleFilter == $role->slug ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            <a href="{{ route('admin.users.access') }}" class="btn-view" style="background: #fff; border: 1px solid #e5e7eb;">Clear</a>
        </form>
    </div>

    <div class="user-table-card">
        <div class="table-responsive">
            <table class="access-table">
                <thead>
                    <tr>
                        <th>User Details</th>
                        <th>Role</th>
                        <th>Login Stats</th>
                        <th>Last Login</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="user-name">{{ $user->name }}</div>
                                    <div class="user-email">📧 {{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="role-badge role-{{ $user->role }}">
                                {{ str_replace('_', ' ', $user->role) }}
                            </span>
                        </td>
                        <td>
                            <div class="login-stats">
                                <div>Count: <strong>{{ $user->login_count ?? 0 }}</strong></div>
                                <div>IP: {{ $user->last_login_ip ?? 'N/A' }}</div>
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 13px; color: #1e293b;">
                                {{ $user->last_login_at ? $user->last_login_at->format('d M, h:i A') : 'Never' }}
                            </div>
                            <div style="font-size: 11px; color: #94a3b8;">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : '-' }}
                            </div>
                        </td>
                        <td>
                            <span class="status-badge" style="color: {{ $user->status == 'active' ? '#10b981' : '#f43f5e' }}">
                                {{ $user->status_display }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('admin.users.access.show', $user->id) }}" class="btn-view">
                                    <span>👁️</span> Detail
                                </a>
                                @if(auth()->id() !== $user->id)
                                <form action="{{ route('admin.users.access.impersonate', $user->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="btn-view" style="background: #6366f1; color: white;">
                                        <span>🔑</span> Login
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div style="margin-top: 20px;">
        {{ $users->appends(['role' => $roleFilter])->links() }}
    </div>

    <div style="margin-top: 30px; background: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; border-radius: 8px; font-size: 13px; color: #92400e;">
        <strong>⚠️ Security Note:</strong> Passwords are encrypted in the database and cannot be shown in plain text for security reasons. To change a user's password, please use the Edit User function.
    </div>
</div>
@endsection
