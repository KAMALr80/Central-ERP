@extends('layouts.app')

@section('title', 'Agent Dashboard')

@section('content')
<style>
    .agent-dashboard {
        padding: 20px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #e5e7eb;
        transition: all 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .stat-title {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: #1e293b;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #3b82f6, #1e40af);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }

    .shipment-card {
        background: white;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 12px;
        border: 1px solid #e5e7eb;
        transition: all 0.3s;
    }

    .shipment-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59,130,246,0.1);
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-pending { background: #fef3c7; color: #92400e; }
    .status-picked { background: #dbeafe; color: #1e40af; }
    .status-in_transit { background: #ede9fe; color: #5b21b6; }
    .status-out_for_delivery { background: #dcfce7; color: #166534; }

    .btn-start {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-start:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16,185,129,0.3);
    }

    .weekly-chart {
        background: white;
        border-radius: 20px;
        padding: 20px;
        margin-top: 20px;
        border: 1px solid #e5e7eb;
    }

    .chart-bar {
        height: 8px;
        background: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 8px;
    }

    .bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6);
        width: 0%;
        border-radius: 4px;
        transition: width 0.5s ease;
    }
</style>

<div class="agent-dashboard">
    <!-- Welcome Header -->
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 28px; font-weight: 700; color: #1e293b;">Welcome back, {{ $agent->name }}!</h1>
        <p style="color: #6b7280;">Here's your delivery summary for today</p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div class="stat-title">Today's Deliveries</div>
                <div class="stat-value">{{ $todayDeliveries }}</div>
            </div>
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        </div>

        <div class="stat-card" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div class="stat-title">Today's Earnings</div>
                <div class="stat-value">₹{{ number_format($todayEarnings, 2) }}</div>
            </div>
            <div class="stat-icon"><i class="fas fa-rupee-sign"></i></div>
        </div>

        <div class="stat-card" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div class="stat-title">Your Rating</div>
                <div class="stat-value">{{ number_format($rating, 1) }} ★</div>
            </div>
            <div class="stat-icon"><i class="fas fa-star"></i></div>
        </div>

        <div class="stat-card" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div class="stat-title">Total Deliveries</div>
                <div class="stat-value">{{ $totalDeliveries }}</div>
            </div>
            <div class="stat-icon"><i class="fas fa-box"></i></div>
        </div>
    </div>

    <!-- Active Deliveries -->
    <div style="margin-bottom: 30px;">
        <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 16px;">
            <i class="fas fa-truck"></i> Active Deliveries
        </h2>

        @if(count($activeShipments) > 0)
            @foreach($activeShipments as $shipment)
                <div class="shipment-card">
                    <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 12px;">
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                                <span class="status-badge status-{{ $shipment->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $shipment->status)) }}
                                </span>
                                <span style="font-weight: 600;">#{{ $shipment->shipment_number }}</span>
                            </div>
                            <div style="margin-top: 8px;">
                                <div><i class="fas fa-user"></i> {{ $shipment->receiver_name }}</div>
                                <div><i class="fas fa-phone"></i> {{ $shipment->receiver_phone }}</div>
                                <div><i class="fas fa-map-marker-alt"></i> {{ $shipment->city }}, {{ $shipment->state }}</div>
                            </div>
                        </div>
                        <div>
                            @if($shipment->status == 'pending')
                                <a href="{{ route('agent.deliveries.start', $shipment->id) }}" class="btn-start">
                                    <i class="fas fa-play"></i> Start Delivery
                                </a>
                            @else
                                <a href="{{ route('agent.tracking', $shipment->id) }}" class="btn-start" style="background: linear-gradient(135deg, #3b82f6, #1e40af);">
                                    <i class="fas fa-map-marker-alt"></i> Track
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div style="text-align: center; padding: 40px; background: white; border-radius: 20px; border: 1px solid #e5e7eb;">
                <i class="fas fa-box-open" style="font-size: 48px; color: #9ca3af; margin-bottom: 16px;"></i>
                <p style="color: #6b7280;">No active deliveries</p>
                <p style="font-size: 13px; color: #9ca3af;">New deliveries will appear here when assigned</p>
            </div>
        @endif
    </div>

    <!-- Weekly Stats -->
    <div class="weekly-chart">
        <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">
            <i class="fas fa-chart-line"></i> Weekly Performance
        </h3>
        <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 12px;">
            @foreach($weeklyStats as $stat)
                <div style="text-align: center;">
                    <div style="font-size: 12px; color: #6b7280;">{{ $stat['day'] }}</div>
                    <div style="font-size: 18px; font-weight: 700; color: #3b82f6;">{{ $stat['count'] }}</div>
                    <div class="chart-bar">
                        <div class="bar-fill" style="width: {{ min($stat['count'] * 10, 100) }}%"></div>
                    </div>
                    <div style="font-size: 10px; color: #9ca3af;">{{ $stat['date'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Deliveries -->
    @if(count($recentDeliveries) > 0)
        <div style="margin-top: 30px;">
            <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">
                <i class="fas fa-history"></i> Recent Deliveries
            </h2>
            <div style="background: white; border-radius: 16px; border: 1px solid #e5e7eb; overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #f8fafc;">
                        <tr>
                            <th style="padding: 12px 16px; text-align: left;">Shipment</th>
                            <th style="padding: 12px 16px; text-align: left;">Customer</th>
                            <th style="padding: 12px 16px; text-align: left;">Date</th>
                            <th style="padding: 12px 16px; text-align: left;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentDeliveries as $delivery)
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 12px 16px;">#{{ $delivery->shipment_number }}</td>
                                <td style="padding: 12px 16px;">{{ $delivery->receiver_name }}</td>
                                <td style="padding: 12px 16px;">{{ $delivery->actual_delivery_date?->format('d M Y') }}</td>
                                <td style="padding: 12px 16px;">₹{{ number_format($delivery->declared_value, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
