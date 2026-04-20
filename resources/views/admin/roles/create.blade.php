@extends('layouts.app')

@section('page-title', 'Create New Role')

@section('content')
    <style>
        .create-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .form-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
        }

        .form-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
        }

        .form-title {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Form Group */
        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-input,
        .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .form-input:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-help {
            font-size: 12px;
            color: #6b7280;
            margin-top: 6px;
        }

        /* Permissions Section */
        .permissions-section {
            background: #f9fafb;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .permissions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }

        .permission-group {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            transition: all 0.3s ease;
        }

        .permission-group:hover {
            border-color: #6366f1;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.1);
        }

        .permission-checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .permission-checkbox input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #6366f1;
        }

        .permission-label {
            cursor: pointer;
            font-weight: 500;
            color: #1f2937;
            flex: 1;
        }

        .permission-group input[type="checkbox"]:checked+label {
            color: #6366f1;
            font-weight: 600;
        }

        /* Action Buttons */
        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
        }

        .btn {
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
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4);
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #1f2937;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary:hover {
            background: #d1d5db;
            transform: translateY(-2px);
        }

        /* Errors */
        .error-message {
            color: #dc2626;
            font-size: 12px;
            margin-top: 4px;
            display: block;
        }

        .form-input.error {
            border-color: #dc2626;
        }

        .error-section {
            background: #fee2e2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 20px;
            color: #991b1b;
            font-size: 14px;
        }

        /* Permission Categories */
        .permission-category {
            margin-bottom: 30px;
        }

        .category-title {
            font-size: 14px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 12px;
            margin-top: 20px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e5e7eb;
        }

        @media (max-width: 768px) {
            .form-card {
                padding: 20px;
            }

            .permissions-grid {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <div class="create-container">
        {{-- Header --}}
        <div class="form-card">
            <div class="form-header">
                <h1 class="form-title">
                    ➕ Create New Role
                </h1>
                <a href="{{ route('admin.roles.index') }}" style="text-decoration: none; color: #6b7280;">
                    <i class="fas fa-times" style="font-size: 20px;"></i>
                </a>
            </div>
        </div>

        {{-- Errors --}}
        @if ($errors->any())
            <div class="error-section">
                <strong>⚠️ Please fix the following errors:</strong>
                <ul style="margin: 8px 0 0 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('admin.roles.store') }}" method="POST" class="form-card">
            @csrf

            {{-- Role Name --}}
            <div class="form-group">
                <label class="form-label" for="name">Role Name *</label>
                <input type="text" id="name" name="name" class="form-input @error('name') error @enderror"
                    value="{{ old('name') }}" placeholder="e.g., Manager, Support Agent" required>
                <p class="form-help">Enter a unique name for this role (e.g., Staff, HR, Admin, Delivery Agent)</p>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            {{-- Role Slug --}}
            <div class="form-group">
                <label class="form-label" for="slug">Role Slug *</label>
                <input type="text" id="slug" name="slug" class="form-input @error('slug') error @enderror"
                    value="{{ old('slug') }}" placeholder="e.g., manager, support_agent" required>
                <p class="form-help">A URL-friendly version of the role name (lowercase, no spaces, use underscores)</p>
                @error('slug')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            {{-- Description --}}
            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" name="description" class="form-textarea @error('description') error @enderror"
                    placeholder="Enter a brief description of this role and its responsibilities...">{{ old('description') }}</textarea>
                @error('description')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            {{-- Permissions --}}
            <div class="permissions-section">
                <h2 class="section-title">
                    📋 Assign Permissions
                </h2>

                <p style="color: #6b7280; font-size: 14px; margin-bottom: 20px;">
                    Select the permissions you want to assign to this role. Users with this role will have access to the
                    selected features.
                </p>

                <div class="permissions-grid">
                    @php
                        $categories = [
                            'Dashboard' => ['view_dashboard'],
                            'Sales' => ['view_sales', 'create_sales', 'edit_sales', 'delete_sales', 'export_sales', 'view_invoices'],
                            'Purchases' => ['view_purchases', 'create_purchases', 'edit_purchases', 'delete_purchases'],
                            'Inventory' => ['view_inventory', 'create_inventory', 'edit_inventory', 'delete_inventory', 'view_stock_alerts', 'generate_barcodes'],
                            'Employees' => ['view_employees', 'create_employees', 'edit_employees', 'delete_employees', 'view_employee_details'],
                            'Customers' => ['view_customers', 'create_customers', 'edit_customers', 'delete_customers', 'manage_customer_wallet'],
                            'Attendance' => ['view_attendance', 'mark_attendance', 'edit_attendance', 'view_attendance_reports', 'export_attendance'],
                            'Leaves' => ['view_leaves', 'apply_leaves', 'edit_leaves', 'manage_leave_policies'],
                            'Logistics' => ['view_shipments', 'create_shipments', 'edit_shipments', 'delete_shipments', 'track_shipments', 'manage_logistics_agents', 'manage_service_areas', 'view_live_tracking'],
                            'Reports' => ['view_reports', 'view_sales_reports', 'view_customers_reports', 'view_inventory_reports', 'view_logistics_reports', 'view_employee_reports', 'view_purchase_reports', 'view_attendance_reports'],
                            'EMI & Payments' => ['view_emi', 'manage_emi', 'view_payments', 'manage_payments'],
                            'Administration' => ['manage_users', 'manage_roles', 'manage_settings', 'view_approvals', 'manage_approvals'],
                        ];
                    @endphp

                    @foreach ($categories as $category => $permissions)
                        <div class="permission-category" style="grid-column: 1 / -1;">
                            <div class="category-title">{{ $category }}</div>
                            <div
                                style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
                                @foreach ($permissions as $permission)
                                    @if (isset($availablePermissions[$permission]))
                                        <div class="permission-group">
                                            <label class="permission-checkbox">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission }}"
                                                    {{ in_array($permission, old('permissions', [])) ? 'checked' : '' }}>
                                                <span class="permission-label">
                                                    {{ $availablePermissions[$permission] }}
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Actions --}}
            <div class="form-actions">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Role
                </button>
            </div>
        </form>
    </div>

@endsection
