{{-- resources/views/components/delivery-map.blade.php --}}
<div class="delivery-map-container">
    <style>
        .delivery-map-container {
            width: 100%;
            height: 100%;
            position: relative;
            background: #f0f4f8;
        }

        #deliveryMap {
            width: 100%;
            height: 100%;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .map-controls {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 400;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .control-btn {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            color: #374151;
            font-size: 18px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .control-btn:hover {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
            transform: scale(1.05);
        }

        .control-btn.active {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        .map-legend {
            position: absolute;
            bottom: 20px;
            left: 20px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            z-index: 400;
            font-size: 12px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        .legend-item:last-child {
            margin-bottom: 0;
        }

        .legend-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 10px;
            font-weight: bold;
        }

        .legend-warehouse {
            background: #8b5cf6;
        }

        .legend-active {
            background: #ef4444;
            animation: pulse 1.5s infinite;
        }

        .legend-completed {
            background: #10b981;
        }

        .legend-in-transit {
            background: #f59e0b;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .tracking-info {
            position: absolute;
            top: 15px;
            left: 15px;
            background: white;
            padding: 12px 16px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            z-index: 400;
            max-width: 300px;
        }

        .tracking-info h3 {
            margin: 0 0 8px 0;
            font-size: 14px;
            font-weight: 600;
            color: #1f2937;
        }

        .tracking-info p {
            margin: 4px 0;
            font-size: 12px;
            color: #6b7280;
        }

        .live-indicator {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fee2e2;
            color: #dc2626;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            margin-top: 8px;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            background: #dc2626;
            border-radius: 50%;
            animation: pulse 1s infinite;
        }

        @media (max-width: 768px) {
            .map-controls {
                top: 10px;
                right: 10px;
            }

            .tracking-info {
                top: 10px;
                left: 10px;
                max-width: calc(100% - 20px);
            }

            .map-legend {
                bottom: 10px;
                left: 10px;
            }
        }
    </style>

    <!-- Map Container -->
    <div id="deliveryMap"></div>

    <!-- Tracking Info Panel -->
    <div class="tracking-info" id="trackingInfo" style="display: none;">
        <h3>📍 Live Tracking</h3>
        <p>Agent: <span id="agentName">Loading...</span></p>
        <p>Status: <span id="deliveryStatus">-</span></p>
        <p>Distance: <span id="distance">-</span> km</p>
        <p>Speed: <span id="speed">-</span> km/h</p>
        <div class="live-indicator">
            <div class="live-dot"></div>
            Live Updates
        </div>
    </div>

    <!-- Map Controls -->
    <div class="map-controls">
        <button class="control-btn" id="zoomIn" title="Zoom In">
            <span>+</span>
        </button>
        <button class="control-btn" id="zoomOut" title="Zoom Out">
            <span>−</span>
        </button>
        <button class="control-btn" id="centerMap" title="Center Map">
            <span>📍</span>
        </button>
        <button class="control-btn" id="toggleTracking" title="Toggle Tracking">
            <span>🔴</span>
        </button>
    </div>

    <!-- Map Legend -->
    <div class="map-legend">
        <div class="legend-item">
            <div class="legend-icon legend-warehouse">🏢</div>
            <span>Warehouse</span>
        </div>
        <div class="legend-item">
            <div class="legend-icon legend-active">📍</div>
            <span>Active Agent</span>
        </div>
        <div class="legend-item">
            <div class="legend-icon legend-in-transit">🚚</div>
            <span>In Transit</span>
        </div>
        <div class="legend-item">
            <div class="legend-icon legend-completed">✓</div>
            <span>Completed</span>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
    document.addEventListener('DOMContentLoaded', async function() {
        // Fetch map configuration from secure endpoint
        let mapConfig = {};
        try {
            const response = await fetch('{{ url('/api/config/maps') }}');
            const result = await response.json();
            if (result.success) {
                mapConfig = result.data;
                console.log('Map config loaded with warehouse:', mapConfig.warehouse_latitude, mapConfig
                    .warehouse_longitude);
            }
        } catch (error) {
            console.warn('Could not fetch map config, using defaults', error);
        }

        // Use warehouse as default center
        const defaultLat = mapConfig.warehouse_latitude || 22.524768;
        const defaultLng = mapConfig.warehouse_longitude || 72.955568;

        console.log('Initializing map with center:', defaultLat, defaultLng);

        const map = L.map('deliveryMap').setView([defaultLat, defaultLng], 13);

        // Add OSM Tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(map);

        // Custom Icons
        const agentIcon = L.divIcon({
            className: 'agent-marker',
            html: `<div style="background: #ef4444; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3); animation: pulse 1.5s infinite;">📍</div>`,
            iconSize: [40, 40],
            className: ''
        });

        const warehouseIcon = L.divIcon({
            className: 'warehouse-marker',
            html: `<div style="background: #8b5cf6; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">🏢</div>`,
            iconSize: [40, 40],
            className: ''
        });

        const destinationIcon = L.divIcon({
            className: 'destination-marker',
            html: `<div style="background: #10b981; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">📦</div>`,
            iconSize: [40, 40],
            className: ''
        });

        // Initialize markers and routes
        let agentMarker = null;
        let warehouseMarker = null;
        let destinationMarker = null;
        let routeLine = null;
        let pathLine = null;
        let isTracking = true;

        // Function to add or update agent location
        window.updateAgentLocation = function(lat, lng, agentName, speed, status) {
            if (agentMarker) {
                agentMarker.setLatLng([lat, lng]);
            } else {
                agentMarker = L.marker([lat, lng], {
                        icon: agentIcon
                    })
                    .bindPopup(`<b>${agentName}</b><br/>Status: ${status}<br/>Speed: ${speed} km/h`)
                    .addTo(map);
            }

            // Update tracking info
            document.getElementById('trackingInfo').style.display = 'block';
            document.getElementById('agentName').textContent = agentName;
            document.getElementById('deliveryStatus').textContent = status;
            document.getElementById('speed').textContent = speed;

            // Center map on agent if tracking is active
            if (isTracking) {
                map.setView([lat, lng], 15);
            }
        };

        // Function to add warehouse
        window.addWarehouse = function(lat, lng, name) {
            if (warehouseMarker) {
                map.removeLayer(warehouseMarker);
            }
            warehouseMarker = L.marker([lat, lng], {
                    icon: warehouseIcon
                })
                .bindPopup(`<b>${name}</b><br/>Warehouse`)
                .addTo(map);
        };

        // Function to add destination
        window.addDestination = function(lat, lng, name, address) {
            if (destinationMarker) {
                map.removeLayer(destinationMarker);
            }
            destinationMarker = L.marker([lat, lng], {
                    icon: destinationIcon
                })
                .bindPopup(`<b>${name}</b><br/>${address}`)
                .addTo(map);
        };

        // Function to draw route
        window.drawRoute = function(points) {
            if (routeLine) {
                map.removeLayer(routeLine);
            }
            routeLine = L.polyline(points, {
                color: '#3b82f6',
                weight: 3,
                opacity: 0.7,
                dashArray: '5, 5'
            }).addTo(map);
        };

        // Function to draw path taken
        window.drawPath = function(points) {
            if (pathLine) {
                map.removeLayer(pathLine);
            }
            if (points.length > 0) {
                pathLine = L.polyline(points, {
                    color: '#f59e0b',
                    weight: 2,
                    opacity: 0.6
                }).addTo(map);
            }
        };

        // Control Buttons
        document.getElementById('zoomIn').addEventListener('click', () => map.zoomIn());
        document.getElementById('zoomOut').addEventListener('click', () => map.zoomOut());

        document.getElementById('centerMap').addEventListener('click', () => {
            if (agentMarker) {
                map.setView(agentMarker.getLatLng(), 15);
            } else {
                map.setView([defaultLat, defaultLng], 13);
            }
        });

        document.getElementById('toggleTracking').addEventListener('click', function() {
            isTracking = !isTracking;
            this.classList.toggle('active');
            if (isTracking && agentMarker) {
                map.setView(agentMarker.getLatLng(), 15);
            }
        });

        // Expose map to window for global access
        window.deliveryMap = map;
        window.mapConfig = mapConfig;
        window.warehouseLat = defaultLat;
        window.warehouseLng = defaultLng;
    });
</script>
