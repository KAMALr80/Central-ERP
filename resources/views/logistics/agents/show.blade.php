{{-- resources/views/logistics/agents/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Agent Details - ' . $agent->name)

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
            min-height: 100vh;
        }

        .agent-page {
            min-height: 100vh;
            padding: clamp(16px, 3vw, 30px);
            width: 100%;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        .agent-card {
            background: #ffffff;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
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

        .agent-header {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            padding: clamp(1.5rem, 4vw, 2rem);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .agent-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
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

        .header-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: 700;
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
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .header-badge {
            display: inline-block;
            padding: 0.5rem 1.25rem;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .header-badge.available {
            background: #10b981;
            color: white;
        }

        .header-badge.busy {
            background: #f59e0b;
            color: white;
        }

        .header-badge.offline {
            background: #6b7280;
            color: white;
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
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .header-btn:hover {
            background: white;
            color: #0f172a;
            transform: translateY(-2px);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
            padding: 1.5rem clamp(1.5rem, 4vw, 2rem);
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
        }

        .stat-card {
            background: white;
            padding: 1.25rem;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            transition: all 0.2s;
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
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        .stat-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .stat-icon.total {
            background: #3b82f6;
            color: white;
        }

        .stat-icon.success {
            background: #10b981;
            color: white;
        }

        .stat-icon.rating {
            background: #f59e0b;
            color: white;
        }

        .stat-icon.days {
            background: #8b5cf6;
            color: white;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            width: 0%;
            transition: width 0.3s ease;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
            padding: clamp(1.5rem, 4vw, 2rem);
            border-bottom: 1px solid #e5e7eb;
        }

        .info-card {
            background: #f8fafc;
            border-radius: 20px;
            padding: 1.5rem;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            border-color: #667eea;
        }

        .info-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .info-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .info-value {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.95rem;
            text-align: right;
        }

        .info-value.highlight {
            color: #667eea;
            font-weight: 700;
        }

        .info-value.success {
            color: #10b981;
        }

        .info-value.warning {
            color: #f59e0b;
        }

        .info-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin: 0.2rem;
        }

        .info-badge.info {
            background: #dbeafe;
            color: #1e40af;
        }

        .info-badge.success {
            background: #d1fae5;
            color: #065f46;
        }

        .documents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .document-card {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
            border: 1px solid #e5e7eb;
            cursor: pointer;
            transition: all 0.2s;
        }

        .document-card:hover {
            transform: translateY(-2px);
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.1);
        }

        .document-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .document-name {
            font-size: 0.85rem;
            font-weight: 600;
            color: #1e293b;
        }

        .document-status {
            font-size: 0.75rem;
            color: #10b981;
            margin-top: 0.25rem;
        }

        .map-section {
            padding: clamp(1.5rem, 4vw, 2rem);
            border-bottom: 1px solid #e5e7eb;
        }

        .map-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        #agentMap {
            height: 400px;
            width: 100%;
            border-radius: 20px;
            border: 2px solid #e5e7eb;
        }

        .location-update {
            margin-top: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            background: #f8fafc;
            padding: 1rem;
            border-radius: 12px;
        }

        .location-time {
            color: #64748b;
            font-size: 0.9rem;
        }

        .location-coords {
            font-family: monospace;
            background: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }

        .deliveries-section {
            padding: clamp(1.5rem, 4vw, 2rem);
            border-bottom: 1px solid #e5e7eb;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .table-responsive {
            overflow-x: auto;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            background: white;
        }

        .deliveries-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        .deliveries-table th {
            background: #f8fafc;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.85rem;
            color: #475569;
            border-bottom: 2px solid #e5e7eb;
        }

        .deliveries-table td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .deliveries-table tbody tr:hover {
            background: #f8fafc;
        }

        .delivery-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .delivery-status.delivered {
            background: #d1fae5;
            color: #065f46;
        }

        .delivery-status.pending {
            background: #fef3c7;
            color: #92400e;
        }

        .delivery-status.in_transit {
            background: #dbeafe;
            color: #1e40af;
        }

        .action-buttons {
            padding: 1.5rem clamp(1.5rem, 4vw, 2rem);
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            background: #f8fafc;
        }

        .btn {
            padding: 0.875rem 2rem;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e5e7eb;
        }

        .btn:hover {
            transform: translateY(-2px);
            filter: brightness(1.05);
        }

        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border-left: 4px solid;
            display: none;
            z-index: 10000;
            animation: slideIn 0.3s ease;
        }

        .toast.success {
            border-left-color: #10b981;
        }

        .toast.error {
            border-left-color: #ef4444;
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

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(4px);
            z-index: 11000;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 1rem;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #e5e7eb;
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

            .deliveries-table {
                min-width: 500px;
            }

            #agentMap {
                height: 300px;
            }
        }
    </style>

    <div class="agent-page">
        <div class="container">
            <div class="agent-card">
                <div id="loadingOverlay" class="loading-overlay">
                    <div class="spinner"></div>
                    <div class="loading-text">Processing...</div>
                </div>

                <div class="agent-header">
                    <div class="header-content">
                        <div class="header-left">
                            <div class="header-avatar">{{ substr($agent->name, 0, 1) }}</div>
                            <div>
                                <div class="header-title">
                                    {{ $agent->name }}
                                    <span class="header-badge {{ $agent->status }}">{{ ucfirst($agent->status) }}</span>
                                </div>
                                <div class="header-subtitle">
                                    <span><i class="fas fa-id-card"></i>
                                        {{ $agent->agent_code ?? 'AG' . str_pad($agent->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    <span>•</span>
                                    <span><i class="fas fa-phone"></i> {{ $agent->phone }}</span>
                                    @if ($agent->email)
                                        <span>•</span>
                                        <span><i class="fas fa-envelope"></i> {{ $agent->email }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="header-actions">
                            <a href="{{ route('logistics.agents.edit', $agent->id) }}" class="header-btn"><i
                                    class="fas fa-edit"></i> Edit Agent</a>
                            <button class="header-btn" onclick="changeStatus()"><i class="fas fa-exchange-alt"></i> Change
                                Status</button>
                            <a href="{{ route('logistics.agents.index') }}" class="header-btn"><i
                                    class="fas fa-arrow-left"></i> Back to List</a>
                        </div>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon total"><i class="fas fa-box"></i></div>
                            <div class="stat-value">{{ number_format($agent->total_deliveries ?? 0) }}</div>
                        </div>
                        <div class="stat-label">Total Deliveries</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon success"><i class="fas fa-check-circle"></i></div>
                            <div class="stat-value">{{ number_format($agent->successful_deliveries ?? 0) }}</div>
                        </div>
                        <div class="stat-label">Successful</div>
                        <div class="progress-bar">
                            <div class="progress-fill"
                                style="width: {{ $agent->total_deliveries > 0 ? ($agent->successful_deliveries / $agent->total_deliveries) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon rating"><i class="fas fa-star"></i></div>
                            <div class="stat-value">{{ number_format($agent->rating ?? 4.5, 1) }}</div>
                        </div>
                        <div class="stat-label">Rating</div>
                        <div>
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= round($agent->rating ?? 4.5))
                                <span style="color:#f59e0b;">★</span>@else<span style="color:#e5e7eb;">☆</span>
                                @endif
                            @endfor
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon days"><i class="fas fa-calendar-alt"></i></div>
                            <div class="stat-value">{{ $agent->created_at->format('d M Y') }}</div>
                        </div>
                        <div class="stat-label">Member Since</div>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-title">
                            <div class="info-icon"><i class="fas fa-user"></i></div><span>Personal Information</span>
                        </div>
                        <div class="info-row"><span class="info-label">Full Name</span><span
                                class="info-value">{{ $agent->name }}</span></div>
                        <div class="info-row"><span class="info-label">Phone Number</span><span
                                class="info-value">{{ $agent->phone }}</span></div>
                        @if ($agent->alternate_phone)
                            <div class="info-row"><span class="info-label">Alternate Phone</span><span
                                    class="info-value">{{ $agent->alternate_phone }}</span></div>
                        @endif
                        @if ($agent->email)
                            <div class="info-row"><span class="info-label">Email</span><span
                                    class="info-value">{{ $agent->email }}</span></div>
                        @endif
                        <div class="info-row"><span class="info-label">Employment Type</span><span
                                class="info-value">{{ ucfirst(str_replace('_', ' ', $agent->employment_type ?? 'Full Time')) }}</span>
                        </div>
                        <div class="info-row"><span class="info-label">Joining Date</span><span
                                class="info-value">{{ $agent->joining_date ? \Carbon\Carbon::parse($agent->joining_date)->format('d M Y') : 'N/A' }}</span>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-title">
                            <div class="info-icon"><i class="fas fa-truck"></i></div><span>Vehicle Information</span>
                        </div>
                        <div class="info-row"><span class="info-label">Vehicle Type</span><span
                                class="info-value">{{ ucfirst($agent->vehicle_type ?? 'Not Assigned') }}</span></div>
                        @if ($agent->vehicle_number)
                            <div class="info-row"><span class="info-label">Vehicle Number</span><span
                                    class="info-value">{{ $agent->vehicle_number }}</span></div>
                        @endif
                        @if ($agent->license_number)
                            <div class="info-row"><span class="info-label">License Number</span><span
                                    class="info-value">{{ $agent->license_number }}</span></div>
                        @endif
                        <div class="info-row"><span class="info-label">Service Areas</span><span class="info-value">
                                @if ($agent->service_areas)
                                    @foreach (json_decode($agent->service_areas, true) ?: [] as $area)
                                        <span class="info-badge info">{{ $area }}</span>
                                    @endforeach
                                @else
                                    Not specified
                                @endif
                            </span></div>
                    </div>

                    <div class="info-card">
                        <div class="info-title">
                            <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div><span>Address
                                Information</span>
                        </div>
                        @if ($agent->address)
                            <div class="info-row"><span class="info-label">Address</span><span
                                    class="info-value">{{ $agent->address }}</span></div>
                        @endif
                        @if ($agent->city)
                            <div class="info-row"><span class="info-label">City</span><span
                                    class="info-value">{{ $agent->city }}</span></div>
                        @endif
                        @if ($agent->state)
                            <div class="info-row"><span class="info-label">State</span><span
                                    class="info-value">{{ $agent->state }}</span></div>
                        @endif
                        @if ($agent->pincode)
                            <div class="info-row"><span class="info-label">Pincode</span><span
                                    class="info-value">{{ $agent->pincode }}</span></div>
                        @endif
                    </div>

                    <div class="info-card">
                        <div class="info-title">
                            <div class="info-icon"><i class="fas fa-university"></i></div><span>Bank Details</span>
                        </div>
                        @if ($agent->bank_name)
                            <div class="info-row"><span class="info-label">Bank Name</span><span
                                    class="info-value">{{ $agent->bank_name }}</span></div>
                        @endif
                        @if ($agent->account_number)
                            <div class="info-row"><span class="info-label">Account Number</span><span
                                    class="info-value">XXXX{{ substr($agent->account_number, -4) }}</span></div>
                        @endif
                        @if ($agent->ifsc_code)
                            <div class="info-row"><span class="info-label">IFSC Code</span><span
                                    class="info-value">{{ $agent->ifsc_code }}</span></div>
                        @endif
                        @if ($agent->upi_id)
                            <div class="info-row"><span class="info-label">UPI ID</span><span
                                    class="info-value">{{ $agent->upi_id }}</span></div>
                        @endif
                        @if ($agent->salary)
                            <div class="info-row"><span class="info-label">Base Salary</span><span
                                    class="info-value success">₹{{ number_format($agent->salary, 2) }}</span></div>
                        @endif
                    </div>

                    <div class="info-card">
                        <div class="info-title">
                            <div class="info-icon"><i class="fas fa-file-alt"></i></div><span>Documents</span>
                        </div>
                        <div class="documents-grid">
                            <div class="document-card"
                                onclick="viewDocument('{{ $agent->aadhar_card ? Storage::url($agent->aadhar_card) : '#' }}')">
                                <div class="document-icon">🆔</div>
                                <div class="document-name">Aadhar Card</div>
                                <div class="document-status">{{ $agent->aadhar_card ? 'Uploaded' : 'Pending' }}</div>
                            </div>
                            <div class="document-card"
                                onclick="viewDocument('{{ $agent->driving_license ? Storage::url($agent->driving_license) : '#' }}')">
                                <div class="document-icon">🚗</div>
                                <div class="document-name">Driving License</div>
                                <div class="document-status">{{ $agent->driving_license ? 'Uploaded' : 'Pending' }}</div>
                            </div>
                            <div class="document-card"
                                onclick="viewDocument('{{ $agent->photo ? Storage::url($agent->photo) : '#' }}')">
                                <div class="document-icon">📸</div>
                                <div class="document-name">Profile Photo</div>
                                <div class="document-status">{{ $agent->photo ? 'Uploaded' : 'Pending' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($agent->current_latitude && $agent->current_longitude)
                    <div class="map-section">
                        <div class="map-title"><i class="fas fa-map-marked-alt"></i> Current Location</div>
                        <div id="agentMap"></div>
                        <div class="location-update">
                            <div class="location-time"><i class="fas fa-clock"></i> Last Updated:
                                {{ $agent->last_location_update ? $agent->last_location_update->diffForHumans() : 'Never' }}
                            </div>
                            <div class="location-coords">{{ $agent->current_latitude }}, {{ $agent->current_longitude }}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="deliveries-section">
                    <div class="section-header">
                        <div class="section-title"><i class="fas fa-box"></i> Recent Deliveries</div>
                        <a href="{{ route('logistics.shipments.index', ['agent_id' => $agent->id]) }}"
                            class="btn btn-secondary" style="padding:0.5rem 1rem;">View All →</a>
                    </div>
                    <div class="table-responsive">
                        <table class="deliveries-table">
                            <thead>
                                <tr>
                                    <th>Shipment #</th>
                                    <th>Customer</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $__col = $agent->assignedShipments()->latest()->take(5)->get(); @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $shipment)
                                    <tr>
                                        <td><a href="{{ route('logistics.shipments.show', $shipment->id) }}"
                                                style="color:#667eea; font-weight:600;">{{ $shipment->shipment_number }}</a>
                                        </td>
                                        <td>{{ $shipment->receiver_name }}</td>
                                        <td>{{ $shipment->city }}, {{ $shipment->state }}</td>
                                        <td><span
                                                class="delivery-status {{ $shipment->status }}">{{ ucfirst(str_replace('_', ' ', $shipment->status)) }}</span>
                                        </td>
                                        <td>{{ $shipment->created_at->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
@else
                                    <tr>
                                        <td colspan="5" style="text-align:center; padding:2rem;">No deliveries assigned
                                            yet</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="{{ route('logistics.agents.edit', $agent->id) }}" class="btn btn-primary"><i
                            class="fas fa-edit"></i> Edit Agent</a>
                    <button class="btn btn-success" onclick="updateStatus('available')"><i
                            class="fas fa-check-circle"></i> Mark Available</button>
                    <button class="btn btn-warning" onclick="updateStatus('busy')"><i class="fas fa-truck"></i> Mark
                        Busy</button>
                    <button class="btn btn-secondary" onclick="updateStatus('offline')"><i class="fas fa-clock"></i> Mark
                        Offline</button>
                    <a href="{{ route('logistics.agents.index') }}" class="btn btn-secondary"><i
                            class="fas fa-arrow-left"></i> Back to List</a>
                </div>
            </div>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async
        defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

    <script>
        let map, marker;

        function initMap() {
            @if ($agent->current_latitude && $agent->current_longitude)
                map = new google.maps.Map(document.getElementById('agentMap'), {
                    center: {
                        lat: {{ $agent->current_latitude }},
                        lng: {{ $agent->current_longitude }}
                    },
                    zoom: 15,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    styles: [{
                        featureType: 'poi',
                        elementType: 'labels',
                        stylers: [{
                            visibility: 'off'
                        }]
                    }]
                });
                marker = new google.maps.Marker({
                    position: {
                        lat: {{ $agent->current_latitude }},
                        lng: {{ $agent->current_longitude }}
                    },
                    map: map,
                    title: '{{ $agent->name }}',
                    icon: {
                        url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                        scaledSize: new google.maps.Size(48, 48)
                    },
                    animation: google.maps.Animation.DROP
                });
                new google.maps.InfoWindow({
                    content: `<b>{{ $agent->name }}</b><br>Current Location`
                }).open(map, marker);
            @endif
        }

        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toast');
            toast.innerHTML = `<div><span>${type === 'success' ? '✅' : '❌'}</span> ${msg}</div>`;
            toast.className = 'toast ' + type;
            toast.style.display = 'block';
            setTimeout(() => toast.style.display = 'none', 3000);
        }

        function changeStatus() {
            const newStatus = prompt('Enter new status (available, busy, offline):', '{{ $agent->status }}');
            if (!newStatus || !['available', 'busy', 'offline'].includes(newStatus)) {
                showToast('Invalid status. Use: available, busy, or offline', 'error');
                return;
            }
            updateStatus(newStatus);
        }

        function updateStatus(status) {
            showLoading();
            fetch(`/logistics/agents/{{ $agent->id }}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status: status
                    })
                })
                .then(r => r.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        showToast('✅ Status updated successfully!');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('❌ ' + data.message, 'error');
                    }
                })
                .catch(() => {
                    hideLoading();
                    showToast('❌ Error updating status', 'error');
                });
        }

        function viewDocument(url) {
            if (url && url !== '#') window.open(url, '_blank');
            else showToast('Document not uploaded yet', 'error');
        }

        window.initMap = initMap;
    </script>
@endsection
