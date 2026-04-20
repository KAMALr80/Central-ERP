@extends('layouts.app')

@section('page-title', 'Inventory Management')

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
        --text-main: #1f2937;
        --text-muted: #6b7280;
        --border: #e5e7eb;
        --bg-light: #f9fafb;
        --bg-white: #ffffff;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --shadow-xl: 0 10px 40px rgba(0, 0, 0, 0.1);
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
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        color: var(--text-main);
        line-height: 1.5;
    }

    /* ================= MAIN CONTAINER ================= */
    .inventory-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: clamp(16px, 3vw, 20px);
        width: 100%;
    }

    .container {
        max-width: 1600px;
        margin: 0 auto;
        width: 100%;
    }

    /* ================= HEADER CARD ================= */
    .header-card {
        background: var(--bg-white);
        border-radius: var(--radius-2xl);
        padding: clamp(20px, 4vw, 30px);
        margin-bottom: 30px;
        box-shadow: var(--shadow-xl);
        border: 1px solid var(--border);
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .header-icon {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        width: clamp(50px, 8vw, 60px);
        height: clamp(50px, 8vw, 60px);
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: clamp(24px, 4vw, 28px);
        color: white;
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        flex-shrink: 0;
    }

    .header-title h1 {
        margin: 0;
        font-size: clamp(24px, 5vw, 32px);
        font-weight: 800;
        color: var(--text-main);
        word-break: break-word;
    }

    .header-title p {
        margin: 10px 0 0 0;
        color: var(--text-muted);
        font-size: clamp(14px, 3vw, 16px);
        word-break: break-word;
    }

    .btn-add {
        background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
        color: white;
        text-decoration: none;
        padding: clamp(12px, 2.5vw, 15px) clamp(20px, 4vw, 30px);
        border-radius: var(--radius-lg);
        font-weight: 600;
        font-size: clamp(14px, 2.5vw, 16px);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.25);
        border: none;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.35);
    }

    /* ================= STATS CARDS ================= */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
        width: 100%;
    }

    .stat-card {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        padding: clamp(20px, 3vw, 25px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border-left: 5px solid;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-lg);
    }

    .stat-card.blue {
        border-left-color: var(--primary);
    }

    .stat-card.green {
        border-left-color: var(--success);
    }

    .stat-card.orange {
        border-left-color: var(--warning);
    }

    .stat-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        margin: 0;
        color: var(--text-muted);
        font-size: clamp(12px, 2.5vw, 14px);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        word-break: break-word;
    }

    .stat-value {
        margin: 10px 0 0 0;
        font-size: clamp(28px, 5vw, 36px);
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.2;
        word-break: break-word;
    }

    .stat-icon {
        width: clamp(50px, 8vw, 60px);
        height: clamp(50px, 8vw, 60px);
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: clamp(20px, 4vw, 24px);
        flex-shrink: 0;
    }

    .stat-icon.blue {
        background: linear-gradient(135deg, #c7d2fe 0%, #a5b4fc 100%);
    }

    .stat-icon.green {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    }

    .stat-icon.orange {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    }

    /* ================= FILTERS SECTION ================= */
    .filters-card {
        background: var(--bg-white);
        border-radius: var(--radius-2xl);
        padding: clamp(20px, 4vw, 25px);
        margin-bottom: 30px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--border);
    }

    .filters-wrapper {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
    }

    .filters-left {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .filter-select {
        padding: clamp(10px, 2vw, 12px) clamp(14px, 3vw, 18px);
        border: 2px solid var(--border);
        border-radius: var(--radius-lg);
        font-size: clamp(13px, 2.5vw, 14px);
        color: var(--text-main);
        background: var(--bg-light);
        cursor: pointer;
        outline: none;
        min-width: 160px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .filter-select:focus {
        border-color: var(--primary);
        background: white;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .search-box {
        position: relative;
        min-width: 250px;
    }

    .search-input {
        width: 100%;
        padding: clamp(10px, 2vw, 12px) 45px clamp(10px, 2vw, 12px) 18px;
        border: 2px solid var(--border);
        border-radius: var(--radius-lg);
        font-size: clamp(13px, 2.5vw, 14px);
        color: var(--text-main);
        background: var(--bg-light);
        transition: all 0.3s ease;
        outline: none;
        font-weight: 500;
    }

    .search-input:focus {
        border-color: var(--primary);
        background: white;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .search-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 16px;
        pointer-events: none;
    }

    /* ================= TABLE CARD ================= */
    .table-card {
        background: var(--bg-white);
        border-radius: var(--radius-2xl);
        overflow: hidden;
        box-shadow: var(--shadow-xl);
        border: 1px solid var(--border);
        width: 100%;
    }

    .table-header {
        padding: clamp(16px, 3vw, 20px) clamp(20px, 4vw, 30px);
        border-bottom: 1px solid var(--border);
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .table-title {
        margin: 0;
        font-size: clamp(18px, 3.5vw, 20px);
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .table-title span {
        background: var(--primary);
        width: 8px;
        height: 30px;
        border-radius: 4px;
        display: inline-block;
    }

    .entries-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .entries-label {
        font-size: clamp(12px, 2.5vw, 14px);
        color: #4b5563;
        font-weight: 500;
    }

    .entries-select {
        padding: 8px 12px;
        border: 2px solid var(--border);
        border-radius: var(--radius-md);
        font-size: clamp(13px, 2.5vw, 14px);
        color: var(--text-main);
        background: white;
        cursor: pointer;
        outline: none;
        font-weight: 500;
        min-width: 80px;
        transition: all 0.3s ease;
    }

    .entries-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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
        min-width: 1200px;
    }

    .data-table thead th {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 2px solid var(--border);
        padding: clamp(12px, 2.5vw, 15px);
        font-weight: 700;
        color: #4b5563;
        font-size: clamp(12px, 2vw, 13px);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        cursor: pointer;
        white-space: nowrap;
    }

    .data-table tbody td {
        padding: clamp(12px, 2.5vw, 15px);
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
        font-size: clamp(13px, 2.2vw, 14px);
        white-space: nowrap;
    }

    .data-table tbody tr:hover {
        background-color: #f9fafb !important;
    }

    /* ================= CHECKBOX ================= */
    .checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    /* ================= IMAGE STYLES ================= */
    .image-container {
        position: relative;
    }

    .product-image {
        width: clamp(40px, 6vw, 50px);
        height: clamp(40px, 6vw, 50px);
        object-fit: cover;
        border-radius: var(--radius-md);
        border: 2px solid var(--border);
        transition: transform 0.3s;
        cursor: pointer;
    }

    .product-image:hover {
        transform: scale(1.1);
    }

    .image-placeholder {
        width: clamp(40px, 6vw, 50px);
        height: clamp(40px, 6vw, 50px);
        background: var(--bg-light);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: clamp(20px, 3vw, 24px);
        color: #9ca3af;
        border: 2px dashed var(--border);
    }

    .external-url-badge {
        position: absolute;
        bottom: -2px;
        right: -2px;
        background: var(--purple);
        color: white;
        border-radius: 50%;
        width: 16px;
        height: 16px;
        font-size: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
    }

    /* ================= BADGES ================= */
    .product-code {
        font-weight: 600;
        color: #374151;
        font-size: clamp(12px, 2.2vw, 14px);
    }

    .product-name {
        font-weight: 700;
        color: var(--text-main);
        font-size: clamp(13px, 2.3vw, 15px);
        margin-bottom: 4px;
    }

    .product-desc {
        font-size: clamp(11px, 2vw, 12px);
        color: var(--text-muted);
        line-height: 1.4;
        max-width: 200px;
        word-break: break-word;
    }

    .stock-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #d1fae5;
        color: var(--success-dark);
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: clamp(12px, 2.2vw, 14px);
        min-width: 60px;
        text-align: center;
    }

    .stock-badge.low {
        background: #fee2e2;
        color: var(--danger-dark);
    }

    .category-badge {
        display: inline-flex;
        align-items: center;
        background: #e0e7ff;
        color: #3730a3;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: clamp(11px, 2.2vw, 13px);
        white-space: nowrap;
    }

    .price {
        font-weight: 700;
        color: var(--text-main);
        font-size: clamp(13px, 2.3vw, 15px);
    }

    /* ================= ACTION BUTTONS ================= */
    .action-group {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: clamp(32px, 5vw, 36px);
        height: clamp(32px, 5vw, 36px);
        border-radius: var(--radius-md);
        text-decoration: none;
        font-size: clamp(14px, 2.5vw, 16px);
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn-view {
        background: #f0f9ff;
        color: #0369a1;
    }

    .btn-view:hover {
        background: #e0f2fe;
        transform: translateY(-1px);
    }

    .btn-edit {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .btn-edit:hover {
        background: #bfdbfe;
        transform: translateY(-1px);
    }

    .btn-delete {
        background: #fee2e2;
        color: #dc2626;
    }

    .btn-delete:hover {
        background: #fecaca;
        transform: translateY(-1px);
    }

    /* ================= FOOTER ================= */
    .table-footer {
        padding: clamp(20px, 4vw, 25px) clamp(24px, 5vw, 30px);
        border-top: 1px solid var(--border);
        background: #f9fafb;
        text-align: right;
    }

    .btn-barcode {
        background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
        color: white;
        padding: clamp(12px, 2.5vw, 14px) clamp(24px, 4vw, 30px);
        border-radius: var(--radius-lg);
        font-weight: 600;
        border: none;
        cursor: pointer;
        font-size: clamp(14px, 2.5vw, 15px);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.25);
    }

    .btn-barcode:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.35);
    }

    /* ================= EMPTY STATE ================= */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-muted);
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        background: var(--bg-light);
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        color: #9ca3af;
        margin: 0 auto 16px;
    }

    .empty-title {
        margin: 0 0 8px 0;
        color: #374151;
        font-weight: 600;
        font-size: 18px;
    }

    .empty-text {
        margin: 0;
        color: var(--text-muted);
        font-size: 14px;
    }

    /* ================= MODAL ================= */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.8);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-image {
        max-width: 90%;
        max-height: 90%;
        border-radius: var(--radius-lg);
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    }

    /* ================= DATATABLE CUSTOM STYLES ================= */
    .dataTables_wrapper {
        padding: 0 20px;
    }

    .dataTables_info {
        padding: 15px 0;
        color: var(--text-muted);
        font-size: 14px;
    }

    .dataTables_paginate {
        padding: 15px 0;
    }

    .dataTables_paginate .paginate_button {
        padding: 8px 12px;
        margin: 0 2px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        color: #4b5563;
        font-weight: 600;
        background: white;
        cursor: pointer;
        display: inline-block;
    }

    .dataTables_paginate .paginate_button.current {
        background: var(--primary);
        color: white !important;
        border-color: var(--primary);
    }

    .dataTables_paginate .paginate_button:hover {
        background: var(--bg-light);
        color: var(--text-main) !important;
        border-color: #d1d5db;
    }

    .dataTables_paginate .paginate_button.disabled {
        background: var(--bg-light);
        color: #9ca3af !important;
        cursor: not-allowed;
    }

    .dt-buttons {
        margin-bottom: 10px;
    }

    .dt-buttons button {
        background: var(--primary) !important;
        color: white !important;
        border: none !important;
        border-radius: var(--radius-md) !important;
        padding: 8px 16px !important;
        margin: 5px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        transition: all 0.3s !important;
    }

    .dt-buttons button:hover {
        background: var(--primary-dark) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(67, 56, 202, 0.3);
    }

    .dt-buttons button.btn-excel {
        background: var(--success) !important;
    }

    .dt-buttons button.btn-pdf {
        background: var(--danger) !important;
    }

    .dt-buttons button.btn-print {
        background: var(--warning) !important;
    }

    /* ================= RESPONSIVE BREAKPOINTS ================= */
    
    /* Large Desktop (1200px and above) */
    @media (min-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* Desktop (992px to 1199px) */
    @media (max-width: 1199px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Tablet (768px to 991px) */
    @media (max-width: 991px) {
        .inventory-page {
            padding: 15px;
        }

        .filters-wrapper {
            flex-direction: column;
            align-items: flex-start;
        }

        .filters-left {
            width: 100%;
        }

        .filter-select {
            width: 100%;
        }

        .search-box {
            width: 100%;
        }

        .table-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .entries-wrapper {
            width: 100%;
        }

        .entries-select {
            flex: 1;
        }
    }

    /* Mobile Landscape (576px to 767px) */
    @media (max-width: 767px) {
        .inventory-page {
            padding: 10px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .stat-card {
            padding: 18px;
        }

        .stat-value {
            font-size: 28px;
        }

        .action-group {
            flex-direction: column;
        }

        .action-btn {
            width: 100%;
        }

        .data-table {
            min-width: 1100px;
        }

        .table-footer {
            text-align: center;
        }

        .btn-barcode {
            width: 100%;
            justify-content: center;
        }

        .dt-buttons {
            display: flex;
            flex-direction: column;
        }

        .dt-buttons button {
            width: 100%;
            margin: 5px 0 !important;
        }
    }

    /* Mobile Portrait (up to 575px) */
    @media (max-width: 575px) {
        .inventory-page {
            padding: 8px;
        }

        .header-title h1 {
            font-size: 22px;
        }

        .header-title p {
            font-size: 13px;
        }

        .stat-card {
            padding: 16px;
        }

        .stat-label {
            font-size: 12px;
        }

        .stat-value {
            font-size: 24px;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            font-size: 20px;
        }

        .filter-select,
        .search-input {
            font-size: 13px;
            padding: 10px;
        }

        .data-table {
            min-width: 1000px;
        }

        .data-table th,
        .data-table td {
            padding: 10px;
            font-size: 12px;
        }

        .product-name {
            font-size: 13px;
        }

        .stock-badge,
        .category-badge {
            padding: 4px 8px;
            font-size: 11px;
        }

        .action-btn {
            width: 30px;
            height: 30px;
            font-size: 13px;
        }

        .empty-icon {
            width: 60px;
            height: 60px;
            font-size: 24px;
        }

        .empty-title {
            font-size: 16px;
        }

        .empty-text {
            font-size: 13px;
        }
    }

    /* Extra Small Devices (up to 360px) */
    @media (max-width: 360px) {
        .inventory-page {
            padding: 5px;
        }

        .header-icon {
            width: 40px;
            height: 40px;
            font-size: 20px;
        }

        .header-title h1 {
            font-size: 20px;
        }

        .stat-value {
            font-size: 22px;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            font-size: 18px;
        }

        .data-table {
            min-width: 900px;
        }

        .data-table th,
        .data-table td {
            padding: 8px;
            font-size: 11px;
        }

        .product-name {
            font-size: 12px;
        }

        .stock-badge,
        .category-badge {
            padding: 3px 6px;
            font-size: 10px;
        }

        .action-btn {
            width: 28px;
            height: 28px;
            font-size: 12px;
        }
    }

    /* Print Styles */
    @media print {
        .btn-add,
        .filter-select,
        .search-box,
        .entries-wrapper,
        .action-group,
        .btn-barcode,
        .dt-buttons,
        .dataTables_paginate {
            display: none !important;
        }

        .stat-card {
            border: 1px solid #000;
            break-inside: avoid;
        }

        .stat-icon {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .stock-badge,
        .category-badge {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<div class="inventory-page">
    <div class="container">
        <!-- Header Card -->
        <div class="header-card">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">📦</div>
                    <div class="header-title">
                        <h1>Inventory Management</h1>
                        <p>Manage your products, track stock levels, and monitor inventory</p>
                    </div>
                </div>

                @if (auth()->user()->hasPermission('create_inventory'))
                    <a href="{{ route('inventory.create') }}" class="btn-add">
                        <span style="font-size: 20px;">+</span>
                        Add New Product
                    </a>
                @endif
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-content">
                    <div class="stat-info">
                        <p class="stat-label">Total Products</p>
                        <h3 class="stat-value">{{ $products->count() }}</h3>
                    </div>
                    <div class="stat-icon blue">📊</div>
                </div>
            </div>

            <div class="stat-card green">
                <div class="stat-content">
                    <div class="stat-info">
                        <p class="stat-label">Low Stock</p>
                        <h3 class="stat-value">{{ $lowStockCount }}</h3>
                    </div>
                    <div class="stat-icon green">⚠️</div>
                </div>
            </div>

            <div class="stat-card orange">
                <div class="stat-content">
                    <div class="stat-info">
                        <p class="stat-label">Total Value</p>
                        <h3 class="stat-value">₹ {{ number_format($totalValue, 2) }}</h3>
                    </div>
                    <div class="stat-icon orange">💰</div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="filters-card">
            <div class="filters-wrapper">
                <!-- Left side: Filters -->
                <div class="filters-left">
                    <!-- Category Filter -->
                    <select id="categoryFilter" class="filter-select">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>

                    <!-- Stock Filter -->
                    <select id="stockFilter" class="filter-select">
                        <option value="">All Stock</option>
                        <option value="low">Low Stock (≤10)</option>
                    </select>
                </div>

                <!-- Right side: Search Box -->
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search products..." class="search-input">
                    <span class="search-icon">🔍</span>
                </div>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="table-card">
            <!-- Table Header -->
            <div class="table-header">
                <h3 class="table-title">
                    <span></span>
                    Product Inventory
                </h3>

                <!-- Entries per page selector -->
                <div class="entries-wrapper">
                    <span class="entries-label">Show:</span>
                    <select id="entriesPerPage" class="entries-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="-1">All</option>
                    </select>
                    <span class="entries-label">entries</span>
                </div>
            </div>

            <!-- Table Container -->
            <div class="table-responsive">
                <table id="inventoryTable" class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 30px;">
                                <input type="checkbox" id="selectAll" class="checkbox">
                            </th>
                            <th style="width: 70px;">Image</th>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $__col = $products; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $p)
                            <tr>
                                <!-- Checkbox -->
                                <td>
                                    <input type="checkbox" class="checkbox product-checkbox" value="{{ $p->product_code }}">
                                </td>

                                <!-- Image Column -->
                                <td>
                                    @if($p->image)
                                        @php
                                            $imageUrl = filter_var($p->image, FILTER_VALIDATE_URL)
                                                ? $p->image
                                                : asset('storage/'.$p->image);
                                        @endphp
                                        <div class="image-container">
                                            <img src="{{ $imageUrl }}"
                                                 alt="{{ $p->name }}"
                                                 class="product-image"
                                                 onclick="openImageModal(this.src)">
                                            @if(filter_var($p->image, FILTER_VALIDATE_URL))
                                                <span class="external-url-badge" title="External URL">🔗</span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="image-placeholder">
                                            📦
                                        </div>
                                    @endif
                                </td>

                                <!-- Product Code -->
                                <td>
                                    <div class="product-code">
                                        {{ $p->product_code }}
                                    </div>
                                </td>

                                <!-- Product Name -->
                                <td>
                                    <div class="product-name">{{ $p->name }}</div>
                                    @if ($p->description)
                                        <div class="product-desc">
                                            {{ \Illuminate\Support\Str::limit($p->description, 40) }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Quantity -->
                                <td>
                                    <div class="stock-badge {{ $p->quantity <= 10 ? 'low' : '' }}">
                                        {{ $p->quantity }}
                                        @if ($p->quantity <= 10)
                                            <span style="margin-left: 5px; font-size: 12px;">⚠️</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Price -->
                                <td>
                                    <div class="price">₹ {{ number_format($p->price, 2) }}</div>
                                </td>

                                <!-- Category -->
                                <td>
                                    <div class="category-badge">
                                        {{ $p->category ?? 'Uncategorized' }}
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td>
                                    <div class="action-group">
                                        @if (auth()->user()->hasPermission('view_inventory'))
                                            <a href="{{ route('inventory.show', $p->id) }}" class="action-btn btn-view" title="View Details">
                                                👁️
                                            </a>
                                        @endif

                                        @if (auth()->user()->hasPermission('edit_inventory'))
                                            <a href="{{ route('inventory.edit', $p->id) }}" class="action-btn btn-edit" title="Edit">
                                                ✏️
                                            </a>
                                        @endif

                                        @if (auth()->user()->hasPermission('delete_inventory'))
                                            <form method="POST" action="{{ route('inventory.destroy', $p->id) }}"
                                                style="display: inline; margin: 0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    onclick="return confirm('Are you sure you want to delete this product?')"
                                                    class="action-btn btn-delete"
                                                    title="Delete">
                                                    🗑️
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
@else
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <div class="empty-icon">📦</div>
                                        <h4 class="empty-title">No Products Found</h4>
                                        <p class="empty-text">Try adjusting your search or add a new product</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @if (auth()->user()->hasPermission('generate_barcodes') && $products->count() > 0)
                <div class="table-footer">
                    <button type="button" onclick="submitBarcodeForm()" class="btn-barcode">
                        <span style="font-size: 18px;">🧾</span>
                        Generate Barcode PDF
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Hidden form for barcode -->
<form id="barcodeForm" method="POST" action="{{ route('inventory.barcode.preview') }}" target="_blank">
    @csrf
    <input type="hidden" name="product_ids" id="productIdsInput">
</form>

<!-- Image Preview Modal -->
<div id="imageModal" class="modal-overlay" onclick="closeImageModal()">
    <img id="modalImage" src="" alt="Full size image" class="modal-image">
</div>

@endsection

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.colVis.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#inventoryTable').DataTable({
                dom: 'Bfrtipl',
                buttons: [
                    @if(auth()->user()->hasPermission('export_inventory'))
                        {
                            extend: 'excel',
                            text: '📊 Excel',
                            className: 'btn-excel',
                            exportOptions: {
                                columns: [2, 3, 4, 5, 6]
                            }
                        },
                        {
                            extend: 'pdf',
                            text: '📄 PDF',
                            className: 'btn-pdf',
                            exportOptions: {
                                columns: [2, 3, 4, 5, 6]
                            }
                        },
                        {
                            extend: 'print',
                            text: '🖨️ Print',
                            className: 'btn-print',
                            exportOptions: {
                                columns: [2, 3, 4, 5, 6]
                            }
                        }
                    @endif
                ],
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                order: [
                    [2, 'asc']
                ],
                language: {
                    search: "",
                    searchPlaceholder: "Search in table...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                },
                columnDefs: [{
                        targets: 0,
                        orderable: false,
                        searchable: false
                    },
                    {
                        targets: 1,
                        orderable: false,
                        searchable: false
                    },
                    {
                        targets: 7,
                        orderable: false,
                        searchable: false
                    }
                ],
                initComplete: function() {
                    // Style DataTable elements
                    $('.dataTables_filter input').css({
                        'border': '2px solid #e5e7eb',
                        'border-radius': '8px',
                        'padding': '8px 12px',
                        'font-size': '14px'
                    });

                    $('.dataTables_length select').css({
                        'border': '2px solid #e5e7eb',
                        'border-radius': '8px',
                        'padding': '6px 10px',
                        'font-size': '14px'
                    });

                    // Hide default search box and length menu
                    $('.dataTables_filter').hide();
                    $('.dataTables_length').hide();

                    // Set initial value for entries per page dropdown
                    $('#entriesPerPage').val('10');
                }
            });

            // Custom Search Box
            $('#searchInput').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Category Filter
            $('#categoryFilter').on('change', function() {
                var category = $(this).val();
                table.column(6).search(category).draw();
            });

            // Stock Filter
            $('#stockFilter').on('change', function() {
                var stockFilter = $(this).val();

                // Remove previous filter if exists
                $.fn.dataTable.ext.search = [];

                if (stockFilter !== '') {
                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        var quantityCell = data[4];
                        var quantityMatch = quantityCell.match(/\d+/);
                        var quantity = quantityMatch ? parseInt(quantityMatch[0]) : 0;

                        if (stockFilter === 'low' && quantity <= 10) {
                            return true;
                        }
                        return false;
                    });
                }

                table.draw();
            });

            // Entries per page selector
            $('#entriesPerPage').on('change', function() {
                var selectedValue = $(this).val();

                if (selectedValue === '-1') {
                    table.page.len(-1).draw();
                } else {
                    var pageLength = parseInt(selectedValue);
                    table.page.len(pageLength).draw();
                }
            });

            // Update entries dropdown when DataTable changes page length
            table.on('length.dt', function(e, settings, len) {
                $('#entriesPerPage').val(len === -1 ? '-1' : len.toString());
            });

            // Select All Checkbox
            $('#selectAll').on('change', function() {
                $('.product-checkbox').prop('checked', this.checked);
            });

            // Individual checkbox handling
            $(document).on('change', '.product-checkbox', function() {
                var allChecked = $('.product-checkbox:checked').length === $('.product-checkbox').length;
                $('#selectAll').prop('checked', allChecked);
            });

            // Handle window resize for responsive adjustments
            $(window).on('resize', function() {
                if ($.fn.DataTable.isDataTable('#inventoryTable')) {
                    table.columns.adjust();
                }
            });
        });

        // Image Modal Functions
        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.remove('active');
            document.body.style.overflow = '';
        }

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === "Escape") {
                closeImageModal();
            }
        });

        // Barcode Form Submission
        function submitBarcodeForm() {
            // Get selected checkboxes
            var selectedCodes = [];

            $('.product-checkbox:checked').each(function() {
                var code = $(this).val();
                if (code && code.trim() !== '') {
                    selectedCodes.push(code.trim());
                }
            });

            // Check if any product selected
            if (selectedCodes.length === 0) {
                alert('⚠️ Please select at least one product');
                return false;
            }

            // Create comma-separated string
            var productCodesString = selectedCodes.join(',');

            // Set value in hidden input
            $('#productIdsInput').val(productCodesString);

            // Show loading message
            alert('🔄 Opening barcode preview for ' + selectedCodes.length +
                ' product(s)...\nPreview will open in new tab.');

            // Submit form (will open in new tab)
            $('#barcodeForm').submit();

            // Reset checkboxes
            setTimeout(function() {
                $('.product-checkbox').prop('checked', false);
                $('#selectAll').prop('checked', false);
            }, 1000);

            return true;
        }
    </script>
@endpush