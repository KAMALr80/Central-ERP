{{-- resources/views/agent/deliveries/history.blade.php --}}
@extends('layouts.app')

@section('title', 'Delivery History')

@section('content')
    <style>
        .history-page {
            padding: 20px;
            background: #f8fafc;
            min-height: 100vh;
        }

        /* Header */
        .page-header {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            border-radius: 20px;
            padding: 25px 30px;
            margin-bottom: 25px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .header-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 5px 0;
        }

        .header-subtitle {
            font-size: 13px;
            opacity: 0.8;
            margin: 0;
        }

        .stats-badge {
            background: rgba(255, 255, 255, 0.15);
            padding: 10px 20px;
            border-radius: 40px;
            text-align: center;
        }

        .stats-badge .count {
            font-size: 24px;
            font-weight: 700;
            line-height: 1;
        }

        .stats-badge .label {
            font-size: 11px;
            opacity: 0.7;
        }

        /* Stats Cards */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 15px;
            border: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .stat-info {
            flex: 1;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
            line-height: 1.2;
        }

        .stat-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Filter Card */
        .filter-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid #e5e7eb;
        }

        /* Table Card */
        .table-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .table-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e5e7eb;
            background: #fafbfc;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .table-header h5 {
            font-size: 14px;
            font-weight: 600;
            margin: 0;
        }

        .delivery-table {
            width: 100%;
            border-collapse: collapse;
        }

        .delivery-table th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            background: #fafbfc;
            border-bottom: 1px solid #e5e7eb;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .delivery-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
            vertical-align: middle;
        }

        .delivery-table tr:hover {
            background: #f8fafc;
        }

        .shipment-link {
            font-weight: 600;
            color: #1e293b;
            text-decoration: none;
        }

        .shipment-link:hover {
            color: #3b82f6;
        }

        .amount {
            font-weight: 700;
            color: #10b981;
        }

        .badge-delivered {
            background: #d1fae5;
            color: #065f46;
            padding: 4px 10px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-view {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            background: #f1f5f9;
            color: #475569;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-view:hover {
            background: #3b82f6;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            background: #f1f5f9;
            border-radius: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 32px;
            color: #94a3b8;
        }

        .pagination-wrapper {
            padding: 15px 20px;
            border-top: 1px solid #e5e7eb;
        }

        @media (max-width: 992px) {
            .stats-row {
                grid-template-columns: repeat(2, 1fr);
            }

            .delivery-table {
                min-width: 700px;
            }
        }

        @media (max-width: 768px) {
            .stats-row {
                grid-template-columns: 1fr;
            }

            .page-header {
                flex-direction: column;
                text-align: center;
            }

            .header-left {
                flex-direction: column;
            }
        }
    </style>

    <div class="history-page">
        <!-- Header -->
        <div class="page-header">
            <div class="header-left">
                <div class="header-icon"><i class="fas fa-history"></i></div>
                <div>
                    <h1 class="header-title">Delivery History</h1>
                    <p class="header-subtitle">
                        <i class="fas fa-calendar-alt me-1"></i>
                        {{ $startDate ?? now()->startOfMonth()->format('d M Y') }} -
                        {{ $endDate ?? now()->format('d M Y') }}
                    </p>
                </div>
            </div>
            <div class="stats-badge">
                <div class="count">{{ $totalDeliveries ?? ($deliveries->total() ?? 0) }}</div>
                <div class="label">Total Deliveries</div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="fas fa-check-circle"></i></div>
                <div class="stat-info">
                    <div class="stat-value">{{ $totalDeliveries ?? $deliveries->count() }}</div>
                    <div class="stat-label">Total Deliveries</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="fas fa-rupee-sign"></i></div>
                <div class="stat-info">
                    <div class="stat-value">₹{{ number_format($totalEarnings ?? 0, 2) }}</div>
                    <div class="stat-label">Total Earnings</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="fas fa-chart-line"></i></div>
                <div class="stat-info">
                    <div class="stat-value">₹{{ number_format($avgPerDelivery ?? 0, 2) }}</div>
                    <div class="stat-label">Average per Delivery</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="fas fa-calendar-week"></i></div>
                <div class="stat-info">
                    <div class="stat-value">{{ $bestDay ?? 'N/A' }}</div>
                    <div class="stat-label">Best Day</div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-card">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Period</label>
                    <select name="period" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Last Month
                        </option>
                        <option value="quarter" {{ request('period') == 'quarter' ? 'selected' : '' }}>This Quarter
                        </option>
                        <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>This Year</option>
                        <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Custom</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">From</label>
                    <input type="date" name="start_date" class="form-control form-control-sm"
                        value="{{ request('start_date', $startDate ?? '') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">To</label>
                    <input type="date" name="end_date" class="form-control form-control-sm"
                        value="{{ request('end_date', $endDate ?? '') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Shipment #, Customer..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Sort By</label>
                    <select name="sort" class="form-select form-select-sm">
                        <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>Date (Newest)
                        </option>
                        <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Date (Oldest)
                        </option>
                        <option value="amount_desc" {{ request('sort') == 'amount_desc' ? 'selected' : '' }}>Amount (High
                            to Low)</option>
                        <option value="amount_asc" {{ request('sort') == 'amount_asc' ? 'selected' : '' }}>Amount (Low to
                            High)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-search me-1"></i> Apply
                        </button>
                        <a href="{{ route('agent.deliveries.history') }}" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="fas fa-undo me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Deliveries Table -->
        <div class="table-card">
            <div class="table-header">
                <h5><i class="fas fa-list me-2 text-primary"></i> Delivery Records</h5>
                <div>
                    <a href="{{ route('agent.earnings.export', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                        class="btn-outline">
                        <i class="fas fa-download me-1"></i> Export
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="delivery-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Shipment #</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $__col = $deliveries; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $delivery)
                            <tr>
                                <td class="text-nowrap">
                                    <i class="fas fa-calendar-alt text-muted me-1" style="font-size: 11px;"></i>
                                    {{ $delivery->actual_delivery_date?->format('d M Y') ?? 'N/A' }}
                                    <div class="small text-muted">
                                        {{ $delivery->actual_delivery_date?->format('h:i A') ?? '' }}</div>
                                </td>
                                <td>
                                    <a href="{{ route('agent.delivery.show', $delivery->id) }}" class="shipment-link">
                                        #{{ $delivery->shipment_number }}
                                    </a>
                                </td>
                                <td>
                                    <strong>{{ $delivery->receiver_name }}</strong>
                                </td>
                                <td>
                                    <i class="fas fa-phone text-muted me-1"></i>
                                    {{ $delivery->receiver_phone }}
                                </td>
                                <td class="small text-muted">
                                    {{ \Illuminate\Support\Str::limit($delivery->shipping_address, 40) }}, {{ $delivery->city }}
                                </td>
                                <td class="amount">₹{{ number_format($delivery->declared_value ?? 0, 2) }}</td>
                                <td>
                                    <span class="badge-delivered">
                                        <i class="fas fa-check-circle"></i> Delivered
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('agent.delivery.show', $delivery->id) }}" class="btn-view">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
@else
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <i class="fas fa-history"></i>
                                        </div>
                                        <h5>No Delivery History</h5>
                                        <p class="text-muted">You haven't completed any deliveries in this period.</p>
                                        <a href="{{ route('agent.dashboard') }}" class="btn btn-primary mt-3">
                                            <i class="fas fa-home me-2"></i> Back to Dashboard
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if ($deliveries->hasPages())
                <div class="pagination-wrapper">
                    {{ $deliveries->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        .btn-outline {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            background: white;
            border: 1px solid #e5e7eb;
            color: #475569;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-outline:hover {
            border-color: #3b82f6;
            color: #3b82f6;
        }

        .form-select-sm,
        .form-control-sm {
            font-size: 12px;
            padding: 6px 12px;
        }

        .form-label {
            font-size: 11px;
            margin-bottom: 4px;
            color: #64748b;
        }

        .pagination {
            margin: 0;
        }

        .text-nowrap {
            white-space: nowrap;
        }
    </style>

    @push('scripts')
        <script>
            // Auto-submit on period change
            document.querySelectorAll('select[name="period"], select[name="sort"]').forEach(select => {
                select.addEventListener('change', function() {
                    this.form.submit();
                });
            });
        </script>
    @endpush
@endsection
