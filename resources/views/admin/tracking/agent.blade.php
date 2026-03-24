{{-- resources/views/admin/tracking/agent.blade.php --}}
@extends('layouts.app')

@section('title', 'Track Agent - ' . $agent->name)

@section('content')
    <style>
        .agent-tracking-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .map-wrapper {
            flex: 1;
            position: relative;
            overflow: hidden;
        }

        .agent-info-panel {
            background: white;
            border-radius: 24px 24px 0 0;
            padding: 20px;
            margin-top: -20px;
            position: relative;
            z-index: 10;
            box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.1);
            max-height: 35vh;
            overflow-y: auto;
        }

        .agent-header {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: white;
            border-radius: 16px;
            padding: 20px;
            display: grid;
            grid-template-columns: 70px 1fr auto;
            gap: 20px;
            align-items: center;
            margin-bottom: 20px;
        }

        .agent-avatar {
            width: 70px;
            height: 70px;
            background: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e293b;
            font-size: 32px;
        }

        .agent-details h4 {
            margin: 0 0 4px 0;
            font-size: 18px;
            font-weight: 700;
        }

        .agent-details p {
            margin: 4px 0;
            font-size: 13px;
            opacity: 0.9;
        }

        .agent-status-badge {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 8px;
        }

        .status-indicator {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .live-dot {
            width: 10px;
            height: 10px;
            background: #10b981;
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

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        .metric-card {
            background: linear-gradient(135deg, #f8fafc, #ffffff);
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px;
            text-align: center;
            transition: all 0.3s;
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-color: #3b82f6;
        }

        .metric-value {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .metric-label {
            font-size: 12px;
            color: #6b7280;
        }

        .shipment-info {
            background: linear-gradient(135deg, #dbeafe, #f0f9ff);
            border-left: 4px solid #3b82f6;
            border-radius: 8px;
            padding: 12px;
            margin-top: 20px;
        }

        .shipment-info h6 {
            margin: 0 0 8px 0;
            color: #0c4a6e;
            font-weight: 700;
            font-size: 13px;
        }

        .shipment-info p {
            margin: 4px 0;
            color: #0c4a6e;
            font-size: 12px;
        }

        .route-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .route-point {
            display: flex;
            gap: 10px;
        }

        .route-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .route-warehouse {
            background: #f3e8ff;
        }

        .route-destination {
            background: #dcfce7;
        }

        .route-text h6 {
            margin: 0 0 2px 0;
            font-size: 12px;
            font-weight: 600;
            color: #1e293b;
        }

        .route-text p {
            margin: 0;
            font-size: 11px;
            color: #6b7280;
        }
    </style>

    <div class="agent-tracking-container">
        <div class="map-wrapper">
            @component('components.delivery-map')
            @endcomponent
        </div>

        <div class="agent-info-panel">
            <div class="agent-header">
                <div class="agent-avatar">🚙</div>
                <div class="agent-details">
                    <h4>{{ $agent->name }}</h4>
                    <p>{{ $agent->agent_code }} • {{ $agent->phone ?? 'N/A' }}</p>
                    <p>{{ $agent->vehicle_type ?? 'Bike/Bike' }} • {{ $agent->vehicle_number ?? 'N/A' }}</p>
                </div>
                <div class="agent-status-badge">
                    <div class="status-indicator" id="statusIndicator">
                        <div class="live-dot"></div>
                        <span>Online</span>
                    </div>
                    <div style="font-size: 11px; color: rgba(255,255,255,0.7);" id="lastUpdateTime">
                        Updating...
                    </div>
                </div>
            </div>

            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-value" id="speedValue">--</div>
                    <div class="metric-label">Speed (km/h)</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value" id="distanceValue">--</div>
                    <div class="metric-label">Distance (km)</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value" id="etaValue">--</div>
                    <div class="metric-label">ETA (min)</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value" id="batteryValue">--</div>
                    <div class="metric-label">Battery %</div>
                </div>
            </div>

            @if ($activeShipment)
                <div class="shipment-info">
                    <h6>📦 Current Delivery</h6>
                    <p><strong>#{{ $activeShipment->shipment_number }}</strong></p>
                    <p>Receiver: {{ $activeShipment->receiver_name }}</p>
                    <p>Status: <span
                            style="background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 4px;">{{ ucfirst(str_replace('_', ' ', $activeShipment->status)) }}</span>
                    </p>
                </div>

                <div class="route-info">
                    <div class="route-point">
                        <div class="route-icon route-warehouse">🏢</div>
                        <div class="route-text">
                            <h6>Warehouse</h6>
                            <p>Main Hub</p>
                        </div>
                    </div>
                    <div class="route-point">
                        <div class="route-icon route-destination">📍</div>
                        <div class="route-text">
                            <h6>Destination</h6>
                            <p>{{ $activeShipment->receiver_name }}</p>
                        </div>
                    </div>
                </div>
            @else
                <div style="background: #f3f4f6; padding: 15px; border-radius: 8px; margin-top: 20px;">
                    <p style="margin: 0; color: #6b7280; font-size: 13px; text-align: center;">
                        No active shipment assigned
                    </p>
                </div>
            @endif
        </div>
    </div>

    <script>
        const AGENT_ID = {{ $agent->user_id }};
        const AGENT_NAME = "{{ $agent->name }}";
        const DEST_LAT = {{ $activeShipment?->destination_latitude ?? 22.524768 }};
        const DEST_LNG = {{ $activeShipment?->destination_longitude ?? 72.955568 }};
        const WAREHOUSE_LAT = {{ env('WAREHOUSE_LAT', 22.524768) }};
        const WAREHOUSE_LNG = {{ env('WAREHOUSE_LNG', 72.955568) }};

        let updateInterval;
        let locationHistory = [];
        let mapConfig = {};

        // Fetch map configuration with API key and defaults
        async function loadMapConfig() {
            try {
                const response = await fetch('{{ url('/api/config/maps') }}');
                const result = await response.json();
                if (result.success) {
                    mapConfig = result.data;
                    console.log('Map config loaded - Warehouse:', {
                        lat: mapConfig.warehouse_latitude,
                        lng: mapConfig.warehouse_longitude
                    });
                }
            } catch (error) {
                console.warn('Could not load map config:', error);
            }
        }

        function calculateDistance(lat1, lng1, lat2, lng2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLng = (lng2 - lng1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLng / 2) * Math.sin(dLng / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        function updateAgentTracking(data) {
            if (!data.latitude || !data.longitude) {
                console.warn('Invalid location data:', data);
                return;
            }

            const lat = parseFloat(data.latitude);
            const lng = parseFloat(data.longitude);

            console.log('Updating agent location:', {
                lat,
                lng,
                agent: AGENT_NAME
            });

            // Update map with agent location
            if (window.deliveryMap) {
                window.updateAgentLocation(lat, lng, AGENT_NAME, data.speed || 0, data.status || 'in_transit');

                // Add warehouse marker
                window.addWarehouse(WAREHOUSE_LAT, WAREHOUSE_LNG, 'Main Warehouse');

                // Add destination marker if available
                if (DEST_LAT && DEST_LNG) {
                    window.addDestination(DEST_LAT, DEST_LNG, 'Delivery Point',
                        '{{ $activeShipment?->receiver_name ?? 'Delivery Location' }}');
                }
            }

            // Update metrics
            document.getElementById('speedValue').textContent = (data.speed || 0).toFixed(1);

            const distance = calculateDistance(lat, lng, DEST_LAT, DEST_LNG);
            document.getElementById('distanceValue').textContent = distance.toFixed(1);

            if (data.speed && data.speed > 0) {
                const eta = (distance / (data.speed || 1)) * 60;
                document.getElementById('etaValue').textContent = Math.round(eta);
            } else {
                document.getElementById('etaValue').textContent = '--';
            }

            document.getElementById('batteryValue').textContent = (data.battery_level || 0) + '%';

            // Update last update time
            const now = new Date();
            document.getElementById('lastUpdateTime').textContent = 'Updated ' + now.toLocaleTimeString();

            // Add to location history for path visualization
            locationHistory.push([lat, lng]);
            if (locationHistory.length > 1) {
                window.drawPath(locationHistory);
            }

            // Update status indicator
            if (data.is_online) {
                document.getElementById('statusIndicator').innerHTML = '<div class="live-dot"></div><span>Online</span>';
            } else {
                document.getElementById('statusIndicator').innerHTML =
                    '<div class="live-dot" style="background:#ef4444;"></div><span>Offline</span>';
            }
        }

        function fetchLocation() {
            const url = `{{ url('/api/admin') }}/agent/{{ $agent->id }}/location`;
            console.log('Fetching location from:', url);

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    console.log('Location response:', data);
                    if (data.latitude) {
                        updateAgentTracking(data);
                    }
                })
                .catch(err => {
                    console.error('Location fetch failed:', err);
                    document.getElementById('statusIndicator').innerHTML =
                        '<div class="live-dot" style="background:#ef4444;"></div><span>Connection Lost</span>';
                });
        }

        document.addEventListener('DOMContentLoaded', async function() {
            console.log('Initializing agent tracking for:', AGENT_NAME);

            // Load map config first
            await loadMapConfig();

            // Initial fetch
            fetchLocation();

            // Set up interval for live updates every 3 seconds
            updateInterval = setInterval(fetchLocation, 3000);

            window.addEventListener('beforeunload', () => {
                if (updateInterval) clearInterval(updateInterval);
            });
        });
    </script>
@endsection
