{{-- resources/views/agent/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Agent Dashboard')

@section('content')
    <style>
        .agent-dashboard {
            padding: 20px;
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

        /* Location Permission Modal */
        .location-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }

        .location-card {
            background: white;
            border-radius: 32px;
            max-width: 450px;
            width: 90%;
            padding: 32px;
            text-align: center;
            animation: slideUp 0.3s ease;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .location-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            animation: pulse-ring 2s infinite;
        }

        .location-icon i {
            font-size: 48px;
            color: white;
        }

        @keyframes pulse-ring {
            0% {
                box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.4);
            }

            70% {
                box-shadow: 0 0 0 20px rgba(102, 126, 234, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(102, 126, 234, 0);
            }
        }

        .location-title {
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 12px;
        }

        .location-desc {
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .location-status {
            background: #f1f5f9;
            border-radius: 16px;
            padding: 12px;
            margin-bottom: 24px;
            font-size: 13px;
        }

        .btn-location {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-location:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-location:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .location-error {
            background: #fee2e2;
            color: #dc2626;
            padding: 12px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 16px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .stat-title {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #1e293b;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #3b82f6, #1e40af);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .shipment-card {
            background: white;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 12px;
            border: 1px solid #e5e7eb;
            transition: all 0.3s;
        }

        .shipment-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-picked {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-in_transit {
            background: #ede9fe;
            color: #5b21b6;
        }

        .status-out_for_delivery {
            background: #dcfce7;
            color: #166534;
        }

        .btn-start {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-start:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            color: white;
        }

        .btn-track {
            background: linear-gradient(135deg, #3b82f6, #1e40af);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-track:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            color: white;
        }

        .weekly-chart {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-top: 20px;
            border: 1px solid #e5e7eb;
        }

        .chart-bar {
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 8px;
        }

        .bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            width: 0%;
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        .location-enabled-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #d1fae5;
            color: #065f46;
            padding: 6px 12px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 500;
            margin-left: 12px;
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

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="agent-dashboard">
        <!-- Location Permission Modal -->
        <div id="locationModal" class="location-modal" style="display: none;">
            <div class="location-card">
                <div class="location-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h2 class="location-title">Enable Location Services</h2>
                <p class="location-desc">
                    To start deliveries and enable live tracking, we need access to your device's location.
                    This helps customers track your delivery in real-time.
                </p>
                <div class="location-status" id="locationStatus">
                    <i class="fas fa-info-circle"></i> Waiting for location permission...
                </div>
                <button class="btn-location" id="enableLocationBtn">
                    <i class="fas fa-location-dot"></i> Enable Location
                </button>
            </div>
        </div>

        <!-- Main Dashboard Content (Initially Hidden) -->
        <div id="dashboardContent" style="display: none;">
            <!-- Welcome Header -->
            <div style="margin-bottom: 24px;">
                <div
                    style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                    <div>
                        <h1 style="font-size: 28px; font-weight: 700; color: #1e293b;">Welcome back, {{ $agent->name }}!
                        </h1>
                        <p style="color: #6b7280;">Here's your delivery summary for today</p>
                    </div>
                    <div class="location-enabled-badge" id="locationBadge">
                        <span class="live-pulse"></span>
                        <span>Live Location Active</span>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div class="stat-title">Today's Deliveries</div>
                        <div class="stat-value">{{ $todayDeliveries }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                </div>

                <div class="stat-card" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div class="stat-title">Today's Earnings</div>
                        <div class="stat-value">₹{{ number_format($todayEarnings, 2) }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-rupee-sign"></i></div>
                </div>

                <div class="stat-card" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div class="stat-title">Your Rating</div>
                        <div class="stat-value">{{ number_format($rating, 1) }} ★</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-star"></i></div>
                </div>

                <div class="stat-card" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div class="stat-title">Total Deliveries</div>
                        <div class="stat-value">{{ $totalDeliveries }}</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-box"></i></div>
                </div>
            </div>

            <!-- Active Deliveries -->
            <div style="margin-bottom: 30px;">
                <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 16px;">
                    <i class="fas fa-truck"></i> Active Deliveries
                </h2>

                @if (count($activeShipments) > 0)
                    @foreach ($activeShipments as $shipment)
                        <div class="shipment-card">
                            <div
                                style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 12px;">
                                <div style="flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                                        <span class="status-badge status-{{ $shipment->status }}">
                                            {{ ucfirst(str_replace('_', ' ', $shipment->status)) }}
                                        </span>
                                        <span style="font-weight: 600;">#{{ $shipment->shipment_number }}</span>
                                    </div>
                                    <div style="margin-top: 8px;">
                                        <div><i class="fas fa-user"></i> {{ $shipment->receiver_name }}</div>
                                        <div><i class="fas fa-phone"></i> {{ $shipment->receiver_phone }}</div>
                                        <div><i class="fas fa-map-marker-alt"></i> {{ $shipment->city }},
                                            {{ $shipment->state }}</div>
                                    </div>
                                </div>
                                <div>
                                    @if ($shipment->status == 'pending')
                                        <a href="{{ route('agent.delivery.start', $shipment->id) }}" class="btn-start"
                                            onclick="return checkLocationAndRedirect(this, event)">
                                            <i class="fas fa-play"></i> Start Delivery
                                        </a>
                                    @else
                                        <a href="{{ route('agent.tracking.live', $shipment->id) }}" class="btn-track"
                                            onclick="return checkLocationAndRedirect(this, event)">
                                            <i class="fas fa-map-marker-alt"></i> Track & Complete
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div
                        style="text-align: center; padding: 40px; background: white; border-radius: 20px; border: 1px solid #e5e7eb;">
                        <i class="fas fa-box-open" style="font-size: 48px; color: #9ca3af; margin-bottom: 16px;"></i>
                        <p style="color: #6b7280;">No active deliveries</p>
                        <p style="font-size: 13px; color: #9ca3af;">New deliveries will appear here when assigned</p>
                    </div>
                @endif
            </div>

            <!-- Weekly Stats -->
            <div class="weekly-chart">
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">
                    <i class="fas fa-chart-line"></i> Weekly Performance
                </h3>
                <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 12px;">
                    @foreach ($weeklyStats as $stat)
                        <div style="text-align: center;">
                            <div style="font-size: 12px; color: #6b7280;">{{ $stat['day'] }}</div>
                            <div style="font-size: 18px; font-weight: 700; color: #3b82f6;">{{ $stat['count'] }}</div>
                            <div class="chart-bar">
                                <div class="bar-fill" style="width: {{ min($stat['count'] * 10, 100) }}%"></div>
                            </div>
                            <div style="font-size: 10px; color: #9ca3af;">{{ $stat['date'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Deliveries -->
            @if (count($recentDeliveries) > 0)
                <div style="margin-top: 30px;">
                    <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">
                        <i class="fas fa-history"></i> Recent Deliveries
                    </h2>
                    <div style="background: white; border-radius: 16px; border: 1px solid #e5e7eb; overflow: hidden;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead style="background: #f8fafc;">
                                <tr>
                                    <th style="padding: 12px 16px; text-align: left;">Shipment</th>
                                    <th style="padding: 12px 16px; text-align: left;">Customer</th>
                                    <th style="padding: 12px 16px; text-align: left;">Date</th>
                                    <th style="padding: 12px 16px; text-align: left;">Amount</th>
                            </thead>
                            <tbody>
                                @foreach ($recentDeliveries as $delivery)
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                        <td style="padding: 12px 16px;">#{{ $delivery->shipment_number }}</td>
                                        <td style="padding: 12px 16px;">{{ $delivery->receiver_name }}</td>
                                        <td style="padding: 12px 16px;">
                                            {{ $delivery->actual_delivery_date?->format('d M Y') }}</td>
                                        <td style="padding: 12px 16px;">₹{{ number_format($delivery->declared_value, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        // ==================== LOCATION PERMISSION HANDLER ====================
        let isLocationEnabled = false;
        let watchId = null;
        let currentPosition = null;

        // Check if location is already enabled
        function checkExistingLocationPermission() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        // Location already enabled
                        isLocationEnabled = true;
                        currentPosition = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                            speed: position.coords.speed || 0,
                            accuracy: position.coords.accuracy
                        };
                        startSendingLocation();
                        hideLocationModal();
                        showDashboard();
                    },
                    (error) => {
                        // Location not enabled or denied
                        showLocationModal();
                    }, {
                        timeout: 5000
                    }
                );
            } else {
                showLocationModal();
            }
        }

        // Show location permission modal
        function showLocationModal() {
            document.getElementById('locationModal').style.display = 'flex';
            document.getElementById('dashboardContent').style.display = 'none';
        }

        // Hide modal and show dashboard
        function hideLocationModal() {
            document.getElementById('locationModal').style.display = 'none';
            document.getElementById('dashboardContent').style.display = 'block';
        }

        function showDashboard() {
            document.getElementById('dashboardContent').style.display = 'block';
        }

        // Enable location and start tracking
        function enableLocation() {
            const statusDiv = document.getElementById('locationStatus');
            const enableBtn = document.getElementById('enableLocationBtn');

            if (!navigator.geolocation) {
                statusDiv.innerHTML =
                    '<i class="fas fa-exclamation-triangle"></i> Geolocation is not supported by your browser.';
                statusDiv.style.background = '#fee2e2';
                statusDiv.style.color = '#dc2626';
                return;
            }

            enableBtn.disabled = true;
            enableBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Getting location...';

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    isLocationEnabled = true;
                    currentPosition = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                        speed: position.coords.speed || 0,
                        accuracy: position.coords.accuracy
                    };

                    statusDiv.innerHTML = '<i class="fas fa-check-circle"></i> Location enabled! Redirecting...';
                    statusDiv.style.background = '#d1fae5';
                    statusDiv.style.color = '#065f46';

                    // Start watching location
                    startWatchingLocation();
                    startSendingLocation();

                    setTimeout(() => {
                        hideLocationModal();
                        showDashboard();
                    }, 1000);
                },
                (error) => {
                    enableBtn.disabled = false;
                    enableBtn.innerHTML = '<i class="fas fa-location-dot"></i> Enable Location';

                    let errorMessage = '';
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = 'Location permission denied. Please enable location in browser settings.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = 'Location information unavailable.';
                            break;
                        case error.TIMEOUT:
                            errorMessage = 'Location request timed out.';
                            break;
                        default:
                            errorMessage = 'An unknown error occurred.';
                    }

                    statusDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + errorMessage;
                    statusDiv.style.background = '#fee2e2';
                    statusDiv.style.color = '#dc2626';
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }

        // Start watching location for continuous updates
        function startWatchingLocation() {
            if (watchId) {
                navigator.geolocation.clearWatch(watchId);
            }

            watchId = navigator.geolocation.watchPosition(
                (position) => {
                    currentPosition = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                        speed: position.coords.speed || 0,
                        accuracy: position.coords.accuracy,
                        heading: position.coords.heading || 0
                    };
                    sendLocationToServer(currentPosition.lat, currentPosition.lng, currentPosition.speed,
                        currentPosition.accuracy);
                },
                (error) => {
                    console.error('Watch location error:', error);
                }, {
                    enableHighAccuracy: true,
                    timeout: 5000,
                    maximumAge: 0
                }
            );
        }

        // Send location to server
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
                        timestamp: new Date().toISOString()
                    })
                });
            } catch (error) {
                console.error('Error sending location:', error);
            }
        }

        // Start sending location periodically
        function startSendingLocation() {
            setInterval(() => {
                if (currentPosition) {
                    sendLocationToServer(currentPosition.lat, currentPosition.lng, currentPosition.speed,
                        currentPosition.accuracy);
                }
            }, 5000);
        }

        // Check location before redirecting to live tracking
        function checkLocationAndRedirect(element, event) {
            if (!isLocationEnabled && !currentPosition) {
                event.preventDefault();
                showLocationModal();
                return false;
            }
            return true;
        }

        // ==================== INITIALIZE ====================
        document.addEventListener('DOMContentLoaded', function() {
            checkExistingLocationPermission();
        });

        // Attach event listener to enable button
        document.getElementById('enableLocationBtn').addEventListener('click', enableLocation);
    </script>
@endsection
