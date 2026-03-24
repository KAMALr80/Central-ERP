{{-- resources/views/admin/tracking/agents.blade.php --}}
@extends('layouts.app')

@section('title', 'Live Agent Tracking')

@section('content')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .tracking-container {
            padding: 20px;
            background: #f8fafc;
            min-height: 100vh;
        }

        /* Stats Bar */
        .stats-bar {
            background: white;
            border-radius: 20px;
            padding: 20px 25px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .stats-item {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .stats-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
        }

        .stats-info h3 {
            font-size: 28px;
            font-weight: 800;
            margin: 0;
            color: #1e293b;
        }

        .stats-info p {
            font-size: 12px;
            margin: 0;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .refresh-btn {
            background: #f1f5f9;
            border: none;
            padding: 10px 20px;
            border-radius: 40px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .refresh-btn:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
        }

        /* Map Card */
        .map-card {
            background: white;
            border-radius: 24px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
        }

        .map-card:hover {
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
        }

        .map-header {
            padding: 20px 25px;
            background: linear-gradient(135deg, #f8fafc, #ffffff);
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .map-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .map-title i {
            font-size: 24px;
            color: #667eea;
        }

        .map-title h5 {
            margin: 0;
            font-weight: 700;
            font-size: 18px;
            color: #1e293b;
        }

        .map-controls {
            display: flex;
            gap: 10px;
        }

        .map-control-btn {
            background: white;
            border: 1px solid #e5e7eb;
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .map-control-btn:hover {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-color: transparent;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        #map {
            height: 65vh;
            width: 100%;
            background: #e9ecef;
        }

        /* Agents Sidebar */
        .agents-sidebar {
            background: white;
            border-radius: 24px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            height: calc(65vh + 80px);
            display: flex;
            flex-direction: column;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .sidebar-header {
            padding: 18px 20px;
            background: linear-gradient(135deg, #f8fafc, #ffffff);
            border-bottom: 1px solid #e5e7eb;
            font-weight: 700;
            font-size: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sidebar-header i {
            color: #667eea;
        }

        .agents-list {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
        }

        .agent-item {
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid #e5e7eb;
            background: white;
        }

        .agent-item:hover {
            transform: translateX(5px);
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
        }

        .agent-item.active {
            background: linear-gradient(135deg, #667eea10, #764ba210);
            border-left: 4px solid #667eea;
            border-color: #667eea;
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
            font-size: 20px;
            flex-shrink: 0;
        }

        .agent-info {
            flex: 1;
        }

        .agent-name {
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 4px;
            color: #1e293b;
        }

        .agent-status {
            font-size: 11px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-online {
            background: #10b981;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
            animation: pulse 1.5s infinite;
        }

        .status-offline {
            background: #ef4444;
        }

        .agent-speed {
            font-size: 11px;
            color: #3b82f6;
            font-weight: 600;
        }

        .agent-delivery {
            font-size: 10px;
            color: #64748b;
            margin-top: 4px;
        }

        .live-pulse {
            width: 10px;
            height: 10px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 1.5s infinite;
            display: inline-block;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
                transform: scale(1);
            }

            100% {
                opacity: 0;
                transform: scale(2.5);
            }
        }

        /* Custom Scrollbar */
        .agents-list::-webkit-scrollbar {
            width: 6px;
        }

        .agents-list::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .agents-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .agents-list::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        @media (max-width: 992px) {
            .stats-bar {
                flex-direction: column;
                align-items: flex-start;
            }

            .agents-sidebar {
                height: auto;
                max-height: 400px;
            }
        }

        @media (max-width: 768px) {
            .tracking-container {
                padding: 12px;
            }

            .stats-icon {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }

            .stats-info h3 {
                font-size: 22px;
            }

            .map-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .map-controls {
                width: 100%;
            }

            .map-control-btn {
                flex: 1;
                justify-content: center;
            }
        }
    </style>

    <div class="tracking-container">
        <div class="container-fluid px-0">
            <!-- Stats Bar -->
            <div class="stats-bar">
                <div class="stats-item">
                    <div class="stats-icon"><i class="fas fa-users"></i></div>
                    <div class="stats-info">
                        <h3 id="totalAgents">0</h3>
                        <p>Active Agents</p>
                    </div>
                </div>
                <div class="stats-item">
                    <div class="stats-icon"><i class="fas fa-truck"></i></div>
                    <div class="stats-info">
                        <h3 id="activeDeliveries">0</h3>
                        <p>Active Deliveries</p>
                    </div>
                </div>
                <div class="stats-item">
                    <div class="stats-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="stats-info">
                        <h3 id="avgSpeed">0</h3>
                        <p>Avg Speed (km/h)</p>
                    </div>
                </div>
                <div class="stats-item">
                    <button class="refresh-btn" onclick="refreshData()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <span id="lastUpdate" class="text-muted small ms-2"></span>
                </div>
            </div>

            <div class="row g-4">
                <!-- Map Column -->
                <div class="col-lg-8">
                    <div class="map-card">
                        <div class="map-header">
                            <div class="map-title">
                                <i class="fas fa-map-marked-alt"></i>
                                <h5>Live Agent Tracking Map</h5>
                            </div>
                            <div class="map-controls">
                                <button class="map-control-btn" onclick="fitAllMarkers()">
                                    <i class="fas fa-globe"></i> Fit All
                                </button>
                                <button class="map-control-btn" onclick="centerOnCurrentLocation()">
                                    <i class="fas fa-location-dot"></i> My Location
                                </button>
                                <button class="map-control-btn" onclick="toggleTraffic()">
                                    <i class="fas fa-car"></i> Traffic
                                </button>
                            </div>
                        </div>
                        <div id="map"></div>
                    </div>
                </div>

                <!-- Agents Sidebar -->
                <div class="col-lg-4">
                    <div class="agents-sidebar">
                        <div class="sidebar-header">
                            <span><i class="fas fa-motorcycle me-2"></i> Active Agents</span>
                            <span id="agentCount" class="badge bg-primary">0</span>
                        </div>
                        <div class="agents-list" id="agentsList">
                            <div class="text-center py-5">
                                <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3 d-block"></i>
                                <p class="text-muted">Loading agents...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

    <script>
        // ==================== GLOBAL VARIABLES ====================
        let map;
        let markers = new Map();
        let agentsData = [];
        let updateInterval;
        let selectedAgentId = null;
        let trafficLayer = null;
        let isTrafficEnabled = false;

        // Default center (India)
        const DEFAULT_CENTER = [22.524768, 72.955568];

        // ==================== INITIALIZE MAP ====================
        function initMap() {
            map = L.map('map').setView(DEFAULT_CENTER, 6);

            // Add beautiful tile layer with satellite style
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 19,
                minZoom: 3
            }).addTo(map);

            // Add scale control
            L.control.scale({
                metric: true,
                imperial: false,
                position: 'bottomleft'
            }).addTo(map);

            // Add zoom control
            map.zoomControl.setPosition('topright');

            // Load agents
            loadAgents();
            startAutoUpdate();
        }

        // ==================== TRAFFIC LAYER ====================
        function toggleTraffic() {
            if (isTrafficEnabled) {
                if (trafficLayer) {
                    map.removeLayer(trafficLayer);
                    trafficLayer = null;
                }
                isTrafficEnabled = false;
                showToast('Traffic layer disabled', 'info');
            } else {
                trafficLayer = L.tileLayer(
                    'https://{s}.tile.thunderforest.com/transport/{z}/{x}/{y}.png?apikey=YOUR_API_KEY', {
                        attribution: '&copy; <a href="https://www.thunderforest.com/">Thunderforest</a>',
                        maxZoom: 19
                    }).addTo(map);
                isTrafficEnabled = true;
                showToast('Traffic layer enabled', 'success');
            }
        }

        // ==================== LOAD AGENTS DATA ====================
        function loadAgents() {
            fetch('/api/admin/agents/locations')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        agentsData = data.agents;
                        updateStats(agentsData);
                        updateAgentsList(agentsData);
                        updateMarkers(agentsData);
                        updateLastUpdateTime();
                    }
                })
                .catch(err => console.error('Error loading agents:', err));
        }

        // ==================== UPDATE STATISTICS ====================
        function updateStats(agents) {
            const activeAgents = agents.filter(a => a.status !== 'offline');
            const activeDeliveries = agents.filter(a => a.current_shipment).length;
            const avgSpeed = agents.length > 0 ?
                (agents.reduce((sum, a) => sum + (a.speed || 0), 0) / agents.length).toFixed(1) : 0;

            document.getElementById('totalAgents').innerText = activeAgents.length;
            document.getElementById('activeDeliveries').innerText = activeDeliveries;
            document.getElementById('avgSpeed').innerText = avgSpeed;
            document.getElementById('agentCount').innerText = agents.length;
        }

        // ==================== UPDATE AGENTS LIST SIDEBAR ====================
        function updateAgentsList(agents) {
            const container = document.getElementById('agentsList');

            if (!agents.length) {
                container.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-user-slash fa-3x text-muted mb-3 d-block"></i>
                    <p class="text-muted">No active agents found</p>
                </div>
            `;
                return;
            }

            container.innerHTML = agents.map(agent => `
            <div class="agent-item ${selectedAgentId === agent.user_id ? 'active' : ''}"
                 onclick="focusOnAgent(${agent.user_id}, ${agent.latitude}, ${agent.longitude})">
                <div class="agent-avatar">
                    ${agent.name.charAt(0).toUpperCase()}
                </div>
                <div class="agent-info">
                    <div class="agent-name">${escapeHtml(agent.name)}</div>
                    <div class="agent-status">
                        <span class="status-dot status-${agent.status === 'available' ? 'online' : 'offline'}"></span>
                        <span>${agent.status === 'available' ? 'Available' : 'Busy'}</span>
                        <span class="agent-speed">
                            <i class="fas fa-tachometer-alt"></i> ${agent.speed || 0} km/h
                        </span>
                    </div>
                    ${agent.current_shipment ? `
                            <div class="agent-delivery">
                                <i class="fas fa-box"></i> <strong>#${escapeHtml(agent.current_shipment.shipment_number)}</strong>
                                <br><small>${escapeHtml(agent.current_shipment.receiver_name)}</small>
                            </div>
                        ` : `
                            <div class="agent-delivery text-muted">
                                <i class="fas fa-box-open"></i> No active delivery
                            </div>
                        `}
                </div>
                <div>
                    ${agent.status === 'available' ? '<span class="live-pulse"></span>' : ''}
                </div>
            </div>
        `).join('');
        }

        // ==================== UPDATE MAP MARKERS ====================
        function updateMarkers(agents) {
            // Remove markers for agents no longer present
            const currentAgentIds = new Set(agents.map(a => a.user_id));
            markers.forEach((marker, agentId) => {
                if (!currentAgentIds.has(agentId)) {
                    map.removeLayer(marker);
                    markers.delete(agentId);
                }
            });

            // Add/Update markers
            agents.forEach(agent => {
                if (agent.latitude && agent.longitude && agent.latitude !== 0 && agent.longitude !== 0) {
                    const isOnline = agent.status === 'available';
                    const hasDelivery = !!agent.current_shipment;

                    // Create custom marker with animation
                    const customIcon = L.divIcon({
                        html: `
                        <div class="custom-marker" style="position: relative;">
                            <div style="width: 40px; height: 40px; background: ${hasDelivery ? '#ef4444' : '#10b981'}; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                                <i class="fas fa-motorcycle" style="color: white; font-size: 18px;"></i>
                            </div>
                            ${isOnline ? '<div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border-radius: 50%; background: rgba(16,185,129,0.4); animation: pulse 1.5s infinite;"></div>' : ''}
                        </div>
                    `,
                        className: 'custom-marker',
                        iconSize: [40, 40],
                        iconAnchor: [20, 20]
                    });

                    const popupContent = `
                    <div style="min-width: 260px;">
                        <div class="text-center mb-3">
                            <div style="background: linear-gradient(135deg, #667eea, #764ba2); width: 60px; height: 60px; border-radius: 16px; display: inline-flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: bold; margin-bottom: 10px;">
                                ${agent.name.charAt(0).toUpperCase()}
                            </div>
                            <h6 class="mb-1 fw-bold">${escapeHtml(agent.name)}</h6>
                            <span class="badge ${agent.status === 'available' ? 'bg-success' : 'bg-secondary'}">${agent.status === 'available' ? 'Available' : 'Busy'}</span>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-2">
                                <span><i class="fas fa-code-branch"></i> Agent Code:</span>
                                <span class="fw-bold">${agent.agent_code}</span>
                            </div>
                            <div class="d-flex justify-content-between small mb-2">
                                <span><i class="fas fa-phone"></i> Phone:</span>
                                <span>${agent.phone || 'N/A'}</span>
                            </div>
                            <div class="d-flex justify-content-between small mb-2">
                                <span><i class="fas fa-tachometer-alt"></i> Speed:</span>
                                <span class="text-primary fw-bold">${agent.speed || 0} km/h</span>
                            </div>
                            <div class="d-flex justify-content-between small mb-2">
                                <span><i class="fas fa-clock"></i> Last Update:</span>
                                <span>${agent.last_update || 'Just now'}</span>
                            </div>
                        </div>

                        ${agent.current_shipment ? `
                                <div class="border-top pt-2 mt-2">
                                    <div class="small fw-bold mb-2"><i class="fas fa-box"></i> Current Delivery</div>
                                    <div class="small mb-1"><strong>Shipment:</strong> #${escapeHtml(agent.current_shipment.shipment_number)}</div>
                                    <div class="small mb-1"><strong>Customer:</strong> ${escapeHtml(agent.current_shipment.receiver_name)}</div>
                                    <div class="small mb-2"><strong>Address:</strong> ${escapeHtml(agent.current_shipment.shipping_address || 'N/A')}</div>
                                    <a href="/logistics/shipments/${agent.current_shipment.id}" class="btn btn-sm btn-primary w-100" target="_blank">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                </div>
                            ` : `
                                <div class="border-top pt-2 mt-2 text-center text-muted">
                                    <i class="fas fa-box-open"></i> No active delivery
                                </div>
                            `}

                        <hr class="my-2">
                        <div class="text-center">
                            <a href="/admin/tracking/agent/${agent.user_id}" class="btn btn-sm btn-outline-primary w-100">
                                <i class="fas fa-map-marker-alt"></i> Track Live
                            </a>
                        </div>
                    </div>
                `;

                    if (markers.has(agent.user_id)) {
                        // Update existing marker with animation
                        const marker = markers.get(agent.user_id);
                        animateMarker(marker, [agent.latitude, agent.longitude], 1000);
                        marker.setPopupContent(popupContent);
                    } else {
                        // Create new marker
                        const marker = L.marker([agent.latitude, agent.longitude], {
                                icon: customIcon
                            })
                            .addTo(map)
                            .bindPopup(popupContent, {
                                maxWidth: 320,
                                minWidth: 280
                            });

                        markers.set(agent.user_id, marker);
                    }
                }
            });
        }

        // ==================== ANIMATE MARKER ====================
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

        // ==================== FOCUS ON SPECIFIC AGENT ====================
        function focusOnAgent(agentId, lat, lng) {
            selectedAgentId = agentId;
            map.setView([lat, lng], 15);

            // Update active state in sidebar
            document.querySelectorAll('.agent-item').forEach(item => {
                item.classList.remove('active');
            });

            // Find and highlight the clicked agent
            const agentItems = document.querySelectorAll('.agent-item');
            for (let item of agentItems) {
                if (item.getAttribute('onclick')?.includes(`focusOnAgent(${agentId}`)) {
                    item.classList.add('active');
                    break;
                }
            }

            // Open popup for this agent
            const marker = markers.get(agentId);
            if (marker) {
                marker.openPopup();
            }

            showToast(`Tracking agent: ${agentsData.find(a => a.user_id === agentId)?.name || 'Agent'}`, 'info');
        }

        // ==================== FIT ALL MARKERS ====================
        function fitAllMarkers() {
            const bounds = [];
            markers.forEach(marker => {
                const latlng = marker.getLatLng();
                bounds.push([latlng.lat, latlng.lng]);
            });

            if (bounds.length > 0) {
                map.fitBounds(bounds);
                showToast(`Showing ${bounds.length} agents on map`, 'success');
            } else {
                map.setView(DEFAULT_CENTER, 6);
                showToast('No agents to display', 'info');
            }
        }

        // ==================== CENTER ON USER LOCATION ====================
        function centerOnCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        map.setView([lat, lng], 13);

                        // Add temporary marker for user location
                        L.marker([lat, lng])
                            .addTo(map)
                            .bindPopup('<b>You are here</b>')
                            .openPopup()
                            .bindTooltip('Your Location', {
                                permanent: false,
                                direction: 'top'
                            });

                        showToast('Centered on your location', 'success');
                    },
                    (error) => {
                        console.error('Geolocation error:', error);
                        showToast('Unable to get your location. Please check permissions.', 'error');
                    }
                );
            } else {
                showToast('Geolocation not supported by this browser.', 'error');
            }
        }

        // ==================== REFRESH DATA ====================
        function refreshData() {
            loadAgents();
            const refreshIcon = document.querySelector('.refresh-btn i');
            refreshIcon.classList.add('fa-spin');
            setTimeout(() => {
                refreshIcon.classList.remove('fa-spin');
            }, 1000);
            showToast('Refreshing agent data...', 'info');
        }

        // ==================== UPDATE LAST UPDATE TIME ====================
        function updateLastUpdateTime() {
            const now = new Date();
            document.getElementById('lastUpdate').innerText = `Last updated: ${now.toLocaleTimeString()}`;
        }

        // ==================== START AUTO UPDATE ====================
        function startAutoUpdate() {
            updateInterval = setInterval(() => {
                loadAgents();
            }, 10000); // Update every 10 seconds
        }

        // ==================== SHOW TOAST NOTIFICATION ====================
        function showToast(message, type = 'info') {
            // Create toast element if it doesn't exist
            let toast = document.getElementById('dynamicToast');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'dynamicToast';
                toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 12px 20px;
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 10000;
                display: none;
                align-items: center;
                gap: 10px;
                animation: slideIn 0.3s ease;
                border-left: 4px solid;
            `;
                document.body.appendChild(toast);
            }

            const colors = {
                success: '#10b981',
                error: '#ef4444',
                info: '#3b82f6',
                warning: '#f59e0b'
            };

            toast.style.borderLeftColor = colors[type] || colors.info;
            toast.innerHTML =
                `<i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}" style="color: ${colors[type]}"></i><span>${message}</span>`;
            toast.style.display = 'flex';

            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);
        }

        // ==================== ESCAPE HTML ====================
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // ==================== CLEANUP ====================
        window.addEventListener('beforeunload', () => {
            if (updateInterval) clearInterval(updateInterval);
        });

        // Add animation style
        const style = document.createElement('style');
        style.textContent = `
        @keyframes pulse {
            0% { transform: scale(0.8); opacity: 0.5; }
            70% { transform: scale(1.2); opacity: 0; }
            100% { transform: scale(0.8); opacity: 0; }
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .custom-marker {
            background: transparent;
        }
    `;
        document.head.appendChild(style);

        // ==================== INITIALIZE ====================
        document.addEventListener('DOMContentLoaded', initMap);

        // Expose functions globally
        window.refreshData = refreshData;
        window.fitAllMarkers = fitAllMarkers;
        window.centerOnCurrentLocation = centerOnCurrentLocation;
        window.focusOnAgent = focusOnAgent;
        window.toggleTraffic = toggleTraffic;
    </script>
@endsection
