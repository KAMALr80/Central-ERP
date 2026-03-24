@extends('layouts.app')

@section('page-title', 'Employee Management')

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
        --bg-light: #f9fafb;
        --bg-white: #ffffff;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --shadow-xl: 0 20px 60px rgba(102, 126, 234, 0.3);
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
        background: #f3f4f6;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        color: var(--text-main);
        line-height: 1.5;
    }

    /* ================= MAIN CONTAINER ================= */
    .employee-page {
        max-width: 1600px;
        margin: 0 auto;
        padding: 20px;
        width: 100%;
        min-height: 100vh;
    }

    /* ================= HEADER SECTION ================= */
    .header-section {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        border-radius: var(--radius-2xl);
        padding: clamp(24px, 5vw, 32px);
        margin-bottom: 32px;
        box-shadow: var(--shadow-xl);
        position: relative;
        overflow: hidden;
        width: 100%;
    }

    .header-pattern {
        position: absolute;
        top: 0;
        right: 0;
        width: 300px;
        height: 100%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="white" opacity="0.1"><path d="M0,0 L100,0 L100,100 Z" /></svg>');
        pointer-events: none;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        z-index: 2;
        flex-wrap: wrap;
        gap: 20px;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: clamp(16px, 4vw, 20px);
        flex-wrap: wrap;
    }

    .header-icon {
        width: clamp(60px, 10vw, 70px);
        height: clamp(60px, 10vw, 70px);
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    }

    .header-icon i {
        font-size: clamp(24px, 5vw, 32px);
        color: white;
    }

    .header-title h1 {
        font-size: clamp(24px, 6vw, 36px);
        font-weight: 800;
        color: white;
        margin: 0;
        letter-spacing: -0.5px;
        line-height: 1.2;
        word-break: break-word;
    }

    .header-title p {
        color: rgba(255, 255, 255, 0.9);
        font-size: clamp(14px, 3vw, 18px);
        margin: 8px 0 0 0;
        font-weight: 400;
        word-break: break-word;
    }

    .btn-add {
        display: inline-flex;
        align-items: center;
        gap: clamp(8px, 2vw, 12px);
        background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
        color: white;
        border: none;
        padding: clamp(14px, 3vw, 16px) clamp(20px, 4vw, 28px);
        border-radius: var(--radius-lg);
        text-decoration: none;
        font-weight: 700;
        font-size: clamp(14px, 3vw, 16px);
        box-shadow: 0 8px 32px rgba(16, 185, 129, 0.4);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        white-space: nowrap;
    }

    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 40px rgba(16, 185, 129, 0.6);
    }

    .btn-add span:first-child {
        font-size: clamp(16px, 4vw, 20px);
    }

    .btn-add-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
        pointer-events: none;
    }

    /* ================= SUCCESS MESSAGE ================= */
    .success-message {
        margin-top: 24px;
        background: rgba(34, 197, 94, 0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(34, 197, 94, 0.3);
        border-radius: var(--radius-lg);
        padding: clamp(14px, 3vw, 16px) clamp(16px, 4vw, 20px);
        display: flex;
        align-items: center;
        gap: clamp(10px, 2vw, 12px);
        animation: slideDown 0.3s ease;
        flex-wrap: wrap;
    }

    .success-icon {
        width: clamp(35px, 6vw, 40px);
        height: clamp(35px, 6vw, 40px);
        background: rgba(34, 197, 94, 0.3);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .success-icon i {
        font-size: clamp(16px, 4vw, 20px);
        color: #22c55e;
    }

    .success-content {
        flex: 1;
        min-width: 200px;
    }

    .success-title {
        color: white;
        font-weight: 600;
        font-size: clamp(14px, 3vw, 16px);
    }

    .success-text {
        color: rgba(255, 255, 255, 0.9);
        font-size: clamp(12px, 2.5vw, 14px);
        margin-top: 2px;
        word-break: break-word;
    }

    .success-close {
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.7);
        font-size: clamp(18px, 4vw, 20px);
        cursor: pointer;
        padding: 0;
        width: clamp(25px, 5vw, 30px);
        height: clamp(25px, 5vw, 30px);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .success-close:hover {
        background-color: rgba(255,255,255,0.1);
        color: white;
    }

    /* ================= CARD ================= */
    .card {
        background: var(--bg-white);
        border-radius: var(--radius-2xl);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--border);
        overflow: hidden;
        width: 100%;
    }

    /* ================= CARD HEADER ================= */
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: clamp(16px, 3vw, 20px) clamp(20px, 4vw, 24px);
        border-bottom: 1px solid var(--border);
        background: var(--bg-white);
        flex-wrap: wrap;
        gap: 15px;
    }

    .show-entries {
        display: flex;
        align-items: center;
        gap: clamp(8px, 2vw, 12px);
        flex-wrap: wrap;
    }

    .show-entries span {
        color: #4b5563;
        font-size: clamp(12px, 2.5vw, 14px);
        font-weight: 500;
    }

    .select-wrapper {
        position: relative;
        min-width: 70px;
    }

    .select-input {
        padding: 8px 32px 8px 16px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-md);
        font-size: clamp(12px, 2.5vw, 14px);
        color: #374151;
        background: white;
        cursor: pointer;
        appearance: none;
        width: 100%;
    }

    .select-input:focus {
        outline: none;
        border-color: var(--info);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .select-arrow {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 12px;
        pointer-events: none;
    }

    /* ================= SEARCH BOX ================= */
    .search-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .search-box {
        position: relative;
    }

    .search-input {
        padding: 10px 40px 10px 16px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-md);
        font-size: clamp(12px, 2.5vw, 14px);
        color: #374151;
        width: clamp(200px, 30vw, 240px);
        transition: all 0.3s;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--info);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .search-icon {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 14px;
        pointer-events: none;
    }

    .clear-btn {
        background: #f3f4f6;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-md);
        width: clamp(35px, 6vw, 40px);
        height: clamp(35px, 6vw, 40px);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
    }

    .clear-btn:hover {
        background: #e5e7eb;
    }

    .clear-btn i {
        color: var(--text-muted);
        font-size: clamp(12px, 2.5vw, 14px);
    }

    /* ================= TABLE ================= */
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

    .data-table thead tr {
        background: var(--bg-light);
        border-bottom: 2px solid var(--border);
    }

    .data-table th {
        padding: 16px 12px;
        text-align: left;
        font-size: clamp(12px, 2vw, 13px);
        font-weight: 600;
        color: #374151;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .data-table th.center {
        text-align: center;
    }

    .sortable-header {
        cursor: pointer;
        user-select: none;
    }

    .sortable-header:hover {
        background-color: #f9fafb;
    }

    .sort-icon {
        margin-left: 6px;
        color: #9ca3af;
        font-size: 12px;
    }

    .sort-asc .sort-icon {
        color: var(--info);
    }

    .sort-desc .sort-icon {
        color: var(--info);
    }

    .data-table td {
        padding: 16px 12px;
        border-bottom: 1px solid #f3f4f6;
        font-size: clamp(13px, 2.2vw, 14px);
        transition: background-color 0.2s;
    }

    .data-table tr:hover td {
        background-color: var(--bg-light);
    }

    /* ================= BADGES ================= */
    .emp-code-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(59, 130, 246, 0.1);
        color: var(--info-dark);
        padding: 6px 12px;
        border-radius: 20px;
        font-size: clamp(12px, 2.2vw, 13px);
        font-weight: 600;
        border: 1px solid rgba(59, 130, 246, 0.2);
        white-space: nowrap;
    }

    .emp-code-badge i {
        font-size: clamp(10px, 2vw, 12px);
    }

    .dept-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(6, 182, 212, 0.1);
        color: #0e7490;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: clamp(12px, 2.2vw, 13px);
        font-weight: 600;
        border: 1px solid rgba(6, 182, 212, 0.2);
        white-space: nowrap;
    }

    .dept-badge i {
        font-size: clamp(10px, 2vw, 12px);
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: clamp(12px, 2.2vw, 13px);
        font-weight: 600;
        white-space: nowrap;
    }

    .status-active {
        background: rgba(34, 197, 94, 0.1);
        color: #166534;
        border: 1px solid rgba(34, 197, 94, 0.2);
    }

    .status-inactive {
        background: rgba(156, 163, 175, 0.1);
        color: #4b5563;
        border: 1px solid rgba(156, 163, 175, 0.2);
    }

    .status-badge i {
        font-size: 8px;
    }

    /* ================= ACTION BUTTONS ================= */
    .action-group {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: clamp(35px, 6vw, 40px);
        height: clamp(35px, 6vw, 40px);
        border-radius: var(--radius-md);
        text-decoration: none;
        font-size: clamp(14px, 2.5vw, 16px);
        transition: all 0.3s;
        border: 1.5px solid transparent;
    }

    .action-btn i {
        font-size: clamp(14px, 2.5vw, 16px);
    }

    .btn-view {
        background: rgba(14, 165, 233, 0.1);
        color: #0ea5e9;
        border-color: rgba(14, 165, 233, 0.2);
    }

    .btn-view:hover {
        background: #0ea5e9;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
    }

    .btn-edit {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
        border-color: rgba(245, 158, 11, 0.2);
    }

    .btn-edit:hover {
        background: #f59e0b;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }

    .btn-delete {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        border-color: rgba(239, 68, 68, 0.2);
    }

    .btn-delete:hover {
        background: #ef4444;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .view-only-badge {
        background: rgba(156, 163, 175, 0.1);
        color: var(--text-muted);
        padding: 8px 16px;
        border-radius: 20px;
        font-size: clamp(11px, 2vw, 12px);
        font-weight: 500;
        border: 1px solid rgba(156, 163, 175, 0.2);
        white-space: nowrap;
    }

    .view-only-badge i {
        margin-right: 4px;
    }

    /* ================= EMPTY STATE ================= */
    .empty-state {
        padding: 40px;
        text-align: center;
        color: var(--text-muted);
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        background: var(--bg-light);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
    }

    .empty-icon i {
        font-size: 32px;
        color: #9ca3af;
    }

    .empty-title {
        font-size: clamp(16px, 3vw, 18px);
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }

    .empty-text {
        font-size: clamp(13px, 2.5vw, 14px);
        color: var(--text-muted);
        margin-bottom: 16px;
    }

    .btn-empty {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, var(--info) 0%, var(--info-dark) 100%);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: var(--radius-md);
        text-decoration: none;
        font-weight: 600;
        font-size: clamp(13px, 2.5vw, 14px);
        margin-top: 8px;
    }

    .btn-empty:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
    }

    /* ================= CARD FOOTER ================= */
    .card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: clamp(16px, 3vw, 20px) clamp(20px, 4vw, 24px);
        border-top: 1px solid var(--border);
        background: var(--bg-white);
        flex-wrap: wrap;
        gap: 15px;
    }

    .info-text {
        color: var(--text-muted);
        font-size: clamp(12px, 2.5vw, 14px);
    }

    .info-text span {
        font-weight: 600;
        color: #374151;
    }

    /* ================= PAGINATION ================= */
    .pagination {
        display: flex;
        align-items: center;
        gap: 4px;
        flex-wrap: wrap;
    }

    .pagination-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: clamp(32px, 5vw, 36px);
        height: clamp(32px, 5vw, 36px);
        border: 1.5px solid var(--border);
        border-radius: var(--radius-md);
        color: #374151;
        text-decoration: none;
        font-size: clamp(12px, 2.5vw, 14px);
        transition: all 0.2s;
        background: white;
        padding: 0 8px;
    }

    .pagination-btn:hover {
        background-color: var(--bg-light);
        border-color: #d1d5db;
    }

    .pagination-btn.active {
        background: linear-gradient(135deg, var(--info) 0%, var(--info-dark) 100%);
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .pagination-btn.disabled {
        color: #9ca3af;
        background: #f9fafb;
        border-color: var(--border);
        cursor: not-allowed;
        pointer-events: none;
    }

    .pagination-btn i {
        font-size: clamp(10px, 2vw, 12px);
    }

    .pagination-ellipsis {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: clamp(32px, 5vw, 36px);
        height: clamp(32px, 5vw, 36px);
        color: #9ca3af;
        font-size: clamp(12px, 2.5vw, 14px);
        margin: 0 2px;
    }

    /* ================= ANIMATIONS ================= */
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .table-row {
        animation: fadeIn 0.3s ease forwards;
        opacity: 0;
    }

    /* ================= RESPONSIVE BREAKPOINTS ================= */
    
    /* Large Desktop (1200px and above) */
    @media (min-width: 1200px) {
        .data-table {
            min-width: 1000px;
        }
    }

    /* Desktop (992px to 1199px) */
    @media (max-width: 1199px) {
        .employee-page {
            padding: 15px;
        }
    }

    /* Tablet (768px to 991px) */
    @media (max-width: 991px) {
        .employee-page {
            padding: 12px;
        }

        .header-content {
            flex-direction: column;
            align-items: flex-start;
        }

        .btn-add {
            width: 100%;
            justify-content: center;
        }

        .card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .show-entries {
            width: 100%;
        }

        .select-wrapper {
            flex: 1;
        }

        .search-wrapper {
            width: 100%;
        }

        .search-box {
            flex: 1;
        }

        .search-input {
            width: 100%;
        }

        .card-footer {
            flex-direction: column;
            align-items: flex-start;
        }

        .pagination {
            width: 100%;
            justify-content: center;
        }
    }

    /* Mobile Landscape (576px to 767px) */
    @media (max-width: 767px) {
        .employee-page {
            padding: 10px;
        }

        .header-left {
            width: 100%;
            justify-content: center;
        }

        .header-icon {
            width: 50px;
            height: 50px;
        }

        .header-icon i {
            font-size: 24px;
        }

        .header-title {
            text-align: center;
        }

        .success-message {
            flex-direction: column;
            text-align: center;
        }

        .success-content {
            text-align: center;
        }

        .action-group {
            flex-direction: column;
        }

        .action-btn {
            width: 100%;
        }

        .view-only-badge {
            width: 100%;
            text-align: center;
        }

        .data-table {
            min-width: 900px;
        }

        .pagination-btn {
            min-width: 30px;
            height: 30px;
        }
    }

    /* Mobile Portrait (up to 575px) */
    @media (max-width: 575px) {
        .employee-page {
            padding: 8px;
        }

        .header-title h1 {
            font-size: 22px;
        }

        .header-title p {
            font-size: 13px;
        }

        .btn-add {
            padding: 12px 20px;
            font-size: 13px;
        }

        .show-entries span {
            font-size: 12px;
        }

        .select-input {
            padding: 6px 28px 6px 12px;
            font-size: 12px;
        }

        .search-input {
            padding: 8px 35px 8px 12px;
            font-size: 12px;
        }

        .clear-btn {
            width: 35px;
            height: 35px;
        }

        .data-table {
            min-width: 800px;
        }

        .data-table th,
        .data-table td {
            padding: 12px 8px;
            font-size: 12px;
        }

        .emp-code-badge,
        .dept-badge,
        .status-badge {
            padding: 4px 8px;
            font-size: 11px;
        }

        .info-text {
            font-size: 12px;
        }

        .pagination-btn {
            min-width: 28px;
            height: 28px;
            font-size: 11px;
        }
    }

    /* Extra Small Devices (up to 360px) */
    @media (max-width: 360px) {
        .header-title h1 {
            font-size: 20px;
        }

        .header-icon {
            width: 40px;
            height: 40px;
        }

        .header-icon i {
            font-size: 20px;
        }

        .btn-add {
            padding: 10px 16px;
            font-size: 12px;
        }

        .data-table {
            min-width: 700px;
        }

        .data-table th,
        .data-table td {
            padding: 10px 6px;
            font-size: 11px;
        }

        .emp-code-badge,
        .dept-badge,
        .status-badge {
            padding: 3px 6px;
            font-size: 10px;
        }

        .action-btn {
            width: 30px;
            height: 30px;
            font-size: 12px;
        }

        .pagination-btn {
            min-width: 25px;
            height: 25px;
            font-size: 10px;
        }
    }

    /* Print Styles */
    @media print {
        .btn-add,
        .action-group,
        .search-wrapper,
        .pagination,
        .success-close {
            display: none !important;
        }

        .header-section {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .emp-code-badge,
        .dept-badge,
        .status-badge {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<div class="employee-page">
    <!-- Header Section -->
    <div class="header-section">
        <div class="header-pattern"></div>
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="header-title">
                    <h1>Employee Management</h1>
                    <p>Manage your team members and departments</p>
                </div>
            </div>

            @if (auth()->user()->role === 'admin')
                <a href="{{ route('employees.create') }}" class="btn-add">
                    <span>+</span>
                    <span>Add Employee</span>
                    <div class="btn-add-overlay"></div>
                </a>
            @endif
        </div>

        @if (session('success'))
            <div class="success-message" id="successMessage">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="success-content">
                    <div class="success-title">Success!</div>
                    <div class="success-text">{{ session('success') }}</div>
                </div>
                <button type="button" class="success-close" onclick="document.getElementById('successMessage').style.display='none'">
                    ×
                </button>
            </div>
        @endif
    </div>

    <!-- DataTable Section -->
    <div class="card">
        <!-- Card Header -->
        <div class="card-header">
            <!-- Show entries -->
            <div class="show-entries">
                <span>Show</span>
                <div class="select-wrapper">
                    <select id="entriesPerPage" class="select-input" onchange="handlePerPageChange(this)">
                        <option value="10" {{ request('per_page', 25) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page', 25) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 25) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <span class="select-arrow">▼</span>
                </div>
                <span>entries</span>
            </div>

            <!-- Search box -->
            <div class="search-wrapper">
                <div class="search-box">
                    <input type="text" id="globalSearch" placeholder="Search employees..."
                        value="{{ request('search') }}" class="search-input"
                        onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'"
                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                    <i class="fas fa-search search-icon"></i>
                </div>
                @if (request('search'))
                    <button id="clearSearch" class="clear-btn">
                        <i class="fas fa-times"></i>
                    </button>
                @endif
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table id="employeeDataTable" class="data-table">
                <thead>
                    <tr>
                        <th class="center" style="min-width: 60px;">#</th>
                        <th class="sortable-header" onclick="sortTable(1)">
                            <span>EMPLOYEE CODE</span>
                            <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th class="sortable-header" onclick="sortTable(2)">
                            <span>NAME</span>
                            <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th class="sortable-header" onclick="sortTable(3)">
                            <span>EMAIL</span>
                            <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th class="sortable-header" onclick="sortTable(4)">
                            <span>DEPARTMENT</span>
                            <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th class="center">STATUS</th>
                        <th class="center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @php $__col = $employees; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $index => $emp)
                        <tr class="table-row" style="animation-delay: {{ $index * 50 }}ms;">
                            <td class="center">
                                {{ $loop->iteration + ($employees->currentPage() - 1) * $employees->perPage() }}
                            </td>
                            <td>
                                <span class="emp-code-badge">
                                    <i class="fas fa-id-card"></i>
                                    {{ $emp->employee_code }}
                                </span>
                            </td>
                            <td style="font-weight: 600;">{{ $emp->name }}</td>
                            <td>
                                <a href="mailto:{{ $emp->email }}" class="email-link"
                                   style="display: inline-flex; align-items: center; gap: 8px; color: var(--info); text-decoration: none; font-size: clamp(13px, 2.2vw, 14px); transition: color 0.2s;"
                                   onmouseover="this.style.color='var(--info-dark)'"
                                   onmouseout="this.style.color='var(--info)'">
                                    <i class="fas fa-envelope" style="font-size: clamp(12px, 2vw, 14px);"></i>
                                    {{ $emp->email }}
                                </a>
                            </td>
                            <td>
                                <span class="dept-badge">
                                    <i class="fas fa-building"></i>
                                    {{ $emp->department }}
                                </span>
                            </td>
                            <td class="center">
                                <span class="status-badge {{ $emp->status == 1 ? 'status-active' : 'status-inactive' }}">
                                    <i class="fas fa-circle" style="color: {{ $emp->status == 1 ? '#22c55e' : '#9ca3af' }};"></i>
                                    {{ $emp->status == 1 ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="center">
                                <div class="action-group">
                                    <!-- View Button -->
                                    <a href="{{ route('employees.show', $emp->id) }}" class="action-btn btn-view" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- Edit Button -->
                                    @if (in_array(auth()->user()->role, ['admin', 'hr']))
                                        <a href="{{ route('employees.edit', $emp->id) }}" class="action-btn btn-edit" title="Edit Employee">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    <!-- Delete Button -->
                                    @if (auth()->user()->role === 'admin')
                                        <form action="{{ route('employees.destroy', $emp->id) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="action-btn btn-delete"
                                                onclick="confirmDelete(event, '{{ $emp->name }}')"
                                                title="Delete Employee">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if (auth()->user()->role === 'staff')
                                        <span class="view-only-badge">
                                            <i class="fas fa-eye"></i> View Only
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
@else
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="empty-title">No employees found</div>
                                    <div class="empty-text">Try adding a new employee or adjust your search</div>
                                    @if (auth()->user()->role === 'admin')
                                        <a href="{{ route('employees.create') }}" class="btn-empty">
                                            <i class="fas fa-plus"></i>
                                            Add First Employee
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="card-footer">
            <div class="info-text">
                Showing <span>{{ $employees->firstItem() ?? 0 }}</span>
                to <span>{{ $employees->lastItem() ?? 0 }}</span>
                of <span>{{ $employees->total() }}</span> entries
            </div>

            <!-- Pagination -->
            @if ($employees->hasPages())
                <div class="pagination">
                    <!-- First Page -->
                    @if (!$employees->onFirstPage())
                        <a href="{{ $employees->url(1) }}" class="pagination-btn" title="First Page">
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                    @else
                        <span class="pagination-btn disabled">
                            <i class="fas fa-angle-double-left"></i>
                        </span>
                    @endif

                    <!-- Previous Page -->
                    @if (!$employees->onFirstPage())
                        <a href="{{ $employees->previousPageUrl() }}" class="pagination-btn" title="Previous">
                            <i class="fas fa-angle-left"></i>
                        </a>
                    @else
                        <span class="pagination-btn disabled">
                            <i class="fas fa-angle-left"></i>
                        </span>
                    @endif

                    <!-- Page Numbers -->
                    @php
                        $current = $employees->currentPage();
                        $last = $employees->lastPage();
                        $start = max(1, $current - 2);
                        $end = min($last, $current + 2);
                    @endphp

                    @if ($start > 1)
                        <a href="{{ $employees->url(1) }}" class="pagination-btn">1</a>
                        @if ($start > 2)
                            <span class="pagination-ellipsis">...</span>
                        @endif
                    @endif

                    @for ($i = $start; $i <= $end; $i++)
                        @if ($i == $current)
                            <span class="pagination-btn active">{{ $i }}</span>
                        @else
                            <a href="{{ $employees->url($i) }}" class="pagination-btn">{{ $i }}</a>
                        @endif
                    @endfor

                    @if ($end < $last)
                        @if ($end < $last - 1)
                            <span class="pagination-ellipsis">...</span>
                        @endif
                        <a href="{{ $employees->url($last) }}" class="pagination-btn">{{ $last }}</a>
                    @endif

                    <!-- Next Page -->
                    @if ($employees->hasMorePages())
                        <a href="{{ $employees->nextPageUrl() }}" class="pagination-btn" title="Next">
                            <i class="fas fa-angle-right"></i>
                        </a>
                    @else
                        <span class="pagination-btn disabled">
                            <i class="fas fa-angle-right"></i>
                        </span>
                    @endif

                    <!-- Last Page -->
                    @if ($employees->hasMorePages())
                        <a href="{{ $employees->url($last) }}" class="pagination-btn" title="Last Page">
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    @else
                        <span class="pagination-btn disabled">
                            <i class="fas fa-angle-double-right"></i>
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function handlePerPageChange(select) {
        const perPage = select.value;
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        url.searchParams.delete('page');
        window.location.href = url.toString();
    }

    function confirmDelete(event, employeeName) {
        if (confirm(`Are you sure you want to delete "${employeeName}"? This action cannot be undone.`)) {
            event.target.closest('form').submit();
        }
        event.preventDefault();
    }

    let currentSortColumn = -1;
    let sortDirection = 1; // 1 = asc, -1 = desc

    function sortTable(columnIndex) {
        const headers = document.querySelectorAll('.sortable-header');

        // Reset all sort icons
        headers.forEach(header => {
            header.classList.remove('sort-asc', 'sort-desc');
            const icon = header.querySelector('.sort-icon');
            if (icon) {
                icon.className = 'fas fa-sort sort-icon';
            }
        });

        // Get current header
        const currentHeader = headers[columnIndex];
        const currentIcon = currentHeader.querySelector('.sort-icon');

        // Toggle direction if same column
        if (currentSortColumn === columnIndex) {
            sortDirection *= -1;
        } else {
            currentSortColumn = columnIndex;
            sortDirection = 1;
        }

        // Update icon
        if (sortDirection === 1) {
            currentHeader.classList.add('sort-asc');
            if (currentIcon) {
                currentIcon.className = 'fas fa-sort-up sort-icon';
            }
        } else {
            currentHeader.classList.add('sort-desc');
            if (currentIcon) {
                currentIcon.className = 'fas fa-sort-down sort-icon';
            }
        }

        // Get table data
        const table = document.getElementById('employeeDataTable');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));

        // Sort rows
        rows.sort((a, b) => {
            const aCell = a.cells[columnIndex + 1]; // +1 because first column is index
            const bCell = b.cells[columnIndex + 1];

            let aValue = aCell ? aCell.textContent.trim() : '';
            let bValue = bCell ? bCell.textContent.trim() : '';

            // For numeric sorting
            if (!isNaN(parseFloat(aValue)) && !isNaN(parseFloat(bValue))) {
                aValue = parseFloat(aValue) || 0;
                bValue = parseFloat(bValue) || 0;
            }

            if (aValue < bValue) return -1 * sortDirection;
            if (aValue > bValue) return 1 * sortDirection;
            return 0;
        });

        // Reorder rows
        rows.forEach(row => tbody.appendChild(row));
    }

    // Search functionality
    let searchTimeout;
    document.getElementById('globalSearch')?.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#employeeDataTable tbody tr');

            rows.forEach(row => {
                if (row.children.length === 7) { // Skip empty state row
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                }
            });
        }, 300);
    });

    // Clear search
    document.getElementById('clearSearch')?.addEventListener('click', function() {
        document.getElementById('globalSearch').value = '';
        const url = new URL(window.location.href);
        url.searchParams.delete('search');
        window.location.href = url.toString();
    });

    // Add animation for table rows
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('#employeeDataTable tbody tr');
        rows.forEach((row, index) => {
            if (row.children.length === 7) { // Skip empty state row
                row.style.opacity = '0';
                row.style.transform = 'translateY(10px)';
                setTimeout(() => {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 50);
            }
        });

        // Make sure all icons are visible
        const allIcons = document.querySelectorAll('i');
        allIcons.forEach(icon => {
            icon.style.visibility = 'visible';
            icon.style.opacity = '1';
        });
    });
</script>
@endsection