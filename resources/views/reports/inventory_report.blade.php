@extends('layouts.app')

@section('page-title', 'Inventory Report')

@section('content')
    <style>
        /* ================= PROFESSIONAL INVENTORY REPORT STYLES ================= */
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --purple: #8b5cf6;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --bg-light: #f9fafb;
            --bg-white: #ffffff;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
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
        }

        .report-wrapper {
            padding: 2rem 1rem;
            min-height: 100vh;
            width: 100%;
        }

        .report-container {
            max-width: 1600px;
            margin: 0 auto;
            width: 100%;
        }

        .report-header {
            background: var(--bg-white);
            border-radius: var(--radius-xl);
            padding: 1.5rem 2rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header-title h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 0 0.25rem;
            color: var(--text-main);
        }

        .header-title p {
            color: var(--text-muted);
            font-size: 0.875rem;
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
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            border: 1px solid transparent;
            cursor: pointer;
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

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-success:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--bg-light);
            color: var(--text-main);
            border-color: var(--border);
        }

        .btn-secondary:hover {
            background: var(--border);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: var(--bg-white);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            transition: all 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .stat-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--text-muted);
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 0.25rem;
        }

        .stat-sub {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .stat-value.positive {
            color: var(--success);
        }

        .stat-value.negative {
            color: var(--danger);
        }

        .stat-value.warning {
            color: var(--warning);
        }

        .stock-status-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stock-card {
            background: var(--bg-white);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            border: 1px solid var(--border);
            text-align: center;
        }

        .stock-card.low {
            border-left: 4px solid var(--danger);
        }

        .stock-card.normal {
            border-left: 4px solid var(--warning);
        }

        .stock-card.high {
            border-left: 4px solid var(--success);
        }

        .stock-count {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .stock-count.low {
            color: var(--danger);
        }

        .stock-count.normal {
            color: var(--warning);
        }

        .stock-count.high {
            color: var(--success);
        }

        .filter-section {
            background: var(--bg-white);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border);
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: flex-end;
        }

        .filter-group {
            flex: 1;
            min-width: 150px;
        }

        .filter-group label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
            text-transform: uppercase;
        }

        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 0.5rem;
        }

        .table-container {
            background: var(--bg-white);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .table-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
            background: var(--bg-light);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .table-header h2 {
            font-size: 1rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
            min-width: 1000px;
        }

        .data-table thead th {
            background: var(--bg-light);
            padding: 0.75rem 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        .data-table tbody td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .data-table tbody tr:hover {
            background: var(--bg-light);
        }

        .serial-cell {
            width: 60px;
            text-align: center;
            font-weight: 600;
            color: var(--text-muted);
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-low {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-normal {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-high {
            background: #d1fae5;
            color: #065f46;
        }

        .amount {
            font-weight: 600;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .pagination-wrapper {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
        }

        .pagination {
            display: flex;
            gap: 0.25rem;
            flex-wrap: wrap;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .page-item .page-link {
            display: inline-block;
            padding: 0.5rem 0.75rem;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            color: var(--text-main);
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.2s;
            background: white;
        }

        .page-item .page-link:hover {
            border-color: var(--primary);
            background: var(--bg-light);
        }

        .page-item.active .page-link {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }

        .empty-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 0.75rem 1rem;
            border-radius: var(--radius-md);
            background: white;
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            z-index: 1000;
            border-left: 4px solid;
            animation: slideIn 0.3s ease;
        }

        .toast-notification.success {
            border-left-color: var(--success);
        }

        .toast-notification.error {
            border-left-color: var(--danger);
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

        .loading-overlay.active {
            display: flex;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--border);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 768px) {
            .report-wrapper {
                padding: 1rem;
            }

            .report-header {
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

            .filter-form {
                flex-direction: column;
            }

            .filter-group {
                width: 100%;
            }

            .filter-actions {
                width: 100%;
            }

            .filter-actions button {
                flex: 1;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .stock-status-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .table-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media print {

            .header-actions,
            .filter-section,
            .pagination-wrapper,
            .btn {
                display: none !important;
            }
        }
    </style>

    <div class="report-wrapper">
        <div class="report-container">
            <!-- Loading Overlay -->
            <div id="loadingOverlay" class="loading-overlay">
                <div class="spinner"></div>
                <div class="loading-text">Generating PDF...</div>
            </div>

            <!-- Header -->
            <div class="report-header">
                <div class="header-title">
                    <h1>📦 Inventory Report</h1>
                    <p>Complete inventory analytics and stock summary</p>
                </div>

            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Products</div>
                    <div class="stat-value">{{ number_format($stats['total_products']) }}</div>
                    <div class="stat-sub">Total in inventory</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Total Quantity</div>
                    <div class="stat-value">{{ number_format($stats['total_quantity']) }}</div>
                    <div class="stat-sub">Units in stock</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Total Value</div>
                    <div class="stat-value positive">₹{{ number_format($stats['total_value'], 2) }}</div>
                    <div class="stat-sub">Inventory worth</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Avg Price</div>
                    <div class="stat-value">₹{{ number_format($stats['avg_price'], 2) }}</div>
                    <div class="stat-sub">Per product</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Stock Health</div>
                    <div
                        class="stat-value {{ $stats['stock_health'] > 70 ? 'positive' : ($stats['stock_health'] > 40 ? 'warning' : 'negative') }}">
                        {{ $stats['stock_health'] }}%
                    </div>
                    <div class="stat-sub">High stock ratio</div>
                </div>
            </div>

            <!-- Stock Status Cards -->
            <div class="stock-status-grid">
                <div class="stock-card low">
                    <div class="stock-count low">{{ $stats['low_stock_count'] }}</div>
                    <div class="stat-label">Low Stock</div>
                    <div class="stat-sub">≤ 10 units</div>
                </div>
                <div class="stock-card normal">
                    <div class="stock-count normal">{{ $stats['normal_stock_count'] }}</div>
                    <div class="stat-label">Normal Stock</div>
                    <div class="stat-sub">11-30 units</div>
                </div>
                <div class="stock-card high">
                    <div class="stock-count high">{{ $stats['high_stock_count'] }}</div>
                    <div class="stat-label">High Stock</div>
                    <div class="stat-sub">> 30 units</div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form method="GET" action="{{ route('reports.inventory') }}" class="filter-form" id="filterForm">
                    <div class="filter-group">
                        <label>Category</label>
                        <select name="category">
                            <option value="all" {{ $filters['category'] == 'all' ? 'selected' : '' }}>All Categories
                            </option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}" {{ $filters['category'] == $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Stock Status</label>
                        <select name="stock_status">
                            <option value="all" {{ $filters['stock_status'] == 'all' ? 'selected' : '' }}>All Stock
                            </option>
                            <option value="low" {{ $filters['stock_status'] == 'low' ? 'selected' : '' }}>Low Stock
                                (≤10)</option>
                            <option value="normal" {{ $filters['stock_status'] == 'normal' ? 'selected' : '' }}>Normal
                                Stock (11-30)</option>
                            <option value="high" {{ $filters['stock_status'] == 'high' ? 'selected' : '' }}>High Stock
                                (>30)</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Search</label>
                        <input type="text" name="search" value="{{ $filters['search'] }}"
                            placeholder="Name, Code, Category...">
                    </div>

                    <div class="filter-group">
                        <label>Sort By</label>
                        <select name="sort_by">
                            <option value="name" {{ $filters['sort_by'] == 'name' ? 'selected' : '' }}>Name</option>
                            <option value="product_code" {{ $filters['sort_by'] == 'product_code' ? 'selected' : '' }}>Code
                            </option>
                            <option value="quantity" {{ $filters['sort_by'] == 'quantity' ? 'selected' : '' }}>Quantity
                            </option>
                            <option value="price" {{ $filters['sort_by'] == 'price' ? 'selected' : '' }}>Price</option>
                            <option value="category" {{ $filters['sort_by'] == 'category' ? 'selected' : '' }}>Category
                            </option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Order</label>
                        <select name="sort_order">
                            <option value="asc" {{ $filters['sort_order'] == 'asc' ? 'selected' : '' }}>Ascending
                            </option>
                            <option value="desc" {{ $filters['sort_order'] == 'desc' ? 'selected' : '' }}>Descending
                            </option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">Apply Filter</button>
                        <a href="{{ route('reports.inventory') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Inventory Table -->
            <div class="table-container">
                <div class="table-header">
                    <h2>
                        <span>📋</span>
                        Product Inventory
                        <span style="font-weight: normal; color: var(--text-muted);">
                            ({{ $products->total() }} records)
                        </span>
                    </h2>

                    <div class="header-actions">
                        <button onclick="exportReport('csv')" class="btn btn-success">
                            📥 Export CSV
                        </button>
                        <button onclick="exportReport('pdf')" class="btn btn-primary">
                            📄 Export PDF
                        </button>
                        <button onclick="window.print()" class="btn btn-secondary">
                            🖨️ Print
                        </button>
                    </div>
                    <div>
                        <input type="text" id="tableSearch" placeholder="Search in table..."
                            style="padding: 0.5rem 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 0.875rem; width: 200px;">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="data-table" id="inventoryTable">
                        <thead>
                           
                            <th class="serial-cell">#</th>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th class="text-right">Quantity</th>
                            <th class="text-right">Price (₹)</th>
                            <th class="text-right">Stock Value (₹)</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </thead>
                        <tbody>
                            @forelse($products as $index => $product)
                                @php
                                    $serial = ($products->currentPage() - 1) * $products->perPage() + $index + 1;
                                    $stockValue = $product->price * $product->quantity;
                                    $statusClass =
                                        $product->quantity <= 10
                                            ? 'badge-low'
                                            : ($product->quantity <= 30
                                                ? 'badge-normal'
                                                : 'badge-high');
                                    $statusText =
                                        $product->quantity <= 10
                                            ? 'Low Stock'
                                            : ($product->quantity <= 30
                                                ? 'Normal'
                                                : 'High Stock');
                                @endphp
                                <tr>
                                    <td class="serial-cell">{{ $serial }}</td>
                                    <td><strong>{{ $product->product_code }}</strong></td>
                                    <td>
                                        <div class="product-name">{{ $product->name }}</div>
                                        @if ($product->description)
                                            <div class="product-meta"
                                                style="font-size: 0.7rem; color: var(--text-muted);">
                                                {{ \Illuminate\Support\Str::limit($product->description, 40) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            style="background: #e0e7ff; padding: 4px 8px; border-radius: 12px; font-size: 0.7rem;">
                                            {{ $product->category ?? 'Uncategorized' }}
                                        </span>
                                    </td>
                                    <td class="text-right {{ $product->quantity <= 10 ? 'amount-negative' : '' }}">
                                        {{ number_format($product->quantity) }}
                                    </td>
                                    <td class="text-right">₹{{ number_format($product->price, 2) }}</td>
                                    <td class="text-right amount amount-positive">₹{{ number_format($stockValue, 2) }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-buttons"
                                            style="display: flex; gap: 0.25rem; justify-content: center;">
                                            <a href="{{ route('inventory.show', $product->id) }}" class="btn-sm"
                                                style="padding: 0.25rem 0.5rem; background: #e0f2fe; border-radius: 4px; text-decoration: none; color: #0369a1;"
                                                title="View">
                                                👁️
                                            </a>
                                            @if (auth()->user()->hasPermission('edit_inventory'))
                                                <a href="{{ route('inventory.edit', $product->id) }}" class="btn-sm"
                                                    style="padding: 0.25rem 0.5rem; background: #fef3c7; border-radius: 4px; text-decoration: none; color: #92400e;"
                                                    title="Edit">
                                                    ✏️
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">
                                        <div class="empty-state">
                                            <div class="empty-icon">📦</div>
                                            <div class="empty-title">No products found</div>
                                            <div class="empty-text">Try adjusting your filters or add new products</div>
                                            <a href="{{ route('inventory.create') }}" class="btn btn-primary"
                                                style="margin-top: 1rem;">
                                                + Add Product
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($products->count() > 0)
                            <tfoot>
                                <tr style="background: var(--bg-light); font-weight: 600;">
                                    <td colspan="4" class="text-right"><strong>Total:</strong></td>
                                    <td class="text-right"><strong>{{ number_format($stats['total_quantity']) }}</strong>
                                    </td>
                                    <td class="text-right"><strong>-</strong></td>
                                    <td class="text-right amount-positive">
                                        <strong>₹{{ number_format($stats['total_value'], 2) }}</strong>
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                @if ($products->hasPages())
                    <div class="pagination-wrapper">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Get current filter parameters
        function getFilterParams() {
            const params = new URLSearchParams();
            const category = document.querySelector('select[name="category"]')?.value || 'all';
            const stockStatus = document.querySelector('select[name="stock_status"]')?.value || 'all';
            const search = document.querySelector('input[name="search"]')?.value || '';
            const sortBy = document.querySelector('select[name="sort_by"]')?.value || 'name';
            const sortOrder = document.querySelector('select[name="sort_order"]')?.value || 'asc';

            if (category !== 'all') params.append('category', category);
            if (stockStatus !== 'all') params.append('stock_status', stockStatus);
            if (search) params.append('search', search);
            if (sortBy !== 'name') params.append('sort_by', sortBy);
            if (sortOrder !== 'asc') params.append('sort_order', sortOrder);

            return params.toString();
        }

        // Export Report Function
        function exportReport(type) {
            const params = getFilterParams();
            let url = '';

            if (type === 'csv') {
                url = '{{ route('reports.inventory.excel') }}?' + params;
                window.location.href = url;
                showToast('CSV export started!', 'success');
            } else if (type === 'pdf') {
                const loadingOverlay = document.getElementById('loadingOverlay');
                loadingOverlay.classList.add('active');

                url = '{{ route('reports.inventory.pdf') }}?' + params;
                window.open(url, '_blank');

                setTimeout(() => {
                    loadingOverlay.classList.remove('active');
                    showToast('PDF generated successfully!', 'success');
                }, 2000);
            }
        }

        // Table search functionality
        document.getElementById('tableSearch')?.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const table = document.getElementById('inventoryTable');
            const rows = table.getElementsByTagName('tbody')[0]?.getElementsByTagName('tr');

            if (!rows) return;

            for (let row of rows) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            }
        });

        // Toast notification
        function showToast(message, type = 'success') {
            const existingToast = document.querySelector('.toast-notification');
            if (existingToast) existingToast.remove();

            const toast = document.createElement('div');
            toast.className = `toast-notification ${type}`;
            const icon = type === 'success' ? '✅' : (type === 'error' ? '❌' : '⚠️');
            toast.innerHTML = `<span>${icon}</span><span>${message}</span>`;
            document.body.appendChild(toast);

            setTimeout(() => toast.remove(), 3000);
        }

        // Check for session messages
        @if (session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if (session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
    </script>
@endsection
