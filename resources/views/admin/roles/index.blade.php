@extends('layouts.app')

@section('page-title', 'Role Management')

@section('content')
    <style>
        .roles-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
        }

        .btn-create {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4);
        }

        /* Roles Grid */
        .roles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 24px;
        }

        .role-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 24px;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .role-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-color: #6366f1;
        }

        .role-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .role-name {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .role-badge {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .role-description {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 16px;
            line-height: 1.5;
            min-height: 40px;
        }

        .role-permissions {
            background: #f9fafb;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 16px;
        }

        .permissions-label {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .permission-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .permission-tag {
            background: #dbeafe;
            color: #0369a1;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 500;
        }

        .role-actions {
            display: flex;
            gap: 12px;
            border-top: 1px solid #e5e7eb;
            padding-top: 16px;
        }

        .btn-edit,
        .btn-delete {
            flex: 1;
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-edit {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
        }

        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-delete {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2);
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-delete:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
            border: 2px dashed #e5e7eb;
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        .empty-state-text {
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 16px;
        }

        /* Toast */
        .toast-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .toast-error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        /* Default Role Assignments */
        .default-roles-section {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 30px;
            border: 2px solid #0284c7;
        }

        .section-heading {
            font-size: 18px;
            font-weight: 700;
            color: #0c4a6e;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .roles-assignment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
        }

        .assignment-card {
            background: white;
            border-radius: 10px;
            padding: 16px;
            border-left: 4px solid;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .assignment-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .assignment-card.staff {
            border-left-color: #6366f1;
        }

        .assignment-card.hr {
            border-left-color: #10b981;
        }

        .assignment-card.delivery {
            border-left-color: #f59e0b;
        }

        .assignment-card.admin {
            border-left-color: #8b5cf6;
            background: linear-gradient(135deg, #f5f3ff 0%, #faf5ff 100%);
        }

        .assignment-user-type {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }

        .assignment-card.admin .assignment-user-type {
            color: #7c3aed;
        }

        .assignment-role-name {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .assignment-role-name.staff {
            color: #6366f1;
        }

        .assignment-role-name.hr {
            color: #10b981;
        }

        .assignment-role-name.delivery {
            color: #f59e0b;
        }

        .assignment-permissions {
            font-size: 12px;
            color: #6b7280;
        }

        .assignment-permissions strong {
            color: #1f2937;
            display: block;
            margin-bottom: 6px;
        }

        .assignment-permission-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .assignment-permission-list li {
            padding: 3px 0;
            padding-left: 18px;
            position: relative;
        }

        .assignment-permission-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #10b981;
            font-weight: bold;
        }

        .info-box {
            background: #f0f9ff;
            border-left: 4px solid #0284c7;
            padding: 12px;
            border-radius: 6px;
            margin-top: 16px;
            font-size: 12px;
            color: #0c4a6e;
            line-height: 1.5;
        }

        /* Assignment Card Actions */
        .assignment-actions {
            display: flex;
            gap: 10px;
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
        }

        .assignment-actions.disabled {
            display: none;
        }

        .btn-assignment-edit,
        .btn-assignment-delete {
            flex: 1;
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.3s ease;
            font-size: 12px;
            font-weight: 500;
        }

        .btn-assignment-edit {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 2px 6px rgba(59, 130, 246, 0.2);
        }

        .btn-assignment-edit:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(59, 130, 246, 0.3);
        }

        .btn-assignment-delete {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.2);
        }

        .btn-assignment-delete:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(239, 68, 68, 0.3);
        }

        .admin-badge {
            display: inline-block;
            background: #7c3aed;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            margin-left: 8px;
        }

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
            }

            .roles-grid {
                grid-template-columns: 1fr;
            }

            .role-actions {
                flex-direction: column;
            }

            .btn-edit,
            .btn-delete {
                width: 100%;
            }

            .roles-assignment-grid {
                grid-template-columns: 1fr;
            }

            .section-heading {
                font-size: 16px;
            }

            .default-roles-section {
                padding: 16px;
            }

            .assignment-actions {
                flex-direction: column;
            }

            .btn-assignment-edit,
            .btn-assignment-delete {
                width: 100%;
            }
        }
    </style>

    <div class="roles-container">
        {{-- Page Header --}}
        <div class="page-header">
            <h1 class="page-title">👥 Role Management</h1>
            @if(auth()->user()->hasPermission('create_roles'))
                <a href="{{ route('admin.roles.create') }}" class="btn-create">
                    <i class="fas fa-plus"></i> Create New Role
                </a>
            @endif
        </div>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="toast-notification toast-success" style="animation: slideIn 0.3s ease;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Error Message --}}
        @if (session('error'))
            <div class="toast-notification toast-error" style="animation: slideIn 0.3s ease;">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        {{-- Default Role Assignments Info --}}
        <div class="default-roles-section">
            <h2 class="section-heading">
                🎯 Default Role Assignments
            </h2>

            <div class="roles-assignment-grid">
                {{-- Admin Role --}}
                <div class="assignment-card admin">
                    <div class="assignment-user-type">👨‍💻 Admin User Type</div>
                    <div class="assignment-role-name staff" style="color: #8b5cf6;">
                        <i class="fas fa-crown"></i> Admin Role
                        <span class="admin-badge">Protected</span>
                    </div>
                    <div class="assignment-permissions">
                        <strong>📋 Assigned Permissions:</strong>
                        <ul class="assignment-permission-list">
                            <li>Full System Access</li>
                            <li>Manage Users & Roles</li>
                            <li>Manage All Modules</li>
                            <li>View Reports & Analytics</li>
                            <li>Approvals & Settings</li>
                        </ul>
                    </div>
                    <div class="info-box" style="margin: 12px 0 0 0; padding: 8px; font-size: 11px;">
                        <i class="fas fa-lock"></i> Admin role cannot be edited or deleted to maintain system integrity.
                    </div>
                </div>

                {{-- Staff Role --}}
                <div class="assignment-card staff">
                    <div class="assignment-user-type">👤 Staff User Type</div>
                    <div class="assignment-role-name staff">
                        <i class="fas fa-user"></i> Staff Role
                    </div>
                    <div class="assignment-permissions">
                        <strong>📋 Assigned Permissions:</strong>
                        <ul class="assignment-permission-list">
                            <li>View Dashboard</li>
                            <li>View & Create Sales</li>
                            <li>View & Create Customers</li>
                            <li>View Attendance</li>
                            <li>View Leaves</li>
                        </ul>
                    </div>
                    @if ($defaultRoles['staff'])
                        <div class="assignment-actions">
                            @if(auth()->user()->hasPermission('edit_roles'))
                                <a href="{{ route('admin.roles.edit', $defaultRoles['staff']) }}" class="btn-assignment-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            @endif
                            @if(auth()->user()->hasPermission('delete_roles'))
                                <form action="{{ route('admin.roles.destroy', $defaultRoles['staff']) }}" method="POST"
                                    style="flex: 1;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-assignment-delete"
                                        onclick="return confirm('Are you sure you want to delete this role?');"
                                        style="width: 100%;">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    @else
                        <div class="info-box">
                            <i class="fas fa-info-circle"></i> Role not found in database
                        </div>
                    @endif
                </div>

                {{-- HR Role --}}
                <div class="assignment-card hr">
                    <div class="assignment-user-type">👔 HR User Type</div>
                    <div class="assignment-role-name hr">
                        <i class="fas fa-briefcase"></i> HR Role
                    </div>
                    <div class="assignment-permissions">
                        <strong>📋 Assigned Permissions:</strong>
                        <ul class="assignment-permission-list">
                            <li>View Dashboard</li>
                            <li>View & Create & Edit Employees</li>
                            <li>View & Manage Attendance</li>
                            <li>View & Manage Leaves</li>
                            <li>View Reports</li>
                        </ul>
                    </div>
                    @if ($defaultRoles['hr'])
                        <div class="assignment-actions">
                            @if(auth()->user()->hasPermission('edit_roles'))
                                <a href="{{ route('admin.roles.edit', $defaultRoles['hr']) }}" class="btn-assignment-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            @endif
                            @if(auth()->user()->hasPermission('delete_roles'))
                                <form action="{{ route('admin.roles.destroy', $defaultRoles['hr']) }}" method="POST"
                                    style="flex: 1;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-assignment-delete"
                                        onclick="return confirm('Are you sure you want to delete this role?');"
                                        style="width: 100%;">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    @else
                        <div class="info-box">
                            <i class="fas fa-info-circle"></i> Role not found in database
                        </div>
                    @endif
                </div>

                {{-- Delivery Agent Role --}}
                <div class="assignment-card delivery">
                    <div class="assignment-user-type">🏍️ Delivery Agent User Type</div>
                    <div class="assignment-role-name delivery">
                        <i class="fas fa-motorcycle"></i> Delivery Agent Role
                    </div>
                    <div class="assignment-permissions">
                        <strong>📋 Assigned Permissions:</strong>
                        <ul class="assignment-permission-list">
                            <li>View Dashboard</li>
                            <li>View Logistics</li>
                            <li>Manage Logistics & Deliveries</li>
                            <li>Track Shipments</li>
                            <li>Manage Routes</li>
                        </ul>
                    </div>
                    @if ($defaultRoles['delivery_agent'])
                        <div class="assignment-actions">
                            @if(auth()->user()->hasPermission('edit_roles'))
                                <a href="{{ route('admin.roles.edit', $defaultRoles['delivery_agent']) }}"
                                    class="btn-assignment-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            @endif
                            @if(auth()->user()->hasPermission('delete_roles'))
                                <form action="{{ route('admin.roles.destroy', $defaultRoles['delivery_agent']) }}"
                                    method="POST" style="flex: 1;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-assignment-delete"
                                        onclick="return confirm('Are you sure you want to delete this role?');"
                                        style="width: 100%;">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    @else
                        <div class="info-box">
                            <i class="fas fa-info-circle"></i> Role not found in database
                        </div>
                    @endif
                </div>
            </div>

            <!-- <div class="info-box">
                <strong>ℹ️ Note:</strong> Ye default roles hain jo har user type ko automatically assign hote hain. Aap inhe
                edit ya delete kar sakte ho, lekin iska effect sab users par padega jinka ye role assigned hai.
            </div> -->
        </div>

        {{-- Roles Grid --}}
        <h2 class="section-heading" style="margin-top: 40px; margin-bottom: 24px;">
            🎨 Custom Roles
        </h2>
        @if ($roles->count() > 0)
            <div class="roles-grid">
                @forelse ($roles as $role)
                    <div class="role-card">
                        {{-- Role Header --}}
                        <div class="role-header">
                            <div class="role-name">
                                <span>{{ $role->name }}</span>
                                <span class="role-badge">{{ count($role->permissions ?? []) }} Permissions</span>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="role-description">
                            {{ $role->description ?? 'No description provided' }}
                        </div>

                        {{-- Permissions --}}
                        <div class="role-permissions">
                            <div class="permissions-label">
                                📋 Assigned Permissions
                            </div>
                            <div class="permission-tags">
                                @if (!empty($role->permissions))
                                    @foreach (array_slice($role->permissions, 0, 3) as $permission)
                                        @if (isset($availablePermissions[$permission]))
                                            <span class="permission-tag">
                                                {{ $availablePermissions[$permission] }}
                                            </span>
                                        @endif
                                    @endforeach
                                    @if (count($role->permissions) > 3)
                                        <span class="permission-tag">
                                            +{{ count($role->permissions) - 3 }} more
                                        </span>
                                    @endif
                                @else
                                    <span class="permission-tag">No permissions assigned</span>
                                @endif
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="role-actions">
                            @if(auth()->user()->hasPermission('edit_roles'))
                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            @endif
                            @if(auth()->user()->hasPermission('delete_roles'))
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" style="flex: 1;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete" style="width: 100%;"
                                        onclick="return confirm('Are you sure you want to delete this role?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty-state" style="grid-column: 1 / -1;">
                        <div class="empty-state-icon">📋</div>
                        <p class="empty-state-text">No roles found. Create your first role to get started!</p>
                        <a href="{{ route('admin.roles.create') }}" class="btn-create">
                            <i class="fas fa-plus"></i> Create First Role
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if ($roles->hasPages())
                <div style="margin-top: 30px; display: flex; justify-content: center;">
                    {{ $roles->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-state-icon">📋</div>
                <p class="empty-state-text">No roles found. Create your first role to get started!</p>
                <a href="{{ route('admin.roles.create') }}" class="btn-create">
                    <i class="fas fa-plus"></i> Create First Role
                </a>
            </div>
        @endif
    </div>

    <script>
        setTimeout(() => {
            const toast = document.querySelector('.toast-notification');
            if (toast) {
                toast.style.animation = 'slideOut 0.3s ease forwards';
                setTimeout(() => toast.remove(), 300);
            }
        }, 3000);
    </script>
@endsection
