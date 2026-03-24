{{-- resources/views/logistics/route-planner.blade.php --}}
@extends('layouts.app')

@section('title', 'Route Planner & Optimization')

@section('content')
    <style>
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

        .route-planner-page {
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

        .filter-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .filter-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .filter-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea15, #764ba215);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #667eea;
            font-size: 1.2rem;
        }

        .filter-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .filter-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: flex-end;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .filter-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .filter-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .filter-btn {
            padding: 0.75rem 2rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .route-grid {
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 1200px) {
            .route-grid {
                grid-template-columns: 1fr;
            }
        }

        .shipments-panel {
            background: white;
            border-radius: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: fit-content;
        }

        .panel-header {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .panel-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .panel-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea15, #764ba215);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #667eea;
            font-size: 1.2rem;
        }

        .panel-title h3 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .panel-title span {
            font-size: 0.85rem;
            color: #64748b;
        }

        .select-all-btn {
            padding: 0.5rem 1rem;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .select-all-btn:hover {
            border-color: #667eea;
            color: #667eea;
        }

        .shipment-list {
            max-height: 500px;
            overflow-y: auto;
            padding: 1rem;
            background: #f8fafc;
        }

        .shipment-item {
            background: white;
            border-radius: 16px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .shipment-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.15);
            border-color: #667eea;
        }

        .shipment-item.selected {
            background: linear-gradient(135deg, #667eea10, #764ba210);
            border-color: #667eea;
            border-width: 2px;
        }

        .shipment-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .shipment-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #667eea;
        }

        .shipment-number {
            font-weight: 700;
            color: #667eea;
            font-size: 0.95rem;
        }

        .shipment-badge {
            background: linear-gradient(135deg, #667eea15, #764ba215);
            color: #667eea;
            padding: 0.25rem 0.75rem;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: auto;
        }

        .shipment-details {
            margin-left: 2.75rem;
        }

        .shipment-customer {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
        }

        .shipment-address {
            font-size: 0.85rem;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .shipment-meta {
            display: flex;
            gap: 1rem;
            margin-top: 0.5rem;
            font-size: 0.8rem;
            color: #94a3b8;
        }

        .order-panel {
            background: white;
            border-radius: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            margin-top: 1.5rem;
        }

        .order-header {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .order-header i {
            color: #667eea;
            font-size: 1.2rem;
        }

        .order-header h4 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .order-list {
            padding: 1rem;
            min-height: 200px;
            background: #f8fafc;
        }

        .order-item {
            background: white;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border: 1px solid #e5e7eb;
            cursor: grab;
            transition: all 0.3s ease;
        }

        .order-item:hover {
            transform: translateX(5px);
            border-color: #667eea;
        }

        .order-item:active {
            cursor: grabbing;
        }

        .order-number {
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .order-content {
            flex: 1;
        }

        .order-customer {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.95rem;
        }

        .order-address {
            font-size: 0.8rem;
            color: #64748b;
        }

        .empty-order {
            text-align: center;
            padding: 3rem 1rem;
            color: #94a3b8;
        }

        .empty-order i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .map-card {
            background: white;
            border-radius: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .map-header {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .map-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .map-title i {
            color: #667eea;
            font-size: 1.2rem;
        }

        .map-title h5 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .map-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .map-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .map-btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .map-btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }

        .map-btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .map-btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .map-btn-success:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(16, 185, 129, 0.4);
        }

        .map-btn-success:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .map-btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 2px solid #e5e7eb;
        }

        .map-btn-secondary:hover {
            background: #e2e8f0;
        }

        .map-container {
            height: 550px;
            width: 100%;
            position: relative;
        }

        #routeMap {
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
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .map-control-btn:hover {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .route-info-card {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-radius: 16px;
            padding: 1rem;
            margin-top: 1rem;
            display: none;
        }

        .route-info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .route-info-item {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
            border: 1px solid #e5e7eb;
        }

        .route-info-label {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
        }

        .route-info-label i {
            color: #667eea;
        }

        .route-info-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
        }

        .route-info-value small {
            font-size: 0.9rem;
            color: #94a3b8;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal-content {
            background: white;
            border-radius: 30px;
            width: 100%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-radius: 30px 30px 0 0;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #64748b;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .modal-close:hover {
            background: #fee2e2;
            color: #ef4444;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-form-group {
            margin-bottom: 1.5rem;
        }

        .modal-form-label {
            display: block;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .modal-form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .modal-form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .modal-footer {
            padding: 1.5rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            background: #f8fafc;
            border-radius: 0 0 30px 30px;
        }

        .modal-btn {
            padding: 0.875rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }

        .modal-btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .modal-btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 2px solid #e5e7eb;
        }

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

        @media (max-width: 768px) {
            .route-planner-page {
                padding: 1rem;
            }

            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .header-text h1 {
                font-size: 2rem;
            }

            .filter-grid {
                flex-direction: column;
            }

            .filter-group {
                width: 100%;
            }

            .filter-btn {
                width: 100%;
                justify-content: center;
            }

            .map-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .map-actions {
                width: 100%;
            }

            .map-btn {
                flex: 1;
                justify-content: center;
            }

            .route-info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="route-planner-page">
        <div id="loadingOverlay" class="loading-overlay">
            <div class="spinner"></div>
            <div class="loading-text">Optimizing route...</div>
        </div>

        <div class="page-header">
            <div class="header-content">
                <div class="header-icon"><i class="fas fa-route"></i></div>
                <div class="header-text">
                    <h1>Route Planner & Optimization</h1>
                    <p><i class="fas fa-map-marked-alt"></i> Plan and optimize delivery routes for maximum efficiency</p>
                </div>
            </div>
        </div>

        <div class="filter-card">
            <div class="filter-header">
                <div class="filter-icon"><i class="fas fa-filter"></i></div>
                <h3 class="filter-title">Filter Shipments</h3>
            </div>
            <form method="GET" action="{{ route('logistics.route-planner') }}" id="filterForm">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label class="filter-label">Delivery Date</label>
                        <input type="date" name="date" class="filter-control" value="{{ $date }}">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Delivery Agent</label>
                        <select name="agent_id" class="filter-control">
                            <option value="">All Agents</option>
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->user_id }}" {{ $agentId == $agent->user_id ? 'selected' : '' }}>
                                    {{ $agent->name }} ({{ $agent->vehicle_type ?? 'Bike' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <button type="submit" class="filter-btn"><i class="fas fa-search"></i> Apply Filters</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="route-grid">
            <div>
                <div class="shipments-panel">
                    <div class="panel-header">
                        <div class="panel-title">
                            <div class="panel-icon"><i class="fas fa-box"></i></div>
                            <div>
                                <h3>Available Shipments</h3>
                                <span>{{ $shipments->count() }} shipments found</span>
                            </div>
                        </div>
                        <button type="button" class="select-all-btn" id="selectAllBtn"><i class="fas fa-check-double"></i>
                            Select All</button>
                    </div>
                    <div class="shipment-list" id="shipmentList">
                        @php $__col = $shipments; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $shipment)
                            <div class="shipment-item" data-id="{{ $shipment->id }}"
                                data-lat="{{ $shipment->destination_latitude ?? 22.524768 }}"
                                data-lng="{{ $shipment->destination_longitude ?? 72.955568 }}"
                                data-address="{{ $shipment->shipping_address }}"
                                data-customer="{{ $shipment->receiver_name }}" data-city="{{ $shipment->city }}"
                                data-phone="{{ $shipment->receiver_phone }}">
                                <div class="shipment-header">
                                    <input type="checkbox" class="shipment-checkbox" value="{{ $shipment->id }}">
                                    <span class="shipment-number">{{ $shipment->shipment_number }}</span>
                                    <span class="shipment-badge">{{ $shipment->city }}</span>
                                </div>
                                <div class="shipment-details">
                                    <div class="shipment-customer"><i class="fas fa-user"
                                            style="margin-right:5px; color:#667eea;"></i> {{ $shipment->receiver_name }}
                                    </div>
                                    <div class="shipment-address"><i class="fas fa-map-pin"></i>
                                        {{ Str::limit($shipment->shipping_address, 50) }}</div>
                                    <div class="shipment-meta"><span><i class="fas fa-phone"></i>
                                            {{ $shipment->receiver_phone }}</span>
                                        @if ($shipment->weight)
                                            <span><i class="fas fa-weight"></i> {{ $shipment->weight }} kg</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
@else
                            <div style="text-align:center; padding:3rem 1rem;"><i class="fas fa-box-open"
                                    style="font-size:3rem; color:#94a3b8; margin-bottom:1rem;"></i>
                                <p style="color:#64748b;">No shipments found</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="order-panel">
                    <div class="order-header"><i class="fas fa-sort-amount-down"></i>
                        <h4>Optimized Delivery Order</h4>
                    </div>
                    <div class="order-list" id="optimizedOrder">
                        <div class="empty-order"><i class="fas fa-route"></i>
                            <p>Select shipments and click "Optimize Route" to see the best order</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="map-card">
                <div class="map-header">
                    <div class="map-title"><i class="fas fa-map-marked-alt"></i>
                        <h5>Route Visualization</h5>
                    </div>
                    <div class="map-actions">
                        <button type="button" class="map-btn map-btn-primary" id="optimizeRouteBtn" disabled><i
                                class="fas fa-route"></i> Optimize Route</button>
                        <button type="button" class="map-btn map-btn-success" id="assignRouteBtn" disabled><i
                                class="fas fa-check"></i> Assign to Agent</button>
                        <button type="button" class="map-btn map-btn-secondary" onclick="resetMap()"><i
                                class="fas fa-redo-alt"></i> Reset</button>
                    </div>
                </div>
                <div class="map-container">
                    <div id="routeMap"></div>
                    <div class="map-controls">
                        <button class="map-control-btn" onclick="centerMap()"><i class="fas fa-crosshairs"></i></button>
                        <button class="map-control-btn" onclick="zoomIn()"><i class="fas fa-plus"></i></button>
                        <button class="map-control-btn" onclick="zoomOut()"><i class="fas fa-minus"></i></button>
                        <button class="map-control-btn" onclick="toggleRouteInfo()"><i
                                class="fas fa-info-circle"></i></button>
                    </div>
                </div>
                <div class="route-info-card" id="routeInfo">
                    <div class="route-info-grid">
                        <div class="route-info-item">
                            <div class="route-info-label"><i class="fas fa-road"></i> Total Distance</div>
                            <div class="route-info-value" id="totalDistance">0 <small>km</small></div>
                        </div>
                        <div class="route-info-item">
                            <div class="route-info-label"><i class="fas fa-clock"></i> Est. Time</div>
                            <div class="route-info-value" id="totalTime">0 <small>mins</small></div>
                        </div>
                        <div class="route-info-item">
                            <div class="route-info-label"><i class="fas fa-map-pin"></i> Total Stops</div>
                            <div class="route-info-value" id="totalStops">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="assignModal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-tie"></i> Assign Route to Agent</h5>
                <button type="button" class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <form id="assignForm">
                    @csrf
                    <div class="modal-form-group">
                        <label class="modal-form-label">Select Delivery Agent</label>
                        <select name="agent_id" class="modal-form-control" required id="agentSelect">
                            <option value="">Choose an agent...</option>
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->user_id }}">{{ $agent->name }}
                                    ({{ $agent->vehicle_type ?? 'Bike' }}) @if ($agent->city)
                                        - {{ $agent->city }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="shipment_ids" id="assignShipmentIds">
                    <input type="hidden" name="route_order" id="assignRouteOrder">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn modal-btn-secondary" onclick="closeModal()"><i
                        class="fas fa-times"></i> Cancel</button>
                <button type="button" class="modal-btn modal-btn-primary" id="confirmAssignBtn"><i
                        class="fas fa-check"></i> Assign Route</button>
            </div>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async
        defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        let map, directionsService, directionsRenderer, warehouseMarker, shipmentMarkers = [];
        let selectedShipments = [],
            currentRouteOrder = [],
            sortable;
        const WAREHOUSE = {
            lat: {{ $warehouse['lat'] }},
            lng: {{ $warehouse['lng'] }}
        };

        function initMap() {
            map = new google.maps.Map(document.getElementById('routeMap'), {
                center: WAREHOUSE,
                zoom: 12,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                styles: [{
                    featureType: 'poi',
                    elementType: 'labels',
                    stylers: [{
                        visibility: 'off'
                    }]
                }]
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
            warehouseMarker = new google.maps.Marker({
                position: WAREHOUSE,
                map: map,
                title: 'Warehouse',
                icon: {
                    url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                    scaledSize: new google.maps.Size(44, 44)
                }
            });
            setupEventListeners();
            updateSelectedShipments();
        }

        function setupEventListeners() {
            document.getElementById('selectAllBtn').addEventListener('click', selectAll);
            document.querySelectorAll('.shipment-checkbox').forEach(cb => cb.addEventListener('change', () =>
                updateSelectedShipments()));
            document.getElementById('optimizeRouteBtn').addEventListener('click', optimizeRoute);
            document.getElementById('assignRouteBtn').addEventListener('click', openAssignModal);
            document.getElementById('confirmAssignBtn').addEventListener('click', assignRoute);
        }

        function updateSelectedShipments() {
            selectedShipments = [];
            document.querySelectorAll('.shipment-checkbox:checked').forEach(cb => {
                let item = cb.closest('.shipment-item');
                selectedShipments.push({
                    id: item.dataset.id,
                    lat: parseFloat(item.dataset.lat),
                    lng: parseFloat(item.dataset.lng),
                    address: item.dataset.address,
                    customer: item.dataset.customer,
                    city: item.dataset.city,
                    phone: item.dataset.phone
                });
                item.classList.add('selected');
            });
            document.querySelectorAll('.shipment-checkbox:not(:checked)').forEach(cb => cb.closest('.shipment-item')
                .classList.remove('selected'));
            document.getElementById('optimizeRouteBtn').disabled = selectedShipments.length === 0;
            updateMarkers();
        }

        function selectAll() {
            let checkboxes = document.querySelectorAll('.shipment-checkbox');
            let allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
            updateSelectedShipments();
            showToast(allChecked ? 'All shipments deselected' : 'All shipments selected', 'info');
        }

        function updateMarkers() {
            shipmentMarkers.forEach(m => m.setMap(null));
            shipmentMarkers = [];
            selectedShipments.forEach((s, i) => {
                let marker = new google.maps.Marker({
                    position: {
                        lat: s.lat,
                        lng: s.lng
                    },
                    map: map,
                    title: s.customer,
                    label: {
                        text: (i + 1).toString(),
                        color: 'white',
                        fontWeight: 'bold',
                        fontSize: '12px'
                    },
                    icon: {
                        url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                        scaledSize: new google.maps.Size(36, 36)
                    }
                });
                let info = new google.maps.InfoWindow({
                    content: `<b>${s.customer}</b><br>${s.address}<br>📞 ${s.phone}`
                });
                marker.addListener('click', () => info.open(map, marker));
                shipmentMarkers.push(marker);
            });
        }

        async function optimizeRoute() {
            if (selectedShipments.length === 0) {
                showToast('Please select at least one shipment', 'error');
                return;
            }
            showLoading();
            try {
                let response = await fetch('{{ route('logistics.route.calculate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        shipment_ids: selectedShipments.map(s => s.id),
                        start_lat: WAREHOUSE.lat,
                        start_lng: WAREHOUSE.lng
                    })
                });
                let data = await response.json();
                hideLoading();
                if (data.success) {
                    displayOptimizedRoute(data);
                    showToast('Route optimized successfully!', 'success');
                } else {
                    showToast(data.message || 'Error optimizing route', 'error');
                }
            } catch (error) {
                hideLoading();
                showToast('Failed to optimize route', 'error');
            }
        }

        function displayOptimizedRoute(data) {
            let waypoints = data.waypoints.map(w => ({
                location: {
                    latLng: {
                        latitude: w.lat,
                        longitude: w.lng
                    }
                }
            }));
            directionsService.route({
                origin: {
                    lat: data.waypoints[0].lat,
                    lng: data.waypoints[0].lng
                },
                destination: {
                    lat: data.waypoints[data.waypoints.length - 1].lat,
                    lng: data.waypoints[data.waypoints.length - 1].lng
                },
                waypoints: waypoints.slice(1, -1),
                travelMode: google.maps.TravelMode.DRIVING
            }, (result, status) => {
                if (status === 'OK') directionsRenderer.setDirections(result);
            });

            let orderList = document.getElementById('optimizedOrder');
            orderList.innerHTML = '';
            currentRouteOrder = [];
            data.waypoints.slice(1).forEach((wp, idx) => {
                let shipment = selectedShipments.find(s => Math.abs(s.lat - wp.lat) < 0.001 && Math.abs(s.lng - wp
                    .lng) < 0.001);
                if (shipment) {
                    currentRouteOrder.push(shipment.id);
                    let div = document.createElement('div');
                    div.className = 'order-item';
                    div.dataset.id = shipment.id;
                    div.innerHTML =
                        `<span class="order-number">${idx + 1}</span><div class="order-content"><div class="order-customer">${shipment.customer}</div><div class="order-address">${shipment.city}</div></div>`;
                    orderList.appendChild(div);
                }
            });
            document.getElementById('routeInfo').style.display = 'block';
            document.getElementById('totalDistance').innerHTML = data.total_distance.toFixed(1) + ' <small>km</small>';
            document.getElementById('totalTime').innerHTML = Math.round(data.total_duration) + ' <small>mins</small>';
            document.getElementById('totalStops').innerHTML = data.waypoints.length - 1;
            document.getElementById('assignRouteBtn').disabled = false;
            if (sortable) sortable.destroy();
            sortable = new Sortable(orderList, {
                animation: 150,
                onEnd: () => updateOrderFromManual()
            });
        }

        function updateOrderFromManual() {
            let items = document.querySelectorAll('#optimizedOrder .order-item');
            currentRouteOrder = [];
            items.forEach((item, idx) => {
                item.querySelector('.order-number').textContent = idx + 1;
                currentRouteOrder.push(item.dataset.id);
            });
            recalculateRoute();
        }

        function recalculateRoute() {
            let waypoints = currentRouteOrder.map(id => {
                let s = selectedShipments.find(s => s.id == id);
                return {
                    location: {
                        latLng: {
                            latitude: s.lat,
                            longitude: s.lng
                        }
                    }
                };
            });
            directionsService.route({
                origin: WAREHOUSE,
                destination: {
                    lat: selectedShipments.find(s => s.id == currentRouteOrder[currentRouteOrder.length - 1]).lat,
                    lng: selectedShipments.find(s => s.id == currentRouteOrder[currentRouteOrder.length - 1]).lng
                },
                waypoints: waypoints,
                travelMode: google.maps.TravelMode.DRIVING
            }, (result, status) => {
                if (status === 'OK') directionsRenderer.setDirections(result);
            });
        }

        function openAssignModal() {
            if (selectedShipments.length === 0) {
                showToast('No shipments selected', 'error');
                return;
            }
            document.getElementById('assignShipmentIds').value = JSON.stringify(selectedShipments.map(s => s.id));
            document.getElementById('assignRouteOrder').value = JSON.stringify(currentRouteOrder);
            document.getElementById('assignModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('assignModal').style.display = 'none';
        }

        async function assignRoute() {
            let formData = new FormData(document.getElementById('assignForm'));
            showLoading();
            try {
                let response = await fetch('{{ route('logistics.route.assign') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });
                let data = await response.json();
                hideLoading();
                closeModal();
                if (data.success) {
                    showToast('✅ Route assigned successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.message || 'Error assigning route', 'error');
                }
            } catch (error) {
                hideLoading();
                showToast('Failed to assign route', 'error');
            }
        }

        function centerMap() {
            map.setCenter(WAREHOUSE);
            map.setZoom(12);
        }

        function zoomIn() {
            map.setZoom(map.getZoom() + 1);
        }

        function zoomOut() {
            map.setZoom(map.getZoom() - 1);
        }

        function toggleRouteInfo() {
            let info = document.getElementById('routeInfo');
            info.style.display = info.style.display === 'none' ? 'block' : 'none';
        }

        function resetMap() {
            if (directionsRenderer) directionsRenderer.setDirections({
                routes: []
            });
            document.querySelectorAll('.shipment-checkbox:checked').forEach(cb => cb.checked = false);
            document.getElementById('optimizedOrder').innerHTML =
                `<div class="empty-order"><i class="fas fa-route"></i><p>Select shipments and click "Optimize Route" to see the best order</p></div>`;
            document.getElementById('routeInfo').style.display = 'none';
            document.getElementById('assignRouteBtn').disabled = true;
            document.getElementById('optimizeRouteBtn').disabled = true;
            updateSelectedShipments();
            showToast('Map reset', 'info');
            if (sortable) sortable.destroy();
        }

        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        function showToast(msg, type = 'success') {
            let toast = document.getElementById('toast');
            toast.innerHTML =
                `<div class="toast-content"><span class="toast-icon">${type === 'success' ? '✅' : type === 'error' ? '❌' : '⚠️'}</span><span class="toast-message">${msg}</span></div>`;
            toast.className = 'toast ' + type;
            toast.style.display = 'block';
            setTimeout(() => toast.style.display = 'none', 3000);
        }

        window.onclick = e => {
            if (e.target === document.getElementById('assignModal')) closeModal();
        };
        window.initMap = initMap;
        window.centerMap = centerMap;
        window.zoomIn = zoomIn;
        window.zoomOut = zoomOut;
        window.toggleRouteInfo = toggleRouteInfo;
        window.resetMap = resetMap;
        window.closeModal = closeModal;
    </script>
@endsection
