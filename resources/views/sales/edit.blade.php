@extends('layouts.app')

@section('page-title', 'Edit Invoice #' . $sale->invoice_no)

@section('content')
    @php
        use Illuminate\Support\Str;
    @endphp

    <meta name="csrf-token" content="{{ csrf_token() }}">

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
            --bg-light: #f9fafb;
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
        .invoice-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
            padding: clamp(16px, 3vw, 30px);
            width: 100%;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        /* ================= INVOICE CARD ================= */
        .invoice-card {
            background: var(--bg-white);
            border-radius: var(--radius-2xl);
            padding: clamp(24px, 5vw, 40px);
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--border);
            width: 100%;
        }

        /* ================= HEADER ================= */
        .invoice-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
            flex: 1;
            min-width: 280px;
        }

        .header-icon {
            width: clamp(50px, 8vw, 60px);
            height: clamp(50px, 8vw, 60px);
            background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(245, 158, 11, 0.25);
            flex-shrink: 0;
        }

        .header-icon span {
            font-size: clamp(24px, 4vw, 28px);
            color: white;
        }

        .header-title {
            font-size: clamp(24px, 5vw, 32px);
            font-weight: 800;
            margin: 0;
            color: var(--text-main);
            letter-spacing: -0.5px;
            word-break: break-word;
        }

        .header-subtitle {
            color: var(--text-muted);
            margin: 5px 0 0;
            font-size: clamp(13px, 2.5vw, 15px);
            word-break: break-word;
        }

        /* ================= CLEAR CUSTOMER BUTTON ================= */
        .btn-clear-customer {
            background: #fef2f2;
            color: var(--danger);
            border: 1.5px solid #fecaca;
            padding: 10px 20px;
            border-radius: var(--radius-lg);
            font-weight: 600;
            font-size: clamp(13px, 2.5vw, 14px);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.1);
            white-space: nowrap;
        }

        .btn-clear-customer:hover {
            background: #fee2e2;
            border-color: #fca5a5;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(220, 38, 38, 0.15);
        }

        /* ================= SECTION CARDS ================= */
        .section-card {
            background: white;
            padding: clamp(20px, 4vw, 25px);
            border-radius: var(--radius-lg);
            margin-bottom: 30px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            width: 100%;
        }

        .section-title {
            font-size: clamp(16px, 3vw, 18px);
            font-weight: 700;
            color: #374151;
            margin: 0 0 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            word-break: break-word;
        }

        .step-badge {
            display: inline-flex;
            width: 24px;
            height: 24px;
            color: white;
            border-radius: var(--radius-sm);
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .step-1 {
            background: var(--primary);
        }

        .step-2 {
            background: var(--warning);
        }

        .step-3 {
            background: var(--success);
        }

        /* ================= FORM ELEMENTS ================= */
        .form-group {
            position: relative;
        }

        .form-label {
            display: block;
            color: #374151;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: clamp(13px, 2.5vw, 14px);
            word-break: break-word;
        }

        .required-star {
            color: var(--danger);
            font-weight: bold;
        }

        .status-text {
            font-size: 12px;
            margin-left: 8px;
            font-weight: normal;
            word-break: break-word;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            border: 1.5px solid #d1d5db;
            background: white;
            font-size: clamp(14px, 2.5vw, 15px);
            color: #374151;
            transition: all 0.2s;
            outline: none;
        }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-input-disabled {
            background: #f3f4f6;
            color: #9ca3af;
            cursor: not-allowed;
        }

        .form-textarea {
            width: 100%;
            padding: 14px 16px;
            border-radius: var(--radius-lg);
            border: 1.5px solid var(--border);
            background: white;
            font-size: clamp(14px, 2.5vw, 15px);
            color: #374151;
            outline: none;
            resize: vertical;
            transition: all 0.2s;
            font-family: inherit;
        }

        .form-textarea:focus {
            border-color: var(--success);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        /* ================= GRID LAYOUTS ================= */
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        /* ================= CUSTOMER SEARCH ================= */
        .customer-search-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .search-wrapper {
            position: relative;
            flex: 1;
            min-width: 250px;
        }

        .search-results {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            width: 100%;
            background: white;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            max-height: 350px;
            overflow-y: auto;
            z-index: 1000;
        }

        .search-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 18px;
            pointer-events: none;
        }

        .hint-text {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 6px;
            word-break: break-word;
        }

        /* ================= BUTTONS ================= */
        .btn-add-customer {
            background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
            color: white;
            border: none;
            padding: 0 24px;
            border-radius: var(--radius-lg);
            font-weight: 600;
            font-size: clamp(13px, 2.5vw, 14px);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
            transition: all 0.2s;
        }

        .btn-add-customer:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3);
        }

        .btn-submit {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            border: none;
            padding: clamp(12px, 2.5vw, 14px) clamp(30px, 5vw, 40px);
            border-radius: var(--radius-lg);
            font-weight: 700;
            font-size: clamp(14px, 2.5vw, 16px);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.25);
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-cancel {
            background: #f3f4f6;
            color: #374151;
            border: 1.5px solid var(--border);
            padding: clamp(10px, 2vw, 12px) clamp(20px, 4vw, 30px);
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: clamp(13px, 2.5vw, 15px);
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-cancel:hover {
            background: #e5e7eb;
        }

        .btn-icon {
            background: rgba(255, 255, 255, 0.2);
            width: 36px;
            height: 36px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .btn-remove-item {
            background: #fef2f2;
            color: var(--danger);
            border: 1.5px solid #fecaca;
            padding: 8px 16px;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: clamp(12px, 2.2vw, 13px);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .btn-remove-item:hover {
            background: #fee2e2;
            border-color: #fca5a5;
        }

        /* ================= SELECTED CUSTOMER CARD ================= */
        .selected-customer-card {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1.5px solid var(--info);
            border-radius: var(--radius-lg);
            padding: 16px 20px;
            margin-bottom: 20px;
            animation: fadeIn 0.3s ease-out;
        }

        .selected-customer-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }

        .customer-avatar {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .customer-avatar span {
            font-size: 22px;
            color: white;
        }

        .customer-details {
            flex: 1;
            min-width: 200px;
        }

        .customer-label {
            font-size: 12px;
            color: #0c4a6e;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .customer-name {
            font-weight: 700;
            color: var(--text-main);
            font-size: 16px;
            word-break: break-word;
        }

        .customer-contact {
            display: flex;
            gap: 10px;
            font-size: 13px;
            color: #475569;
            flex-wrap: wrap;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* ================= TABLE STYLES ================= */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        .invoice-table th {
            padding: 16px 20px;
            text-align: left;
            font-weight: 700;
            font-size: clamp(12px, 2vw, 14px);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #374151;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-bottom: 2px solid #dbeafe;
            white-space: nowrap;
        }

        .invoice-table td {
            padding: 20px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .empty-state td {
            background: #f8fafc;
            text-align: center;
            color: var(--text-muted);
            font-style: italic;
        }

        .empty-state-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            color: #94a3b8;
            padding: 60px 20px;
        }

        .empty-icon {
            font-size: 48px;
        }

        /* ================= PRODUCT IMAGES ================= */
        .product-image-sm {
            width: clamp(25px, 4vw, 30px);
            height: clamp(25px, 4vw, 30px);
            border-radius: var(--radius-sm);
            object-fit: cover;
            border: 1px solid var(--border);
            background: #f8fafc;
        }

        .product-image-placeholder {
            width: clamp(25px, 4vw, 30px);
            height: clamp(25px, 4vw, 30px);
            border-radius: var(--radius-sm);
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 12px;
            flex-shrink: 0;
        }

        /* ================= PRODUCT INPUTS ================= */
        .item-price,
        .item-total,
        .qty {
            width: 100%;
            padding: 10px 12px;
            border-radius: var(--radius-md);
            border: 1.5px solid var(--border);
            background: white;
            font-size: clamp(13px, 2.2vw, 15px);
            color: #374151;
            text-align: right;
            outline: none;
            font-weight: 600;
            transition: all 0.2s;
        }

        .item-price:focus,
        .qty:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .qty {
            text-align: center;
        }

        .item-total {
            background: #f1f5f9;
            color: #1e293b;
            font-weight: 700;
        }

        /* ================= TOTALS SECTION ================= */
        .totals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .total-item {
            position: relative;
        }

        .total-label {
            display: block;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 8px;
            font-size: clamp(12px, 2.5vw, 14px);
            word-break: break-word;
        }

        .grand-total-label {
            color: var(--text-main);
            font-weight: 700;
        }

        .input-prefix {
            position: relative;
        }

        .prefix {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #374151;
            font-weight: 600;
            font-size: clamp(14px, 2.5vw, 18px);
            pointer-events: none;
        }

        .grand-prefix {
            color: var(--text-main);
            font-weight: 700;
        }

        .total-input {
            width: 100%;
            padding: 14px 16px 14px 42px;
            border-radius: var(--radius-lg);
            border: 1.5px solid var(--border);
            background: #f8fafc;
            font-size: clamp(16px, 3vw, 18px);
            font-weight: 700;
            color: #374151;
            text-align: right;
            outline: none;
        }

        .total-input-editable {
            width: 100%;
            padding: 14px 16px 14px 42px;
            border-radius: var(--radius-lg);
            border: 1.5px solid var(--border);
            background: white;
            font-size: clamp(14px, 2.5vw, 16px);
            color: #374151;
            text-align: right;
            outline: none;
            transition: all 0.2s;
        }

        .total-input-editable:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .grand-total-input {
            width: 100%;
            padding: 14px 16px 14px 42px;
            border-radius: var(--radius-lg);
            border: 2px solid #1e293b;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            font-size: clamp(18px, 3.5vw, 20px);
            font-weight: 800;
            color: #1e293b;
            text-align: right;
            outline: none;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.15);
        }

        /* ================= FORM ACTIONS ================= */
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        /* ================= MODAL ================= */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-content {
            background: white;
            width: 100%;
            max-width: 500px;
            padding: clamp(20px, 4vw, 30px);
            border-radius: var(--radius-2xl);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: modalSlideIn 0.3s ease-out;
            border: 1px solid var(--border);
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .modal-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .modal-icon span {
            font-size: 24px;
            color: white;
        }

        .modal-title {
            font-size: clamp(20px, 4vw, 22px);
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
            letter-spacing: -0.5px;
            word-break: break-word;
        }

        .modal-subtitle {
            color: var(--text-muted);
            margin: 4px 0 0;
            font-size: clamp(12px, 2.5vw, 14px);
            word-break: break-word;
        }

        .modal-body {
            display: grid;
            gap: 16px;
        }

        .modal-footer {
            display: flex;
            gap: 12px;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid var(--border);
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .btn-save-customer {
            background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: clamp(13px, 2.5vw, 15px);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
            transition: all 0.2s;
        }

        .btn-save-customer:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3);
        }

        .btn-save-customer:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* ================= Barcode Scanner Input ================= */
        .barcode-scanner-input {
            position: absolute;
            opacity: 0;
            height: 0;
            width: 0;
            pointer-events: none;
        }

        /* ================= TOAST NOTIFICATIONS ================= */
        .toast-notification {
            position: fixed;
            top: 30px;
            right: 30px;
            padding: 16px 24px;
            border-radius: var(--radius-lg);
            font-weight: 600;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 300px;
            max-width: 400px;
            animation: slideInRight 0.3s ease-out, fadeOut 0.3s ease-out 2.7s forwards;
        }

        .toast-success {
            background: var(--success);
        }

        .toast-error {
            background: var(--danger);
        }

        .toast-warning {
            background: var(--warning);
        }

        .toast-info {
            background: var(--primary);
        }

        .toast-icon {
            font-size: 20px;
        }

        .toast-message {
            flex: 1;
            color: white;
            word-break: break-word;
        }

        /* ================= ANIMATIONS ================= */
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
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

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(10px);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes buttonSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .shake {
            animation: shake 0.5s ease-in-out;
        }

        .spin {
            animation: spin 1s linear infinite;
        }

        /* ================= RESPONSIVE BREAKPOINTS ================= */
        
        /* Large Desktop (1200px and above) */
        @media (min-width: 1200px) {
            .totals-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        /* Desktop (992px to 1199px) */
        @media (max-width: 1199px) {
            .invoice-card {
                padding: 30px;
            }
        }

        /* Tablet (768px to 991px) */
        @media (max-width: 991px) {
            .invoice-page {
                padding: 20px;
            }

            .header-title {
                font-size: 28px;
            }

            .customer-search-group {
                flex-direction: column;
            }

            .btn-add-customer {
                padding: 12px;
                justify-content: center;
            }

            .totals-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Mobile Landscape (576px to 767px) */
        @media (max-width: 767px) {
            .invoice-page {
                padding: 15px;
            }

            .invoice-card {
                padding: 20px;
            }

            .header-left {
                flex-direction: column;
                text-align: center;
            }

            .header-icon {
                margin: 0 auto;
            }

            .selected-customer-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .customer-contact {
                flex-direction: column;
                width: 100%;
            }

            .totals-grid {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn-submit,
            .btn-cancel {
                width: 100%;
                justify-content: center;
            }

            .modal-footer {
                flex-direction: column;
            }

            .btn-cancel,
            .btn-save-customer {
                width: 100%;
                justify-content: center;
            }

            .invoice-table {
                min-width: 700px;
            }

            .invoice-table th,
            .invoice-table td {
                padding: 15px 12px;
            }
        }

        /* Mobile Portrait (up to 575px) */
        @media (max-width: 575px) {
            .invoice-page {
                padding: 12px;
            }

            .invoice-card {
                padding: 16px;
            }

            .header-title {
                font-size: 24px;
            }

            .header-subtitle {
                font-size: 13px;
            }

            .section-title {
                font-size: 16px;
            }

            .form-input,
            .form-textarea {
                padding: 10px 14px;
                font-size: 13px;
            }

            .customer-avatar {
                width: 40px;
                height: 40px;
            }

            .customer-avatar span {
                font-size: 20px;
            }

            .customer-name {
                font-size: 15px;
            }

            .customer-contact {
                font-size: 12px;
            }

            .btn-remove-item {
                padding: 6px 12px;
                font-size: 11px;
            }

            .item-price,
            .item-total,
            .qty {
                padding: 8px 10px;
                font-size: 12px;
            }

            .total-input,
            .total-input-editable,
            .grand-total-input {
                padding: 12px 12px 12px 35px;
                font-size: 14px;
            }

            .prefix {
                left: 12px;
                font-size: 14px;
            }

            .toast-notification {
                left: 15px;
                right: 15px;
                min-width: auto;
                max-width: none;
                top: 20px;
            }
        }

        /* Extra Small Devices (up to 360px) */
        @media (max-width: 360px) {
            .invoice-card {
                padding: 12px;
            }

            .header-title {
                font-size: 22px;
            }

            .header-icon {
                width: 45px;
                height: 45px;
            }

            .header-icon span {
                font-size: 22px;
            }

            .section-card {
                padding: 15px;
            }

            .form-input,
            .form-textarea {
                padding: 8px 12px;
                font-size: 12px;
            }

            .btn-add-customer {
                padding: 10px;
                font-size: 12px;
            }

            .customer-avatar {
                width: 35px;
                height: 35px;
            }

            .customer-avatar span {
                font-size: 18px;
            }

            .customer-name {
                font-size: 14px;
            }

            .invoice-table {
                min-width: 600px;
            }

            .invoice-table th,
            .invoice-table td {
                padding: 12px 8px;
                font-size: 11px;
            }

            .btn-remove-item {
                padding: 4px 8px;
                font-size: 10px;
            }

            .item-price,
            .item-total,
            .qty {
                padding: 6px 8px;
                font-size: 11px;
            }

            .btn-submit {
                padding: 12px 24px;
                font-size: 13px;
            }

            .btn-icon {
                width: 30px;
                height: 30px;
                font-size: 14px;
            }
        }

        /* Print Styles */
        @media print {
            .btn-add-customer,
            .btn-clear-customer,
            .btn-remove-item,
            .btn-submit,
            .btn-cancel,
            .modal,
            .search-results,
            .toast-notification,
            .barcode-scanner-input {
                display: none !important;
            }

            .invoice-card {
                box-shadow: none;
                border: 1px solid #000;
            }

            .selected-customer-card {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .grand-total-input {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>

    <div class="invoice-page">
        <div class="container">
            <div class="invoice-card">
                {{-- INVOICE HEADER --}}
                <div class="invoice-header">
                    <div class="header-left">
                        <div class="header-icon">
                            <span>✏️</span>
                        </div>
                        <div>
                            <h1 class="header-title">Edit Invoice #{{ $sale->invoice_no }}</h1>
                            <p class="header-subtitle">Update invoice details below</p>
                        </div>
                    </div>

                    {{-- CLEAR CUSTOMER BUTTON --}}
                    <div id="clearCustomerContainer" style="{{ $sale->customer_id ? 'display:block;' : 'display:none;' }}">
                        <button type="button" onclick="InvoiceManager.clearCustomerSelection()" class="btn-clear-customer">
                            <span>✕</span>
                            Clear Customer
                        </button>
                    </div>
                </div>

                <form method="POST" action="{{ route('sales.update', $sale->id) }}" id="invoiceForm"
                    onsubmit="return InvoiceManager.handleSubmit(event)">
                    @csrf
                    @method('PUT')

                    {{-- Hidden Barcode Scanner Input --}}
                    <input type="text" id="barcodeInput" autocomplete="off" class="barcode-scanner-input">

                    {{-- Invoice Token --}}
                    <input type="hidden" name="invoice_token" value="{{ $sale->invoice_token ?? Str::uuid() }}">

                    {{-- CUSTOMER SECTION --}}
                    <div class="section-card">
                        <h3 class="section-title">
                            <span class="step-badge step-1">1</span>
                            Customer Information
                        </h3>

                        {{-- SELECTED CUSTOMER INFO DISPLAY --}}
                        <div id="selectedCustomerInfo" class="selected-customer-card" style="{{ $sale->customer_id ? 'display:block;' : 'display:none;' }}">
                            <div class="selected-customer-content">
                                <div class="customer-avatar">
                                    <span>👤</span>
                                </div>
                                <div class="customer-details">
                                    <div class="customer-label">CURRENT CUSTOMER</div>
                                    <div id="selectedCustomerName" class="customer-name">{{ $sale->customer->name ?? '' }}</div>
                                </div>
                                <div class="customer-contact">
                                    <div id="selectedCustomerMobile" class="contact-item">
                                        <span>📱</span>
                                        <span id="selectedCustomerMobileText">{{ $sale->customer->mobile ?? 'N/A' }}</span>
                                    </div>
                                    <div id="selectedCustomerEmail" class="contact-item">
                                        <span>✉️</span>
                                        <span id="selectedCustomerEmailText">{{ $sale->customer->email ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid-2">
                            {{-- Customer Selection --}}
                            <div class="form-group">
                                <label class="form-label">
                                    Change Customer
                                    <span id="customerStatus" class="status-text"></span>
                                </label>
                                <div class="customer-search-group">
                                    <div class="search-wrapper">
                                        <input type="text" id="customerSearch"
                                            placeholder="Type customer name or mobile to change customer..." autocomplete="off"
                                            class="form-input" value="{{ $sale->customer->name ?? '' }}">
                                        <input type="hidden" name="customer_id" id="customer_id" value="{{ $sale->customer_id }}">
                                        <div id="customerResults" class="search-results"></div>
                                    </div>
                                    <button type="button" onclick="InvoiceManager.openCustomerModal()" class="btn-add-customer">
                                        <span>+</span>
                                        Add New
                                    </button>
                                </div>
                            </div>

                            {{-- Product Search --}}
                            <div class="form-group">
                                <label class="form-label">
                                    Add More Products
                                    <span id="productStatus" class="status-text"></span>
                                </label>
                                <div class="search-wrapper">
                                    <input type="text" id="productSearch"
                                        placeholder="Type product name to add more..." class="form-input">
                                    <span class="search-icon">🔍</span>
                                    <div id="productResults" class="search-results"></div>
                                </div>
                                <div class="hint-text">
                                    <span id="productSearchHint">Search products to add to this invoice</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ITEMS TABLE SECTION --}}
                    <div class="section-card">
                        <h3 class="section-title">
                            <span class="step-badge step-2">2</span>
                            Invoice Items
                            <span id="itemsStatus" class="status-text"></span>
                        </h3>

                        <div class="table-responsive">
                            <table class="invoice-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price (₹)</th>
                                        <th>Quantity</th>
                                        <th>Total (₹)</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTable">
                                    @php $__col = $sale->items; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $item)
                                        <tr data-pid="{{ $item->product_id }}" id="product-row-{{ $item->product_id }}">
                                            <td style="padding: 20px;">
                                                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                                    @php
                                                        $imageUrl = $item->product->image ?
                                                            (str_starts_with($item->product->image, 'http') ?
                                                                $item->product->image :
                                                                Storage::url($item->product->image)) : null;
                                                    @endphp

                                                    @if($imageUrl)
                                                        <img src="{{ $imageUrl }}" alt="{{ $item->product->name }}"
                                                            class="product-image-sm"
                                                            onerror="this.onerror=null; this.src=''; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <div class="product-image-placeholder" style="display: none;">
                                                            {{ $item->product->name[0] ?? 'P' }}
                                                        </div>
                                                    @else
                                                        <div class="product-image-placeholder">
                                                            {{ $item->product->name[0] ?? 'P' }}
                                                        </div>
                                                    @endif

                                                    <div>
                                                        <div style="font-weight: 600; color: #374151;">{{ $item->product->name }}</div>
                                                        <div style="font-size: 12px; color: #64748b;">
                                                            Code: {{ $item->product->product_code ?? 'N/A' }} |
                                                            Stock: <span class="stock-display">{{ $item->product->quantity }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="items[product_id][]" value="{{ $item->product_id }}">
                                            </td>
                                            <td style="padding: 20px;">
                                                <input name="items[price][]" value="{{ $item->price }}"
                                                    oninput="InvoiceManager.calculate()"
                                                    class="item-price">
                                            </td>
                                            <td style="padding: 20px;">
                                                <input type="number" class="qty" name="items[quantity][]" value="{{ $item->quantity }}"
                                                    min="1" max="{{ $item->product->quantity + $item->quantity }}"
                                                    oninput="InvoiceManager.calculate()"
                                                    data-original-stock="{{ $item->product->quantity }}"
                                                    data-current-qty="{{ $item->quantity }}">
                                            </td>
                                            <td style="padding: 20px;">
                                                <input name="items[total][]" readonly value="{{ $item->total }}"
                                                    class="item-total">
                                            </td>
                                            <td style="padding: 20px;">
                                                <button type="button" onclick="InvoiceManager.removeProduct('product-row-{{ $item->product_id }}')"
                                                    class="btn-remove-item">
                                                    <span>🗑️</span>
                                                    Remove
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
@else
                                        <tr id="emptyState" class="empty-state">
                                            <td colspan="5">
                                                <div class="empty-state-content">
                                                    <span class="empty-icon">📦</span>
                                                    <p>No products in this invoice. Search and add products above.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TOTALS SECTION --}}
                    <div class="section-card">
                        <h3 class="section-title">
                            <span class="step-badge step-3">3</span>
                            Invoice Summary
                        </h3>

                        <div class="totals-grid">
                            <div class="total-item">
                                <label class="total-label">Sub Total</label>
                                <div class="input-prefix">
                                    <span class="prefix">₹</span>
                                    <input id="sub_total" name="sub_total" readonly value="{{ $sale->sub_total }}"
                                        class="total-input">
                                </div>
                            </div>
                            <div class="total-item">
                                <label class="total-label">Discount (₹)</label>
                                <div class="input-prefix">
                                    <span class="prefix">₹</span>
                                    <input id="discount" name="discount" value="{{ $sale->discount }}"
                                        oninput="InvoiceManager.calculate()" class="total-input-editable">
                                </div>
                            </div>
                            <div class="total-item">
                                <label class="total-label">Tax (%)</label>
                                <div class="input-prefix">
                                    <span class="prefix">%</span>
                                    <input id="tax" name="tax" value="{{ $sale->tax }}"
                                        oninput="InvoiceManager.calculate()" class="total-input-editable">
                                </div>
                            </div>
                            <div class="total-item">
                                <label class="total-label grand-total-label">Grand Total</label>
                                <div class="input-prefix">
                                    <span class="prefix grand-prefix">₹</span>
                                    <input id="grand_total" name="grand_total" readonly value="{{ $sale->grand_total }}"
                                        class="grand-total-input">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- FORM ACTIONS --}}
                    <div class="form-actions">
                        <a href="{{ route('sales.show', $sale->id) }}" class="btn-cancel">
                            Cancel
                        </a>
                        <button type="submit" id="updateBtn" class="btn-submit">
                            <span class="btn-icon">💾</span>
                            Update Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- CUSTOMER MODAL --}}
    <div id="customerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-icon">
                    <span>👤</span>
                </div>
                <div>
                    <h3 class="modal-title">Add New Customer</h3>
                    <p class="modal-subtitle">Fill customer details below</p>
                </div>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Full Name <span class="required-star">*</span></label>
                    <input id="c_name" placeholder="Enter customer name" class="form-input">
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Mobile <span class="required-star">*</span></label>
                        <input id="c_mobile" placeholder="Enter mobile number" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input id="c_email" placeholder="Enter email address" class="form-input">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea id="c_address" placeholder="Enter customer address" rows="3" class="form-textarea"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button onclick="InvoiceManager.closeCustomerModal()" class="btn-cancel">Cancel</button>
                <button onclick="InvoiceManager.saveCustomer()" id="saveCustomerBtn" class="btn-save-customer">
                    <span>✓</span>
                    Save Customer
                </button>
            </div>
        </div>
    </div>

    <script>
        /**
         * Invoice Manager - Edit mode
         * Handles customer selection, product management, barcode scanning, and invoice update
         */
        const InvoiceManager = (function() {
            // ========== STATE MANAGEMENT ==========
            let state = {
                products: @json($products),
                isCustomerSelected: {{ $sale->customer_id ? 'true' : 'false' }},
                isSavingCustomer: false,
                isScannerEnabled: false,
                barcodeBuffer: '',
                barcodeTimeout: null,
                customerTimer: null,
                isEditMode: true
            };

            // ========== DOM ELEMENTS ==========
            const elements = {
                // Customer elements
                customerSearch: document.getElementById('customerSearch'),
                customerResults: document.getElementById('customerResults'),
                customerIdInput: document.getElementById('customer_id'),
                customerStatus: document.getElementById('customerStatus'),
                selectedCustomerInfo: document.getElementById('selectedCustomerInfo'),
                selectedCustomerName: document.getElementById('selectedCustomerName'),
                selectedCustomerMobileText: document.getElementById('selectedCustomerMobileText'),
                selectedCustomerEmailText: document.getElementById('selectedCustomerEmailText'),
                clearCustomerContainer: document.getElementById('clearCustomerContainer'),

                // Product elements
                productSearch: document.getElementById('productSearch'),
                productResults: document.getElementById('productResults'),
                productStatus: document.getElementById('productStatus'),
                productSearchHint: document.getElementById('productSearchHint'),

                // Table elements
                itemsTable: document.getElementById('itemsTable'),
                emptyState: document.getElementById('emptyState'),
                itemsStatus: document.getElementById('itemsStatus'),

                // Barcode elements
                barcodeInput: document.getElementById('barcodeInput'),

                // Modal elements
                customerModal: document.getElementById('customerModal'),
                saveCustomerBtn: document.getElementById('saveCustomerBtn')
            };

            // ========== HELPER FUNCTIONS ==========
            function getProductImageUrl(product) {
                if (!product.image) return null;
                if (product.image.startsWith('http://') || product.image.startsWith('https://')) {
                    return product.image;
                }
                return `/storage/${product.image}`;
            }

            function escapeHTML(str) {
                if (!str) return '';
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            // ========== INITIALIZATION ==========
            function init() {
                attachEventListeners();
                updateUIState();

                // Enable barcode scanner if customer is selected
                if (state.isCustomerSelected) {
                    setTimeout(() => enableBarcodeScanner(), 500);
                }

                // Initial calculation
                calculate();
            }

            // ========== EVENT LISTENERS ==========
            function attachEventListeners() {
                // Customer search
                if (elements.customerSearch) {
                    elements.customerSearch.addEventListener('input', handleCustomerSearch);
                    elements.customerSearch.addEventListener('focus', disableBarcodeScanner);
                    elements.customerSearch.addEventListener('blur', handleCustomerBlur);
                }

                // Product search
                if (elements.productSearch) {
                    elements.productSearch.addEventListener('input', handleProductSearch);
                    elements.productSearch.addEventListener('focus', disableBarcodeScanner);
                    elements.productSearch.addEventListener('blur', handleProductBlur);
                }

                // Barcode scanner
                if (elements.barcodeInput) {
                    elements.barcodeInput.addEventListener('input', handleBarcodeInput);
                    elements.barcodeInput.addEventListener('keydown', handleBarcodeKeydown);
                }

                // Document click (close search results)
                document.addEventListener('click', handleDocumentClick);
                document.addEventListener('mousedown', handleDocumentMousedown);
            }

            // ========== CUSTOMER FUNCTIONS ==========
            function handleCustomerSearch() {
                const query = this.value.trim();

                clearTimeout(state.customerTimer);

                if (query.length < 2) {
                    elements.customerResults.style.display = 'none';
                    return;
                }

                elements.customerResults.innerHTML = getSearchLoadingHTML();
                elements.customerResults.style.display = 'block';

                state.customerTimer = setTimeout(() => {
                    performCustomerSearch(query);
                }, 500);
            }

            function performCustomerSearch(query) {
                fetch(`{{ route('customers.ajax.search') }}?search=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(customers => {
                        elements.customerResults.innerHTML = '';

                        if (customers.length === 0) {
                            elements.customerResults.innerHTML = getNoCustomersHTML();
                            elements.customerResults.style.display = 'block';
                            return;
                        }

                        customers.forEach((customer, index) => {
                            const customerElement = createCustomerElement(customer, index, customers.length);
                            elements.customerResults.appendChild(customerElement);
                        });

                        elements.customerResults.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        elements.customerResults.innerHTML = getSearchErrorHTML();
                        elements.customerResults.style.display = 'block';
                    });
            }

            function createCustomerElement(customer, index, total) {
                const item = document.createElement('div');
                item.style.cssText = `
                    padding: 14px 16px;
                    cursor: pointer;
                    border-bottom: ${index === total - 1 ? 'none' : '1px solid #f1f5f9'};
                    transition: all 0.2s;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                `;

                item.innerHTML = `
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: #374151; margin-bottom: 4px; font-size: 15px;">
                            ${escapeHTML(customer.name)}
                        </div>
                        <div style="display: flex; gap: 12px; font-size: 13px; color: #64748b;">
                            <span>📱 ${escapeHTML(customer.mobile || 'No phone')}</span>
                            ${customer.email ? `<span>✉️ ${escapeHTML(customer.email)}</span>` : ''}
                        </div>
                    </div>
                    <div style="
                        background: #3b82f6;
                        color: white;
                        padding: 6px 12px;
                        border-radius: 8px;
                        font-weight: 600;
                        font-size: 13px;
                        white-space: nowrap;
                    ">
                        Select
                    </div>
                `;

                item.onmouseover = () => item.style.background = '#f8fafc';
                item.onmouseout = () => item.style.background = 'white';
                item.onclick = () => selectCustomer(customer);

                return item;
            }

            function selectCustomer(customer) {
                // Update hidden input
                if (elements.customerIdInput) elements.customerIdInput.value = customer.id;
                if (elements.customerSearch) {
                    elements.customerSearch.value = customer.name;
                    elements.customerSearch.style.borderColor = '#10b981';
                }

                // Update selected customer display
                if (elements.selectedCustomerName) elements.selectedCustomerName.textContent = customer.name;
                if (elements.selectedCustomerMobileText) elements.selectedCustomerMobileText.textContent = customer.mobile || 'Not provided';
                if (elements.selectedCustomerEmailText) elements.selectedCustomerEmailText.textContent = customer.email || 'Not provided';

                // Show customer info and clear button
                if (elements.selectedCustomerInfo) elements.selectedCustomerInfo.style.display = 'block';
                if (elements.clearCustomerContainer) elements.clearCustomerContainer.style.display = 'block';

                // Hide search results
                if (elements.customerResults) elements.customerResults.style.display = 'none';

                // Update state
                state.isCustomerSelected = true;

                // Enable barcode scanner
                enableBarcodeScanner();

                // Show success toast
                showToast(`Customer changed to "${customer.name}"`, 'success');

                // Update UI state
                updateUIState();
            }

            function clearCustomerSelection() {
                if (!confirm('Are you sure you want to remove this customer? This invoice needs a customer.')) {
                    return;
                }

                // Clear customer data
                if (elements.customerSearch) {
                    elements.customerSearch.value = '';
                    elements.customerSearch.style.borderColor = '#d1d5db';
                }
                if (elements.customerIdInput) elements.customerIdInput.value = '';

                // Update state
                state.isCustomerSelected = false;

                // Hide customer info and clear button
                if (elements.selectedCustomerInfo) elements.selectedCustomerInfo.style.display = 'none';
                if (elements.clearCustomerContainer) elements.clearCustomerContainer.style.display = 'none';

                // Disable barcode scanner
                disableBarcodeScanner();

                // Focus back on customer search
                if (elements.customerSearch) elements.customerSearch.focus();

                // Update customer status
                if (elements.customerStatus) {
                    elements.customerStatus.textContent = 'Required';
                    elements.customerStatus.style.color = '#dc2626';
                }

                // Update UI state
                updateUIState();

                showToast('Customer removed. Please select a customer.', 'warning');
            }

            function handleCustomerBlur() {
                setTimeout(() => {
                    if (state.isCustomerSelected && !isInputFieldActive()) {
                        enableBarcodeScanner();
                    }
                }, 200);
            }

            // ========== PRODUCT FUNCTIONS ==========
            function handleProductSearch() {
                const val = this.value.toLowerCase().trim();
                if (elements.productResults) elements.productResults.innerHTML = '';

                if (!val) {
                    if (elements.productResults) elements.productResults.style.display = 'none';
                    return;
                }

                const filteredProducts = state.products.filter(p =>
                    (p.name && p.name.toLowerCase().includes(val)) ||
                    (p.product_code && p.product_code.toString().toLowerCase().includes(val))
                );

                const exactMatch = state.products.find(
                    p => p.product_code && p.product_code.toString().toLowerCase() === val
                );

                if (exactMatch) {
                    addProduct(exactMatch);
                    if (elements.productResults) elements.productResults.style.display = 'none';
                    if (elements.productSearch) elements.productSearch.value = '';
                    showToast(`Product added: ${exactMatch.name}`, 'success');
                    return;
                }

                if (filteredProducts.length === 0) {
                    if (elements.productResults) {
                        elements.productResults.innerHTML = `
                            <div style="padding: 20px; text-align: center; color: #94a3b8; font-style: italic;">
                                No products found matching "${escapeHTML(val)}"
                            </div>
                        `;
                        elements.productResults.style.display = 'block';
                    }
                    return;
                }

                filteredProducts.forEach((p, index) => {
                    if (!elements.productResults) return;

                    const item = document.createElement('div');
                    item.style.cssText = `
                        padding: 14px 16px;
                        cursor: pointer;
                        border-bottom: ${index === filteredProducts.length - 1 ? 'none' : '1px solid #f1f5f9'};
                        transition: all 0.2s;
                    `;

                    const imageUrl = getProductImageUrl(p);
                    const stockWarning = p.quantity <= 0 ? ' (Out of Stock)' : '';

                    item.innerHTML = `
                        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            ${imageUrl ?
                                `<img src="${imageUrl}" alt="${escapeHTML(p.name)}" class="product-image-sm" onerror="this.onerror=null; this.src=''; this.style.display='none'; this.nextElementSibling.style.display='flex';">` :
                                ''
                            }
                            <div class="product-image-placeholder" style="${imageUrl ? 'display: none;' : 'display: flex;'}">
                                ${escapeHTML(p.name?.charAt(0) || 'P')}
                            </div>
                            <div style="flex: 1; min-width: 150px;">
                                <div style="font-weight: 600; color: #374151; margin-bottom: 4px;">
                                    ${escapeHTML(p.name)} ${p.quantity <= 0 ? '<span style="color: #dc2626; font-size: 11px;">(Out of Stock)</span>' : ''}
                                </div>
                                <div style="font-size: 12px; color: #64748b;">
                                    Code: ${escapeHTML(p.product_code || 'N/A')} | Stock: ${p.quantity}
                                </div>
                            </div>
                            <div style="
                                background: ${p.quantity > 0 ? '#10b981' : '#9ca3af'};
                                color: white;
                                padding: 6px 12px;
                                border-radius: 8px;
                                font-weight: 700;
                                font-size: 14px;
                                white-space: nowrap;
                            ">
                                ₹${parseFloat(p.price || 0).toFixed(2)}
                            </div>
                        </div>
                    `;

                    item.onmouseover = () => item.style.background = '#f8fafc';
                    item.onmouseout = () => item.style.background = 'white';

                    if (p.quantity > 0) {
                        item.onclick = () => addProduct(p);
                        item.style.cursor = 'pointer';
                    } else {
                        item.style.opacity = '0.6';
                        item.style.cursor = 'not-allowed';
                    }

                    elements.productResults.appendChild(item);
                });

                if (elements.productResults) elements.productResults.style.display = 'block';
            }

            function getProductQuantityInInvoice(productId) {
                let total = 0;
                document.querySelectorAll('#itemsTable tr[data-pid="' + productId + '"] .qty').forEach(input => {
                    total += parseInt(input.value) || 0;
                });
                return total;
            }

            function addProduct(p) {
                // Check stock
                const currentQty = getProductQuantityInInvoice(p.id);
                if (p.quantity <= currentQty) {
                    showToast(`Insufficient stock! Only ${p.quantity} available.`, 'error');
                    return;
                }

                if (elements.productResults) elements.productResults.style.display = 'none';
                if (elements.productSearch) elements.productSearch.value = '';
                if (elements.emptyState) elements.emptyState.style.display = 'none';

                // Check if product already exists
                let existingRow = null;
                if (elements.itemsTable) {
                    document.querySelectorAll('#itemsTable tr[data-pid]').forEach(row => {
                        if (row.dataset.pid == p.id) {
                            existingRow = row;
                        }
                    });
                }

                if (existingRow) {
                    // Increment quantity
                    const qtyInput = existingRow.querySelector('.qty');
                    if (qtyInput) {
                        const newQty = parseInt(qtyInput.value || 0) + 1;
                        const maxQty = parseInt(qtyInput.getAttribute('max')) || 0;
                        
                        if (newQty <= maxQty) {
                            qtyInput.value = newQty;
                            existingRow.style.background = '#f0f9ff';
                            setTimeout(() => existingRow.style.background = '', 300);
                            calculate();
                            showToast(`Quantity updated for ${p.name}`, 'success');
                        } else {
                            showToast(`Maximum stock limit reached!`, 'error');
                        }
                    }
                } else {
                    // Add new row
                    const rowId = `product-row-${p.id}`;
                    if (elements.itemsTable) {
                        elements.itemsTable.insertAdjacentHTML('beforeend', getProductRowHTML(p, rowId));

                        setTimeout(() => {
                            const newRow = document.getElementById(rowId);
                            if (newRow) newRow.style.background = '';
                        }, 300);
                        
                        calculate();
                        showToast(`Product added: ${p.name}`, 'success');
                    }
                }

                updateUIState();
            }

            function getProductRowHTML(p, rowId) {
                const imageUrl = getProductImageUrl(p);
                const maxStock = p.quantity;

                return `
                    <tr data-pid="${p.id}" id="${rowId}" style="
                        border-bottom: 1px solid #e5e7eb;
                        animation: slideIn 0.3s ease-out;
                        background: #f8fafc;
                    ">
                        <td style="padding: 20px;">
                            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                ${imageUrl ?
                                    `<img src="${imageUrl}" alt="${escapeHTML(p.name)}" class="product-image-sm" onerror="this.onerror=null; this.src=''; this.style.display='none'; this.nextElementSibling.style.display='flex';">` :
                                    ''
                                }
                                <div class="product-image-placeholder" style="${imageUrl ? 'display: none; width: 30px; height: 30px;' : 'display: flex; width: 30px; height: 30px;'}">
                                    ${escapeHTML(p.name?.charAt(0) || 'P')}
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #374151;">${escapeHTML(p.name || 'Product')}</div>
                                    <div style="font-size: 12px; color: #64748b;">
                                        Code: ${escapeHTML(p.product_code || 'N/A')} |
                                        Stock: ${maxStock}
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="items[product_id][]" value="${escapeHTML(p.id)}">
                        </td>
                        <td style="padding: 20px;">
                            <input name="items[price][]" value="${parseFloat(p.price || 0).toFixed(2)}"
                                oninput="InvoiceManager.calculate()"
                                class="item-price">
                        </td>
                        <td style="padding: 20px;">
                            <input type="number" class="qty" name="items[quantity][]" value="1"
                                min="1" max="${maxStock}"
                                oninput="InvoiceManager.calculate()"
                                data-original-stock="${maxStock}">
                        </td>
                        <td style="padding: 20px;">
                            <input name="items[total][]" readonly value="${parseFloat(p.price || 0).toFixed(2)}"
                                class="item-total">
                        </td>
                        <td style="padding: 20px;">
                            <button type="button" onclick="InvoiceManager.removeProduct('${rowId}')"
                                class="btn-remove-item">
                                <span>🗑️</span>
                                Remove
                            </button>
                        </td>
                    </tr>
                `;
            }

            function removeProduct(rowId) {
                const row = document.getElementById(rowId);
                if (row) {
                    const productName = row.querySelector('td:first-child div div:first-child')?.textContent || 'Product';
                    
                    row.style.animation = 'slideOut 0.3s ease-out';
                    setTimeout(() => {
                        row.remove();
                        if (elements.itemsTable && elements.itemsTable.children.length === 0) {
                            if (elements.emptyState) elements.emptyState.style.display = '';
                        }
                        calculate();
                        updateUIState();
                        showToast(`${productName} removed`, 'info');
                    }, 300);
                }
            }

            function handleProductBlur() {
                setTimeout(() => {
                    if (state.isCustomerSelected && !isInputFieldActive()) {
                        enableBarcodeScanner();
                    }
                }, 200);
            }

            // ========== BARCODE SCANNER FUNCTIONS ==========
            function enableBarcodeScanner() {
                state.isScannerEnabled = true;
                if (elements.barcodeInput) {
                    elements.barcodeInput.disabled = false;
                    elements.barcodeInput.value = '';
                }
                state.barcodeBuffer = '';

                if (!isInputFieldActive()) {
                    setTimeout(() => {
                        if (elements.barcodeInput) elements.barcodeInput.focus();
                    }, 100);
                }
            }

            function disableBarcodeScanner() {
                state.isScannerEnabled = false;
                if (elements.barcodeInput) {
                    elements.barcodeInput.disabled = true;
                    elements.barcodeInput.value = '';
                }
                state.barcodeBuffer = '';

                if (state.barcodeTimeout) {
                    clearTimeout(state.barcodeTimeout);
                    state.barcodeTimeout = null;
                }
            }

            function handleBarcodeInput(e) {
                if (!state.isScannerEnabled || !state.isCustomerSelected) return;

                state.barcodeBuffer += e.target.value;
                e.target.value = '';

                if (state.barcodeTimeout) {
                    clearTimeout(state.barcodeTimeout);
                }

                state.barcodeTimeout = setTimeout(() => {
                    if (state.barcodeBuffer.length > 0) {
                        processBarcode(state.barcodeBuffer.trim());
                        state.barcodeBuffer = '';
                    }
                }, 100);
            }

            function handleBarcodeKeydown(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();

                    if (!state.isScannerEnabled || !state.isCustomerSelected) {
                        showToast('Please select customer first', 'error');
                        e.target.value = '';
                        return;
                    }

                    const scannedCode = e.target.value.trim();
                    if (scannedCode) {
                        processBarcode(scannedCode);
                    }
                    e.target.value = '';
                }
            }

            function processBarcode(code) {
                if (!code || code.length === 0) return;

                const product = state.products.find(p =>
                    p.product_code && p.product_code.toString() === code.toString()
                );

                if (!product) {
                    showToast(`Product not found for code: ${code}`, 'error');
                    return;
                }

                if (product.quantity <= 0) {
                    showToast(`Product "${product.name}" is out of stock!`, 'error');
                    return;
                }

                addProduct(product);
            }

            // ========== CALCULATION FUNCTIONS ==========
            function calculate() {
                let subTotal = 0;

                document.querySelectorAll('#itemsTable tr[data-pid]').forEach(row => {
                    const qty = parseFloat(row.querySelector('.qty')?.value) || 0;
                    const price = parseFloat(row.querySelector('[name="items[price][]"]')?.value) || 0;
                    const total = qty * price;

                    const totalInput = row.querySelector('[name="items[total][]"]');
                    if (totalInput) totalInput.value = total.toFixed(2);

                    subTotal += total;
                });

                const discount = parseFloat(document.getElementById('discount')?.value) || 0;
                const tax = parseFloat(document.getElementById('tax')?.value) || 0;
                const taxAmount = (subTotal * tax) / 100;
                const grandTotal = Math.max(0, subTotal - discount + taxAmount);

                const subTotalInput = document.getElementById('sub_total');
                if (subTotalInput) subTotalInput.value = subTotal.toFixed(2);

                const grandTotalInput = document.getElementById('grand_total');
                if (grandTotalInput) grandTotalInput.value = grandTotal.toFixed(2);
            }

            // ========== FORM SUBMISSION ==========
            function handleSubmit(event) {
                event.preventDefault();

                // Validation
                if (!state.isCustomerSelected) {
                    showToast('Please select a customer', 'error');
                    if (elements.customerSearch) {
                        elements.customerSearch.focus();
                        elements.customerSearch.classList.add('shake');
                        setTimeout(() => elements.customerSearch.classList.remove('shake'), 500);
                    }
                    return false;
                }

                const hasProducts = document.querySelectorAll('#itemsTable tr[data-pid]').length > 0;
                if (!hasProducts) {
                    showToast('Please add at least one product', 'error');
                    if (elements.productSearch) {
                        elements.productSearch.focus();
                        elements.productSearch.classList.add('shake');
                        setTimeout(() => elements.productSearch.classList.remove('shake'), 500);
                    }
                    return false;
                }

                // Validate stock
                let stockError = false;
                document.querySelectorAll('#itemsTable tr[data-pid]').forEach(row => {
                    const qty = parseInt(row.querySelector('.qty')?.value) || 0;
                    const maxQty = parseInt(row.querySelector('.qty')?.getAttribute('max')) || 0;

                    if (qty > maxQty) {
                        stockError = true;
                        row.style.background = '#fee2e2';
                        setTimeout(() => row.style.background = '', 2000);
                    }
                });

                if (stockError) {
                    showToast('Some products exceed available stock!', 'error');
                    return false;
                }

                // Disable submit button
                const btn = document.getElementById('updateBtn');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = `
                        <span class="btn-icon spin">⏳</span>
                        Updating...
                    `;
                }

                showToast('Updating invoice...', 'info');
                document.getElementById('invoiceForm').submit();

                return false;
            }

            // ========== CUSTOMER MODAL FUNCTIONS ==========
            function openCustomerModal() {
                ['c_name', 'c_mobile', 'c_email', 'c_address'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.value = '';
                });

                if (elements.customerModal) elements.customerModal.style.display = 'flex';
                document.getElementById('c_name')?.focus();
            }

            function closeCustomerModal() {
                if (elements.customerModal) elements.customerModal.style.display = 'none';
            }

            function saveCustomer() {
                if (state.isSavingCustomer) return;

                const name = document.getElementById('c_name')?.value.trim();
                const mobile = document.getElementById('c_mobile')?.value.trim();
                const email = document.getElementById('c_email')?.value.trim();
                const address = document.getElementById('c_address')?.value.trim();

                if (!name) {
                    showToast('Customer name is required', 'error');
                    document.getElementById('c_name')?.focus();
                    return;
                }

                if (!mobile) {
                    showToast('Mobile number is required', 'error');
                    document.getElementById('c_mobile')?.focus();
                    return;
                }

                state.isSavingCustomer = true;

                if (elements.saveCustomerBtn) {
                    elements.saveCustomerBtn.innerHTML = `
                        <span style="
                            display: inline-block;
                            width: 16px;
                            height: 16px;
                            border: 2px solid rgba(255,255,255,0.3);
                            border-top-color: white;
                            border-radius: 50%;
                            animation: buttonSpin 0.6s linear infinite;
                        "></span>
                        Saving...
                    `;
                    elements.saveCustomerBtn.disabled = true;
                }

                fetch("{{ route('customers.store.ajax') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({ name, mobile, email, address })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.customer) {
                        closeCustomerModal();
                        selectCustomer(data.customer);
                        showToast('Customer added and selected!', 'success');
                    } else {
                        showToast(data.message || 'Error saving customer', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Failed to save customer', 'error');
                })
                .finally(() => {
                    state.isSavingCustomer = false;
                    if (elements.saveCustomerBtn) {
                        elements.saveCustomerBtn.innerHTML = `<span>✓</span>Save Customer`;
                        elements.saveCustomerBtn.disabled = false;
                    }
                });
            }

            // ========== UI UTILITIES ==========
            function updateUIState() {
                const hasProducts = document.querySelectorAll('#itemsTable tr[data-pid]').length > 0;

                if (!state.isCustomerSelected) {
                    if (elements.customerStatus) {
                        elements.customerStatus.textContent = 'Required';
                        elements.customerStatus.style.color = '#dc2626';
                    }
                    if (elements.itemsStatus) {
                        elements.itemsStatus.textContent = 'Select customer first';
                        elements.itemsStatus.style.color = '#dc2626';
                    }
                } else if (!hasProducts) {
                    if (elements.customerStatus) {
                        elements.customerStatus.textContent = '✅ Selected';
                        elements.customerStatus.style.color = '#059669';
                    }
                    if (elements.itemsStatus) {
                        elements.itemsStatus.textContent = 'No products added';
                        elements.itemsStatus.style.color = '#f59e0b';
                    }
                } else {
                    if (elements.customerStatus) {
                        elements.customerStatus.textContent = '✅ Selected';
                        elements.customerStatus.style.color = '#059669';
                    }
                    if (elements.itemsStatus) {
                        elements.itemsStatus.textContent = `${hasProducts} item(s)`;
                        elements.itemsStatus.style.color = '#059669';
                    }
                }
            }

            function isInputFieldActive() {
                const activeElement = document.activeElement;
                if (!activeElement) return false;

                const activeTag = activeElement.tagName.toLowerCase();
                const activeId = activeElement.id;

                return (
                    activeTag === 'input' ||
                    activeTag === 'textarea' ||
                    activeTag === 'select' ||
                    activeId === 'customerSearch' ||
                    activeId === 'productSearch' ||
                    activeElement.closest('#customerModal')
                );
            }

            function handleDocumentClick(e) {
                if (elements.customerResults &&
                    !elements.customerSearch?.contains(e.target) &&
                    !elements.customerResults.contains(e.target)) {
                    elements.customerResults.style.display = 'none';
                }

                if (elements.productResults &&
                    !elements.productSearch?.contains(e.target) &&
                    !elements.productResults.contains(e.target)) {
                    elements.productResults.style.display = 'none';
                }
            }

            function handleDocumentMousedown(e) {
                if (isInputFieldActive() || e.target.closest('button') || e.target.closest('a')) {
                    return;
                }

                if (state.isCustomerSelected && state.isScannerEnabled && !isInputFieldActive()) {
                    setTimeout(() => {
                        if (elements.barcodeInput) elements.barcodeInput.focus();
                    }, 50);
                }
            }

            // ========== HELPER FUNCTIONS ==========
            function getSearchLoadingHTML() {
                return `
                    <div style="padding: 20px; text-align: center; color: #64748b;">
                        <div style="
                            display: inline-block;
                            width: 20px;
                            height: 20px;
                            border: 2px solid #e5e7eb;
                            border-top-color: #3b82f6;
                            border-radius: 50%;
                            animation: spin 0.8s linear infinite;
                            margin-right: 10px;
                        "></div>
                        Searching...
                    </div>
                `;
            }

            function getNoCustomersHTML() {
                return `
                    <div style="padding: 30px 20px; text-align: center; color: #64748b;">
                        <div style="font-size: 40px; margin-bottom: 10px;">👤</div>
                        No customers found
                    </div>
                `;
            }

            function getSearchErrorHTML() {
                return `
                    <div style="padding: 20px; text-align: center; color: #ef4444;">
                        <div style="font-size: 40px; margin-bottom: 10px;">⚠️</div>
                        Search failed
                    </div>
                `;
            }

            function showToast(message, type = 'success') {
                document.querySelectorAll('.toast-notification').forEach(el => el.remove());

                const toast = document.createElement('div');
                toast.className = 'toast-notification';

                const bgColor = type === 'success' ? '#10b981' : 
                               type === 'error' ? '#ef4444' : 
                               type === 'warning' ? '#f59e0b' : '#3b82f6';
                const icon = type === 'success' ? '✓' : 
                            type === 'error' ? '⚠' : 
                            type === 'warning' ? '⚠' : 'ℹ';

                toast.style.background = bgColor;
                toast.innerHTML = `
                    <span class="toast-icon">${icon}</span>
                    <span class="toast-message">${escapeHTML(message)}</span>
                `;

                document.body.appendChild(toast);

                setTimeout(() => {
                    if (toast.parentNode) toast.remove();
                }, 3000);
            }

            // ========== PUBLIC API ==========
            return {
                init,
                selectCustomer,
                clearCustomerSelection,
                addProduct,
                removeProduct,
                calculate,
                handleSubmit,
                openCustomerModal,
                closeCustomerModal,
                saveCustomer,
                showToast
            };
        })();

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            InvoiceManager.init();
        });

        // Make InvoiceManager globally available
        window.InvoiceManager = InvoiceManager;
    </script>
@endsection