{{-- resources/views/agent/earnings/index.blade.php --}}
@extends('layouts.app')

@section('title', 'My Earnings')

@section('content')
    <style>
        .earnings-page {
            padding: 20px;
            background: #f8fafc;
            min-height: 100vh;
        }

        /* Header */
        .earnings-header {
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

        .commission-badge {
            background: rgba(255, 255, 255, 0.15);
            padding: 10px 20px;
            border-radius: 40px;
            text-align: center;
        }

        .commission-badge small {
            font-size: 11px;
            opacity: 0.7;
            display: block;
        }

        .commission-badge strong {
            font-size: 18px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 18px;
            border: 1px solid #e5e7eb;
            transition: all 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .stat-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 600;
        }

        .stat-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .stat-value {
            font-size: 26px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .stat-trend {
            font-size: 11px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .trend-up {
            color: #10b981;
        }

        .trend-down {
            color: #ef4444;
        }

        /* Summary Row */
        .summary-row {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .summary-item {
            background: white;
            border-radius: 12px;
            padding: 12px 20px;
            border: 1px solid #e5e7eb;
            flex: 1;
            min-width: 120px;
            text-align: center;
        }

        .summary-value {
            font-size: 20px;
            font-weight: 700;
            color: #10b981;
        }

        .summary-label {
            font-size: 11px;
            color: #64748b;
            margin-top: 5px;
        }

        /* Filter */
        .filter-card {
            background: white;
            border-radius: 16px;
            padding: 15px 20px;
            margin-bottom: 25px;
            border: 1px solid #e5e7eb;
        }

        /* Charts Row */
        .charts-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .chart-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #e5e7eb;
        }

        .chart-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Tables */
        .table-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            margin-bottom: 25px;
        }

        .table-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e5e7eb;
            background: #f8fafc;
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

        .earnings-table {
            width: 100%;
            border-collapse: collapse;
        }

        .earnings-table th {
            padding: 12px 15px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            background: #fafbfc;
            border-bottom: 1px solid #e5e7eb;
        }

        .earnings-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
        }

        .earnings-table tr:hover {
            background: #f8fafc;
        }

        .badge-sm {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 500;
        }

        .badge-fixed {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-percentage {
            background: #dcfce7;
            color: #166534;
        }

        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .btn-sm {
            padding: 5px 12px;
            font-size: 12px;
            border-radius: 20px;
        }

        .btn-outline {
            border: 1px solid #e5e7eb;
            background: white;
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 13px;
            transition: all 0.2s;
        }

        .btn-outline:hover {
            border-color: #10b981;
            color: #10b981;
        }

        @media (max-width: 992px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .charts-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .summary-row {
                flex-direction: column;
            }

            .earnings-table {
                min-width: 600px;
            }
        }
    </style>

    <div class="earnings-page">
        <div class="earnings-header">
            <div class="header-left">
                <div class="header-icon"><i class="fas fa-rupee-sign"></i></div>
                <div>
                    <h1 class="header-title">My Earnings</h1>
                    <p class="header-subtitle">{{ number_format($totalDeliveries ?? 0) }} deliveries •
                        {{ now()->format('F Y') }}</p>
                </div>
            </div>
            <div class="commission-badge">
                <small>Commission Rate</small>
                <strong>
                    @if ($agent->commission_type == 'fixed')
                        ₹{{ $agent->commission_value ?? 50 }}/delivery
                    @else
                        {{ $agent->commission_value ?? 10 }}%
                    @endif
                </strong>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Total Earnings</span>
                    <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="fas fa-rupee-sign"></i></div>
                </div>
                <div class="stat-value">₹{{ number_format($totalEarnings, 2) }}</div>
                <div class="stat-trend {{ ($earningsTrend ?? 0) >= 0 ? 'trend-up' : 'trend-down' }}">
                    <i class="fas fa-arrow-{{ ($earningsTrend ?? 0) >= 0 ? 'up' : 'down' }}"></i>
                    {{ abs($earningsTrend ?? 0) }}% from last month
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">This Month</span>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="fas fa-calendar-alt"></i></div>
                </div>
                <div class="stat-value">₹{{ number_format($monthlyEarnings ?? 0, 2) }}</div>
                <div class="stat-trend">Target: ₹{{ number_format($monthlyTarget ?? 0, 2) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">This Week</span>
                    <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="fas fa-calendar-week"></i></div>
                </div>
                <div class="stat-value">₹{{ number_format($weeklyEarnings ?? 0, 2) }}</div>
                <div class="stat-trend">{{ $deliveriesThisWeek ?? 0 }} deliveries</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Pending Payout</span>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="fas fa-clock"></i></div>
                </div>
                <div class="stat-value">₹{{ number_format($pendingPayout ?? 0, 2) }}</div>
                <div class="stat-trend">Next: {{ $nextPayoutDate ?? 'This Friday' }}</div>
            </div>
        </div>

        <!-- Summary Row -->
        <div class="summary-row">
            <div class="summary-item">
                <div class="summary-value">{{ $totalDeliveries ?? 0 }}</div>
                <div class="summary-label">Total Deliveries</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">₹{{ number_format($avgPerDelivery ?? 0, 2) }}</div>
                <div class="summary-label">Average per Delivery</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $bestDay ?? 'N/A' }}</div>
                <div class="summary-label">Best Day</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $highestEarningDay ?? '₹0' }}</div>
                <div class="summary-label">Highest Day</div>
            </div>
        </div>

        <!-- Filter -->
        <div class="filter-card">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Period</label>
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
                    <label class="form-label small fw-semibold">From</label>
                    <input type="date" name="start_date" class="form-control form-control-sm"
                        value="{{ $startDate }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">To</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fas fa-filter me-1"></i>
                        Apply</button>
                </div>
                <div class="col-md-2">
                  <a href="{{ route('agent.earnings') }}" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
                </div>
            </form>
        </div>

        <!-- Charts Row -->
        <div class="charts-row">
            <div class="chart-card">
                <div class="chart-title"><i class="fas fa-chart-line text-success"></i> Earnings Trend</div>
                <canvas id="earningsChart" height="200"></canvas>
            </div>
            <div class="chart-card">
                <div class="chart-title"><i class="fas fa-chart-pie text-primary"></i> Breakdown</div>
                <canvas id="breakdownChart" height="200"></canvas>
            </div>
        </div>

        <!-- Monthly Summary -->
        <div class="table-card">
            <div class="table-header">
                <h5><i class="fas fa-chart-bar me-2 text-primary"></i> Monthly Summary</h5>
                <small class="text-muted">Last 6 months</small>
            </div>
            <div class="table-responsive">
                <table class="earnings-table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Deliveries</th>
                            <th>Earnings</th>
                            <th>Avg/Delivery</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($monthlySummary as $month)
                            <tr>
                                <td class="fw-semibold">{{ $month['month'] }}</td>
                                <td>{{ $month['shipments'] }}</td>
                                <td class="text-success fw-bold">₹{{ number_format($month['earnings'], 2) }}</td>
                                <td>₹{{ number_format($month['avg_per_delivery'] ?? 0, 2) }}</td>
                                <td><a href="?start_date={{ \Carbon\Carbon::parse($month['month'])->startOfMonth()->toDateString() }}&end_date={{ \Carbon\Carbon::parse($month['month'])->endOfMonth()->toDateString() }}"
                                        class="btn btn-sm btn-outline-primary">View</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="table-card">
            <div class="table-header">
                <h5><i class="fas fa-history me-2 text-primary"></i> Transaction History</h5>
                <div>
                    <a href="{{ route('agent.earnings.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                        class="btn-outline me-2">
                        <i class="fas fa-download"></i> Export
                    </a>
                    <a href="{{ route('agent.earnings.invoice') }}" class="btn-outline">
                        <i class="fas fa-file-invoice"></i> Invoice
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="earnings-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Shipment</th>
                            <th>Customer</th>
                            <th>Order Value</th>
                            <th>Commission</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $__col = $shipmentEarnings; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $item)
                            <tr>
                                <td>{{ $item['date']?->format('d M Y') ?? 'N/A' }}</td>
                                <td><a href="{{ route('agent.delivery.show', $item['shipment']->id) }}"
                                        class="text-decoration-none">#{{ $item['shipment']->shipment_number }}</a></td>
                                <td>{{ $item['shipment']->receiver_name }}</td>
                                <td>₹{{ number_format($item['shipment']->declared_value ?? 0, 2) }}</td>
                                <td class="text-success fw-bold">+ ₹{{ number_format($item['earnings'], 2) }}</td>
                                <td><span
                                        class="badge-sm status-{{ $item['payout_status'] ?? 'pending' }}">{{ ucfirst($item['payout_status'] ?? 'Pending') }}</span>
                                </td>
                            </tr>
                        @endforeach
@else
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted"><i
                                        class="fas fa-receipt fa-2x mb-2 d-block"></i>No earnings data</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new Chart(document.getElementById('earningsChart'), {
                type: 'line',
                data: {
                    labels: {!! json_encode(array_column($dailyEarnings ?? [], 'date')) !!},
                    datasets: [{
                        label: 'Earnings (₹)',
                        data: {!! json_encode(array_column($dailyEarnings ?? [], 'earnings')) !!},
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,0.05)',
                        tension: 0.3,
                        fill: true,
                        pointRadius: 3,
                        pointBackgroundColor: '#10b981'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `₹${ctx.raw}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (v) => '₹' + v
                            }
                        }
                    }
                }
            });

            new Chart(document.getElementById('breakdownChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Base', 'Bonus', 'Incentives', 'Tips'],
                    datasets: [{
                        data: [{{ $baseCommission ?? 80 }}, {{ $bonusEarnings ?? 10 }},
                            {{ $incentives ?? 5 }}, {{ $tips ?? 5 }}
                        ],
                        backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });
    </script>
@endsection
