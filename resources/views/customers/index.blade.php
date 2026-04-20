@extends('layouts.app')

@section('page-title', 'Customer Management | ERP System')

@section('content')
    <style>
        /* ================= PROFESSIONAL ERP DESIGN SYSTEM ================= */
        :root {
            --primary: #1e40af;
            --primary-light: #3b82f6;
            --primary-dark: #1e3a8a;
            --secondary: #64748b;
            --accent: #0ea5e9;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #06b6d4;
            --dark: #0f172a;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --radius-sm: 0.25rem;
            --radius-md: 0.375rem;
            --radius-lg: 0.5rem;
            --radius-xl: 0.75rem;
            --shadow-xs: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            --font-sans: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--gray-100);
            font-family: var(--font-sans);
            color: var(--gray-800);
            line-height: 1.5;
        }

        /* ================= MAIN CONTAINER ================= */
        .erp-dashboard {
            max-width: 1600px;
            margin: 1.5rem auto;
            padding: 0 1.5rem;
            width: 100%;
        }

        /* ================= HEADER SECTION ================= */
        .erp-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 1.25rem 1.5rem;
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .brand-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .brand-title h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-800);
            margin: 0 0 0.125rem;
            letter-spacing: -0.01em;
        }

        .brand-title p {
            color: var(--gray-500);
            font-size: 0.8rem;
            margin: 0;
        }

        .header-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: var(--radius-md);
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            border: 1px solid transparent;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-secondary {
            background: white;
            color: var(--gray-700);
            border-color: var(--gray-300);
        }

        .btn-secondary:hover {
            background: var(--gray-50);
            border-color: var(--gray-400);
        }

        .btn-danger {
            background: white;
            color: var(--danger);
            border-color: var(--gray-300);
        }

        .btn-danger:hover {
            background: #fef2f2;
            border-color: var(--danger);
        }

        /* ================= STATS CARDS ================= */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: white;
            border-radius: var(--radius-xl);
            padding: 1.25rem;
            box-shadow: var(--shadow-xs);
            border: 1px solid var(--gray-200);
            transition: all 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--gray-300);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .stat-label {
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--gray-500);
        }

        .stat-icon {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .stat-icon.primary {
            background: #e0e7ff;
            color: var(--primary);
        }

        .stat-icon.success {
            background: #d1fae5;
            color: var(--success);
        }

        .stat-icon.warning {
            background: #fed7aa;
            color: var(--warning);
        }

        .stat-icon.info {
            background: #cffafe;
            color: var(--info);
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--gray-800);
            line-height: 1.2;
            margin-bottom: 0.25rem;
        }

        .stat-trend {
            font-size: 0.7rem;
            color: var(--gray-500);
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* ================= FILTER BAR ================= */
        .filter-bar {
            background: white;
            border-radius: var(--radius-xl);
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .filter-tabs {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .filter-chip {
            padding: 0.375rem 1rem;
            border-radius: 2rem;
            border: 1px solid var(--gray-200);
            background: white;
            color: var(--gray-600);
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-chip:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .filter-chip.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .search-wrapper {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--gray-50);
            padding: 0.375rem 0.875rem;
            border-radius: 2rem;
            border: 1px solid var(--gray-200);
            min-width: 260px;
        }

        .search-wrapper span {
            color: var(--gray-400);
            font-size: 0.9rem;
        }

        .search-wrapper input {
            border: none;
            background: none;
            outline: none;
            font-size: 0.85rem;
            color: var(--gray-700);
            width: 100%;
        }

        .search-wrapper input::placeholder {
            color: var(--gray-400);
        }

        /* ================= DATA TABLE CONTAINER ================= */
        .data-card {
            background: white;
            border-radius: var(--radius-xl);
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-xs);
            position: relative; /* Ensure it doesn't clip children if not necessary */
        }

        .card-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--gray-200);
            background: var(--gray-50);
            flex-wrap: wrap;
            gap: 1rem;
        }

        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .toolbar-left h3 {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--gray-700);
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .record-badge {
            background: var(--gray-200);
            padding: 0.25rem 0.6rem;
            border-radius: 2rem;
            font-size: 0.7rem;
            font-weight: 500;
            color: var(--gray-600);
        }

        .toolbar-right {
            display: flex;
            gap: 0.5rem;
        }

        .icon-btn {
            padding: 0.375rem 0.875rem;
            border-radius: var(--radius-md);
            border: 1px solid var(--gray-300);
            background: white;
            font-size: 0.8rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .icon-btn:hover {
            background: var(--gray-50);
            border-color: var(--gray-400);
        }

        /* ================= DATATABLE STYLES ================= */
        .dt-container {
            width: 100%;
            /* Removed overflow-x: auto to prevent vertical clipping of dropdowns */
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
            min-width: 1100px;
        }

        .data-table thead th {
            padding: 0.875rem 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--gray-600);
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .data-table tbody td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid var(--gray-100);
            color: var(--gray-700);
            vertical-align: middle;
        }

        .data-table tbody tr:hover {
            background: var(--gray-50);
        }

        /* Customer Info Cell */
        .customer-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .customer-avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .customer-details .name {
            font-weight: 500;
            color: var(--gray-800);
            margin-bottom: 0.125rem;
        }

        .customer-details .id {
            font-size: 0.7rem;
            color: var(--gray-500);
        }

        /* Contact Cell */
        .contact-cell .mobile {
            font-weight: 500;
        }

        .contact-cell .email {
            font-size: 0.7rem;
            color: var(--gray-500);
        }

        /* Wallet Badge */
        .wallet-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.75rem;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .wallet-badge.positive {
            background: #d1fae5;
            color: #065f46;
            border-color: #a7f3d0;
        }

        .wallet-badge.zero {
            background: var(--gray-100);
            color: var(--gray-500);
            border-color: var(--gray-200);
        }

        .wallet-badge:hover {
            transform: scale(1.02);
            filter: brightness(0.97);
        }

        /* Action Dropdown */
        .action-dropdown {
            position: relative;
            display: inline-block;
        }

        .action-btn {
            padding: 0.375rem 0.875rem;
            border-radius: var(--radius-md);
            border: 1px solid var(--gray-200);
            background: white;
            color: var(--gray-600);
            font-size: 0.75rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            transition: all 0.2s;
        }

        .action-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .action-menu {
            position: absolute;
            top: calc(100% + 0.5rem);
            right: 0;
            min-width: 180px;
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-200);
            z-index: 100;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px);
            transition: all 0.2s;
            display: block !important; /* Force block to override layout's display:none */
        }

        .action-dropdown.active .action-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .action-item {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.625rem 1rem;
            color: var(--gray-700);
            text-decoration: none;
            font-size: 0.8rem;
            transition: all 0.15s;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            border-bottom: 1px solid var(--gray-100);
        }

        .action-item:last-child {
            border-bottom: none;
        }

        .action-item:hover {
            background: var(--gray-50);
        }

        .action-item.view {
            color: var(--primary);
        }

        .action-item.edit {
            color: var(--warning);
        }

        .action-item.credit {
            color: var(--success);
        }

        .action-item.debit {
            color: var(--danger);
        }

        .action-item.delete {
            color: var(--danger);
        }

        /* Checkbox Styling */
        .checkbox-cell {
            width: 30px;
            text-align: center;
        }

        .checkbox-cell input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: var(--primary);
        }

        /* Pagination */
        .pagination-wrapper {
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--gray-200);
            display: flex;
            justify-content: flex-end;
            background: white;
        }

        /* ================= MODALS ================= */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(2px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-container {
            background: white;
            border-radius: var(--radius-xl);
            max-width: 520px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: var(--shadow-lg);
            animation: modalFadeIn 0.2s ease;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border-radius: var(--radius-xl) var(--radius-xl) 0 0;
        }

        .modal-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1;
            padding: 0;
        }

        .modal-close:hover {
            color: white;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .balance-card {
            background: linear-gradient(135deg, var(--gray-50), white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .balance-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: var(--gray-500);
            letter-spacing: 0.03em;
        }

        .balance-amount {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-800);
            margin: 0.25rem 0;
        }

        .wallet-actions-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .wallet-action-card {
            padding: 1rem;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .wallet-action-card:hover {
            border-color: var(--primary);
            background: var(--gray-50);
            transform: translateY(-2px);
        }

        .transaction-list {
            max-height: 280px;
            overflow-y: auto;
        }

        .transaction-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--gray-100);
        }

        .transaction-icon {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }

        .transaction-icon.credit {
            background: #d1fae5;
            color: var(--success);
        }

        .transaction-icon.debit {
            background: #fee2e2;
            color: var(--danger);
        }

        .transaction-details {
            flex: 1;
        }

        .transaction-type {
            font-weight: 500;
            font-size: 0.85rem;
        }

        .transaction-meta {
            font-size: 0.7rem;
            color: var(--gray-500);
        }

        .transaction-amount {
            font-weight: 600;
            font-size: 0.85rem;
        }

        .transaction-amount.credit {
            color: var(--success);
        }

        .transaction-amount.debit {
            color: var(--danger);
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.375rem;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--gray-700);
        }

        .form-control {
            width: 100%;
            padding: 0.625rem 0.875rem;
            border-radius: var(--radius-md);
            border: 1px solid var(--gray-300);
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
        }

        .amount-input-large {
            font-size: 1.25rem;
            font-weight: 600;
            padding: 0.75rem 1rem;
        }

        .quick-amounts {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .quick-amount {
            padding: 0.375rem 0.875rem;
            border-radius: 2rem;
            border: 1px solid var(--gray-200);
            background: white;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .quick-amount:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .payment-methods {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .method-card {
            flex: 1;
            padding: 0.5rem;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-md);
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: white;
        }

        .method-card.selected {
            border-color: var(--primary);
            background: #eff6ff;
            color: var(--primary);
        }

        .info-box {
            background: #fef3c7;
            padding: 0.75rem 1rem;
            border-radius: var(--radius-md);
            font-size: 0.8rem;
            margin: 1rem 0;
            border-left: 3px solid var(--warning);
        }

        .btn-submit {
            width: 100%;
            padding: 0.75rem;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit.credit {
            background: var(--success);
            color: white;
        }

        .btn-submit.debit {
            background: var(--danger);
            color: white;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            filter: brightness(0.95);
        }

        /* Toast */
        .toast-notification {
            position: fixed;
            bottom: 24px;
            right: 24px;
            padding: 0.875rem 1.25rem;
            border-radius: var(--radius-lg);
            background: white;
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            z-index: 1100;
            min-width: 280px;
            border-left: 4px solid;
            animation: slideUp 0.3s ease;
        }

        .toast-notification.success {
            border-left-color: var(--success);
        }

        .toast-notification.error {
            border-left-color: var(--danger);
        }

        @keyframes slideUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Loading */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .loading-overlay.active {
            display: flex;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--gray-200);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .erp-dashboard {
                padding: 0 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .search-wrapper {
                width: 100%;
            }
        }
    </style>

    <div class="erp-dashboard">
        <!-- Loading -->
        <div id="loadingOverlay" class="loading-overlay">
            <div class="spinner"></div>
        </div>

        <!-- Header -->
        <div class="erp-header">
            <div class="header-brand">
                <div class="brand-icon">👥</div>
                <div class="brand-title">
                    <h1>Customer Management</h1>
                    <p>Manage customer profiles, wallet balances & transactions</p>
                </div>
            </div>
            <div class="header-actions">
                @if(auth()->user()->hasPermission('view_reports'))
                    <a href="{{ route('wallet.report') }}" class="btn btn-secondary">
                        <span>📊</span> Wallet Report
                    </a>
                @endif
                @if(auth()->user()->hasPermission('create_customers'))
                    <a href="{{ route('customers.create') }}" class="btn btn-primary">
                        <span>+</span> Add Customer
                    </a>
                @endif
            </div>
        </div>

        <!-- Stats Cards -->
        @php
            $totalWalletBalance = 0;
            $customersWithWallet = 0;
            $totalTransactions = 0;
            foreach ($customers as $customer) {
                $balance = $customer->getCurrentWalletBalanceAttribute();
                if ($balance > 0) {
                    $totalWalletBalance += $balance;
                    $customersWithWallet++;
                }
                $totalTransactions += $customer->wallet()->count();
            }
        @endphp

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Total Customers</span>
                    <div class="stat-icon primary">👥</div>
                </div>
                <div class="stat-value">{{ number_format($customers->total()) }}</div>
                <div class="stat-trend">Registered in system</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Wallet Balance</span>
                    <div class="stat-icon success">💰</div>
                </div>
                <div class="stat-value">₹{{ number_format($totalWalletBalance, 2) }}</div>
                <div class="stat-trend">{{ $customersWithWallet }} customers with balance</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Active Wallets</span>
                    <div class="stat-icon warning">👛</div>
                </div>
                <div class="stat-value">{{ $customersWithWallet }}</div>
                <div class="stat-trend">Active wallet holders</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Transactions</span>
                    <div class="stat-icon info">📋</div>
                </div>
                <div class="stat-value">{{ number_format($totalTransactions) }}</div>
                <div class="stat-trend">Total wallet transactions</div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <div class="filter-tabs">
                <button class="filter-chip active" data-filter="all">All Customers</button>
                <button class="filter-chip" data-filter="with-balance">With Balance</button>
                <button class="filter-chip" data-filter="zero-balance">Zero Balance</button>
            </div>
            <div class="search-wrapper">
                <span>🔍</span>
                <input type="text" id="searchInput" placeholder="Search by name, email, mobile, ID...">
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="data-card">
            <div class="card-toolbar">
                <div class="toolbar-left">
                    <h3>Customer List</h3>
                    <span class="record-badge" id="recordCount">{{ $customers->total() }} records</span>
                </div>
                <div class="toolbar-right">
                    @if(auth()->user()->hasPermission('export_customers'))
                        <button class="icon-btn" onclick="exportCustomers()">
                            <span>📥</span> Export CSV
                        </button>
                    @endif
                    @if(auth()->user()->hasPermission('delete_customers'))
                        <button class="icon-btn" id="bulkDeleteBtn" onclick="bulkDelete()" style="display: none; color: var(--danger);">
                            <span>🗑️</span> Delete Selected
                        </button>
                    @endif
                </div>
            </div>

            <div class="dt-container">
                <table class="data-table" id="customersTable">
                    <thead>
                        <tr>
                            <th class="checkbox-cell">
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)">
                            </th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>GST Number</th>
                            <th>Wallet Balance</th>
                            <th>Last Activity</th>
                            <th style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse ($customers as $index => $customer)
                            @php
                                $walletBalance = $customer->getCurrentWalletBalanceAttribute();
                                $lastTransaction = $customer->wallet()->latest()->first();
                                $customerId = $customer->id;
                                $customerName = addslashes($customer->name);
                                $mobile = $customer->mobile ?? 'N/A';
                                $email = $customer->email ?? 'No email';
                                $gst = $customer->gst_no ?? '—';
                                $lastActivity = $lastTransaction ? $lastTransaction->created_at->diffForHumans() : 'No activity';
                                $balanceClass = $walletBalance > 0 ? 'positive' : 'zero';
                            @endphp
                            <tr data-wallet="{{ $walletBalance > 0 ? 'positive' : 'zero' }}"
                                data-customer-id="{{ $customerId }}"
                                data-name="{{ strtolower($customerName) }}"
                                data-email="{{ strtolower($email) }}"
                                data-mobile="{{ $mobile }}">
                                <td class="checkbox-cell">
                                    <input type="checkbox" class="customer-checkbox" value="{{ $customerId }}" onchange="updateBulkDeleteBtn()">
                                </td>
                                <td>
                                    <div class="customer-info">
                                        <div class="customer-avatar">{{ strtoupper(substr($customer->name, 0, 1)) }}</div>
                                        <div class="customer-details">
                                            <div class="name">{{ $customer->name }}</div>
                                            <div class="id">ID: {{ str_pad($customerId, 5, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="contact-cell">
                                        <div class="mobile">{{ $mobile }}</div>
                                        <div class="email">{{ $email }}</div>
                                    </div>
                                </td>
                                <td>{{ $gst }}</td>
                                <td>
                                    <button class="wallet-badge {{ $balanceClass }}" onclick="showWalletModal({{ $customerId }}, '{{ $customerName }}', {{ $walletBalance }})">
                                        <span>💰</span> ₹{{ number_format($walletBalance, 2) }}
                                    </button>
                                </td>
                                <td style="font-size: 0.75rem; color: var(--gray-500);">{{ $lastActivity }}</td>
                                <td>
                                    <div class="action-dropdown" data-index="{{ $index }}">
                                        <button class="action-btn" onclick="toggleActionDropdown(this, event)">
                                            Actions <span>▼</span>
                                        </button>
                                        <div class="action-menu">
                                            @if(auth()->user()->hasPermission('view_customers'))
                                                <a href="{{ route('customers.sales', $customerId) }}" class="action-item view">
                                                    <span>👁️</span> View Details
                                                </a>
                                            @endif
                                            @if(auth()->user()->hasPermission('manage_customer_wallet'))
                                                <button class="action-item credit" onclick="showAddAdvanceModal({{ $customerId }}, '{{ $customerName }}', {{ $walletBalance }})">
                                                    <span>➕</span> Add to Wallet
                                                </button>
                                                @if ($walletBalance > 0)
                                                    <button class="action-item debit" onclick="showUseAdvanceModal({{ $customerId }}, '{{ $customerName }}', {{ $walletBalance }})">
                                                        <span>➖</span> Use from Wallet
                                                    </button>
                                                @endif
                                                <a href="{{ route('customers.edit', $customerId) }}" class="action-item edit">
                                                    <span>✏️</span> Edit Customer
                                                </a>
                                            @endif
                                            @if(auth()->user()->hasPermission('delete_customers'))
                                                <form method="POST" action="{{ route('customers.destroy', $customerId) }}" style="margin:0;" onsubmit="return confirmDelete(event, '{{ $customerName }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-item delete">
                                                        <span>🗑️</span> Delete Customer
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 3rem;">
                                    <div style="font-size: 3rem; opacity: 0.5;">📭</div>
                                    <div style="margin-top: 1rem; color: var(--gray-500);">No customers found</div>
                                    <a href="{{ route('customers.create') }}" class="btn btn-primary" style="margin-top: 1rem;">Add Customer</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($customers, 'links') && $customers->hasPages())
                <div class="pagination-wrapper">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Wallet Modal -->
    <div id="walletModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3>💰 Wallet Details</h3>
                <button class="modal-close" onclick="closeWalletModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="balance-card" id="walletInfo"></div>
                <div class="wallet-actions-grid">
                    @if(auth()->user()->hasPermission('edit_customers'))
                        <div class="wallet-action-card" onclick="proceedToAdd()">
                            <div style="font-size: 1.5rem; margin-bottom: 0.25rem;">➕</div>
                            <div style="font-weight: 600;">Add Money</div>
                            <div style="font-size: 0.7rem; color: var(--gray-500);">Credit to wallet</div>
                        </div>
                        <div class="wallet-action-card" id="useWalletCard" onclick="proceedToUse()">
                            <div style="font-size: 1.5rem; margin-bottom: 0.25rem;">➖</div>
                            <div style="font-weight: 600;">Use Money</div>
                            <div style="font-size: 0.7rem; color: var(--gray-500);">Debit from wallet</div>
                        </div>
                    @endif
                </div>
                <div>
                    <h4 style="font-size: 0.8rem; font-weight: 600; margin-bottom: 0.75rem; text-transform: uppercase; color: var(--gray-500);">Recent Transactions</h4>
                    <div id="transactionList" class="transaction-list">
                        <div style="text-align: center; padding: 1rem; color: var(--gray-400);">Loading...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Amount Transaction Modal -->
    <div id="amountModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header" id="amountModalHeader">
                <h3 id="amountModalTitle">Add to Wallet</h3>
                <button class="modal-close" onclick="closeAmountModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="walletForm">
                    @csrf
                    <input type="hidden" name="customer_id" id="walletCustomerId">
                    <input type="hidden" name="method" id="selectedMethod" value="cash">

                    <div class="form-group">
                        <label id="amountLabel">Amount (₹)</label>
                        <input type="number" name="amount" id="walletAmount" class="form-control amount-input-large" step="1" min="1" required placeholder="Enter amount" oninput="onAmountChange()">
                        <div id="amountError" style="font-size: 0.7rem; color: var(--danger); margin-top: 0.25rem;"></div>
                    </div>

                    <div class="quick-amounts">
                        <button type="button" class="quick-amount" onclick="setAmount(500)">₹500</button>
                        <button type="button" class="quick-amount" onclick="setAmount(1000)">₹1,000</button>
                        <button type="button" class="quick-amount" onclick="setAmount(2000)">₹2,000</button>
                        <button type="button" class="quick-amount" onclick="setAmount(5000)">₹5,000</button>
                        <button type="button" class="quick-amount" onclick="setAmount(10000)">₹10,000</button>
                    </div>

                    <div id="paymentSection">
                        <div class="form-group">
                            <label>Payment Method</label>
                            <div class="payment-methods">
                                <div class="method-card selected" onclick="selectMethod('cash', this)">💵 Cash</div>
                                <div class="method-card" onclick="selectMethod('upi', this)">📱 UPI</div>
                                <div class="method-card" onclick="selectMethod('card', this)">💳 Card</div>
                                <div class="method-card" onclick="selectMethod('bank_transfer', this)">🏦 Bank</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Reference (Optional)</label>
                            <input type="text" name="reference" id="walletReference" class="form-control" placeholder="UTR / Cheque No / Ref ID">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Remarks (Optional)</label>
                        <input type="text" name="remarks" class="form-control" placeholder="Transaction note">
                    </div>

                    <div id="useAdvanceInfo" class="info-box" style="display: none;">
                        ⚠️ This amount will be deducted from the customer's wallet balance.
                    </div>

                    <button type="submit" class="btn-submit credit" id="submitBtn">
                        <span>💳</span> Process Transaction
                    </button>
                    <button type="button" style="width: 100%; margin-top: 0.5rem; padding: 0.5rem; background: none; border: 1px solid var(--gray-300); border-radius: var(--radius-md); cursor: pointer; font-size: 0.8rem;" onclick="closeAmountModal()">Cancel</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div id="toast" class="toast-notification" style="display: none;">
        <span id="toastIcon">✅</span>
        <span id="toastMessage"></span>
    </div>

    @push('scripts')
    <script>
        // ================= GLOBAL VARIABLES =================
        let currentCustomerId = null;
        let currentCustomerName = '';
        let currentBalance = 0;
        let isAddMode = true;

        // ================= HELPER FUNCTIONS =================
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const icon = document.getElementById('toastIcon');
            const msg = document.getElementById('toastMessage');
            msg.textContent = message;
            icon.textContent = type === 'success' ? '✅' : type === 'error' ? '❌' : '⚠️';
            toast.className = `toast-notification ${type}`;
            toast.style.display = 'flex';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);
        }

        function showLoading(show) {
            const overlay = document.getElementById('loadingOverlay');
            if (show) overlay.classList.add('active');
            else overlay.classList.remove('active');
        }

        window.closeAllDropdowns = function() {
            document.querySelectorAll('.action-dropdown.active').forEach(el => {
                el.classList.remove('active');
            });
        };

        // Forcefully expose to window to avoid conflicts
        window.toggleActionDropdown = function(btn, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            const dropdown = btn.closest('.action-dropdown');
            if (!dropdown) return;

            const wasActive = dropdown.classList.contains('active');
            window.closeAllDropdowns();

            if (!wasActive) {
                dropdown.classList.add('active');
                console.log('Dropdown activated for:', dropdown.dataset.index);
            }
        };

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.action-dropdown')) {
                window.closeAllDropdowns();
            }
        });

        // ================= SEARCH & FILTER (Live DataTable) =================
        function filterAndSearch() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const activeFilter = document.querySelector('.filter-chip.active').dataset.filter;
            const rows = document.querySelectorAll('#customersTable tbody tr[data-customer-id]');
            let visibleCount = 0;

            rows.forEach(row => {
                const walletType = row.dataset.wallet;
                const name = row.dataset.name || '';
                const email = row.dataset.email || '';
                const mobile = row.dataset.mobile || '';
                const id = row.dataset.customerId || '';

                let matchesFilter = true;
                if (activeFilter === 'with-balance') matchesFilter = walletType === 'positive';
                else if (activeFilter === 'zero-balance') matchesFilter = walletType === 'zero';

                const matchesSearch = searchTerm === '' || 
                    name.includes(searchTerm) || 
                    email.includes(searchTerm) || 
                    mobile.includes(searchTerm) || 
                    id.includes(searchTerm);

                if (matchesFilter && matchesSearch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            document.getElementById('recordCount').innerText = `${visibleCount} records`;
            document.getElementById('selectAllCheckbox').checked = false;
            updateBulkDeleteBtn();
        }

        document.getElementById('searchInput').addEventListener('keyup', filterAndSearch);
        document.querySelectorAll('.filter-chip').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-chip').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                filterAndSearch();
            });
        });

        // ================= EXPORT =================
        function exportCustomers() {
            const rows = [];
            document.querySelectorAll('#customersTable tbody tr[data-customer-id]:not([style*="display: none"])').forEach(row => {
                const name = row.querySelector('.customer-details .name')?.textContent || '';
                const mobile = row.querySelector('.contact-cell .mobile')?.textContent || '';
                const email = row.querySelector('.contact-cell .email')?.textContent || '';
                const gst = row.querySelector('td:nth-child(4)')?.textContent || '';
                const balance = row.querySelector('.wallet-badge')?.textContent.replace('💰', '').trim() || '0';
                rows.push([name, mobile, email, gst, balance]);
            });
            if (rows.length === 0) {
                showToast('No customers to export', 'warning');
                return;
            }
            const csv = ['Name,Mobile,Email,GST,Wallet Balance', ...rows.map(r => r.join(','))].join('\n');
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'customers_export.csv';
            a.click();
            window.URL.revokeObjectURL(url);
            showToast('Export completed', 'success');
        }

        // ================= BULK DELETE =================
        function toggleSelectAll(checkbox) {
            const visibleCheckboxes = document.querySelectorAll('#customersTable tbody tr[data-customer-id]:not([style*="display: none"]) .customer-checkbox');
            visibleCheckboxes.forEach(cb => cb.checked = checkbox.checked);
            updateBulkDeleteBtn();
        }

        function updateBulkDeleteBtn() {
            const allCheckboxes = document.querySelectorAll('.customer-checkbox');
            const selectedCount = Array.from(allCheckboxes).filter(cb => cb.checked).length;
            const deleteBtn = document.getElementById('bulkDeleteBtn');
            if (selectedCount > 0) {
                deleteBtn.style.display = 'inline-flex';
                deleteBtn.innerHTML = `<span>🗑️</span> Delete (${selectedCount})`;
            } else {
                deleteBtn.style.display = 'none';
            }
            const visibleCustomerRows = document.querySelectorAll('#customersTable tbody tr[data-customer-id]:not([style*="display: none"])').length;
            const visibleChecked = Array.from(document.querySelectorAll('#customersTable tbody tr[data-customer-id]:not([style*="display: none"]) .customer-checkbox')).filter(cb => cb.checked).length;
            const selectAll = document.getElementById('selectAllCheckbox');
            if (visibleChecked === 0) {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            } else if (visibleChecked === visibleCustomerRows && visibleCustomerRows > 0) {
                selectAll.checked = true;
                selectAll.indeterminate = false;
            } else {
                selectAll.checked = false;
                selectAll.indeterminate = true;
            }
        }

        function bulkDelete() {
            const selectedIds = Array.from(document.querySelectorAll('.customer-checkbox:checked')).map(cb => cb.value);
            if (selectedIds.length === 0) {
                showToast('Please select customers to delete', 'warning');
                return;
            }
            if (!confirm(`Delete ${selectedIds.length} customer(s)? This action cannot be undone.`)) return;
            showLoading(true);
            fetch('{{ route('customers.bulk-delete') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ customer_ids: selectedIds })
            })
            .then(res => res.json())
            .then(data => {
                showLoading(false);
                if (data.success) {
                    showToast(`${selectedIds.length} customer(s) deleted`, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.message || 'Deletion failed', 'error');
                }
            })
            .catch(() => {
                showLoading(false);
                showToast('Network error', 'error');
            });
        }

        // ================= WALLET MODAL =================
        function showWalletModal(customerId, customerName, balance) {
            closeAllDropdowns();
            currentCustomerId = customerId;
            currentCustomerName = customerName;
            currentBalance = balance;
            document.getElementById('walletInfo').innerHTML = `
                <div class="balance-label">Current Balance</div>
                <div class="balance-amount">₹${balance.toFixed(2)}</div>
                <div style="font-size: 0.8rem; color: var(--gray-600);">${customerName}</div>
            `;
            document.getElementById('useWalletCard').style.display = balance > 0 ? 'flex' : 'none';
            document.getElementById('walletModal').classList.add('active');
            loadWalletHistory(customerId);
        }

        function closeWalletModal() {
            document.getElementById('walletModal').classList.remove('active');
        }

        function loadWalletHistory(customerId) {
            const list = document.getElementById('transactionList');
            list.innerHTML = '<div style="text-align: center; padding: 1rem;">Loading...</div>';
            fetch(`/wallet/history/${customerId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.history?.length) {
                        list.innerHTML = data.history.map(trans => `
                            <div class="transaction-item">
                                <div class="transaction-icon ${trans.type}">${trans.type === 'credit' ? '➕' : '➖'}</div>
                                <div class="transaction-details">
                                    <div class="transaction-type">${trans.type === 'credit' ? 'Credit' : 'Debit'}</div>
                                    <div class="transaction-meta">${trans.reference || 'No ref'} | Bal: ₹${parseFloat(trans.balance).toFixed(2)}</div>
                                </div>
                                <div class="transaction-amount ${trans.type}">${trans.type === 'credit' ? '+' : '-'}₹${parseFloat(trans.amount).toFixed(2)}</div>
                            </div>
                        `).join('');
                    } else {
                        list.innerHTML = '<div style="text-align: center; padding: 1rem; color: var(--gray-400);">No transactions</div>';
                    }
                })
                .catch(() => {
                    list.innerHTML = '<div style="text-align: center; padding: 1rem; color: var(--danger);">Error loading</div>';
                });
        }

        function proceedToAdd() {
            showAddAdvanceModal(currentCustomerId, currentCustomerName, currentBalance);
        }

        function proceedToUse() {
            if (currentBalance <= 0) {
                showToast('No wallet balance available', 'warning');
                return;
            }
            showUseAdvanceModal(currentCustomerId, currentCustomerName, currentBalance);
        }

        // ================= ADD/USE MODAL =================
        function showAddAdvanceModal(customerId, customerName, balance) {
            closeAllDropdowns();
            currentCustomerId = customerId;
            currentCustomerName = customerName;
            currentBalance = parseFloat(balance) || 0;
            isAddMode = true;

            document.getElementById('amountModalTitle').innerHTML = '➕ Add to Wallet';
            document.getElementById('amountModalHeader').style.background = 'linear-gradient(135deg, #1e40af, #1e3a8a)';
            document.getElementById('amountLabel').innerHTML = 'Amount to Add (₹)';
            document.getElementById('walletCustomerId').value = customerId;
            document.getElementById('paymentSection').style.display = 'block';
            document.getElementById('useAdvanceInfo').style.display = 'none';
            document.getElementById('submitBtn').className = 'btn-submit credit';
            document.getElementById('submitBtn').innerHTML = '<span>➕</span> Add to Wallet';
            document.getElementById('walletForm').action = '{{ route('wallet.add') }}';
            document.getElementById('selectedMethod').value = 'cash';
            document.querySelectorAll('.method-card').forEach(c => c.classList.remove('selected'));
            document.querySelector('.method-card')?.classList.add('selected');
            document.getElementById('walletForm').reset();
            document.getElementById('amountError').textContent = '';
            document.getElementById('liveSummary')?.remove();
            closeWalletModal();
            document.getElementById('amountModal').classList.add('active');
            setTimeout(() => document.getElementById('walletAmount').focus(), 200);
        }

        function showUseAdvanceModal(customerId, customerName, balance) {
            closeAllDropdowns();
            if (balance <= 0) {
                showToast('No wallet balance', 'warning');
                return;
            }
            currentCustomerId = customerId;
            currentCustomerName = customerName;
            currentBalance = parseFloat(balance) || 0;
            isAddMode = false;

            document.getElementById('amountModalTitle').innerHTML = '➖ Use from Wallet';
            document.getElementById('amountModalHeader').style.background = 'linear-gradient(135deg, #dc2626, #b91c1c)';
            document.getElementById('amountLabel').innerHTML = 'Amount to Use (₹)';
            document.getElementById('walletCustomerId').value = customerId;
            document.getElementById('paymentSection').style.display = 'none';
            document.getElementById('useAdvanceInfo').style.display = 'block';
            document.getElementById('submitBtn').className = 'btn-submit debit';
            document.getElementById('submitBtn').innerHTML = '<span>➖</span> Deduct from Wallet';
            document.getElementById('walletForm').action = '{{ route('wallet.use') }}';
            document.getElementById('walletForm').reset();
            document.getElementById('amountError').textContent = '';
            closeWalletModal();
            document.getElementById('amountModal').classList.add('active');
            setTimeout(() => document.getElementById('walletAmount').focus(), 200);
        }

        function closeAmountModal() {
            document.getElementById('amountModal').classList.remove('active');
        }

        function setAmount(amount) {
            document.getElementById('walletAmount').value = amount;
            onAmountChange();
        }

        function selectMethod(method, element) {
            document.querySelectorAll('.method-card').forEach(c => c.classList.remove('selected'));
            element.classList.add('selected');
            document.getElementById('selectedMethod').value = method;
        }

        function onAmountChange() {
            const amount = parseFloat(document.getElementById('walletAmount').value) || 0;
            const errorEl = document.getElementById('amountError');
            if (!isAddMode && amount > currentBalance) {
                errorEl.textContent = `Insufficient balance. Available: ₹${currentBalance.toFixed(2)}`;
            } else {
                errorEl.textContent = '';
            }
        }

        // ================= FORM SUBMIT =================
        document.getElementById('walletForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const amount = parseFloat(document.getElementById('walletAmount').value);
            if (!amount || amount <= 0) {
                showToast('Enter valid amount', 'error');
                return;
            }
            if (!isAddMode && amount > currentBalance) {
                showToast(`Insufficient balance. Max: ₹${currentBalance.toFixed(2)}`, 'error');
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span>⏳</span> Processing...';
            showLoading(true);

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    customer_id: document.getElementById('walletCustomerId').value,
                    amount: amount,
                    method: document.getElementById('selectedMethod').value,
                    reference: document.getElementById('walletReference')?.value || '',
                    remarks: document.querySelector('[name="remarks"]')?.value || ''
                })
            })
            .then(res => res.json())
            .then(data => {
                showLoading(false);
                if (data.success) {
                    showToast(data.message, 'success');
                    closeAmountModal();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.message || 'Transaction failed', 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = isAddMode ? '<span>➕</span> Add to Wallet' : '<span>➖</span> Deduct from Wallet';
                }
            })
            .catch(() => {
                showLoading(false);
                showToast('Network error', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = isAddMode ? '<span>➕</span> Add to Wallet' : '<span>➖</span> Deduct from Wallet';
            });
        });

        function confirmDelete(event, customerName) {
            if (!confirm(`Delete "${customerName}"? This will delete all associated records.`)) {
                event.preventDefault();
                return false;
            }
            return true;
        }

        // Session messages
        @if (session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif
        @if (session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
    </script>
    @endpush
@endsection