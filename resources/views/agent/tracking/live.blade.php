{{-- resources/views/logistics/live-track.blade.php --}}
@extends('layouts.app')

@section('title', 'Live Tracking - ' . $shipment->shipment_number)

@section('content')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            background: #f5f7fa;
            overflow: hidden;
        }

        .live-track-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
            background: #f5f7fa;
        }

        .tracking-header {
            background: white;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            z-index: 100;
            border-bottom: 1px solid #eef2f6;
            flex-shrink: 0;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .back-btn {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #1f2937;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .back-btn:hover {
            background: #f3f4f6;
        }

        .tracking-title h2 {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
            color: #1f2937;
        }

        .tracking-title p {
            font-size: 12px;
            color: #6b7280;
            margin: 2px 0 0;
        }

        .live-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #fee2e2;
            padding: 6px 12px;
            border-radius: 30px;
            flex-shrink: 0;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.5;
                transform: scale(1.2);
            }
        }

        .live-badge span {
            font-size: 11px;
            font-weight: 600;
            color: #dc2626;
        }

        .tracking-id {
            font-family: monospace;
            font-size: 12px;
            color: #6b7280;
            background: #f3f4f6;
            padding: 6px 12px;
            border-radius: 20px;
        }

        .tracking-main {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        .info-panel {
            width: 380px;
            background: white;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            box-shadow: 2px 0 20px rgba(0, 0, 0, 0.05);
            z-index: 10;
        }

        .agent-card {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            padding: 24px;
            color: white;
            flex-shrink: 0;
        }

        .agent-profile {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .agent-avatar {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            border: 3px solid rgba(255, 255, 255, 0.2);
            flex-shrink: 0;
        }

        .agent-info {
            flex: 1;
            min-width: 0;
        }

        .agent-info h3 {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .agent-info p {
            font-size: 12px;
            opacity: 0.8;
            margin: 4px 0 0;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .rating {
            display: flex;
            align-items: center;
            gap: 4px;
            background: rgba(255, 255, 255, 0.2);
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 11px;
        }

        .live-status {
            margin-top: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .live-pulse {
            width: 10px;
            height: 10px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse-ring 1.5s infinite;
            flex-shrink: 0;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(0.8);
                opacity: 0.8;
            }

            100% {
                transform: scale(1.2);
                opacity: 0;
            }
        }

        .last-update {
            margin-left: auto;
            font-size: 11px;
            opacity: 0.7;
        }

        .delivery-info {
            padding: 20px;
            border-bottom: 1px solid #eef2f6;
            flex-shrink: 0;
        }

        .section-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #9ca3af;
            margin-bottom: 12px;
        }

        .address-detail {
            font-size: 14px;
            line-height: 1.5;
            color: #1f2937;
            margin-bottom: 12px;
            word-break: break-word;
        }

        /* ---- CURRENT LOCATION CARD ---- */
        .current-location-card {
            margin: 0 16px 0;
            background: #f0fdf4;
            border: 1.5px solid #86efac;
            border-radius: 16px;
            padding: 14px 16px;
            flex-shrink: 0;
        }

        .current-location-card .cl-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        .current-location-card .cl-dot {
            width: 10px;
            height: 10px;
            background: #16a34a;
            border-radius: 50%;
            animation: pulse-green 1.5s infinite;
            flex-shrink: 0;
        }

        .current-location-card .cl-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #15803d;
        }

        .current-location-card .cl-area {
            font-size: 14px;
            font-weight: 600;
            color: #14532d;
            line-height: 1.4;
            word-break: break-word;
        }

        .current-location-card .cl-coords {
            font-size: 11px;
            color: #16a34a;
            font-family: monospace;
            margin-top: 4px;
        }

        .current-location-card .cl-updated {
            font-size: 11px;
            color: #6b7280;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .eta-card {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            margin: 16px;
            border-radius: 20px;
            padding: 20px;
            flex-shrink: 0;
        }

        .eta-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .eta-label {
            font-size: 12px;
            color: #6b7280;
        }

        .eta-value {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
        }

        .eta-unit {
            font-size: 12px;
            color: #9ca3af;
            margin-left: 4px;
        }

        .progress-bar {
            height: 6px;
            background: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            width: 0%;
            transition: width 0.5s ease;
        }

        .stats-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-top: 16px;
            flex-wrap: wrap;
        }

        .stat-item {
            flex: 1;
            text-align: center;
            padding: 12px;
            background: white;
            border-radius: 12px;
            min-width: 80px;
        }

        .stat-label-sm {
            font-size: 10px;
            color: #9ca3af;
            margin-bottom: 4px;
        }

        .stat-value-sm {
            font-size: 16px;
            font-weight: 700;
            color: #1f2937;
        }

        .timeline-section {
            padding: 20px;
            flex: 1;
            overflow-y: auto;
            min-height: 0;
        }

        .timeline-item {
            display: flex;
            gap: 12px;
            padding: 12px 0;
            position: relative;
        }

        .timeline-time {
            min-width: 60px;
            font-size: 11px;
            color: #9ca3af;
        }

        .timeline-icon {
            width: 28px;
            height: 28px;
            background: #f3f4f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            flex-shrink: 0;
        }

        .timeline-icon i {
            font-size: 12px;
            color: #667eea;
        }

        .timeline-icon::after {
            content: '';
            position: absolute;
            top: 28px;
            left: 13px;
            width: 2px;
            height: calc(100% + 8px);
            background: #e5e7eb;
        }

        .timeline-item:last-child .timeline-icon::after {
            display: none;
        }

        .timeline-content {
            flex: 1;
            min-width: 0;
        }

        .timeline-status {
            font-weight: 600;
            font-size: 13px;
            color: #1f2937;
        }

        .timeline-location {
            font-size: 11px;
            color: #6b7280;
            margin-top: 2px;
            word-break: break-word;
        }

        .map-panel {
            flex: 1;
            position: relative;
            background: #e9ecef;
        }

        #trackingMap {
            width: 100%;
            height: 100%;
        }

        /* ---- STATUS CIRCLE ---- */
        .status-circle {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 50%;
            width: 130px;
            height: 130px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .status-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            margin-bottom: 6px;
        }

        .status-dot.live {
            background: #10b981;
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
            animation: pulse-green 1.5s infinite;
        }

        .status-dot.offline {
            background: #ef4444;
        }

        @keyframes pulse-green {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.6;
                transform: scale(1.2);
            }
        }

        .status-time {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            line-height: 1.2;
        }

        .status-time small {
            font-size: 12px;
            font-weight: 400;
            color: #6b7280;
        }

        .status-distance {
            font-size: 20px;
            font-weight: 600;
            color: #667eea;
            margin-top: 2px;
        }

        .status-distance small {
            font-size: 10px;
            font-weight: 400;
            color: #9ca3af;
        }

        .direction-indicator {
            margin-top: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 6px;
            background: #f3f4f6;
            padding: 4px 12px;
            border-radius: 20px;
        }

        .direction-indicator i {
            font-size: 14px;
            color: #667eea;
            transition: transform 0.3s ease;
        }

        /* ---- DEBUG BADGE (only visible in dev) ---- */
        .debug-badge {
            position: absolute;
            bottom: 80px;
            left: 10px;
            z-index: 999;
            background: rgba(0, 0, 0, 0.75);
            color: #0f0;
            font-family: monospace;
            font-size: 11px;
            padding: 6px 10px;
            border-radius: 6px;
            display: none;
            /* set to block to debug */
        }

        .map-controls {
            position: absolute;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .map-control-btn {
            width: 44px;
            height: 44px;
            background: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #5f6368;
            font-size: 20px;
            transition: all 0.2s;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .map-control-btn:hover {
            background: #667eea;
            color: white;
            transform: scale(1.05);
        }

        .no-agent-message {
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 20px;
            background: #f8f9fa;
            color: #6c757d;
            padding: 20px;
            text-align: center;
        }

        .no-agent-message i {
            font-size: 80px;
            opacity: 0.3;
            color: #667eea;
        }

        .no-agent-message h3 {
            font-size: 20px;
            color: #1f2937;
            margin: 0;
        }

        .no-agent-message p {
            font-size: 14px;
            margin: 0;
            max-width: 400px;
        }

        .no-agent-message .info-box {
            background: white;
            padding: 24px;
            border-radius: 16px;
            text-align: center;
            max-width: 400px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .assign-btn-large {
            margin-top: 16px;
            padding: 10px 24px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .assign-btn-large:hover {
            background: #5a67d8;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .tracking-main {
                flex-direction: column;
            }

            .info-panel {
                width: 100%;
                max-height: 45vh;
                flex-shrink: 0;
            }

            .map-panel {
                height: 55vh;
                flex: none;
            }

            .status-circle {
                width: 110px;
                height: 110px;
                top: 10px;
                right: 10px;
            }

            .status-time {
                font-size: 22px;
            }

            .status-distance {
                font-size: 16px;
            }
        }
    </style>

    <div class="live-track-container">

        {{-- Header --}}
        <div class="tracking-header">
            <div class="header-left">
                <button class="back-btn" onclick="window.history.back()">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <div class="tracking-title">
                    <h2>Live Tracking</h2>
                    <p>Real-time shipment location</p>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <div class="live-badge">
                    <span class="live-dot"></span>
                    <span>LIVE</span>
                </div>
                <div class="tracking-id">
                    {{ $shipment->tracking_number ?? $shipment->shipment_number }}
                </div>
            </div>
        </div>

        <div class="tracking-main">

            {{-- LEFT PANEL --}}
            <div class="info-panel">
                <div class="agent-card">
                    <div class="agent-profile">
                        <div class="agent-avatar">
                            <i class="fas fa-motorcycle"></i>
                        </div>
                        <div class="agent-info">
                            <h3 id="agentName">{{ $agent->name ?? 'Delivery Partner' }}</h3>
                            <p>
                                <span class="rating">
                                    <i class="fas fa-star" style="font-size: 10px;"></i>
                                    {{ $agent->rating ?? '4.9' }}
                                </span>
                                <span>{{ $agent->total_deliveries ?? '0' }} deliveries</span>
                            </p>
                        </div>
                    </div>
                    <div class="live-status" id="liveStatus">
                        @if ($agent && $agent->id)
                            <div class="live-pulse"></div>
                            <span style="font-size: 13px;">Connecting to agent...</span>
                            <div class="last-update" id="lastUpdate">--</div>
                        @else
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-clock" style="opacity: 0.7;"></i>
                                <span style="font-size: 13px;">Waiting for agent assignment</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="delivery-info">
                    <div class="section-label">DELIVERY ADDRESS</div>
                    <div class="address-detail">
                        <i class="fas fa-map-pin" style="color: #667eea; margin-right: 6px;"></i>
                        {{ $shipment->shipping_address }}, {{ $shipment->city }}, {{ $shipment->state }} -
                        {{ $shipment->pincode }}
                    </div>
                    <div style="display: flex; gap: 12px; font-size: 13px; color: #6b7280; flex-wrap: wrap;">
                        <span><i class="fas fa-user"></i> {{ $shipment->receiver_name }}</span>
                        <span><i class="fas fa-phone"></i> {{ $shipment->receiver_phone }}</span>
                    </div>
                </div>

                {{-- AGENT CURRENT LOCATION CARD --}}
                <div class="current-location-card" id="currentLocationCard" style="display:none;">
                    <div class="cl-header">
                        <div class="cl-dot"></div>
                        <span class="cl-label">Agent Abhi Yahan Hai</span>
                    </div>
                    <div class="cl-area" id="clArea">Locating...</div>
                    <div class="cl-coords" id="clCoords"></div>
                    <div class="cl-updated">
                        <i class="fas fa-clock" style="font-size:10px;"></i>
                        <span id="clUpdated">--</span>
                    </div>
                </div>

                <div class="eta-card">
                    <div class="eta-header">
                        <div>
                            <div class="eta-label">ESTIMATED ARRIVAL</div>
                            <div class="eta-value" id="etaTime">--:--</div>
                            <div class="eta-label" id="etaDate">Today</div>
                        </div>
                        <div style="text-align: right;">
                            <div class="eta-label">DISTANCE LEFT</div>
                            <div class="eta-value" id="distanceLeft">-- <span class="eta-unit">km</span></div>
                        </div>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill" style="width: 0%"></div>
                    </div>
                    <div class="stats-row">
                        <div class="stat-item">
                            <div class="stat-label-sm">SPEED</div>
                            <div class="stat-value-sm" id="currentSpeed">0 <span style="font-size: 10px;">km/h</span></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label-sm">ACCURACY</div>
                            <div class="stat-value-sm" id="gpsAccuracy">-- <span style="font-size: 10px;">m</span></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label-sm">TIME LEFT</div>
                            <div class="stat-value-sm" id="timeLeft">-- <span style="font-size: 10px;">min</span></div>
                        </div>
                    </div>
                </div>

                <div class="timeline-section">
                    <div class="section-label" style="margin-bottom: 8px;">TRACKING HISTORY</div>
                    <div id="timelineContainer">
                        @foreach ($shipment->trackings->take(5) as $track)
                            <div class="timeline-item">
                                <div class="timeline-time">{{ $track->tracked_at->format('h:i A') }}</div>
                                <div class="timeline-icon">
                                    <i
                                        class="fas {{ $track->status === 'delivered' ? 'fa-check-circle' : 'fa-circle' }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-status">{{ ucfirst(str_replace('_', ' ', $track->status)) }}</div>
                                    <div class="timeline-location">{{ $track->location ?? $shipment->city }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- RIGHT PANEL - MAP --}}
            <div class="map-panel">
                <div id="trackingMap" style="height: 100%; width: 100%;"></div>

                {{-- Status Circle --}}
                <div id="statusCircle" class="status-circle">
                    <div id="statusDot" class="status-dot offline"></div>
                    <div class="status-time" id="circleTime">--<small>min</small></div>
                    <div class="status-distance" id="circleDistance">--<small>km</small></div>
                    <div class="direction-indicator" id="directionIndicator">
                        <i class="fas fa-location-arrow" id="directionArrow"></i>
                        <span class="direction-text" id="directionText">--</span>
                    </div>
                </div>

                {{-- Debug badge (remove in production) --}}
                <div class="debug-badge" id="debugBadge">Waiting...</div>

                <div class="map-controls">
                    <button class="map-control-btn" onclick="centerOnAgent()" title="Center on agent">
                        <i class="fas fa-crosshairs"></i>
                    </button>
                    <button class="map-control-btn" onclick="fitBothMarkers()" title="Show both markers">
                        <i class="fas fa-compress-arrows-alt"></i>
                    </button>
                    <button class="map-control-btn" onclick="zoomIn()" title="Zoom in">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button class="map-control-btn" onclick="zoomOut()" title="Zoom out">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button class="map-control-btn" onclick="toggleFullscreen()" title="Full screen">
                        <i class="fas fa-expand"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden data inputs --}}
    <input type="hidden" id="shipmentId" value="{{ $shipment->id }}">
    <input type="hidden" id="agentId" value="{{ $agent->id ?? 0 }}">
    <input type="hidden" id="destLat" value="{{ $shipment->destination_latitude ?? '' }}">
    <input type="hidden" id="destLng" value="{{ $shipment->destination_longitude ?? '' }}">
    <input type="hidden" id="warehouseLat" value="{{ env('WAREHOUSE_LAT', 22.524768) }}">
    <input type="hidden" id="warehouseLng" value="{{ env('WAREHOUSE_LNG', 72.955568) }}">
    <input type="hidden" id="agentLat" value="{{ $agent->current_latitude ?? '' }}">
    <input type="hidden" id="agentLng" value="{{ $agent->current_longitude ?? '' }}">

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async
        defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        // ==================== CONSTANTS ====================
        const SHIPMENT_ID = parseInt(document.getElementById('shipmentId').value) || 0;
        const AGENT_ID = parseInt(document.getElementById('agentId').value) || 0;
        const DEST_LAT = parseFloat(document.getElementById('destLat').value) || 0;
        const DEST_LNG = parseFloat(document.getElementById('destLng').value) || 0;
        const WAREHOUSE_LAT = parseFloat(document.getElementById('warehouseLat').value) || 0;
        const WAREHOUSE_LNG = parseFloat(document.getElementById('warehouseLng').value) || 0;

        // ==================== STATE ====================
        let map, directionsService, directionsRenderer;
        let agentMarker, destMarker, warehouseMarker;
        let mapInitialized = false;
        let updateInterval = null;
        let consecutiveErrors = 0;

        let currentLat = parseFloat(document.getElementById('agentLat').value) || 0;
        let currentLng = parseFloat(document.getElementById('agentLng').value) || 0;
        let currentSpeed = 0;

        let lastPosition = {
            lat: null,
            lng: null,
            time: Date.now()
        };
        let lastDirection = '';
        let lastDistance = 0;
        let totalDistance = 0; // set once on first successful fetch

        // ==================== HELPERS ====================
        function haversine(lat1, lng1, lat2, lng2) {
            if (!lat1 || !lng1 || !lat2 || !lng2) return 0;
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLng = (lng2 - lng1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) ** 2 +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLng / 2) ** 2;
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        }

        function calcBearing(lat1, lng1, lat2, lng2) {
            const φ1 = lat1 * Math.PI / 180,
                φ2 = lat2 * Math.PI / 180;
            const Δλ = (lng2 - lng1) * Math.PI / 180;
            const y = Math.sin(Δλ) * Math.cos(φ2);
            const x = Math.cos(φ1) * Math.sin(φ2) - Math.sin(φ1) * Math.cos(φ2) * Math.cos(Δλ);
            return (Math.atan2(y, x) * 180 / Math.PI + 360) % 360;
        }

        function bearingToDir(b) {
            return ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'][Math.round(b / 45) % 8];
        }

        function isValidCoord(v) {
            const n = parseFloat(v);
            return !isNaN(n) && n !== 0;
        }

        function agentAssigned() {
            return AGENT_ID > 0 && isValidCoord(currentLat) && isValidCoord(currentLng) &&
                isValidCoord(DEST_LAT) && isValidCoord(DEST_LNG);
        }

        function debug(msg) {
            const el = document.getElementById('debugBadge');
            if (el) el.textContent = msg;
            console.log('[LiveTrack]', msg);
        }

        // ==================== MAP INIT ====================
        function initMap() {
            if (!agentAssigned()) {
                showNoAgent();
                return;
            }

            try {
                map = new google.maps.Map(document.getElementById('trackingMap'), {
                    center: {
                        lat: currentLat,
                        lng: currentLng
                    },
                    zoom: 13,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    styles: [{
                        featureType: 'poi',
                        elementType: 'labels',
                        stylers: [{
                            visibility: 'off'
                        }]
                    }],
                    zoomControl: false,
                    fullscreenControl: false
                });

                directionsService = new google.maps.DirectionsService();
                directionsRenderer = new google.maps.DirectionsRenderer({
                    map,
                    suppressMarkers: true,
                    polylineOptions: {
                        strokeColor: '#667eea',
                        strokeWeight: 5,
                        strokeOpacity: 0.8
                    }
                });

                // Warehouse marker (blue)
                if (isValidCoord(WAREHOUSE_LAT) && isValidCoord(WAREHOUSE_LNG)) {
                    warehouseMarker = new google.maps.Marker({
                        position: {
                            lat: WAREHOUSE_LAT,
                            lng: WAREHOUSE_LNG
                        },
                        map,
                        title: 'Warehouse',
                        icon: {
                            url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                            scaledSize: new google.maps.Size(36, 36)
                        }
                    });
                }

                // Destination marker (red)
                destMarker = new google.maps.Marker({
                    position: {
                        lat: DEST_LAT,
                        lng: DEST_LNG
                    },
                    map,
                    title: 'Destination',
                    animation: google.maps.Animation.DROP,
                    icon: {
                        url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                        scaledSize: new google.maps.Size(44, 44)
                    }
                });

                destMarker.addListener('click', () => {
                    new google.maps.InfoWindow({
                        content: `<div style="padding:8px;"><strong>📍 Delivery Destination</strong><br>
                              {{ $shipment->receiver_name }}<br>
                              {{ $shipment->shipping_address }}</div>`
                    }).open(map, destMarker);
                });

                // Agent marker (green motorcycle)
                agentMarker = new google.maps.Marker({
                    position: {
                        lat: currentLat,
                        lng: currentLng
                    },
                    map,
                    title: 'Delivery Partner',
                    animation: google.maps.Animation.DROP,
                    icon: {
                        url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                        scaledSize: new google.maps.Size(48, 48)
                    }
                });

                agentMarker.addListener('click', () => {
                    new google.maps.InfoWindow({
                        content: `<div style="padding:8px;"><strong>🏍️ {{ $agent->name ?? 'Delivery Partner' }}</strong><br>
                              Speed: <span id="popupSpeed">${Math.round(currentSpeed)}</span> km/h</div>`
                    }).open(map, agentMarker);
                });

                fitBothMarkers();
                mapInitialized = true;

                // Set total distance for progress bar (warehouse → destination)
                totalDistance = haversine(WAREHOUSE_LAT, WAREHOUSE_LNG, DEST_LAT, DEST_LNG);

                drawRoute();
                startPolling();
                debug('Map initialized ✅');

            } catch (err) {
                console.error('Map init error:', err);
                debug('Map init error: ' + err.message);
            }
        }

        // ==================== DRAW ROUTE ====================
        function drawRoute() {
            if (!directionsService || !directionsRenderer) return;
            if (!isValidCoord(currentLat) || !isValidCoord(currentLng)) return;
            if (!isValidCoord(DEST_LAT) || !isValidCoord(DEST_LNG)) return;

            directionsService.route({
                origin: {
                    lat: currentLat,
                    lng: currentLng
                },
                destination: {
                    lat: DEST_LAT,
                    lng: DEST_LNG
                },
                travelMode: google.maps.TravelMode.DRIVING
            }, (result, status) => {
                if (status === 'OK') {
                    directionsRenderer.setDirections(result);
                    const leg = result.routes[0].legs[0];
                    const distKm = leg.distance.value / 1000;
                    const durationMin = Math.round(leg.duration.value / 60);
                    updateLeftPanel(distKm, durationMin, currentSpeed, 0);
                    updateStatusCircle(distKm, durationMin, true, currentSpeed);
                    debug(`Route OK — ${distKm.toFixed(1)} km, ${durationMin} min`);
                } else {
                    debug('Directions failed: ' + status);
                    // Fallback: update using haversine
                    const distKm = haversine(currentLat, currentLng, DEST_LAT, DEST_LNG);
                    const timeMin = currentSpeed > 0 ? (distKm / currentSpeed) * 60 : distKm * 2.5;
                    updateLeftPanel(distKm, timeMin, currentSpeed, 0);
                    updateStatusCircle(distKm, timeMin, true, currentSpeed);
                }
            });
        }

        // ==================== REVERSE GEOCODING ====================
        let geocoder = null;
        let lastGeocodedLat = null;
        let lastGeocodedLng = null;
        const GEO_MIN_MOVE_KM = 0.1; // only re-geocode if agent moved 100m+

        function reverseGeocode(lat, lng) {
            // Only re-geocode if agent moved meaningfully (saves API quota)
            if (lastGeocodedLat !== null) {
                const moved = haversine(lastGeocodedLat, lastGeocodedLng, lat, lng);
                if (moved < GEO_MIN_MOVE_KM) return;
            }

            if (!geocoder) {
                if (typeof google === 'undefined' || !google.maps) return;
                geocoder = new google.maps.Geocoder();
            }

            lastGeocodedLat = lat;
            lastGeocodedLng = lng;

            geocoder.geocode({
                location: {
                    lat,
                    lng
                }
            }, (results, status) => {
                const card = document.getElementById('currentLocationCard');
                const areaEl = document.getElementById('clArea');
                const coordEl = document.getElementById('clCoords');
                const updEl = document.getElementById('clUpdated');

                if (!card) return;
                card.style.display = 'block';

                if (status === 'OK' && results && results.length > 0) {
                    // Try to get a friendly area name
                    // Priority: sublocality_level_1 > locality > route > formatted_address
                    let area = '';
                    let locality = '';

                    for (const component of results[0].address_components) {
                        if (!area && (component.types.includes('sublocality_level_1') || component.types.includes(
                                'sublocality'))) {
                            area = component.long_name;
                        }
                        if (!locality && component.types.includes('locality')) {
                            locality = component.long_name;
                        }
                    }

                    // Build display string
                    if (area && locality) {
                        areaEl.textContent = area + ', ' + locality;
                    } else if (results[0].formatted_address) {
                        // Trim long formatted address to first 2 parts
                        const parts = results[0].formatted_address.split(',');
                        areaEl.textContent = parts.slice(0, 2).join(',').trim();
                    } else {
                        areaEl.textContent = 'Location found';
                    }
                } else {
                    // Geocoding failed — show coordinates at least
                    areaEl.textContent = 'GPS Location Active';
                }

                if (coordEl) coordEl.textContent = lat.toFixed(5) + ', ' + lng.toFixed(5);
                if (updEl) updEl.textContent = new Date().toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
            });
        }

        // ==================== UPDATE AGENT POSITION ====================
        function updateAgentPosition(lat, lng, speed, accuracy) {
            lat = parseFloat(lat);
            lng = parseFloat(lng);
            if (isNaN(lat) || isNaN(lng) || (lat === 0 && lng === 0)) return;

            currentLat = lat;
            currentLng = lng;
            currentSpeed = parseFloat(speed) || 0;

            // Move marker
            if (agentMarker && mapInitialized) {
                agentMarker.setPosition({
                    lat,
                    lng
                });
                agentMarker.setAnimation(google.maps.Animation.BOUNCE);
                setTimeout(() => agentMarker.setAnimation(null), 700);
                drawRoute();
            } else if (!mapInitialized) {
                // Map not ready yet — try re-initializing
                initMap();
                return;
            }

            // Distance from agent to dest (straight line, for immediate display)
            const distKm = haversine(lat, lng, DEST_LAT, DEST_LNG);
            const timeMin = currentSpeed > 0 ? (distKm / currentSpeed) * 60 : distKm * 2.5;

            updateLeftPanel(distKm, timeMin, currentSpeed, accuracy);
            updateStatusCircle(distKm, timeMin, true, currentSpeed);
            updateDirection(lat, lng);
            reverseGeocode(lat, lng); // update "agent abhi yahan hai" card
            const liveStatusDiv = document.getElementById('liveStatus');
            if (liveStatusDiv) {
                liveStatusDiv.innerHTML = `
                <div class="live-pulse"></div>
                <span style="font-size:13px;">Live Tracking Active</span>
                <div class="last-update" id="lastUpdate">Just now</div>`;
            }

            consecutiveErrors = 0;
            debug(
                `Pos: ${lat.toFixed(5)}, ${lng.toFixed(5)} | ${Math.round(currentSpeed)} km/h | ${distKm.toFixed(2)} km left`);
        }

        // ==================== UPDATE UI PANELS ====================
        function updateLeftPanel(distKm, timeMin, speed, accuracy) {
            const progress = totalDistance > 0 ?
                Math.min(((totalDistance - distKm) / totalDistance) * 100, 100) :
                0;

            const eta = new Date(Date.now() + timeMin * 60000);

            setText('distanceLeft', distKm.toFixed(1) + ' <span class="eta-unit">km</span>');
            setText('timeLeft', Math.round(timeMin) + ' <span style="font-size:10px;">min</span>');
            setText('currentSpeed', Math.round(speed) + ' <span style="font-size:10px;">km/h</span>');

            if (accuracy) {
                setText('gpsAccuracy', Math.round(accuracy) + ' <span style="font-size:10px;">m</span>');
            }

            const etaEl = document.getElementById('etaTime');
            if (etaEl) etaEl.textContent = eta.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });

            const etaDateEl = document.getElementById('etaDate');
            if (etaDateEl) {
                etaDateEl.textContent = eta.toDateString() === new Date().toDateString() ?
                    'Today' : eta.toLocaleDateString([], {
                        month: 'short',
                        day: 'numeric'
                    });
            }

            const bar = document.getElementById('progressFill');
            if (bar) bar.style.width = progress + '%';
        }

        function updateStatusCircle(distKm, timeMin, isLive, speed) {
            setText('circleTime', Math.round(timeMin) + '<small>min</small>');
            setText('circleDistance', distKm.toFixed(1) + '<small>km</small>');

            const dot = document.getElementById('statusDot');
            if (dot) dot.className = isLive ? 'status-dot live' : 'status-dot offline';

            if (distKm < 0.5 && lastDistance >= 0.5 && isLive) {
                playNearDestinationBeep();
            }
            lastDistance = distKm;
        }

        function updateDirection(lat, lng) {
            if (!lastPosition.lat) {
                lastPosition = {
                    lat,
                    lng,
                    time: Date.now()
                };
                return;
            }

            const bearing = calcBearing(lastPosition.lat, lastPosition.lng, lat, lng);
            const direction = bearingToDir(bearing);

            const arrowEl = document.getElementById('directionArrow');
            const textEl = document.getElementById('directionText');
            if (arrowEl) arrowEl.style.transform = `rotate(${bearing}deg)`;
            if (textEl) textEl.textContent = direction;

            if (lastDirection && lastDirection !== direction) playBeep();
            lastDirection = direction;
            lastPosition = {
                lat,
                lng,
                time: Date.now()
            };
        }

        function setText(id, html) {
            const el = document.getElementById(id);
            if (el) el.innerHTML = html;
        }

        // ==================== POLLING — THIS IS THE FIXED PART ====================
        /**
         * TWO-STRATEGY FETCH:
         *
         * Strategy 1 (preferred):  GET /api/agent-location/realtime/{shipmentId}
         *   → returns { success: true, data: { location: { latitude, longitude, speed_kmh, accuracy }, ... } }
         *
         * Strategy 2 (fallback):   GET /logistics/agents/{agentId}/location
         *   → returns { latitude, longitude, accuracy, status }  (NO success field)
         *
         * The old code only tried Strategy 2 and checked data.success → always undefined → never updated map.
         * Now we try Strategy 1 first, fall back to Strategy 2 if needed.
         */
        async function fetchAgentLocation() {
            if (!AGENT_ID) return;

            try {
                // ---- Strategy 1: dedicated realtime endpoint ----
                const res1 = await fetch(`/api/agent-location/realtime/${SHIPMENT_ID}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (res1.ok) {
                    const data = await res1.json();

                    if (data.success && data.data && data.data.location) {
                        const loc = data.data.location;
                        const lat = parseFloat(loc.latitude);
                        const lng = parseFloat(loc.longitude);

                        if (!isNaN(lat) && !isNaN(lng) && (lat !== 0 || lng !== 0)) {
                            updateAgentPosition(lat, lng, loc.speed_kmh || 0, loc.accuracy || 0);

                            // Update lastUpdate text
                            const luEl = document.getElementById('lastUpdate');
                            if (luEl) luEl.textContent = loc.recorded_at_human || 'Just now';
                            return; // success — skip fallback
                        }
                    }
                }
            } catch (e) {
                debug('Strategy 1 error: ' + e.message);
            }

            // ---- Strategy 2: simple agent location endpoint (fallback) ----
            try {
                const res2 = await fetch(`/logistics/agents/${AGENT_ID}/location`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (res2.ok) {
                    const data = await res2.json();

                    // This endpoint returns latitude/longitude directly (no 'success' wrapper)
                    const lat = parseFloat(data.latitude);
                    const lng = parseFloat(data.longitude);

                    if (!isNaN(lat) && !isNaN(lng) && (lat !== 0 || lng !== 0)) {
                        updateAgentPosition(lat, lng, data.speed || 0, data.accuracy || 0);

                        const luEl = document.getElementById('lastUpdate');
                        if (luEl) luEl.textContent = data.last_update ?
                            new Date(data.last_update).toLocaleTimeString([], {
                                hour: '2-digit',
                                minute: '2-digit'
                            }) :
                            'Just now';
                        return;
                    }
                }

                consecutiveErrors++;
                if (consecutiveErrors >= 5) {
                    const liveStatusDiv = document.getElementById('liveStatus');
                    if (liveStatusDiv) {
                        liveStatusDiv.innerHTML = `
                        <div style="color:#fca5a5;display:flex;align-items:center;gap:8px;">
                            <i class="fas fa-exclamation-circle"></i>
                            <span style="font-size:13px;">Agent offline or GPS unavailable</span>
                        </div>`;
                    }
                    const dot = document.getElementById('statusDot');
                    if (dot) dot.className = 'status-dot offline';
                    debug('Agent offline (5 consecutive errors)');
                }

            } catch (e) {
                debug('Strategy 2 error: ' + e.message);
            }
        }

        function startPolling() {
            if (!AGENT_ID) return;
            fetchAgentLocation(); // immediate first call
            updateInterval = setInterval(fetchAgentLocation, 4000); // then every 4s
            console.log('✅ Polling started (every 4s) for agent', AGENT_ID, 'shipment', SHIPMENT_ID);
        }

        // ==================== MAP CONTROLS ====================
        function fitBothMarkers() {
            if (!map) return;
            const bounds = new google.maps.LatLngBounds();
            if (agentMarker) bounds.extend(agentMarker.getPosition());
            if (destMarker) bounds.extend(destMarker.getPosition());
            if (!bounds.isEmpty()) map.fitBounds(bounds);
        }

        function centerOnAgent() {
            if (agentMarker && map) {
                map.setCenter(agentMarker.getPosition());
                map.setZoom(16);
                agentMarker.setAnimation(google.maps.Animation.BOUNCE);
                setTimeout(() => agentMarker.setAnimation(null), 1000);
            }
        }

        function zoomIn() {
            if (map) map.setZoom(map.getZoom() + 1);
        }

        function zoomOut() {
            if (map) map.setZoom(map.getZoom() - 1);
        }

        function toggleFullscreen() {
            const el = document.querySelector('.map-panel');
            if (!document.fullscreenElement) el.requestFullscreen();
            else document.exitFullscreen();
        }

        function goToShipmentDetails() {
            window.location.href = `/logistics/shipments/${SHIPMENT_ID}`;
        }

        // ==================== NO AGENT STATE ====================
        function showNoAgent() {
            const mapEl = document.getElementById('trackingMap');
            if (mapEl) mapEl.innerHTML = `
            <div class="no-agent-message">
                <i class="fas fa-user-clock"></i>
                <div class="info-box">
                    <h3>No Delivery Partner Assigned</h3>
                    <p>Live tracking will start once a delivery partner is assigned to this shipment.</p>
                    <button class="assign-btn-large" onclick="goToShipmentDetails()">
                        <i class="fas fa-user-plus"></i> Assign Agent
                    </button>
                </div>
            </div>`;

            const liveStatusDiv = document.getElementById('liveStatus');
            if (liveStatusDiv) liveStatusDiv.innerHTML = `
            <div style="display:flex;align-items:center;gap:8px;">
                <i class="fas fa-clock" style="opacity:0.7;"></i>
                <span>No agent assigned</span>
            </div>`;
        }

        // ==================== SOUND ====================
        let audioCtx = null;

        function playBeep(freq = 800, dur = 0.25) {
            try {
                if (!audioCtx) audioCtx = new(window.AudioContext || window.webkitAudioContext)();
                const osc = audioCtx.createOscillator();
                const g = audioCtx.createGain();
                osc.connect(g);
                g.connect(audioCtx.destination);
                osc.frequency.value = freq;
                g.gain.value = 0.25;
                osc.start();
                g.gain.exponentialRampToValueAtTime(0.00001, audioCtx.currentTime + dur);
                osc.stop(audioCtx.currentTime + dur);
            } catch (e) {}
        }

        function playNearDestinationBeep() {
            playBeep(1000, 0.2);
            setTimeout(() => playBeep(1200, 0.2), 300);
        }

        document.addEventListener('click', () => {
            if (audioCtx && audioCtx.state === 'suspended') audioCtx.resume();
        }, {
            once: true
        });

        // ==================== EXPOSE GLOBALS ====================
        window.initMap = initMap;
        window.centerOnAgent = centerOnAgent;
        window.fitBothMarkers = fitBothMarkers;
        window.zoomIn = zoomIn;
        window.zoomOut = zoomOut;
        window.toggleFullscreen = toggleFullscreen;
        window.goToShipmentDetails = goToShipmentDetails;

        // ==================== CLEANUP ====================
        window.addEventListener('beforeunload', () => {
            if (updateInterval) clearInterval(updateInterval);
        });
    </script>
@endsection
