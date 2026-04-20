@extends('layouts.app')

@section('page-title', 'Sales Management')

@section('content')
    <style>
        /* ================= PROFESSIONAL DESIGN SYSTEM ================= */
        :root {
            --primary: #3b82f6;
            --primary-dark: #1d4ed8;
            --success: #10b981;
            --success-dark: #059669;
            --danger: #ef4444;
            --danger-dark: #dc2626;
            --warning: #f59e0b;
            --info: #0ea5e9;
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
            --radius-2xl: 24px;
            --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
            font-family: var(--font-sans);
            color: var(--text-main);
            line-height: 1.5;
        }

        /* ================= MAIN CONTAINER ================= */
        .sales-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
            padding: clamp(16px, 3vw, 30px);
            width: 100%;
        }

        .container {
            max-width: 1600px;
            margin: 0 auto;
            width: 100%;
        }

        /* ================= SALES DASHBOARD ================= */
        .sales-dashboard {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            padding: clamp(20px, 4vw, 30px);
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--border);
            width: 100%;
        }

        /* ================= HEADER ================= */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 25px;
            border-bottom: 2px solid #f1f5f9;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: clamp(12px, 3vw, 20px);
            flex-wrap: wrap;
        }

        .header-icon {
            width: clamp(50px, 8vw, 60px);
            height: clamp(50px, 8vw, 60px);
            background: linear-gradient(135deg, var(--purple) 0%, #7c3aed 100%);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
            flex-shrink: 0;
        }

        .header-icon span {
            font-size: clamp(24px, 4vw, 28px);
            color: white;
        }

        .header-content h1 {
            margin: 0;
            font-size: clamp(24px, 5vw, 32px);
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.5px;
            word-break: break-word;
        }

        .header-content p {
            margin: 6px 0 0;
            color: var(--text-muted);
            font-size: clamp(13px, 2.5vw, 15px);
            word-break: break-word;
        }

        /* ================= ACTION BUTTONS ================= */
        .action-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: clamp(12px, 2.5vw, 14px) clamp(20px, 4vw, 28px);
            border-radius: var(--radius-lg);
            border: none;
            font-weight: 600;
            font-size: clamp(13px, 2.5vw, 14px);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.25);
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.35);
        }

        .btn-secondary {
            background: white;
            color: #475569;
            padding: clamp(12px, 2.5vw, 14px) clamp(20px, 4vw, 28px);
            border-radius: var(--radius-lg);
            border: 1.5px solid var(--border);
            font-weight: 600;
            font-size: clamp(13px, 2.5vw, 14px);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.3s;
            white-space: nowrap;
        }

        .btn-secondary:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .btn-bulk-download {
            background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
            color: white;
            padding: clamp(12px, 2.5vw, 14px) clamp(20px, 4vw, 28px);
            border-radius: var(--radius-lg);
            border: none;
            font-weight: 600;
            font-size: clamp(13px, 2.5vw, 14px);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 20px rgba(245, 158, 11, 0.25);
            text-decoration: none;
            white-space: nowrap;
            animation: pulse 1.5s infinite;
        }

        .btn-bulk-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.35);
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(245, 158, 11, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0);
            }
        }

        /* ================= STATS CARDS ================= */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: clamp(20px, 3vw, 24px);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(135deg, var(--purple), var(--primary));
        }

        .stat-content {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .stat-text {
            flex: 1;
        }

        .stat-text h3 {
            margin: 0;
            font-size: clamp(24px, 4vw, 28px);
            font-weight: 800;
            color: var(--text-main);
            word-break: break-word;
        }

        .stat-text p {
            margin: 4px 0 0;
            color: var(--text-muted);
            font-size: clamp(12px, 2.5vw, 14px);
            font-weight: 500;
            word-break: break-word;
        }

        /* ================= SUCCESS ALERT ================= */
        .success-alert {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            color: #065f46;
            padding: clamp(14px, 3vw, 16px) clamp(16px, 4vw, 20px);
            border-radius: var(--radius-lg);
            margin-bottom: 25px;
            border-left: 4px solid var(--success);
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            font-size: clamp(13px, 2.5vw, 14px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
            word-break: break-word;
        }

        .success-alert::before {
            content: "✓";
            background: var(--success);
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .error-alert {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border-left: 4px solid var(--danger);
            padding: clamp(14px, 3vw, 16px) clamp(16px, 4vw, 20px);
            border-radius: var(--radius-lg);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            font-size: clamp(13px, 2.5vw, 14px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
            word-break: break-word;
        }

        .error-alert::before {
            content: "⚠";
            background: var(--danger);
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            flex-shrink: 0;
        }

        /* ================= SEARCH BOX ================= */
        .search-box {
            position: relative;
            margin-bottom: 25px;
        }

        .search-input {
            width: 100%;
            padding: 14px 20px 14px 48px;
            border-radius: var(--radius-lg);
            border: 1.5px solid var(--border);
            font-size: clamp(14px, 2.5vw, 15px);
            color: #374151;
            background: white;
            transition: all 0.2s;
            box-shadow: var(--shadow-md);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }

        .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 18px;
            pointer-events: none;
        }

        .search-clear {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 18px;
            display: none;
            padding: 4px;
            border-radius: 50%;
            transition: all 0.2s;
        }

        .search-clear:hover {
            background: #f1f5f9;
            color: #475569;
        }

        /* ================= ADVANCED FILTER BAR ================= */
        .advanced-filter-bar {
            background: white;
            padding: clamp(20px, 3vw, 25px);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            margin-bottom: 25px;
            box-shadow: var(--shadow-md);
        }

        .filter-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .filter-title {
            font-size: clamp(14px, 2.5vw, 16px);
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-toggle {
            background: #f1f5f9;
            border: none;
            padding: 8px 16px;
            border-radius: var(--radius-md);
            color: #64748b;
            font-size: clamp(12px, 2.5vw, 13px);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .filter-toggle:hover {
            background: #e2e8f0;
        }

        .filter-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-label {
            font-size: clamp(12px, 2.5vw, 13px);
            font-weight: 600;
            color: #475569;
        }

        .filter-input {
            padding: 10px 14px;
            border-radius: var(--radius-md);
            border: 1.5px solid var(--border);
            font-size: clamp(13px, 2.5vw, 14px);
            color: #374151;
            background: white;
            transition: all 0.2s;
            width: 100%;
        }

        .filter-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .filter-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding-top: 15px;
            border-top: 1px solid var(--border);
            flex-wrap: wrap;
        }

        /* ================= DATE RANGE PICKER ================= */
        .date-range-picker {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .date-input {
            flex: 1;
            min-width: 120px;
        }

        .date-separator {
            color: #94a3b8;
            font-weight: 500;
        }

        @media (max-width: 576px) {
            .date-range-picker {
                flex-direction: column;
                align-items: stretch;
            }

            .date-separator {
                display: none;
            }
        }

        /* ================= DATATABLE CONTAINER ================= */
        .datatable-container {
            background: white;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: visible !important;
            width: 100%;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }

        .datatable {
            width: 100%;
            border-collapse: collapse;
            font-size: clamp(13px, 2.2vw, 14px);
            min-width: 1200px;
        }

        .datatable thead {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }

        .datatable th {
            padding: 18px 20px;
            text-align: left;
            font-weight: 700;
            color: #475569;
            font-size: clamp(11px, 2vw, 12px);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--border);
            white-space: nowrap;
            user-select: none;
            position: relative;
        }

        .datatable th.sortable {
            cursor: pointer;
            transition: color 0.2s;
        }

        .datatable th.sortable:hover {
            color: var(--primary);
            background: #f1f5f9;
        }

        .datatable th.sortable .sort-icon {
            margin-left: 6px;
            opacity: 0.5;
            transition: opacity 0.2s;
            display: inline-block;
        }

        .datatable th.sortable:hover .sort-icon {
            opacity: 1;
        }

        .datatable tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s ease;
        }

        .datatable tbody tr:hover {
            background: #f8fafc;
        }

        .datatable td {
            padding: 18px 20px;
            color: #475569;
            font-weight: 500;
            vertical-align: middle;
            white-space: nowrap;
        }

        /* ================= ACTION CELL ================= */
        .action-cell {
            position: relative;
            width: 140px;
            min-width: 140px;
        }

        .action-container {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .action-btn {
            padding: 10px 18px;
            border-radius: var(--radius-md);
            border: none;
            font-size: clamp(12px, 2.2vw, 13px);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            color: var(--text-main);
            border: 1px solid var(--border);
            white-space: nowrap;
            min-width: 110px;
            justify-content: center;
            position: relative;
            z-index: 10;
        }

        .action-btn:hover {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-color: transparent;
        }

        .action-btn.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-color: transparent;
        }

        .action-menu {
            display: none;
            position: absolute;
            background: white;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            min-width: 200px;
            width: max-content;
            top: 100%;
            left: 0;
            margin-top: 4px;
            overflow: hidden;
            animation: fadeIn 0.15s ease;
        }

        .action-menu::before {
            content: '';
            position: absolute;
            top: -10px;
            left: 0;
            right: 0;
            height: 10px;
            background: transparent;
        }

        .action-container:hover .action-menu,
        .action-menu:hover {
            display: block;
        }

        .action-menu.show {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .action-menu-item {
            padding: 12px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            color: #374151;
            font-size: clamp(13px, 2.2vw, 14px);
            font-weight: 500;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            white-space: nowrap;
            border-bottom: 1px solid #f1f5f9;
        }

        .action-menu-item:last-child {
            border-bottom: none;
        }

        .action-menu-item:hover {
            background: #f8fafc;
            padding-left: 24px;
        }

        .action-menu-item.view:hover {
            color: var(--primary);
        }

        .action-menu-item.edit:hover {
            color: var(--purple);
        }

        .action-menu-item.download:hover {
            color: var(--success);
        }

        .action-menu-item.email:hover {
            color: var(--info);
            background: #e0f2fe;
        }

        .action-menu-item.delete:hover {
            color: var(--danger);
            background: #fee2e2;
        }

        .delete-form {
            margin: 0;
            padding: 0;
        }

        .delete-form button {
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            padding: 0;
            margin: 0;
            font: inherit;
            cursor: pointer;
        }

        /* ================= INVOICE CELL ================= */
        .invoice-cell {
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .invoice-cell .invoice-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4f46e5;
            font-size: 14px;
            font-weight: 600;
            flex-shrink: 0;
        }

        /* ================= DATE CELL ================= */
        .date-cell {
            color: #64748b;
            font-size: clamp(12px, 2.2vw, 13px);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .date-cell .date-icon {
            color: #94a3b8;
            font-size: 16px;
        }

        /* ================= CUSTOMER CELL ================= */
        .customer-cell {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 200px;
        }

        .customer-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 15px;
            flex-shrink: 0;
        }

        .customer-info {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .customer-name {
            font-weight: 600;
            color: #374151;
            font-size: clamp(13px, 2.2vw, 14px);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .customer-mobile {
            font-size: clamp(11px, 2vw, 12px);
            color: #94a3b8;
        }

        /* ================= STATUS BADGES ================= */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: clamp(10px, 2vw, 11px);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .status-badge:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .status-paid {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .status-pending {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .status-overdue {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .status-draft {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            color: #4b5563;
            border: 1px solid #d1d5db;
        }

        /* ================= AMOUNT CELL ================= */
        .amount-cell {
            font-weight: 700;
            font-size: clamp(14px, 2.5vw, 15px);
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .amount-positive {
            color: #065f46;
        }

        .amount-negative {
            color: #dc2626;
        }

        .currency-symbol {
            color: #94a3b8;
            font-weight: 600;
        }

        /* ================= EMPTY STATE ================= */
        .empty-state {
            padding: 60px 20px;
            text-align: center;
            background: #f8fafc;
        }

        .empty-content {
            max-width: 400px;
            margin: 0 auto;
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin: 0 auto 20px;
            color: #4f46e5;
        }

        .empty-title {
            font-size: clamp(18px, 3.5vw, 20px);
            font-weight: 700;
            color: #374151;
            margin-bottom: 10px;
            word-break: break-word;
        }

        .empty-description {
            color: #6b7280;
            font-size: clamp(13px, 2.5vw, 14px);
            line-height: 1.6;
            margin-bottom: 25px;
            word-break: break-word;
        }

        /* ================= BULK ACTIONS ================= */
        .bulk-actions {
            display: none;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 16px 20px;
            background: #f1f5f9;
            border-bottom: 1px solid var(--border);
            flex-wrap: wrap;
        }

        .bulk-actions.show {
            display: flex;
        }

        .bulk-select-all {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: clamp(13px, 2.5vw, 14px);
            color: #475569;
            font-weight: 500;
            flex-wrap: wrap;
        }

        .bulk-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .bulk-btn {
            padding: 8px 16px;
            border-radius: var(--radius-md);
            border: none;
            font-weight: 600;
            font-size: clamp(12px, 2.2vw, 13px);
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .bulk-btn.bulk-download {
            background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
            color: white;
        }

        .bulk-btn.bulk-download:hover {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            transform: translateY(-1px);
        }

        .bulk-btn.bulk-print {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
        }

        .bulk-btn.bulk-export {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .bulk-btn.bulk-email {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: white;
        }

        .bulk-btn.bulk-delete {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .bulk-btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
        }

        .table-checkbox {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            border: 2px solid #cbd5e1;
            cursor: pointer;
            transition: all 0.2s;
        }

        .table-checkbox:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        /* ================= EXPORT MENU ================= */
        .export-menu {
            position: relative;
            display: inline-block;
        }

        .export-dropdown {
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 5px;
            background: white;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-lg);
            min-width: 200px;
            z-index: 100;
            display: none;
        }

        .export-dropdown.show {
            display: block;
        }

        .export-option {
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: background 0.2s;
            border-bottom: 1px solid #f1f5f9;
        }

        .export-option:last-child {
            border-bottom: none;
        }

        .export-option:hover {
            background: #f8fafc;
        }

        /* ================= DATATABLE FOOTER ================= */
        .datatable-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: clamp(16px, 3vw, 20px);
            background: #f8fafc;
            border-top: 1px solid var(--border);
            flex-wrap: wrap;
            gap: 15px;
        }

        .pagination-info {
            color: #64748b;
            font-size: clamp(13px, 2.5vw, 14px);
            font-weight: 500;
            word-break: break-word;
        }

        .pagination {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .pagination-btn {
            padding: 10px 14px;
            border-radius: var(--radius-md);
            border: 1.5px solid var(--border);
            background: white;
            color: #475569;
            font-weight: 600;
            font-size: clamp(12px, 2.2vw, 13px);
            cursor: pointer;
            transition: all 0.2s;
            min-width: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .pagination-btn:hover:not(:disabled) {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        .pagination-btn.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-color: transparent;
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination-ellipsis {
            padding: 10px;
            color: #94a3b8;
        }

        /* ================= LOADING OVERLAY ================= */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.95);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading-overlay.show {
            display: flex;
        }

        .export-progress {
            background: white;
            padding: 30px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            text-align: center;
            max-width: 400px;
            margin: 20px;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid var(--border);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: var(--border);
            border-radius: 4px;
            margin: 20px 0;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            width: 0%;
            transition: width 0.3s ease;
        }

        .progress-text {
            font-size: 14px;
            color: #64748b;
            margin-top: 10px;
        }

        /* ================= TOAST NOTIFICATION ================= */
        .toast-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 12px 24px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 10000;
            display: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            animation: slideIn 0.3s ease;
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

        /* ================= RESPONSIVE BREAKPOINTS ================= */
        @media (min-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 1199px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 991px) {
            .dashboard-header {
                flex-direction: column;
                align-items: stretch;
            }

            .action-buttons {
                justify-content: flex-start;
            }

            .filter-content {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .sales-page {
                padding: 15px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .datatable-footer {
                flex-direction: column;
                align-items: stretch;
            }

            .action-btn {
                padding: 8px 12px;
                font-size: 12px;
                min-width: 90px;
            }

            .datatable {
                min-width: 1000px;
            }

            .bulk-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .bulk-buttons {
                justify-content: center;
            }
        }

        @media (max-width: 575px) {
            .sales-page {
                padding: 12px;
            }

            .sales-dashboard {
                padding: 16px;
            }

            .filter-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .filter-actions {
                flex-direction: column;
            }

            .btn-primary,
            .btn-secondary {
                width: 100%;
                justify-content: center;
            }

            .bulk-buttons {
                flex-direction: column;
                width: 100%;
            }

            .bulk-btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media print {

            .action-buttons,
            .btn-primary,
            .btn-secondary,
            .export-menu,
            .filter-toggle,
            .filter-actions,
            .bulk-actions,
            .pagination,
            .search-clear,
            .action-cell,
            .table-checkbox {
                display: none !important;
            }

            .status-badge {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>

    <div class="sales-page">
        <div class="container">
            <div class="sales-dashboard" id="salesDashboard">
                <!-- Loading Overlay -->
                <div class="loading-overlay" id="loadingOverlay">
                    <div class="export-progress">
                        <div class="loading-spinner"></div>
                        <h3 style="margin: 20px 0 10px; color: #1e293b;">Processing Request</h3>
                        <div class="progress-bar">
                            <div class="progress-fill" id="progressFill"></div>
                        </div>
                        <div class="progress-text" id="progressText">Please wait...</div>
                    </div>
                </div>

                <!-- Toast Notification -->
                <div id="toastNotification" class="toast-notification"></div>

                {{-- HEADER --}}
                <div class="dashboard-header">
                    <div class="header-left">
                        <div class="header-icon">
                            <span>💰</span>
                        </div>
                        <div class="header-content">
                            <h1>Sales Management</h1>
                            <p>Track, manage, and analyze your sales transactions</p>
                        </div>
                    </div>
                    <div class="action-buttons">
                        @if(auth()->user()->hasPermission('create_sales'))
                            <a href="{{ route('sales.create') }}" class="btn-primary">
                                <span style="font-size: 20px;">+</span>
                                New Sale
                            </a>
                        @endif
                        @if(auth()->user()->hasPermission('export_sales'))
                            <div class="export-menu">
                                <button class="btn-secondary" id="exportBtn">
                                    <span>📤</span>
                                    Export
                                    <span>▼</span>
                                </button>
                                <div class="export-dropdown" id="exportDropdown">
                                    <div class="export-option" data-format="csv">
                                        <span>📁</span>
                                        Export as CSV
                                    </div>
                                    <div class="export-option" data-format="excel">
                                        <span>📊</span>
                                        Export as Excel
                                    </div>
                                    <div class="export-option" data-format="pdf">
                                        <span>📄</span>
                                        Export as PDF
                                    </div>
                                    <div class="export-option" onclick="window.print()">
                                        <span>🖨️</span>
                                        Print List
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- STATS CARDS --}}
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-content">
                            <div class="stat-icon"
                                style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white;">
                                📊
                            </div>
                            <div class="stat-text">
                                <h3 id="totalInvoices">{{ $sales->total() }}</h3>
                                <p>Total Invoices</p>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-content">
                            <div class="stat-icon"
                                style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                                ₹
                            </div>
                            <div class="stat-text">
                                <h3 id="totalRevenue">₹{{ number_format($sales->sum('grand_total'), 2) }}</h3>
                                <p>Total Revenue</p>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-content">
                            <div class="stat-icon"
                                style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white;">
                                👥
                            </div>
                            <div class="stat-text">
                                <h3 id="totalCustomers">{{ $customersCount ?? '0' }}</h3>
                                <p>Active Customers</p>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-content">
                            <div class="stat-icon"
                                style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
                                📈
                            </div>
                            <div class="stat-text">
                                <h3 id="averageInvoice">₹{{ number_format($sales->avg('grand_total') ?? 0, 2) }}</h3>
                                <p>Average Invoice</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SUCCESS/ERROR MESSAGES --}}
                @if (session('success'))
                    <div class="success-alert" id="successAlert">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="error-alert" id="errorAlert">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- SEARCH BAR --}}
                <div class="search-box">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="searchInput" class="search-input"
                        placeholder="Search by invoice number, customer name, amount...">
                    <button class="search-clear" id="searchClear" title="Clear search">×</button>
                </div>

                {{-- ADVANCED FILTER --}}
                <div class="advanced-filter-bar">
                    <div class="filter-header">
                        <div class="filter-title">
                            <span>⚙️</span>
                            Advanced Filters
                        </div>
                        <button class="filter-toggle" id="toggleFilters">
                            <span>▼</span>
                            Show Filters
                        </button>
                    </div>
                    <div class="filter-content" id="filterContent" style="display: none;">
                        <div class="filter-group">
                            <label class="filter-label">Date Range</label>
                            <div class="date-range-picker">
                                <input type="date" id="startDate" class="filter-input date-input">
                                <span class="date-separator">to</span>
                                <input type="date" id="endDate" class="filter-input date-input">
                            </div>
                        </div>
                        <div class="filter-group">
                            <label class="filter-label">Status</label>
                            <select class="filter-input" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="paid">Paid</option>
                                <option value="pending">Pending</option>
                                <option value="overdue">Overdue</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label class="filter-label">Customer</label>
                            <select class="filter-input" id="customerFilter">
                                <option value="">All Customers</option>
                                @foreach ($customers ?? [] as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group">
                            <label class="filter-label">Amount Range</label>
                            <div class="date-range-picker">
                                <input type="number" id="minAmount" class="filter-input date-input" placeholder="Min">
                                <span class="date-separator">to</span>
                                <input type="number" id="maxAmount" class="filter-input date-input" placeholder="Max">
                            </div>
                        </div>
                    </div>
                    <div class="filter-actions" id="filterActions" style="display: none;">
                        <button class="btn-secondary" id="resetFilters">
                            Reset All
                        </button>
                        <button class="btn-primary" id="applyFilters">
                            Apply Filters
                        </button>
                    </div>
                </div>

                {{-- BULK ACTIONS --}}
                <div class="bulk-actions" id="bulkActions">
                    <div class="bulk-select-all">
                        <input type="checkbox" id="selectAllBulk" class="table-checkbox">
                        <span id="selectedCount">0 items selected</span>
                    </div>
                    <div class="bulk-buttons">
                        @if(auth()->user()->hasPermission('export_sales'))
                            <button class="bulk-btn bulk-download" id="bulkDownloadZip"
                                title="Download selected invoices as ZIP">
                                <span>📦</span>
                                Bulk Invoice Download
                            </button>
                        @endif
                        @if(auth()->user()->hasPermission('view_sales'))
                            <button class="bulk-btn bulk-print" id="bulkPrint" title="Print Selected">
                                <span>🖨️</span>
                                Print
                            </button>
                        @endif
                        @if(auth()->user()->hasPermission('export_sales'))
                            <button class="bulk-btn bulk-export" id="bulkExportBtn" title="Export Selected">
                                <span>📤</span>
                                Export
                            </button>
                        @endif
                        @if(auth()->user()->hasPermission('view_sales'))
                            <button class="bulk-btn bulk-email" id="bulkEmailBtn" title="Email Selected Invoices">
                                <span>📧</span>
                                Email
                            </button>
                        @endif
                        @if(auth()->user()->hasPermission('delete_sales'))
                            <button class="bulk-btn bulk-delete" id="bulkDelete" title="Delete Selected">
                                <span>🗑️</span>
                                Delete
                            </button>
                        @endif
                    </div>
                </div>

                {{-- DATATABLE --}}
                <div class="datatable-container">
                    <div class="table-responsive">
                        <table class="datatable" id="salesTable">
                            <thead>

                                <th width="40">
                                    <input type="checkbox" id="selectAll" class="table-checkbox">
                                </th>
                                <th width="140">Actions</th>
                                <th class="sortable" data-sort="invoice_no">
                                    Invoice # <span class="sort-icon">↓</span>
                                </th>
                                <th class="sortable" data-sort="sale_date">
                                    Date <span class="sort-icon">↓</span>
                                </th>
                                <th class="sortable" data-sort="customer_name">
                                    Customer <span class="sort-icon">↓</span>
                                </th>
                                <th class="sortable" data-sort="status">
                                    Status <span class="sort-icon">↓</span>
                                </th>
                                <th class="sortable" data-sort="amount">
                                    Amount <span class="sort-icon">↓</span>
                                </th>
                                </tr>
                            </thead>
                            <tbody id="salesTableBody">
                                @if (count($sales) > 0)
                                    @foreach ($sales as $sale)
                                        <tr data-id="{{ $sale->id }}" data-invoice="{{ $sale->invoice_no }}"
                                            data-customer="{{ $sale->customer->name ?? 'Walk-in Customer' }}"
                                            data-status="{{ $sale->payment_status }}"
                                            data-amount="{{ $sale->grand_total }}" data-date="{{ $sale->sale_date }}"
                                            data-customer-id="{{ $sale->customer_id }}"
                                            data-customer-email="{{ $sale->customer->email ?? '' }}">
                                            <td>
                                                <input type="checkbox" class="row-checkbox table-checkbox">
                                            </td>
                                            <td class="action-cell">
                                                <div class="action-container">
                                                    <button type="button" class="action-btn">
                                                        <span>⚡</span>
                                                        Actions
                                                        <span style="font-size: 12px;">▼</span>
                                                    </button>
                                                    <div class="action-menu">
                                                        @if(auth()->user()->hasPermission('view_sales'))
                                                            <a href="{{ route('sales.show', $sale->id) }}"
                                                                class="action-menu-item view">
                                                                <span>👁️</span>
                                                                View Details
                                                            </a>
                                                        @endif
                                                        @if(auth()->user()->hasPermission('edit_sales'))
                                                            <a href="{{ route('sales.edit', $sale->id) }}"
                                                                class="action-menu-item edit">
                                                                <span>✏️</span>
                                                                Edit Sale
                                                            </a>
                                                        @endif
                                                        @if(auth()->user()->hasPermission('view_sales'))
                                                            <a href="{{ route('sales.invoice', $sale->id) }}"
                                                                class="action-menu-item download" download>
                                                                <span>📥</span>
                                                                Download Invoice
                                                            </a>
                                                        @endif
                                                        @if(auth()->user()->hasPermission('export_sales'))
                                                            @if ($sale->customer && $sale->customer->email)
                                                                <a href="#"
                                                                    onclick="sendSingleEmail('{{ $sale->id }}', '{{ $sale->customer->email }}', '{{ $sale->invoice_no }}'); return false;"
                                                                    class="action-menu-item email">
                                                                    <span>📧</span>
                                                                    Email Invoice
                                                                </a>
                                                            @else
                                                                <span class="action-menu-item email"
                                                                    style="opacity:0.5; cursor:not-allowed;"
                                                                    title="No customer email available">
                                                                    <span>📧</span>
                                                                    Email Invoice
                                                                </span>
                                                            @endif
                                                        @endif
                                                        @if(auth()->user()->hasPermission('delete_sales'))
                                                            <form action="{{ route('sales.destroy', $sale->id) }}"
                                                                method="POST" class="delete-form">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="action-menu-item delete"
                                                                    onclick="return confirm('Are you sure you want to delete this sale?')">
                                                                    <span>🗑️</span>
                                                                    Delete Sale
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="invoice-cell">
                                                    <div class="invoice-icon">
                                                        {{ substr($sale->invoice_no, -3) }}
                                                    </div>
                                                    <div>
                                                        <div>{{ $sale->invoice_no }}</div>
                                                        <div style="font-size: 11px; color: #94a3b8;">
                                                            {{ $sale->invoice_type ?? 'Sale' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="date-cell">
                                                    <span class="date-icon">📅</span>
                                                    {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="customer-cell">
                                                    <div class="customer-avatar">
                                                        {{ substr($sale->customer->name ?? 'W', 0, 1) }}
                                                    </div>
                                                    <div class="customer-info">
                                                        <div class="customer-name"
                                                            title="{{ $sale->customer->name ?? 'Walk-in Customer' }}">
                                                            {{ $sale->customer->name ?? 'Walk-in Customer' }}
                                                        </div>
                                                        <div class="customer-mobile">
                                                            {{ $sale->customer->mobile ?? 'No contact' }}
                                                            @if ($sale->customer && $sale->customer->email)
                                                                <span style="margin-left:8px; color:#0ea5e9;"
                                                                    title="Email available">📧</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = 'status-paid';
                                                    $statusIcon = '✓';
                                                    if ($sale->payment_status == 'pending') {
                                                        $statusClass = 'status-pending';
                                                        $statusIcon = '⏱️';
                                                    } elseif ($sale->payment_status == 'overdue') {
                                                        $statusClass = 'status-overdue';
                                                        $statusIcon = '⚠️';
                                                    } elseif ($sale->payment_status == 'draft') {
                                                        $statusClass = 'status-draft';
                                                        $statusIcon = '📝';
                                                    }
                                                @endphp
                                                <span class="status-badge {{ $statusClass }}">
                                                    <span>{{ $statusIcon }}</span>
                                                    {{ ucfirst($sale->payment_status ?? 'paid') }}
                                                </span>
                                            </td>
                                            <td>
                                                <div
                                                    class="amount-cell {{ $sale->grand_total >= 0 ? 'amount-positive' : 'amount-negative' }}">
                                                    <span class="currency-symbol">₹</span>
                                                    {{ number_format($sale->grand_total, 2) }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="empty-state">
                                            <div class="empty-content">
                                                <div class="empty-icon">
                                                    📊
                                                </div>
                                                <div class="empty-title">No Sales Records Found</div>
                                                <div class="empty-description">
                                                    Start by creating your first sales invoice. All your sales transactions
                                                    will
                                                    appear here for tracking and analysis.
                                                </div>
                                                <a href="{{ route('sales.create') }}" class="btn-primary"
                                                    style="display: inline-flex; margin-top: 15px;">
                                                    <span>+</span>
                                                    Create Your First Sale
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- PAGINATION --}}
                @if ($sales->hasPages())
                    <div class="datatable-footer">
                        <div class="pagination-info" id="paginationInfo">
                            Showing <span id="startCount">{{ $sales->firstItem() ?? 0 }}</span> to
                            <span id="endCount">{{ $sales->lastItem() ?? 0 }}</span> of
                            <span id="totalCount">{{ $sales->total() }}</span> entries
                        </div>
                        <div class="pagination">
                            @if ($sales->onFirstPage())
                                <button class="pagination-btn" disabled>
                                    <span>←</span>
                                    Previous
                                </button>
                            @else
                                <a href="{{ $sales->previousPageUrl() }}" class="pagination-btn">
                                    <span>←</span>
                                    Previous
                                </a>
                            @endif

                            @php
                                $current = $sales->currentPage();
                                $last = $sales->lastPage();
                                $start = max($current - 2, 1);
                                $end = min($current + 2, $last);
                            @endphp

                            @if ($start > 1)
                                <a href="{{ $sales->url(1) }}"
                                    class="pagination-btn {{ 1 == $current ? 'active' : '' }}">
                                    1
                                </a>
                                @if ($start > 2)
                                    <span class="pagination-ellipsis">...</span>
                                @endif
                            @endif

                            @for ($i = $start; $i <= $end; $i++)
                                <a href="{{ $sales->url($i) }}"
                                    class="pagination-btn {{ $i == $current ? 'active' : '' }}">
                                    {{ $i }}
                                </a>
                            @endfor

                            @if ($end < $last)
                                @if ($end < $last - 1)
                                    <span class="pagination-ellipsis">...</span>
                                @endif
                                <a href="{{ $sales->url($last) }}"
                                    class="pagination-btn {{ $last == $current ? 'active' : '' }}">
                                    {{ $last }}
                                </a>
                            @endif

                            @if ($sales->hasMorePages())
                                <a href="{{ $sales->nextPageUrl() }}" class="pagination-btn">
                                    Next
                                    <span>→</span>
                                </a>
                            @else
                                <button class="pagination-btn" disabled>
                                    Next
                                    <span>→</span>
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

    <script>
        // ✅ TOAST FUNCTION
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toastNotification');
            const colors = {
                success: '#10b981',
                error: '#ef4444',
                warning: '#f59e0b',
                info: '#3b82f6'
            };
            toast.style.background = colors[type] || colors.info;
            toast.textContent = message;
            toast.style.display = 'block';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);
        }

        // ✅ DIRECT SINGLE EMAIL FUNCTION
        function sendSingleEmail(saleId, customerEmail, invoiceNo) {
            if (!customerEmail) {
                showToast('Cannot send email: Customer email not available', 'error');
                return;
            }

            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.classList.add('show');
                document.getElementById('progressText').textContent = 'Sending email...';
            }

            fetch('{{ route('sales.send-invoice') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ||
                            '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        sale_id: saleId,
                        recipient_email: customerEmail,
                        email_subject: `Invoice #${invoiceNo} from {{ config('app.name') }}`,
                        email_body: `Dear Customer,\n\nPlease find attached the invoice #${invoiceNo} for your recent purchase.\n\nThank you for your business!`
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (loadingOverlay) {
                        loadingOverlay.classList.remove('show');
                    }
                    if (data.success) {
                        showToast(`✅ Email sent to ${customerEmail}`, 'success');
                    } else {
                        showToast(`❌ Error: ${data.message}`, 'error');
                    }
                })
                .catch(error => {
                    if (loadingOverlay) {
                        loadingOverlay.classList.remove('show');
                    }
                    showToast('❌ Error sending email', 'error');
                    console.error('Error:', error);
                });
        }

        // ✅ BULK DOWNLOAD AS ZIP FUNCTION
        async function bulkDownloadAsZip() {
            const selectedIds = getSelectedIds();

            if (selectedIds.length === 0) {
                showToast('Please select at least one invoice to download', 'warning');
                return;
            }

            const loadingOverlay = document.getElementById('loadingOverlay');
            const progressFill = document.getElementById('progressFill');
            const progressText = document.getElementById('progressText');

            if (loadingOverlay) {
                loadingOverlay.classList.add('show');
                progressFill.style.width = '0%';
                progressText.textContent = `Preparing to download ${selectedIds.length} invoices...`;
            }

            try {
                const zip = new JSZip();
                let completed = 0;
                let failed = 0;

                for (const saleId of selectedIds) {
                    const row = document.querySelector(`tr[data-id="${saleId}"]`);
                    const invoiceNo = row?.dataset.invoice || saleId;

                    try {
                        // Fetch PDF for each invoice
                        const response = await fetch(`/sales/${saleId}/invoice`, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (response.ok) {
                            const blob = await response.blob();
                            zip.file(`Invoice_${invoiceNo}.pdf`, blob);
                            completed++;
                        } else {
                            failed++;
                            console.error(`Failed to download invoice ${invoiceNo}`);
                        }
                    } catch (error) {
                        failed++;
                        console.error(`Error downloading invoice ${invoiceNo}:`, error);
                    }

                    // Update progress
                    const progress = ((completed + failed) / selectedIds.length) * 100;
                    if (progressFill) {
                        progressFill.style.width = `${progress}%`;
                    }
                    if (progressText) {
                        progressText.textContent = `Downloaded ${completed} of ${selectedIds.length} invoices...`;
                    }
                }

                // Generate and download ZIP file
                if (completed > 0) {
                    if (progressText) {
                        progressText.textContent = 'Creating ZIP file...';
                    }

                    const zipBlob = await zip.generateAsync({
                        type: 'blob'
                    });
                    const timestamp = new Date().toISOString().slice(0, 19).replace(/:/g, '-');
                    saveAs(zipBlob, `Invoices_${timestamp}.zip`);

                    showToast(
                        `✅ Successfully downloaded ${completed} invoices${failed > 0 ? ` (${failed} failed)` : ''}`,
                        failed > 0 ? 'warning' : 'success');
                } else {
                    showToast('❌ Failed to download any invoices', 'error');
                }

            } catch (error) {
                console.error('Bulk download error:', error);
                showToast('❌ Error creating ZIP file', 'error');
            } finally {
                if (loadingOverlay) {
                    loadingOverlay.classList.remove('show');
                }
                if (progressFill) {
                    progressFill.style.width = '0%';
                }
            }
        }

        // ✅ BULK EMAIL FUNCTION
        function sendBulkEmail() {
            const selectedIds = getSelectedIds();
            if (selectedIds.length === 0) {
                showToast('Please select at least one invoice', 'warning');
                return;
            }

            const rowsWithEmail = [];
            const rowsWithoutEmail = [];

            selectedIds.forEach(id => {
                const row = document.querySelector(`tr[data-id="${id}"]`);
                const customerEmail = row?.dataset.customerEmail;
                const invoiceNo = row?.dataset.invoice;

                if (customerEmail) {
                    rowsWithEmail.push({
                        id,
                        email: customerEmail,
                        invoice: invoiceNo
                    });
                } else {
                    rowsWithoutEmail.push(invoiceNo || id);
                }
            });

            if (rowsWithEmail.length === 0) {
                showToast('None of the selected invoices have customer email addresses', 'warning');
                return;
            }

            if (rowsWithoutEmail.length > 0) {
                showToast(`${rowsWithEmail.length} emails sending, ${rowsWithoutEmail.length} skipped (no email)`, 'info');
            }

            const loadingOverlay = document.getElementById('loadingOverlay');
            const progressFill = document.getElementById('progressFill');
            const progressText = document.getElementById('progressText');

            if (loadingOverlay) {
                loadingOverlay.classList.add('show');
                progressFill.style.width = '0%';
                progressText.textContent = `Sending ${rowsWithEmail.length} emails...`;
            }

            let sentCount = 0;
            let failedCount = 0;

            const promises = rowsWithEmail.map((item, index) => {
                return fetch('{{ route('sales.send-invoice') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ||
                                '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            sale_id: item.id,
                            recipient_email: item.email,
                            email_subject: `Invoice #${item.invoice} from {{ config('app.name') }}`,
                            email_body: `Dear Customer,\n\nPlease find attached the invoice #${item.invoice} for your recent purchase.\n\nThank you for your business!`
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            sentCount++;
                        } else {
                            failedCount++;
                        }

                        // Update progress
                        const progress = ((sentCount + failedCount) / rowsWithEmail.length) * 100;
                        if (progressFill) {
                            progressFill.style.width = `${progress}%`;
                        }
                        if (progressText) {
                            progressText.textContent = `Sent ${sentCount} of ${rowsWithEmail.length} emails...`;
                        }
                    })
                    .catch(() => {
                        failedCount++;
                        const progress = ((sentCount + failedCount) / rowsWithEmail.length) * 100;
                        if (progressFill) {
                            progressFill.style.width = `${progress}%`;
                        }
                        if (progressText) {
                            progressText.textContent = `Sent ${sentCount} of ${rowsWithEmail.length} emails...`;
                        }
                    });
            });

            Promise.all(promises).then(() => {
                if (loadingOverlay) {
                    loadingOverlay.classList.remove('show');
                }
                showToast(`✅ Emails sent: ${sentCount} successful, ${failedCount} failed`,
                    failedCount > 0 ? 'warning' : 'success');

                document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
                document.getElementById('selectAll').checked = false;
                updateBulkActions();
            });
        }

        function getSelectedIds() {
            const selectedIds = [];
            document.querySelectorAll('.row-checkbox:checked').forEach(checkbox => {
                const row = checkbox.closest('tr');
                if (row && row.dataset.id) {
                    selectedIds.push(row.dataset.id);
                }
            });
            return selectedIds;
        }

        function updateBulkActions() {
            const selectedCount = document.querySelectorAll('.row-checkbox:checked').length;
            const selectedCountElement = document.getElementById('selectedCount');
            const bulkActions = document.getElementById('bulkActions');

            if (selectedCountElement) {
                selectedCountElement.textContent = `${selectedCount} item${selectedCount !== 1 ? 's' : ''} selected`;
            }

            if (bulkActions) {
                if (selectedCount > 0) {
                    bulkActions.classList.add('show');
                } else {
                    bulkActions.classList.remove('show');
                }
            }
        }

        // ✅ MAIN INITIALIZATION
        document.addEventListener('DOMContentLoaded', function() {
            // DOM Elements
            const searchInput = document.getElementById('searchInput');
            const searchClear = document.getElementById('searchClear');
            const toggleFilters = document.getElementById('toggleFilters');
            const filterContent = document.getElementById('filterContent');
            const filterActions = document.getElementById('filterActions');
            const exportBtn = document.getElementById('exportBtn');
            const exportDropdown = document.getElementById('exportDropdown');
            const applyFilters = document.getElementById('applyFilters');
            const resetFilters = document.getElementById('resetFilters');
            const selectAll = document.getElementById('selectAll');
            const bulkDelete = document.getElementById('bulkDelete');
            const bulkPrint = document.getElementById('bulkPrint');
            const bulkExportBtn = document.getElementById('bulkExportBtn');
            const bulkEmailBtn = document.getElementById('bulkEmailBtn');
            const bulkDownloadZip = document.getElementById('bulkDownloadZip');
            const allRows = Array.from(document.querySelectorAll('#salesTableBody tr[data-id]'));

            // Bulk Download ZIP button
            if (bulkDownloadZip) {
                bulkDownloadZip.addEventListener('click', bulkDownloadAsZip);
            }

            // Initialize date inputs
            const today = new Date().toISOString().split('T')[0];
            const lastMonth = new Date();
            lastMonth.setMonth(lastMonth.getMonth() - 1);

            if (document.getElementById('startDate')) {
                document.getElementById('startDate').value = lastMonth.toISOString().split('T')[0];
            }
            if (document.getElementById('endDate')) {
                document.getElementById('endDate').value = today;
            }

            // Toggle filters visibility
            if (toggleFilters) {
                toggleFilters.addEventListener('click', function() {
                    const isVisible = filterContent.style.display === 'grid';
                    filterContent.style.display = isVisible ? 'none' : 'grid';
                    filterActions.style.display = isVisible ? 'none' : 'flex';
                    toggleFilters.innerHTML = isVisible ?
                        '<span>▼</span> Show Filters' :
                        '<span>▲</span> Hide Filters';
                });
            }

            // Toggle export dropdown
            if (exportBtn) {
                exportBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (exportDropdown) {
                        exportDropdown.classList.toggle('show');
                    }
                });
            }

            // Close export dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (exportBtn && exportDropdown && !exportBtn.contains(e.target) && !exportDropdown
                    .contains(e.target)) {
                    exportDropdown.classList.remove('show');
                }
            });

            // Search functionality
            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        const searchValue = this.value.toLowerCase();
                        if (searchClear) {
                            searchClear.style.display = searchValue ? 'block' : 'none';
                        }
                        filterTable(searchValue);
                    }, 300);
                });
            }

            // Clear search
            if (searchClear) {
                searchClear.addEventListener('click', function() {
                    if (searchInput) searchInput.value = '';
                    this.style.display = 'none';
                    filterTable('');
                });
            }

            // Apply filters
            if (applyFilters) {
                applyFilters.addEventListener('click', function() {
                    filterTable(searchInput ? searchInput.value : '');
                });
            }

            // Reset filters
            if (resetFilters) {
                resetFilters.addEventListener('click', function() {
                    if (document.getElementById('startDate')) {
                        document.getElementById('startDate').value = lastMonth.toISOString().split('T')[0];
                    }
                    if (document.getElementById('endDate')) {
                        document.getElementById('endDate').value = today;
                    }
                    if (document.getElementById('statusFilter')) {
                        document.getElementById('statusFilter').value = '';
                    }
                    if (document.getElementById('customerFilter')) {
                        document.getElementById('customerFilter').value = '';
                    }
                    if (document.getElementById('minAmount')) {
                        document.getElementById('minAmount').value = '';
                    }
                    if (document.getElementById('maxAmount')) {
                        document.getElementById('maxAmount').value = '';
                    }
                    filterTable(searchInput ? searchInput.value : '');
                });
            }

            // Filter table function
            function filterTable(searchValue) {
                if (allRows.length === 0) return;

                const startDate = document.getElementById('startDate')?.value;
                const endDate = document.getElementById('endDate')?.value;
                const status = document.getElementById('statusFilter')?.value;
                const customerId = document.getElementById('customerFilter')?.value;
                const minAmount = parseFloat(document.getElementById('minAmount')?.value) || 0;
                const maxAmount = parseFloat(document.getElementById('maxAmount')?.value) || Infinity;

                let visibleCount = 0;

                allRows.forEach(row => {
                    let showRow = true;

                    // Search filter
                    if (searchValue) {
                        const invoice = row.dataset.invoice?.toLowerCase() || '';
                        const customer = row.dataset.customer?.toLowerCase() || '';
                        const amount = row.dataset.amount || '';

                        if (!invoice.includes(searchValue) &&
                            !customer.includes(searchValue) &&
                            !amount.includes(searchValue)) {
                            showRow = false;
                        }
                    }

                    // Date filter
                    if (showRow && startDate && endDate && row.dataset.date) {
                        const rowDate = new Date(row.dataset.date);
                        const start = new Date(startDate);
                        const end = new Date(endDate);
                        end.setHours(23, 59, 59, 999);

                        if (rowDate < start || rowDate > end) {
                            showRow = false;
                        }
                    }

                    // Status filter
                    if (showRow && status && row.dataset.status !== status) {
                        showRow = false;
                    }

                    // Customer filter
                    if (showRow && customerId && row.dataset.customerId !== customerId) {
                        showRow = false;
                    }

                    // Amount filter
                    if (showRow) {
                        const amount = parseFloat(row.dataset.amount) || 0;
                        if (minAmount > 0 && amount < minAmount) {
                            showRow = false;
                        }
                        if (maxAmount < Infinity && amount > maxAmount) {
                            showRow = false;
                        }
                    }

                    row.style.display = showRow ? '' : 'none';
                    if (showRow) visibleCount++;
                });

                updateVisibleCount(visibleCount);
            }

            function updateVisibleCount(count) {
                if (document.getElementById('paginationInfo')) {
                    document.getElementById('paginationInfo').innerHTML =
                        `Showing <span id="startCount">1</span> to ` +
                        `<span id="endCount">${count}</span> of ` +
                        `<span id="totalCount">${count}</span> entries`;
                }
            }

            // Bulk selection
            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    const isChecked = this.checked;
                    document.querySelectorAll('.row-checkbox').forEach(checkbox => {
                        checkbox.checked = isChecked;
                    });
                    updateBulkActions();
                });

                document.addEventListener('change', function(e) {
                    if (e.target.classList.contains('row-checkbox')) {
                        updateBulkActions();
                        if (selectAll) {
                            const allCheckboxes = document.querySelectorAll('.row-checkbox');
                            const checkedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
                            selectAll.checked = allCheckboxes.length === checkedCheckboxes.length;
                        }
                    }
                });
            }

            // Bulk delete
            if (bulkDelete) {
                bulkDelete.addEventListener('click', function() {
                    const selectedIds = getSelectedIds();
                    if (selectedIds.length > 0) {
                        if (confirm(
                                `Are you sure you want to delete ${selectedIds.length} selected items?`)) {
                            const loadingOverlay = document.getElementById('loadingOverlay');
                            if (loadingOverlay) {
                                loadingOverlay.classList.add('show');
                                let progress = 0;
                                const interval = setInterval(() => {
                                    progress += 10;
                                    if (progress > 100) progress = 100;
                                    if (document.getElementById('progressFill')) {
                                        document.getElementById('progressFill').style.width =
                                            progress + '%';
                                    }
                                    if (document.getElementById('progressText')) {
                                        document.getElementById('progressText').textContent =
                                            `Deleting... ${progress}%`;
                                    }
                                    if (progress >= 100) {
                                        clearInterval(interval);
                                        setTimeout(() => {
                                            loadingOverlay.classList.remove('show');
                                            showToast(
                                                `${selectedIds.length} items deleted successfully!`,
                                                'success');
                                            setTimeout(() => window.location.reload(),
                                                1000);
                                        }, 500);
                                    }
                                }, 100);
                            }
                        }
                    }
                });
            }

            // Bulk print
            if (bulkPrint) {
                bulkPrint.addEventListener('click', function() {
                    const selectedIds = getSelectedIds();
                    if (selectedIds.length > 0) {
                        selectedIds.forEach(id => {
                            window.open(`/sales/${id}/invoice`, '_blank');
                        });
                    } else {
                        showToast('Please select at least one invoice', 'warning');
                    }
                });
            }

            // Bulk email
            if (bulkEmailBtn) {
                bulkEmailBtn.addEventListener('click', sendBulkEmail);
            }

            // Export functionality
            if (exportDropdown) {
                exportDropdown.querySelectorAll('.export-option').forEach(option => {
                    option.addEventListener('click', function() {
                        const format = this.dataset.format;
                        if (format) {
                            const loadingOverlay = document.getElementById('loadingOverlay');
                            if (loadingOverlay) {
                                loadingOverlay.classList.add('show');
                                let progress = 0;
                                const interval = setInterval(() => {
                                    progress += 10;
                                    if (progress > 100) progress = 100;
                                    if (document.getElementById('progressFill')) {
                                        document.getElementById('progressFill').style
                                            .width = progress + '%';
                                    }
                                    if (document.getElementById('progressText')) {
                                        document.getElementById('progressText')
                                            .textContent = `Exporting data... ${progress}%`;
                                    }
                                    if (progress >= 100) {
                                        clearInterval(interval);
                                        setTimeout(() => {
                                            loadingOverlay.classList.remove('show');
                                            showToast(
                                                `Export as ${format.toUpperCase()} completed!`,
                                                'success');
                                        }, 500);
                                    }
                                }, 200);
                            }
                        }
                        exportDropdown.classList.remove('show');
                    });
                });
            }

            // Initialize visible count
            updateVisibleCount(allRows.length);

            // Auto-hide success/error messages after 5 seconds
            setTimeout(() => {
                const successAlert = document.getElementById('successAlert');
                const errorAlert = document.getElementById('errorAlert');
                if (successAlert) successAlert.style.display = 'none';
                if (errorAlert) errorAlert.style.display = 'none';
            }, 5000);
        });
    </script>
@endsection
