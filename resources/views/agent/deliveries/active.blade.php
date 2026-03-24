{{-- resources/views/agent/deliveries/active.blade.php --}}
@extends('layouts.app')

@section('title', 'Active Deliveries')

@section('content')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .active-deliveries {
            padding: 20px;
            background: #f8fafc;
            min-height: 100vh;
        }

        /* Header */
        .page-header {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            border-radius: 24px;
            padding: 25px 30px;
            margin-bottom: 25px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
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

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
            position: relative;
            z-index: 1;
        }

        .header-icon {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            backdrop-filter: blur(10px);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        .header-title {
            font-size: 28px;
            font-weight: 800;
            margin: 0 0 5px 0;
        }

        .header-subtitle {
            font-size: 13px;
            opacity: 0.9;
            margin: 0;
        }

        .stats-badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 12px 24px;
            border-radius: 50px;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .stats-badge .count {
            font-size: 28px;
            font-weight: 800;
            line-height: 1;
        }

        /* Stats Row */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 18px;
            border: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 55px;
            height: 55px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-info {
            flex: 1;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 800;
            color: #1e293b;
            line-height: 1.2;
        }

        .stat-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        /* Delivery Cards Grid */
        .deliveries-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(450px, 1fr));
            gap: 25px;
        }

        .delivery-card {
            background: white;
            border-radius: 24px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            transition: all 0.3s;
            position: relative;
            animation: fadeInUp 0.5s ease;
        }

        .delivery-card.new {
            animation: slideInRight 0.5s ease;
            border: 2px solid #10b981;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .delivery-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 30px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            padding: 18px 20px;
            background: linear-gradient(135deg, #fafbfc, #ffffff);
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .shipment-code {
            font-weight: 800;
            font-size: 16px;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-badge {
            padding: 5px 14px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-in_transit {
            background: #ede9fe;
            color: #5b21b6;
        }

        .status-out_for_delivery {
            background: #dcfce7;
            color: #166534;
        }

        .card-body {
            padding: 20px;
        }

        /* Live Location Section */
        .live-location {
            background: linear-gradient(135deg, #667eea10, #764ba210);
            border-radius: 16px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #e5e7eb;
        }

        .live-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .live-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.1);
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 600;
            color: #10b981;
        }

        .speed-gauge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: white;
            padding: 8px 12px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 700;
        }

        .speed-value {
            color: #3b82f6;
            font-size: 18px;
            font-weight: 800;
        }

        .distance-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
        }

        .distance-item {
            text-align: center;
            flex: 1;
        }

        .distance-value {
            font-size: 20px;
            font-weight: 800;
            color: #667eea;
        }

        .distance-label {
            font-size: 10px;
            color: #64748b;
        }

        .info-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 12px;
        }

        .info-icon {
            width: 32px;
            color: #94a3b8;
            font-size: 14px;
            text-align: center;
        }

        .info-text {
            flex: 1;
            font-size: 13px;
            color: #334155;
        }

        .address-text {
            font-size: 12px;
            color: #64748b;
            line-height: 1.4;
        }

        .value-highlight {
            font-size: 18px;
            font-weight: 800;
            color: #10b981;
        }

        .card-footer {
            padding: 16px 20px;
            background: #fafbfc;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 12px;
        }

        .btn-action {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s;
            text-decoration: none;
            flex: 1;
        }

        .btn-map {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-map:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }

        .btn-start {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .btn-track {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
        }

        .btn-call {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e5e7eb;
        }

        .live-pulse {
            width: 8px;
            height: 8px;
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
                transform: scale(2);
            }
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 24px;
            border: 1px solid #e5e7eb;
        }

        /* Toast Notification */
        .toast-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: white;
            border-radius: 12px;
            padding: 14px 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            display: none;
            align-items: center;
            gap: 12px;
            animation: slideUp 0.3s ease;
            border-left: 4px solid #10b981;
            max-width: 350px;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 992px) {
            .stats-row {
                grid-template-columns: repeat(2, 1fr);
            }

            .deliveries-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .stats-row {
                grid-template-columns: 1fr;
            }

            .page-header {
                flex-direction: column;
                text-align: center;
            }

            .header-left {
                flex-direction: column;
            }

            .card-footer {
                flex-direction: column;
            }
        }
    </style>

    <div class="active-deliveries">
        <!-- Header -->
        <div class="page-header">
            <div class="header-left">
                <div class="header-icon"><i class="fas fa-truck-fast"></i></div>
                <div>
                    <h1 class="header-title">Active Deliveries</h1>
                    <p class="header-subtitle" id="liveDateTime">
                        <i class="fas fa-clock me-1"></i> <span id="currentTime"></span>
                    </p>
                </div>
            </div>
            <div class="stats-badge">
                <div class="count" id="activeCount">{{ $activeShipments->count() }}</div>
                <div class="label">Active Deliveries</div>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="fas fa-hourglass-half"></i></div>
                <div class="stat-info">
                    <div class="stat-value" id="pendingCount">{{ $activeShipments->where('status', 'pending')->count() }}
                    </div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="fas fa-truck-moving"></i></div>
                <div class="stat-info">
                    <div class="stat-value" id="transitCount">{{ $activeShipments->where('status', 'in_transit')->count() }}
                    </div>
                    <div class="stat-label">In Transit</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="fas fa-flag-checkered"></i></div>
                <div class="stat-info">
                    <div class="stat-value" id="deliveryCount">
                        {{ $activeShipments->where('status', 'out_for_delivery')->count() }}</div>
                    <div class="stat-label">Out for Delivery</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="fas fa-chart-simple"></i></div>
                <div class="stat-info">
                    <div class="stat-value" id="todayCompleted">{{ $todayCompleted ?? 0 }}</div>
                    <div class="stat-label">Completed Today</div>
                </div>
            </div>
        </div>

        <!-- Active Deliveries Grid -->
        @if ($activeShipments->count() > 0)
            <div class="deliveries-grid" id="deliveriesGrid">
                @foreach ($activeShipments as $shipment)
                    @php
                        $progressMap = ['pending' => 0, 'in_transit' => 50, 'out_for_delivery' => 75];
                        $progress = $progressMap[$shipment->status] ?? 0;
                        $distance = $shipment->distance_from_agent ?? 0;
                        $eta = $shipment->eta_minutes ?? 0;
                    @endphp
                    <div class="delivery-card" data-shipment-id="{{ $shipment->id }}"
                        data-lat="{{ $shipment->destination_latitude ?? 22.524768 }}"
                        data-lng="{{ $shipment->destination_longitude ?? 72.955568 }}">
                        <div class="card-header">
                            <span class="shipment-code">
                                <i class="fas fa-hashtag"></i>{{ $shipment->shipment_number }}
                            </span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="live-badge">
                                    <span class="live-pulse"></span> Live
                                </span>
                                <span class="status-badge status-{{ $shipment->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $shipment->status)) }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Live Location Tracking -->
                            <div class="live-location" id="liveLocation-{{ $shipment->id }}">
                                <div class="live-header">
                                    <span class="live-badge">
                                        <i class="fas fa-satellite-dish"></i> Live Tracking
                                    </span>
                                    <div class="speed-gauge">
                                        <i class="fas fa-tachometer-alt"></i>
                                        <span class="speed-value" id="speed-{{ $shipment->id }}">--</span>
                                        <span>km/h</span>
                                    </div>
                                </div>
                                <div class="distance-info">
                                    <div class="distance-item">
                                        <div class="distance-value" id="distance-{{ $shipment->id }}">
                                            {{ number_format($distance, 1) }}</div>
                                        <div class="distance-label">km to destination</div>
                                    </div>
                                    <div class="distance-item">
                                        <div class="distance-value" id="eta-{{ $shipment->id }}">
                                            {{ $eta > 0 ? round($eta) . ' min' : '--' }}</div>
                                        <div class="distance-label">Est. Time</div>
                                    </div>
                                    <div class="distance-item">
                                        <div class="distance-value" id="status-icon-{{ $shipment->id }}">
                                            <i class="fas fa-motorcycle" style="color: #10b981;"></i>
                                        </div>
                                        <div class="distance-label">On Route</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Customer Info -->
                            <div class="info-row">
                                <div class="info-icon"><i class="fas fa-user-circle"></i></div>
                                <div class="info-text">
                                    <strong>{{ $shipment->receiver_name }}</strong>
                                    <div class="small text-muted">Receiver</div>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                                <div class="info-text">{{ $shipment->receiver_phone }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-icon"><i class="fas fa-location-dot"></i></div>
                                <div class="info-text address-text">
                                    {{ $shipment->shipping_address }}, {{ $shipment->city }}, {{ $shipment->state }} -
                                    {{ $shipment->pincode }}
                                </div>
                            </div>
                            @if ($shipment->declared_value)
                                <div class="info-row">
                                    <div class="info-icon"><i class="fas fa-rupee-sign"></i></div>
                                    <div class="info-text">
                                        <span
                                            class="value-highlight">₹{{ number_format($shipment->declared_value, 2) }}</span>
                                        <span class="text-muted ms-1">Order Value</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <a href="https://www.google.com/maps/dir/{{ auth()->user()->deliveryAgent->current_latitude ?? 22.524768 }}/{{ auth()->user()->deliveryAgent->current_longitude ?? 72.955568 }}/{{ $shipment->destination_latitude ?? 22.524768 }},{{ $shipment->destination_longitude ?? 72.955568 }}"
                                class="btn-action btn-map" target="_blank">
                                <i class="fas fa-map-marked-alt"></i> View Route
                            </a>
                            @if ($shipment->status == 'pending')
                                <a href="{{ route('agent.delivery.start', $shipment->id) }}" class="btn-action btn-start"
                                    onclick="return confirm('Start delivery for #{{ $shipment->shipment_number }}?')">
                                    <i class="fas fa-play"></i> Start
                                </a>
                            @else
                                <a href="tel:{{ $shipment->receiver_phone }}" class="btn-action btn-call">
                                    <i class="fas fa-phone-alt"></i> Call
                                </a>
                                <a href="{{ route('agent.tracking.live', $shipment->id) }}" class="btn-action btn-track">
                                    <i class="fas fa-map-marker-alt"></i> Track
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-box-open fa-4x text-muted mb-3 d-block"></i>
                <h4>No Active Deliveries</h4>
                <p class="text-muted">When admin assigns deliveries, they will appear here instantly.</p>
                <a href="{{ route('agent.deliveries.assigned') }}" class="btn btn-outline-primary mt-3">
                    <i class="fas fa-list me-2"></i> View All Assigned
                </a>
            </div>
        @endif
    </div>

    <script>
        // ==================== GLOBAL VARIABLES ====================
        let updateInterval = null;
        let shipmentLocations = {};
        let lastCheckedTime = new Date();
        let activeCount = {{ $activeShipments->count() }};
        let userLocation = {
            lat: {{ auth()->user()->deliveryAgent->current_latitude ?? 22.524768 }},
            lng: {{ auth()->user()->deliveryAgent->current_longitude ?? 72.955568 }}
        };

        // ==================== UPDATE CURRENT TIME ====================
        function updateCurrentTime() {
            const now = new Date();
            document.getElementById('currentTime').innerHTML = now.toLocaleTimeString() + ' | ' + now.toLocaleDateString();
        }
        setInterval(updateCurrentTime, 1000);
        updateCurrentTime();

        // ==================== CALCULATE DISTANCE (Haversine Formula) ====================
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

        // ==================== UPDATE DELIVERY STATS ====================
        function updateDeliveryStats(shipmentId, agentLat, agentLng) {
            const card = document.querySelector(`.delivery-card[data-shipment-id="${shipmentId}"]`);
            if (!card) return;

            const destLat = parseFloat(card.dataset.lat);
            const destLng = parseFloat(card.dataset.lng);
            if (isNaN(destLat) || isNaN(destLng)) return;

            const distance = calculateDistance(agentLat, agentLng, destLat, destLng);
            const speed = shipmentLocations[shipmentId]?.speed || 0;

            // Calculate ETA
            let eta = '--';
            if (speed > 0) {
                const minutes = (distance / speed) * 60;
                if (minutes < 60) eta = Math.round(minutes) + ' min';
                else eta = Math.floor(minutes / 60) + 'h ' + Math.round(minutes % 60) + 'm';
            }

            document.getElementById(`distance-${shipmentId}`).innerHTML = distance.toFixed(1);
            document.getElementById(`eta-${shipmentId}`).innerHTML = eta;
            document.getElementById(`speed-${shipmentId}`).innerHTML = Math.round(speed);

            // Update status icon based on distance
            const statusIcon = document.getElementById(`status-icon-${shipmentId}`);
            if (distance < 0.5) {
                statusIcon.innerHTML = '<i class="fas fa-flag-checkered" style="color: #10b981;"></i>';
                statusIcon.parentElement.querySelector('.distance-label').innerHTML = 'Nearby';
            } else if (distance < 2) {
                statusIcon.innerHTML = '<i class="fas fa-location-arrow" style="color: #f59e0b;"></i>';
                statusIcon.parentElement.querySelector('.distance-label').innerHTML = 'Close';
            } else {
                statusIcon.innerHTML = '<i class="fas fa-motorcycle" style="color: #3b82f6;"></i>';
                statusIcon.parentElement.querySelector('.distance-label').innerHTML = 'En Route';
            }
        }

        // ==================== FETCH AGENT LOCATION ====================
        async function fetchAgentLocation() {
            try {
                const response = await fetch('{{ route('agent.location.current') }}');
                const data = await response.json();
                if (data.success) {
                    userLocation = {
                        lat: data.latitude,
                        lng: data.longitude
                    };
                    document.querySelectorAll('.delivery-card').forEach(card => {
                        updateDeliveryStats(card.dataset.shipmentId, userLocation.lat, userLocation.lng);
                    });
                }
            } catch (error) {
                console.error('Error fetching location:', error);
            }
        }

        // ==================== CHECK FOR NEW ASSIGNMENTS ====================
        async function checkNewAssignments() {
            try {
                const response = await fetch('{{ route('agent.deliveries.check-new') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        last_checked: lastCheckedTime.toISOString()
                    })
                });
                const data = await response.json();

                if (data.success && data.new_shipments && data.new_shipments.length > 0) {
                    showNotification(`${data.new_shipments.length} new delivery(s) assigned!`, 'success');
                    setTimeout(() => location.reload(), 2000);
                }
                lastCheckedTime = new Date();
            } catch (error) {
                console.error('Error checking new assignments:', error);
            }
        }

        // ==================== SHOW NOTIFICATION ====================
        function showNotification(message, type = 'success') {
            if (Notification.permission === 'granted') {
                new Notification('INVOZA One', {
                    body: message,
                    icon: '/favicon.ico'
                });
            }

            let toast = document.getElementById('notificationToast');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'notificationToast';
                toast.className = 'toast-notification';
                document.body.appendChild(toast);
            }

            const icon = type === 'success' ? 'fa-check-circle' : 'fa-bell';
            const color = type === 'success' ? '#10b981' : '#3b82f6';

            toast.innerHTML = `
            <i class="fas ${icon}" style="color: ${color}; font-size: 20px;"></i>
            <div style="flex: 1;">
                <div style="font-weight: 600; margin-bottom: 2px;">New Assignment</div>
                <div style="font-size: 13px; color: #475569;">${message}</div>
            </div>
            <button onclick="this.parentElement.remove()" style="background: none; border: none; cursor: pointer; color: #94a3b8;">
                <i class="fas fa-times"></i>
            </button>
        `;
            toast.style.display = 'flex';
            setTimeout(() => {
                if (toast) toast.style.display = 'none';
            }, 8000);
        }

        // ==================== SEND LOCATION TO SERVER ====================
        async function sendLocationToServer(lat, lng, speed) {
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
                        speed: speed
                    })
                });
            } catch (error) {
                console.error('Error sending location:', error);
            }
        }

        // ==================== GET USER LOCATION ====================
        function getUserLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.watchPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        const speed = position.coords.speed ? position.coords.speed * 3.6 : 0;
                        userLocation = {
                            lat,
                            lng
                        };
                        sendLocationToServer(lat, lng, speed);
                        document.querySelectorAll('.delivery-card').forEach(card => {
                            updateDeliveryStats(card.dataset.shipmentId, lat, lng);
                        });
                    },
                    (error) => console.error('Geolocation error:', error), {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    }
                );
            }
        }

        // ==================== ADD NEW CARD ANIMATION ====================
        function addNewCardAnimation() {
            document.querySelectorAll('.delivery-card').forEach(card => {
                if (!card.hasAttribute('data-animated')) {
                    card.classList.add('new');
                    card.setAttribute('data-animated', 'true');
                    setTimeout(() => card.classList.remove('new'), 2000);
                }
            });
        }

        // ==================== REQUEST NOTIFICATION PERMISSION ====================
        if (Notification.permission !== 'granted' && Notification.permission !== 'denied') {
            Notification.requestPermission();
        }

        // ==================== INITIALIZE ====================
        getUserLocation();
        fetchAgentLocation();
        addNewCardAnimation();

        updateInterval = setInterval(() => {
            fetchAgentLocation();
            checkNewAssignments();
        }, 10000);

        window.addEventListener('beforeunload', () => {
            if (updateInterval) clearInterval(updateInterval);
        });
    </script>
@endsection
