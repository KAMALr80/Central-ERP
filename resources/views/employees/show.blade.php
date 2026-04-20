@extends('layouts.app')

@section('page-title', 'Employee Details - ' . $employee->name)

@section('content')
<style>
    /* ================= PROFESSIONAL DESIGN SYSTEM ================= */
    :root {
        --primary: #667eea;
        --primary-dark: #5a67d8;
        --secondary: #764ba2;
        --success: #10b981;
        --success-dark: #059669;
        --danger: #ef4444;
        --danger-dark: #dc2626;
        --warning: #f59e0b;
        --info: #3b82f6;
        --info-dark: #1d4ed8;
        --purple: #8b5cf6;
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
        --radius-2xl: 20px;
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
    .employee-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: clamp(16px, 3vw, 24px);
        width: 100%;
    }

    .container {
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
    }

    /* ================= HEADER ================= */
    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
        padding: 0 12px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .header-left {
        flex: 1;
        min-width: 280px;
    }

    .page-title {
        font-size: clamp(24px, 5vw, 28px);
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 8px;
        letter-spacing: -0.5px;
        word-break: break-word;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        font-size: clamp(12px, 2.5vw, 14px);
        flex-wrap: wrap;
    }

    .breadcrumb a {
        color: var(--text-muted);
        text-decoration: none;
        transition: color 0.2s;
    }

    .breadcrumb a:hover {
        color: var(--info);
    }

    .breadcrumb-separator {
        color: #cbd5e1;
    }

    .breadcrumb-current {
        color: #475569;
        font-weight: 500;
    }

    .header-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: white;
        color: #4b5563;
        border: 1px solid var(--border);
        padding: clamp(8px, 2vw, 10px) clamp(16px, 3vw, 20px);
        border-radius: var(--radius-md);
        text-decoration: none;
        font-weight: 500;
        font-size: clamp(12px, 2.5vw, 14px);
        transition: all 0.3s;
        white-space: nowrap;
    }

    .btn-back:hover {
        background: var(--bg-light);
        border-color: #d1d5db;
        transform: translateY(-1px);
    }

    .btn-edit {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, var(--info) 0%, var(--info-dark) 100%);
        color: white;
        border: none;
        padding: clamp(8px, 2vw, 10px) clamp(16px, 3vw, 24px);
        border-radius: var(--radius-md);
        text-decoration: none;
        font-weight: 600;
        font-size: clamp(12px, 2.5vw, 14px);
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        transition: all 0.3s;
        white-space: nowrap;
    }

    .btn-edit:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
    }

    /* ================= MAIN CARD ================= */
    .main-card {
        background: var(--bg-white);
        border-radius: var(--radius-2xl);
        box-shadow: var(--shadow-xl);
        border: 1px solid var(--border);
        overflow: hidden;
        width: 100%;
    }

    /* ================= PROFILE HEADER ================= */
    .profile-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        padding: clamp(24px, 5vw, 40px);
        position: relative;
        width: 100%;
    }

    .profile-content {
        display: flex;
        align-items: center;
        gap: clamp(16px, 4vw, 24px);
        flex-wrap: wrap;
    }

    .profile-avatar {
        width: clamp(70px, 15vw, 100px);
        height: clamp(70px, 15vw, 100px);
        border-radius: 50%;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: clamp(24px, 5vw, 36px);
        color: var(--secondary);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        flex-shrink: 0;
    }

    .profile-info {
        flex: 1;
        min-width: 280px;
    }

    .profile-name-section {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }

    .profile-name {
        font-size: clamp(24px, 5vw, 32px);
        font-weight: 800;
        color: white;
        margin: 0;
        word-break: break-word;
    }

    .profile-badge {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        color: white;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: clamp(12px, 2.5vw, 14px);
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.3);
        white-space: nowrap;
    }

    .profile-status {
        background: {{ $employee->status == 1 ? 'rgba(34, 197, 94, 0.2)' : 'rgba(107, 114, 128, 0.2)' }};
        backdrop-filter: blur(10px);
        color: {{ $employee->status == 1 ? '#22c55e' : '#9ca3af' }};
        padding: 6px 16px;
        border-radius: 20px;
        font-size: clamp(12px, 2.5vw, 14px);
        font-weight: 600;
        border: 1px solid {{ $employee->status == 1 ? 'rgba(34, 197, 94, 0.3)' : 'rgba(156, 163, 175, 0.3)' }};
        white-space: nowrap;
    }

    .profile-contact {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .contact-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: rgba(255, 255, 255, 0.9);
    }

    .contact-icon {
        font-size: clamp(16px, 3vw, 18px);
    }

    .contact-text {
        color: white;
        text-decoration: none;
        font-size: clamp(14px, 2.5vw, 16px);
        font-weight: 500;
        word-break: break-word;
    }

    /* ================= INFORMATION GRID ================= */
    .info-section {
        padding: clamp(24px, 5vw, 40px);
    }

    .section-title {
        font-size: clamp(18px, 3.5vw, 20px);
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
        margin-bottom: 40px;
    }

    .info-card {
        background: var(--bg-light);
        border-radius: var(--radius-lg);
        padding: 24px;
        border: 1px solid var(--border);
        transition: all 0.3s ease;
    }

    .info-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary);
    }

    .info-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--border);
    }

    .info-icon {
        font-size: 24px;
    }

    .info-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-main);
    }

    .info-row {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px dashed var(--border);
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        min-width: 100px;
        font-weight: 600;
        color: var(--text-muted);
        font-size: 14px;
    }

    .info-value {
        flex: 1;
        color: var(--text-main);
        font-weight: 500;
        font-size: 15px;
        word-break: break-word;
    }

    /* ================= QUICK ACTIONS SECTION ================= */
    .quick-actions-section {
        padding: clamp(24px, 5vw, 40px);
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: var(--radius-lg);
        border: 2px solid #e2e8f0;
    }

    .quick-actions-title {
        font-size: clamp(16px, 3vw, 18px);
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .quick-actions-grid {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }

    /* Email Form */
    .email-form {
        flex: 2;
        min-width: 300px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        font-size: clamp(13px, 2.5vw, 15px);
        display: block;
    }

    .form-select {
        padding: 10px 16px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-md);
        font-size: clamp(13px, 2.5vw, 14px);
        color: #374151;
        background: white;
        cursor: pointer;
        appearance: none;
        width: 100%;
        transition: all 0.3s;
    }

    .form-select:focus {
        outline: none;
        border-color: var(--info);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .btn-email {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: linear-gradient(135deg, var(--info) 0%, var(--info-dark) 100%);
        color: white;
        border: none;
        padding: clamp(14px, 3vw, 16px) clamp(20px, 4vw, 24px);
        border-radius: var(--radius-lg);
        text-decoration: none;
        font-weight: 600;
        font-size: clamp(14px, 2.5vw, 15px);
        transition: all 0.3s;
        width: 100%;
        cursor: pointer;
        text-align: left;
    }

    .btn-email:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
    }

    .btn-email-icon {
        font-size: clamp(18px, 3vw, 20px);
    }

    .btn-email-content {
        flex: 1;
    }

    .btn-email-title {
        font-weight: 700;
        font-size: clamp(14px, 2.5vw, 15px);
    }

    .btn-email-desc {
        font-size: clamp(11px, 2vw, 13px);
        color: rgba(255, 255, 255, 0.9);
        margin-top: 2px;
    }

    /* Action Buttons */
    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: white;
        border: 2px solid;
        padding: clamp(14px, 3vw, 16px) clamp(20px, 4vw, 24px);
        border-radius: var(--radius-lg);
        text-decoration: none;
        font-weight: 600;
        font-size: clamp(14px, 2.5vw, 15px);
        transition: all 0.3s;
        flex: 1;
        min-width: 200px;
        cursor: pointer;
        text-align: left;
    }

    .action-btn.call {
        color: var(--success);
        border-color: #d1fae5;
    }

    .action-btn.call:hover {
        background: #d1fae5;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.15);
    }

    .action-btn.delete {
        color: var(--danger);
        border-color: #fee2e2;
    }

    .action-btn.delete:hover {
        background: #fee2e2;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.15);
    }

    .action-icon {
        font-size: clamp(18px, 3vw, 20px);
    }

    .action-content {
        flex: 1;
    }

    .action-title {
        font-weight: 700;
    }

    .action-desc {
        font-size: clamp(11px, 2vw, 13px);
        color: var(--text-muted);
        margin-top: 2px;
    }

    /* Loading Indicator */
    .loading-indicator {
        display: none;
        text-align: center;
        margin-top: 12px;
    }

    .loading-content {
        color: var(--info);
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    /* Message Container */
    .message-container {
        margin-top: 12px;
    }

    .message-success {
        background: #d1fae5;
        color: #065f46;
        padding: 12px 16px;
        border-radius: var(--radius-md);
        border: 1px solid #a7f3d0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .message-error {
        background: #fee2e2;
        color: #991b1b;
        padding: 12px 16px;
        border-radius: var(--radius-md);
        border: 1px solid #fecaca;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .message-icon {
        font-size: 18px;
    }

    .message-content {
        flex: 1;
    }

    .message-title {
        font-weight: 600;
        margin-bottom: 2px;
    }

    .message-desc {
        font-size: 12px;
        opacity: 0.8;
    }

    /* ================= MODAL ================= */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: var(--radius-2xl);
        padding: clamp(24px, 5vw, 32px);
        width: 90%;
        max-width: 600px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .modal-title {
        font-size: clamp(20px, 4vw, 22px);
        font-weight: 700;
        color: var(--text-main);
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: var(--text-muted);
        cursor: pointer;
        padding: 0;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s;
    }

    .modal-close:hover {
        background: var(--bg-light);
        color: #475569;
    }

    .modal-body {
        margin-bottom: 24px;
    }

    .modal-input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-md);
        font-size: 15px;
        transition: all 0.3s;
        margin-bottom: 16px;
    }

    .modal-input:focus {
        outline: none;
        border-color: var(--info);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .modal-textarea {
        width: 100%;
        padding: 16px;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-lg);
        font-size: 15px;
        resize: vertical;
        font-family: inherit;
        line-height: 1.5;
        transition: all 0.3s;
    }

    .modal-textarea:focus {
        outline: none;
        border-color: var(--info);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        flex-wrap: wrap;
    }

    .modal-btn {
        padding: 12px 24px;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
    }

    .modal-btn.cancel {
        background: var(--bg-light);
        color: #475569;
    }

    .modal-btn.cancel:hover {
        background: #e2e8f0;
    }

    .modal-btn.submit {
        background: linear-gradient(135deg, var(--info) 0%, var(--info-dark) 100%);
        color: white;
    }

    .modal-btn.submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
    }

    /* ================= RESPONSIVE BREAKPOINTS ================= */
    
    /* Large Desktop (1200px and above) */
    @media (min-width: 1200px) {
        .info-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* Desktop (992px to 1199px) */
    @media (max-width: 1199px) {
        .info-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Tablet (768px to 991px) */
    @media (max-width: 991px) {
        .employee-page {
            padding: 16px;
        }

        .header-section {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-actions {
            width: 100%;
        }

        .btn-back,
        .btn-edit {
            flex: 1;
            justify-content: center;
        }

        .profile-content {
            flex-direction: column;
            text-align: center;
        }

        .profile-name-section {
            justify-content: center;
        }

        .profile-contact {
            justify-content: center;
        }

        .info-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .quick-actions-grid {
            flex-direction: column;
        }

        .email-form {
            width: 100%;
        }

        .action-btn {
            width: 100%;
        }
    }

    /* Mobile Landscape (576px to 767px) */
    @media (max-width: 767px) {
        .employee-page {
            padding: 12px;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .info-card {
            padding: 20px;
        }

        .info-row {
            flex-direction: column;
            gap: 4px;
        }

        .info-label {
            min-width: auto;
        }

        .profile-name-section {
            flex-direction: column;
        }

        .profile-contact {
            flex-direction: column;
            align-items: flex-start;
        }

        .modal-content {
            padding: 20px;
        }

        .modal-footer {
            flex-direction: column;
        }

        .modal-btn {
            width: 100%;
        }
    }

    /* Mobile Portrait (up to 575px) */
    @media (max-width: 575px) {
        .employee-page {
            padding: 8px;
        }

        .page-title {
            font-size: 22px;
        }

        .breadcrumb {
            font-size: 11px;
        }

        .profile-avatar {
            width: 60px;
            height: 60px;
            font-size: 24px;
        }

        .profile-name {
            font-size: 22px;
        }

        .profile-badge,
        .profile-status {
            padding: 4px 12px;
            font-size: 11px;
        }

        .contact-text {
            font-size: 13px;
        }

        .info-card {
            padding: 16px;
        }

        .info-header {
            margin-bottom: 16px;
        }

        .info-title {
            font-size: 16px;
        }

        .info-value {
            font-size: 14px;
        }

        .quick-actions-section {
            padding: 20px;
        }

        .btn-email {
            padding: 14px 20px;
        }

        .action-btn {
            padding: 14px 20px;
        }
    }

    /* Extra Small Devices (up to 360px) */
    @media (max-width: 360px) {
        .page-title {
            font-size: 20px;
        }

        .profile-avatar {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }

        .profile-name {
            font-size: 20px;
        }

        .contact-icon {
            font-size: 14px;
        }

        .contact-text {
            font-size: 12px;
        }

        .info-card {
            padding: 12px;
        }

        .info-header {
            gap: 8px;
        }

        .info-icon {
            font-size: 20px;
        }

        .info-title {
            font-size: 15px;
        }

        .info-value {
            font-size: 13px;
        }

        .modal-content {
            padding: 16px;
        }

        .modal-title {
            font-size: 18px;
        }
    }

    /* Print Styles */
    @media print {
        .btn-back,
        .btn-edit,
        .quick-actions-section,
        .modal-overlay {
            display: none !important;
        }

        .profile-header {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .info-card {
            break-inside: avoid;
            border: 1px solid #000;
        }
    }
</style>

<div class="employee-page">
    <div class="container">
        <!-- Header -->
        <div class="header-section">
            <div class="header-left">
                <h1 class="page-title">Employee Details</h1>
                <div class="breadcrumb">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <span class="breadcrumb-separator">/</span>
                    <a href="{{ route('employees.index') }}">Employees</a>
                    <span class="breadcrumb-separator">/</span>
                    <span class="breadcrumb-current">Details</span>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('employees.index') }}" class="btn-back">
                    <span>←</span> Back
                </a>
                @if (auth()->user()->hasPermission('edit_employees'))
                    <a href="{{ route('employees.edit', $employee->id) }}" class="btn-edit">
                        <span>✏️</span> Edit
                    </a>
                @endif
            </div>
        </div>

        <!-- Main Card -->
        <div class="main-card">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-content">
                    <div class="profile-avatar">
                        {{ strtoupper(substr($employee->name, 0, 1)) }}
                    </div>
                    <div class="profile-info">
                        <div class="profile-name-section">
                            <h2 class="profile-name">{{ $employee->name }}</h2>
                            <span class="profile-badge">{{ $employee->employee_code }}</span>
                            <span class="profile-status">
                                ● {{ $employee->status == 1 ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="profile-contact">
                            <div class="contact-item">
                                <span class="contact-icon">📧</span>
                                <span class="contact-text">{{ $employee->email }}</span>
                            </div>
                            @if ($employee->phone)
                                <div class="contact-item">
                                    <span class="contact-icon">📱</span>
                                    <span class="contact-text">{{ $employee->phone }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Information Grid -->
            <div class="info-section">
                <h3 class="section-title">
                    <span>📋</span> Personal Information
                </h3>
                <div class="info-grid">
                    <!-- Personal Details Card -->
                    <div class="info-card">
                        <div class="info-header">
                            <span class="info-icon">👤</span>
                            <h4 class="info-title">Personal Details</h4>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Full Name</span>
                            <span class="info-value">{{ $employee->name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Employee Code</span>
                            <span class="info-value">{{ $employee->employee_code }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status</span>
                            <span class="info-value" style="color: {{ $employee->status == 1 ? '#10b981' : '#ef4444' }};">
                                {{ $employee->status == 1 ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    <!-- Contact Details Card -->
                    <div class="info-card">
                        <div class="info-header">
                            <span class="info-icon">📞</span>
                            <h4 class="info-title">Contact Information</h4>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email</span>
                            <span class="info-value">{{ $employee->email }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phone</span>
                            <span class="info-value">{{ $employee->phone ?? 'Not provided' }}</span>
                        </div>
                    </div>

                    <!-- Work Details Card -->
                    <div class="info-card">
                        <div class="info-header">
                            <span class="info-icon">🏢</span>
                            <h4 class="info-title">Work Information</h4>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Department</span>
                            <span class="info-value">{{ $employee->department ?? 'Not assigned' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Role</span>
                            <span class="info-value">{{ ucfirst($employee->role ?? 'staff') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Joining Date</span>
                            <span class="info-value">
                                {{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('d M, Y') : 'Not set' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Section -->
                <div class="quick-actions-section">
                    <h3 class="quick-actions-title">
                        <span>⚡</span> Quick Actions
                    </h3>
                    <div class="quick-actions-grid">
                        <!-- Send Email Form -->
                        <div class="email-form">
                            <form id="sendEmailForm" action="{{ route('employee.send.email', $employee->id) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <div class="form-label">Email Template:</div>
                                    <select id="emailTemplate" name="template" onchange="updateEmailForm()" class="form-select">
                                        <option value="general">General Inquiry</option>
                                        <option value="meeting">Meeting Request</option>
                                        <option value="welcome">Welcome Email</option>
                                        <option value="followup">Follow-up</option>
                                        <option value="status">Status Update</option>
                                        <option value="custom">Custom Message</option>
                                    </select>
                                </div>

                                <!-- Hidden fields for custom message -->
                                <input type="hidden" id="emailSubject" name="subject" value="">
                                <input type="hidden" id="emailBody" name="body" value="">

                                <button type="button" onclick="prepareAndSendEmail()" class="btn-email">
                                    <span class="btn-email-icon">📧</span>
                                    <div class="btn-email-content">
                                        <div class="btn-email-title" id="emailButtonText">Send General Email</div>
                                        <div class="btn-email-desc" id="emailButtonDesc">
                                            Send via Laravel Mail
                                        </div>
                                    </div>
                                </button>

                                <!-- Loading indicator -->
                                <div id="loadingIndicator" class="loading-indicator">
                                    <div class="loading-content">
                                        <span>⏳</span> Sending email...
                                    </div>
                                </div>

                                <!-- Success/Error messages -->
                                <div id="messageContainer" class="message-container"></div>
                            </form>
                        </div>

                        <!-- Other action buttons -->
                        @if ($employee->phone)
                            <a href="tel:{{ $employee->phone }}" class="action-btn call">
                                <span class="action-icon">📱</span>
                                <div class="action-content">
                                    <div class="action-title">Make a Call</div>
                                    <div class="action-desc">Call {{ $employee->phone }}</div>
                                </div>
                            </a>
                        @endif

                        @if (auth()->user()->hasPermission('delete_employees'))
                            <div style="flex: 1; min-width: 200px;">
                                <button onclick="confirmDelete()" class="action-btn delete">
                                    <span class="action-icon">🗑️</span>
                                    <div class="action-content">
                                        <div class="action-title">Delete Employee</div>
                                        <div class="action-desc">Remove permanently</div>
                                    </div>
                                </button>
                                <form id="deleteForm" action="{{ route('employees.destroy', $employee->id) }}"
                                    method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Message Modal -->
<div id="customMessageModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Write Custom Message</h3>
            <button onclick="closeCustomMessageModal()" class="modal-close">×</button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                <div class="form-label">Subject:</div>
                <input type="text" id="customSubject" class="modal-input"
                    value="Regarding Employee: {{ $employee->name }} ({{ $employee->employee_code }})">
            </div>

            <div class="form-group">
                <div class="form-label">Message:</div>
                <textarea id="customBody" rows="8" class="modal-textarea">Dear {{ $employee->name }},

I hope this email finds you well.

Regarding your employment details at our company, I wanted to discuss a few important matters with you.

Please let me know your availability for a brief meeting next week.

Best regards,
{{ auth()->user()->name ?? 'Your Name' }}
{{ auth()->user()->role ? '(' . ucfirst(auth()->user()->role) . ')' : '' }}</textarea>
            </div>
        </div>

        <div class="modal-footer">
            <button onclick="closeCustomMessageModal()" class="modal-btn cancel">
                Cancel
            </button>
            <button onclick="sendCustomEmail()" class="modal-btn submit">
                Send Email
            </button>
        </div>
    </div>
</div>

<script>
    // Email Templates
    const emailTemplates = {
        general: {
            name: "General Inquiry",
            description: "Send via Laravel Mail",
            subject: "Regarding Employee: {{ $employee->name }} ({{ $employee->employee_code }})",
            body: `Dear {{ $employee->name }},

I hope this email finds you well.

Regarding your employment details at our company, I wanted to discuss a few important matters with you.

Please let me know your availability for a brief meeting next week.

Best regards,
{{ auth()->user()->name ?? 'Your Name' }}
{{ auth()->user()->role ? '(' . ucfirst(auth()->user()->role) . ')' : '' }}`
        },
        meeting: {
            name: "Meeting Request",
            description: "Send via Laravel Mail",
            subject: "Meeting Request - {{ $employee->name }} ({{ $employee->employee_code }})",
            body: `Dear {{ $employee->name }},

I hope you are doing well.

I would like to schedule a meeting with you to discuss your recent work and future projects. Please let me know your availability for the coming week.

Looking forward to our discussion.

Best regards,
{{ auth()->user()->name ?? 'Your Name' }}
{{ auth()->user()->role ? '(' . ucfirst(auth()->user()->role) . ')' : '' }}`
        },
        welcome: {
            name: "Welcome Email",
            description: "Send via Laravel Mail",
            subject: "Welcome to the Team - {{ $employee->name }}",
            body: `Dear {{ $employee->name }},

Welcome to our team! We are excited to have you on board as part of the {{ $employee->department ?? 'our' }} team.

Your employee code is: {{ $employee->employee_code }}

If you have any questions or need assistance, please don't hesitate to reach out.

Warm regards,
{{ auth()->user()->name ?? 'Your Name' }}
{{ auth()->user()->role ? '(' . ucfirst(auth()->user()->role) . ')' : '' }}`
        },
        followup: {
            name: "Follow-up",
            description: "Send via Laravel Mail",
            subject: "Follow-up: {{ $employee->name }}",
            body: `Hi {{ $employee->name }},

Just following up on our previous conversation. Please provide an update when you get a chance.

Thank you.

Best,
{{ auth()->user()->name ?? 'Your Name' }}
{{ auth()->user()->role ? '(' . ucfirst(auth()->user()->role) . ')' : '' }}`
        },
        status: {
            name: "Status Update",
            description: "Send via Laravel Mail",
            subject: "Status Update Request - {{ $employee->name }}",
            body: `Hello {{ $employee->name }},

Could you please provide a status update on your current projects?

Please share your progress and any blockers you might be facing.

Thanks,
{{ auth()->user()->name ?? 'Your Name' }}
{{ auth()->user()->role ? '(' . ucfirst(auth()->user()->role) . ')' : '' }}`
        },
        custom: {
            name: "Custom Message",
            description: "Write your own message",
            subject: "",
            body: ""
        }
    };

    let selectedTemplate = 'general';

    function updateEmailForm() {
        const templateSelect = document.getElementById('emailTemplate');
        selectedTemplate = templateSelect.value;
        const template = emailTemplates[selectedTemplate];

        document.getElementById('emailButtonText').textContent = `Send ${template.name}`;
        document.getElementById('emailButtonDesc').textContent = template.description;

        // Update hidden fields for non-custom templates
        if (selectedTemplate !== 'custom') {
            document.getElementById('emailSubject').value = template.subject;
            document.getElementById('emailBody').value = template.body;
        }
    }

    function prepareAndSendEmail() {
        if (selectedTemplate === 'custom') {
            openCustomMessageModal();
        } else {
            sendEmail();
        }
    }

    function sendEmail() {
        const form = document.getElementById('sendEmailForm');
        const submitButton = form.querySelector('button[type="button"]');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const messageContainer = document.getElementById('messageContainer');

        // Show loading
        submitButton.disabled = true;
        submitButton.style.opacity = '0.7';
        loadingIndicator.style.display = 'block';
        messageContainer.innerHTML = '';

        // Submit form via AJAX
        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                subject: document.getElementById('emailSubject').value,
                body: document.getElementById('emailBody').value,
                template: selectedTemplate
            })
        })
        .then(response => response.json())
        .then(data => {
            // Hide loading
            submitButton.disabled = false;
            submitButton.style.opacity = '1';
            loadingIndicator.style.display = 'none';

            // Show message
            if (data.success) {
                messageContainer.innerHTML = `
                    <div class="message-success">
                        <span class="message-icon">✅</span>
                        <div class="message-content">
                            <div class="message-title">${data.message}</div>
                            <div class="message-desc">Email sent to {{ $employee->email }}</div>
                        </div>
                    </div>
                `;
            } else {
                messageContainer.innerHTML = `
                    <div class="message-error">
                        <span class="message-icon">❌</span>
                        <div class="message-content">
                            <div class="message-title">${data.message || 'Failed to send email'}</div>
                            <div class="message-desc">Please try again</div>
                        </div>
                    </div>
                `;
            }

            // Clear message after 5 seconds
            setTimeout(() => {
                messageContainer.innerHTML = '';
            }, 5000);
        })
        .catch(error => {
            submitButton.disabled = false;
            submitButton.style.opacity = '1';
            loadingIndicator.style.display = 'none';

            messageContainer.innerHTML = `
                <div class="message-error">
                    <span class="message-icon">❌</span>
                    <div class="message-content">
                        <div class="message-title">Network error occurred</div>
                        <div class="message-desc">Please check your connection</div>
                    </div>
                </div>
            `;
        });
    }

    function openCustomMessageModal() {
        document.getElementById('customMessageModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeCustomMessageModal() {
        document.getElementById('customMessageModal').classList.remove('active');
        document.body.style.overflow = '';
    }

    function sendCustomEmail() {
        const subject = document.getElementById('customSubject').value;
        const body = document.getElementById('customBody').value;

        // Update hidden fields
        document.getElementById('emailSubject').value = subject;
        document.getElementById('emailBody').value = body;

        closeCustomMessageModal();
        sendEmail();
    }

    function confirmDelete() {
        if (confirm('Are you sure you want to delete this employee? This action cannot be undone.')) {
            document.getElementById('deleteForm').submit();
        }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateEmailForm();

        // Close modal on outside click
        document.getElementById('customMessageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCustomMessageModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCustomMessageModal();
            }
        });
    });
</script>
@endsection