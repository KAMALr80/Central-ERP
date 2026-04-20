@extends('layouts.app')

@section('title', 'Active Agents')

@section('content')
    <style>
        .active-agents-page {
            padding: 20px;
            background: #f8fafc;
            min-height: 100vh;
        }

        .page-header {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            border-radius: 20px;
            padding: 25px 30px;
            margin-bottom: 25px;
            color: white;
        }

        .agents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }

        .agent-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            transition: all 0.3s;
        }

        .agent-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }

        .agent-header {
            padding: 16px 20px;
            background: #fafbfc;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .agent-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .agent-avatar {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 18px;
        }

        .agent-name {
            font-weight: 700;
            color: #1e293b;
        }

        .agent-code {
            font-size: 11px;
            color: #64748b;
        }

        .live-badge {
            background: rgba(16, 185, 129, 0.1);
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 11px;
            color: #10b981;
        }

        .agent-body {
            padding: 16px 20px;
        }

        .location-info {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 12px;
        }

        .delivery-info {
            background: #fef3c7;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 12px;
        }

        .speed-gauge {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
        }

        .speed-value {
            font-size: 18px;
            font-weight: 700;
            color: #3b82f6;
        }

        .btn-track-agent {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            width: 100%;
            justify-content: center;
        }

        .btn-track-agent:hover {
            transform: translateY(-1px);
            color: white;
        }

        @media (max-width: 768px) {
            .agents-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="active-agents-page">
        <div class="page-header">
            <div>
                <h1 class="mb-0"><i class="fas fa-users me-2"></i> Active Delivery Agents</h1>
                <p class="mb-0 mt-2 opacity-75">Real-time tracking of all active agents</p>
            </div>
            <div class="mt-2">
                <span class="badge bg-success">🟢 {{ $activeAgents->count() }} Active Now</span>
            </div>
        </div>

        <div class="agents-grid">
            @foreach ($activeAgents as $agent)
                <div class="agent-card">
                    <div class="agent-header">
                        <div class="agent-info">
                            <div class="agent-avatar">{{ strtoupper(substr($agent->name, 0, 1)) }}</div>
                            <div>
                                <div class="agent-name">{{ $agent->name }}</div>
                                <div class="agent-code">{{ $agent->agent_code }}</div>
                            </div>
                        </div>
                        <div class="live-badge">
                            <span class="live-pulse"
                                style="display: inline-block; width: 8px; height: 8px; background: #10b981; border-radius: 50%; animation: pulse 1.5s infinite; margin-right: 6px;"></span>
                            Live
                        </div>
                    </div>
                    <div class="agent-body">
                        <div class="location-info">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">📍 Current Location</span>
                                <span class="text-muted small">Last update:
                                    {{ $agent->last_location_update?->diffForHumans() ?? 'N/A' }}</span>
                            </div>
                            <div class="speed-gauge">
                                <i class="fas fa-tachometer-alt text-primary"></i>
                                <span class="speed-value">{{ $agent->current_speed ?? 0 }} km/h</span>
                                <span class="text-muted ms-2">Current Speed</span>
                            </div>
                            <div class="mt-2">
                                <i class="fas fa-map-marker-alt text-danger"></i>
                                <span class="small">{{ $agent->current_location ?? 'Location not available' }}</span>
                            </div>
                        </div>

                        @if ($agent->currentShipment)
                            <div class="delivery-info">
                                <div class="d-flex justify-content-between mb-2">
                                    <span><i class="fas fa-box"></i> Current Delivery</span>
                                    <span
                                        class="badge bg-warning">{{ ucfirst(str_replace('_', ' ', $agent->currentShipment->status)) }}</span>
                                </div>
                                <div class="small"><strong>To:</strong> {{ $agent->currentShipment->receiver_name }}</div>
                                <div class="small"><strong>Address:</strong>
                                    {{ \Illuminate\Support\Str::limit($agent->currentShipment->shipping_address, 50) }}</div>
                                @if ($agent->currentShipment->destination_latitude)
                                    <div class="mt-2">
                                        <div class="progress" style="height: 4px;">
                                            <div class="progress-bar bg-success"
                                                style="width: {{ $progressPercentage ?? 0 }}%"></div>
                                        </div>
                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-route"></i> {{ number_format($remainingDistance ?? 0, 1) }} km
                                            remaining
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-2 text-muted">
                                <i class="fas fa-box-open"></i> No active delivery
                            </div>
                        @endif

                        <a href="{{ route('admin.tracking.agent', $agent->user_id) }}" class="btn-track-agent mt-2">
                            <i class="fas fa-map-marker-alt"></i> Track Live Location
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        @keyframes pulse {
            0% {
                opacity: 1;
                transform: scale(1);
            }

            100% {
                opacity: 0;
                transform: scale(2);
            }
        }
    </style>
@endsection
