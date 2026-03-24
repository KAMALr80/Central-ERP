{{-- resources/views/logistics/service-areas.blade.php --}}
@extends('layouts.app')

@section('title', 'Service Areas & Coverage Map')

@section('content')
    <style>
        /* ================= PROFESSIONAL SERVICE AREAS STYLES ================= */
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

        .service-areas-page {
            padding: 2rem 1.5rem;
            max-width: 1600px;
            margin: 0 auto;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border-radius: 30px;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
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
            align-items: center;
            gap: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .header-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .header-text h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
        }

        .header-text p {
            font-size: 1rem;
            opacity: 0.9;
            margin: 0.5rem 0 0;
        }

        /* Main Grid */
        .service-grid {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 1.5rem;
        }

        @media (max-width: 1200px) {
            .service-grid {
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
        }

        .map-tabs {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .map-tab {
            padding: 0.7rem 1.5rem;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            background: transparent;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .map-tab:hover {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .map-tab.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .map-container {
            height: 600px;
            width: 100%;
            position: relative;
            background: #e9ecef;
        }

        #serviceMap {
            height: 100%;
            width: 100%;
        }

        .map-controls {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .map-control-btn {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: white;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            font-size: 1.2rem;
        }

        .map-control-btn:hover {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            transform: translateY(-2px);
        }

        /* Sidebar Cards */
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .stats-card {
            background: white;
            border-radius: 30px;
            padding: 1.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .card-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #667eea15, #764ba215);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #667eea;
            font-size: 1.2rem;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .card-subtitle {
            color: #64748b;
            font-size: 0.85rem;
            margin: 0.25rem 0 0;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .stat-item {
            background: #f8fafc;
            border-radius: 16px;
            padding: 1rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            background: linear-gradient(135deg, #667eea10, #764ba210);
            transform: scale(1.02);
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #667eea;
            line-height: 1.2;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        .stat-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
            margin: 1rem 0;
        }

        /* Agent List */
        .agent-list {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        .agent-list::-webkit-scrollbar {
            width: 6px;
        }

        .agent-list::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .agent-list::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 10px;
        }

        .agent-item {
            background: #f8fafc;
            border-radius: 16px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .agent-item:hover {
            background: white;
            border-color: #667eea;
            transform: translateX(5px);
        }

        .agent-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .agent-status {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }

        .agent-status.available {
            background: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }

        .agent-status.busy {
            background: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
        }

        .agent-status.offline {
            background: #6b7280;
            box-shadow: 0 0 0 3px rgba(107, 114, 128, 0.2);
        }

        .agent-name {
            font-weight: 700;
            color: #1e293b;
            font-size: 1rem;
        }

        .agent-badge {
            background: linear-gradient(135deg, #667eea15, #764ba215);
            color: #667eea;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .agent-details {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 0.5rem;
        }

        .agent-detail {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .agent-detail i {
            color: #667eea;
            font-size: 0.8rem;
        }

        /* Legend */
        .legend {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem;
            border-radius: 10px;
        }

        .legend-item:hover {
            background: #f8fafc;
        }

        .legend-color {
            width: 24px;
            height: 24px;
            border-radius: 8px;
        }

        .legend-color.available {
            background: #10b981;
        }

        .legend-color.busy {
            background: #f59e0b;
        }

        .legend-color.offline {
            background: #6b7280;
        }

        .legend-color.hotzone {
            background: linear-gradient(135deg, #ef4444, #f97316);
        }

        .legend-color.zone {
            background: rgba(59, 130, 246, 0.2);
            border: 2px solid #3b82f6;
        }

        .legend-text {
            font-weight: 500;
            color: #1e293b;
            flex: 1;
        }

        .legend-count {
            background: #f1f5f9;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            color: #475569;
        }

        /* City Grid */
        .city-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .city-item {
            background: #f8fafc;
            border-radius: 12px;
            padding: 0.75rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .city-item:hover {
            background: white;
            border: 1px solid #667eea;
            transform: translateY(-2px);
        }

        .city-name {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.9rem;
        }

        .city-count {
            font-size: 0.8rem;
            color: #667eea;
            font-weight: 600;
            margin-top: 0.25rem;
        }

        /* Loading Overlay */
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

        /* Toast */
        .toast {
            position: fixed;
            top: 30px;
            right: 30px;
            padding: 1rem 1.5rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border-left: 4px solid;
            display: none;
            z-index: 10000;
            max-width: 400px;
            animation: slideInRight 0.3s ease;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .toast.success {
            border-left-color: #10b981;
        }

        .toast.error {
            border-left-color: #ef4444;
        }

        .toast.warning {
            border-left-color: #f59e0b;
        }

        .toast-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #1e293b;
        }

        .toast-icon {
            font-size: 1.5rem;
        }

        .toast-message {
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .service-areas-page {
                padding: 1rem;
            }

            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .header-text h1 {
                font-size: 2rem;
            }

            .map-tabs {
                flex-direction: column;
            }

            .map-tab {
                width: 100%;
                justify-content: center;
            }

            .map-container {
                height: 400px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .city-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="service-areas-page">
        {{-- Loading Overlay --}}
        <div id="loadingOverlay" class="loading-overlay">
            <div class="spinner"></div>
            <div class="loading-text">Loading map data...</div>
        </div>

        {{-- Page Header --}}
        <div class="page-header">
            <div class="header-content">
                <div class="header-icon">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <div class="header-text">
                    <h1>Service Areas & Coverage</h1>
                    <p><i class="fas fa-location-dot"></i> Track delivery agents, heatmaps, and service zones</p>
                </div>
            </div>
        </div>

        {{-- Main Grid --}}
        <div class="service-grid">
            {{-- Left Column - Map --}}
            <div class="map-card">
                <div class="map-header">
                    <div class="map-tabs" id="mapTabs">
                        <button class="map-tab active" data-tab="agents">
                            <i class="fas fa-users"></i> Delivery Agents
                        </button>
                        <button class="map-tab" data-tab="heatmap">
                            <i class="fas fa-fire"></i> Delivery Heatmap
                        </button>
                        <button class="map-tab" data-tab="coverage">
                            <i class="fas fa-map-marked-alt"></i> Coverage Areas
                        </button>
                        <button class="map-tab" data-tab="zones">
                            <i class="fas fa-draw-polygon"></i> Service Zones
                        </button>
                    </div>
                </div>
                <div class="map-container">
                    <div id="serviceMap"></div>
                    <div class="map-controls">
                        <button class="map-control-btn" onclick="centerMap()" title="Center Map">
                            <i class="fas fa-crosshairs"></i>
                        </button>
                        <button class="map-control-btn" onclick="zoomIn()" title="Zoom In">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button class="map-control-btn" onclick="zoomOut()" title="Zoom Out">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button class="map-control-btn" onclick="refreshMap()" title="Refresh">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Right Column - Sidebar --}}
            <div class="sidebar">
                {{-- Statistics Card --}}
                <div class="stats-card">
                    <div class="card-header">
                        <div class="card-icon"><i class="fas fa-chart-pie"></i></div>
                        <div>
                            <h3 class="card-title">Service Statistics</h3>
                            <p class="card-subtitle">Real-time delivery metrics</p>
                        </div>
                    </div>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value">{{ $agents->count() }}</div>
                            <div class="stat-label">Total Agents</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $agents->where('status', 'available')->count() }}</div>
                            <div class="stat-label">Available</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $agents->where('status', 'busy')->count() }}</div>
                            <div class="stat-label">Busy</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $agents->where('status', 'offline')->count() }}</div>
                            <div class="stat-label">Offline</div>
                        </div>
                    </div>
                    <div class="stat-divider"></div>
                    <div style="display: flex; justify-content: space-between;">
                        <div>
                            <div style="font-size: 0.9rem; color: #64748b;">Coverage Pincodes</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #667eea;">{{ $coverage->count() }}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.9rem; color: #64748b;">Cities Covered</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #667eea;">
                                {{ $coverage->pluck('city')->unique()->count() }}</div>
                        </div>
                    </div>
                </div>

                {{-- Active Agents Card --}}
                <div class="stats-card">
                    <div class="card-header">
                        <div class="card-icon"><i class="fas fa-truck"></i></div>
                        <div>
                            <h3 class="card-title">Active Agents</h3>
                            <p class="card-subtitle">Currently on duty</p>
                        </div>
                    </div>
                    <div class="agent-list">
                        @php $__col = $agents->where('status', '!=', 'offline'); @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $agent)
                            <div class="agent-item"
                                onclick="focusAgent({{ $agent->current_latitude ?? 22.524768 }}, {{ $agent->current_longitude ?? 72.955568 }})">
                                <div class="agent-header">
                                    <span class="agent-status {{ $agent->status }}"></span>
                                    <span class="agent-name">{{ $agent->name }}</span>
                                    @if (($agent->successful_deliveries ?? 0) > 100)
                                        <span class="agent-badge">⭐ Top Performer</span>
                                    @endif
                                </div>
                                <div class="agent-details">
                                    <span class="agent-detail"><i class="fas fa-map-pin"></i>
                                        {{ $agent->city ?? 'Location unknown' }}</span>
                                    <span class="agent-detail"><i class="fas fa-box"></i>
                                        {{ $agent->successful_deliveries ?? 0 }} deliveries</span>
                                    @if ($agent->vehicle_type)
                                        <span class="agent-detail"><i class="fas fa-motorcycle"></i>
                                            {{ ucfirst($agent->vehicle_type) }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
@else
                            <div style="text-align: center; padding: 2rem; color: #64748b;">
                                <i class="fas fa-info-circle" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                                <p>No active agents at the moment</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Legend Card --}}
                <div class="stats-card">
                    <div class="card-header">
                        <div class="card-icon"><i class="fas fa-legend"></i></div>
                        <div>
                            <h3 class="card-title">Map Legend</h3>
                            <p class="card-subtitle">Understanding the map</p>
                        </div>
                    </div>
                    <div class="legend">
                        <div class="legend-item">
                            <div class="legend-color available"></div>
                            <span class="legend-text">Available Agent</span>
                            <span class="legend-count">{{ $agents->where('status', 'available')->count() }}</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color busy"></div>
                            <span class="legend-text">Busy Agent</span>
                            <span class="legend-count">{{ $agents->where('status', 'busy')->count() }}</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color offline"></div>
                            <span class="legend-text">Offline Agent</span>
                            <span class="legend-count">{{ $agents->where('status', 'offline')->count() }}</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color hotzone"></div>
                            <span class="legend-text">Hot Zone (High Deliveries)</span>
                            <span class="legend-count">{{ $hotspots->count() }}</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color zone"></div>
                            <span class="legend-text">5km Service Radius</span>
                            <span class="legend-count">1 zone</span>
                        </div>
                    </div>
                </div>

                {{-- Top Cities Card --}}
                <div class="stats-card">
                    <div class="card-header">
                        <div class="card-icon"><i class="fas fa-city"></i></div>
                        <div>
                            <h3 class="card-title">Top Cities</h3>
                            <p class="card-subtitle">By delivery volume</p>
                        </div>
                    </div>
                    <div class="city-grid">
                        @foreach ($coverage->groupBy('city')->sortByDesc(function ($items) {
                return $items->count();
            })->take(6) as $city => $areas)
                            <div class="city-item">
                                <div class="city-name">{{ $city }}</div>
                                <div class="city-count">{{ $areas->count() }} pincodes</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div id="toast" class="toast"></div>

    {{-- Google Maps API with your key --}}
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async
        defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

    <script>
        // ==================== MAP VARIABLES ====================
        let map;
        let markers = [];
        let heatmap = null;
        let circles = [];
        let polygons = [];
        let currentTab = 'agents';

        const GUJARAT_CENTER = {
            lat: 22.524768,
            lng: 72.955568
        };

        // Heatmap data points
        const heatmapData = [
            @foreach ($hotspots as $spot)
                {
                    location: new google.maps.LatLng({{ $spot->lat }}, {{ $spot->lng }}),
                    weight: {{ $spot->count }}
                },
            @endforeach
        ];

        // Service coverage data
        const coverageData = [
            @foreach ($coverage as $area)
                {
                    lat: {{ $area->lat ?? 22.524768 }},
                    lng: {{ $area->lng ?? 72.955568 }},
                    city: "{{ $area->city }}",
                    pincode: "{{ $area->pincode }}"
                },
            @endforeach
        ];

        // ==================== INITIALIZATION ====================
        function initMap() {
            map = new google.maps.Map(document.getElementById('serviceMap'), {
                center: GUJARAT_CENTER,
                zoom: 10,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                styles: [{
                    featureType: 'poi',
                    elementType: 'labels',
                    stylers: [{
                        visibility: 'off'
                    }]
                }]
            });

            setupTabs();
            loadAgentsLayer();
            showToast('Map loaded successfully', 'success');
        }

        function setupTabs() {
            const tabs = document.querySelectorAll('.map-tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    currentTab = this.dataset.tab;
                    switchTab(currentTab);
                });
            });
        }

        function switchTab(tabName) {
            clearLayers();
            switch (tabName) {
                case 'agents':
                    loadAgentsLayer();
                    break;
                case 'heatmap':
                    loadHeatmapLayer();
                    break;
                case 'coverage':
                    loadCoverageLayer();
                    break;
                case 'zones':
                    loadZonesLayer();
                    break;
            }
            showToast(`Switched to ${tabName} view`, 'info');
        }

        function clearLayers() {
            markers.forEach(m => m.setMap(null));
            markers = [];
            if (heatmap) heatmap.setMap(null);
            circles.forEach(c => c.setMap(null));
            circles = [];
            polygons.forEach(p => p.setMap(null));
            polygons = [];
        }

        // ==================== AGENTS LAYER ====================
        function loadAgentsLayer() {
            @foreach ($agents as $agent)
                @if ($agent->current_latitude && $agent->current_longitude)
                    (function() {
                        let color = '#10b981';
                        @if ($agent->status == 'busy')
                            color = '#f59e0b';
                        @elseif ($agent->status == 'offline')
                            color = '#6b7280';
                        @endif

                        let marker = new google.maps.Marker({
                            position: {
                                lat: {{ $agent->current_latitude }},
                                lng: {{ $agent->current_longitude }}
                            },
                            map: map,
                            title: "{{ $agent->name }}",
                            icon: {
                                url: 'https://maps.google.com/mapfiles/ms/icons/' +
                                    ('{{ $agent->status }}' === 'available' ? 'green' :
                                        '{{ $agent->status }}' === 'busy' ? 'orange' : 'grey') + '-dot.png',
                                scaledSize: new google.maps.Size(40, 40)
                            }
                        });

                        let infoContent = `
                    <div style="padding: 8px; min-width: 200px;">
                        <b style="font-size: 1rem;">{{ $agent->name }}</b><br>
                        <div style="margin: 5px 0;">
                            <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: ${color};"></span>
                            <span style="text-transform: capitalize;"> {{ $agent->status }}</span>
                        </div>
                        <div><i class="fas fa-phone"></i> {{ $agent->phone ?? 'N/A' }}</div>
                        <div><i class="fas fa-box"></i> {{ $agent->successful_deliveries ?? 0 }} deliveries</div>
                        <div><i class="fas fa-map-pin"></i> {{ $agent->city ?? 'Location unknown' }}</div>
                    </div>
                `;

                        let infoWindow = new google.maps.InfoWindow({
                            content: infoContent
                        });
                        marker.addListener('click', () => infoWindow.open(map, marker));
                        markers.push(marker);
                    })();
                @endif
            @endforeach
        }

        // ==================== HEATMAP LAYER ====================
        function loadHeatmapLayer() {
            if (heatmapData.length === 0) {
                showToast('No heatmap data available', 'warning');
                return;
            }

            heatmap = new google.maps.visualization.HeatmapLayer({
                data: heatmapData.map(d => d.location),
                map: map,
                radius: 30,
                opacity: 0.7,
                gradient: [
                    'rgba(59, 130, 246, 0)',
                    'rgba(59, 130, 246, 0.6)',
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(245, 158, 11, 0.9)',
                    'rgba(239, 68, 68, 1)'
                ]
            });
        }

        // ==================== COVERAGE LAYER ====================
        function loadCoverageLayer() {
            coverageData.forEach(area => {
                let circle = new google.maps.Circle({
                    center: {
                        lat: area.lat,
                        lng: area.lng
                    },
                    radius: 2000,
                    map: map,
                    fillColor: '#3b82f6',
                    fillOpacity: 0.1,
                    strokeColor: '#3b82f6',
                    strokeWeight: 1
                });

                let infoWindow = new google.maps.InfoWindow({
                    content: `<b>Pincode: ${area.pincode}</b><br>City: ${area.city}<br>Service Area: 2km radius`
                });
                circle.addListener('click', () => infoWindow.open(map, circle));
                circles.push(circle);
            });
        }

        // ==================== ZONES LAYER ====================
        function loadZonesLayer() {
            // Main warehouse zone (5km)
            let mainZone = new google.maps.Circle({
                center: {
                    lat: 22.524768,
                    lng: 72.955568
                },
                radius: 5000,
                map: map,
                fillColor: '#10b981',
                fillOpacity: 0.1,
                strokeColor: '#10b981',
                strokeWeight: 2
            });
            let mainInfo = new google.maps.InfoWindow({
                content: '<b>Main Warehouse</b><br>5km Service Zone<br>Priority Delivery Area'
            });
            mainZone.addListener('click', () => mainInfo.open(map, mainZone));
            circles.push(mainZone);

            // Vadodara zone
            let vadodaraZone = new google.maps.Circle({
                center: {
                    lat: 22.3072,
                    lng: 73.1812
                },
                radius: 8000,
                map: map,
                fillColor: '#f59e0b',
                fillOpacity: 0.1,
                strokeColor: '#f59e0b',
                strokeWeight: 2
            });
            let vadodaraInfo = new google.maps.InfoWindow({
                content: '<b>Vadodara Hub</b><br>8km Service Zone'
            });
            vadodaraZone.addListener('click', () => vadodaraInfo.open(map, vadodaraZone));
            circles.push(vadodaraZone);

            // Surat zone
            let suratZone = new google.maps.Circle({
                center: {
                    lat: 21.1702,
                    lng: 72.8311
                },
                radius: 8000,
                map: map,
                fillColor: '#f59e0b',
                fillOpacity: 0.1,
                strokeColor: '#f59e0b',
                strokeWeight: 2
            });
            let suratInfo = new google.maps.InfoWindow({
                content: '<b>Surat Hub</b><br>8km Service Zone'
            });
            suratZone.addListener('click', () => suratInfo.open(map, suratZone));
            circles.push(suratZone);
        }

        // ==================== MAP CONTROLS ====================
        function centerMap() {
            map.setCenter(GUJARAT_CENTER);
            map.setZoom(10);
            showToast('Map centered to Gujarat', 'info');
        }

        function zoomIn() {
            map.setZoom(map.getZoom() + 1);
        }

        function zoomOut() {
            map.setZoom(map.getZoom() - 1);
        }

        function refreshMap() {
            showLoading();
            clearLayers();
            setTimeout(() => {
                switchTab(currentTab);
                hideLoading();
                showToast('Map refreshed successfully', 'success');
            }, 500);
        }

        function focusAgent(lat, lng) {
            map.setCenter({
                lat: lat,
                lng: lng
            });
            map.setZoom(15);
            showToast('Focusing on agent location', 'info');
        }

        // ==================== UTILITIES ====================
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.innerHTML =
                `<div class="toast-content"><span class="toast-icon">${type === 'success' ? '✅' : type === 'error' ? '❌' : '⚠️'}</span><span class="toast-message">${message}</span></div>`;
            toast.className = 'toast ' + type;
            toast.style.display = 'block';
            setTimeout(() => toast.style.display = 'none', 3000);
        }

        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        window.centerMap = centerMap;
        window.zoomIn = zoomIn;
        window.zoomOut = zoomOut;
        window.refreshMap = refreshMap;
        window.focusAgent = focusAgent;

        // Handle window resize
        window.addEventListener('resize', () => {
            if (map) setTimeout(() => google.maps.event.trigger(map, 'resize'), 200);
        });
    </script>
@endsection
