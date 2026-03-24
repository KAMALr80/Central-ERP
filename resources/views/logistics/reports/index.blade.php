{{-- resources/views/logistics/reports.blade.php --}}
@extends('layouts.app')

@section('title', 'Logistics Reports & Analytics')

@section('content')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #1e293b;
            line-height: 1.5;
        }

        .reports-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
            padding: clamp(16px, 3vw, 30px);
            width: 100%;
        }

        .container {
            max-width: 1600px;
            margin: 0 auto;
            width: 100%;
        }

        .reports-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            width: 100%;
            border: 1px solid #e5e7eb;
        }

        .reports-header {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            padding: clamp(1.5rem, 4vw, 2rem);
            color: white;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header-left {
            flex: 1;
            min-width: 280px;
        }

        .header-title {
            font-size: clamp(1.5rem, 5vw, 2rem);
            font-weight: 700;
            margin: 0 0 0.5rem 0;
            line-height: 1.2;
        }

        .header-subtitle {
            opacity: 0.9;
            font-size: clamp(0.9rem, 2.5vw, 1rem);
        }

        .header-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .header-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-size: clamp(0.9rem, 2vw, 1rem);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
        }

        .header-btn:hover {
            background: white;
            color: #0f172a;
        }

        .date-filter-section {
            padding: 1.5rem clamp(1.5rem, 4vw, 2rem);
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: flex-end;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 0.25rem;
        }

        .filter-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.95rem;
            color: #1e293b;
            background: white;
            transition: all 0.2s;
        }

        .filter-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            white-space: nowrap;
        }

        .filter-btn-primary {
            background: #3b82f6;
            color: white;
        }

        .filter-btn-primary:hover {
            background: #2563eb;
        }

        .filter-btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e5e7eb;
        }

        .filter-btn-secondary:hover {
            background: #e2e8f0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.25rem;
            padding: 1.5rem clamp(1.5rem, 4vw, 2rem);
            border-bottom: 1px solid #e5e7eb;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
            transition: all 0.2s;
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
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.05);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .stat-title {
            font-size: 0.9rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .stat-icon.blue {
            background: #dbeafe;
            color: #1e40af;
        }

        .stat-icon.green {
            background: #d1fae5;
            color: #065f46;
        }

        .stat-icon.yellow {
            background: #fef3c7;
            color: #92400e;
        }

        .stat-icon.purple {
            background: #ede9fe;
            color: #5b21b6;
        }

        .stat-icon.red {
            background: #fee2e2;
            color: #991b1b;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.2;
            margin-bottom: 0.25rem;
        }

        .stat-sub {
            font-size: 0.85rem;
            color: #64748b;
        }

        .stat-trend {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }

        .stat-trend.up {
            color: #10b981;
        }

        .stat-trend.down {
            color: #ef4444;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 1.5rem;
            padding: 1.5rem clamp(1.5rem, 4vw, 2rem);
            border-bottom: 1px solid #e5e7eb;
        }

        .chart-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .chart-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .chart-container {
            height: 300px;
            position: relative;
        }

        .tables-section {
            padding: 1.5rem clamp(1.5rem, 4vw, 2rem);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .export-buttons {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .export-btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid #e5e7eb;
            background: white;
            color: #475569;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .export-btn:hover {
            background: #f1f5f9;
            border-color: #3b82f6;
        }

        .table-responsive {
            overflow-x: auto;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            background: white;
            margin-bottom: 2rem;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        .data-table th {
            background: #f8fafc;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.85rem;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 2px solid #e5e7eb;
        }

        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            font-size: 0.95rem;
            color: #1e293b;
        }

        .data-table tbody tr:hover {
            background: #f8fafc;
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge.success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge.warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge.danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge.info {
            background: #dbeafe;
            color: #1e40af;
        }

        .progress-bar-sm {
            width: 100%;
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .progress-fill-sm {
            height: 100%;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            width: 0%;
            transition: width 0.3s ease;
        }

        .city-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .city-card {
            background: #f8fafc;
            border-radius: 12px;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .city-name {
            font-weight: 600;
            color: #1e293b;
        }

        .city-count {
            background: white;
            padding: 0.25rem 0.75rem;
            border-radius: 2rem;
            font-weight: 600;
            color: #3b82f6;
            border: 1px solid #dbeafe;
        }

        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border-left: 4px solid;
            display: none;
            z-index: 9999;
            max-width: 400px;
            width: calc(100% - 40px);
            animation: slideIn 0.3s ease;
        }

        .toast.success {
            border-left-color: #10b981;
        }

        .toast.error {
            border-left-color: #ef4444;
        }

        .toast.warning {
            border-left-color: #f59e0b;
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
            z-index: 11000;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 1rem;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #e5e7eb;
            border-top-color: #3b82f6;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media print {

            .header-actions,
            .filter-form,
            .export-buttons,
            .btn,
            .map-controls {
                display: none !important;
            }

            .stat-card {
                break-inside: avoid;
                border: 1px solid #000;
            }

            .chart-container {
                page-break-inside: avoid;
            }

            .reports-header {
                background: #1e293b;
                color: black;
            }
        }

        @media (max-width: 768px) {
            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }

            .charts-grid {
                grid-template-columns: 1fr;
            }

            .chart-container {
                height: 250px;
            }

            .export-buttons {
                flex-direction: column;
                width: 100%;
            }

            .export-btn {
                width: 100%;
                justify-content: center;
            }

            .city-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="reports-page">
        <div class="container">
            <div class="reports-card">
                <div id="loadingOverlay" class="loading-overlay">
                    <div class="spinner"></div>
                    <div class="loading-text">Generating report...</div>
                </div>

                <div class="reports-header">
                    <div class="header-content">
                        <div class="header-left">
                            <h1 class="header-title">Logistics Reports & Analytics</h1>
                            <p class="header-subtitle">Comprehensive performance metrics and insights</p>
                        </div>
                        <div class="header-actions">
                            <button class="header-btn" onclick="refreshReports()"><i class="fas fa-sync-alt"></i>
                                Refresh</button>
                            <button class="header-btn" onclick="generatePDF()"><i class="fas fa-file-pdf"></i> PDF
                                Report</button>
                            <button class="header-btn" onclick="exportToExcel()"><i class="fas fa-file-excel"></i> Excel
                                Export</button>
                            <button class="header-btn" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                        </div>
                    </div>
                </div>

                <div class="date-filter-section">
                    <form method="GET" action="{{ route('logistics.reports') }}" class="filter-form" id="filterForm">
                        <div class="filter-group">
                            <label class="filter-label">From Date</label>
                            <input type="date" name="from_date" class="filter-input"
                                value="{{ $fromDate ?? now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="filter-group">
                            <label class="filter-label">To Date</label>
                            <input type="date" name="to_date" class="filter-input"
                                value="{{ $toDate ?? now()->format('Y-m-d') }}">
                        </div>
                        <div class="filter-actions">
                            <button type="submit" class="filter-btn filter-btn-primary"><i class="fas fa-search"></i> Apply
                                Filter</button>
                            <a href="{{ route('logistics.reports') }}" class="filter-btn filter-btn-secondary"><i
                                    class="fas fa-redo-alt"></i> Reset</a>
                        </div>
                    </form>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header"><span class="stat-title">Total Shipments</span><span
                                class="stat-icon blue">📦</span></div>
                        <div class="stat-value">{{ number_format($stats['total_shipments'] ?? 0) }}</div>
                        <div class="stat-sub">Total shipments in period</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header"><span class="stat-title">Delivered</span><span
                                class="stat-icon green">✅</span></div>
                        <div class="stat-value">{{ number_format($stats['delivered'] ?? 0) }}</div>
                        <div class="stat-sub">{{ number_format($stats['delivered'] ?? 0) }} delivered</div>
                        <div class="stat-trend up">↑ {{ $stats['delivery_rate'] ?? 0 }}% delivery rate</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header"><span class="stat-title">In Transit</span><span
                                class="stat-icon yellow">🚚</span></div>
                        <div class="stat-value">{{ number_format($stats['in_transit'] ?? 0) }}</div>
                        <div class="stat-sub">{{ $stats['in_transit'] ?? 0 }} shipments on the way</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header"><span class="stat-title">Pending</span><span
                                class="stat-icon purple">⏳</span></div>
                        <div class="stat-value">{{ number_format($stats['pending'] ?? 0) }}</div>
                        <div class="stat-sub">{{ $stats['pending'] ?? 0 }} pending pickup</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header"><span class="stat-title">Failed/Returned</span><span
                                class="stat-icon red">❌</span></div>
                        <div class="stat-value">{{ number_format($stats['failed'] ?? 0) }}</div>
                        <div class="stat-sub">{{ $stats['failed'] ?? 0 }} failed deliveries</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header"><span class="stat-title">Total Revenue</span><span
                                class="stat-icon blue">💰</span></div>
                        <div class="stat-value">₹{{ number_format($stats['total_revenue'] ?? 0, 2) }}</div>
                        <div class="stat-sub">From shipping charges</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header"><span class="stat-title">Avg. Delivery Time</span><span
                                class="stat-icon green">⏱️</span></div>
                        <div class="stat-value">{{ $stats['avg_delivery_time'] ?? 0 }} hrs</div>
                        <div class="stat-sub">Average delivery time</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header"><span class="stat-title">COD Shipments</span><span
                                class="stat-icon yellow">💵</span></div>
                        <div class="stat-value">{{ number_format($stats['cod_shipments'] ?? 0) }}</div>
                        <div class="stat-sub">Cash on Delivery orders</div>
                    </div>
                </div>

                <div class="charts-grid">
                    <div class="chart-card">
                        <div class="chart-header">
                            <div class="chart-title"><span>📈</span><span>Daily Shipment Trend</span></div>
                        </div>
                        <div class="chart-container"><canvas id="dailyTrendChart"></canvas></div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-header">
                            <div class="chart-title"><span>🥧</span><span>Shipments by Courier</span></div>
                        </div>
                        <div class="chart-container"><canvas id="courierChart"></canvas></div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-header">
                            <div class="chart-title"><span>📊</span><span>Shipment Status</span></div>
                        </div>
                        <div class="chart-container"><canvas id="statusChart"></canvas></div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-header">
                            <div class="chart-title"><span>🏙️</span><span>Top Cities</span></div>
                        </div>
                        <div class="chart-container"><canvas id="cityChart"></canvas></div>
                    </div>
                </div>

                <div class="tables-section">
                    <div class="section-header">
                        <div class="section-title"><span>🚚</span><span>Courier Partner Performance</span></div>
                        <div class="export-buttons"><button class="export-btn" onclick="exportTableToExcel('courier')"><i
                                    class="fas fa-file-excel"></i> Export Courier Data</button></div>
                    </div>
                    <div class="table-responsive">
                        <table class="data-table" id="courierTable">
                            <thead>
                                <tr>
                                    <th>Courier Partner</th>
                                    <th>Total Shipments</th>
                                    <th>Delivered</th>
                                    <th>Pending</th>
                                    <th>Failed</th>
                                    <th>Success Rate</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $__col = $byCourier; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $courier)
                                    <tr>
                                        <td><strong>{{ $courier->courier_partner ?? 'Not Assigned' }}</strong></td>
                                        <td>{{ number_format($courier->total) }}</td>
                                        <td>{{ number_format($courier->delivered ?? 0) }}</td>
                                        <td>{{ number_format($courier->pending ?? 0) }}</td>
                                        <td>{{ number_format($courier->failed ?? 0) }}</td>
                                        <td>@php $successRate = $courier->total > 0 ? round(($courier->delivered / $courier->total) * 100) : 0; @endphp<span
                                                class="badge {{ $successRate >= 80 ? 'success' : ($successRate >= 50 ? 'warning' : 'danger') }}">{{ $successRate }}%</span>
                                            <div class="progress-bar-sm">
                                                <div class="progress-fill-sm" style="width: {{ $successRate }}%"></div>
                                            </div>
                                        </td>
                                        <td>₹{{ number_format($courier->revenue ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach
@else <tr>
                                        <td colspan="7" style="text-align:center; padding:2rem;">No courier data
                                            available</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="section-header">
                        <div class="section-title"><span>🏙️</span><span>Top Delivery Cities</span></div>
                        <div class="export-buttons"><button class="export-btn" onclick="exportCitiesToExcel()"><i
                                    class="fas fa-file-excel"></i> Export Cities Data</button></div>
                    </div>
                    <div class="city-grid" id="citiesGrid">
                        @php $__col = $byCity; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $city)
                            <div class="city-card"><span class="city-name">{{ $city->city }}</span><span
                                    class="city-count">{{ number_format($city->total) }}</span></div>
                        @endforeach
@else <div style="text-align:center; padding:2rem; grid-column:1/-1;">No city data available
                            </div>
                        @endif
                    </div>

                    <div class="section-header">
                        <div class="section-title"><span>📅</span><span>Daily Shipment Trend</span></div>
                        <div class="export-buttons"><button class="export-btn" onclick="exportDailyToExcel()"><i
                                    class="fas fa-file-excel"></i> Export Daily Data</button></div>
                    </div>
                    <div class="table-responsive">
                        <table class="data-table" id="dailyTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Shipments</th>
                                    <th>Delivered</th>
                                    <th>In Transit</th>
                                    <th>Pending</th>
                                    <th>Failed</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $__col = $dailyTrend ?: []; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $day)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($day->date)->format('d M Y') }}</td>
                                        <td>{{ number_format($day->total) }}</td>
                                        <td>{{ number_format($day->delivered ?? 0) }}</td>
                                        <td>{{ number_format($day->in_transit ?? 0) }}</td>
                                        <td>{{ number_format($day->pending ?? 0) }}</td>
                                        <td>{{ number_format($day->failed ?? 0) }}</td>
                                        <td>₹{{ number_format($day->revenue ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach
@else <tr>
                                        <td colspan="7" style="text-align:center; padding:2rem;">No daily data
                                            available</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        const dailyData = @json($dailyTrend ?? []);
        const courierData = @json($byCourier ?? []);
        const cityData = @json($byCity ?? []);
        const stats = @json($stats ?? []);

        let dailyChart, courierChart, statusChart, cityChart;

        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toast');
            toast.innerHTML = `<div>${type === 'success' ? '✅' : '❌'} ${msg}</div>`;
            toast.className = 'toast ' + type;
            toast.style.display = 'block';
            setTimeout(() => toast.style.display = 'none', 3000);
        }

        function refreshReports() {
            showLoading();
            window.location.reload();
        }

        // Initialize Charts
        function initCharts() {
            if (document.getElementById('dailyTrendChart') && dailyData.length) {
                const ctx = document.getElementById('dailyTrendChart').getContext('2d');
                dailyChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: dailyData.map(d => d.date),
                        datasets: [{
                            label: 'Shipments',
                            data: dailyData.map(d => d.total),
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            if (document.getElementById('courierChart') && courierData.length) {
                const ctx = document.getElementById('courierChart').getContext('2d');
                courierChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: courierData.map(c => c.courier_partner || 'Not Assigned'),
                        datasets: [{
                            data: courierData.map(c => c.total),
                            backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444',
                                '#6b7280'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            }

            if (document.getElementById('statusChart')) {
                const ctx = document.getElementById('statusChart').getContext('2d');
                statusChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Delivered', 'In Transit', 'Pending', 'Failed/Returned'],
                        datasets: [{
                            data: [stats.delivered || 0, stats.in_transit || 0, stats.pending || 0, stats
                                .failed || 0
                            ],
                            backgroundColor: ['#10b981', '#f59e0b', '#8b5cf6', '#ef4444'],
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            if (document.getElementById('cityChart') && cityData.length) {
                const ctx = document.getElementById('cityChart').getContext('2d');
                cityChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: cityData.slice(0, 10).map(c => c.city),
                        datasets: [{
                            label: 'Shipments',
                            data: cityData.slice(0, 10).map(c => c.total),
                            backgroundColor: '#8b5cf6',
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        }

        // PDF Generation
        async function generatePDF() {
            showLoading();
            const {
                jsPDF
            } = window.jspdf;
            const pdf = new jsPDF('p', 'mm', 'a4');

            const element = document.querySelector('.reports-card');
            const canvas = await html2canvas(element, {
                scale: 2,
                logging: false,
                useCORS: true
            });
            const imgData = canvas.toDataURL('image/png');
            const imgWidth = 190;
            const pageHeight = 277;
            const imgHeight = (canvas.height * imgWidth) / canvas.width;
            let heightLeft = imgHeight;
            let position = 10;

            pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
            while (heightLeft > 0) {
                position = heightLeft - imgHeight;
                pdf.addPage();
                pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }

            pdf.save(`Logistics_Report_${new Date().toISOString().slice(0,10)}.pdf`);
            hideLoading();
            showToast('PDF report generated successfully!', 'success');
        }

        // Excel Export Functions
        function exportToExcel() {
            showLoading();
            const wb = XLSX.utils.book_new();

            const courierData2 = @json($byCourier ?? []).map(c => ({
                'Courier Partner': c.courier_partner || 'Not Assigned',
                'Total Shipments': c.total,
                'Delivered': c.delivered || 0,
                'Pending': c.pending || 0,
                'Failed': c.failed || 0,
                'Success Rate': c.total > 0 ? ((c.delivered / c.total) * 100).toFixed(2) + '%' : '0%',
                'Revenue': c.revenue || 0
            }));
            const wsCourier = XLSX.utils.json_to_sheet(courierData2);
            XLSX.utils.book_append_sheet(wb, wsCourier, 'Courier Performance');

            const cityData2 = @json($byCity ?? []).map(c => ({
                'City': c.city,
                'Shipments': c.total
            }));
            const wsCity = XLSX.utils.json_to_sheet(cityData2);
            XLSX.utils.book_append_sheet(wb, wsCity, 'Top Cities');

            const dailyData2 = @json($dailyTrend ?? []).map(d => ({
                'Date': d.date,
                'Total Shipments': d.total,
                'Delivered': d.delivered || 0,
                'In Transit': d.in_transit || 0,
                'Pending': d.pending || 0,
                'Failed': d.failed || 0,
                'Revenue': d.revenue || 0
            }));
            const wsDaily = XLSX.utils.json_to_sheet(dailyData2);
            XLSX.utils.book_append_sheet(wb, wsDaily, 'Daily Trend');

            XLSX.writeFile(wb, `Logistics_Report_${new Date().toISOString().slice(0,10)}.xlsx`);
            hideLoading();
            showToast('Excel report exported successfully!', 'success');
        }

        function exportTableToExcel(tableId) {
            showLoading();
            let data = [];
            if (tableId === 'courier') {
                data = @json($byCourier ?? []).map(c => ({
                    'Courier Partner': c.courier_partner || 'Not Assigned',
                    'Total': c.total,
                    'Delivered': c.delivered || 0,
                    'Pending': c.pending || 0,
                    'Failed': c.failed || 0,
                    'Revenue': c.revenue || 0
                }));
            }
            const ws = XLSX.utils.json_to_sheet(data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, `${tableId}_Data`);
            XLSX.writeFile(wb, `${tableId}_data_${new Date().toISOString().slice(0,10)}.xlsx`);
            hideLoading();
            showToast('Table exported successfully!', 'success');
        }

        function exportCitiesToExcel() {
            showLoading();
            const data = @json($byCity ?? []).map(c => ({
                'City': c.city,
                'Shipments': c.total
            }));
            const ws = XLSX.utils.json_to_sheet(data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Cities_Data');
            XLSX.writeFile(wb, `cities_data_${new Date().toISOString().slice(0,10)}.xlsx`);
            hideLoading();
            showToast('Cities data exported!', 'success');
        }

        function exportDailyToExcel() {
            showLoading();
            const data = @json($dailyTrend ?? []).map(d => ({
                'Date': d.date,
                'Total': d.total,
                'Delivered': d.delivered || 0,
                'In Transit': d.in_transit || 0,
                'Pending': d.pending || 0,
                'Failed': d.failed || 0,
                'Revenue': d.revenue || 0
            }));
            const ws = XLSX.utils.json_to_sheet(data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Daily_Trend');
            XLSX.writeFile(wb, `daily_trend_${new Date().toISOString().slice(0,10)}.xlsx`);
            hideLoading();
            showToast('Daily data exported!', 'success');
        }

        document.addEventListener('DOMContentLoaded', initCharts);
        setInterval(() => {
            if (!document.getElementById('loadingOverlay').style.display === 'flex') location.reload();
        }, 600000);
    </script>
@endsection
