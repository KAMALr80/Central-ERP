{{-- resources/views/logistics/shipments/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create New Shipment')

@section('content')
    <style>
        /* ================= PROFESSIONAL CREATE SHIPMENT STYLES ================= */
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

        .create-shipment-page {
            padding: 2rem 1.5rem;
            max-width: 1400px;
            margin: 0 auto;
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
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
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

        /* Main Card */
        .shipment-card {
            background: white;
            border-radius: 30px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1.5rem 2rem;
            border-bottom: 3px solid #667eea;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .card-header-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .card-header-text h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
        }

        .card-header-text p {
            margin: 0.25rem 0 0;
            color: #64748b;
            font-size: 0.9rem;
        }

        /* Form Container */
        .form-container {
            padding: 2rem;
        }

        /* Form Sections */
        .form-section {
            background: #f8fafc;
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .form-section:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border-color: #667eea;
        }

        .form-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 4px 0 0 4px;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .section-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }

        .section-subtitle {
            color: #64748b;
            font-size: 0.9rem;
            margin: 0.25rem 0 0;
        }

        /* Form Grid */
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
            font-size: 1rem;
        }

        .required-star {
            color: #ef4444;
            margin-left: 0.25rem;
            font-size: 1.2rem;
            line-height: 1;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
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

        .form-control:hover {
            border-color: #94a3b8;
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
            background-size: 1rem;
        }

        /* Dimensions Grid */
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
            font-weight: 500;
            background: #f1f5f9;
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
        }

        /* Courier Options */
        .courier-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 0.75rem;
            margin-top: 0.5rem;
        }

        .courier-option {
            position: relative;
        }

        .courier-option input[type="radio"] {
            display: none;
        }

        .courier-option label {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .courier-option label:hover {
            border-color: #667eea;
            background: #f8fafc;
            transform: translateY(-2px);
        }

        .courier-option input[type="radio"]:checked+label {
            border-color: #667eea;
            background: linear-gradient(135deg, #667eea10, #764ba210);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .courier-icon {
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
        }

        /* Location Detection */
        .location-detect-btn {
            position: absolute;
            right: 10px;
            top: 35px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            z-index: 10;
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }

        .location-detect-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(102, 126, 234, 0.4);
        }

        .location-detect-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .location-status {
            font-size: 0.85rem;
            margin-top: 0.5rem;
            padding: 0.5rem;
            border-radius: 8px;
            display: none;
        }

        .location-status.success {
            background: #d1fae5;
            color: #065f46;
            display: block;
        }

        .location-status.error {
            background: #fee2e2;
            color: #991b1b;
            display: block;
        }

        .location-status.loading {
            background: #dbeafe;
            color: #1e40af;
            display: block;
        }

        /* Address Suggestions */
        .address-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 2px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 12px 12px;
            max-height: 250px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
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

        .suggestion-main {
            font-weight: 500;
            color: #1e293b;
        }

        .suggestion-secondary {
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 0.25rem;
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

        /* Agent Section */
        .agent-select-container {
            margin-top: 1rem;
        }

        .agent-map-preview {
            height: 300px;
            border-radius: 12px;
            margin-top: 1rem;
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .agent-info-card {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            margin-top: 1rem;
            border: 2px solid #e5e7eb;
            display: none;
        }

        .agent-info-card.show {
            display: block;
            animation: slideIn 0.3s ease;
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

        .agent-info-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .agent-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .agent-details {
            flex: 1;
        }

        .agent-name {
            font-weight: 600;
            font-size: 1.1rem;
            color: #1e293b;
        }

        .agent-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.25rem;
        }

        .status-available {
            background: #d1fae5;
            color: #065f46;
        }

        .status-busy {
            background: #fee2e2;
            color: #991b1b;
        }

        .agent-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .agent-stat-item {
            text-align: center;
            padding: 0.5rem;
            background: #f8fafc;
            border-radius: 8px;
        }

        .agent-stat-label {
            font-size: 0.7rem;
            color: #64748b;
        }

        .agent-stat-value {
            font-weight: 600;
            color: #1e293b;
        }

        /* Price Preview */
        .price-preview {
            background: linear-gradient(135deg, #f8fafc, #e9ecef);
            border-radius: 20px;
            padding: 1.5rem;
            margin-top: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .price-item {
            background: white;
            padding: 1rem;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .price-label {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .price-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
        }

        .price-value.total {
            color: #667eea;
            font-size: 1.75rem;
        }

        /* Submit Section */
        .submit-section {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px dashed #e5e7eb;
        }

        .btn {
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
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

        /* Toast */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border-left: 4px solid;
            z-index: 9999;
            display: none;
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

        .spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .create-shipment-page {
                padding: 1rem;
            }

            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .header-text h1 {
                font-size: 2rem;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .dimensions-grid {
                grid-template-columns: 1fr;
            }

            .submit-section {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .courier-options {
                grid-template-columns: 1fr;
            }

            .agent-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="create-shipment-page">
        {{-- Page Header --}}
        <div class="page-header">
            <div class="header-content">
                <div class="header-icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="header-text">
                    <h1>Create New Shipment</h1>
                    <p>Fill in the details below to create a new shipment</p>
                </div>
            </div>
        </div>

        {{-- Main Card --}}
        <div class="shipment-card">
            <div class="card-header">
                <div class="card-header-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="card-header-text">
                    <h2>Shipment Information</h2>
                    <p>Please provide accurate shipping details</p>
                </div>
            </div>

            <div class="form-container">
                <form method="POST" action="{{ route('logistics.shipments.store') }}" id="shipmentForm">
                    @csrf

                    {{-- Receiver Details Section --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-user"></i></div>
                            <div>
                                <h3 class="section-title">Receiver Details</h3>
                                <p class="section-subtitle">Enter the recipient's information</p>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user"></i> Receiver Name <span class="required-star">*</span>
                                </label>
                                <input type="text" name="receiver_name" class="form-control" required
                                    placeholder="Enter receiver's full name" value="{{ old('receiver_name') }}">
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-phone"></i> Phone Number <span class="required-star">*</span>
                                </label>
                                <input type="tel" name="receiver_phone" class="form-control" required
                                    placeholder="10 digit mobile number" value="{{ old('receiver_phone') }}">
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-phone-alt"></i> Alternate Phone
                                </label>
                                <input type="tel" name="receiver_alternate_phone" class="form-control"
                                    placeholder="Alternate contact number" value="{{ old('receiver_alternate_phone') }}">
                            </div>
                        </div>

                        {{-- Google Places Address Search --}}
                        <div class="form-group mt-3" style="position: relative;">
                            <label class="form-label">
                                <i class="fas fa-map-marker-alt"></i> Shipping Address <span class="required-star">*</span>
                            </label>
                            <div style="position: relative;">
                                <input type="text" id="address-input" class="form-control"
                                    placeholder="Start typing to search address or use current location" autocomplete="off"
                                    required>
                                <button type="button" id="detect-location" class="location-detect-btn">
                                    <i class="fas fa-location-dot"></i> Use My Location
                                </button>
                                <div id="address-suggestions" class="address-suggestions"></div>
                            </div>
                            <div id="location-status" class="location-status"></div>

                            <input type="hidden" name="shipping_address" id="shipping_address">
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">
                            <input type="hidden" name="place_id" id="place_id">
                            <input type="hidden" name="city" id="city">
                            <input type="hidden" name="state" id="state">
                            <input type="hidden" name="pincode" id="pincode">
                            <input type="hidden" name="country" id="country">
                        </div>

                        <div id="address-preview" class="address-preview">
                            <i class="fas fa-check-circle" style="color: #10b981; margin-right: 0.5rem;"></i>
                            <span id="preview-text"></span>
                        </div>

                        <div class="form-group mt-3">
                            <label class="form-label"><i class="fas fa-map-pin"></i> Landmark</label>
                            <input type="text" name="landmark" class="form-control"
                                placeholder="Nearby landmark (optional)" value="{{ old('landmark') }}">
                        </div>
                    </div>

                    {{-- Package Details Section --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-box"></i></div>
                            <div>
                                <h3 class="section-title">Package Details</h3>
                                <p class="section-subtitle">Specify package dimensions and value</p>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-weight"></i> Weight (kg)</label>
                                <input type="number" step="0.01" name="weight" class="form-control"
                                    placeholder="0.00" id="weight" value="{{ old('weight', 0) }}">
                            </div>

                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-box"></i> Quantity</label>
                                <input type="number" name="quantity" class="form-control"
                                    value="{{ old('quantity', 1) }}" min="1" id="quantity">
                            </div>

                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-tag"></i> Package Type</label>
                                <select name="package_type" class="form-control">
                                    <option value="box" {{ old('package_type') == 'box' ? 'selected' : '' }}>📦 Box
                                    </option>
                                    <option value="envelope" {{ old('package_type') == 'envelope' ? 'selected' : '' }}>✉️
                                        Envelope</option>
                                    <option value="pallet" {{ old('package_type') == 'pallet' ? 'selected' : '' }}>📏
                                        Pallet</option>
                                </select>
                            </div>
                        </div>

                        <div class="dimensions-grid">
                            <div class="dimension-item">
                                <label class="form-label">Length</label>
                                <input type="number" name="length" class="form-control" placeholder="0"
                                    id="length" value="{{ old('length') }}">
                                <span class="dimension-unit">cm</span>
                            </div>
                            <div class="dimension-item">
                                <label class="form-label">Width</label>
                                <input type="number" name="width" class="form-control" placeholder="0"
                                    id="width" value="{{ old('width') }}">
                                <span class="dimension-unit">cm</span>
                            </div>
                            <div class="dimension-item">
                                <label class="form-label">Height</label>
                                <input type="number" name="height" class="form-control" placeholder="0"
                                    id="height" value="{{ old('height') }}">
                                <span class="dimension-unit">cm</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-dollar-sign"></i> Declared Value <span
                                    class="required-star">*</span></label>
                            <input type="number" name="declared_value" class="form-control" required placeholder="0.00"
                                step="0.01" id="declaredValue" value="{{ old('declared_value', 0) }}">
                        </div>
                    </div>

                    {{-- Shipping Options Section --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-truck"></i></div>
                            <div>
                                <h3 class="section-title">Shipping Options</h3>
                                <p class="section-subtitle">Select courier and shipping method</p>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-truck"></i> Shipping Method <span
                                        class="required-star">*</span></label>
                                <select name="shipping_method" class="form-control" required id="shippingMethod">
                                    <option value="standard" {{ old('shipping_method') == 'standard' ? 'selected' : '' }}>
                                        🚚 Standard (3-5 days)</option>
                                    <option value="express" {{ old('shipping_method') == 'express' ? 'selected' : '' }}>⚡
                                        Express (1-2 days)</option>
                                    <option value="overnight"
                                        {{ old('shipping_method') == 'overnight' ? 'selected' : '' }}>🌙 Overnight</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-credit-card"></i> Payment Mode <span
                                        class="required-star">*</span></label>
                                <select name="payment_mode" class="form-control" required id="paymentMode">
                                    <option value="prepaid" {{ old('payment_mode') == 'prepaid' ? 'selected' : '' }}>💳
                                        Prepaid</option>
                                    <option value="cod" {{ old('payment_mode') == 'cod' ? 'selected' : '' }}>💵 Cash on
                                        Delivery</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-calendar"></i> Estimated Delivery Date</label>
                                <input type="date" name="estimated_delivery_date" class="form-control"
                                    min="{{ now()->addDay()->format('Y-m-d') }}"
                                    value="{{ old('estimated_delivery_date', now()->addDays(3)->format('Y-m-d')) }}"
                                    id="estimatedDate">
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <label class="form-label"><i class="fas fa-building"></i> Courier Partner <span
                                    class="required-star">*</span></label>
                            <div class="courier-options" id="courierOptions">
                                @php
                                    $couriers = [
                                        ['code' => 'DELHIVERY', 'name' => 'Delhivery', 'icon' => '🚚'],
                                        ['code' => 'BLUEDART', 'name' => 'BlueDart', 'icon' => '✈️'],
                                        ['code' => 'DTDC', 'name' => 'DTDC', 'icon' => '🚛'],
                                        ['code' => 'FEDEX', 'name' => 'FedEx', 'icon' => '📦'],
                                        ['code' => 'EKART', 'name' => 'Ekart', 'icon' => '🛵'],
                                        ['code' => 'XPRESSBEES', 'name' => 'XpressBees', 'icon' => '🐝'],
                                    ];
                                @endphp

                                @foreach ($couriers as $courier)
                                    <div class="courier-option">
                                        <input type="radio" name="courier_partner" value="{{ $courier['code'] }}"
                                            id="courier_{{ $courier['code'] }}"
                                            {{ old('courier_partner', 'DELHIVERY') == $courier['code'] ? 'checked' : '' }}>
                                        <label for="courier_{{ $courier['code'] }}">
                                            <span class="courier-icon">{{ $courier['icon'] }}</span>
                                            <span>{{ $courier['name'] }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Delivery Agent Section --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-user-tie"></i></div>
                            <div>
                                <h3 class="section-title">Delivery Agent (Optional)</h3>
                                <p class="section-subtitle">Assign shipment to a delivery agent</p>
                            </div>
                        </div>

                        <div class="agent-select-container">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-user-tie"></i> Select Delivery Agent</label>
                                <select name="agent_id" class="form-control" id="agentSelect">
                                    <option value="">-- No Agent (Assign Later) --</option>
                                    @php $__col = $deliveryAgents; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $agent)
                                        <option value="{{ $agent->id }}"
                                            data-lat="{{ $agent->current_latitude ?? '' }}"
                                            data-lng="{{ $agent->current_longitude ?? '' }}"
                                            data-phone="{{ $agent->mobile ?? ($agent->phone ?? '') }}"
                                            data-vehicle="{{ $agent->vehicle_type ?? 'Bike' }}"
                                            data-status="{{ $agent->status ?? 'available' }}"
                                            data-deliveries="{{ $agent->total_deliveries ?? 0 }}"
                                            data-rating="{{ $agent->rating ?? '4.5' }}"
                                            data-city="{{ $agent->city ?? '' }}"
                                            {{ old('agent_id') == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->name }}
                                            @if (isset($agent->city) && $agent->city)
                                                - {{ $agent->city }}
                                            @endif
                                            @if (isset($agent->status))
                                                ({{ $agent->status == 'available' ? '✅ Available' : ($agent->status == 'busy' ? '⏰ Busy' : '📴 Offline') }})
                                            @endif
                                        </option>
                                    @endforeach
@else
                                        <option value="" disabled>No agents available</option>
                                    @endif
                                </select>
                            </div>

                            <div class="form-group">
                                <button type="button" id="track-live-agents" class="btn btn-secondary"
                                    style="width: 100%;">
                                    <i class="fas fa-sync-alt"></i> Refresh Live Agent Locations
                                </button>
                                <small id="last-update-time"
                                    style="display: block; text-align: center; margin-top: 0.5rem; color: #64748b;">
                                    <i class="fas fa-clock"></i> Last updated: Just now
                                </small>
                            </div>

                            <div id="agentInfoCard" class="agent-info-card">
                                <div class="agent-info-header">
                                    <div class="agent-avatar"><i class="fas fa-motorcycle"></i></div>
                                    <div class="agent-details">
                                        <div class="agent-name" id="agentName">-</div>
                                        <span class="agent-status" id="agentStatus">-</span>
                                    </div>
                                </div>
                                <div class="agent-stats">
                                    <div class="agent-stat-item">
                                        <div class="agent-stat-label">Phone</div>
                                        <div class="agent-stat-value" id="agentPhone">-</div>
                                    </div>
                                    <div class="agent-stat-item">
                                        <div class="agent-stat-label">Vehicle</div>
                                        <div class="agent-stat-value" id="agentVehicle">-</div>
                                    </div>
                                    <div class="agent-stat-item">
                                        <div class="agent-stat-label">City</div>
                                        <div class="agent-stat-value" id="agentCity">-</div>
                                    </div>
                                    <div class="agent-stat-item">
                                        <div class="agent-stat-label">Deliveries</div>
                                        <div class="agent-stat-value" id="agentDeliveries">0</div>
                                    </div>
                                    <div class="agent-stat-item">
                                        <div class="agent-stat-label">Rating</div>
                                        <div class="agent-stat-value" id="agentRating">N/A</div>
                                    </div>
                                    <div class="agent-stat-item">
                                        <div class="agent-stat-label">Distance</div>
                                        <div class="agent-stat-value" id="agentDistance">-</div>
                                    </div>
                                </div>
                            </div>

                            <div id="agent-map-preview" class="agent-map-preview"></div>
                        </div>
                    </div>

                    {{-- Price Preview --}}
                    <div class="price-preview" id="pricePreview">
                        <div class="price-item">
                            <div class="price-label">Shipping Charge</div>
                            <div class="price-value" id="shippingCharge">₹0.00</div>
                        </div>
                        <div class="price-item">
                            <div class="price-label">COD Charge</div>
                            <div class="price-value" id="codCharge">₹0.00</div>
                        </div>
                        <div class="price-item">
                            <div class="price-label">Insurance</div>
                            <div class="price-value" id="insuranceCharge">₹0.00</div>
                        </div>
                        <div class="price-item">
                            <div class="price-label">Total</div>
                            <div class="price-value total" id="totalCharge">₹0.00</div>
                        </div>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="submit-section">
                        <a href="{{ route('logistics.shipments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-paper-plane"></i> Create Shipment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places,geometry&callback=initGoogleMaps"
        async defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

    <script>
        // ==================== GOOGLE MAPS & PLACES API ====================
        let map;
        let marker;
        let geocoder;
        let autocompleteService;
        let placesService;
        let mapInitialized = false;
        let agentMarkers = [];

        function initGoogleMaps() {
            geocoder = new google.maps.Geocoder();
            autocompleteService = new google.maps.places.AutocompleteService();
            placesService = new google.maps.places.PlacesService(document.createElement('div'));

            const mapElement = document.getElementById('agent-map-preview');
            if (mapElement) {
                const defaultLat = 22.524768;
                const defaultLng = 72.955568;

                map = new google.maps.Map(mapElement, {
                    center: {
                        lat: defaultLat,
                        lng: defaultLng
                    },
                    zoom: 12,
                    mapTypeControl: false,
                    fullscreenControl: true,
                    streetViewControl: false,
                    styles: [{
                        featureType: "poi",
                        elementType: "labels",
                        stylers: [{
                            visibility: "off"
                        }]
                    }]
                });
                mapInitialized = true;
                showAllAgentsOnMap();

                const selectedAgent = document.getElementById('agentSelect').selectedOptions[0];
                if (selectedAgent && selectedAgent.value) {
                    updateAgentInfo(selectedAgent);
                }
            }
            setupAddressAutocomplete();
        }

        // ==================== GEOLOCATION ====================
        document.getElementById('detect-location').addEventListener('click', function() {
            detectUserLocation();
        });

        function detectUserLocation() {
            const statusDiv = document.getElementById('location-status');
            const detectBtn = document.getElementById('detect-location');

            if (!navigator.geolocation) {
                statusDiv.className = 'location-status error';
                statusDiv.innerHTML =
                    '<i class="fas fa-exclamation-circle"></i> Geolocation is not supported by your browser';
                return;
            }

            statusDiv.className = 'location-status loading';
            statusDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Detecting your location...';
            detectBtn.disabled = true;

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    geocoder.geocode({
                        location: {
                            lat,
                            lng
                        }
                    }, function(results, status) {
                        if (status === 'OK' && results[0]) {
                            const address = results[0].formatted_address;
                            document.getElementById('address-input').value = address;
                            document.getElementById('shipping_address').value = address;
                            document.getElementById('latitude').value = lat;
                            document.getElementById('longitude').value = lng;

                            const components = {};
                            results[0].address_components.forEach(component => {
                                const type = component.types[0];
                                components[type] = component;
                            });

                            if (components.locality) document.getElementById('city').value = components.locality
                                .long_name;
                            if (components.administrative_area_level_1) document.getElementById('state').value =
                                components.administrative_area_level_1.long_name;
                            if (components.postal_code) document.getElementById('pincode').value = components
                                .postal_code.long_name;
                            if (components.country) document.getElementById('country').value = components
                                .country.long_name;

                            document.getElementById('preview-text').textContent = address;
                            document.getElementById('address-preview').classList.add('show');

                            if (map) updateMapWithLocation({
                                lat,
                                lng
                            });
                            updateAgentDistances(lat, lng);

                            statusDiv.className = 'location-status success';
                            statusDiv.innerHTML =
                                '<i class="fas fa-check-circle"></i> Location detected successfully!';
                        } else {
                            statusDiv.className = 'location-status error';
                            statusDiv.innerHTML =
                                '<i class="fas fa-exclamation-circle"></i> Could not get address for your location';
                        }
                        setTimeout(() => statusDiv.style.display = 'none', 3000);
                        detectBtn.disabled = false;
                    });
                },
                function(error) {
                    let message = '';
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            message = 'Location access denied. Please enable location permissions.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            message = 'Location information is unavailable.';
                            break;
                        case error.TIMEOUT:
                            message = 'Location request timed out.';
                            break;
                    }
                    statusDiv.className = 'location-status error';
                    statusDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + message;
                    detectBtn.disabled = false;
                    setTimeout(() => statusDiv.style.display = 'none', 3000);
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }

        function updateAgentDistances(userLat, userLng) {
            const agentSelect = document.getElementById('agentSelect');
            const options = agentSelect.options;

            for (let i = 1; i < options.length; i++) {
                const option = options[i];
                const agentLat = parseFloat(option.dataset.lat);
                const agentLng = parseFloat(option.dataset.lng);
                if (agentLat && agentLng) {
                    const distance = calculateDistance(userLat, userLng, agentLat, agentLng);
                    option.textContent = option.textContent.split(' - ')[0] + ` - ${distance.toFixed(1)}km away `;
                }
            }
        }

        // ==================== ADDRESS AUTOCOMPLETE ====================
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
                fields: ['address_components', 'formatted_address', 'geometry', 'place_id']
            }, (place, status) => {
                if (status === google.maps.places.PlacesServiceStatus.OK) {
                    const components = {};
                    place.address_components.forEach(component => {
                        components[component.types[0]] = component;
                    });

                    document.getElementById('shipping_address').value = place.formatted_address;
                    document.getElementById('latitude').value = place.geometry.location.lat();
                    document.getElementById('longitude').value = place.geometry.location.lng();
                    document.getElementById('place_id').value = place.place_id;

                    if (components.locality) document.getElementById('city').value = components.locality.long_name;
                    if (components.administrative_area_level_1) document.getElementById('state').value = components
                        .administrative_area_level_1.long_name;
                    if (components.postal_code) document.getElementById('pincode').value = components.postal_code
                        .long_name;
                    if (components.country) document.getElementById('country').value = components.country.long_name;

                    document.getElementById('preview-text').textContent = place.formatted_address;
                    document.getElementById('address-preview').classList.add('show');

                    updateMapWithLocation(place.geometry.location);
                    updateAgentDistances(place.geometry.location.lat(), place.geometry.location.lng());
                }
            });
        }

        function updateMapWithLocation(location) {
            if (!map) return;
            if (marker) marker.setMap(null);
            marker = new google.maps.Marker({
                position: location,
                map: map,
                title: 'Delivery Location',
                icon: {
                    url: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png',
                    scaledSize: new google.maps.Size(40, 40)
                }
            });
            map.setCenter(location);
            map.setZoom(15);
        }

        // ==================== AGENT FUNCTIONS ====================
        function showAllAgentsOnMap() {
            if (!map) return;
            agentMarkers.forEach(m => m.setMap(null));
            agentMarkers = [];

            const agentSelect = document.getElementById('agentSelect');
            const options = agentSelect.options;

            for (let i = 1; i < options.length; i++) {
                const option = options[i];
                const lat = parseFloat(option.dataset.lat);
                const lng = parseFloat(option.dataset.lng);
                if (lat && lng) {
                    let iconUrl = option.dataset.status === 'available' ?
                        'http://maps.google.com/mapfiles/ms/icons/green-dot.png' :
                        option.dataset.status === 'busy' ? 'http://maps.google.com/mapfiles/ms/icons/yellow-dot.png' :
                        'http://maps.google.com/mapfiles/ms/icons/red-dot.png';

                    const markerObj = new google.maps.Marker({
                        position: {
                            lat,
                            lng
                        },
                        map: map,
                        title: option.text.split(' - ')[0],
                        icon: {
                            url: iconUrl,
                            scaledSize: new google.maps.Size(32, 32)
                        }
                    });
                    markerObj.addListener('click', () => {
                        agentSelect.value = option.value;
                        updateAgentInfo(option);
                        map.setCenter({
                            lat,
                            lng
                        });
                        map.setZoom(15);
                    });
                    agentMarkers.push(markerObj);
                }
            }
        }

        function updateAgentInfo(selectedOption) {
            const card = document.getElementById('agentInfoCard');
            if (!selectedOption || !selectedOption.value) {
                card.classList.remove('show');
                return;
            }

            document.getElementById('agentName').textContent = selectedOption.text.split(' - ')[0];
            document.getElementById('agentPhone').textContent = selectedOption.dataset.phone || '-';
            document.getElementById('agentVehicle').textContent = selectedOption.dataset.vehicle || 'Bike';
            document.getElementById('agentCity').textContent = selectedOption.dataset.city || '-';
            document.getElementById('agentDeliveries').textContent = selectedOption.dataset.deliveries || '0';
            document.getElementById('agentRating').textContent = selectedOption.dataset.rating || 'N/A';

            const status = selectedOption.dataset.status || 'offline';
            const statusEl = document.getElementById('agentStatus');
            statusEl.textContent = status === 'available' ? '✅ Available' : status === 'busy' ? '⏰ Busy' : '📴 Offline';
            statusEl.className = 'agent-status ' + (status === 'available' ? 'status-available' : status === 'busy' ?
                'status-busy' : '');

            const shipmentLat = parseFloat(document.getElementById('latitude').value);
            const shipmentLng = parseFloat(document.getElementById('longitude').value);
            const agentLat = parseFloat(selectedOption.dataset.lat);
            const agentLng = parseFloat(selectedOption.dataset.lng);
            if (shipmentLat && shipmentLng && agentLat && agentLng) {
                const distance = calculateDistance(shipmentLat, shipmentLng, agentLat, agentLng);
                document.getElementById('agentDistance').textContent = distance.toFixed(2) + ' km';
            } else {
                document.getElementById('agentDistance').textContent = '-';
            }
            card.classList.add('show');
        }

        function refreshAgentLocations() {
            const btn = document.getElementById('track-live-agents');
            btn.innerHTML = '<span class="spinner"></span> Updating...';
            btn.disabled = true;
            setTimeout(() => {
                document.getElementById('last-update-time').innerHTML =
                    '<i class="fas fa-clock"></i> Last updated: ' + new Date().toLocaleTimeString();
                showAllAgentsOnMap();
                btn.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh Live Agent Locations';
                btn.disabled = false;
                showToast('Agent locations updated successfully!', 'success');
            }, 1000);
        }

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) ** 2 + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(
                dLon / 2) ** 2;
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        }

        // ==================== PRICE CALCULATOR ====================
        function calculateCharges() {
            const weight = parseFloat(document.getElementById('weight').value) || 0;
            const declaredValue = parseFloat(document.getElementById('declaredValue').value) || 0;
            const shippingMethod = document.getElementById('shippingMethod').value;
            const paymentMode = document.getElementById('paymentMode').value;

            let baseRate = shippingMethod === 'standard' ? 50 : shippingMethod === 'express' ? 75 : 100;
            const weightCharge = weight * baseRate;

            let codCharge = 0;
            if (paymentMode === 'cod') {
                if (declaredValue <= 5000) codCharge = 30;
                else if (declaredValue <= 10000) codCharge = 50;
                else if (declaredValue <= 25000) codCharge = 100;
                else codCharge = declaredValue * 0.005;
            }

            let insuranceCharge = declaredValue > 10000 ? declaredValue * 0.001 : 0;
            const total = weightCharge + codCharge + insuranceCharge;

            document.getElementById('shippingCharge').textContent = '₹' + weightCharge.toFixed(2);
            document.getElementById('codCharge').textContent = '₹' + codCharge.toFixed(2);
            document.getElementById('insuranceCharge').textContent = '₹' + insuranceCharge.toFixed(2);
            document.getElementById('totalCharge').textContent = '₹' + total.toFixed(2);
        }

        // ==================== EVENT LISTENERS ====================
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('weight').addEventListener('input', calculateCharges);
            document.getElementById('declaredValue').addEventListener('input', calculateCharges);
            document.getElementById('shippingMethod').addEventListener('change', calculateCharges);
            document.getElementById('paymentMode').addEventListener('change', calculateCharges);
            calculateCharges();

            document.getElementById('agentSelect').addEventListener('change', function(e) {
                updateAgentInfo(e.target.options[e.target.selectedIndex]);
            });

            document.getElementById('shippingMethod').addEventListener('change', function() {
                const date = new Date();
                date.setDate(date.getDate() + (this.value === 'standard' ? 3 : 1));
                document.getElementById('estimatedDate').value = date.toISOString().split('T')[0];
            });

            document.getElementById('track-live-agents').addEventListener('click', refreshAgentLocations);
            document.getElementById('shipmentForm').addEventListener('submit', function(e) {
                if (!document.getElementById('shipping_address').value) {
                    e.preventDefault();
                    showToast(
                        'Please select a valid address from the suggestions or use your current location',
                        'error');
                } else {
                    document.getElementById('submitBtn').innerHTML =
                        '<span class="spinner"></span> Creating...';
                    document.getElementById('submitBtn').disabled = true;
                }
            });

            setInterval(refreshAgentLocations, 30000);
        });

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.innerHTML = `<div class="toast-content">${type === 'success' ? '✅' : '❌'} ${message}</div>`;
            toast.className = 'toast ' + type;
            toast.style.display = 'block';
            setTimeout(() => toast.style.display = 'none', 3000);
        }

        window.initGoogleMaps = initGoogleMaps;
    </script>
@endsection
