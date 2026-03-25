{{-- resources/views/agent/delivery/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Delivery Details')

@section('content')
    <style>
        .delivery-page {
            min-height: 100vh;
            padding: clamp(16px, 3vw, 30px);
            width: 100%;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .delivery-card {
            background: #ffffff;
            border-radius: 30px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .delivery-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            padding: clamp(1.5rem, 4vw, 2rem);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .delivery-header::before {
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

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex: 1;
            min-width: 280px;
        }

        .header-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            flex-shrink: 0;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
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
            font-size: clamp(1.5rem, 5vw, 2rem);
            font-weight: 700;
            margin: 0 0 0.5rem 0;
        }

        .header-subtitle {
            opacity: 0.9;
            font-size: clamp(0.9rem, 2.5vw, 1rem);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .header-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
        }

        .header-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .header-btn {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .header-btn:hover {
            background: white;
            color: #1e293b;
            transform: translateY(-2px);
        }

        /* Info Cards */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
            padding: clamp(1.5rem, 4vw, 2rem);
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            border-color: #667eea;
        }

        .card-header-custom {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .card-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0;
            color: #1e293b;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.75rem;
            padding: 0.5rem 0;
            border-bottom: 1px dashed #e5e7eb;
        }

        .info-label {
            font-weight: 600;
            color: #64748b;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-weight: 600;
            color: #1e293b;
            text-align: right;
            font-size: 0.95rem;
        }

        .info-value i {
            margin-right: 0.25rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 30px;
            font-size: 0.85rem;
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

        .status-delivered {
            background: #d1fae5;
            color: #065f46;
        }

        /* Action Section */
        .action-section {
            padding: clamp(1.5rem, 4vw, 2rem);
            background: white;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .btn {
            padding: 1rem 2rem;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(16, 185, 129, 0.4);
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 2px solid #e5e7eb;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            box-shadow: 0 10px 20px rgba(245, 158, 11, 0.3);
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(245, 158, 11, 0.4);
        }

        .timeline-section {
            padding: 0 clamp(1.5rem, 4vw, 2rem) clamp(1.5rem, 4vw, 2rem);
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;
        }

        .timeline {
            position: relative;
            padding-left: 2rem;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
            padding-left: 1.5rem;
            border-left: 2px solid #e5e7eb;
        }

        .timeline-item:last-child {
            border-left-color: transparent;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 0;
            width: 14px;
            height: 14px;
            background: #667eea;
            border-radius: 50%;
            border: 2px solid white;
        }

        .timeline-status {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .timeline-time {
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .timeline-location {
            font-size: 0.85rem;
            color: #667eea;
        }

        .timeline-remarks {
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            border-left: 4px solid;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-info {
            background: #e0f2fe;
            border-left-color: #0284c7;
            color: #0c4a6e;
        }

        /* Destination Map Styles */
        .destination-map-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
            margin-bottom: 1.5rem;
        }

        .map-container {
            width: 100%;
            height: 400px;
            border-radius: 16px;
            overflow: hidden;
            border: 2px solid #e5e7eb;
            margin-top: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        #destinationMap {
            width: 100%;
            height: 100%;
        }

        .map-info {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 12px;
            font-size: 0.9rem;
        }

        .map-info-item {
            flex: 1;
        }

        .map-info-label {
            font-weight: 600;
            color: #64748b;
            font-size: 0.75rem;
            text-transform: uppercase;
            margin-bottom: 0.25rem;
        }

        .map-info-value {
            color: #1e293b;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .header-left {
                flex-direction: column;
                text-align: center;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .timeline {
                padding-left: 1rem;
            }

            .timeline-item {
                padding-left: 1rem;
            }
        }
    </style>

    <div class="delivery-page">
        <div class="container">
            <div class="delivery-card">
                <div class="delivery-header">
                    <div class="header-content">
                        <div class="header-left">
                            <div class="header-icon"><i class="fas fa-box-open"></i></div>
                            <div>
                                <h1 class="header-title">Delivery Details</h1>
                                <p class="header-subtitle">
                                    <span><i class="fas fa-hashtag"></i> {{ $shipment->shipment_number }}</span>
                                    <span class="header-badge">
                                        <i class="fas fa-calendar-alt"></i>
                                        {{ $shipment->created_at->format('d M Y, h:i A') }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="header-actions">
                            <a href="{{ route('agent.dashboard') }}" class="header-btn">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="info-grid" style="padding-bottom: 0;">
                    <div class="info-card"
                        style="background: linear-gradient(135deg, #f8fafc, #ffffff); text-align: center;">
                        @php
                            $statusColors = [
                                'pending' => 'status-pending',
                                'picked' => 'status-picked',
                                'in_transit' => 'status-in_transit',
                                'out_for_delivery' => 'status-out_for_delivery',
                                'delivered' => 'status-delivered',
                            ];
                            $statusClass = $statusColors[$shipment->status] ?? 'status-pending';
                        @endphp
                        <div class="status-badge {{ $statusClass }}" style="display: inline-flex;">
                            <i
                                class="fas fa-{{ $shipment->status == 'pending' ? 'clock' : ($shipment->status == 'in_transit' ? 'truck' : ($shipment->status == 'delivered' ? 'check-circle' : 'box')) }}"></i>
                            {{ ucfirst(str_replace('_', ' ', $shipment->status)) }}
                        </div>
                        @if ($shipment->estimated_delivery_date)
                            <p class="mt-2 mb-0 text-muted">
                                <i class="fas fa-calendar-week"></i> Est. Delivery:
                                {{ $shipment->estimated_delivery_date->format('d M Y') }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Info Cards -->
                <div class="info-grid">
                    <!-- Receiver Details -->
                    <div class="info-card">
                        <div class="card-header-custom">
                            <div class="card-icon"><i class="fas fa-user"></i></div>
                            <h3 class="card-title">Receiver Details</h3>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-user"></i> Name</span>
                            <span class="info-value">{{ $shipment->receiver_name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-phone"></i> Phone</span>
                            <span class="info-value">{{ $shipment->receiver_phone }}</span>
                        </div>
                        @if ($shipment->receiver_alternate_phone)
                            <div class="info-row">
                                <span class="info-label"><i class="fas fa-phone-alt"></i> Alternate</span>
                                <span class="info-value">{{ $shipment->receiver_alternate_phone }}</span>
                            </div>
                        @endif
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-map-marker-alt"></i> Address</span>
                            <span class="info-value">{{ $shipment->shipping_address }}, {{ $shipment->city }},
                                {{ $shipment->state }} - {{ $shipment->pincode }}</span>
                        </div>
                        @if ($shipment->landmark)
                            <div class="info-row">
                                <span class="info-label"><i class="fas fa-flag"></i> Landmark</span>
                                <span class="info-value">{{ $shipment->landmark }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Package Details -->
                    <div class="info-card">
                        <div class="card-header-custom">
                            <div class="card-icon"><i class="fas fa-box"></i></div>
                            <h3 class="card-title">Package Details</h3>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-weight-hanging"></i> Weight</span>
                            <span class="info-value">{{ $shipment->weight ?? 'N/A' }} kg</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-rupee-sign"></i> Declared Value</span>
                            <span class="info-value">₹{{ number_format($shipment->declared_value, 2) }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-credit-card"></i> Payment Mode</span>
                            <span class="info-value">{{ ucfirst($shipment->payment_mode ?? 'COD') }}</span>
                        </div>
                        @if ($shipment->total_charge)
                            <div class="info-row">
                                <span class="info-label"><i class="fas fa-receipt"></i> Total Charge</span>
                                <span class="info-value">₹{{ number_format($shipment->total_charge, 2) }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Delivery Instructions -->
                    <div class="info-card">
                        <div class="card-header-custom">
                            <div class="card-icon"><i class="fas fa-clipboard-list"></i></div>
                            <h3 class="card-title">Delivery Instructions</h3>
                        </div>
                        @if ($shipment->delivery_instructions)
                            <p class="text-muted">{{ $shipment->delivery_instructions }}</p>
                        @else
                            <p class="text-muted">No special instructions provided.</p>
                        @endif

                        <div class="mt-3 pt-2 border-top">
                            <div class="info-row">
                                <span class="info-label"><i class="fas fa-truck"></i> Courier Partner</span>
                                <span class="info-value">{{ $shipment->courier_partner ?? 'INVOZA Logistics' }}</span>
                            </div>
                            @if ($shipment->tracking_number)
                                <div class="info-row">
                                    <span class="info-label"><i class="fas fa-qrcode"></i> Tracking #</span>
                                    <span class="info-value">{{ $shipment->tracking_number }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Destination Map Card -->
                <div style="padding: 0 clamp(1.5rem, 4vw, 2rem);">
                    <div class="destination-map-card">
                        <div class="card-header-custom">
                            <div class="card-icon"><i class="fas fa-map-marked-alt"></i></div>
                            <h3 class="card-title">Delivery Location Map</h3>
                        </div>

                        @if ($shipment->destination_latitude && $shipment->destination_longitude)
                            <div class="map-container">
                                <div id="destinationMap"></div>
                            </div>
                            <div class="map-info">
                                <div class="map-info-item">
                                    <div class="map-info-label">📍 Coordinates</div>
                                    <div class="map-info-value">{{ number_format($shipment->destination_latitude, 6) }},
                                        {{ number_format($shipment->destination_longitude, 6) }}</div>
                                </div>
                                <div class="map-info-item">
                                    <div class="map-info-label">🏠 Address</div>
                                    <div class="map-info-value">{{ $shipment->city }}, {{ $shipment->state }} -
                                        {{ $shipment->pincode }}</div>
                                </div>
                                <div class="map-info-item">
                                    <div class="map-info-label">📍 Receiver</div>
                                    <div class="map-info-value">{{ $shipment->receiver_name }}</div>
                                </div>
                            </div>
                        @else
                            <div style="padding: 2rem; text-align: center; color: #64748b;">
                                <i class="fas fa-map-marker-alt"
                                    style="font-size: 2rem; margin-bottom: 0.5rem; display: block; opacity: 0.5;"></i>
                                <p>No destination coordinates available. Contact support if this is an issue.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-section">
                    @if ($shipment->status == 'pending')
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle fa-lg"></i>
                            <span>Ready to start delivery. Click "Start Delivery" to begin tracking.</span>
                        </div>
                        <div class="action-buttons">
                            <form action="{{ route('agent.delivery.start', $shipment->id) }}" method="POST"
                                style="width: 100%;">
                                @csrf
                                <button type="submit" class="btn btn-success w-100"
                                    onclick="return confirm('Start delivery for shipment #{{ $shipment->shipment_number }}?')">
                                    <i class="fas fa-play"></i> Start Delivery
                                </button>
                            </form>
                        </div>
                    @elseif(in_array($shipment->status, ['picked', 'in_transit', 'out_for_delivery']))
                        <div class="alert alert-info">
                            <i class="fas fa-map-marker-alt fa-lg"></i>
                            <span>Delivery in progress. Click "Track & Complete" to view live location and complete
                                delivery.</span>
                        </div>
                        <div class="action-buttons">
                            <a href="{{ route('agent.tracking.live', $shipment->id) }}" class="btn btn-primary w-100">
                                <i class="fas fa-map-marker-alt"></i> Track & Complete Delivery
                            </a>
                        </div>
                    @elseif($shipment->status == 'delivered')
                        <div class="alert alert-success" style="background: #d1fae5; border-left-color: #10b981;">
                            <i class="fas fa-check-circle fa-lg text-success"></i>
                            <span>Delivered successfully on
                                {{ $shipment->actual_delivery_date?->format('d M Y, h:i A') }}</span>
                        </div>
                        <div class="action-buttons">
                            <a href="{{ route('agent.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-home"></i> Back to Dashboard
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Tracking Timeline -->
                @if ($shipment->trackings && $shipment->trackings->count() > 0)
                    <div class="timeline-section">
                        <div class="card-header-custom" style="margin-bottom: 1.5rem;">
                            <div class="card-icon" style="width: 40px; height: 40px;"><i class="fas fa-history"></i>
                            </div>
                            <h3 class="card-title">Tracking History</h3>
                        </div>
                        <div class="timeline">
                            @foreach ($shipment->trackings->sortByDesc('tracked_at') as $track)
                                <div class="timeline-item">
                                    <div class="timeline-status">
                                        <i
                                            class="fas fa-{{ $track->status == 'delivered' ? 'check-circle' : ($track->status == 'in_transit' ? 'truck' : 'clock') }}"></i>
                                        {{ ucfirst(str_replace('_', ' ', $track->status)) }}
                                    </div>
                                    <div class="timeline-time">
                                        <i class="fas fa-calendar-alt"></i>
                                        {{ $track->tracked_at->format('d M Y, h:i A') }}
                                    </div>
                                    @if ($track->location)
                                        <div class="timeline-location">
                                            <i class="fas fa-map-pin"></i> {{ $track->location }}
                                        </div>
                                    @endif
                                    @if ($track->remarks)
                                        <div class="timeline-remarks">
                                            <i class="fas fa-comment"></i> "{{ $track->remarks }}"
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Initialize destination map
        @if ($shipment->destination_latitude && $shipment->destination_longitude)
            let map;
            const destLat = {{ $shipment->destination_latitude }};
            const destLng = {{ $shipment->destination_longitude }};
            const receiverName = "{{ $shipment->receiver_name }}";
            const address = "{{ $shipment->shipping_address }}, {{ $shipment->city }}, {{ $shipment->state }}";

            function initDestinationMap() {
                const mapContainer = document.getElementById('destinationMap');

                if (!mapContainer) return;

                map = new google.maps.Map(mapContainer, {
                    center: {
                        lat: destLat,
                        lng: destLng
                    },
                    zoom: 15,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    styles: [{
                        featureType: "all",
                        elementType: "labels.text.fill",
                        stylers: [{
                            color: "#616161"
                        }]
                    }]
                });

                // Add destination marker
                const marker = new google.maps.Marker({
                    position: {
                        lat: destLat,
                        lng: destLng
                    },
                    map: map,
                    title: receiverName,
                    icon: {
                        url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                        scaledSize: new google.maps.Size(48, 48)
                    },
                    animation: google.maps.Animation.DROP
                });

                // Add info window
                const infoWindow = new google.maps.InfoWindow({
                    content: `
                <div style="padding: 10px; font-family: Arial, sans-serif;">
                    <strong style="font-size: 14px; color: #1e293b;">📍 ${receiverName}</strong><br>
                    <small style="color: #64748b;">${address}</small><br>
                    <small style="color: #667eea; margin-top: 4px; display: block;">
                        <i class="fas fa-map-marked-alt"></i> ${ destLat.toFixed(6) }, ${destLng.toFixed(6)}
                    </small>
                </div>
            `
                });

                marker.addListener('click', function() {
                    infoWindow.open(map, marker);
                });

                // Open info window by default
                setTimeout(() => {
                    infoWindow.open(map, marker);
                }, 500);

                console.log('✅ Destination map initialized');
            }

            // Initialize map when page loads
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initDestinationMap);
            } else {
                initDestinationMap();
            }
        @endif
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}" async defer></script>

@endsection
