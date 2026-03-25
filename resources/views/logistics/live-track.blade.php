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
            gap: 0;
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
            background: #e9ecef;
        }

        /* Status Circle - TOP RIGHT CORNER */
        .status-circle {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: white;
            border-radius: 50%;
            width: 130px;
            height: 130px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
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

        .direction-indicator .direction-text {
            font-weight: 600;
            color: #1f2937;
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

        .no-agent-message .assign-btn-large {
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

        .no-agent-message .assign-btn-large:hover {
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
                font-size: 24px;
            }

            .status-distance {
                font-size: 16px;
            }

            .direction-indicator {
                font-size: 11px;
                padding: 3px 8px;
            }
        }

        @media (max-width: 480px) {
            .status-circle {
                width: 90px;
                height: 90px;
            }

            .status-time {
                font-size: 20px;
            }

            .status-distance {
                font-size: 14px;
            }

            .direction-indicator {
                font-size: 10px;
                padding: 2px 6px;
            }

            .status-dot {
                width: 10px;
                height: 10px;
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
                                <span>{{ $agent->total_deliveries ?? '1,234' }} deliveries</span>
                            </p>
                        </div>
                    </div>
                    <div class="live-status" id="liveStatus">
                        @if ($agent && $agent->id && $agent->current_latitude && $agent->current_longitude)
                            <div class="live-pulse"></div>
                            <span style="font-size: 13px;">Live Tracking Active</span>
                            <div class="last-update" id="lastUpdate">Updating...</div>
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

                <div class="eta-card">
                    <div class="eta-header">
                        <div>
                            <div class="eta-label">ESTIMATED ARRIVAL</div>
                            <div class="eta-value" id="etaTime">--:--</div>
                            <div class="eta-label" id="etaDate">Today</div>
                        </div>
                        <div style="text-align: right;">
                            <div class="eta-label">DISTANCE LEFT</div>
                            <div class="eta-value" id="distanceLeft">0 <span class="eta-unit">km</span></div>
                        </div>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill" style="width: 0%"></div>
                    </div>
                    <div class="stats-row">
                        <div class="stat-item">
                            <div class="stat-label-sm">CURRENT SPEED</div>
                            <div class="stat-value-sm" id="currentSpeed">0 <span style="font-size: 10px;">km/h</span></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label-sm">GPS ACCURACY</div>
                            <div class="stat-value-sm" id="gpsAccuracy">0 <span style="font-size: 10px;">m</span></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label-sm">TIME LEFT</div>
                            <div class="stat-value-sm" id="timeLeft">0 <span style="font-size: 10px;">min</span></div>
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

            {{-- RIGHT PANEL - MAP WITH TOP-RIGHT STATUS CIRCLE --}}
            <div class="map-panel">
                <div id="trackingMap" style="height: 100%; width: 100%;"></div>

                {{-- Status Circle - TOP RIGHT CORNER --}}
                <div id="statusCircle" class="status-circle">
                    <div id="statusDot" class="status-dot offline"></div>
                    <div class="status-time" id="circleTime">--<small>min</small></div>
                    <div class="status-distance" id="circleDistance">0<small>km</small></div>
                    <div class="direction-indicator" id="directionIndicator">
                        <i class="fas fa-location-arrow" id="directionArrow"></i>
                        <span class="direction-text" id="directionText">--</span>
                    </div>
                </div>

                <div class="map-controls">
                    <button class="map-control-btn" onclick="centerOnAgent()" title="Center on agent">
                        <i class="fas fa-crosshairs"></i>
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

    <input type="hidden" id="shipmentId" value="{{ $shipment->id }}">
    <input type="hidden" id="agentId" value="{{ $agent->id ?? 0 }}">
    <input type="hidden" id="destLat" value="{{ $shipment->destination_latitude ?? 22.524768 }}">
    <input type="hidden" id="destLng" value="{{ $shipment->destination_longitude ?? 72.955568 }}">
    <input type="hidden" id="warehouseLat" value="{{ env('WAREHOUSE_LAT', 22.524768) }}">
    <input type="hidden" id="warehouseLng" value="{{ env('WAREHOUSE_LNG', 72.955568) }}">
    <input type="hidden" id="agentLat" value="{{ $agent->current_latitude ?? 0 }}">
    <input type="hidden" id="agentLng" value="{{ $agent->current_longitude ?? 0 }}">

    {{-- Google Maps API --}}
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async
        defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

    <script>
        // ==================== VARIABLES ====================
        let map;
        let directionsService;
        let directionsRenderer;
        let agentMarker;
        let destMarker;
        let warehouseMarker;
        let updateInterval;
        let lastPosition = {
            lat: null,
            lng: null,
            time: Date.now()
        };
        let lastDirection = '';
        let lastDistance = 0;
        let mapInitialized = false;

        let audioContext = null;
        let beepEnabled = true;

        const shipmentId = document.getElementById('shipmentId')?.value || 0;
        const agentId = parseInt(document.getElementById('agentId')?.value || 0);
        const destLat = parseFloat(document.getElementById('destLat')?.value || 0);
        const destLng = parseFloat(document.getElementById('destLng')?.value || 0);
        const warehouseLat = parseFloat(document.getElementById('warehouseLat')?.value || 0);
        const warehouseLng = parseFloat(document.getElementById('warehouseLng')?.value || 0);
        let currentLat = parseFloat(document.getElementById('agentLat')?.value || 0);
        let currentLng = parseFloat(document.getElementById('agentLng')?.value || 0);
        let currentSpeed = 0;

        // ==================== SOUND FUNCTIONS ====================
        function playBeep() {
            if (!beepEnabled) return;
            try {
                if (!audioContext) {
                    audioContext = new(window.AudioContext || window.webkitAudioContext)();
                }
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                oscillator.frequency.value = 800;
                gainNode.gain.value = 0.3;
                oscillator.start();
                gainNode.gain.exponentialRampToValueAtTime(0.00001, audioContext.currentTime + 0.3);
                oscillator.stop(audioContext.currentTime + 0.3);
            } catch (e) {
                console.log('Audio not supported');
            }
        }

        function playDirectionChangeSound() {
            playBeep();
        }

        function playNearDestinationSound() {
            if (!beepEnabled) return;
            try {
                if (!audioContext) {
                    audioContext = new(window.AudioContext || window.webkitAudioContext)();
                }
                for (let i = 0; i < 2; i++) {
                    setTimeout(() => {
                        const oscillator = audioContext.createOscillator();
                        const gainNode = audioContext.createGain();
                        oscillator.connect(gainNode);
                        gainNode.connect(audioContext.destination);
                        oscillator.frequency.value = 1000;
                        gainNode.gain.value = 0.4;
                        oscillator.start();
                        gainNode.gain.exponentialRampToValueAtTime(0.00001, audioContext.currentTime + 0.2);
                        oscillator.stop(audioContext.currentTime + 0.2);
                    }, i * 200);
                }
            } catch (e) {}
        }

        // ==================== HELPER FUNCTIONS ====================
        function isAgentAssigned() {
            if (!agentId || agentId === 0) return false;
            if (isNaN(currentLat) || isNaN(currentLng) || currentLat === 0 || currentLng === 0) return false;
            if (isNaN(destLat) || isNaN(destLng) || destLat === 0 || destLng === 0) return false;
            return true;
        }

        function calculateDistance(lat1, lon1, lat2, lon2) {
            if (!lat1 || !lon1 || !lat2 || !lon2) return 0;
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) ** 2 + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(
                dLon / 2) ** 2;
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        }

        function calculateBearing(lat1, lng1, lat2, lng2) {
            if (!lat1 || !lng1 || !lat2 || !lng2) return 0;
            const φ1 = lat1 * Math.PI / 180;
            const φ2 = lat2 * Math.PI / 180;
            const Δλ = (lng2 - lng1) * Math.PI / 180;
            const y = Math.sin(Δλ) * Math.cos(φ2);
            const x = Math.cos(φ1) * Math.sin(φ2) - Math.sin(φ1) * Math.cos(φ2) * Math.cos(Δλ);
            let θ = Math.atan2(y, x);
            return (θ * 180 / Math.PI + 360) % 360;
        }

        function getDirection(bearing) {
            const directions = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'];
            const index = Math.round(bearing / 45) % 8;
            return directions[index];
        }

        function updateStatusCircle(distanceKm, timeMinutes, isLive, speed = 0) {
            const circleTime = document.getElementById('circleTime');
            const circleDistance = document.getElementById('circleDistance');
            const statusDot = document.getElementById('statusDot');
            const directionText = document.getElementById('directionText');
            const directionArrow = document.getElementById('directionArrow');

            if (circleTime) circleTime.innerHTML = Math.round(timeMinutes) + '<small>min</small>';
            if (circleDistance) circleDistance.innerHTML = distanceKm.toFixed(1) + '<small>km</small>';

            if (statusDot) {
                if (isLive && speed > 0) {
                    statusDot.className = 'status-dot live';
                } else if (isLive) {
                    statusDot.className = 'status-dot live';
                } else {
                    statusDot.className = 'status-dot offline';
                }
            }

            // Update direction
            if (currentLat && currentLng && destLat && destLng) {
                const bearing = calculateBearing(currentLat, currentLng, destLat, destLng);
                const direction = getDirection(bearing);
                if (directionText) directionText.innerHTML = direction;
                if (directionArrow) directionArrow.style.transform = `rotate(${bearing}deg)`;

                if (lastDirection !== direction && lastDirection !== '' && isLive) {
                    playDirectionChangeSound();
                }
                lastDirection = direction;

                if (distanceKm < 0.5 && lastDistance >= 0.5 && isLive) {
                    playNearDestinationSound();
                }
                lastDistance = distanceKm;
            }
        }

        function showNoAgentMessage() {
            const mapContainer = document.getElementById('trackingMap');
            if (!mapContainer) return;
            mapContainer.innerHTML = `
                <div class="no-agent-message">
                    <i class="fas fa-user-clock"></i>
                    <div class="info-box">
                        <h3>No Delivery Partner Assigned</h3>
                        <p>Live tracking will start once a delivery partner is assigned to this shipment.</p>
                        <button class="assign-btn-large" onclick="goToShipmentDetails()">
                            <i class="fas fa-user-plus"></i> Assign Agent
                        </button>
                    </div>
                </div>
            `;
            const liveStatusDiv = document.getElementById('liveStatus');
            if (liveStatusDiv) {
                liveStatusDiv.innerHTML =
                    `<div style="display: flex; align-items: center; gap: 8px;"><i class="fas fa-clock" style="opacity: 0.7;"></i><span>No agent assigned</span></div>`;
            }
            updateStatusCircle(0, 0, false);
        }

        function goToShipmentDetails() {
            window.location.href = `/logistics/shipments/${shipmentId}`;
        }

        // ==================== MAP FUNCTIONS ====================
        function initMap() {
            const mapContainer = document.getElementById('trackingMap');
            if (!mapContainer) return;
            if (!isAgentAssigned()) {
                showNoAgentMessage();
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
                    }]
                });

                directionsService = new google.maps.DirectionsService();
                directionsRenderer = new google.maps.DirectionsRenderer({
                    map: map,
                    suppressMarkers: true,
                    polylineOptions: {
                        strokeColor: '#667eea',
                        strokeWeight: 5,
                        strokeOpacity: 0.8
                    }
                });

                if (warehouseLat && warehouseLat !== 0 && !isNaN(warehouseLat)) {
                    warehouseMarker = new google.maps.Marker({
                        position: {
                            lat: warehouseLat,
                            lng: warehouseLng
                        },
                        map: map,
                        title: 'Warehouse',
                        icon: {
                            url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                            scaledSize: new google.maps.Size(36, 36)
                        }
                    });
                }

                destMarker = new google.maps.Marker({
                    position: {
                        lat: destLat,
                        lng: destLng
                    },
                    map: map,
                    title: 'Destination',
                    icon: {
                        url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                        scaledSize: new google.maps.Size(44, 44)
                    }
                });

                agentMarker = new google.maps.Marker({
                    position: {
                        lat: currentLat,
                        lng: currentLng
                    },
                    map: map,
                    title: 'Delivery Partner',
                    icon: {
                        url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                        scaledSize: new google.maps.Size(48, 48)
                    },
                    animation: google.maps.Animation.DROP
                });

                drawRoute();
                const bounds = new google.maps.LatLngBounds();
                bounds.extend({
                    lat: currentLat,
                    lng: currentLng
                });
                bounds.extend({
                    lat: destLat,
                    lng: destLng
                });
                if (warehouseLat && warehouseLat !== 0) bounds.extend({
                    lat: warehouseLat,
                    lng: warehouseLng
                });
                map.fitBounds(bounds);

                mapInitialized = true;
                startLiveUpdates();
                console.log('✅ Google Maps live tracking initialized');
            } catch (error) {
                console.error('Map error:', error);
            }
        }

        function drawRoute() {
            if (!directionsService || !directionsRenderer) return;
            if (isNaN(currentLat) || isNaN(currentLng) || isNaN(destLat) || isNaN(destLng)) return;

            directionsService.route({
                origin: {
                    lat: currentLat,
                    lng: currentLng
                },
                destination: {
                    lat: destLat,
                    lng: destLng
                },
                travelMode: google.maps.TravelMode.DRIVING
            }, (result, status) => {
                if (status === 'OK') {
                    directionsRenderer.setDirections(result);
                    const leg = result.routes[0].legs[0];
                    const distanceKm = leg.distance.value / 1000;
                    const durationMin = Math.round(leg.duration.value / 60);
                    updateUI(distanceKm, durationMin);
                    updateStatusCircle(distanceKm, durationMin, true, currentSpeed);
                } else {
                    console.error('Directions request failed:', status);
                }
            });
        }

        function updateAgentPosition(lat, lng, speed = 0, accuracy = 50) {
            lat = parseFloat(lat);
            lng = parseFloat(lng);
            if (isNaN(lat) || isNaN(lng)) return;

            currentLat = lat;
            currentLng = lng;
            currentSpeed = speed;

            if (agentMarker && mapInitialized) {
                agentMarker.setPosition({
                    lat: lat,
                    lng: lng
                });
                // Animate marker
                agentMarker.setAnimation(google.maps.Animation.BOUNCE);
                setTimeout(() => agentMarker.setAnimation(null), 750);
                drawRoute();
            } else if (!mapInitialized && isAgentAssigned()) {
                initMap();
            }

            const distance = calculateDistance(lat, lng, destLat, destLng);
            const totalDist = calculateDistance(warehouseLat || lat, warehouseLng || lng, destLat, destLng);
            const progress = totalDist > 0 ? ((totalDist - distance) / totalDist) * 100 : 0;
            const timeLeft = speed > 0 ? (distance / speed) * 60 : distance * 2;

            // Update left panel UI
            const distanceLeftEl = document.getElementById('distanceLeft');
            const progressFillEl = document.getElementById('progressFill');
            const gpsAccuracyEl = document.getElementById('gpsAccuracy');
            const lastUpdateEl = document.getElementById('lastUpdate');
            const timeLeftEl = document.getElementById('timeLeft');
            const currentSpeedEl = document.getElementById('currentSpeed');

            if (distanceLeftEl) distanceLeftEl.innerHTML = distance.toFixed(1) + ' <span class="eta-unit">km</span>';
            if (progressFillEl) progressFillEl.style.width = Math.min(progress, 100) + '%';
            if (gpsAccuracyEl) gpsAccuracyEl.innerHTML = Math.round(accuracy) + ' <span style="font-size:10px;">m</span>';
            if (lastUpdateEl) lastUpdateEl.innerHTML = 'Just now';
            if (timeLeftEl) timeLeftEl.innerHTML = Math.round(timeLeft) + ' <span style="font-size:10px;">min</span>';
            if (currentSpeedEl) currentSpeedEl.innerHTML = Math.round(speed) + ' <span style="font-size:10px;">km/h</span>';

            // Update circle with distance and time
            updateStatusCircle(distance, timeLeft, true, speed);

            // Update live status
            const liveStatusDiv = document.getElementById('liveStatus');
            if (liveStatusDiv) {
                liveStatusDiv.innerHTML = `
                    <div class="live-pulse"></div>
                    <span style="font-size: 13px;">Live Tracking Active</span>
                    <div class="last-update" id="lastUpdate">Just now</div>
                `;
            }

            updateSpeedFromMovement(lat, lng);
        }

        function updateUI(distanceKm, timeMinutes) {
            const timeLeftEl = document.getElementById('timeLeft');
            const etaTime = document.getElementById('etaTime');
            const etaDate = document.getElementById('etaDate');

            if (timeLeftEl) timeLeftEl.innerHTML = Math.round(timeMinutes) + ' <span style="font-size:10px;">min</span>';

            const eta = new Date(Date.now() + timeMinutes * 60000);
            if (etaTime) etaTime.innerHTML = eta.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });
            if (etaDate) etaDate.innerHTML = eta.toDateString() === new Date().toDateString() ? 'Today' : eta
                .toLocaleDateString([], {
                    month: 'short',
                    day: 'numeric'
                });
        }

        function updateSpeedFromMovement(lat, lng) {
            const now = Date.now();
            const timeDiff = (now - lastPosition.time) / 1000 / 3600;
            if (lastPosition.lat && timeDiff > 0 && timeDiff < 0.1) {
                const distance = calculateDistance(lastPosition.lat, lastPosition.lng, lat, lng);
                const calculatedSpeed = distance / timeDiff;
                if (calculatedSpeed > 0 && calculatedSpeed < 120) {
                    currentSpeed = calculatedSpeed;
                    const speedEl = document.getElementById('currentSpeed');
                    if (speedEl) speedEl.innerHTML = Math.round(calculatedSpeed) +
                        ' <span style="font-size:10px;">km/h</span>';
                }
            }
            lastPosition = {
                lat: lat,
                lng: lng,
                time: now
            };
        }

        // ==================== FETCH AGENT LOCATION ====================
        async function fetchAgentLocation() {
            if (agentId === 0) return;

            try {
                // Correct endpoint: /logistics/agents/{id}/location
                const response = await fetch(`/logistics/agents/${agentId}/location`);
                const data = await response.json();

                if (data.success) {
                    let lat = parseFloat(data.latitude);
                    let lng = parseFloat(data.longitude);
                    let speed = parseFloat(data.speed) || 0;
                    let accuracy = parseFloat(data.accuracy) || 50;

                    if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
                        updateAgentPosition(lat, lng, speed, accuracy);
                    } else {
                        console.log('Invalid coordinates received:', {
                            lat,
                            lng
                        });
                    }
                } else {
                    console.error('API error:', data.error);
                }
            } catch (error) {
                console.error('Fetch error:', error);
            }
        }

        // ==================== START REAL-TIME UPDATES ====================
        function startLiveUpdates() {
            if (agentId && agentId !== 0) {
                // Initial fetch
                fetchAgentLocation();
                // Set interval for continuous updates (3 seconds for real-time)
                updateInterval = setInterval(fetchAgentLocation, 3000);
                console.log('✅ Live updates started every 3 seconds');
            }
        }

        // ==================== MAP CONTROLS ====================
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
            const elem = document.querySelector('.map-panel');
            if (!document.fullscreenElement) {
                elem.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        }

        // ==================== INITIALIZE AUDIO ON USER CLICK ====================
        document.addEventListener('click', function initAudio() {
            if (audioContext && audioContext.state === 'suspended') {
                audioContext.resume();
            }
            document.removeEventListener('click', initAudio);
        });

        // ==================== EXPOSE GLOBAL FUNCTIONS ====================
        window.initMap = initMap;
        window.centerOnAgent = centerOnAgent;
        window.zoomIn = zoomIn;
        window.zoomOut = zoomOut;
        window.toggleFullscreen = toggleFullscreen;
        window.goToShipmentDetails = goToShipmentDetails;

        // ==================== INITIALIZE ON PAGE LOAD ====================
        document.addEventListener('DOMContentLoaded', function() {
            if (isAgentAssigned()) {
                // Wait for Google Maps to load
                if (typeof google !== 'undefined' && google.maps) {
                    initMap();
                    startLiveUpdates();
                } else {
                    // Check again after 500ms
                    setTimeout(() => {
                        if (typeof google !== 'undefined' && google.maps) {
                            initMap();
                            startLiveUpdates();
                        } else {
                            showNoAgentMessage();
                        }
                    }, 500);
                }
            } else {
                showNoAgentMessage();
            }
        });

        // ==================== CLEANUP ====================
        window.addEventListener('beforeunload', () => {
            if (updateInterval) clearInterval(updateInterval);
        });
    </script>
@endsection
