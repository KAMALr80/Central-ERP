@extends('layouts.app')

@section('page-title', 'Edit Employee - ' . ($employee->name ?? ''))

@section('content')
<style>
    /* ================= PROFESSIONAL DESIGN SYSTEM ================= */
    :root {
        --primary: #f59e0b;
        --primary-dark: #d97706;
        --success: #10b981;
        --success-dark: #059669;
        --danger: #ef4444;
        --danger-dark: #dc2626;
        --warning: #f59e0b;
        --info: #3b82f6;
        --info-dark: #1d4ed8;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --border: #e5e7eb;
        --bg-light: #f8fafc;
        --bg-white: #ffffff;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --shadow-xl: 0 20px 60px rgba(0, 0, 0, 0.08);
        --radius-sm: 6px;
        --radius-md: 8px;
        --radius-lg: 12px;
        --radius-xl: 16px;
        --radius-2xl: 24px;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        color: var(--text-main);
        line-height: 1.5;
    }

    /* ================= MAIN CONTAINER ================= */
    .form-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 24px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-container {
        max-width: 1100px;
        width: 100%;
        margin: 0 auto;
    }

    /* ================= UNAUTHORIZED ACCESS ================= */
    .unauthorized-container {
        max-width: 500px;
        margin: 0 auto;
        width: 100%;
    }

    .unauthorized-card {
        background: var(--bg-white);
        border-radius: var(--radius-2xl);
        padding: clamp(40px, 8vw, 60px) clamp(24px, 5vw, 40px);
        text-align: center;
        width: 100%;
        box-shadow: var(--shadow-xl);
        border: 1px solid var(--border);
    }

    .unauthorized-icon {
        font-size: clamp(60px, 15vw, 80px);
        margin-bottom: 24px;
        opacity: 0.8;
    }

    .unauthorized-title {
        font-size: clamp(24px, 5vw, 28px);
        font-weight: 800;
        color: var(--danger);
        margin: 0 0 16px 0;
        word-break: break-word;
    }

    .unauthorized-message {
        color: var(--text-muted);
        font-size: clamp(14px, 3vw, 16px);
        margin: 0 0 32px 0;
        line-height: 1.6;
        word-break: break-word;
    }

    .btn-back {
        background: linear-gradient(135deg, var(--info) 0%, var(--info-dark) 100%);
        color: white;
        padding: clamp(12px, 3vw, 14px) clamp(20px, 4vw, 28px);
        border-radius: var(--radius-lg);
        text-decoration: none;
        font-weight: 600;
        font-size: clamp(14px, 2.5vw, 15px);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.25);
        white-space: nowrap;
    }

    .btn-back:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.35);
    }

    /* ================= FORM CARD ================= */
    .form-card {
        background: var(--bg-white);
        border-radius: var(--radius-2xl);
        padding: clamp(24px, 5vw, 40px);
        width: 100%;
        box-shadow: var(--shadow-xl);
        border: 1px solid var(--border);
    }

    /* ================= FORM HEADER ================= */
    .form-header {
        display: flex;
        align-items: center;
        gap: clamp(16px, 4vw, 24px);
        margin-bottom: 32px;
        flex-wrap: wrap;
    }

    .form-icon {
        font-size: clamp(32px, 8vw, 48px);
        width: clamp(60px, 12vw, 80px);
        height: clamp(60px, 12vw, 80px);
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: var(--radius-xl);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3);
        flex-shrink: 0;
    }

    .form-header-text {
        flex: 1;
        min-width: 250px;
    }

    .form-title {
        font-size: clamp(24px, 5vw, 32px);
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
        letter-spacing: -0.5px;
        word-break: break-word;
    }

    .form-subtitle {
        color: var(--text-muted);
        font-size: clamp(14px, 3vw, 16px);
        margin: 8px 0 0 0;
        word-break: break-word;
    }

    /* ================= EMPLOYEE INFO ================= */
    .employee-info {
        background: linear-gradient(135deg, var(--bg-light) 0%, #f1f5f9 100%);
        border-radius: var(--radius-lg);
        padding: clamp(20px, 4vw, 24px);
        margin-bottom: 32px;
        display: flex;
        align-items: center;
        gap: clamp(16px, 4vw, 20px);
        border: 2px solid var(--border);
        flex-wrap: wrap;
    }

    .employee-avatar {
        width: clamp(60px, 12vw, 80px);
        height: clamp(60px, 12vw, 80px);
        background: linear-gradient(135deg, var(--info) 0%, var(--info-dark) 100%);
        border-radius: var(--radius-xl);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: clamp(24px, 5vw, 32px);
        font-weight: 700;
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.2);
        flex-shrink: 0;
    }

    .employee-details {
        flex: 1;
        min-width: 200px;
    }

    .employee-code {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: clamp(12px, 2.5vw, 13px);
        font-weight: 700;
        display: inline-block;
        margin-bottom: 8px;
        border: 1px solid #fbbf24;
        word-break: break-word;
    }

    .employee-name {
        font-size: clamp(18px, 4vw, 20px);
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 4px;
        word-break: break-word;
    }

    .employee-email {
        color: var(--text-muted);
        font-size: clamp(12px, 2.5vw, 14px);
        word-break: break-word;
    }

    /* ================= SUCCESS MESSAGE ================= */
    .success-message {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        border: 2px solid var(--success);
        color: #065f46;
        padding: clamp(14px, 3vw, 16px) clamp(20px, 4vw, 24px);
        border-radius: var(--radius-lg);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
        word-break: break-word;
        flex-wrap: wrap;
    }

    .success-icon {
        font-size: clamp(18px, 4vw, 20px);
        color: var(--success);
        flex-shrink: 0;
    }

    /* ================= ERROR ALERT ================= */
    .error-alert {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border: 2px solid var(--danger);
        border-radius: var(--radius-lg);
        padding: clamp(16px, 4vw, 20px);
        margin-bottom: 32px;
        display: flex;
        gap: clamp(12px, 3vw, 16px);
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .error-icon {
        font-size: clamp(20px, 5vw, 24px);
        color: var(--danger-dark);
        flex-shrink: 0;
    }

    .error-content {
        flex: 1;
        min-width: 200px;
    }

    .error-title {
        font-size: clamp(14px, 3vw, 16px);
        font-weight: 700;
        color: #991b1b;
        margin: 0 0 8px 0;
        word-break: break-word;
    }

    .error-list {
        margin: 0;
        padding-left: 20px;
        color: #7f1d1d;
    }

    .error-list li {
        margin-bottom: 4px;
        font-size: clamp(12px, 2.5vw, 14px);
        word-break: break-word;
    }

    /* ================= FORM GRID ================= */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 40px;
        width: 100%;
    }

    /* ================= FORM GROUPS ================= */
    .form-group {
        margin-bottom: 0;
        width: 100%;
    }

    .form-label {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 4px 8px;
        margin-bottom: 12px;
        font-size: clamp(12px, 2.5vw, 14px);
        font-weight: 600;
        color: #374151;
    }

    .label-icon {
        font-size: clamp(14px, 3vw, 16px);
        opacity: 0.8;
    }

    .required {
        color: var(--danger);
        margin-left: 4px;
    }

    .optional {
        color: var(--text-muted);
        font-size: clamp(11px, 2vw, 12px);
        font-weight: normal;
        margin-left: 6px;
    }

    /* ================= FORM INPUTS ================= */
    .form-input {
        width: 100%;
        padding: clamp(14px, 3vw, 16px) clamp(16px, 4vw, 20px);
        border: 2px solid var(--border);
        border-radius: var(--radius-lg);
        font-size: clamp(14px, 3vw, 15px);
        color: var(--text-main);
        background: white;
        transition: all 0.3s ease;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
    }

    .form-input[readonly] {
        background: var(--bg-light);
        color: var(--text-muted);
        cursor: not-allowed;
        border-color: var(--border);
    }

    .form-input::placeholder {
        color: #9ca3af;
    }

    /* ================= PASSWORD FIELD ================= */
    .password-wrapper {
        position: relative;
    }

    .password-input {
        padding-right: 60px;
    }

    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--text-muted);
        font-size: clamp(16px, 4vw, 18px);
        cursor: pointer;
        padding: 4px;
        border-radius: var(--radius-sm);
        transition: all 0.3s ease;
        line-height: 1;
    }

    .password-toggle:hover {
        background: var(--bg-light);
        color: #4b5563;
    }

    .password-hint {
        font-size: clamp(11px, 2vw, 12px);
        color: var(--text-muted);
        margin-top: 8px;
        font-style: italic;
        word-break: break-word;
    }

    /* ================= SELECT WRAPPER ================= */
    .select-wrapper {
        position: relative;
    }

    .form-select {
        width: 100%;
        padding: clamp(14px, 3vw, 16px) clamp(16px, 4vw, 20px);
        border: 2px solid var(--border);
        border-radius: var(--radius-lg);
        font-size: clamp(14px, 3vw, 15px);
        color: var(--text-main);
        background: white;
        appearance: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .form-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
    }

    .select-arrow {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        pointer-events: none;
        font-size: 12px;
    }

    /* ================= FIELD HINTS ================= */
    .field-hint {
        font-size: clamp(11px, 2vw, 12px);
        color: var(--text-muted);
        margin-top: 8px;
        font-style: italic;
        word-break: break-word;
    }

    /* ================= FORM ACTIONS ================= */
    .form-actions {
        display: flex;
        gap: 16px;
        padding-top: 32px;
        border-top: 1px solid var(--border);
        flex-wrap: wrap;
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        border: none;
        padding: clamp(16px, 4vw, 18px) clamp(24px, 5vw, 32px);
        border-radius: var(--radius-lg);
        font-size: clamp(14px, 3vw, 16px);
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(245, 158, 11, 0.3);
        flex: 1;
        min-width: 200px;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(245, 158, 11, 0.4);
    }

    .btn-cancel {
        background: white;
        color: #374151;
        border: 2px solid var(--border);
        padding: clamp(16px, 4vw, 18px) clamp(24px, 5vw, 32px);
        border-radius: var(--radius-lg);
        font-size: clamp(14px, 3vw, 16px);
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        text-decoration: none;
        transition: all 0.3s ease;
        flex: 1;
        min-width: 200px;
    }

    .btn-cancel:hover {
        background: var(--bg-light);
        border-color: #d1d5db;
        transform: translateY(-2px);
    }

    .btn-icon {
        font-size: clamp(16px, 4vw, 18px);
    }

    /* ================= RESPONSIVE BREAKPOINTS ================= */
    
    /* Large Desktop (1200px and above) */
    @media (min-width: 1200px) {
        .form-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Desktop (992px to 1199px) */
    @media (max-width: 1199px) {
        .form-page {
            padding: 20px;
        }
    }

    /* Tablet (768px to 991px) */
    @media (max-width: 991px) {
        .form-page {
            padding: 16px;
        }

        .form-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-actions {
            flex-direction: row;
        }
    }

    /* Mobile Landscape (576px to 767px) */
    @media (max-width: 767px) {
        .form-page {
            padding: 12px;
        }

        .form-header {
            flex-direction: column;
            text-align: center;
            gap: 16px;
        }

        .form-icon {
            margin: 0 auto;
        }

        .form-header-text {
            text-align: center;
        }

        .employee-info {
            flex-direction: column;
            text-align: center;
            gap: 16px;
        }

        .employee-avatar {
            margin: 0 auto;
        }

        .employee-details {
            text-align: center;
        }

        .form-grid {
            grid-template-columns: 1fr;
            gap: 18px;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn-submit,
        .btn-cancel {
            width: 100%;
            min-width: 100%;
        }

        .error-alert {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .error-list {
            text-align: left;
        }

        .success-message {
            flex-direction: column;
            text-align: center;
        }
    }

    /* Mobile Portrait (up to 575px) */
    @media (max-width: 575px) {
        .form-page {
            padding: 8px;
        }

        .form-card {
            padding: 20px;
        }

        .form-title {
            font-size: 22px;
        }

        .form-subtitle {
            font-size: 13px;
        }

        .form-label {
            font-size: 12px;
        }

        .form-input,
        .form-select {
            padding: 12px 14px;
            font-size: 13px;
        }

        .password-hint,
        .field-hint {
            font-size: 10px;
        }

        .employee-name {
            font-size: 18px;
        }

        .employee-email {
            font-size: 12px;
        }

        .error-title {
            font-size: 13px;
        }

        .error-list li {
            font-size: 11px;
        }
    }

    /* Extra Small Devices (up to 360px) */
    @media (max-width: 360px) {
        .form-card {
            padding: 16px;
        }

        .form-title {
            font-size: 20px;
        }

        .form-icon {
            width: 50px;
            height: 50px;
            font-size: 24px;
        }

        .employee-avatar {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }

        .form-input,
        .form-select {
            padding: 10px 12px;
            font-size: 12px;
        }

        .password-toggle {
            font-size: 14px;
        }

        .btn-submit,
        .btn-cancel {
            padding: 14px 20px;
            font-size: 13px;
        }

        .unauthorized-card {
            padding: 30px 16px;
        }

        .unauthorized-icon {
            font-size: 50px;
        }

        .unauthorized-title {
            font-size: 20px;
        }
    }

    /* Print Styles */
    @media print {
        .form-page {
            padding: 0;
            background: white;
        }

        .form-card {
            box-shadow: none;
            border: 1px solid #000;
        }

        .btn-submit,
        .btn-cancel,
        .password-toggle,
        .btn-back {
            display: none !important;
        }

        .form-icon,
        .employee-avatar {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .employee-code,
        .error-alert,
        .success-message {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<div class="form-page">
    <div class="form-container">
        {{-- Permission Check --}}
        @if (!auth()->user()->hasPermission('edit_employees'))
            <div class="unauthorized-container">
                <div class="unauthorized-card">
                    <div class="unauthorized-icon">🚫</div>
                    <h2 class="unauthorized-title">Access Restricted</h2>
                    <p class="unauthorized-message">Only administrators and HR managers can edit employee details.</p>
                    <a href="{{ route('employees.index') }}" class="btn-back">
                        ← Back to Employees
                    </a>
                </div>
            </div>
        @else
            <div class="form-card">
                <div class="form-header">
                    <div class="form-icon">✏️</div>
                    <div class="form-header-text">
                        <h1 class="form-title">Edit Employee</h1>
                        <p class="form-subtitle">Update details for {{ $employee->name }}</p>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="error-alert">
                        <span class="error-icon">⚠️</span>
                        <div class="error-content">
                            <h4 class="error-title">Please fix the following errors:</h4>
                            <ul class="error-list">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="success-message">
                        <span class="success-icon">✓</span>
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('employees.update', $employee->id) }}" class="employee-form">
                    @csrf
                    @method('PUT')

                    <div class="employee-info">
                        <div class="employee-avatar">
                            {{ strtoupper(substr($employee->name, 0, 1)) }}
                        </div>
                        <div class="employee-details">
                            <div class="employee-code">{{ $employee->employee_code }}</div>
                            <div class="employee-name">{{ $employee->name }}</div>
                            <div class="employee-email">{{ $employee->email }}</div>
                        </div>
                    </div>

                    <div class="form-grid">
                        {{-- Name Field --}}
                        <div class="form-group">
                            <label class="form-label">
                                <span class="label-icon">👤</span>
                                Full Name
                                <span class="required">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $employee->name) }}" required
                                class="form-input" placeholder="Enter employee name">
                        </div>

                        {{-- Email Field --}}
                        <div class="form-group">
                            <label class="form-label">
                                <span class="label-icon">📧</span>
                                Email Address
                                <span class="required">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email', $employee->email) }}" required
                                class="form-input" placeholder="employee@company.com">
                        </div>

                        {{-- Password Field --}}
                        <div class="form-group">
                            <label class="form-label">
                                <span class="label-icon">🔒</span>
                                Password
                                <span class="optional">(Optional)</span>
                            </label>
                            <div class="password-wrapper">
                                <input type="password" name="password" class="form-input password-input"
                                    placeholder="Leave blank to keep current" id="password">
                                <button type="button" class="password-toggle" onclick="togglePassword()">
                                    👁️
                                </button>
                            </div>
                            <div class="password-hint">
                                Only enter if you want to change the password
                            </div>
                        </div>

                        {{-- Phone Field --}}
                        <div class="form-group">
                            <label class="form-label">
                                <span class="label-icon">📱</span>
                                Phone Number
                            </label>
                            <input type="tel" name="phone" value="{{ old('phone', $employee->phone) }}"
                                class="form-input" placeholder="+1 234 567 8900">
                        </div>

                        {{-- Department Field --}}
                        <div class="form-group">
                            <label class="form-label">
                                <span class="label-icon">🏢</span>
                                Department
                            </label>
                            <div class="select-wrapper">
                                <select name="department" class="form-select">
                                    <option value="">Select Department</option>
                                    <option value="Human Resources"
                                        {{ old('department', $employee->department) == 'Human Resources' ? 'selected' : '' }}>
                                        Human Resources</option>
                                    <option value="Engineering"
                                        {{ old('department', $employee->department) == 'Engineering' ? 'selected' : '' }}>
                                        Engineering</option>
                                    <option value="Sales"
                                        {{ old('department', $employee->department) == 'Sales' ? 'selected' : '' }}>Sales
                                    </option>
                                    <option value="Marketing"
                                        {{ old('department', $employee->department) == 'Marketing' ? 'selected' : '' }}>
                                        Marketing</option>
                                    <option value="Finance"
                                        {{ old('department', $employee->department) == 'Finance' ? 'selected' : '' }}>
                                        Finance</option>
                                    <option value="Operations"
                                        {{ old('department', $employee->department) == 'Operations' ? 'selected' : '' }}>
                                        Operations</option>
                                    <option value="Customer Support"
                                        {{ old('department', $employee->department) == 'Customer Support' ? 'selected' : '' }}>
                                        Customer Support</option>
                                    <option value="Other"
                                        {{ old('department', $employee->department) == 'Other' ? 'selected' : '' }}>Other
                                    </option>
                                </select>
                                <span class="select-arrow">▼</span>
                            </div>
                        </div>

                        {{-- Joining Date Field --}}
                        <div class="form-group">
                            <label class="form-label">
                                <span class="label-icon">📅</span>
                                Joining Date
                            </label>
                            <input type="date" name="joining_date"
                                value="{{ old('joining_date', $employee->joining_date) }}" class="form-input">
                        </div>

                        {{-- Employee Code Field --}}
                        <div class="form-group">
                            <label class="form-label">
                                <span class="label-icon">#️⃣</span>
                                Employee Code
                            </label>
                            <input type="text" name="employee_code"
                                value="{{ old('employee_code', $employee->employee_code) }}" class="form-input" readonly>
                            <div class="field-hint">Employee code cannot be changed</div>
                        </div>

                        {{-- Role Field --}}
                        @if (auth()->user()->hasPermission('manage_roles'))
                            <div class="form-group">
                                <label class="form-label">
                                    <span class="label-icon">🎭</span>
                                    Role
                                </label>
                                <div class="select-wrapper">
                                    <select name="role" class="form-select">
                                        <option value="staff"
                                            {{ old('role', $employee->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                                        <option value="hr"
                                            {{ old('role', $employee->role) == 'hr' ? 'selected' : '' }}>HR Manager
                                        </option>
                                        <option value="admin"
                                            {{ old('role', $employee->role) == 'admin' ? 'selected' : '' }}>Administrator
                                        </option>
                                    </select>
                                    <span class="select-arrow">▼</span>
                                </div>
                                <div class="field-hint">Admin only: Change user role</div>
                            </div>
                        @endif

                        {{-- Status Field --}}
                        @if (auth()->user()->hasPermission('edit_employees'))
                            <div class="form-group">
                                <label class="form-label">
                                    <span class="label-icon">📊</span>
                                    Status
                                </label>
                                <div class="select-wrapper">
                                    <select name="status" class="form-select">
                                        <option value="active"
                                            {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="inactive"
                                            {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>Inactive
                                        </option>
                                        <option value="on_leave"
                                            {{ old('status', $employee->status) == 'on_leave' ? 'selected' : '' }}>On Leave
                                        </option>
                                    </select>
                                    <span class="select-arrow">▼</span>
                                </div>
                                <div class="field-hint">Admin only: Change employment status</div>
                            </div>
                        @endif
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <span class="btn-icon">💾</span>
                            Update Employee
                        </button>
                        <a href="{{ route('employees.index') }}" class="btn-cancel">
                            <span class="btn-icon">❌</span>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleButton = document.querySelector('.password-toggle');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleButton.textContent = '🙈';
        } else {
            passwordInput.type = 'password';
            toggleButton.textContent = '👁️';
        }
    }

    // Responsive handling for window resize
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            // Any responsive adjustments if needed
        }, 250);
    });
</script>
@endsection