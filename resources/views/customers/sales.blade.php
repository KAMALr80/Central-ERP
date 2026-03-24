@extends('layouts.app')

@section('page-title', 'Sales History - ' . $customer->name)

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

    /* ================= MAIN CONTAINER ================= */
    .sales-wrapper {
        min-height: 100vh;
        background: #f1f5f9;
        padding: 2rem 1rem;
        width: 100%;
    }

    .sales-container {
        max-width: 1200px;
        margin: 0 auto;
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        padding: 2rem;
        width: 100%;
    }

    /* ================= ALERTS ================= */
    .alert {
        padding: 1rem 1.5rem;
        border-radius: var(--radius-md);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.95rem;
        border: 1px solid transparent;
    }

    .alert-success {
        background: #dcfce7;
        border-color: #86efac;
        color: #166534;
    }

    .alert-error {
        background: #fee2e2;
        border-color: #fecaca;
        color: #991b1b;
    }

    /* ================= HEADER SECTION ================= */
    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid var(--border);
        flex-wrap: wrap;
        gap: 1rem;
    }

    .customer-info h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-main);
        margin: 0 0 0.25rem;
        word-break: break-word;
    }

    .customer-meta {
        color: var(--text-muted);
        font-size: 0.9rem;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .balance-badge {
        padding: 0.75rem 1.5rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 1rem;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .balance-badge.due {
        background: #fee2e2;
        color: #b91c1c;
    }

    .balance-badge.advance {
        background: #dcfce7;
        color: #166534;
    }

    .balance-badge.zero {
        background: #f3f4f6;
        color: #4b5563;
    }

    /* ================= TABS ================= */
    .tabs-container {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .tab {
        flex: 1;
        min-width: 150px;
        padding: 1rem;
        text-align: center;
        border-radius: var(--radius-md);
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid var(--border);
    }

    .tab.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .tab.inactive {
        background: var(--bg-light);
        color: var(--text-muted);
    }

    .tab.inactive:hover {
        background: #e2e8f0;
    }

    /* ================= TABLE CONTAINER ================= */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        margin-bottom: 1.5rem;
    }

    .sales-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

    .sales-table thead tr {
        background: #1f2937;
        color: white;
    }

    .sales-table th {
        padding: 1rem;
        text-align: left;
        font-size: 0.9rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .sales-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    .sales-table tbody tr:hover {
        background: var(--bg-light);
    }

    .invoice-link {
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
    }

    .invoice-link:hover {
        text-decoration: underline;
    }

    /* ================= STATUS BADGES ================= */
    .status-badge {
        display: inline-block;
        padding: 0.35rem 1rem;
        border-radius: 2rem;
        font-size: 0.8rem;
        font-weight: 600;
        text-align: center;
        white-space: nowrap;
    }

    .status-paid {
        background: #dcfce7;
        color: #166534;
    }

    .status-partial {
        background: #fef3c7;
        color: #92400e;
    }

    .status-emi {
        background: #e0e7ff;
        color: #3730a3;
    }

    .status-unpaid {
        background: #fee2e2;
        color: #991b1b;
    }

    /* ================= ACTION BUTTONS ================= */
    .action-group {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .action-btn {
        padding: 0.5rem 0.75rem;
        border-radius: var(--radius-sm);
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .action-btn.view {
        background: #e0f2fe;
        color: #0369a1;
    }

    .action-btn.view:hover {
        background: #bae6fd;
    }

    .action-btn.print {
        background: #dcfce7;
        color: #166534;
    }

    .action-btn.print:hover {
        background: #bbf7d0;
    }

    .action-btn.edit {
        background: #fef3c7;
        color: #92400e;
    }

    .action-btn.edit:hover {
        background: #fde68a;
    }

    .action-btn.delete {
        background: #fee2e2;
        color: #dc2626;
    }

    .action-btn.delete:hover {
        background: #fecaca;
    }

    /* ================= EMPTY STATE ================= */
    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
        color: var(--text-muted);
    }

    .empty-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .empty-text {
        margin-bottom: 1.5rem;
    }

    .create-btn {
        display: inline-block;
        background: var(--primary);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius-md);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s;
    }

    .create-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    /* ================= PAGINATION ================= */
    .pagination-wrapper {
        margin-top: 1.5rem;
        display: flex;
        justify-content: center;
    }

    .pagination {
        display: flex;
        gap: 0.25rem;
        flex-wrap: wrap;
        list-style: none;
        padding: 0;
    }

    .page-link {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        color: var(--text-main);
        text-decoration: none;
        transition: all 0.2s;
        background: white;
    }

    .page-link:hover {
        border-color: var(--primary);
        background: var(--bg-light);
    }

    .page-item.active .page-link {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    /* ================= BACK BUTTON ================= */
    .back-btn-wrapper {
        margin-top: 2rem;
        text-align: center;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #f3f4f6;
        color: #4b5563;
        padding: 0.75rem 2rem;
        border-radius: var(--radius-md);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s;
    }

    .back-btn:hover {
        background: #e5e7eb;
        transform: translateY(-2px);
        box-shadow: var(--shadow-sm);
    }

    /* ================= RESPONSIVE BREAKPOINTS ================= */
    
    /* Large Desktop (1200px and above) */
    @media (min-width: 1200px) {
        .sales-container {
            max-width: 1200px;
        }
    }

    /* Desktop (992px to 1199px) */
    @media (max-width: 1199px) {
        .sales-container {
            max-width: 95%;
            padding: 1.75rem;
        }

        .customer-info h1 {
            font-size: 1.6rem;
        }
    }

    /* Tablet (768px to 991px) */
    @media (max-width: 991px) {
        .sales-wrapper {
            padding: 1.5rem 0.75rem;
        }

        .sales-container {
            padding: 1.5rem;
        }

        .header-section {
            flex-direction: column;
            align-items: flex-start;
        }

        .balance-badge {
            align-self: flex-start;
        }

        .customer-info h1 {
            font-size: 1.5rem;
        }

        .tabs-container {
            flex-direction: row;
        }

        .tab {
            min-width: 120px;
            padding: 0.875rem;
        }
    }

    /* Mobile Landscape (576px to 767px) */
    @media (max-width: 767px) {
        .sales-wrapper {
            padding: 1rem 0.5rem;
        }

        .sales-container {
            padding: 1.25rem;
        }

        .customer-info h1 {
            font-size: 1.35rem;
        }

        .customer-meta {
            flex-direction: column;
            gap: 0.25rem;
        }

        .balance-badge {
            width: 100%;
            justify-content: center;
            font-size: 0.9rem;
            padding: 0.6rem 1rem;
        }

        .tabs-container {
            flex-direction: column;
        }

        .tab {
            width: 100%;
        }

        .table-responsive {
            margin-bottom: 1rem;
        }

        .sales-table {
            min-width: 800px;
        }

        .sales-table th,
        .sales-table td {
            padding: 0.75rem;
            font-size: 0.85rem;
        }

        .action-group {
            flex-direction: column;
        }

        .action-btn {
            width: 100%;
            justify-content: center;
        }

        .empty-state {
            padding: 2.5rem 1rem;
        }

        .empty-icon {
            font-size: 3rem;
        }

        .empty-title {
            font-size: 1.1rem;
        }

        .back-btn {
            width: 100%;
            justify-content: center;
            padding: 0.75rem 1rem;
        }
    }

    /* Mobile Portrait (up to 575px) */
    @media (max-width: 575px) {
        .sales-container {
            padding: 1rem;
        }

        .customer-info h1 {
            font-size: 1.25rem;
        }

        .customer-meta {
            font-size: 0.8rem;
        }

        .balance-badge {
            font-size: 0.85rem;
            padding: 0.5rem 0.75rem;
        }

        .alert {
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
        }

        .sales-table th,
        .sales-table td {
            padding: 0.6rem;
            font-size: 0.8rem;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            font-size: 0.7rem;
        }

        .page-link {
            padding: 0.4rem 0.75rem;
            font-size: 0.85rem;
        }
    }

    /* Extra Small Devices (up to 360px) */
    @media (max-width: 360px) {
        .sales-container {
            padding: 0.75rem;
        }

        .customer-info h1 {
            font-size: 1.1rem;
        }

        .customer-meta {
            font-size: 0.75rem;
        }

        .balance-badge {
            font-size: 0.8rem;
            padding: 0.4rem 0.6rem;
        }

        .sales-table th,
        .sales-table td {
            padding: 0.5rem;
            font-size: 0.75rem;
        }

        .action-btn {
            padding: 0.4rem 0.5rem;
            font-size: 0.7rem;
        }
    }

    /* Print Styles */
    @media print {
        .tabs-container,
        .action-group,
        .back-btn-wrapper,
        .pagination-wrapper,
        .alert {
            display: none !important;
        }

        .sales-table {
            border: 1px solid #000;
        }

        .sales-table th {
            background: #f0f0f0 !important;
            color: #000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .status-badge {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<div class="sales-wrapper">
    <div class="sales-container">
        {{-- Success/Error Messages --}}
        @if (session('success'))
            <div class="alert alert-success">
                <span style="font-size: 20px;">✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                <span style="font-size: 20px;">❌</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Header with Customer Info and Balance --}}
        <div class="header-section">
            <div class="customer-info">
                <h1>🧾 {{ $customer->name }}</h1>
                <div class="customer-meta">
                    <span>📱 {{ $customer->mobile ?? 'N/A' }}</span>
                    <span>✉️ {{ $customer->email ?? 'N/A' }}</span>
                    <span>🆔 #{{ $customer->id }}</span>
                </div>
            </div>

            {{-- Balance Badge --}}
            @php
                $balance = $customer->open_balance ?? 0;
                $balanceClass = $balance > 0 ? 'due' : ($balance < 0 ? 'advance' : 'zero');
            @endphp
            <div class="balance-badge {{ $balanceClass }}">
                <span>💰</span>
                Balance: ₹ {{ number_format($balance, 2) }}
            </div>
        </div>

        {{-- Tabs for switching between views --}}
        <div class="tabs-container">
            <a href="{{ route('customers.sales', $customer->id) }}" class="tab active">
                📋 Invoices ({{ $sales->total() }})
            </a>
            <a href="{{ route('customers.payments', $customer->id) }}" class="tab inactive">
                💳 Payments
            </a>
        </div>

        {{-- Invoices Table --}}
        <div class="table-responsive">
            <table class="sales-table">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Date</th>
                        <th class="text-right">Total</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" colspan="3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php $__col = $sales; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $sale)
                        @php
                            $statusClass = match ($sale->payment_status) {
                                'paid' => 'status-paid',
                                'partial' => 'status-partial',
                                'emi' => 'status-emi',
                                'unpaid' => 'status-unpaid',
                                default => 'status-unpaid',
                            };
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('sales.show', $sale->id) }}" class="invoice-link">
                                    #{{ $sale->invoice_no }}
                                </a>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</td>
                            <td class="text-right" style="font-weight: 700; color: #059669;">
                                ₹ {{ number_format($sale->grand_total, 2) }}
                            </td>
                            <td class="text-center">
                                <span class="status-badge {{ $statusClass }}">
                                    {{ strtoupper($sale->payment_status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="action-group">
                                    {{-- VIEW --}}
                                    <a href="{{ route('sales.show', $sale->id) }}" class="action-btn view" title="View Invoice">
                                        👁️ View
                                    </a>

                                    {{-- PRINT --}}
                                    <a href="{{ route('sales.invoice', $sale->id) }}" target="_blank" class="action-btn print" title="Print Invoice">
                                        🖨️ Print
                                    </a>

                                    {{-- EDIT --}}
                                    <a href="{{ route('sales.edit', $sale->id) }}" class="action-btn edit" title="Edit Invoice">
                                        ✏️ Edit
                                    </a>

                                    {{-- DELETE --}}
                                    <form method="POST" action="{{ route('sales.destroy', $sale->id) }}"
                                        onsubmit="return confirm('⚠️ Delete this invoice?\n\nInvoice #: {{ $sale->invoice_no }}\nAmount: ₹{{ number_format($sale->grand_total, 2) }}\n\nThis will also delete all related payments. Are you sure?')"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete" title="Delete Invoice">
                                            🗑️ Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
@else
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <div class="empty-icon">📭</div>
                                    <div class="empty-title">No invoices found</div>
                                    <div class="empty-text">Create a new invoice for this customer</div>
                                    <a href="{{ route('sales.create') }}?customer_id={{ $customer->id }}&customer_name={{ urlencode($customer->name) }}"
                                        class="create-btn">
                                        ➕ Create New Invoice
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if (method_exists($sales, 'links') && $sales->hasPages())
            <div class="pagination-wrapper">
                {{ $sales->links() }}
            </div>
        @endif

        {{-- Back Button --}}
        <div class="back-btn-wrapper">
            <a href="{{ route('customers.index') }}" class="back-btn">
                ← Back to Customers
            </a>
        </div>
    </div>
</div>

<script>
    // Print shortcut (Ctrl+P)
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            window.print();
        }
    });

    // Handle window resize for responsive adjustments
    window.addEventListener('resize', function() {
        // Any dynamic adjustments can go here
    });
</script>
@endsection