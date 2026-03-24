{{-- resources/views/logistics/shipments/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Shipment Details - ' . $shipment->shipment_number)

@section('content')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .shipment-detail-page {
            padding: 24px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .header-card {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border-radius: 24px;
            padding: 24px 32px;
            margin-bottom: 24px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-info h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 8px 0;
        }

        .header-info p {
            opacity: 0.9;
            margin: 0;
            font-size: 14px;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
        }

        .status-badge.pending {
            background: #f59e0b;
            color: white;
        }

        .status-badge.picked {
            background: #3b82f6;
            color: white;
        }

        .status-badge.in_transit {
            background: #8b5cf6;
            color: white;
        }

        .status-badge.out_for_delivery {
            background: #10b981;
            color: white;
        }

        .status-badge.delivered {
            background: #10b981;
            color: white;
        }

        .status-badge.failed {
            background: #ef4444;
            color: white;
        }

        .btn-icon {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 10px 18px;
            border-radius: 40px;
            color: white;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .btn-icon:hover {
            background: white;
            color: #1e3c72;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.8);
            border-color: rgba(239, 68, 68, 0.5);
        }

        .btn-danger:hover {
            background: #ef4444;
            color: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            border: 1px solid #e9ecef;
        }

        .stat-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
        }

        .content-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-bottom: 24px;
        }

        .info-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            border: 1px solid #e9ecef;
        }

        .card-title {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e9ecef;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title i {
            color: #667eea;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f1f3f5;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #6c757d;
            font-size: 14px;
        }

        .info-value {
            font-weight: 600;
            color: #1e293b;
            font-size: 14px;
            text-align: right;
        }

        .address-text {
            font-size: 14px;
            line-height: 1.6;
            color: #495057;
            background: #f8f9fa;
            padding: 16px;
            border-radius: 12px;
            margin-top: 8px;
        }

        .agent-info-card {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 16px;
            padding: 16px;
            margin-top: 12px;
            color: white;
        }

        .agent-info-card .agent-name {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .agent-info-card .agent-details {
            font-size: 12px;
            opacity: 0.9;
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .map-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #e9ecef;
            margin-bottom: 24px;
        }

        .map-header {
            padding: 20px 24px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .map-header h3 {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .map-coordinates {
            font-size: 12px;
            color: #6c757d;
            font-family: monospace;
            background: #e9ecef;
            padding: 6px 12px;
            border-radius: 20px;
        }

        .map-container {
            height: 500px;
            width: 100%;
            position: relative;
            background: #e9ecef;
        }

        #shipmentMap {
            height: 100%;
            width: 100%;
        }

        .route-info-panel {
            position: absolute;
            bottom: 20px;
            left: 20px;
            background: white;
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            font-size: 13px;
            display: flex;
            gap: 20px;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.95);
        }

        .timeline-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            border: 1px solid #e9ecef;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
            max-height: 400px;
            overflow-y: auto;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 8px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            opacity: 0.3;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 24px;
        }

        .timeline-dot {
            position: absolute;
            left: -30px;
            top: 4px;
            width: 16px;
            height: 16px;
            background: #667eea;
            border-radius: 50%;
            border: 3px solid white;
        }

        .timeline-content {
            background: #f8f9fa;
            padding: 12px 16px;
            border-radius: 12px;
            margin-left: 8px;
        }

        .timeline-status {
            font-weight: 700;
            font-size: 13px;
            color: #1e293b;
        }

        .timeline-time {
            font-size: 11px;
            color: #6c757d;
            margin-top: 4px;
        }

        .action-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            border-radius: 24px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
        }

        .modal-body {
            padding: 24px;
        }

        .agent-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .agent-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .agent-item:hover {
            background: #f8f9fa;
            border-color: #667eea;
        }

        .agent-info h4 {
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 4px 0;
        }

        .agent-info p {
            font-size: 12px;
            color: #6c757d;
            margin: 0;
        }

        .agent-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .agent-status.available {
            background: #d1fae5;
            color: #065f46;
        }

        .assign-btn {
            padding: 8px 16px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
        }

        .remove-agent-btn {
            padding: 8px 16px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .toast-notification {
            position: fixed;
            top: 24px;
            right: 24px;
            padding: 12px 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-left: 4px solid #10b981;
            z-index: 1000;
            display: none;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @media (max-width: 992px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .shipment-detail-page {
                padding: 16px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .map-container {
                height: 400px;
            }
        }
    </style>

    <div class="shipment-detail-page">
        {{-- Header --}}
        <div class="header-card">
            <div class="header-info">
                <h1>Shipment #{{ $shipment->shipment_number }}</h1>
                <p>
                    <i class="fas fa-barcode"></i> Tracking: {{ $shipment->tracking_number ?? 'Not assigned' }}
                    @if ($shipment->sale_id)
                        <span style="margin-left: 16px;"><i class="fas fa-file-invoice"></i> Invoice: <a
                                href="{{ route('sales.show', $shipment->sale_id) }}"
                                style="color: white; text-decoration: underline;">{{ $shipment->sale->invoice_no ?? 'N/A' }}</a></span>
                    @endif
                </p>
            </div>
            <div class="action-buttons">
                <span class="status-badge {{ $shipment->status }}">
                    {{ strtoupper(str_replace('_', ' ', $shipment->status)) }}
                </span>
                <button class="btn-icon" onclick="copyTracking()">
                    <i class="fas fa-copy"></i> Copy Tracking
                </button>
                <button class="btn-icon" id="assignAgentBtn" onclick="showAssignAgentModal()"
                    style="{{ $shipment->assigned_to ? 'display: none;' : '' }}">
                    <i class="fas fa-user-plus"></i> Assign Agent
                </button>
                <button class="btn-icon btn-danger" id="removeAgentBtn" onclick="removeAgent()"
                    style="{{ $shipment->assigned_to ? '' : 'display: none;' }}">
                    <i class="fas fa-user-minus"></i> Remove Agent
                </button>
                @if ($shipment->tracking_number)
                    <a href="{{ route('logistics.live-track', $shipment->tracking_number) }}" class="btn-icon"
                        target="_blank">
                        <i class="fas fa-map-marked-alt"></i> Live Track
                    </a>
                @endif
                @if ($shipment->status != 'delivered')
                    <button class="btn-icon" onclick="quickDeliver()">
                        <i class="fas fa-check-circle"></i> Mark Delivered
                    </button>
                @endif
                <a href="{{ route('logistics.shipments.edit', $shipment->id) }}" class="btn-icon">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>

        {{-- Stats --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Declared Value</div>
                <div class="stat-value">₹{{ number_format($shipment->declared_value, 2) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Weight</div>
                <div class="stat-value">{{ $shipment->weight ?? '0' }} <span class="stat-unit">kg</span></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Quantity</div>
                <div class="stat-value">{{ $shipment->quantity ?? 1 }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Charge</div>
                <div class="stat-value">₹{{ number_format($shipment->total_charge, 2) }}</div>
            </div>
        </div>

        {{-- Content Grid --}}
        <div class="content-grid">
            <div class="info-card">
                <div class="card-title"><i class="fas fa-user"></i> Receiver Details</div>
                <div class="info-row"><span class="info-label">Name</span><span
                        class="info-value">{{ $shipment->receiver_name }}</span></div>
                <div class="info-row"><span class="info-label">Phone</span><span
                        class="info-value">{{ $shipment->receiver_phone }}</span></div>
            </div>

            <div class="info-card">
                <div class="card-title"><i class="fas fa-map-marker-alt"></i> Shipping Address</div>
                <div class="address-text">
                    <i class="fas fa-location-dot" style="color: #667eea; margin-right: 8px;"></i>
                    {{ $shipment->shipping_address }}<br>
                    {{ $shipment->city }}, {{ $shipment->state }}<br>
                    PIN: {{ $shipment->pincode }}, {{ $shipment->country }}
                </div>
            </div>

            <div class="info-card">
                <div class="card-title"><i class="fas fa-box"></i> Package Details</div>
                <div class="info-row"><span class="info-label">Package Type</span><span
                        class="info-value">{{ ucfirst($shipment->package_type ?? 'Box') }}</span></div>
                <div class="info-row"><span class="info-label">Shipping Method</span><span
                        class="info-value">{{ ucfirst($shipment->shipping_method ?? 'Standard') }}</span></div>
                <div class="info-row"><span class="info-label">Payment Mode</span><span
                        class="info-value">{{ strtoupper($shipment->payment_mode ?? 'Prepaid') }}</span></div>
            </div>

            <div class="info-card">
                <div class="card-title"><i class="fas fa-truck"></i> Courier Details</div>
                <div class="info-row"><span class="info-label">Courier Partner</span><span
                        class="info-value">{{ $shipment->courier_partner ?? 'Not Assigned' }}</span></div>
                <div class="info-row">
                    <span class="info-label">Assigned Agent</span>
                    <span class="info-value" id="assignedAgentName">
                        @if ($shipment->assigned_to)
                            <span
                                id="currentAgentNameDisplay">{{ $shipment->agent->name ?? 'Agent #' . $shipment->assigned_to }}</span>
                        @else
                            Not Assigned
                        @endif
                    </span>
                </div>
                <div class="info-row"><span class="info-label">Estimated Delivery</span><span
                        class="info-value">{{ $shipment->estimated_delivery_date?->format('d M Y') ?? 'Not set' }}</span>
                </div>

                @if ($shipment->assigned_to && $shipment->agent)
                    <div id="agentDetailsCard" class="agent-info-card">
                        <div class="agent-name">
                            <i class="fas fa-motorcycle"></i> {{ $shipment->agent->name }}
                        </div>
                        <div class="agent-details">
                            <span><i class="fas fa-phone"></i> {{ $shipment->agent->mobile ?? 'N/A' }}</span>
                            <span><i class="fas fa-star"></i> {{ $shipment->agent->rating ?? '4.5' }} ★</span>
                            <span><i class="fas fa-check-circle"></i> {{ $shipment->agent->total_deliveries ?? 0 }}
                                deliveries</span>
                        </div>
                    </div>
                @else
                    <div id="agentDetailsCard" style="display: none;"></div>
                @endif
            </div>
        </div>

        {{-- MAP CARD --}}
        <div class="map-card">
            <div class="map-header">
                <h3><i class="fas fa-map-marked-alt"></i> Shipment Location Map</h3>
                @if ($shipment->destination_latitude && $shipment->destination_longitude)
                    <div class="map-coordinates">
                        📍 {{ number_format($shipment->destination_latitude, 6) }},
                        {{ number_format($shipment->destination_longitude, 6) }}
                    </div>
                @endif
            </div>
            <div class="map-container">
                <div id="shipmentMap"></div>
            </div>
        </div>

        {{-- Tracking History --}}
        <div class="timeline-card">
            <div class="card-title"><i class="fas fa-history"></i> Tracking History</div>
            <div class="timeline">
                @php $__col = $shipment->trackings->sortByDesc('tracked_at'); @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $track)
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <div class="timeline-status">{{ strtoupper(str_replace('_', ' ', $track->status)) }}</div>
                            <div class="timeline-time">{{ $track->tracked_at->format('d M Y, h:i A') }}</div>
                            @if ($track->location)
                                <div class="timeline-location"><i class="fas fa-map-pin"></i> {{ $track->location }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
@else
                    <div style="text-align: center; padding: 40px; color: #6c757d;">
                        <i class="fas fa-info-circle" style="font-size: 40px; margin-bottom: 16px;"></i>
                        <p>No tracking history available</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Footer --}}
        <div class="action-footer">
            <a href="{{ route('logistics.shipments.index') }}" class="btn-icon"
                style="background: #f1f5f9; color: #475569;">
                <i class="fas fa-arrow-left"></i> Back to Shipments
            </a>
            <div style="display: flex; gap: 12px;">
                <button class="btn-icon" style="background: #f1f5f9; color: #475569;" onclick="window.print()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>

    {{-- Assign Agent Modal --}}
    <div id="assignAgentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-user-plus"></i> Assign Delivery Partner</h3>
                <button class="close-modal" onclick="closeAssignAgentModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="currentAgentInfo" class="current-agent" style="display: none;"></div>
                <div style="font-size: 12px; font-weight: 600; color: #6c757d; margin-bottom: 12px;">AVAILABLE DELIVERY
                    PARTNERS</div>
                <div id="agentList" class="agent-list">
                    <div style="text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin"></i> Loading
                        agents...</div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="shipmentId" value="{{ $shipment->id }}">
    <input type="hidden" id="destLat" value="{{ $shipment->destination_latitude ?? 22.524768 }}">
    <input type="hidden" id="destLng" value="{{ $shipment->destination_longitude ?? 72.955568 }}">
    <input type="hidden" id="warehouseLat" value="{{ env('WAREHOUSE_LAT', 22.524768) }}">
    <input type="hidden" id="warehouseLng" value="{{ env('WAREHOUSE_LNG', 72.955568) }}">
    <input type="hidden" id="agentLat" value="{{ $shipment->agent->current_latitude ?? 0 }}">
    <input type="hidden" id="agentLng" value="{{ $shipment->agent->current_longitude ?? 0 }}">
    <input type="hidden" id="agentId" value="{{ $shipment->assigned_to ?? 0 }}">
    <input type="hidden" id="agentName" value="{{ $shipment->agent->name ?? '' }}">
    <input type="hidden" id="agentPhone" value="{{ $shipment->agent->mobile ?? '' }}">
    <input type="hidden" id="agentRating" value="{{ $shipment->agent->rating ?? '4.5' }}">
    <input type="hidden" id="agentDeliveries" value="{{ $shipment->agent->total_deliveries ?? 0 }}">

    <div id="toast" class="toast-notification"></div>

    {{-- Google Maps API --}}
    <script>
        // ==================== GLOBAL VARIABLES ====================
        let map;
        let directionsService;
        let directionsRenderer;
        let agentMarker;
        let destMarker;
        let routeInfoWindow;

        const shipmentId = {{ $shipment->id }};
        const destLat = parseFloat(document.getElementById('destLat').value);
        const destLng = parseFloat(document.getElementById('destLng').value);
        const warehouseLat = parseFloat(document.getElementById('warehouseLat').value);
        const warehouseLng = parseFloat(document.getElementById('warehouseLng').value);
        let agentLat = parseFloat(document.getElementById('agentLat').value);
        let agentLng = parseFloat(document.getElementById('agentLng').value);
        let agentId = parseInt(document.getElementById('agentId').value);
        let currentAgentName = document.getElementById('agentName').value;

        console.log('Initial values:', {
            agentId,
            agentLat,
            agentLng,
            destLat,
            destLng
        });

        // ==================== GOOGLE MAPS INITIALIZATION ====================
        function initMap() {
            const mapContainer = document.getElementById('shipmentMap');
            console.log('initMap called');

            if (isNaN(destLat) || isNaN(destLng) || destLat === 0 || destLng === 0) {
                mapContainer.innerHTML =
                    `<div class="map-loading"><i class="fas fa-map-marker-alt"></i><p>No destination coordinates available</p></div>`;
                return;
            }

            try {
                map = new google.maps.Map(document.getElementById('shipmentMap'), {
                    center: {
                        lat: destLat,
                        lng: destLng
                    },
                    zoom: 12,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
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
                if (warehouseLat && warehouseLat !== 0 && !isNaN(warehouseLat)) {
                    new google.maps.Marker({
                        position: {
                            lat: warehouseLat,
                            lng: warehouseLng
                        },
                        map: map,
                        title: 'Warehouse',
                        icon: {
                            url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                            scaledSize: new google.maps.Size(40, 40)
                        }
                    }).addListener('click', () => new google.maps.InfoWindow({
                        content: '<b>🏢 Warehouse</b>'
                    }).open(map));
                }

                // Destination marker
                destMarker = new google.maps.Marker({
                    position: {
                        lat: destLat,
                        lng: destLng
                    },
                    map: map,
                    title: 'Destination',
                    icon: {
                        url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                        scaledSize: new google.maps.Size(44, 44)
                    }
                });
                destMarker.addListener('click', () => new google.maps.InfoWindow({
                    content: '<b>📍 Delivery Location</b><br>{{ $shipment->receiver_name }}'
                }).open(map, destMarker));

                const hasAgent = agentId > 0 && agentLat && agentLng && !isNaN(agentLat) && agentLat !== 0;

                if (hasAgent) {
                    console.log('✅ Agent assigned, drawing route...');

                    agentMarker = new google.maps.Marker({
                        position: {
                            lat: agentLat,
                            lng: agentLng
                        },
                        map: map,
                        title: 'Delivery Partner',
                        icon: {
                            url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                            scaledSize: new google.maps.Size(48, 48)
                        },
                        animation: google.maps.Animation.DROP
                    });
                    agentMarker.addListener('click', () => new google.maps.InfoWindow({
                        content: `<b>🛵 ${currentAgentName}</b>`
                    }).open(map, agentMarker));

                    drawRoute(agentLat, agentLng, destLat, destLng);

                    const bounds = new google.maps.LatLngBounds();
                    bounds.extend({
                        lat: agentLat,
                        lng: agentLng
                    });
                    bounds.extend({
                        lat: destLat,
                        lng: destLng
                    });
                    map.fitBounds(bounds);
                } else {
                    console.log('ℹ️ No agent assigned, showing destination only');
                    const bounds = new google.maps.LatLngBounds();
                    bounds.extend({
                        lat: destLat,
                        lng: destLng
                    });
                    map.fitBounds(bounds);
                }
                console.log('✅ Map initialized');
            } catch (error) {
                console.error('Map error:', error);
                mapContainer.innerHTML =
                    `<div class="map-loading"><i class="fas fa-exclamation-triangle"></i><p>Error loading map: ${error.message}</p></div>`;
            }
        }

        function drawRoute(originLat, originLng, destLat, destLng) {
            if (!directionsService) return;
            directionsService.route({
                origin: {
                    lat: originLat,
                    lng: originLng
                },
                destination: {
                    lat: destLat,
                    lng: destLng
                },
                travelMode: google.maps.TravelMode.DRIVING
            }, (result, status) => {
                if (status === 'OK') {
                    directionsRenderer.setDirections(result);
                    const leg = result.routes[0].legs[0];
                    addRouteInfoPanel(leg.distance.text, leg.duration.text);
                    console.log(`✅ Route drawn: ${leg.distance.text}, ${leg.duration.text}`);
                } else {
                    console.error('Route error:', status);
                }
            });
        }

        function addRouteInfoPanel(distance, duration) {
            const existingPanel = document.querySelector('.route-info-panel');
            if (existingPanel) existingPanel.remove();
            const panel = document.createElement('div');
            panel.className = 'route-info-panel';
            panel.innerHTML =
                `<div><i class="fas fa-road"></i> ${distance}</div><div><i class="fas fa-clock"></i> ${duration}</div><div><i class="fas fa-truck"></i> Delivery in progress</div>`;
            document.querySelector('.map-container').appendChild(panel);
        }

        // ==================== AGENT FUNCTIONS ====================
        function showAssignAgentModal() {
            document.getElementById('assignAgentModal').style.display = 'flex';
            loadAvailableAgents();
        }

        function closeAssignAgentModal() {
            document.getElementById('assignAgentModal').style.display = 'none';
        }

        function loadAvailableAgents() {
            const agentListDiv = document.getElementById('agentList');
            agentListDiv.innerHTML =
                `<div style="text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin"></i> Loading agents...</div>`;

            fetch('/logistics/api/available-agents')
                .then(res => {
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);
                    return res.json();
                })
                .then(agents => {
                    if (!agents || agents.length === 0) {
                        agentListDiv.innerHTML =
                            `<div style="text-align: center; padding: 40px;"><i class="fas fa-user-slash"></i><p>No available agents found</p></div>`;
                        return;
                    }

                    // Sort agents by city (optional)
                    agents.sort((a, b) => (a.city || '').localeCompare(b.city || ''));

                    agentListDiv.innerHTML = agents.map(agent => `
                <div class="agent-item" onclick="assignAgent(${agent.id}, '${agent.name}', ${agent.current_latitude || 0}, ${agent.current_longitude || 0}, '${agent.phone || ''}', ${agent.rating || 4.5}, ${agent.total_deliveries || 0})">
                    <div class="agent-info">
                        <h4>${agent.name}</h4>
                        <p style="margin-top: 4px;">
                            <i class="fas fa-phone"></i> ${agent.phone || 'N/A'} &nbsp;|&nbsp;
                            <i class="fas fa-star" style="color: #f59e0b;"></i> ${agent.rating || '4.5'} &nbsp;|&nbsp;
                            <i class="fas fa-box"></i> ${agent.total_deliveries || 0} deliveries
                        </p>
                        <p style="font-size: 11px; color: #667eea; margin-top: 4px;">
                            <i class="fas fa-map-marker-alt"></i> <strong>${agent.city || 'City not set'}</strong>
                            ${agent.current_latitude && agent.current_latitude !== 0 ?
                                '<span style="color: #10b981; margin-left: 8px;"><i class="fas fa-satellite-dish"></i> Live location</span>' :
                                '<span style="color: #f59e0b; margin-left: 8px;"><i class="fas fa-clock"></i> Location pending</span>'
                            }
                        </p>
                    </div>
                    <div>
                        <span class="agent-status ${agent.status === 'available' ? 'available' : 'busy'}">
                            ${agent.status === 'available' ? 'Available' : 'Busy'}
                        </span>
                        <button class="assign-btn" onclick="event.stopPropagation(); assignAgent(${agent.id}, '${agent.name}', ${agent.current_latitude || 0}, ${agent.current_longitude || 0}, '${agent.phone || ''}', ${agent.rating || 4.5}, ${agent.total_deliveries || 0})">
                            Assign
                        </button>
                    </div>
                </div>
            `).join('');
                })
                .catch(error => {
                    console.error('Error:', error);
                    agentListDiv.innerHTML =
                        `<div style="text-align: center; padding: 40px; color: red;">Error loading agents: ${error.message}</div>`;
                });
        }

        function assignAgent(id, name, lat, lng, phone, rating, deliveries) {
            if (!confirm(`Assign ${name} to this shipment?`)) return;

            fetch(`/logistics/shipments/${shipmentId}/assign-agent`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        agent_id: id
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(`✅ Agent ${name} assigned successfully!`, 'success');

                        agentId = id;
                        agentLat = data.agent_latitude || (typeof lat === 'number' ? lat : 22.524768);
                        agentLng = data.agent_longitude || (typeof lng === 'number' ? lng : 72.955568);
                        currentAgentName = name;

                        document.getElementById('agentId').value = id;
                        document.getElementById('agentLat').value = agentLat;
                        document.getElementById('agentLng').value = agentLng;
                        document.getElementById('agentName').value = name;

                        document.getElementById('assignedAgentName').innerHTML =
                            `<span id="currentAgentNameDisplay">${name}</span>`;
                        document.getElementById('assignAgentBtn').style.display = 'none';
                        document.getElementById('removeAgentBtn').style.display = 'inline-flex';

                        const agentCard = document.getElementById('agentDetailsCard');
                        agentCard.style.display = 'block';
                        agentCard.innerHTML = `
                        <div class="agent-name"><i class="fas fa-motorcycle"></i> ${name}</div>
                        <div class="agent-details">
                            <span><i class="fas fa-phone"></i> ${phone || 'N/A'}</span>
                            <span><i class="fas fa-star"></i> ${rating} ★</span>
                            <span><i class="fas fa-check-circle"></i> ${deliveries} deliveries</span>
                        </div>
                    `;

                        closeAssignAgentModal();
                        if (map) map = null;
                        setTimeout(() => initMap(), 500);
                    } else {
                        showToast('❌ ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    showToast('❌ Error assigning agent', 'error');
                    console.error('Error:', error);
                });
        }

        function removeAgent() {
            if (!confirm('Remove assigned agent from this shipment?')) return;

            fetch(`/logistics/shipments/${shipmentId}/remove-agent`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('✅ Agent removed successfully!', 'success');
                        agentId = 0;
                        agentLat = 0;
                        agentLng = 0;
                        currentAgentName = '';
                        document.getElementById('agentId').value = 0;
                        document.getElementById('agentLat').value = 0;
                        document.getElementById('agentLng').value = 0;
                        document.getElementById('agentName').value = '';
                        document.getElementById('assignedAgentName').innerHTML = 'Not Assigned';
                        document.getElementById('assignAgentBtn').style.display = 'inline-flex';
                        document.getElementById('removeAgentBtn').style.display = 'none';
                        document.getElementById('agentDetailsCard').style.display = 'none';
                        if (map) map = null;
                        setTimeout(() => initMap(), 500);
                    } else {
                        showToast('❌ ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    showToast('❌ Error removing agent', 'error');
                    console.error('Error:', error);
                });
        }

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.innerHTML = `<div><span>${type === 'success' ? '✅' : '❌'}</span> ${message}</div>`;
            toast.style.borderLeftColor = type === 'success' ? '#10b981' : '#ef4444';
            toast.style.display = 'block';
            setTimeout(() => toast.style.display = 'none', 3000);
        }

        function copyTracking() {
            navigator.clipboard.writeText('{{ $shipment->tracking_number ?? $shipment->shipment_number }}');
            showToast('Tracking number copied!', 'success');
        }

        function quickDeliver() {
            if (!confirm('Mark this shipment as delivered?')) return;
            fetch(`/logistics/shipments/${shipmentId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        status: 'delivered',
                        remarks: 'Marked delivered'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('✅ Shipment marked as delivered!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('❌ ' + data.message, 'error');
                    }
                })
                .catch(() => showToast('❌ Error updating status', 'error'));
        }

        window.initMap = initMap;
        window.onclick = function(e) {
            if (e.target === document.getElementById('assignAgentModal')) closeAssignAgentModal();
        };
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async
        defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
@endsection
