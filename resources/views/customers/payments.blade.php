{{-- D:\smartErp\resources\views\customers\payments.blade.php --}}
@extends('layouts.app')
@section('page-title', 'Payments History - ' . $customer->name)

@section('content')
    <style>
        /* ================= PROFESSIONAL DESIGN SYSTEM ================= */
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --success: #16a34a;
            --danger: #dc2626;
            --warning: #d97706;
            --purple: #7c3aed;
            --info: #0ea5e9;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --bg-light: #f8fafc;
            --bg-white: #ffffff;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            color: var(--text-main);
            line-height: 1.5;
        }

        /* ================= CONTAINER ================= */
        .payment-wrapper {
            min-height: 100vh;
            background: #f1f5f9;
            padding: 2rem 1rem;
        }

        .payment-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* ================= HEADER CARD ================= */
        .header-card {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            padding: 1.5rem 2rem;
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
            color: white;
            box-shadow: var(--shadow-md);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .customer-name {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
        }

        .customer-contact {
            color: #cbd5e1;
            margin-top: 0.5rem;
            font-size: 0.95rem;
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .btn-outline-light {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .btn-outline-light:hover {
            background: white;
            color: #0f172a;
        }

        /* ================= BULK ACTIONS BAR ================= */
        .bulk-actions-bar {
            background: var(--bg-white);
            padding: 0.75rem 2rem;
            border-bottom: 2px solid var(--primary);
            display: none;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            box-shadow: var(--shadow-sm);
        }

        .bulk-actions-bar.show {
            display: flex;
        }

        .bulk-select-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.9rem;
        }

        .bulk-select-info strong {
            color: var(--primary);
            font-size: 1.1rem;
        }

        .bulk-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .bulk-btn {
            padding: 0.5rem 1rem;
            border-radius: var(--radius-sm);
            font-size: 0.9rem;
            font-weight: 500;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .bulk-btn-info {
            background: var(--info);
            color: white;
        }

        .bulk-btn-info:hover:not(:disabled) {
            background: #0284c7;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .bulk-btn-info:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .bulk-btn-secondary {
            background: var(--bg-light);
            color: var(--text-main);
            border: 1px solid var(--border);
        }

        .bulk-btn-secondary:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .bulk-btn-success {
            background: var(--success);
            color: white;
        }

        .bulk-btn-success:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* ================= STATS GRID ================= */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            padding: 1.5rem 2rem;
            background: var(--bg-white);
        }

        .stat-card {
            background: var(--bg-light);
            padding: 1.5rem 1.25rem;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            transition: all 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary);
        }

        .stat-icon {
            font-size: 1.5rem;
            margin-bottom: 0.75rem;
            display: inline-block;
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.2;
            color: var(--text-main);
        }

        .stat-value.success {
            color: var(--success);
        }

        .stat-value.danger {
            color: var(--danger);
        }

        .stat-value.warning {
            color: var(--warning);
        }

        .stat-value.purple {
            color: var(--purple);
        }

        .stat-value.primary {
            color: var(--primary);
        }

        .stat-value.info {
            color: var(--info);
        }

        /* ================= DUE BALANCE CARD ================= */
        .due-balance-card {
            background: var(--bg-white);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .due-balance-label {
            font-weight: 600;
            color: var(--text-muted);
            font-size: 1rem;
        }

        .due-balance-amount {
            font-size: 2rem;
            font-weight: 800;
            color: var(--danger);
        }

        .due-balance-breakdown {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-left: auto;
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .breakdown-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .breakdown-label {
            color: var(--text-muted);
        }

        .breakdown-value {
            font-weight: 600;
            color: var(--text-main);
        }

        .breakdown-value.success {
            color: var(--success);
        }

        .breakdown-value.danger {
            color: var(--danger);
        }

        /* ================= TABLE CONTAINER ================= */
        .table-container {
            background: var(--bg-white);
            padding: 1.5rem 2rem;
            border-top: 1px solid var(--border);
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* ================= BADGES ================= */
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            text-align: center;
        }

        .badge-paid {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .badge-partial {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fbbf24;
        }

        .badge-unpaid {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #f87171;
        }

        .badge-emi {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        .badge-credit {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .badge-debit {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #f87171;
        }

        /* ================= CHECKBOX STYLES ================= */
        .invoice-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--primary);
        }

        .select-all-row {
            background: #f0f9ff !important;
        }

        .select-all-row td {
            padding: 0.5rem 1rem;
        }

        /* ================= TABLES ================= */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            overflow: hidden;
            font-size: 0.9rem;
            background: var(--bg-white);
            box-shadow: var(--shadow-sm);
        }

        .data-table th {
            background: #f1f5f9;
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.8rem;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 2px solid var(--border);
        }

        .data-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .data-table tbody tr:hover {
            background: var(--bg-light);
        }

        .data-table .total-row {
            background: #f1f5f9;
            font-weight: 600;
            border-top: 2px solid var(--border);
        }

        .data-table .total-row td {
            border-bottom: none;
        }

        /* ================= ACTION BUTTONS ================= */
        .btn-group {
            display: flex;
            gap: 0.25rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 0.35rem 0.75rem;
            border-radius: var(--radius-sm);
            font-size: 0.8rem;
            font-weight: 500;
            border: 1px solid var(--border);
            background: white;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .btn-sm:hover {
            background: #f1f5f9;
        }

        .btn-sm.sending {
            opacity: 0.7;
            cursor: wait;
            pointer-events: none;
        }

        .btn-danger:hover {
            background: var(--danger);
            color: white;
            border-color: var(--danger);
        }

        .btn-primary:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .btn-warning:hover {
            background: var(--warning);
            color: white;
            border-color: var(--warning);
        }

        .btn-info {
            color: var(--info);
            border-color: var(--info);
        }

        .btn-info:hover {
            background: var(--info);
            color: white;
        }

        .btn-success {
            color: var(--success);
            border-color: var(--success);
        }

        .btn-success:hover {
            background: var(--success);
            color: white;
        }

        /* ================= TEXT UTILITIES ================= */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: 600;
        }

        .text-success {
            color: var(--success);
        }

        .text-danger {
            color: var(--danger);
        }

        .text-primary {
            color: var(--primary);
        }

        .text-purple {
            color: var(--purple);
        }

        .text-warning {
            color: var(--warning);
        }

        .text-info {
            color: var(--info);
        }

        /* ================= TOAST ================= */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 0.75rem 1.5rem;
            background: white;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            border-left: 4px solid;
            display: none;
            z-index: 10001;
            min-width: 300px;
            animation: slideIn 0.3s ease;
        }

        .toast.success {
            border-left-color: var(--success);
        }

        .toast.error {
            border-left-color: var(--danger);
        }

        .toast.warning {
            border-left-color: var(--warning);
        }

        .toast.info {
            border-left-color: var(--info);
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

        /* ================= LOADING OVERLAY ================= */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(4px);
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 1rem;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #e2e8f0;
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        .progress-text {
            font-size: 1rem;
            color: var(--text-main);
            font-weight: 500;
        }

        .progress-bar {
            width: 300px;
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--info));
            width: 0%;
            transition: width 0.3s ease;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ================= EMPTY STATE ================= */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--bg-white);
            border-radius: var(--radius-lg);
            border: 1px dashed var(--border);
            margin: 2rem 0;
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .empty-text {
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .header-card {
                padding: 1.5rem;
            }

            .customer-name {
                font-size: 1.5rem;
            }

            .stats-grid {
                padding: 1.5rem;
            }

            .table-container {
                padding: 1.5rem;
                overflow-x: auto;
            }

            .data-table {
                min-width: 1200px;
            }

            .btn-group {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-sm {
                width: 100%;
                justify-content: center;
            }

            .due-balance-card {
                flex-direction: column;
                align-items: flex-start;
            }

            .due-balance-breakdown {
                margin-left: 0;
                flex-direction: column;
                gap: 0.5rem;
            }

            .bulk-actions-bar {
                flex-direction: column;
                align-items: flex-start;
            }

            .bulk-buttons {
                width: 100%;
                flex-direction: column;
            }

            .bulk-btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media print {

            .btn-group,
            .loading-overlay,
            .toast,
            .bulk-actions-bar,
            .invoice-checkbox {
                display: none !important;
            }

            .header-card {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .badge {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>

    @php
        // Helper function to format currency with ₹ symbol
        function formatCurrency($amount)
        {
            return '₹' . number_format($amount, 2);
        }

        // ================= GET ALL DATA =================
        $walletTransactions = \App\Models\CustomerWallet::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $allPayments = \App\Models\Payment::where('customer_id', $customer->id)
            ->with('sale')
            ->orderBy('created_at', 'desc')
            ->get();

        // Wallet Summary
        $totalAdded = $walletTransactions->where('type', 'credit')->sum('amount');
        $totalUsed = $walletTransactions->where('type', 'debit')->sum('amount');
        $currentBalance = $walletTransactions->first()?->balance ?? 0;

        // ========== SMART DUE CALCULATION ==========
        // Total Invoice Amount
        $totalInvoiceAmount = $invoices->sum('grand_total');
        
        // Total Paid Amount (Cash + Wallet Used)
        $totalPaidAmount = $allPayments
            ->where('status', 'paid')
            ->whereIn('remarks', ['INVOICE', 'EMI_DOWN', 'ADVANCE_USED'])
            ->sum('amount');
        
        // Total Due Amount (Invoice Total - Paid Amount)
        $totalDueAmount = max(0, $totalInvoiceAmount - $totalPaidAmount);
        
        // Payment Breakdown
        $invoicePaymentsTotal = $allPayments->whereIn('remarks', ['INVOICE', 'EMI_DOWN'])->sum('amount');
        $walletUsedTotal = $allPayments->where('remarks', 'ADVANCE_USED')->sum('amount');
        $walletAdditionsTotal = $allPayments
            ->whereIn('remarks', ['EXCESS_TO_ADVANCE', 'ADVANCE_ONLY', 'WALLET_ADD'])
            ->sum('amount');
        
        $appliedToInvoices = $invoicePaymentsTotal + $walletUsedTotal;
        $totalReceived = $allPayments->sum('amount');
        
        // Net Position (Wallet Balance - Total Due)
        $netPosition = $currentBalance - $totalDueAmount;

        // Invoice Summaries
        $invoiceSummaries = [];
        $totalGrandAmount = 0;
        $totalCashPayments = 0;
        $totalWalletUsed = 0;
        $totalWalletAdded = 0;
        $totalReceivedAmount = 0;
        $totalAppliedToInvoice = 0;
        $totalDueAmt = 0;
        $dueInvoicesCount = 0;

        foreach ($invoices as $inv) {
            $invoicePayments = $allPayments->where('sale_id', $inv->id);

            $cashPayments = $invoicePayments->whereIn('remarks', ['INVOICE', 'EMI_DOWN'])->sum('amount');
            $walletUsed = $invoicePayments->where('remarks', 'ADVANCE_USED')->sum('amount');
            $walletAdded = $invoicePayments
                ->whereIn('remarks', ['WALLET_ADD', 'EXCESS_TO_ADVANCE', 'ADVANCE_ONLY'])
                ->sum('amount');

            $totalReceivedForInvoice = $cashPayments + $walletUsed + $walletAdded;
            $appliedToThisInvoice = $cashPayments + $walletUsed;
            $dueAmount = max(0, $inv->grand_total - $appliedToThisInvoice);

            $status = $dueAmount <= 0 ? 'paid' : ($appliedToThisInvoice > 0 ? 'partial' : 'unpaid');
            if ($inv->payment_status === 'emi') {
                $status = 'emi';
            }

            if ($dueAmount > 0) {
                $dueInvoicesCount++;
            }

            $totalGrandAmount += $inv->grand_total;
            $totalCashPayments += $cashPayments;
            $totalWalletUsed += $walletUsed;
            $totalWalletAdded += $walletAdded;
            $totalReceivedAmount += $totalReceivedForInvoice;
            $totalAppliedToInvoice += $appliedToThisInvoice;
            $totalDueAmt += $dueAmount;

            $invoiceSummaries[] = [
                'id' => $inv->id,
                'date' => $inv->sale_date->format('d-m-Y'),
                'invoice_no' => $inv->invoice_no,
                'grand_total' => $inv->grand_total,
                'cash_paid' => $cashPayments,
                'wallet_used' => $walletUsed,
                'wallet_added' => $walletAdded,
                'total_received' => $totalReceivedForInvoice,
                'applied' => $appliedToThisInvoice,
                'due' => $dueAmount,
                'status' => $status,
                'payment_count' => $invoicePayments->count(),
                'has_emi' => $inv->payment_status === 'emi',
                'customer_email' => $customer->email ?? ''
            ];
        }

        usort($invoiceSummaries, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
    @endphp

    <div class="payment-wrapper">
        <div class="payment-container">

            {{-- Loading Overlay --}}
            <div id="loadingOverlay" class="loading-overlay">
                <div class="spinner"></div>
                <div class="progress-text" id="loadingText">Processing...</div>
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
            </div>

            {{-- Header Card --}}
            <div class="header-card">
                <div class="header-content">
                    <div>
                        <h1 class="customer-name">{{ $customer->name }}</h1>
                        <div class="customer-contact">
                            <span>📱 {{ $customer->mobile ?? 'N/A' }}</span>
                            <span>✉️ {{ $customer->email ?? 'N/A' }}</span>
                            <span>🆔 #{{ $customer->id }}</span>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('customers.index') }}" class="btn-outline-light">← Back</a>
                        @if(auth()->user()->hasPermission('export_sales'))
                            <button onclick="window.print()" class="btn-outline-light">🖨️ Print</button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- SMART DUE BALANCE CARD - Only Due, No Advance --}}
            <div class="due-balance-card">
                <span class="due-balance-label">💰 Total Outstanding:</span>
                <span class="due-balance-amount">{{ formatCurrency($totalDueAmount) }}</span>
                
                <div class="due-balance-breakdown">
                    <div class="breakdown-item">
                        <span class="breakdown-label">📄 Total Invoices:</span>
                        <span class="breakdown-value">{{ formatCurrency($totalInvoiceAmount) }}</span>
                    </div>
                    <div class="breakdown-item">
                        <span class="breakdown-label">✅ Total Paid:</span>
                        <span class="breakdown-value success">{{ formatCurrency($totalPaidAmount) }}</span>
                    </div>
                    <div class="breakdown-item">
                        <span class="breakdown-label">💰 Wallet Balance:</span>
                        <span class="breakdown-value {{ $currentBalance > 0 ? 'success' : 'text-muted' }}">
                            {{ formatCurrency($currentBalance) }}
                        </span>
                    </div>
                    @if($netPosition != 0)
                    <div class="breakdown-item">
                        <span class="breakdown-label">⚖️ Net Position:</span>
                        <span class="breakdown-value {{ $netPosition > 0 ? 'success' : ($netPosition < 0 ? 'danger' : '') }}">
                            @if($netPosition > 0)
                                +{{ formatCurrency($netPosition) }}
                            @elseif($netPosition < 0)
                                {{ formatCurrency($netPosition) }}
                            @else
                                {{ formatCurrency(0) }}
                            @endif
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Stats Cards --}}
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-label">Wallet Balance</div>
                    <div class="stat-value success">{{ formatCurrency($currentBalance) }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-label">Total Invoice Due</div>
                    <div class="stat-value danger">{{ formatCurrency($totalDueAmount) }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⚖️</div>
                    <div class="stat-label">Net Position</div>
                    <div class="stat-value {{ $netPosition >= 0 ? 'success' : 'danger' }}">
                        @if ($netPosition > 0)
                            +{{ formatCurrency($netPosition) }}
                        @elseif($netPosition < 0)
                            -{{ formatCurrency(abs($netPosition)) }}
                        @else
                            {{ formatCurrency(0) }}
                        @endif
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">➕</div>
                    <div class="stat-label">Wallet Added</div>
                    <div class="stat-value purple">{{ formatCurrency($totalAdded) }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">➖</div>
                    <div class="stat-label">Wallet Used</div>
                    <div class="stat-value warning">{{ formatCurrency($totalUsed) }}</div>
                </div>
            </div>

            {{-- Payment Breakdown --}}
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-label">Total Received</div>
                    <div class="stat-value primary">{{ formatCurrency($totalReceived) }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📄</div>
                    <div class="stat-label">Invoice Payments</div>
                    <div class="stat-value success">{{ formatCurrency($invoicePaymentsTotal) }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🔄</div>
                    <div class="stat-label">Wallet Used</div>
                    <div class="stat-value purple">{{ formatCurrency($walletUsedTotal) }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">➕</div>
                    <div class="stat-label">Wallet Additions</div>
                    <div class="stat-value warning">{{ formatCurrency($walletAdditionsTotal) }}</div>
                </div>
            </div>

            {{-- BULK ACTIONS BAR --}}
            <div id="bulkActionsBar" class="bulk-actions-bar">
                <div class="bulk-select-info">
                    <span>📋 <strong id="selectedCount">0</strong> invoices selected</span>
                    <span>💰 Total Due: <strong id="selectedTotalDue">₹0.00</strong></span>
                </div>
                <div class="bulk-buttons">
                    <button class="bulk-btn bulk-btn-info" onclick="sendBulkReminders()" id="bulkEmailBtn" disabled>
                        <span>📧📧</span>
                        Send Bulk Reminders
                    </button>
                    <button class="bulk-btn bulk-btn-secondary" onclick="clearAllSelections()">
                        <span>✕</span>
                        Clear All
                    </button>
                </div>
            </div>

            {{-- Invoice Table --}}
            <div class="table-container">
                <h3 class="section-title">📋 Invoice-wise Summary</h3>

                @if (count($invoiceSummaries) > 0)
                    {{-- Select All Row --}}
                    <div class="select-all-row">
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 40px; text-align: center;">
                                    <input type="checkbox" id="selectAllCheckbox" class="invoice-checkbox" onchange="toggleSelectAll(this)">
                                </td>
                                <td colspan="10">
                                    <label for="selectAllCheckbox" style="font-weight: 600; color: var(--primary); cursor: pointer;">
                                        Select All Due Invoices ({{ $dueInvoicesCount }} invoices with due amount)
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <table class="data-table" id="invoicesTable">
                        <thead>
                            <tr>
                                <th style="width: 40px;">✓</th>
                                <th>Date</th>
                                <th>Invoice #</th>
                                <th>Status</th>
                                <th class="text-right">Grand Total</th>
                                <th class="text-right">Cash Paid</th>
                                <th class="text-right">Wallet Used</th>
                                <th class="text-right">Wallet Added</th>
                                <th class="text-right">Total Received</th>
                                <th class="text-right">Applied</th>
                                <th class="text-right">Due</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoiceSummaries as $inv)
                                <tr data-id="{{ $inv['id'] }}" 
                                    data-invoice="{{ $inv['invoice_no'] }}" 
                                    data-due="{{ $inv['due'] }}"
                                    data-email="{{ $inv['customer_email'] }}">
                                    <td style="text-align: center;">
                                        @if($inv['due'] > 0)
                                            <input type="checkbox" class="invoice-checkbox row-checkbox" 
                                                   data-id="{{ $inv['id'] }}"
                                                   data-invoice="{{ $inv['invoice_no'] }}"
                                                   data-due="{{ $inv['due'] }}"
                                                   onchange="updateBulkSelection()">
                                        @endif
                                    </td>
                                    <td>{{ $inv['date'] }}</td>
                                    <td>
                                        <a href="{{ route('sales.show', $inv['id']) }}"
                                            style="color: var(--primary); text-decoration: none;">
                                            #{{ $inv['invoice_no'] }}
                                        </a>
                                        @if ($inv['has_emi'])
                                            <span class="badge badge-emi">EMI</span>
                                        @endif
                                    </td>
                                    <td><span
                                            class="badge badge-{{ $inv['status'] }}">{{ strtoupper($inv['status']) }}</span>
                                    </td>
                                    <td class="text-right">{{ formatCurrency($inv['grand_total']) }}</td>
                                    <td class="text-right text-primary">{{ formatCurrency($inv['cash_paid']) }}</td>
                                    <td class="text-right text-purple">{{ formatCurrency($inv['wallet_used']) }}</td>
                                    <td class="text-right text-warning">{{ formatCurrency($inv['wallet_added']) }}</td>
                                    <td class="text-right text-success fw-bold">
                                        {{ formatCurrency($inv['total_received']) }}</td>
                                    <td class="text-right text-primary">{{ formatCurrency($inv['applied']) }}</td>
                                    <td class="text-right {{ $inv['due'] > 0 ? 'text-danger' : 'text-success' }} fw-bold">
                                        {{ formatCurrency($inv['due']) }}
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            @if(auth()->user()->hasPermission('view_sales'))
                                                <a href="{{ route('sales.show', $inv['id']) }}" class="btn-sm"
                                                    title="View">👁️</a>
                                            @endif
                                            
                                            {{-- DIRECT EMAIL ICON - No Modal, No Form, Click = Instant Send --}}
                                            @if($inv['due'] > 0 && $customer->email)
                                                @if(auth()->user()->hasPermission('export_sales'))
                                                    <button class="btn-sm btn-info email-btn" 
                                                        onclick="sendSingleReminder({{ $inv['id'] }}, '{{ $inv['invoice_no'] }}', {{ $inv['due'] }}, this)"
                                                        title="Send Payment Reminder - Instant">
                                                        <span>📧</span>
                                                    </button>
                                                @endif
                                            @endif
                                            
                                            @if ($inv['payment_count'] > 0)
                                                @if(auth()->user()->hasPermission('delete_payments'))
                                                    <button class="btn-sm btn-danger"
                                                        onclick="bulkDeletePayments({{ $inv['id'] }}, '{{ $inv['invoice_no'] }}', {{ $inv['total_received'] }})"
                                                        title="Delete Payments">🗑️</button>
                                                @endif
                                            @endif
                                            
                                            @if(auth()->user()->hasPermission('delete_sales'))
                                                <button class="btn-sm btn-warning"
                                                    onclick="deleteInvoiceWithPayments({{ $inv['id'] }}, '{{ $inv['invoice_no'] }}', {{ $inv['total_received'] }})"
                                                    title="Delete Invoice">❌</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td></td>
                                <td colspan="3" class="text-right">Total:</td>
                                <td class="text-right">{{ formatCurrency($totalGrandAmount) }}</td>
                                <td class="text-right text-primary">{{ formatCurrency($totalCashPayments) }}</td>
                                <td class="text-right text-purple">{{ formatCurrency($totalWalletUsed) }}</td>
                                <td class="text-right text-warning">{{ formatCurrency($totalWalletAdded) }}</td>
                                <td class="text-right text-success">{{ formatCurrency($totalReceivedAmount) }}</td>
                                <td class="text-right text-primary">{{ formatCurrency($totalAppliedToInvoice) }}</td>
                                <td class="text-right {{ $totalDueAmt > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ formatCurrency($totalDueAmt) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">📭</div>
                        <div class="empty-title">No invoices found</div>
                        <div class="empty-text">Create a new invoice to get started</div>
                        @if(auth()->user()->hasPermission('create_sales'))
                            <a href="{{ route('sales.create') }}?customer_id={{ $customer->id }}&customer_name={{ urlencode($customer->name) }}"
                                class="btn-sm" style="padding: 0.75rem 1.5rem;">
                                ➕ Create New Invoice
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Wallet Ledger --}}
            @if ($walletTransactions->count() > 0)
                <div class="table-container">
                    <h3 class="section-title">📒 Wallet Ledger</h3>
                    <table class="data-table" id="walletTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th class="text-right">Amount</th>
                                <th class="text-right">Balance</th>
                                <th>Reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($walletTransactions as $trans)
                                @php
                                    $displayBalance = max(0, $trans->balance);
                                @endphp
                                <tr>
                                    <td>{{ $trans->created_at->format('d-m-Y H:i') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $trans->type == 'credit' ? 'credit' : 'debit' }}">
                                            {{ $trans->type == 'credit' ? 'CREDIT' : 'DEBIT' }}
                                        </span>
                                    </td>
                                    <td
                                        class="text-right {{ $trans->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                        {{ $trans->type == 'credit' ? '+' : '-' }} {{ formatCurrency($trans->amount) }}
                                    </td>
                                    <td class="text-right fw-bold">{{ formatCurrency($displayBalance) }}</td>
                                    <td>{{ $trans->reference }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Toast --}}
    <div id="toast" class="toast"></div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        const customerName = "{{ $customer->name }}";
        const customerEmail = "{{ $customer->email }}";
        
        // Selected invoices for bulk actions
        let selectedInvoices = [];

        // Initialize DataTables
        $(document).ready(function() {
            if ($('#invoicesTable').length) {
                $('#invoicesTable').DataTable({
                    pageLength: 25,
                    order: [
                        [1, 'desc']
                    ],
                    language: {
                        search: "Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        paginate: {
                            previous: "Previous",
                            next: "Next"
                        }
                    },
                    columnDefs: [
                        { orderable: false, targets: [0, 11] }
                    ]
                });
            }

            if ($('#walletTable').length) {
                $('#walletTable').DataTable({
                    pageLength: 25,
                    order: [
                        [0, 'desc']
                    ],
                    language: {
                        search: "Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        paginate: {
                            previous: "Previous",
                            next: "Next"
                        }
                    }
                });
            }
        });

        // Toast notification
        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toast');
            toast.innerHTML = msg;
            toast.className = 'toast ' + type;
            toast.style.display = 'block';
            setTimeout(() => toast.style.display = 'none', 3000);
        }

        // Loading overlay
        function showLoading(showProgress = false) {
            document.getElementById('loadingOverlay').style.display = 'flex';
            if (!showProgress) {
                document.querySelector('.progress-bar').style.display = 'none';
            } else {
                document.querySelector('.progress-bar').style.display = 'block';
            }
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        function updateProgress(percent, message) {
            document.getElementById('progressFill').style.width = percent + '%';
            if (message) {
                document.getElementById('loadingText').textContent = message;
            }
        }

        // ========== SINGLE INSTANT REMINDER - NO MODAL, NO FORM ==========
        function sendSingleReminder(invoiceId, invoiceNo, dueAmount, button) {
            if (!customerEmail) {
                showToast('❌ Customer email not available', 'error');
                return;
            }

            // Prevent double-click
            if (button.classList.contains('sending')) {
                return;
            }
            
            // Add sending state
            button.classList.add('sending');
            button.innerHTML = '<span>⏳</span>';
            
            const formattedDue = '₹' + parseFloat(dueAmount).toFixed(2);
            const subject = `Payment Reminder: Invoice #${invoiceNo} - Due: ${formattedDue}`;
            const body = `Dear ${customerName},

This is a friendly reminder that the following invoice has an outstanding balance:

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Invoice #: ${invoiceNo}
Due Amount: ${formattedDue}
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Please make the payment at your earliest convenience.

Thank you for your business!`;

            // Send email instantly
            fetch('{{ route("sales.send-invoice") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    sale_id: invoiceId,
                    recipient_email: customerEmail,
                    email_subject: subject,
                    email_body: body
                })
            })
            .then(response => response.json())
            .then(data => {
                button.classList.remove('sending');
                button.innerHTML = '<span>📧</span>';
                
                if (data.success) {
                    showToast(`✅ Reminder sent for Invoice #${invoiceNo}`, 'success');
                } else {
                    showToast(`❌ Failed: ${data.message}`, 'error');
                }
            })
            .catch(error => {
                button.classList.remove('sending');
                button.innerHTML = '<span>📧</span>';
                showToast('❌ Error sending email', 'error');
                console.error('Error:', error);
            });
        }

        // ========== BULK INSTANT REMINDERS - NO MODAL, NO FORM ==========
        function sendBulkReminders() {
            if (selectedInvoices.length === 0) {
                showToast('Please select at least one invoice', 'warning');
                return;
            }
            
            if (!customerEmail) {
                showToast('❌ Customer email not available', 'error');
                return;
            }
            
            // Disable bulk button
            const bulkBtn = document.getElementById('bulkEmailBtn');
            bulkBtn.disabled = true;
            bulkBtn.innerHTML = '<span>⏳</span> Sending...';
            
            // Show progress
            showLoading(true);
            updateProgress(0, `Sending 0/${selectedInvoices.length} emails...`);
            
            let sentCount = 0;
            let failedCount = 0;
            let totalInvoices = selectedInvoices.length;
            
            // Send emails one by one
            selectedInvoices.forEach((invoice, index) => {
                const formattedDue = '₹' + invoice.due.toFixed(2);
                const subject = `Payment Reminder: Invoice #${invoice.invoice} - Due: ${formattedDue}`;
                const body = `Dear ${customerName},

This is a friendly reminder that you have an outstanding invoice:

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Invoice #: ${invoice.invoice}
Due Amount: ${formattedDue}
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Please make the payment at your earliest convenience.

Thank you for your business!`;
                
                fetch('{{ route("sales.send-invoice") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        sale_id: invoice.id,
                        recipient_email: customerEmail,
                        email_subject: subject,
                        email_body: body
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        sentCount++;
                    } else {
                        failedCount++;
                    }
                })
                .catch(() => {
                    failedCount++;
                })
                .finally(() => {
                    // Update progress
                    const progress = Math.round(((index + 1) / totalInvoices) * 100);
                    updateProgress(progress, `Sending ${index + 1}/${totalInvoices} emails...`);
                    
                    // If this is the last invoice, show summary
                    if (index === totalInvoices - 1) {
                        setTimeout(() => {
                            hideLoading();
                            bulkBtn.disabled = false;
                            bulkBtn.innerHTML = '<span>📧📧</span> Send Bulk Reminders';
                            
                            if (sentCount > 0) {
                                showToast(`✅ ${sentCount} reminders sent! ${failedCount > 0 ? `(${failedCount} failed)` : ''}`, 
                                         failedCount > 0 ? 'warning' : 'success');
                            } else {
                                showToast('❌ Failed to send emails', 'error');
                            }
                            
                            // Clear selections
                            clearAllSelections();
                        }, 500);
                    }
                });
            });
        }

        // ========== BULK SELECTION FUNCTIONS ==========
        function updateBulkSelection() {
            selectedInvoices = [];
            let totalDue = 0;
            
            document.querySelectorAll('.row-checkbox:checked').forEach(checkbox => {
                const id = checkbox.dataset.id;
                const invoice = checkbox.dataset.invoice;
                const due = parseFloat(checkbox.dataset.due);
                
                selectedInvoices.push({
                    id: id,
                    invoice: invoice,
                    due: due
                });
                
                totalDue += due;
            });
            
            // Update UI
            document.getElementById('selectedCount').textContent = selectedInvoices.length;
            document.getElementById('selectedTotalDue').textContent = '₹' + totalDue.toFixed(2);
            
            // Show/hide bulk actions bar
            const bulkBar = document.getElementById('bulkActionsBar');
            const bulkBtn = document.getElementById('bulkEmailBtn');
            
            if (selectedInvoices.length > 0) {
                bulkBar.classList.add('show');
                bulkBtn.disabled = false;
            } else {
                bulkBar.classList.remove('show');
                bulkBtn.disabled = true;
            }
            
            // Update select all checkbox
            const totalDueCheckboxes = document.querySelectorAll('.row-checkbox').length;
            const checkedCheckboxes = document.querySelectorAll('.row-checkbox:checked').length;
            const selectAll = document.getElementById('selectAllCheckbox');
            
            if (selectAll) {
                selectAll.checked = totalDueCheckboxes > 0 && totalDueCheckboxes === checkedCheckboxes;
                selectAll.indeterminate = checkedCheckboxes > 0 && checkedCheckboxes < totalDueCheckboxes;
            }
        }

        function toggleSelectAll(checkbox) {
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = checkbox.checked);
            updateBulkSelection();
        }

        function clearAllSelections() {
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('selectAllCheckbox').checked = false;
            document.getElementById('selectAllCheckbox').indeterminate = false;
            updateBulkSelection();
        }

        // ========== DELETE FUNCTIONS ==========
        function bulkDeletePayments(id, no, amount) {
            if (!confirm(`Delete all payments for #${no} (₹${amount})?`)) return;
            showLoading();
            fetch(`/payments/bulk/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(r => r.json())
                .then(d => {
                    hideLoading();
                    if (d.success) {
                        showToast('✅ Payments deleted!');
                        setTimeout(() => location.reload(), 1500);
                    } else showToast('❌ ' + d.message, 'error');
                })
                .catch(() => {
                    hideLoading();
                    showToast('❌ Error', 'error');
                });
        }

        function deleteInvoiceWithPayments(id, no, amount) {
            if (!confirm(`Delete Invoice #${no} and all payments?`)) return;
            showLoading();
            fetch(`/invoices/${id}/delete-with-payments`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(r => r.json())
                .then(d => {
                    hideLoading();
                    if (d.success) {
                        showToast('✅ Invoice deleted!');
                        setTimeout(() => location.reload(), 1500);
                    } else showToast('❌ ' + d.message, 'error');
                })
                .catch(() => {
                    hideLoading();
                    showToast('❌ Error', 'error');
                });
        }

        // Print shortcut
        document.addEventListener('keydown', e => {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
@endsection