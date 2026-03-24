@extends('layouts.app')

@section('page-title', 'Customer Management')

@section('content')
<style>
    /* ================= PROFESSIONAL DESIGN SYSTEM ================= */
    :root {
        --primary: #0f172a;
        --primary-light: #1e293b;
        --secondary: #334155;
        --accent: #3b82f6;
        --accent-light: #60a5fa;
        --success: #059669;
        --danger: #dc2626;
        --warning: #d97706;
        --info: #2563eb;
        --text-primary: #0f172a;
        --text-secondary: #475569;
        --text-muted: #64748b;
        --bg-primary: #ffffff;
        --bg-secondary: #f8fafc;
        --bg-tertiary: #f1f5f9;
        --border: #e2e8f0;
        --border-dark: #cbd5e1;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
        --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1);
        --radius-sm: 4px;
        --radius-md: 6px;
        --radius-lg: 8px;
        --radius-xl: 12px;
        --font-sans: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: var(--bg-secondary);
        font-family: var(--font-sans);
        color: var(--text-primary);
        line-height: 1.5;
    }

    /* ================= MAIN CONTAINER ================= */
    .customer-dashboard {
        max-width: 1440px;
        margin: 2rem auto;
        padding: 0 1.5rem;
        width: 100%;
    }

    /* ================= HEADER SECTION ================= */
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding: 1.5rem 2rem;
        background: var(--bg-primary);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
        width: 100%;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .header-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .header-title h1 {
        font-size: 1.75rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 0.25rem;
        letter-spacing: -0.01em;
    }

    .header-title p {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin: 0;
    }

    .header-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .btn {
        padding: 0.625rem 1.25rem;
        border-radius: var(--radius-md);
        font-size: 0.9rem;
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
        background: var(--accent);
        color: white;
        border-color: var(--accent);
    }

    .btn-primary:hover {
        background: var(--info);
        border-color: var(--info);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .btn-secondary {
        background: var(--bg-secondary);
        color: var(--text-primary);
        border-color: var(--border);
    }

    .btn-secondary:hover {
        background: var(--bg-tertiary);
        border-color: var(--border-dark);
        transform: translateY(-1px);
    }

    /* ================= METRICS GRID ================= */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
        width: 100%;
    }

    .metric-card {
        background: var(--bg-primary);
        border-radius: var(--radius-xl);
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
        transition: all 0.2s;
        width: 100%;
    }

    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: var(--accent-light);
    }

    .metric-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .metric-icon {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .metric-icon.total { background: #e0f2fe; color: #0369a1; }
    .metric-icon.balance { background: #dcfce7; color: #059669; }
    .metric-icon.wallet { background: #fef3c7; color: #b45309; }
    .metric-icon.transactions { background: #e0e7ff; color: #4f46e5; }

    .metric-label {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        white-space: nowrap;
    }

    .metric-value {
        font-size: 1.75rem;
        font-weight: 600;
        color: var(--text-primary);
        line-height: 1.2;
        word-break: break-word;
    }

    .metric-sub {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-top: 0.25rem;
        word-break: break-word;
    }

    /* ================= FILTERS SECTION ================= */
    .filters-section {
        background: var(--bg-primary);
        border-radius: var(--radius-xl);
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        width: 100%;
    }

    .filter-group {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 0.5rem 1rem;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        background: var(--bg-primary);
        color: var(--text-secondary);
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .filter-btn:hover {
        border-color: var(--accent);
        color: var(--accent);
    }

    .filter-btn.active {
        background: var(--accent);
        color: white;
        border-color: var(--accent);
    }

    .search-box {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--bg-secondary);
        padding: 0.5rem 1rem;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        width: auto;
        min-width: 250px;
    }

    .search-box input {
        border: none;
        background: none;
        outline: none;
        font-size: 0.9rem;
        color: var(--text-primary);
        width: 100%;
    }

    .search-box input::placeholder {
        color: var(--text-muted);
    }

    .search-box span {
        color: var(--text-muted);
        font-size: 1rem;
        flex-shrink: 0;
    }

    /* ================= TABLE SECTION ================= */
    .table-container {
        background: var(--bg-primary);
        border-radius: var(--radius-xl);
        border: 1px solid var(--border);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        width: 100%;
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid var(--border);
        background: var(--bg-secondary);
        flex-wrap: wrap;
        gap: 1rem;
    }

    .table-header h2 {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .table-header h2 span {
        color: var(--text-muted);
        font-weight: normal;
        font-size: 0.9rem;
    }

    .export-btn {
        padding: 0.5rem 1rem;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        background: var(--bg-primary);
        color: var(--text-secondary);
        font-size: 0.9rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .export-btn:hover {
        background: var(--bg-secondary);
        border-color: var(--border-dark);
    }

    /* ================= CUSTOM TABLE ================= */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        width: 100%;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1000px;
    }

    .data-table thead th {
        background: var(--bg-secondary);
        padding: 1rem 1.5rem;
        text-align: left;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.03em;
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }

    .data-table tbody td {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
        color: var(--text-primary);
        font-size: 0.95rem;
        white-space: nowrap;
    }

    .data-table tbody tr:last-child td {
        border-bottom: none;
    }

    .data-table tbody tr:hover {
        background: var(--bg-secondary);
    }

    /* ================= CUSTOMER INFO ================= */
    .customer-cell {
        display: flex;
        align-items: center;
        gap: 1rem;
        white-space: nowrap;
    }

    .customer-avatar {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 500;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .customer-details {
        line-height: 1.4;
    }

    .customer-name {
        font-weight: 500;
        color: var(--text-primary);
        margin-bottom: 0.15rem;
    }

    .customer-id {
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    /* ================= WALLET BADGE ================= */
    .wallet-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: var(--radius-lg);
        font-weight: 500;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid transparent;
        white-space: nowrap;
    }

    .wallet-badge.positive {
        background: #dcfce7;
        color: #059669;
        border-color: #86efac;
    }

    .wallet-badge.zero {
        background: var(--bg-tertiary);
        color: var(--text-muted);
        border-color: var(--border);
    }

    .wallet-badge:hover {
        transform: scale(1.02);
        filter: brightness(0.98);
    }

    /* ================= ACTION DROPDOWN ================= */
    .action-dropdown {
        position: relative;
        display: inline-block;
    }

    .action-trigger {
        padding: 0.5rem 1rem;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        background: var(--bg-primary);
        color: var(--text-secondary);
        font-size: 0.9rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .action-trigger:hover {
        border-color: var(--accent);
        color: var(--accent);
    }

    .action-menu {
        position: absolute;
        top: calc(100% + 0.5rem);
        right: 0;
        min-width: 200px;
        background: var(--bg-primary);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border);
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.2s;
    }

    .action-dropdown.active .action-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .action-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        color: var(--text-primary);
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.2s;
        border-bottom: 1px solid var(--border);
        width: 100%;
        text-align: left;
        background: none;
        border: none;
        cursor: pointer;
    }

    .action-item:last-child {
        border-bottom: none;
    }

    .action-item:hover {
        background: var(--bg-secondary);
    }

    .action-item.view { color: var(--info); }
    .action-item.edit { color: var(--warning); }
    .action-item.wallet-add { color: var(--success); }
    .action-item.wallet-use { color: var(--primary); }
    .action-item.delete { color: var(--danger); }

    /* ================= EMPTY STATE ================= */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-title {
        font-size: 1.1rem;
        font-weight: 500;
        color: var(--text-secondary);
        margin-bottom: 0.5rem;
    }

    .empty-text {
        color: var(--text-muted);
        margin-bottom: 1.5rem;
    }

    /* ================= PAGINATION ================= */
    .pagination {
        display: flex;
        justify-content: flex-end;
        gap: 0.25rem;
        padding: 1.5rem;
        border-top: 1px solid var(--border);
        flex-wrap: wrap;
    }

    .pagination .page-link {
        padding: 0.5rem 0.75rem;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.2s;
        background: var(--bg-primary);
        display: inline-block;
    }

    .pagination .page-link:hover {
        border-color: var(--accent);
        color: var(--accent);
    }

    .pagination .active .page-link {
        background: var(--accent);
        color: white;
        border-color: var(--accent);
    }

    /* ================= MODALS ================= */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: var(--bg-primary);
        border-radius: var(--radius-xl);
        max-width: 500px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        padding: 2rem;
        position: relative;
        box-shadow: var(--shadow-lg);
        margin: auto;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border);
        flex-wrap: wrap;
        gap: 1rem;
    }

    .modal-header h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--text-muted);
        line-height: 1;
        padding: 0.5rem;
    }

    .modal-close:hover {
        color: var(--danger);
    }

    .balance-info {
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: white;
        padding: 2rem;
        border-radius: var(--radius-lg);
        text-align: center;
        margin-bottom: 2rem;
    }

    .balance-label {
        font-size: 0.8rem;
        opacity: 0.9;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .balance-amount {
        font-size: 2.5rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
        word-break: break-word;
    }

    .balance-customer {
        font-size: 0.95rem;
        opacity: 0.9;
    }

    .wallet-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .wallet-action-card {
        padding: 1.5rem;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .wallet-action-card:hover {
        border-color: var(--accent);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .wallet-action-icon {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .wallet-action-icon.credit { color: var(--success); }
    .wallet-action-icon.debit { color: var(--danger); }

    .wallet-action-title {
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    .wallet-action-desc {
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    /* ================= ADD TO WALLET IMPROVED ================= */
    .wallet-modal-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem 1.5rem;
        background: linear-gradient(135deg, #0f172a, #1e3a5f);
        border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        margin: -2rem -2rem 1.5rem -2rem;
        color: white;
    }

    .wallet-modal-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        font-weight: 700;
        flex-shrink: 0;
    }

    .wallet-modal-customer-name {
        font-size: 1.05rem;
        font-weight: 600;
        line-height: 1.2;
    }

    .wallet-modal-balance {
        font-size: 0.85rem;
        opacity: 0.8;
        margin-top: 0.2rem;
    }

    .amount-input-wrapper {
        position: relative;
        margin-bottom: 0.5rem;
    }

    .amount-prefix {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--success);
        pointer-events: none;
    }

    .amount-input-large {
        width: 100%;
        padding: 0.875rem 1rem 0.875rem 2.25rem;
        border-radius: var(--radius-md);
        border: 2px solid var(--border);
        font-size: 1.4rem;
        font-weight: 600;
        transition: all 0.2s;
        color: var(--text-primary);
    }

    .amount-input-large:focus {
        outline: none;
        border-color: var(--success);
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
    }

    .amount-input-large.has-error {
        border-color: var(--danger);
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }

    .amount-error-msg {
        font-size: 0.82rem;
        color: var(--danger);
        margin-top: 0.3rem;
        min-height: 1.1rem;
    }

    .quick-amounts {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.5rem;
        margin-bottom: 1.25rem;
    }

    .quick-amount-btn {
        padding: 0.55rem 0.25rem;
        border-radius: var(--radius-md);
        border: 1.5px solid var(--border);
        background: var(--bg-secondary);
        color: var(--text-secondary);
        font-size: 0.88rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.18s;
        text-align: center;
    }

    .quick-amount-btn:hover {
        background: var(--success);
        color: white;
        border-color: var(--success);
        transform: translateY(-1px);
    }

    .payment-method-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.5rem;
        margin-bottom: 1.25rem;
    }

    .method-card {
        border: 2px solid var(--border);
        border-radius: var(--radius-md);
        padding: 0.75rem 0.4rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.18s;
        background: var(--bg-secondary);
        user-select: none;
    }

    .method-card:hover {
        border-color: var(--accent);
        background: #eff6ff;
    }

    .method-card.selected {
        border-color: var(--accent);
        background: #eff6ff;
        color: var(--accent);
    }

    .method-card-icon { font-size: 1.3rem; margin-bottom: 0.2rem; }
    .method-card-label { font-size: 0.76rem; font-weight: 500; }

    .live-summary {
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        border: 1.5px solid #86efac;
        border-radius: var(--radius-md);
        padding: 1rem 1.25rem;
        margin: 1rem 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .live-summary-label {
        font-size: 0.85rem;
        color: #166534;
        font-weight: 500;
    }

    .live-summary-amount {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--success);
    }

    .live-summary-new-bal {
        font-size: 0.8rem;
        color: #166534;
        margin-top: 0.1rem;
    }

    .btn-wallet-submit {
        background: linear-gradient(135deg, var(--success), #059669);
        color: white;
        border: none;
        padding: 0.95rem;
        border-radius: var(--radius-md);
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        width: 100%;
        margin-top: 0.5rem;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-wallet-submit:hover {
        background: linear-gradient(135deg, #047857, #065f46);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(5,150,105,0.35);
    }

    .btn-wallet-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .btn-wallet-use-submit {
        background: linear-gradient(135deg, var(--danger), #b91c1c);
    }
    .btn-wallet-use-submit:hover {
        background: linear-gradient(135deg, #b91c1c, #991b1b);
        box-shadow: 0 4px 12px rgba(220,38,38,0.35);
    }

    .transaction-history {
        border-top: 1px solid var(--border);
        padding-top: 1.5rem;
    }

    .transaction-history h4 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .transaction-list {
        max-height: 300px;
        overflow-y: auto;
    }

    .transaction-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-bottom: 1px solid var(--border);
        flex-wrap: wrap;
    }

    .transaction-item:last-child {
        border-bottom: none;
    }

    .transaction-icon {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .transaction-icon.credit { background: #dcfce7; color: var(--success); }
    .transaction-icon.debit { background: #fee2e2; color: var(--danger); }

    .transaction-details {
        flex: 1;
        min-width: 150px;
    }

    .transaction-type {
        font-weight: 500;
        margin-bottom: 0.15rem;
    }

    .transaction-meta {
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    .transaction-amount {
        font-weight: 600;
        white-space: nowrap;
    }

    .transaction-amount.credit { color: var(--success); }
    .transaction-amount.debit { color: var(--danger); }

    /* ================= FORM ELEMENTS ================= */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        font-size: 0.9rem;
        color: var(--text-secondary);
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .quick-amounts {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }

    .quick-amount-btn {
        padding: 0.5rem 1rem;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        background: var(--bg-primary);
        color: var(--text-secondary);
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
        flex: 1 1 auto;
    }

    .quick-amount-btn:hover {
        background: var(--accent);
        color: white;
        border-color: var(--accent);
    }

    .info-box {
        background: #eff6ff;
        padding: 1rem;
        border-radius: var(--radius-md);
        color: #1e40af;
        margin: 1rem 0;
        font-size: 0.9rem;
        border-left: 4px solid var(--info);
    }

    .modal-footer {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        flex-wrap: wrap;
    }

    .modal-footer button {
        flex: 1;
        padding: 0.875rem;
        border-radius: var(--radius-md);
        font-weight: 500;
        cursor: pointer;
        border: none;
        font-size: 0.95rem;
        transition: all 0.2s;
        min-width: 120px;
    }

    .btn-confirm {
        background: var(--accent);
        color: white;
    }

    .btn-confirm:hover {
        background: var(--info);
    }

    .btn-cancel {
        background: var(--bg-tertiary);
        color: var(--text-secondary);
    }

    .btn-cancel:hover {
        background: var(--border);
    }

    /* ================= TOAST NOTIFICATION ================= */
    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: var(--radius-lg);
        background: yellow;
        box-shadow: var(--shadow-lg);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        z-index: 11000;
        min-width: 300px;
        max-width: 90%;
        border-left: 4px solid;
        animation: slideIn 0.3s ease;
    }

    .toast-notification.success { border-left-color: var(--success); }
    .toast-notification.error { border-left-color: var(--danger); }
    .toast-notification.warning { border-left-color: var(--warning); }

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
    
    /* Large Desktop (1200px and above) */
    @media (min-width: 1200px) {
        .metrics-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    /* Desktop (992px to 1199px) */
    @media (max-width: 1199px) {
        .metrics-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .metric-value {
            font-size: 1.5rem;
        }
    }

    /* Tablet (768px to 991px) */
    @media (max-width: 991px) {
        .customer-dashboard {
            padding: 0 1rem;
        }

        .dashboard-header {
            padding: 1.25rem;
        }

        .header-title h1 {
            font-size: 1.5rem;
        }

        .metrics-grid {
            gap: 1rem;
        }

        .metric-card {
            padding: 1.25rem;
        }

        .metric-value {
            font-size: 1.25rem;
        }

        .filter-group {
            width: 100%;
            justify-content: flex-start;
        }

        .search-box {
            width: 100%;
        }

        .table-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .export-btn {
            width: 100%;
            justify-content: center;
        }
    }

    /* Mobile Landscape (576px to 767px) */
    @media (max-width: 767px) {
        .dashboard-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-actions {
            width: 100%;
        }

        .btn {
            flex: 1;
            justify-content: center;
        }

        .metrics-grid {
            grid-template-columns: 1fr;
        }

        .filters-section {
            flex-direction: column;
            align-items: flex-start;
        }

        .filter-group {
            width: 100%;
            overflow-x: auto;
            padding-bottom: 0.5rem;
            -webkit-overflow-scrolling: touch;
        }

        .filter-btn {
            flex-shrink: 0;
        }

        .wallet-actions {
            grid-template-columns: 1fr;
        }

        .modal-footer {
            flex-direction: column;
        }

        .modal-footer button {
            width: 100%;
        }

        .toast-notification {
            left: 20px;
            right: 20px;
            min-width: auto;
        }
    }

    /* Mobile Portrait (up to 575px) */
    @media (max-width: 575px) {
        .customer-dashboard {
            margin: 1rem auto;
            padding: 0 0.75rem;
        }

        .dashboard-header {
            padding: 1rem;
        }

        .header-left {
            width: 100%;
        }

        .header-icon {
            width: 40px;
            height: 40px;
            font-size: 1.25rem;
        }

        .header-title h1 {
            font-size: 1.25rem;
        }

        .header-title p {
            font-size: 0.8rem;
        }

        .header-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }

        .metric-card {
            padding: 1rem;
        }

        .metric-header {
            gap: 0.5rem;
        }

        .metric-icon {
            width: 32px;
            height: 32px;
            font-size: 1rem;
        }

        .metric-label {
            font-size: 0.75rem;
        }

        .metric-value {
            font-size: 1.1rem;
        }

        .filters-section {
            padding: 1rem;
        }

        .table-header {
            padding: 1rem;
        }

        .table-header h2 {
            font-size: 1rem;
        }

        .table-header h2 span {
            font-size: 0.8rem;
        }

        .modal-content {
            padding: 1.5rem;
        }

        .balance-amount {
            font-size: 2rem;
        }

        .balance-info {
            padding: 1.5rem;
        }

        .wallet-action-card {
            padding: 1rem;
        }

        .transaction-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .transaction-details {
            width: 100%;
        }

        .transaction-amount {
            align-self: flex-end;
        }
    }

    /* Extra Small Devices (up to 360px) */
    @media (max-width: 360px) {
        .customer-dashboard {
            padding: 0 0.5rem;
        }

        .filter-group {
            flex-direction: column;
        }

        .filter-btn {
            width: 100%;
        }

        .search-box {
            padding: 0.4rem 0.8rem;
        }

        .search-box input {
            font-size: 0.8rem;
        }

        .pagination {
            justify-content: center;
        }

        .pagination .page-link {
            padding: 0.4rem 0.6rem;
            font-size: 0.8rem;
        }
    }
</style>

<div class="customer-dashboard">
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="modal-overlay">
        <div class="modal-content" style="max-width: 300px; text-align: center;">
            <div style="width: 40px; height: 40px; border: 3px solid var(--border); border-top-color: var(--accent); border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 1rem;"></div>
            <p style="color: var(--text-secondary);">Loading...</p>
        </div>
    </div>

    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-left">
            <div class="header-icon">👥</div>
            <div class="header-title">
                <h1>Customer Management</h1>
                <p>Manage customers and wallet balances</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('wallet.report') }}" class="btn btn-secondary">
                <span>📊</span> Wallet Report
            </a>
            <a href="{{ route('customers.create') }}" class="btn btn-primary">
                <span>+</span> Add Customer
            </a>
        </div>
    </div>

    <!-- Metrics -->
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

    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-header">
                <div class="metric-icon total">👥</div>
                <span class="metric-label">Total Customers</span>
            </div>
            <div class="metric-value">{{ $customers->total() }}</div>
            <div class="metric-sub">Registered customers</div>
        </div>

        <div class="metric-card">
            <div class="metric-header">
                <div class="metric-icon balance">💰</div>
                <span class="metric-label">Wallet Balance</span>
            </div>
            <div class="metric-value">₹{{ number_format($totalWalletBalance, 2) }}</div>
            <div class="metric-sub">{{ $customersWithWallet }} customers with balance</div>
        </div>

        <div class="metric-card">
            <div class="metric-header">
                <div class="metric-icon wallet">👛</div>
                <span class="metric-label">Active Wallets</span>
            </div>
            <div class="metric-value">{{ $customersWithWallet }}</div>
            <div class="metric-sub">Customers with wallet</div>
        </div>

        <div class="metric-card">
            <div class="metric-header">
                <div class="metric-icon transactions">📋</div>
                <span class="metric-label">Transactions</span>
            </div>
            <div class="metric-value">{{ $totalTransactions }}</div>
            <div class="metric-sub">Wallet transactions</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <div class="filter-group">
            <button class="filter-btn active" onclick="filterCustomers('all')">All Customers</button>
            <button class="filter-btn" onclick="filterCustomers('with-balance')">With Balance</button>
            <button class="filter-btn" onclick="filterCustomers('zero-balance')">Zero Balance</button>
        </div>
        <div class="search-box">
            <span>🔍</span>
            <input type="text" id="searchInput" placeholder="Search customers..." onkeyup="searchCustomers()">
        </div>
    </div>

    <!-- Customers Table -->
    <div class="table-container">
        <div class="table-header">
            <h2>
                Customer List
                <span>({{ $customers->total() }} records)</span>
            </h2>
            <button class="export-btn" onclick="exportCustomers()">
                <span>📥</span> Export CSV
            </button>
        </div>

        <div class="table-responsive">
            <table class="data-table" id="customersTable">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Contact</th>
                        <th>GST Number</th>
                        <th>Wallet Balance</th>
                        <th>Last Activity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php $__col = $customers; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $index => $customer)
                        @php
                            $walletBalance = $customer->getCurrentWalletBalanceAttribute();
                            $lastTransaction = $customer->wallet()->first();
                        @endphp
                        <tr data-wallet="{{ $walletBalance > 0 ? 'positive' : 'zero' }}">
                            <td>
                                <div class="customer-cell">
                                    <div class="customer-avatar">{{ strtoupper(substr($customer->name, 0, 1)) }}</div>
                                    <div class="customer-details">
                                        <div class="customer-name">{{ $customer->name }}</div>
                                        <div class="customer-id">ID: {{ str_pad($customer->id, 5, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.9rem;">{{ $customer->mobile ?? 'N/A' }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $customer->email ?? 'No email' }}</div>
                            </td>
                            <td>{{ $customer->gst_no ?? '—' }}</td>
                            <td>
                                <button class="wallet-badge {{ $walletBalance > 0 ? 'positive' : 'zero' }}" 
                                        onclick="showWalletModal({{ $customer->id }}, '{{ addslashes($customer->name) }}', {{ $walletBalance }})">
                                    <span>💰</span>
                                    ₹{{ number_format($walletBalance, 2) }}
                                </button>
                            </td>
                            <td>
                                @if($lastTransaction)
                                    <span style="font-size: 0.85rem;">{{ $lastTransaction->created_at->diffForHumans() }}</span>
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.85rem;">No activity</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-dropdown" data-index="{{ $index }}">
                                    <button class="action-trigger" onclick="toggleDropdown(this, event)">
                                        Actions <span>▼</span>
                                    </button>
                                    <div class="action-menu">
                                        <a href="{{ route('customers.sales', $customer->id) }}" class="action-item view">
                                            <span>👁️</span> View Details
                                        </a>
                                        <button class="action-item wallet-add" onclick="showAddAdvanceModal({{ $customer->id }}, '{{ addslashes($customer->name) }}', {{ $walletBalance }})">
                                            <span>➕</span> Add to Wallet
                                        </button>
                                        @if($walletBalance > 0)
                                        <button class="action-item wallet-use" onclick="showUseAdvanceModal({{ $customer->id }}, '{{ addslashes($customer->name) }}', {{ $walletBalance }})">
                                            <span>🔄</span> Use from Wallet
                                        </button>
                                        @endif
                                        <a href="{{ route('customers.edit', $customer->id) }}" class="action-item edit">
                                            <span>✏️</span> Edit Customer
                                        </a>
                                        <form method="POST" action="{{ route('customers.destroy', $customer->id) }}" style="margin:0;" onsubmit="return confirmDelete(event, '{{ addslashes($customer->name) }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-item delete">
                                                <span>🗑️</span> Delete Customer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
@else
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-icon">📭</div>
                                    <div class="empty-title">No customers found</div>
                                    <div class="empty-text">Add your first customer to get started</div>
                                    <a href="{{ route('customers.create') }}" class="btn btn-primary">Add Customer</a>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @if(method_exists($customers, 'links') && $customers->hasPages())
            <div class="pagination">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Wallet Modal -->
<div id="walletModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>💰 Wallet Details</h3>
            <button class="modal-close" onclick="closeWalletModal()">&times;</button>
        </div>

        <div id="walletInfo" class="balance-info"></div>

        <div class="wallet-actions">
            <div class="wallet-action-card" onclick="proceedToAdd()">
                <div class="wallet-action-icon credit">➕</div>
                <div class="wallet-action-title">Add Money</div>
                <div class="wallet-action-desc">Credit to wallet</div>
            </div>
            <div class="wallet-action-card" id="useWalletCard" onclick="proceedToUse()">
                <div class="wallet-action-icon debit">➖</div>
                <div class="wallet-action-title">Use Money</div>
                <div class="wallet-action-desc">Debit from wallet</div>
            </div>
        </div>

        <div class="transaction-history">
            <h4>
                <span>📋</span> Recent Transactions
            </h4>
            <div id="transactionList" class="transaction-list" style="min-height: 100px;">
                <div style="text-align: center; padding: 2rem; color: var(--text-muted);">Loading...</div>
            </div>
        </div>
    </div>
</div>

<!-- Amount Modal -->
<div id="amountModal" class="modal-overlay">
    <div class="modal-content" style="max-width: 520px;">

        {{-- Customer header strip --}}
        <div class="wallet-modal-header" id="walletModalHeader">
            <div class="wallet-modal-avatar" id="walletModalAvatar">?</div>
            <div>
                <div class="wallet-modal-customer-name" id="walletModalCustomerName">Customer</div>
                <div class="wallet-modal-balance" id="walletModalBalanceInfo">Current Balance: ₹0.00</div>
            </div>
            <button class="modal-close" onclick="closeAmountModal()" style="margin-left:auto; color:white; opacity:0.8;">&times;</button>
        </div>

        <form id="walletForm" method="POST">
            @csrf
            <input type="hidden" name="customer_id" id="walletCustomerId">
            <input type="hidden" name="method" id="selectedMethod" value="cash">

            {{-- Amount Input --}}
            <div class="form-group">
                <label id="amountLabel" style="font-weight:600; font-size:0.95rem; color: var(--text-primary);">Amount to Add (₹)</label>
                <div class="amount-input-wrapper">
                    <span class="amount-prefix" id="amountPrefix">₹</span>
                    <input type="number" name="amount" id="walletAmount"
                           class="amount-input-large"
                           step="1" min="1" required
                           placeholder="0"
                           oninput="onAmountChange()">
                </div>
                <div class="amount-error-msg" id="amountError"></div>
            </div>

            {{-- Quick amount buttons --}}
            <div class="quick-amounts">
                <button type="button" class="quick-amount-btn" onclick="setAmount(500)">₹500</button>
                <button type="button" class="quick-amount-btn" onclick="setAmount(1000)">₹1,000</button>
                <button type="button" class="quick-amount-btn" onclick="setAmount(2000)">₹2,000</button>
                <button type="button" class="quick-amount-btn" onclick="setAmount(5000)">₹5,000</button>
            </div>

            {{-- Payment Method Cards --}}
            <div id="paymentSection">
                <div class="form-group">
                    <label style="font-weight:600; font-size:0.9rem; margin-bottom:0.65rem; display:block;">Payment Method</label>
                    <div class="payment-method-grid">
                        <div class="method-card selected" onclick="selectMethod('cash', this)">
                            <div class="method-card-icon">💵</div>
                            <div class="method-card-label">Cash</div>
                        </div>
                        <div class="method-card" onclick="selectMethod('upi', this)">
                            <div class="method-card-icon">📱</div>
                            <div class="method-card-label">UPI</div>
                        </div>
                        <div class="method-card" onclick="selectMethod('card', this)">
                            <div class="method-card-icon">💳</div>
                            <div class="method-card-label">Card</div>
                        </div>
                        <div class="method-card" onclick="selectMethod('bank_transfer', this)">
                            <div class="method-card-icon">🏦</div>
                            <div class="method-card-label">Bank</div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Reference No. <span style="color:var(--text-muted); font-weight:400;">(Optional)</span></label>
                    <input type="text" name="reference" id="walletReference" class="form-control"
                           placeholder="e.g. UPI Ref / Cheque No / UTR">
                </div>
            </div>

            {{-- Note --}}
            <div class="form-group">
                <label>Note <span style="color:var(--text-muted); font-weight:400;">(Optional)</span></label>
                <input type="text" name="remarks" class="form-control"
                       placeholder="e.g. Advance for order, Monthly deposit...">
            </div>

            {{-- Use wallet info --}}
            <div id="useAdvanceInfo" class="info-box" style="display: none;">
                ⚠️ <strong>Note:</strong> This amount will be deducted from the customer's wallet balance.
            </div>

            {{-- Live Summary --}}
            <div class="live-summary" id="liveSummary" style="display:none;">
                <div>
                    <div class="live-summary-label" id="liveSummaryLabel">Adding to Wallet</div>
                    <div class="live-summary-new-bal" id="liveSummaryNewBal">New Balance: ₹0.00</div>
                </div>
                <div class="live-summary-amount" id="liveSummaryAmount">₹0</div>
            </div>

            {{-- Buttons --}}
            <button type="submit" class="btn-wallet-submit" id="confirmBtn">
                <span id="confirmBtnIcon">💳</span>
                <span id="confirmBtnText">Add to Wallet</span>
            </button>
            <button type="button" style="width:100%; margin-top:0.5rem; padding:0.7rem; border:1px solid var(--border); border-radius:var(--radius-md); background:none; cursor:pointer; color:var(--text-muted); font-size:0.9rem;" onclick="closeAmountModal()">Cancel</button>
        </form>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="toast-notification" style="display: none;">
    <span id="toastIcon">✅</span>
    <span id="toastMessage"></span>
</div>

<script>
    // Global variables
    let currentCustomerId = null;
    let currentCustomerName = '';
    let currentBalance = 0;

    // Toggle dropdown
    function toggleDropdown(button, event) {
        event.stopPropagation();
        const container = button.closest('.action-dropdown');
        document.querySelectorAll('.action-dropdown').forEach(el => {
            if (el !== container) el.classList.remove('active');
        });
        container.classList.toggle('active');
    }

    // Close dropdowns on click outside
    document.addEventListener('click', function() {
        document.querySelectorAll('.action-dropdown').forEach(el => {
            el.classList.remove('active');
        });
    });

    // Search customers
    function searchCustomers() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#customersTable tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    }

    // Filter customers
    function filterCustomers(type) {
        // Update active filter button
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');

        const rows = document.querySelectorAll('#customersTable tbody tr');
        
        rows.forEach(row => {
            if (type === 'all') {
                row.style.display = '';
            } else if (type === 'with-balance') {
                row.style.display = row.dataset.wallet === 'positive' ? '' : 'none';
            } else if (type === 'zero-balance') {
                row.style.display = row.dataset.wallet === 'zero' ? '' : 'none';
            }
        });
    }

    // Export customers
    function exportCustomers() {
        const rows = [];
        document.querySelectorAll('#customersTable tbody tr').forEach(row => {
            if (row.style.display !== 'none') {
                const customerName = row.querySelector('.customer-name')?.textContent || '';
                const mobile = row.querySelector('td:nth-child(2) div:first-child')?.textContent || '';
                const email = row.querySelector('td:nth-child(2) div:last-child')?.textContent || '';
                const gst = row.querySelector('td:nth-child(3)')?.textContent || '';
                const balance = row.querySelector('.wallet-badge')?.textContent.replace('💰', '').trim() || '0';
                
                rows.push([customerName, mobile, email, gst, balance]);
            }
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
        a.download = 'customers.csv';
        a.click();
        window.URL.revokeObjectURL(url);
    }

    // Confirm delete
    function confirmDelete(event, customerName) {
        if (!confirm(`Are you sure you want to delete "${customerName}"? This will delete all associated records.`)) {
            event.preventDefault();
            return false;
        }
        return true;
    }

    // Show toast
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

    // Wallet modal functions
    function showWalletModal(customerId, customerName, balance) {
        currentCustomerId = customerId;
        currentCustomerName = customerName;
        currentBalance = balance;

        document.getElementById('walletInfo').innerHTML = `
            <div class="balance-label">Current Balance</div>
            <div class="balance-amount">₹${balance.toFixed(2)}</div>
            <div class="balance-customer">${customerName}</div>
        `;

        document.getElementById('useWalletCard').style.display = balance > 0 ? 'block' : 'none';
        document.getElementById('walletModal').classList.add('active');
        
        loadWalletHistory(customerId);
    }

    function closeWalletModal() {
        document.getElementById('walletModal').classList.remove('active');
    }

    function loadWalletHistory(customerId) {
        const list = document.getElementById('transactionList');
        list.innerHTML = '<div style="text-align: center; padding: 2rem;">Loading...</div>';

        fetch(`/wallet/history/${customerId}`)
            .then(res => res.json())
            .then(data => {
                if (data.history?.length > 0) {
                    list.innerHTML = data.history.map(trans => `
                        <div class="transaction-item">
                            <div class="transaction-icon ${trans.type}">
                                ${trans.type === 'credit' ? '➕' : '➖'}
                            </div>
                            <div class="transaction-details">
                                <div class="transaction-type">${trans.type === 'credit' ? 'Credit' : 'Debit'}</div>
                                <div class="transaction-meta">${trans.reference || 'No reference'}</div>
                                <div class="transaction-meta">Balance: ₹${parseFloat(trans.balance).toFixed(2)}</div>
                            </div>
                            <div class="transaction-amount ${trans.type}">
                                ${trans.type === 'credit' ? '+' : '-'}₹${parseFloat(trans.amount).toFixed(2)}
                            </div>
                        </div>
                    `).join('');
                } else {
                    list.innerHTML = '<div style="text-align: center; padding: 2rem; color: var(--text-muted);">No transactions yet</div>';
                }
            })
            .catch(() => {
                list.innerHTML = '<div style="text-align: center; padding: 2rem; color: var(--danger);">Error loading transactions</div>';
            });
    }

    // ========== PAYMENT METHOD SELECTION ==========
    let isAddMode = true; // true = add to wallet, false = use from wallet

    function selectMethod(method, card) {
        document.querySelectorAll('.method-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        document.getElementById('selectedMethod').value = method;
    }

    // ========== LIVE AMOUNT PREVIEW ==========
    function onAmountChange() {
        const amountInput = document.getElementById('walletAmount');
        const amount = parseFloat(amountInput.value) || 0;
        const errorEl = document.getElementById('amountError');
        const summaryEl = document.getElementById('liveSummary');
        const summaryAmountEl = document.getElementById('liveSummaryAmount');
        const summaryNewBalEl = document.getElementById('liveSummaryNewBal');
        const summaryLabelEl = document.getElementById('liveSummaryLabel');

        // Clear errors
        amountInput.classList.remove('has-error');
        errorEl.textContent = '';

        if (amount <= 0) {
            summaryEl.style.display = 'none';
            return;
        }

        if (!isAddMode && amount > currentBalance) {
            amountInput.classList.add('has-error');
            errorEl.textContent = `❌ Insufficient balance. Available: ₹${currentBalance.toFixed(2)}`;
            summaryEl.style.display = 'none';
            return;
        }

        // Show live summary
        summaryEl.style.display = 'flex';
        summaryAmountEl.textContent = '₹' + amount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        
        if (isAddMode) {
            const newBal = currentBalance + amount;
            summaryLabelEl.textContent = '✅ Adding to Wallet';
            summaryNewBalEl.textContent = `New Balance: ₹${newBal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            summaryEl.style.background = 'linear-gradient(135deg, #f0fdf4, #dcfce7)';
            summaryEl.style.borderColor = '#86efac';
            summaryAmountEl.style.color = 'var(--success)';
            summaryLabelEl.style.color = '#166534';
            summaryNewBalEl.style.color = '#166534';
        } else {
            const newBal = currentBalance - amount;
            summaryLabelEl.textContent = '⬇️ Deducting from Wallet';
            summaryNewBalEl.textContent = `Remaining Balance: ₹${newBal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            summaryEl.style.background = 'linear-gradient(135deg, #fff7ed, #fef3c7)';
            summaryEl.style.borderColor = '#fcd34d';
            summaryAmountEl.style.color = 'var(--warning)';
            summaryLabelEl.style.color = '#854d0e';
            summaryNewBalEl.style.color = '#854d0e';
        }
    }

    // ========== WALLET MODAL FUNCTIONS ==========
    function showAddAdvanceModal(customerId, customerName, balance) {
        currentCustomerId = customerId;
        currentCustomerName = customerName;
        currentBalance = parseFloat(balance) || 0;
        isAddMode = true;

        // Set customer header
        document.getElementById('walletModalAvatar').textContent = customerName.charAt(0).toUpperCase();
        document.getElementById('walletModalCustomerName').textContent = customerName;
        document.getElementById('walletModalBalanceInfo').textContent = 'Current Balance: ₹' + currentBalance.toLocaleString('en-IN', {minimumFractionDigits: 2});
        document.getElementById('walletModalHeader').style.background = 'linear-gradient(135deg, #0f172a, #1e3a5f)';

        // Set form
        document.getElementById('walletCustomerId').value = customerId;
        document.getElementById('amountLabel').textContent = 'Amount to Add (₹)';
        document.getElementById('amountPrefix').textContent = '₹';
        document.getElementById('amountPrefix').style.color = 'var(--success)';
        document.getElementById('paymentSection').style.display = 'block';
        document.getElementById('useAdvanceInfo').style.display = 'none';
        document.getElementById('confirmBtn').className = 'btn-wallet-submit';
        document.getElementById('confirmBtnIcon').textContent = '💳';
        document.getElementById('confirmBtnText').textContent = 'Add to Wallet';
        document.getElementById('walletForm').action = '{{ route("wallet.add") }}';
        document.getElementById('walletForm').reset();
        document.getElementById('selectedMethod').value = 'cash';
        document.querySelectorAll('.method-card').forEach(c => c.classList.remove('selected'));
        document.querySelector('.method-card')?.classList.add('selected');
        document.getElementById('liveSummary').style.display = 'none';
        document.getElementById('amountError').textContent = '';
        document.getElementById('walletAmount').classList.remove('has-error');

        closeWalletModal();
        document.getElementById('amountModal').classList.add('active');
        setTimeout(() => document.getElementById('walletAmount').focus(), 200);
    }

    function showUseAdvanceModal(customerId, customerName, balance) {
        if (balance <= 0) {
            showToast('No wallet balance available', 'warning');
            return;
        }

        currentCustomerId = customerId;
        currentCustomerName = customerName;
        currentBalance = parseFloat(balance) || 0;
        isAddMode = false;

        // Set customer header
        document.getElementById('walletModalAvatar').textContent = customerName.charAt(0).toUpperCase();
        document.getElementById('walletModalCustomerName').textContent = customerName;
        document.getElementById('walletModalBalanceInfo').textContent = 'Available Balance: ₹' + currentBalance.toLocaleString('en-IN', {minimumFractionDigits: 2});
        document.getElementById('walletModalHeader').style.background = 'linear-gradient(135deg, #7f1d1d, #991b1b)';

        // Set form
        document.getElementById('walletCustomerId').value = customerId;
        document.getElementById('amountLabel').textContent = 'Amount to Use (₹)';
        document.getElementById('amountPrefix').textContent = '₹';
        document.getElementById('amountPrefix').style.color = 'var(--danger)';
        document.getElementById('paymentSection').style.display = 'none';
        document.getElementById('useAdvanceInfo').style.display = 'block';
        document.getElementById('confirmBtn').className = 'btn-wallet-submit btn-wallet-use-submit';
        document.getElementById('confirmBtnIcon').textContent = '➖';
        document.getElementById('confirmBtnText').textContent = 'Deduct from Wallet';
        document.getElementById('walletForm').action = '{{ route("wallet.use") }}';
        document.getElementById('walletForm').reset();
        document.getElementById('liveSummary').style.display = 'none';
        document.getElementById('amountError').textContent = '';
        document.getElementById('walletAmount').classList.remove('has-error');

        closeWalletModal();
        document.getElementById('amountModal').classList.add('active');
        setTimeout(() => document.getElementById('walletAmount').focus(), 200);
    }

    function closeAmountModal() {
        document.getElementById('amountModal').classList.remove('active');
        document.getElementById('walletForm').reset();
        document.getElementById('liveSummary').style.display = 'none';
        document.getElementById('amountError').textContent = '';
        document.getElementById('walletAmount').classList.remove('has-error');
    }

    function setAmount(amount) {
        document.getElementById('walletAmount').value = amount;
        onAmountChange();
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

    // ========== FORM SUBMISSION ==========
    document.getElementById('walletForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const amount = parseFloat(document.getElementById('walletAmount').value);
        const errorEl = document.getElementById('amountError');
        const amountInput = document.getElementById('walletAmount');

        if (!amount || amount <= 0) {
            amountInput.classList.add('has-error');
            errorEl.textContent = '❌ Please enter a valid amount greater than 0';
            amountInput.focus();
            return;
        }

        if (!isAddMode && amount > currentBalance) {
            amountInput.classList.add('has-error');
            errorEl.textContent = `❌ Insufficient balance. Available: ₹${currentBalance.toFixed(2)}`;
            return;
        }

        // Show loading state on button
        const btn = document.getElementById('confirmBtn');
        const origIcon = document.getElementById('confirmBtnIcon').textContent;
        const origText = document.getElementById('confirmBtnText').textContent;
        btn.disabled = true;
        document.getElementById('confirmBtnIcon').textContent = '⏳';
        document.getElementById('confirmBtnText').textContent = 'Processing...';

        fetch(this.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                customer_id: document.getElementById('walletCustomerId').value,
                amount: amount,
                method: document.getElementById('selectedMethod').value || 'cash',
                reference: document.getElementById('walletReference')?.value || '',
                remarks: document.querySelector('[name="remarks"]')?.value || ''
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || (isAddMode ? '✅ Amount added to wallet!' : '✅ Amount deducted from wallet!'), 'success');
                closeAmountModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(data.message || '❌ Transaction failed', 'error');
                btn.disabled = false;
                document.getElementById('confirmBtnIcon').textContent = origIcon;
                document.getElementById('confirmBtnText').textContent = origText;
            }
        })
        .catch(() => {
            showToast('❌ Network error. Please try again.', 'error');
            btn.disabled = false;
            document.getElementById('confirmBtnIcon').textContent = origIcon;
            document.getElementById('confirmBtnText').textContent = origText;
        });
    });

    // Check for session messages
    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif

    @if(session('error'))
        showToast("{{ session('error') }}", 'error');
    @endif

    @if(session('warning'))
        showToast("{{ session('warning') }}", 'warning');
    @endif
</script>
@endsection