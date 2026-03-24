{{-- resources/views/agent/performance/index.blade.php --}}
@extends('layouts.app')

@section('title', 'My Performance Dashboard')

@section('content')
    <style>
        .performance-page {
            min-height: 100vh;
            padding: clamp(16px, 3vw, 30px);
            width: 100%;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        /* Header Section */
        .performance-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border-radius: 30px;
            padding: clamp(1.5rem, 4vw, 2rem);
            margin-bottom: 2rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .performance-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .header-content {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .header-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        .header-title {
            font-size: clamp(1.5rem, 5vw, 2rem);
            font-weight: 700;
            margin: 0 0 0.5rem 0;
        }

        .header-subtitle {
            opacity: 0.9;
            font-size: clamp(0.9rem, 2.5vw, 1rem);
        }

        .rating-badge {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            text-align: center;
        }

        .rating-value {
            font-size: 2rem;
            font-weight: 700;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 24px;
            padding: 1.5rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            font-weight: 600;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .stat-trend {
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .trend-up {
            color: #10b981;
        }

        .trend-down {
            color: #ef4444;
        }

        /* Filter Section */
        .filter-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid #e5e7eb;
        }

        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .chart-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .chart-card:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .chart-title {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #1e293b;
        }

        /* Summary Cards */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .summary-card {
            background: linear-gradient(135deg, #f8fafc, #ffffff);
            border-radius: 16px;
            padding: 1rem;
            text-align: center;
            border: 1px solid #e5e7eb;
        }

        .summary-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
        }

        .summary-label {
            font-size: 0.75rem;
            color: #64748b;
        }

        /* Table Styles */
        .table-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .table-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            background: #f8fafc;
        }

        .performance-table {
            width: 100%;
            border-collapse: collapse;
        }

        .performance-table th {
            padding: 1rem 1.5rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.85rem;
            color: #64748b;
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
        }

        .performance-table td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        .performance-table tr:hover {
            background: #f8fafc;
        }

        .rating-stars {
            color: #f59e0b;
            letter-spacing: 2px;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .performance-table th,
            .performance-table td {
                padding: 0.75rem 1rem;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="performance-page">
        <div class="container">
            <!-- Header -->
            <div class="performance-header">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-icon"><i class="fas fa-chart-line"></i></div>
                        <div>
                            <h1 class="header-title">My Performance Dashboard</h1>
                            <p class="header-subtitle">
                                <i class="fas fa-calendar-alt me-2"></i>
                                {{ now()->format('F Y') }} |
                                <i class="fas fa-route ms-2 me-1"></i>
                                {{ number_format($totalDistance ?? 0, 1) }} km traveled
                            </p>
                        </div>
                    </div>
                    <div class="rating-badge">
                        <div class="small">Overall Rating</div>
                        <div class="rating-value">{{ number_format($agent->rating ?? 4.5, 1) }} ★</div>
                        <div class="small">{{ $totalRatings ?? 0 }} reviews</div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-label">Total Shipments</span>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-box"></i>
                        </div>
                    </div>
                    <div class="stat-value">{{ $totalShipments }}</div>
                    <div class="stat-trend {{ $shipmentTrend >= 0 ? 'trend-up' : 'trend-down' }}">
                        <i class="fas fa-arrow-{{ $shipmentTrend >= 0 ? 'up' : 'down' }}"></i>
                        {{ abs($shipmentTrend) }}% from last month
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-label">Completed</span>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="stat-value">{{ $completedShipments }}</div>
                    <div class="stat-trend {{ $completionTrend >= 0 ? 'trend-up' : 'trend-down' }}">
                        <i class="fas fa-arrow-{{ $completionTrend >= 0 ? 'up' : 'down' }}"></i>
                        {{ abs($completionTrend) }}% from last month
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-label">Completion Rate</span>
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                    <div class="stat-value">{{ $completionRate }}%</div>
                    <div class="stat-trend {{ $completionRate >= 95 ? 'trend-up' : 'trend-down' }}">
                        <i class="fas fa-trophy"></i> {{ $completionRate >= 95 ? 'Excellent' : 'Needs Improvement' }}
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-label">Avg Delivery Time</span>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="stat-value">{{ $avgDeliveryTime }} <small class="fs-6">min</small></div>
                    <div class="stat-trend {{ $timeTrend <= 0 ? 'trend-up' : 'trend-down' }}">
                        <i class="fas fa-arrow-{{ $timeTrend <= 0 ? 'down' : 'up' }}"></i>
                        {{ abs($timeTrend) }} min {{ $timeTrend <= 0 ? 'faster' : 'slower' }}
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="summary-grid">
                <div class="summary-card">
                    <div class="summary-value">{{ number_format($totalEarnings ?? 0, 2) }}</div>
                    <div class="summary-label">Total Earnings (₹)</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">{{ $onTimeRate ?? 0 }}%</div>
                    <div class="summary-label">On-Time Delivery</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">{{ $successRate ?? 0 }}%</div>
                    <div class="summary-label">Success Rate</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">{{ $customerSatisfaction ?? 0 }}%</div>
                    <div class="summary-label">Customer Satisfaction</div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-card">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-calendar-week text-primary me-1"></i> Date Range
                        </label>
                        <select name="period" class="form-select" onchange="this.form.submit()">
                            <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>This Week</option>
                            <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>This Month</option>
                            <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Last Month
                            </option>
                            <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Custom Range
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i> Apply Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Charts Grid -->
            <div class="charts-grid">
                <!-- Daily Performance Chart -->
                <div class="chart-card">
                    <div class="chart-title">
                        <i class="fas fa-chart-line text-primary"></i>
                        Daily Performance Trend
                    </div>
                    <canvas id="performanceChart" height="250"></canvas>
                </div>

                <!-- Earnings Chart -->
                <div class="chart-card">
                    <div class="chart-title">
                        <i class="fas fa-rupee-sign text-success"></i>
                        Daily Earnings
                    </div>
                    <canvas id="earningsChart" height="250"></canvas>
                </div>
            </div>

            <!-- Second Row Charts -->
            <div class="charts-grid">
                <!-- Delivery Time Chart -->
                <div class="chart-card">
                    <div class="chart-title">
                        <i class="fas fa-hourglass-half text-warning"></i>
                        Average Delivery Time by Day
                    </div>
                    <canvas id="deliveryTimeChart" height="250"></canvas>
                </div>

                <!-- Rating Distribution -->
                <div class="chart-card">
                    <div class="chart-title">
                        <i class="fas fa-star text-warning"></i>
                        Rating Distribution
                    </div>
                    <canvas id="ratingChart" height="250"></canvas>
                </div>
            </div>

            <!-- Performance Logs Table -->
            <div class="table-card">
                <div class="table-header">
                    <h5 class="mb-0"><i class="fas fa-history me-2 text-primary"></i> Performance History</h5>
                    <small class="text-muted">Detailed breakdown of your daily performance</small>
                </div>
                <div class="table-responsive">
                    <table class="performance-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Shipments</th>
                                <th>Delivered</th>
                                <th>Failed</th>
                                <th>On-Time</th>
                                <th>Rating</th>
                                <th>Distance</th>
                                <th>Earnings</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $__col = $performanceLogs; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $log)
                                <tr>
                                    <td class="fw-semibold">{{ $log->log_date->format('d M Y') }}</td>
                                    <td>{{ $log->shipments_assigned ?? 0 }}</td>
                                    <td>
                                        <span class="badge-success">{{ $log->shipments_delivered ?? 0 }}</span>
                                    </td>
                                    <td>
                                        @if (($log->shipments_failed ?? 0) > 0)
                                            <span class="badge-warning">{{ $log->shipments_failed ?? 0 }}</span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php $onTime = $log->on_time_rate ?? 100; @endphp
                                        <div class="d-flex align-items-center gap-2">
                                            <span>{{ $onTime }}%</span>
                                            <div class="progress flex-grow-1" style="height: 5px;">
                                                <div class="progress-bar bg-success" style="width: {{ $onTime }}%">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="rating-stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= round($log->average_rating ?? 0))
                                                    ★
                                                @else
                                                    ☆
                                                @endif
                                            @endfor
                                        </div>
                                        <small
                                            class="text-muted">({{ number_format($log->average_rating ?? 0, 1) }})</small>
                                    </td>
                                    <td>{{ number_format($log->total_distance_km ?? 0, 1) }} km</td>
                                    <td class="text-success fw-bold">₹{{ number_format($log->total_earnings ?? 0, 2) }}
                                    </td>
                                </tr>
                            @endforeach
@else
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fas fa-chart-line fa-3x mb-3 d-block text-muted"></i>
                                        <h6>No performance data available</h6>
                                        <p class="small">Complete some deliveries to see your performance metrics</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Export Button -->
            <div class="mt-4 text-end">
                <a href="{{ route('agent.performance.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                    class="btn btn-outline-primary">
                    <i class="fas fa-download me-2"></i> Export Report
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Daily Performance Chart
            const perfCtx = document.getElementById('performanceChart').getContext('2d');
            new Chart(perfCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode(array_column($dailyPerformance, 'date')) !!},
                    datasets: [{
                        label: 'Deliveries Completed',
                        data: {!! json_encode(array_column($dailyPerformance, 'count')) !!},
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointRadius: 4,
                        pointHoverRadius: 6
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
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0
                            }
                        }
                    }
                }
            });

            // Earnings Chart
            const earnCtx = document.getElementById('earningsChart').getContext('2d');
            new Chart(earnCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_column($dailyEarnings, 'date')) !!},
                    datasets: [{
                        label: 'Earnings (₹)',
                        data: {!! json_encode(array_column($dailyEarnings, 'earnings')) !!},
                        backgroundColor: 'rgba(16, 185, 129, 0.5)',
                        borderColor: '#10b981',
                        borderWidth: 1,
                        borderRadius: 8
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
                                label: (ctx) => `₹${ctx.raw.toLocaleString()}`
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

            // Delivery Time Chart
            const timeCtx = document.getElementById('deliveryTimeChart').getContext('2d');
            new Chart(timeCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode(array_column($dailyPerformance, 'date')) !!},
                    datasets: [{
                        label: 'Avg Delivery Time (minutes)',
                        data: {!! json_encode(array_column($deliveryTimes ?? [], 'time')) !!},
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Minutes'
                            }
                        }
                    }
                }
            });

            // Rating Distribution Chart
            const ratingCtx = document.getElementById('ratingChart').getContext('2d');
            new Chart(ratingCtx, {
                type: 'doughnut',
                data: {
                    labels: ['5 Star', '4 Star', '3 Star', '2 Star', '1 Star'],
                    datasets: [{
                        data: {!! json_encode($ratingDistribution ?? [0, 0, 0, 0, 0]) !!},
                        backgroundColor: ['#10b981', '#34d399', '#f59e0b', '#f97316', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `${ctx.label}: ${ctx.raw} reviews`
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
