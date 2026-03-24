{{-- resources/views/logistics/shipments/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Shipment #' . $shipment->shipment_number)

@section('content')
    <style>
        /* ================= PROFESSIONAL EDIT SHIPMENT STYLES ================= */
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

        .edit-page {
            min-height: 100vh;
            padding: clamp(16px, 3vw, 30px);
            width: 100%;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .edit-card {
            background: #ffffff;
            border-radius: 30px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            width: 100%;
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

        .edit-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            padding: clamp(1.5rem, 4vw, 2rem);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .edit-header::before {
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
            flex: 1;
            min-width: 280px;
        }

        .header-title {
            font-size: clamp(1.5rem, 5vw, 2rem);
            font-weight: 700;
            margin: 0 0 0.5rem 0;
            line-height: 1.2;
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1.25rem;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(4px);
            white-space: nowrap;
        }

        .header-subtitle {
            opacity: 0.9;
            font-size: clamp(0.9rem, 2.5vw, 1rem);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
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
            font-size: clamp(0.9rem, 2vw, 1rem);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
        }

        .header-btn:hover {
            background: white;
            color: #1e293b;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .form-section {
            padding: clamp(1.5rem, 4vw, 2rem);
            border-bottom: 1px solid #e5e7eb;
            background: white;
            transition: all 0.3s ease;
            position: relative;
        }

        .form-section:hover {
            background: #f8fafc;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .section-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .section-subtitle {
            color: #64748b;
            font-size: 0.9rem;
            margin: 0.25rem 0 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
            position: relative;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-label i {
            color: #667eea;
        }

        .required-star {
            color: #ef4444;
            margin-left: 0.25rem;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            font-size: 0.95rem;
            color: #1e293b;
            background: white;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-control[readonly] {
            background: #f1f5f9;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
        }

        .dimensions-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .dimension-item {
            position: relative;
        }

        .dimension-item .form-control {
            padding-right: 2.5rem;
        }

        .dimension-unit {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 0.85rem;
            background: #f1f5f9;
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
        }

        .input-hint {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        .info-card {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-radius: 20px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: white;
            padding: 0.75rem 1.25rem;
            border-radius: 12px;
            flex: 1;
            min-width: 200px;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .info-label {
            font-size: 0.8rem;
            color: #64748b;
            text-transform: uppercase;
        }

        .info-value {
            font-weight: 700;
            color: #1e293b;
        }

        .agent-section {
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            border: 1px solid #bae6fd;
        }

        .agent-stats {
            display: flex;
            gap: 1rem;
            margin-top: 0.75rem;
            flex-wrap: wrap;
        }

        .agent-stat-badge {
            background: white;
            padding: 0.5rem 1rem;
            border-radius: 30px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .agent-list-preview {
            margin-top: 1rem;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: white;
        }

        .agent-preview-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .agent-preview-item:hover {
            background: #f8fafc;
        }

        .agent-status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .agent-status-dot.available {
            background: #10b981;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
        }

        .agent-status-dot.busy {
            background: #f59e0b;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2);
        }

        .agent-status-dot.offline {
            background: #6b7280;
            box-shadow: 0 0 0 2px rgba(107, 114, 128, 0.2);
        }

        .agent-preview-name {
            font-weight: 600;
            color: #1e293b;
        }

        .agent-preview-city {
            font-size: 0.8rem;
            color: #64748b;
            margin-left: auto;
        }

        .address-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 2px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 12px 12px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .address-suggestions.show {
            display: block;
        }

        .suggestion-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            border-bottom: 1px solid #f1f5f9;
        }

        .suggestion-item:hover {
            background: #f8fafc;
            padding-left: 1.5rem;
        }

        .address-preview {
            margin-top: 1rem;
            padding: 1rem;
            background: #e8f0fe;
            border-radius: 12px;
            border-left: 4px solid #667eea;
            display: none;
        }

        .address-preview.show {
            display: block;
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
            white-space: nowrap;
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

        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 2px solid #e5e7eb;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
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

        @media (max-width: 768px) {
            .edit-page {
                padding: 1rem;
            }

            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .dimensions-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .info-card {
                flex-direction: column;
            }

            .info-item {
                width: 100%;
            }

            .agent-stats {
                flex-direction: column;
            }
        }
    </style>

    <div class="edit-page">
        <div id="loadingOverlay" class="loading-overlay">
            <div class="spinner"></div>
            <div class="loading-text">Saving changes...</div>
        </div>

        <div class="edit-card">
            <div class="edit-header">
                <div class="header-content">
                    <div class="header-left">
                        <h1 class="header-title">
                            Edit Shipment #{{ $shipment->shipment_number }}
                            <span class="status-badge">{{ strtoupper(str_replace('_', ' ', $shipment->status)) }}</span>
                        </h1>
                        <div class="header-subtitle">
                            <span><i class="fas fa-calendar"></i> Created:
                                {{ $shipment->created_at->format('d M Y, h:i A') }}</span>
                            <span>•</span>
                            <span><i class="fas fa-sync-alt"></i> Last updated:
                                {{ $shipment->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('logistics.shipments.show', $shipment->id) }}" class="header-btn">
                            <i class="fas fa-arrow-left"></i> Back to Details
                        </a>
                    </div>
                </div>
            </div>

            {{-- Info Card --}}
            @if ($shipment->sale_id)
                <div class="form-section" style="padding-bottom: 0;">
                    <div class="info-card">
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-file-invoice"></i></div>
                            <div>
                                <div class="info-label">Linked Invoice</div>
                                <div class="info-value">#{{ $shipment->sale->invoice_no ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-user"></i></div>
                            <div>
                                <div class="info-label">Customer</div>
                                <div class="info-value">{{ $shipment->customer->name ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-phone"></i></div>
                            <div>
                                <div class="info-label">Contact</div>
                                <div class="info-value">{{ $shipment->customer->mobile ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('logistics.shipments.update', $shipment->id) }}" id="editForm">
                @csrf
                @method('PUT')

                {{-- Receiver Information --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-user"></i></div>
                        <div>
                            <h3 class="section-title">Receiver Information</h3>
                            <p class="section-subtitle">Update receiver's contact details</p>
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-user"></i> Receiver Name <span
                                    class="required-star">*</span></label>
                            <input type="text" name="receiver_name" class="form-control"
                                value="{{ old('receiver_name', $shipment->receiver_name) }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-phone"></i> Phone Number <span
                                    class="required-star">*</span></label>
                            <input type="tel" name="receiver_phone" class="form-control"
                                value="{{ old('receiver_phone', $shipment->receiver_phone) }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-phone-alt"></i> Alternate Phone</label>
                            <input type="tel" name="receiver_alternate_phone" class="form-control"
                                value="{{ old('receiver_alternate_phone', $shipment->receiver_alternate_phone) }}">
                        </div>
                    </div>
                </div>

                {{-- Shipping Address with Google Places --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <h3 class="section-title">Shipping Address</h3>
                            <p class="section-subtitle">Update delivery address</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-search"></i> Search Address</label>
                        <div style="position: relative;">
                            <input type="text" id="address-input" class="form-control"
                                placeholder="Search for address..." autocomplete="off"
                                value="{{ $shipment->shipping_address }}">
                            <div id="address-suggestions" class="address-suggestions"></div>
                        </div>
                        <div class="input-hint"><i class="fas fa-info-circle"></i> Type to search, select from suggestions
                        </div>
                    </div>

                    <div id="address-preview" class="address-preview">
                        <i class="fas fa-check-circle" style="color: #10b981;"></i>
                        <span id="preview-text"></span>
                    </div>

                    <div class="form-grid">
                        <div class="form-group" style="grid-column: 1/-1;">
                            <label class="form-label"><i class="fas fa-map-pin"></i> Full Address <span
                                    class="required-star">*</span></label>
                            <textarea name="shipping_address" class="form-control" id="shipping_address" required>{{ old('shipping_address', $shipment->shipping_address) }}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Landmark</label>
                            <input type="text" name="landmark" class="form-control"
                                value="{{ old('landmark', $shipment->landmark) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">City <span class="required-star">*</span></label>
                            <input type="text" name="city" class="form-control" id="city"
                                value="{{ old('city', $shipment->city) }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">State <span class="required-star">*</span></label>
                            <input type="text" name="state" class="form-control" id="state"
                                value="{{ old('state', $shipment->state) }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Pincode <span class="required-star">*</span></label>
                            <input type="text" name="pincode" class="form-control" id="pincode"
                                value="{{ old('pincode', $shipment->pincode) }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" class="form-control" id="country"
                                value="{{ old('country', $shipment->country ?? 'India') }}">
                        </div>
                    </div>
                    <input type="hidden" name="latitude" id="latitude" value="{{ $shipment->destination_latitude }}">
                    <input type="hidden" name="longitude" id="longitude"
                        value="{{ $shipment->destination_longitude }}">
                    <input type="hidden" name="place_id" id="place_id">
                </div>

                {{-- Package Details --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-box"></i></div>
                        <div>
                            <h3 class="section-title">Package Details</h3>
                            <p class="section-subtitle">Update package dimensions and value</p>
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Weight (kg)</label>
                            <input type="number" step="0.01" name="weight" class="form-control" id="weight"
                                value="{{ old('weight', $shipment->weight) }}" placeholder="0.00">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Package Type</label>
                            <select name="package_type" class="form-control">
                                <option value="">Select Type</option>
                                <option value="box"
                                    {{ old('package_type', $shipment->package_type) == 'box' ? 'selected' : '' }}>📦 Box
                                </option>
                                <option value="envelope"
                                    {{ old('package_type', $shipment->package_type) == 'envelope' ? 'selected' : '' }}>✉️
                                    Envelope</option>
                                <option value="pallet"
                                    {{ old('package_type', $shipment->package_type) == 'pallet' ? 'selected' : '' }}>📏
                                    Pallet</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" min="1" id="quantity"
                                value="{{ old('quantity', $shipment->quantity ?? 1) }}">
                        </div>
                    </div>
                    <div class="dimensions-grid">
                        <div class="dimension-item">
                            <label class="form-label">Length (cm)</label>
                            <input type="number" step="0.1" name="length" class="form-control"
                                value="{{ old('length', $shipment->length) }}">
                            <span class="dimension-unit">cm</span>
                        </div>
                        <div class="dimension-item">
                            <label class="form-label">Width (cm)</label>
                            <input type="number" step="0.1" name="width" class="form-control"
                                value="{{ old('width', $shipment->width) }}">
                            <span class="dimension-unit">cm</span>
                        </div>
                        <div class="dimension-item">
                            <label class="form-label">Height (cm)</label>
                            <input type="number" step="0.1" name="height" class="form-control"
                                value="{{ old('height', $shipment->height) }}">
                            <span class="dimension-unit">cm</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-rupee-sign"></i> Declared Value (₹)</label>
                        <input type="number" step="0.01" name="declared_value" class="form-control"
                            id="declaredValue" value="{{ old('declared_value', $shipment->declared_value) }}">
                    </div>
                </div>

                {{-- Courier Information --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-truck"></i></div>
                        <div>
                            <h3 class="section-title">Courier Information</h3>
                            <p class="section-subtitle">Update courier and tracking details</p>
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Courier Partner</label>
                            <select name="courier_partner" class="form-control" id="courierPartner">
                                <option value="">Select Courier</option>
                                <option value="Delhivery"
                                    {{ old('courier_partner', $shipment->courier_partner) == 'Delhivery' ? 'selected' : '' }}>
                                    🚚 Delhivery</option>
                                <option value="BlueDart"
                                    {{ old('courier_partner', $shipment->courier_partner) == 'BlueDart' ? 'selected' : '' }}>
                                    ✈️ BlueDart</option>
                                <option value="DTDC"
                                    {{ old('courier_partner', $shipment->courier_partner) == 'DTDC' ? 'selected' : '' }}>🚛
                                    DTDC</option>
                                <option value="FedEx"
                                    {{ old('courier_partner', $shipment->courier_partner) == 'FedEx' ? 'selected' : '' }}>
                                    📦 FedEx</option>
                                <option value="Ekart"
                                    {{ old('courier_partner', $shipment->courier_partner) == 'Ekart' ? 'selected' : '' }}>
                                    🛵 Ekart</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tracking Number</label>
                            <input type="text" name="tracking_number" class="form-control"
                                value="{{ old('tracking_number', $shipment->tracking_number) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">AWB Number</label>
                            <input type="text" name="awb_number" class="form-control"
                                value="{{ old('awb_number', $shipment->awb_number) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Shipping Method</label>
                            <select name="shipping_method" class="form-control" id="shippingMethod">
                                <option value="standard"
                                    {{ old('shipping_method', $shipment->shipping_method) == 'standard' ? 'selected' : '' }}>
                                    🚚 Standard (3-5 days)</option>
                                <option value="express"
                                    {{ old('shipping_method', $shipment->shipping_method) == 'express' ? 'selected' : '' }}>
                                    ⚡ Express (1-2 days)</option>
                                <option value="overnight"
                                    {{ old('shipping_method', $shipment->shipping_method) == 'overnight' ? 'selected' : '' }}>
                                    🌙 Overnight</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Delivery Agent Section --}}
                <div class="form-section agent-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-user-tie"></i></div>
                        <div>
                            <h3 class="section-title">Delivery Agent Assignment</h3>
                            <p class="section-subtitle">Assign or change delivery agent</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-user-check"></i> Assigned Agent</label>
                        <select name="agent_id" class="form-control" id="agentSelect">
                            <option value="">-- Unassigned (No Agent) --</option>
                            @php
                                $agents = App\Models\User::where('role', 'delivery_agent')
                                    ->where('status', 'active')
                                    ->get();
                            @endphp
                            @php $__col = $agents; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $agent)
                                <option value="{{ $agent->id }}"
                                    {{ old('agent_id', $shipment->assigned_to) == $agent->id ? 'selected' : '' }}
                                    data-lat="{{ $agent->current_latitude ?? '' }}"
                                    data-lng="{{ $agent->current_longitude ?? '' }}" data-phone="{{ $agent->mobile }}"
                                    data-city="{{ $agent->city ?? '' }}"
                                    data-deliveries="{{ $agent->total_deliveries ?? 0 }}"
                                    data-rating="{{ $agent->rating ?? 4.5 }}"
                                    data-status="{{ $agent->status ?? 'available' }}">
                                    {{ $agent->name }} @if ($agent->city)
                                        - {{ $agent->city }}
                                    @endif
                                    @if (isset($agent->status) && $agent->status == 'available')
                                        ✅ Available
                                    @elseif(isset($agent->status) && $agent->status == 'busy')
                                        🔴 Busy
                                    @else
                                        ⭕ Offline
                                    @endif
                                </option>
                            @endforeach
@else
                                <option value="" disabled>No agents available</option>
                            @endif
                        </select>

                        @if (isset($agents) && $agents->count() > 0)
                            <div class="agent-stats">
                                <span class="agent-stat-badge"><i class="fas fa-users"></i> Total:
                                    {{ $agents->count() }}</span>
                                <span class="agent-stat-badge"><i class="fas fa-check-circle" style="color:#10b981;"></i>
                                    Available: {{ $agents->where('status', 'available')->count() }}</span>
                                <span class="agent-stat-badge"><i class="fas fa-truck" style="color:#f59e0b;"></i> Busy:
                                    {{ $agents->where('status', 'busy')->count() }}</span>
                            </div>
                            <div class="agent-list-preview" id="agentListPreview">
                                @foreach ($agents->take(5) as $agent)
                                    <div class="agent-preview-item" onclick="selectAgent({{ $agent->id }})">
                                        <span class="agent-status-dot {{ $agent->status ?? 'offline' }}"></span>
                                        <span class="agent-preview-name">{{ $agent->name }}</span>
                                        <span class="agent-preview-city">{{ $agent->city ?? 'N/A' }}</span>
                                        <span style="font-size:0.8rem; color:#667eea;">{{ $agent->total_deliveries ?? 0 }}
                                            deliveries</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div id="agentInfoCard"
                            style="display: none; margin-top: 1rem; background: white; border-radius: 12px; padding: 1rem; border: 1px solid #e5e7eb;">
                            <div style="display: flex; gap: 1rem; align-items: center;">
                                <div
                                    style="width: 50px; height: 50px; background: linear-gradient(135deg,#667eea,#764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                                    <i class="fas fa-motorcycle"></i></div>
                                <div>
                                    <div><strong id="selectedAgentName">-</strong></div>
                                    <div style="font-size:0.85rem; color:#64748b;"><span
                                            id="selectedAgentDistance">-</span> away</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Delivery Dates --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-calendar-alt"></i></div>
                        <div>
                            <h3 class="section-title">Delivery Dates</h3>
                            <p class="section-subtitle">Update pickup and delivery dates</p>
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Pickup Date</label>
                            <input type="date" name="pickup_date" class="form-control"
                                value="{{ old('pickup_date', $shipment->pickup_date ? $shipment->pickup_date->format('Y-m-d') : '') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Estimated Delivery Date</label>
                            <input type="date" name="estimated_delivery_date" class="form-control" id="estimatedDate"
                                value="{{ old('estimated_delivery_date', $shipment->estimated_delivery_date ? $shipment->estimated_delivery_date->format('Y-m-d') : '') }}">
                        </div>
                        @if ($shipment->status == 'delivered' && $shipment->actual_delivery_date)
                            <div class="form-group">
                                <label class="form-label">Actual Delivery Date</label>
                                <input type="text" class="form-control" readonly
                                    value="{{ $shipment->actual_delivery_date->format('d M Y h:i A') }}">
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Delivery Status --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-chart-line"></i></div>
                        <div>
                            <h3 class="section-title">Delivery Status</h3>
                            <p class="section-subtitle">Update current status</p>
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Current Status</label>
                            <select name="status" class="form-control" id="statusSelect">
                                <option value="pending"
                                    {{ old('status', $shipment->status) == 'pending' ? 'selected' : '' }}>⏳ Pending
                                </option>
                                <option value="picked"
                                    {{ old('status', $shipment->status) == 'picked' ? 'selected' : '' }}>📦 Picked Up
                                </option>
                                <option value="in_transit"
                                    {{ old('status', $shipment->status) == 'in_transit' ? 'selected' : '' }}>🚚 In Transit
                                </option>
                                <option value="out_for_delivery"
                                    {{ old('status', $shipment->status) == 'out_for_delivery' ? 'selected' : '' }}>🚀 Out
                                    for Delivery</option>
                                <option value="delivered"
                                    {{ old('status', $shipment->status) == 'delivered' ? 'selected' : '' }}>✅ Delivered
                                </option>
                                <option value="failed"
                                    {{ old('status', $shipment->status) == 'failed' ? 'selected' : '' }}>❌ Failed</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status Note</label>
                            <textarea name="status_note" class="form-control" placeholder="Additional notes">{{ old('status_note', $shipment->status_note) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Charges --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-coins"></i></div>
                        <div>
                            <h3 class="section-title">Shipping Charges</h3>
                            <p class="section-subtitle">Update charges</p>
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Shipping Charge (₹)</label>
                            <input type="number" name="shipping_charge" class="form-control" step="0.01"
                                id="shippingCharge" value="{{ old('shipping_charge', $shipment->shipping_charge) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">COD Charge (₹)</label>
                            <input type="number" name="cod_charge" class="form-control" step="0.01" id="codCharge"
                                value="{{ old('cod_charge', $shipment->cod_charge) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Insurance Charge (₹)</label>
                            <input type="number" name="insurance_charge" class="form-control" step="0.01"
                                id="insuranceCharge" value="{{ old('insurance_charge', $shipment->insurance_charge) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Payment Mode</label>
                            <select name="payment_mode" class="form-control" id="paymentMode">
                                <option value="prepaid"
                                    {{ old('payment_mode', $shipment->payment_mode) == 'prepaid' ? 'selected' : '' }}>💳
                                    Prepaid</option>
                                <option value="cod"
                                    {{ old('payment_mode', $shipment->payment_mode) == 'cod' ? 'selected' : '' }}>💵 Cash
                                    on Delivery</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Total Charge (₹)</label>
                        <input type="number" name="total_charge" class="form-control" step="0.01" readonly
                            id="totalCharge" value="{{ old('total_charge', $shipment->total_charge) }}"
                            style="background:#f1f5f9; font-weight:700;">
                    </div>
                </div>

                <div class="form-section">
                    <div class="action-buttons">
                        <a href="{{ route('logistics.shipments.show', $shipment->id) }}" class="btn btn-secondary"><i
                                class="fas fa-times"></i> Cancel</a>
                        <button type="submit" class="btn btn-primary" id="submitBtn"><i class="fas fa-save"></i> Save
                            Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initGooglePlaces"
        async defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

    <script>
        let autocompleteService, placesService;

        function initGooglePlaces() {
            autocompleteService = new google.maps.places.AutocompleteService();
            placesService = new google.maps.places.PlacesService(document.createElement('div'));
            setupAddressAutocomplete();
        }

        function setupAddressAutocomplete() {
            const addressInput = document.getElementById('address-input');
            const suggestionsDiv = document.getElementById('address-suggestions');
            if (!addressInput) return;

            let debounceTimer;
            addressInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const query = this.value.trim();
                if (query.length < 3) {
                    suggestionsDiv.classList.remove('show');
                    return;
                }
                debounceTimer = setTimeout(() => {
                    autocompleteService.getPlacePredictions({
                        input: query,
                        types: ['address'],
                        componentRestrictions: {
                            country: 'IN'
                        }
                    }, (predictions, status) => {
                        if (status === google.maps.places.PlacesServiceStatus.OK && predictions) {
                            displaySuggestions(predictions);
                        } else {
                            suggestionsDiv.classList.remove('show');
                        }
                    });
                }, 300);
            });

            document.addEventListener('click', function(e) {
                if (!addressInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                    suggestionsDiv.classList.remove('show');
                }
            });
        }

        function displaySuggestions(predictions) {
            const suggestionsDiv = document.getElementById('address-suggestions');
            suggestionsDiv.innerHTML = '';
            predictions.forEach(prediction => {
                const item = document.createElement('div');
                item.className = 'suggestion-item';
                item.innerHTML =
                    `<div class="suggestion-main">${prediction.structured_formatting.main_text}</div>
                              <div class="suggestion-secondary">${prediction.structured_formatting.secondary_text}</div>`;
                item.addEventListener('click', () => {
                    selectPlace(prediction.place_id);
                    suggestionsDiv.classList.remove('show');
                    document.getElementById('address-input').value = prediction.description;
                });
                suggestionsDiv.appendChild(item);
            });
            suggestionsDiv.classList.add('show');
        }

        function selectPlace(placeId) {
            placesService.getDetails({
                placeId: placeId,
                fields: ['address_components', 'formatted_address', 'geometry']
            }, (place, status) => {
                if (status === google.maps.places.PlacesServiceStatus.OK) {
                    const components = {};
                    place.address_components.forEach(component => {
                        components[component.types[0]] = component;
                    });

                    document.getElementById('shipping_address').value = place.formatted_address;
                    document.getElementById('latitude').value = place.geometry.location.lat();
                    document.getElementById('longitude').value = place.geometry.location.lng();

                    if (components.locality) document.getElementById('city').value = components.locality.long_name;
                    if (components.administrative_area_level_1) document.getElementById('state').value = components
                        .administrative_area_level_1.long_name;
                    if (components.postal_code) document.getElementById('pincode').value = components.postal_code
                        .long_name;
                    if (components.country) document.getElementById('country').value = components.country.long_name;

                    document.getElementById('preview-text').textContent = place.formatted_address;
                    document.getElementById('address-preview').classList.add('show');
                    updateAgentDistance(place.geometry.location.lat(), place.geometry.location.lng());
                }
            });
        }

        function updateAgentDistance(shipmentLat, shipmentLng) {
            const agentSelect = document.getElementById('agentSelect');
            const selectedOption = agentSelect.options[agentSelect.selectedIndex];
            const agentLat = parseFloat(selectedOption?.dataset?.lat);
            const agentLng = parseFloat(selectedOption?.dataset?.lng);
            if (agentLat && agentLng && shipmentLat && shipmentLng) {
                const distance = calculateDistance(shipmentLat, shipmentLng, agentLat, agentLng);
                document.getElementById('selectedAgentDistance').innerHTML = distance.toFixed(2) + ' km';
                document.getElementById('agentInfoCard').style.display = 'block';
                document.getElementById('selectedAgentName').innerHTML = selectedOption.text.split(' - ')[0];
            }
        }

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) ** 2 + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(
                dLon / 2) ** 2;
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        }

        function selectAgent(agentId) {
            document.getElementById('agentSelect').value = agentId;
            const lat = parseFloat(document.getElementById('latitude').value);
            const lng = parseFloat(document.getElementById('longitude').value);
            if (lat && lng) updateAgentDistance(lat, lng);
            showToast('Agent selected', 'success');
        }

        function calculateTotal() {
            const shipping = parseFloat(document.getElementById('shippingCharge').value) || 0;
            const cod = parseFloat(document.getElementById('codCharge').value) || 0;
            const insurance = parseFloat(document.getElementById('insuranceCharge').value) || 0;
            document.getElementById('totalCharge').value = (shipping + cod + insurance).toFixed(2);
        }

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.innerHTML =
                `<div class="toast-content"><span>${type === 'success' ? '✅' : '❌'}</span><span>${message}</span></div>`;
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

        document.getElementById('shippingCharge').addEventListener('input', calculateTotal);
        document.getElementById('codCharge').addEventListener('input', calculateTotal);
        document.getElementById('insuranceCharge').addEventListener('input', calculateTotal);
        document.getElementById('shippingMethod').addEventListener('change', function() {
            const date = new Date();
            const days = this.value === 'standard' ? 3 : 1;
            date.setDate(date.getDate() + days);
            document.getElementById('estimatedDate').value = date.toISOString().split('T')[0];
        });
        document.getElementById('agentSelect').addEventListener('change', function() {
            const lat = parseFloat(document.getElementById('latitude').value);
            const lng = parseFloat(document.getElementById('longitude').value);
            if (lat && lng) updateAgentDistance(lat, lng);
        });

        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const required = ['receiver_name', 'receiver_phone', 'shipping_address', 'city', 'state', 'pincode'];
            for (let field of required) {
                if (!document.querySelector(`[name="${field}"]`).value.trim()) {
                    showToast(`Please fill ${field.replace('_', ' ')}`, 'error');
                    return;
                }
            }
            if (!/^[0-9]{10}$/.test(document.querySelector('[name="receiver_phone"]').value.trim())) {
                showToast('Please enter valid 10-digit phone number', 'error');
                return;
            }
            if (!confirm('Save changes to this shipment?')) return;
            showLoading();
            fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: new FormData(this)
                })
                .then(r => r.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        showToast('✅ ' + data.message, 'success');
                        setTimeout(() => window.location.href =
                            '{{ route('logistics.shipments.show', $shipment->id) }}', 1500);
                    } else showToast('❌ ' + (data.message || 'Error updating'), 'error');
                })
                .catch(e => {
                    hideLoading();
                    showToast('❌ Network error', 'error');
                });
        });

        calculateTotal();
        window.initGooglePlaces = initGooglePlaces;
    </script>
@endsection
