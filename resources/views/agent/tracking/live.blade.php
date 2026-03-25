{{-- resources/views/agent/tracking/live.blade.php --}}
@extends('layouts.app')

@section('title', 'Live Tracking - ' . $shipment->shipment_number)

@section('content')
    <style>
        :root {
            --primary: #3b82f6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #1e293b;
        }

        .tracking-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
            background: #f8fafc;
        }

        #map {
            flex: 1;
            min-height: 60vh;
            width: 100%;
        }

        /* Info Panel */
        .info-panel {
            background: white;
            border-radius: 24px 24px 0 0;
            padding: 20px;
            box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.1);
            margin-top: -20px;
            z-index: 10;
            position: relative;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 16px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 12px;
            background: #10b981;
            color: white;
        }

        .live-pulse {
            width: 10px;
            height: 10px;
            background: white;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 1.5s infinite;
            margin-right: 8px;
        }

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

        /* Metrics Cards */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 12px;
            margin: 15px 0;
        }

        .metric-card {
            background: linear-gradient(135deg, #f8fafc, #ffffff);
            border-radius: 16px;
            padding: 12px;
            text-align: center;
            border: 1px solid #e5e7eb;
            transition: all 0.3s;
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border-color: var(--primary);
        }

        .metric-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), #6366f1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            color: white;
            font-size: 18px;
        }

        .metric-value {
            font-size: 20px;
            font-weight: 700;
            color: var(--dark);
        }

        .metric-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Progress Bar */
        .progress-section {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 12px;
            margin: 15px 0;
        }

        .progress-bar-container {
            background: #e2e8f0;
            border-radius: 20px;
            height: 8px;
            overflow: hidden;
            margin: 8px 0;
        }

        .progress-bar-fill {
            background: linear-gradient(90deg, var(--primary), var(--success));
            height: 100%;
            width: 0%;
            transition: width 0.5s ease;
            border-radius: 20px;
        }

        /* Action Buttons */
        .btn-outline-primary {
            border: 2px solid var(--primary);
            background: transparent;
            color: var(--primary);
            padding: 10px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
        }

        .btn-outline-danger {
            border: 2px solid var(--danger);
            background: transparent;
            color: var(--danger);
            padding: 10px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-outline-danger:hover {
            background: var(--danger);
            color: white;
        }

        .btn-complete {
            background: linear-gradient(135deg, var(--success), #059669);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 16px;
            width: 100%;
            margin-top: 15px;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-complete:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        }

        /* Delivery Info */
        .delivery-info {
            background: #f8fafc;
            border-radius: 16px;
            padding: 15px;
            margin: 15px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .info-label {
            color: #64748b;
            font-weight: 500;
        }

        .info-value {
            font-weight: 600;
            color: var(--dark);
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 24px;
            border: none;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary), #6366f1);
            color: white;
            border-radius: 24px 24px 0 0;
        }

        .form-control {
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            padding: 12px;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Map Controls */
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
    </style>

    <div class="tracking-container">
        <!-- Map -->
        <div id="map" style="height: 100%; width: 100%;"></div>

        <!-- Status Circle -->
        <div id="statusCircle" class="status-circle">
            <div id="statusDot" class="status-dot offline"></div>
            <div class="status-time" id="circleTime">--<small>min</small></div>
            <div class="status-distance" id="circleDistance">0<small>km</small></div>
            <div class="direction-indicator" id="directionIndicator">
                <i class="fas fa-location-arrow" id="directionArrow"></i>
                <span class="direction-text" id="directionText">--</span>
            </div>
        </div>

        <!-- Map Controls -->
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

        <!-- Info Panel -->
        <div class="info-panel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-box text-primary me-2"></i> {{ $shipment->shipment_number }}
                    </h5>
                    <small class="text-muted">Tracking ID: {{ $shipment->tracking_number ?? 'N/A' }}</small>
                </div>
                <span class="status-badge" id="connectionStatus">
                    <span class="live-pulse"></span> Live
                </span>
            </div>

            <!-- Metrics Grid -->
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-icon"><i class="fas fa-tachometer-alt"></i></div>
                    <div class="metric-value" id="speedValue">0</div>
                    <div class="metric-label">Speed (km/h)</div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="metric-value" id="distanceValue">0</div>
                    <div class="metric-label">Distance (km)</div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon"><i class="fas fa-clock"></i></div>
                    <div class="metric-value" id="etaValue">--:--</div>
                    <div class="metric-label">ETA</div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon"><i class="fas fa-battery-full"></i></div>
                    <div class="metric-value" id="batteryValue">--</div>
                    <div class="metric-label">Battery</div>
                </div>
            </div>

            <!-- Progress Section -->
            <div class="progress-section">
                <div class="d-flex justify-content-between mb-1">
                    <span><i class="fas fa-flag-checkered text-danger"></i> Delivery Progress</span>
                    <span id="progressPercent">0%</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" id="progressFill"></div>
                </div>
            </div>

            <!-- Delivery Info -->
            <div class="delivery-info">
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-user"></i> Receiver:</span>
                    <span class="info-value">{{ $shipment->receiver_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-phone"></i> Phone:</span>
                    <span class="info-value">{{ $shipment->receiver_phone }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-map-marker-alt"></i> Address:</span>
                    <span class="info-value">{{ Str::limit($shipment->shipping_address, 50) }}, {{ $shipment->city }}</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row g-2">
                <div class="col-6">
                    <button class="btn-outline-primary w-100" onclick="centerOnCurrentLocation()">
                        <i class="fas fa-location-arrow me-2"></i> My Location
                    </button>
                </div>
                <div class="col-6">
                    <button class="btn-outline-danger w-100" onclick="centerOnDestination()">
                        <i class="fas fa-flag-checkered me-2"></i> Destination
                    </button>
                </div>
            </div>

            <button class="btn-complete" onclick="showCompleteModal()">
                <i class="fas fa-check-circle me-2"></i> Mark as Delivered
            </button>
        </div>
    </div>

    <!-- Delivery Completion Modal -->
    <div class="modal fade" id="completeModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i> Complete Delivery</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Customer Signature <span class="text-danger">*</span></label>
                        <input type="file" name="signature" id="signature" accept="image/*" class="form-control"
                            required>
                        <small class="text-muted">Take photo of customer signature</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Delivery Photo</label>
                        <input type="file" name="photo" id="photo" accept="image/*" class="form-control">
                        <small class="text-muted">Optional: Photo of delivered package</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Delivery Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Any issues or remarks..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="completeDelivery()">
                        <i class="fas fa-check me-2"></i> Confirm Delivery
                    </button>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="shipmentId" value="{{ $shipment->id }}">
    <input type="hidden" id="destLat" value="{{ $shipment->destination_latitude ?? 22.524768 }}">
    <input type="hidden" id="destLng" value="{{ $shipment->destination_longitude ?? 72.955568 }}">
    <input type="hidden" id="agentLat" value="{{ $agent->current_latitude ?? 22.524768 }}">
    <input type="hidden" id="agentLng" value="{{ $agent->current_longitude ?? 72.955568 }}">

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async
        defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

    <script>
        // ==================== GLOBAL VARIABLES ====================
        let map;
        let directionsService;
        let directionsRenderer;
        let agentMarker;
        let destMarker;
        let watchId;
        let updateInterval;
        let currentPosition = null;
        let lastPosition = {
            lat: null,
            lng: null,
            time: Date.now()
        };
        let lastDirection = '';
        let lastDistance = 0;

        const DEST_LAT = parseFloat(document.getElementById('destLat').value);
        const DEST_LNG = parseFloat(document.getElementById('destLng').value);
        const SHIPMENT_ID = parseInt(document.getElementById('shipmentId').value);
        let currentLat = parseFloat(document.getElementById('agentLat').value);
        let currentLng = parseFloat(document.getElementById('agentLng').value);
        let currentSpeed = 0;

        // ==================== INITIALIZE MAP ====================
        function initMap() {
            const center = {
                lat: currentLat || DEST_LAT,
                lng: currentLng || DEST_LNG
            };

            map = new google.maps.Map(document.getElementById('map'), {
                center: center,
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
                map: map,
                suppressMarkers: true,
                polylineOptions: {
                    strokeColor: '#3b82f6',
                    strokeWeight: 5,
                    strokeOpacity: 0.8
                }
            });

            // Destination Marker
            const destIcon = {
                url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                scaledSize: new google.maps.Size(48, 48),
                labelOrigin: new google.maps.Point(24, 24)
            };

            destMarker = new google.maps.Marker({
                position: {
                    lat: DEST_LAT,
                    lng: DEST_LNG
                },
                map: map,
                title: 'Destination',
                icon: destIcon,
                animation: google.maps.Animation.DROP,
                label: {
                    text: '📍',
                    color: 'white',
                    fontWeight: 'bold',
                    fontSize: '16px'
                }
            });

            destMarker.addListener('click', () => {
                new google.maps.InfoWindow({
                    content: `<div style="padding: 8px;"><strong>📍 Delivery Location</strong><br>{{ $shipment->receiver_name }}<br>{{ $shipment->shipping_address }}</div>`
                }).open(map, destMarker);
            });

            // Agent Marker
            const agentIcon = {
                url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                scaledSize: new google.maps.Size(48, 48),
                labelOrigin: new google.maps.Point(24, 24)
            };

            agentMarker = new google.maps.Marker({
                position: {
                    lat: currentLat,
                    lng: currentLng
                },
                map: map,
                title: 'Your Location',
                icon: agentIcon,
                animation: google.maps.Animation.DROP,
                label: {
                    text: '🏍️',
                    color: 'white',
                    fontWeight: 'bold',
                    fontSize: '16px'
                }
            });

            agentMarker.addListener('click', () => {
                new google.maps.InfoWindow({
                    content: `<div style="padding: 8px;"><strong>You are here</strong><br>Speed: <span id="speedDisplay">${currentSpeed}</span> km/h</div>`
                }).open(map, agentMarker);
            });

            drawRoute();
            startLocationTracking();

            // Fit bounds to show both points
            const bounds = new google.maps.LatLngBounds();
            bounds.extend({
                lat: currentLat,
                lng: currentLng
            });
            bounds.extend({
                lat: DEST_LAT,
                lng: DEST_LNG
            });
            map.fitBounds(bounds);
        }

        // ==================== DRAW ROUTE ====================
        function drawRoute() {
            if (!directionsService || !directionsRenderer) return;
            if (isNaN(currentLat) || isNaN(currentLng) || isNaN(DEST_LAT) || isNaN(DEST_LNG)) return;

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
                    const distanceKm = leg.distance.value / 1000;
                    const durationMin = Math.round(leg.duration.value / 60);
                    updateUI(distanceKm, durationMin);
                    updateStatusCircle(distanceKm, durationMin, true, currentSpeed);
                } else {
                    console.error('Directions request failed:', status);
                }
            });
        }

        // ==================== UPDATE AGENT POSITION ====================
        function updateAgentPosition(lat, lng, speed = 0, accuracy = 50) {
            lat = parseFloat(lat);
            lng = parseFloat(lng);
            if (isNaN(lat) || isNaN(lng)) return;

            currentLat = lat;
            currentLng = lng;
            currentSpeed = speed;

            if (agentMarker) {
                agentMarker.setPosition({
                    lat: lat,
                    lng: lng
                });
                agentMarker.setAnimation(google.maps.Animation.BOUNCE);
                setTimeout(() => agentMarker.setAnimation(null), 750);
                drawRoute();
            }

            const distance = calculateDistance(lat, lng, DEST_LAT, DEST_LNG);
            const totalDist = calculateDistance(22.524768, 72.955568, DEST_LAT, DEST_LNG);
            const progress = totalDist > 0 ? ((totalDist - distance) / totalDist) * 100 : 0;
            const timeLeft = speed > 0 ? (distance / speed) * 60 : distance * 2;

            document.getElementById('distanceValue').innerHTML = distance.toFixed(1);
            document.getElementById('speedValue').innerHTML = Math.round(speed);
            document.getElementById('progressPercent').innerHTML = `${Math.round(progress)}%`;
            document.getElementById('progressFill').style.width = `${progress}%`;
            document.getElementById('timeLeft').innerHTML = Math.round(timeLeft) + ' min';
            document.getElementById('currentSpeed').innerHTML = Math.round(speed) + ' km/h';
            document.getElementById('lastUpdate').innerHTML = 'Just now';
            updateStatusCircle(distance, timeLeft, true, speed);
            updateBearing(lat, lng);
        }

        // ==================== UPDATE BEARING AND DIRECTION ====================
        function updateBearing(lat, lng) {
            if (lastPosition.lat && lastPosition.lng) {
                const bearing = calculateBearing(lastPosition.lat, lastPosition.lng, lat, lng);
                const direction = getDirection(bearing);
                const directionArrow = document.getElementById('directionArrow');
                const directionText = document.getElementById('directionText');

                if (directionArrow) directionArrow.style.transform = `rotate(${bearing}deg)`;
                if (directionText) directionText.innerHTML = direction;

                if (lastDirection !== direction && lastDirection !== '') {
                    console.log('Direction changed to:', direction);
                }
                lastDirection = direction;
            }
            lastPosition = {
                lat: lat,
                lng: lng,
                time: Date.now()
            };
        }

        function calculateBearing(lat1, lng1, lat2, lng2) {
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

        // ==================== UPDATE UI ====================
        function updateUI(distanceKm, timeMinutes) {
            const eta = new Date(Date.now() + timeMinutes * 60000);
            document.getElementById('etaValue').innerHTML = eta.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });
            document.getElementById('etaDate').innerHTML = eta.toDateString() === new Date().toDateString() ? 'Today' : eta
                .toLocaleDateString();
            document.getElementById('timeLeft').innerHTML = Math.round(timeMinutes) + ' min';
        }

        function updateStatusCircle(distanceKm, timeMinutes, isLive, speed = 0) {
            document.getElementById('circleTime').innerHTML = Math.round(timeMinutes) + '<small>min</small>';
            document.getElementById('circleDistance').innerHTML = distanceKm.toFixed(1) + '<small>km</small>';
            const statusDot = document.getElementById('statusDot');
            if (statusDot) {
                statusDot.className = (isLive && speed > 0) ? 'status-dot live' : 'status-dot offline';
            }
        }

        // ==================== CALCULATE DISTANCE ====================
        function calculateDistance(lat1, lng1, lat2, lng2) {
            if (!lat1 || !lng1 || !lat2 || !lng2) return 0;
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLng = (lng2 - lng1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) ** 2 + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(
                dLng / 2) ** 2;
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        }

        // ==================== GPS TRACKING ====================
        function startLocationTracking() {
            if (!navigator.geolocation) {
                document.getElementById('connectionStatus').innerHTML =
                    '<span class="live-pulse" style="background:#ef4444;"></span> GPS Not Supported';
                return;
            }

            watchId = navigator.geolocation.watchPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const speed = position.coords.speed || 0;
                    const accuracy = position.coords.accuracy;

                    updateAgentPosition(lat, lng, speed, accuracy);
                    sendLocationToServer(lat, lng, speed, accuracy);
                    document.getElementById('connectionStatus').innerHTML = '<span class="live-pulse"></span> Live';
                    document.getElementById('gpsAccuracy').innerHTML = Math.round(accuracy) +
                        ' <span style="font-size:10px;">m</span>';
                },
                (error) => {
                    console.error('Geolocation error:', error);
                    document.getElementById('connectionStatus').innerHTML =
                        '<span class="live-pulse" style="background:#ef4444;"></span> Lost Signal';
                }, {
                    enableHighAccuracy: true,
                    timeout: 5000,
                    maximumAge: 0
                }
            );

            updateInterval = setInterval(() => {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition((position) => {
                        sendLocationToServer(position.coords.latitude, position.coords.longitude, position
                            .coords.speed || 0, position.coords.accuracy);
                    });
                }
            }, 5000);
        }

        // ==================== SEND LOCATION TO SERVER ====================
        async function sendLocationToServer(lat, lng, speed, accuracy) {
            try {
                await fetch('{{ route('agent.location.update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        latitude: lat,
                        longitude: lng,
                        speed: speed,
                        accuracy: accuracy,
                        shipment_id: SHIPMENT_ID
                    })
                });
            } catch (error) {
                console.error('Failed to send location:', error);
            }
        }

        // ==================== MAP CONTROLS ====================
        function centerOnAgent() {
            if (agentMarker) {
                map.setCenter(agentMarker.getPosition());
                map.setZoom(16);
                agentMarker.setAnimation(google.maps.Animation.BOUNCE);
                setTimeout(() => agentMarker.setAnimation(null), 1000);
            }
        }

        function centerOnCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        map.setCenter({
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        });
                        map.setZoom(15);
                    },
                    (error) => console.error('Geolocation error:', error)
                );
            } else if (agentMarker) {
                centerOnAgent();
            }
        }

        function centerOnDestination() {
            map.setCenter({
                lat: DEST_LAT,
                lng: DEST_LNG
            });
            map.setZoom(15);
            destMarker.setAnimation(google.maps.Animation.BOUNCE);
            setTimeout(() => destMarker.setAnimation(null), 1000);
        }

        function zoomIn() {
            map.setZoom(map.getZoom() + 1);
        }

        function zoomOut() {
            map.setZoom(map.getZoom() - 1);
        }

        function toggleFullscreen() {
            const elem = document.querySelector('.tracking-container');
            if (!document.fullscreenElement) {
                elem.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        }

        // ==================== COMPLETE DELIVERY ====================
        function showCompleteModal() {
            new bootstrap.Modal(document.getElementById('completeModal')).show();
        }

        function completeDelivery() {
            const formData = new FormData();
            const signature = document.getElementById('signature').files[0];
            const photo = document.getElementById('photo').files[0];
            const notes = document.getElementById('notes').value;

            if (!signature) {
                alert('Please capture customer signature');
                return;
            }

            formData.append('signature', signature);
            if (photo) formData.append('photo', photo);
            if (notes) formData.append('notes', notes);

            fetch('{{ route('agent.delivery.complete', $shipment->id) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            }).then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    alert('Delivery completed successfully!');
                    window.location.href = '{{ route('agent.dashboard') }}';
                }
            }).catch(err => {
                console.error('Delivery completion failed:', err);
                alert('Failed to complete delivery. Please try again.');
            });
        }

        // ==================== BATTERY STATUS ====================
        if (navigator.getBattery) {
            navigator.getBattery().then(battery => {
                document.getElementById('batteryValue').innerHTML = `${Math.round(battery.level * 100)}%`;
                battery.addEventListener('levelchange', () => {
                    document.getElementById('batteryValue').innerHTML =
                        `${Math.round(battery.level * 100)}%`;
                });
            });
        }

        // ==================== CLEANUP ====================
        window.addEventListener('beforeunload', () => {
            if (watchId) navigator.geolocation.clearWatch(watchId);
            if (updateInterval) clearInterval(updateInterval);
        });

        // Expose global functions
        window.centerOnAgent = centerOnAgent;
        window.centerOnCurrentLocation = centerOnCurrentLocation;
        window.centerOnDestination = centerOnDestination;
        window.zoomIn = zoomIn;
        window.zoomOut = zoomOut;
        window.toggleFullscreen = toggleFullscreen;
        window.showCompleteModal = showCompleteModal;
        window.completeDelivery = completeDelivery;
    </script>
@endsection
