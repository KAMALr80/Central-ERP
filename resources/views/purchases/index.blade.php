@extends('layouts.app')

@section('page-title', 'Purchase Management')

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
        --text-main: #111827;
        --text-muted: #6b7280;
        --border: #e5e7eb;
        --bg-light: #f9fafb;
        --bg-white: #ffffff;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --shadow-xl: 0 10px 25px rgba(0, 0, 0, 0.08);
        --radius-sm: 6px;
        --radius-md: 8px;
        --radius-lg: 12px;
        --radius-xl: 16px;
        --radius-2xl: 18px;
        --font-sans: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: #f3f4f6;
        font-family: var(--font-sans);
        color: var(--text-main);
        line-height: 1.5;
    }

    /* ================= MAIN CONTAINER ================= */
    .purchase-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
        padding: clamp(16px, 3vw, 30px) clamp(12px, 2vw, 20px);
        width: 100%;
    }

    .container {
        max-width: 1600px;
        margin: 0 auto;
        width: 100%;
    }

    /* ================= HEADER ================= */
    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .header-left {
        flex: 1;
        min-width: 280px;
    }

    .header-title {
        margin: 0;
        font-size: clamp(24px, 5vw, 28px);
        font-weight: 800;
        color: var(--text-main);
        word-break: break-word;
    }

    .header-subtitle {
        margin-top: 6px;
        color: var(--text-muted);
        font-size: clamp(14px, 3vw, 16px);
        word-break: break-word;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
        padding: 10px 18px;
        border-radius: var(--radius-md);
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    /* ================= ALERTS ================= */
    .alert {
        padding: 15px;
        border-radius: var(--radius-md);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        word-break: break-word;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .alert-icon {
        font-size: 20px;
    }

    /* ================= STATISTICS CARDS ================= */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        padding: 25px;
        border-radius: var(--radius-lg);
        color: white;
        transition: transform 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    .stat-card.blue {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stat-card.pink {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .stat-card.purple {
        background: linear-gradient(135deg, #5f2c82 0%, #49a09d 100%);
    }

    .stat-card.orange {
        background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);
    }

    .stat-label {
        font-size: 14px;
        opacity: 0.9;
        margin-bottom: 5px;
        word-break: break-word;
    }

    .stat-value {
        font-size: clamp(28px, 5vw, 32px);
        font-weight: 800;
        line-height: 1.2;
        word-break: break-word;
    }

    /* ================= FILTER CARD ================= */
    .filter-card {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border);
    }

    .filter-form {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr auto auto;
        gap: 10px;
        align-items: center;
    }

    @media (max-width: 1200px) {
        .filter-form {
            grid-template-columns: 1fr 1fr 1fr 1fr;
        }
    }

    @media (max-width: 992px) {
        .filter-form {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 576px) {
        .filter-form {
            grid-template-columns: 1fr;
        }
    }

    .filter-input {
        padding: 10px;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 14px;
        transition: all 0.2s ease;
        width: 100%;
    }

    .filter-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .filter-select {
        padding: 10px;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 14px;
        background: white;
        cursor: pointer;
        width: 100%;
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .filter-btn {
        background: var(--primary);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: var(--radius-md);
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .filter-btn:hover {
        background: var(--primary-dark);
    }

    .filter-clear {
        background: var(--text-muted);
        color: white;
        padding: 10px 20px;
        border-radius: var(--radius-md);
        text-decoration: none;
        font-weight: 600;
        text-align: center;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .filter-clear:hover {
        background: #4b5563;
    }

    /* ================= TABLE CARD ================= */
    .table-card {
        background: var(--bg-white);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-xl);
        overflow: hidden;
        width: 100%;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        width: 100%;
    }

    .purchase-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1200px;
    }

    .purchase-table thead tr {
        background: var(--bg-light);
        border-bottom: 2px solid var(--border);
    }

    .purchase-table th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        color: #374151;
        font-size: clamp(12px, 2vw, 13px);
        white-space: nowrap;
    }

    .purchase-table th.right {
        text-align: right;
    }

    .purchase-table th.center {
        text-align: center;
    }

    .purchase-table td {
        padding: 15px;
        border-bottom: 1px solid var(--border);
        font-size: clamp(13px, 2.2vw, 14px);
        transition: background 0.2s;
        white-space: nowrap;
    }

    .purchase-table td.right {
        text-align: right;
    }

    .purchase-table td.center {
        text-align: center;
    }

    .purchase-table tbody tr:hover td {
        background: var(--bg-light);
    }

    /* ================= INVOICE LINK ================= */
    .invoice-link {
        color: var(--primary);
        text-decoration: none;
        font-family: monospace;
        font-weight: 600;
        transition: color 0.2s;
    }

    .invoice-link:hover {
        color: var(--primary-dark);
        text-decoration: underline;
    }

    /* ================= PRODUCT INFO ================= */
    .product-name {
        font-weight: 600;
        word-break: break-word;
    }

    .product-code {
        font-size: 12px;
        color: var(--text-muted);
        word-break: break-word;
    }

    /* ================= STATUS BADGES ================= */
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-badge.completed {
        background: #dcfce7;
        color: #166534;
    }

    .status-badge.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-badge.cancelled {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-badge.paid {
        background: #dcfce7;
        color: #166534;
    }

    /* ================= ACTION BUTTONS ================= */
    .action-group {
        display: flex;
        gap: 8px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .action-btn {
        color: var(--text-main);
        text-decoration: none;
        padding: 5px;
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        transform: scale(1.1);
    }

    .action-btn.view {
        color: var(--primary);
    }

    .action-btn.edit {
        color: #f59e0b;
    }

    .action-btn.delete {
        color: var(--danger);
        background: none;
        border: none;
        cursor: pointer;
        font-size: inherit;
        padding: 5px;
    }

    /* ================= EMPTY STATE ================= */
    .empty-state {
        padding: 50px;
        text-align: center;
        color: var(--text-muted);
    }

    .empty-icon {
        font-size: 48px;
        margin-bottom: 10px;
    }

    .empty-title {
        font-size: 18px;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .empty-link {
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
    }

    .empty-link:hover {
        text-decoration: underline;
    }

    /* ================= PAGINATION ================= */
    .pagination-wrapper {
        padding: 15px;
        background: #f9fafb;
        border-top: 1px solid var(--border);
    }

    .pagination-info {
        color: var(--text-muted);
        font-size: 14px;
        word-break: break-word;
    }

    .pagination {
        display: flex;
        gap: 5px;
        list-style: none;
        padding: 0;
        margin: 0;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .pagination li a,
    .pagination li span {
        display: inline-block;
        padding: 8px 12px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        color: #374151;
        text-decoration: none;
        transition: all 0.2s;
        font-size: 14px;
    }

    .pagination li.active span {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
    }

    .pagination li a:hover {
        background: var(--bg-light);
        border-color: var(--text-muted);
    }

    /* ================= RESPONSIVE BREAKPOINTS ================= */
    
    /* Large Desktop (1200px and above) */
    @media (min-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(4, 1fr);
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
        .header-section {
            flex-direction: column;
            align-items: flex-start;
        }

        .btn-primary {
            width: 100%;
            justify-content: center;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Mobile Landscape (576px to 767px) */
    @media (max-width: 767px) {
        .purchase-page {
            padding: 15px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .stat-card {
            padding: 20px;
        }

        .stat-value {
            font-size: 28px;
        }

        .action-group {
            flex-direction: column;
        }

        .pagination-wrapper {
            flex-direction: column;
            gap: 15px;
        }

        .pagination {
            justify-content: center;
        }
    }

    /* Mobile Portrait (up to 575px) */
    @media (max-width: 575px) {
        .purchase-page {
            padding: 12px;
        }

        .header-title {
            font-size: 24px;
        }

        .header-subtitle {
            font-size: 14px;
        }

        .stat-card {
            padding: 18px;
        }

        .stat-value {
            font-size: 26px;
        }

        .stat-label {
            font-size: 13px;
        }

        .filter-card {
            padding: 15px;
        }

        .filter-input,
        .filter-select,
        .filter-btn,
        .filter-clear {
            padding: 8px;
            font-size: 13px;
        }

        .purchase-table {
            min-width: 1000px;
        }

        .purchase-table th,
        .purchase-table td {
            padding: 12px 10px;
            font-size: 12px;
        }

        .status-badge {
            padding: 3px 8px;
            font-size: 11px;
        }

        .pagination li a,
        .pagination li span {
            padding: 6px 10px;
            font-size: 12px;
        }
    }

    /* Extra Small Devices (up to 360px) */
    @media (max-width: 360px) {
        .purchase-page {
            padding: 10px;
        }

        .header-title {
            font-size: 22px;
        }

        .stat-value {
            font-size: 24px;
        }

        .stat-card {
            padding: 16px;
        }

        .filter-card {
            padding: 12px;
        }

        .purchase-table {
            min-width: 900px;
        }

        .purchase-table th,
        .purchase-table td {
            padding: 10px 8px;
            font-size: 11px;
        }

        .status-badge {
            padding: 2px 6px;
            font-size: 10px;
        }

        .action-group {
            gap: 5px;
        }

        .action-btn {
            padding: 3px;
        }

        .pagination li a,
        .pagination li span {
            padding: 5px 8px;
            font-size: 11px;
        }

        .pagination-info {
            font-size: 12px;
        }
    }

    /* Print Styles */
    @media print {
        .btn-primary,
        .filter-card,
        .action-group,
        .pagination {
            display: none !important;
        }

        .stat-card {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            border: 1px solid #000;
        }

        .status-badge {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .purchase-table {
            border: 1px solid #000;
        }

        .purchase-table th {
            background: #f0f0f0 !important;
        }
    }
</style>

<div class="purchase-page">
    <div class="container">
        {{-- Page Header --}}
        <div class="header-section">
            <div class="header-left">
                <h2 class="header-title">
                    🛒 Purchase Management
                </h2>
                <p class="header-subtitle">
                    Manage purchases, track payments, and monitor stock
                </p>
            </div>
            @if (auth()->user()->hasPermission('create_purchases'))
                <a href="{{ route('purchases.create') }}" class="btn-primary">
                    ➕ New Purchase
                </a>
            @endif
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success">
                <span class="alert-icon">✅</span>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <span class="alert-icon">❌</span>
                {{ session('error') }}
            </div>
        @endif

        {{-- Statistics Cards --}}
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-label">Total Products</div>
                <div class="stat-value">{{ $totalProducts ?? 0 }}</div>
            </div>
            
            <div class="stat-card pink">
                <div class="stat-label">Total Purchase Amount</div>
                <div class="stat-value">₹ {{ number_format($totalPurchaseAmount ?? 0, 2) }}</div>
            </div>
            
            <div class="stat-card purple">
                <div class="stat-label">Low Stock Items</div>
                <div class="stat-value">{{ $lowStockProducts->count() ?? 0 }}</div>
            </div>
            
            <div class="stat-card orange">
                <div class="stat-label">Total Purchases</div>
                <div class="stat-value">{{ $purchases->total() }}</div>
            </div>
        </div>

        {{-- Search and Filter Bar --}}
        <div class="filter-card">
            <form method="GET" action="{{ route('purchases.index') }}" class="filter-form">
                <input type="text" name="search" placeholder="Search by invoice, product, supplier..." 
                    value="{{ request('search') }}" class="filter-input">
                
                <input type="date" name="date_from" placeholder="From Date" value="{{ request('date_from') }}"
                    class="filter-input">
                
                <input type="date" name="date_to" placeholder="To Date" value="{{ request('date_to') }}"
                    class="filter-input">
                
                <select name="status" class="filter-select">
                    <option value="">All Status</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                
                <button type="submit" class="filter-btn">
                    🔍 Search
                </button>
                
                @if(request()->anyFilled(['search', 'date_from', 'date_to', 'status']))
                    <a href="{{ route('purchases.index') }}" class="filter-clear">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- Purchases Table --}}
        <div class="table-card">
            <div class="table-responsive">
                <table class="purchase-table">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Supplier</th>
                            <th class="right">Quantity</th>
                            <th class="right">Unit Price</th>
                            <th class="right">Total</th>
                            <th class="center">Status</th>
                            <th class="center">Payment</th>
                            <th class="center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $__col = $purchases; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $purchase)
                        <tr>
                            <td>
                                @if(auth()->user()->hasPermission('view_purchases'))
                                    <a href="{{ route('purchases.show', $purchase->id) }}" class="invoice-link">
                                        {{ $purchase->invoice_number }}
                                    </a>
                                @else
                                    {{ $purchase->invoice_number }}
                                @endif
                            </td>
                            <td>{{ $purchase->purchase_date->format('d M Y') }}</td>
                            <td>
                                <div class="product-name">{{ $purchase->product->name ?? 'N/A' }}</div>
                                <div class="product-code">Code: {{ $purchase->product->product_code ?? 'N/A' }}</div>
                            </td>
                            <td>{{ $purchase->supplier_name ?? '-' }}</td>
                            <td class="right">{{ $purchase->quantity }}</td>
                            <td class="right">₹ {{ number_format($purchase->price, 2) }}</td>
                            <td class="right"><strong>₹ {{ number_format($purchase->grand_total, 2) }}</strong></td>
                            <td class="center">
                                <span class="status-badge {{ $purchase->status }}">
                                    {{ ucfirst($purchase->status) }}
                                </span>
                            </td>
                            <td class="center">
                                <span class="status-badge {{ $purchase->payment_status }}">
                                    {{ ucfirst($purchase->payment_status) }}
                                </span>
                            </td>
                            <td class="center">
                                <div class="action-group">
                                    @if (auth()->user()->hasPermission('view_purchases'))
                                        <a href="{{ route('purchases.show', $purchase->id) }}" class="action-btn view" title="View">👁️</a>
                                    @endif

                                    @if (auth()->user()->hasPermission('edit_purchases'))
                                        <a href="{{ route('purchases.edit', $purchase->id) }}" class="action-btn edit" title="Edit">✏️</a>
                                    @endif

                                    @if (auth()->user()->hasPermission('delete_purchases'))
                                        <form method="POST" action="{{ route('purchases.destroy', $purchase->id) }}" 
                                            onsubmit="return confirm('Delete this purchase? This will restore stock!')"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn delete" title="Delete">🗑️</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
@else
                        <tr>
                            <td colspan="10">
                                <div class="empty-state">
                                    <div class="empty-icon">📭</div>
                                    <div class="empty-title">No purchases found</div>
                                    @if (auth()->user()->hasPermission('create_purchases'))
                                        <a href="{{ route('purchases.create') }}" class="empty-link">
                                            Create your first purchase →
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="pagination-wrapper">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                    <div class="pagination-info">
                        Showing {{ $purchases->firstItem() ?? 0 }} to {{ $purchases->lastItem() ?? 0 }} of {{ $purchases->total() }} results
                    </div>
                    <div>
                        {{ $purchases->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection