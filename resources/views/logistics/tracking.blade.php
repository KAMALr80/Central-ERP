{{-- resources/views/logistics/tracking.blade.php --}}
@extends('layouts.app')

@section('title', 'Track Shipment - ' . $shipment->shipment_number)

@section('content')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
        }

        .track-page {
            padding: 2rem 1.5rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header */
        .track-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border-radius: 30px;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
        }

        .track-header::before {
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            position: relative;
            z-index: 1;
        }

        .header-title h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .tracking-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 30px;
            font-size: 0.9rem;
            backdrop-filter: blur(4px);
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1.25rem;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: #f59e0b;
            color: white;
        }

        .status-picked {
            background: #3b82f6;
            color: white;
        }

        .status-in_transit {
            background: #8b5cf6;
            color: white;
        }

        .status-out_for_delivery {
            background: #10b981;
            color: white;
        }

        .status-delivered {
            background: #10b981;
            color: white;
        }

        .status-failed {
            background: #ef4444;
            color: white;
        }

        /* Main Grid */
        .track-grid {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 1.5rem;
        }

        @media (max-width: 992px) {
            .track-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Map Card */
        .map-card {
            background: white;
            border-radius: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .map-header {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .map-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: #1e293b;
        }

        .map-title i {
            color: #667eea;
        }

        .live-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: #fee2e2;
            padding: 0.25rem 0.75rem;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #dc2626;
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

        .map-container {
            height: 550px;
            width: 100%;
            position: relative;
        }

        #trackMap {
            height: 100%;
            width: 100%;
        }

        .map-controls {
            position: absolute;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .map-control-btn {
            width: 40px;
            height: 40px;
            background: white;
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            transition: all 0.2s;
        }

        .map-control-btn:hover {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            transform: scale(1.05);
        }

        /* Progress Card */
        .progress-card {
            background: white;
            border-radius: 30px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .progress-label {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
        }

        .progress-bar {
            height: 8px;
            background: #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            width: 0%;
            transition: width 0.5s ease;
            border-radius: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-top: 1rem;
        }

        .stat-item {
            background: #f8fafc;
            border-radius: 16px;
            padding: 1rem;
            text-align: center;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        /* Info Card */
        .info-card {
            background: white;
            border-radius: 30px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .info-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-title i {
            color: #667eea;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #64748b;
            font-size: 0.9rem;
        }

        .info-value {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.9rem;
            text-align: right;
        }

        /* Timeline */
        .timeline {
            max-height: 350px;
            overflow-y: auto;
            padding-left: 1rem;
            position: relative;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1.25rem;
            padding-left: 1.5rem;
            border-left: 2px solid #e5e7eb;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-badge {
            position: absolute;
            left: -8px;
            top: 0;
            width: 14px;
            height: 14px;
            background: #667eea;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 0 0 2px #667eea;
        }

        .timeline-item.delivered .timeline-badge {
            background: #10b981;
            box-shadow: 0 0 0 2px #10b981;
        }

        .timeline-time {
            font-size: 0.7rem;
            color: #94a3b8;
            margin-bottom: 0.25rem;
        }

        .timeline-status {
            font-weight: 600;
            font-size: 0.85rem;
            color: #1e293b;
        }

        .timeline-location {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        /* Share Button */
        .share-btn {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .share-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        /* Toast */
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            padding: 1rem 1.5rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border-left: 4px solid #10b981;
            display: none;
            z-index: 10000;
            animation: slideInRight 0.3s ease;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0%);
                opacity: 1;
            }
        }

        /* Loading */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(5px);
            z-index: 11000;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 1.5rem;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #e5e7eb;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .loading-text {
            color: #1e293b;
            font-weight: 600;
            font-size: 1.1rem;
            background: white;
            padding: 1rem 2rem;
            border-radius: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .track-page {
                padding: 1rem;
            }

            .header-title h1 {
                font-size: 1.5rem;
            }

            .map-container {
                height: 400px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="track-page">
        {{-- Loading Overlay --}}
        <div id="loadingOverlay" class="loading-overlay">
            <div class="spinner"></div>
            <div class="loading-text">Loading live location...</div>
        </div>

        {{-- Header --}}
        <div class="track-header">
            <div class="header-content">
                <div class="header-title">
                    <h1>
                        <i class="fas fa-box"></i>
                        Track Shipment
                        <span class="tracking-badge">
                            <i class="fas fa-barcode"></i>
                            {{ $shipment->tracking_number ?? $shipment->shipment_number }}
                        </span>
                    </h1>
                    <div>
                        <span class="status-badge status-{{ str_replace('_', '-', $shipment->status) }}">
                            {{ ucfirst(str_replace('_', ' ', $shipment->status)) }}
                        </span>
                    </div>
                </div>
                <div>
                    <button class="share-btn" id="shareBtn" style="background: rgba(255,255,255,0.2);">
                        <i class="fas fa-share-alt"></i> Share Tracking
                    </button>
                </div>
            </div>
        </div>

        <div class="track-grid">
            {{-- Map --}}
            <div class="map-card">
                <div class="map-header">
                    <div class="map-title">
                        <i class="fas fa-map-marked-alt"></i>
                        <span>Live Location Tracking</span>
                    </div>
                    <div class="live-badge">
                        <span class="live-dot"></span>
                        <span>LIVE UPDATES</span>
                    </div>
                </div>
                <div class="map-container">
                    <div id="trackMap"></div>
                    <div class="map-controls">
                        <button class="map-control-btn" onclick="centerOnCurrent()" title="Center on current location">
                            <i class="fas fa-location-dot"></i>
                        </button>
                        <button class="map-control-btn" onclick="zoomIn()" title="Zoom in">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button class="map-control-btn" onclick="zoomOut()" title="Zoom out">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Right Panel --}}
            <div>
                {{-- Progress Card --}}
                <div class="progress-card">
                    <div class="progress-label">
                        <span>Delivery Progress</span>
                        <span id="progressPercent">0%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value" id="distanceRemaining">--</div>
                            <div class="stat-label">Distance Remaining</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value" id="eta">--</div>
                            <div class="stat-label">Estimated Arrival</div>
                        </div>
                    </div>
                </div>

                {{-- Shipment Info --}}
                <div class="info-card">
                    <div class="info-title">
                        <i class="fas fa-info-circle"></i>
                        Shipment Details
                    </div>
                    <div class="info-row">
                        <span class="info-label">Shipment Number</span>
                        <span class="info-value">{{ $shipment->shipment_number }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tracking Number</span>
                        <span class="info-value">{{ $shipment->tracking_number ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Receiver</span>
                        <span class="info-value">{{ $shipment->receiver_name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Phone</span>
                        <span class="info-value">{{ $shipment->receiver_phone }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Address</span>
                        <span class="info-value">{{ \Illuminate\Support\Str::limit($shipment->full_address, 50) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Est. Delivery</span>
                        <span
                            class="info-value">{{ $shipment->estimated_delivery_date?->format('d M Y, h:i A') ?? 'Not set' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Last Updated</span>
                        <span class="info-value"
                            id="lastUpdated">{{ $shipment->last_location_update?->diffForHumans() ?? 'N/A' }}</span>
                    </div>
                </div>

                {{-- Tracking Timeline --}}
                <div class="info-card">
                    <div class="info-title">
                        <i class="fas fa-history"></i>
                        Tracking History
                    </div>
                    <div class="timeline">
                        @php $__col = $shipment->trackings()->latest()->take(10)->get(); @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $track)
                            <div class="timeline-item {{ $track->status == 'delivered' ? 'delivered' : '' }}">
                                <div class="timeline-badge"></div>
                                <div class="timeline-time">{{ $track->tracked_at->format('d M Y, h:i A') }}</div>
                                <div class="timeline-status">
                                    {{ ucfirst(str_replace('_', ' ', $track->status)) }}
                                </div>
                                @if ($track->location)
                                    <div class="timeline-location">
                                        <i class="fas fa-map-pin"></i> {{ $track->location }}
                                    </div>
                                @endif
                                @if ($track->remarks)
                                    <div class="timeline-location" style="color: #667eea;">
                                        <i class="fas fa-comment"></i> {{ $track->remarks }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
@else
                            <div class="timeline-item">
                                <div class="timeline-badge"></div>
                                <div class="timeline-status">No tracking history available</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div id="toast" class="toast"></div>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async
        defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

    <script>
        // ==================== VARIABLES ====================
        let map;
        let directionsService;
        let directionsRenderer;
        let currentMarker;
        let destMarker;
        let warehouseMarker;
        let updateInterval;

        // Coordinates
        const WAREHOUSE = {
            lat: {{ $origin['lat'] }},
            lng: {{ $origin['lng'] }}
        };
        const DESTINATION = {
            lat: {{ $destination['lat'] }},
            lng: {{ $destination['lng'] }}
        };
        let currentLat = {{ $currentLocation['lat'] }};
        let currentLng = {{ $currentLocation['lng'] }};
        let shipmentId = {{ $shipment->id }};
        let isLive = {{ $shipment->status != 'delivered' ? 'true' : 'false' }};

        // ==================== MAP INITIALIZATION ====================
        function initMap() {
            const centerLat = currentLat && currentLat !== 0 ? currentLat : DESTINATION.lat;
            const centerLng = currentLng && currentLng !== 0 ? currentLng : DESTINATION.lng;

            map = new google.maps.Map(document.getElementById('trackMap'), {
                center: {
                    lat: centerLat,
                    lng: centerLng
                },
                zoom: 12,
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

            // Warehouse marker
            warehouseMarker = new google.maps.Marker({
                position: WAREHOUSE,
                map: map,
                title: 'Warehouse',
                icon: {
                    url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                    scaledSize: new google.maps.Size(40, 40)
                }
            });

            // Destination marker
            destMarker = new google.maps.Marker({
                position: DESTINATION,
                map: map,
                title: 'Delivery Location',
                icon: {
                    url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                    scaledSize: new google.maps.Size(44, 44)
                }
            });

            // Current location marker
            if (currentLat && currentLng && currentLat !== 0 && currentLng !== 0) {
                currentMarker = new google.maps.Marker({
                    position: {
                        lat: currentLat,
                        lng: currentLng
                    },
                    map: map,
                    title: 'Current Location',
                    icon: {
                        url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                        scaledSize: new google.maps.Size(48, 48)
                    },
                    animation: google.maps.Animation.DROP
                });

                drawRoute();
            }

            // Fit bounds
            const bounds = new google.maps.LatLngBounds();
            bounds.extend(WAREHOUSE);
            bounds.extend(DESTINATION);
            if (currentLat && currentLng && currentLat !== 0) bounds.extend({
                lat: currentLat,
                lng: currentLng
            });
            map.fitBounds(bounds);

            // Start live updates if not delivered
            if (isLive) {
                startLiveUpdates();
            }

            // Hide loading
            setTimeout(() => {
                document.getElementById('loadingOverlay').style.display = 'none';
            }, 1000);
        }

        function drawRoute() {
            if (!directionsService || !currentLat || currentLat === 0) return;

            directionsService.route({
                origin: {
                    lat: currentLat,
                    lng: currentLng
                },
                destination: DESTINATION,
                travelMode: google.maps.TravelMode.DRIVING
            }, (result, status) => {
                if (status === 'OK') {
                    directionsRenderer.setDirections(result);
                    const leg = result.routes[0].legs[0];
                    const distance = leg.distance.text;
                    const duration = leg.duration.text;
                    const distanceKm = leg.distance.value / 1000;
                    const durationMin = Math.round(leg.duration.value / 60);

                    updateProgress(distanceKm, durationMin);
                    updateUI(distance, duration);
                }
            });
        }

        function updateProgress(distanceKm, durationMin) {
            const totalDist = calculateDistance(WAREHOUSE.lat, WAREHOUSE.lng, DESTINATION.lat, DESTINATION.lng);
            const progress = totalDist > 0 ? ((totalDist - distanceKm) / totalDist) * 100 : 0;

            const progressFill = document.getElementById('progressFill');
            const progressPercent = document.getElementById('progressPercent');
            const distanceRemaining = document.getElementById('distanceRemaining');
            const eta = document.getElementById('eta');

            if (progressFill) progressFill.style.width = Math.min(progress, 100) + '%';
            if (progressPercent) progressPercent.textContent = Math.round(progress) + '%';
            if (distanceRemaining) distanceRemaining.textContent = distanceKm.toFixed(1) + ' km';

            const etaTime = new Date(Date.now() + durationMin * 60000);
            if (eta) eta.textContent = etaTime.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function updateUI(distance, duration) {
            const lastUpdated = document.getElementById('lastUpdated');
            if (lastUpdated) lastUpdated.textContent = 'Just now';
        }

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) ** 2 + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(
                dLon / 2) ** 2;
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        }

        // ==================== LIVE UPDATES ====================
        function startLiveUpdates() {
            updateInterval = setInterval(fetchLiveLocation, 10000);
        }

        function fetchLiveLocation() {
            fetch(`/logistics/api/shipments/${shipmentId}/location`)
                .then(r => r.json())
                .then(data => {
                    if (data.latitude && data.longitude && data.latitude !== 0 && data.longitude !== 0) {
                        updateLocation(data.latitude, data.longitude);
                    }
                })
                .catch(e => console.log('Fetch error:', e));
        }

        function updateLocation(lat, lng) {
            currentLat = lat;
            currentLng = lng;

            if (currentMarker) {
                currentMarker.setPosition({
                    lat: lat,
                    lng: lng
                });
                currentMarker.setAnimation(google.maps.Animation.BOUNCE);
                setTimeout(() => currentMarker.setAnimation(null), 1000);
            } else {
                currentMarker = new google.maps.Marker({
                    position: {
                        lat: lat,
                        lng: lng
                    },
                    map: map,
                    title: 'Current Location',
                    icon: {
                        url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                        scaledSize: new google.maps.Size(48, 48)
                    },
                    animation: google.maps.Animation.DROP
                });
            }

            drawRoute();

            // Update last updated time
            const lastUpdated = document.getElementById('lastUpdated');
            if (lastUpdated) lastUpdated.textContent = 'Just now';

            // Auto-center on current location
            map.setCenter({
                lat: lat,
                lng: lng
            });
            map.setZoom(14);
        }

        // ==================== MAP CONTROLS ====================
        function centerOnCurrent() {
            if (currentLat && currentLng && currentLat !== 0) {
                map.setCenter({
                    lat: currentLat,
                    lng: currentLng
                });
                map.setZoom(15);
            } else {
                map.setCenter(DESTINATION);
                map.setZoom(12);
            }
        }

        function zoomIn() {
            map.setZoom(map.getZoom() + 1);
        }

        function zoomOut() {
            map.setZoom(map.getZoom() - 1);
        }

        // ==================== SHARE FUNCTION ====================
        document.getElementById('shareBtn').addEventListener('click', function() {
            const url = window.location.href;
            if (navigator.share) {
                navigator.share({
                    title: 'Track Shipment {{ $shipment->shipment_number }}',
                    text: 'Track your shipment delivery status',
                    url: url
                }).catch(() => copyToClipboard(url));
            } else {
                copyToClipboard(url);
            }
        });

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                showToast('Tracking link copied to clipboard!', 'success');
            }).catch(() => {
                showToast('Failed to copy link', 'error');
            });
        }

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.innerHTML = `<div class="toast-content"><span>${type === 'success' ? '✅' : '❌'}</span> ${message}</div>`;
            toast.style.borderLeftColor = type === 'success' ? '#10b981' : '#ef4444';
            toast.style.display = 'block';
            setTimeout(() => toast.style.display = 'none', 3000);
        }

        window.initMap = initMap;
        window.centerOnCurrent = centerOnCurrent;
        window.zoomIn = zoomIn;
        window.zoomOut = zoomOut;

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            if (updateInterval) clearInterval(updateInterval);
        });
    </script>
@endsection
