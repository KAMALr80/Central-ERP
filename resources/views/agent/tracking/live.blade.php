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
            border-radius: 0;
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
    </style>

    <div class="tracking-container">
        <!-- Map -->
        <div id="map"></div>

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

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

    <script>
        // Configuration
        const DEST_LAT = {{ $shipment->destination_latitude ?? 22.524768 }};
        const DEST_LNG = {{ $shipment->destination_longitude ?? 72.955568 }};
        const SHIPMENT_ID = {{ $shipment->id }};

        // Global variables
        let map, myMarker, destMarker, routeLayer;
        let watchId;
        let currentPosition = null;
        let updateInterval;
        let lastUpdateTime = null;

        // Initialize Map
        function initMap() {
            map = L.map('map').setView([DEST_LAT, DEST_LNG], 13);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            // Destination marker with custom icon
            const destIcon = L.divIcon({
                html: '<div style="background:#ef4444; width:44px; height:44px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-size:22px; border:3px solid white; box-shadow:0 2px 10px rgba(0,0,0,0.2);"><i class="fas fa-flag-checkered"></i></div>',
                iconSize: [44, 44],
                className: 'dest-marker'
            });

            destMarker = L.marker([DEST_LAT, DEST_LNG], {
                    icon: destIcon
                })
                .addTo(map)
                .bindPopup(`
            <div class="text-center">
                <strong>📍 Delivery Location</strong><br>
                {{ $shipment->receiver_name }}<br>
                {{ $shipment->shipping_address }}
            </div>
        `)
                .openPopup();

            startLocationTracking();
        }

        // Start GPS Tracking
        function startLocationTracking() {
            if (!navigator.geolocation) {
                document.getElementById('connectionStatus').innerHTML =
                    '<span class="live-pulse" style="background:#ef4444;"></span> GPS Not Supported';
                return;
            }

            // Watch position with high accuracy
            watchId = navigator.geolocation.watchPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const speed = position.coords.speed || 0;
                    const accuracy = position.coords.accuracy;
                    const heading = position.coords.heading || 0;

                    currentPosition = {
                        lat,
                        lng,
                        speed,
                        accuracy,
                        heading
                    };

                    // Update UI
                    updateMyLocation(lat, lng);
                    updateMetrics(lat, lng, speed);
                    updateProgress(lat, lng);
                    sendLocationToServer(lat, lng, speed, accuracy, heading);
                    calculateRouteAndETA(lat, lng);

                    // Update connection status
                    document.getElementById('connectionStatus').innerHTML = '<span class="live-pulse"></span> Live';
                    lastUpdateTime = Date.now();
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

            // Backup location update every 3 seconds
            updateInterval = setInterval(() => {
                if (navigator.geolocation && (!lastUpdateTime || Date.now() - lastUpdateTime > 3000)) {
                    navigator.geolocation.getCurrentPosition((position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        const speed = position.coords.speed || 0;
                        sendLocationToServer(lat, lng, speed, position.coords.accuracy, position.coords
                            .heading || 0);
                    });
                }
            }, 3000);
        }

        // Update Agent Marker on Map
        function updateMyLocation(lat, lng) {
            const myIcon = L.divIcon({
                html: `<div style="background:#3b82f6; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-size:20px; border:3px solid white; box-shadow:0 2px 10px rgba(0,0,0,0.2); animation: pulse 1.5s infinite;">
                    <i class="fas fa-motorcycle"></i>
                </div>`,
                iconSize: [40, 40],
                className: 'agent-marker'
            });

            if (myMarker) {
                animateMarker(myMarker, [lat, lng], 1000); // Animate over 1 second
            } else {
                myMarker = L.marker([lat, lng], {
                    icon: myIcon
                }).addTo(map);
                myMarker.bindPopup('<b>You are here</b>').openPopup();
            }
        }

        // Function to animate marker movement smoothly
        function animateMarker(marker, newLatLng, duration) {
            const startLatLng = marker.getLatLng();
            const startTime = performance.now();

            function step(currentTime) {
                const elapsed = currentTime - startTime;
                let progress = elapsed / duration;

                if (progress > 1) progress = 1;

                // Easing function (ease-out cubic)
                const easeProgress = 1 - Math.pow(1 - progress, 3);

                const currentLat = startLatLng.lat + (newLatLng[0] - startLatLng.lat) * easeProgress;
                const currentLng = startLatLng.lng + (newLatLng[1] - startLatLng.lng) * easeProgress;

                marker.setLatLng([currentLat, currentLng]);

                if (progress < 1) {
                    requestAnimationFrame(step);
                }
            }

            requestAnimationFrame(step);
        }

        // Update Speed and Distance Metrics
        function updateMetrics(lat, lng, speed) {
            // Speed in km/h
            const speedKmh = (speed * 3.6).toFixed(1);
            document.getElementById('speedValue').innerHTML = speedKmh;

            // Calculate distance to destination
            const distance = calculateDistance(lat, lng, DEST_LAT, DEST_LNG);
            document.getElementById('distanceValue').innerHTML = distance.toFixed(1);

            // Calculate ETA
            if (speed > 0) {
                const etaMinutes = (distance / speedKmh) * 60;
                if (etaMinutes < 60) {
                    document.getElementById('etaValue').innerHTML = `${Math.round(etaMinutes)} min`;
                } else {
                    const hours = Math.floor(etaMinutes / 60);
                    const minutes = Math.round(etaMinutes % 60);
                    document.getElementById('etaValue').innerHTML = `${hours}h ${minutes}m`;
                }
            } else {
                document.getElementById('etaValue').innerHTML = 'Calculating...';
            }

            // Simulate battery (can be replaced with actual battery API)
            if (navigator.getBattery) {
                navigator.getBattery().then(battery => {
                    document.getElementById('batteryValue').innerHTML = `${Math.round(battery.level * 100)}%`;
                });
            }
        }

        // Update Progress Percentage
        function updateProgress(lat, lng) {
            const totalDistance = calculateDistance(DEST_LAT, DEST_LNG, 22.524768, 72.955568); // Warehouse coordinates
            const remainingDistance = calculateDistance(lat, lng, DEST_LAT, DEST_LNG);
            const progress = Math.max(0, Math.min(100, ((totalDistance - remainingDistance) / totalDistance) * 100));

            document.getElementById('progressPercent').innerHTML = `${Math.round(progress)}%`;
            document.getElementById('progressFill').style.width = `${progress}%`;
        }

        // Calculate Distance using Haversine Formula
        function calculateDistance(lat1, lng1, lat2, lng2) {
            const R = 6371; // Earth's radius in km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLng = (lng2 - lng1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLng / 2) * Math.sin(dLng / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        // Send Location to Server
        function sendLocationToServer(lat, lng, speed, accuracy, heading) {
            // Get battery level if available
            let batteryLevel = null;
            if (navigator.getBattery) {
                navigator.getBattery().then(battery => {
                    batteryLevel = Math.round(battery.level * 100);
                });
            }

            fetch('{{ url('/api/agent-location/update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Authorization': 'Bearer ' + (localStorage.getItem('api_token') || '')
                },
                body: JSON.stringify({
                    latitude: lat,
                    longitude: lng,
                    speed: speed || 0,
                    accuracy: accuracy || null,
                    heading: heading || 0,
                    battery_level: batteryLevel || 0,
                    shipment_id: SHIPMENT_ID
                })
            }).catch(err => console.error('Failed to send location:', err));
        }

        // Calculate Route and Update ETA
        function calculateRouteAndETA(originLat, originLng) {
            const url =
                `https://maps.googleapis.com/maps/api/directions/json?origin=${originLat},${originLng}&destination=${DEST_LAT},${DEST_LNG}&key={{ env('GOOGLE_MAPS_API_KEY') }}`;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (data.routes && data.routes[0]) {
                        const route = data.routes[0];
                        const distance = route.legs[0].distance.text;
                        const duration = route.legs[0].duration.text;

                        // Update route line
                        if (routeLayer) routeLayer.remove();
                        const points = decodePolyline(route.overview_polyline.points);
                        routeLayer = L.polyline(points, {
                            color: '#3b82f6',
                            weight: 5,
                            opacity: 0.7
                        }).addTo(map);

                        // Update ETA display
                        document.getElementById('etaValue').innerHTML = duration;
                    }
                })
                .catch(err => console.error('Route calculation failed:', err));
        }

        // Decode Google Maps Polyline
        function decodePolyline(encoded) {
            let points = [];
            let index = 0,
                len = encoded.length;
            let lat = 0,
                lng = 0;

            while (index < len) {
                let b, shift = 0,
                    result = 0;
                do {
                    b = encoded.charCodeAt(index++) - 63;
                    result |= (b & 0x1f) << shift;
                    shift += 5;
                } while (b >= 0x20);
                let dlat = ((result & 1) ? ~(result >> 1) : (result >> 1));
                lat += dlat;

                shift = 0;
                result = 0;
                do {
                    b = encoded.charCodeAt(index++) - 63;
                    result |= (b & 0x1f) << shift;
                    shift += 5;
                } while (b >= 0x20);
                let dlng = ((result & 1) ? ~(result >> 1) : (result >> 1));
                lng += dlng;

                points.push([lat * 1e-5, lng * 1e-5]);
            }
            return points;
        }

        // Center Map on Current Location
        function centerOnCurrentLocation() {
            if (myMarker) {
                map.setView(myMarker.getLatLng(), 15);
                myMarker.openPopup();
            } else if (currentPosition) {
                map.setView([currentPosition.lat, currentPosition.lng], 15);
            }
        }

        // Center Map on Destination
        function centerOnDestination() {
            map.setView([DEST_LAT, DEST_LNG], 15);
            destMarker.openPopup();
        }

        // Show Completion Modal
        function showCompleteModal() {
            new bootstrap.Modal(document.getElementById('completeModal')).show();
        }

        // Complete Delivery
        function completeDelivery() {
            const formData = new FormData();
            const signature = document.getElementById('signature').files[0];
            const photo = document.getElementById('photo').files[0];
            const notes = document.getElementById('notes').value;

            if (!signature) {
                alert('Please capture customer signature');
                return;
            }

            if (signature) formData.append('signature', signature);
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

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            if (watchId) navigator.geolocation.clearWatch(watchId);
            if (updateInterval) clearInterval(updateInterval);
        });

        document.addEventListener('DOMContentLoaded', initMap);
    </script>
@endsection
