{{-- resources/views/logistics/agents/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Delivery Agents Management')

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

        .agents-page {
            min-height: 100vh;
            padding: clamp(16px, 3vw, 30px);
            width: 100%;
        }

        .container {
            max-width: 1600px;
            margin: 0 auto;
            width: 100%;
        }

        .agents-card {
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

        .agents-header {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            padding: clamp(1.5rem, 4vw, 2rem);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .agents-header::before {
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
            flex: 1;
            min-width: 280px;
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

        .header-subtitle {
            opacity: 0.9;
            font-size: clamp(0.9rem, 2.5vw, 1rem);
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
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
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

        .stat-icon.available {
            background: #10b981;
            color: white;
        }

        .stat-icon.busy {
            background: #f59e0b;
            color: white;
        }

        .stat-icon.offline {
            background: #64748b;
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

        .filter-bar {
            padding: 1.25rem clamp(1.5rem, 4vw, 2rem);
            background: white;
            border-bottom: 1px solid #e5e7eb;
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: flex-end;
        }

        .filter-group {
            flex: 1;
            min-width: 180px;
        }

        .filter-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 0.25rem;
        }

        .filter-input,
        .filter-select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .filter-input:focus,
        .filter-select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .filter-btn-primary {
            background: #3b82f6;
            color: white;
        }

        .filter-btn-primary:hover {
            background: #2563eb;
        }

        .filter-btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e5e7eb;
        }

        .table-responsive {
            overflow-x: auto;
            border-radius: 16px;
            background: white;
        }

        .agents-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1200px;
        }

        .agents-table th {
            background: #f8fafc;
            padding: 1rem 1.25rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.85rem;
            color: #475569;
            text-transform: uppercase;
            border-bottom: 2px solid #e5e7eb;
        }

        .agents-table td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        .agents-table tbody tr:hover {
            background: #f8fafc;
        }

        .agent-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .agent-avatar {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-badge.available {
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge.busy {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge.offline {
            background: #e2e8f0;
            color: #475569;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .status-dot.available {
            background: #10b981;
        }

        .status-dot.busy {
            background: #f59e0b;
        }

        .status-dot.offline {
            background: #64748b;
        }

        .rating-stars {
            display: flex;
            gap: 0.125rem;
        }

        .star-filled {
            color: #f59e0b;
        }

        .star-empty {
            color: #e5e7eb;
        }

        .action-btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            transition: all 0.2s;
        }

        .action-btn-view {
            background: #3b82f6;
            color: white;
        }

        .action-btn-edit {
            background: #f59e0b;
            color: white;
        }

        .action-btn-map {
            background: #8b5cf6;
            color: white;
        }

        .action-btn-status {
            background: #10b981;
            color: white;
        }

        .action-btn-danger {
            background: #ef4444;
            color: white;
        }

        .action-btn-danger:hover {
            background: #dc2626;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }

        /* Map Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 24px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
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
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        #agentMap {
            height: 400px;
            width: 100%;
            border-radius: 12px;
        }

        /* Delete Confirmation Modal */
        .delete-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 10000;
            align-items: center;
            justify-content: center;
        }

        .delete-modal-content {
            background: white;
            border-radius: 24px;
            width: 90%;
            max-width: 450px;
            overflow: hidden;
            animation: modalSlideIn 0.3s ease;
        }

        .delete-modal-header {
            padding: 1.5rem;
            background: #fee2e2;
            border-bottom: 1px solid #fecaca;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .delete-modal-header i {
            font-size: 2rem;
            color: #ef4444;
        }

        .delete-modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #991b1b;
            margin: 0;
        }

        .delete-modal-body {
            padding: 1.5rem;
        }

        .delete-agent-info {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 12px;
            margin: 1rem 0;
            border: 1px solid #e5e7eb;
        }

        .delete-agent-name {
            font-weight: 700;
            font-size: 1rem;
            color: #1e293b;
        }

        .delete-agent-details {
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        .delete-warning {
            background: #fef3c7;
            padding: 0.75rem;
            border-radius: 8px;
            font-size: 0.85rem;
            color: #92400e;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .delete-modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            background: #f8fafc;
        }

        .delete-btn-confirm {
            background: #ef4444;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .delete-btn-confirm:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        .delete-btn-cancel {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e5e7eb;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .delete-btn-cancel:hover {
            background: #e2e8f0;
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
            border-top-color: #3b82f6;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .pagination-section {
            padding: 1.5rem;
            background: white;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .pagination {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .pagination-btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: white;
            cursor: pointer;
            text-decoration: none;
            color: #475569;
            transition: all 0.2s;
        }

        .pagination-btn:hover {
            background: #f1f5f9;
            border-color: #3b82f6;
        }

        .pagination-btn.active {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        @media (max-width: 768px) {
            .filter-form {
                flex-direction: column;
            }

            .pagination-section {
                flex-direction: column;
                text-align: center;
            }

            .agents-table {
                min-width: 1000px;
            }
        }
    </style>

    <div class="agents-page">
        <div class="container">
            <div class="agents-card">
                <div id="loadingOverlay" class="loading-overlay">
                    <div class="spinner"></div>
                    <div class="loading-text">Loading...</div>
                </div>

                <div class="agents-header">
                    <div class="header-content">
                        <div class="header-left">
                            <h1 class="header-title">
                                <i class="fas fa-users"></i> Delivery Agents
                                <span class="status-badge available"
                                    style="background: rgba(255,255,255,0.2);">{{ $stats['total'] ?? 0 }} Total</span>
                            </h1>
                            <p class="header-subtitle">Manage your delivery fleet and track agent performance</p>
                        </div>
                        <div class="header-actions">
                            <button class="header-btn" onclick="refreshAgents()"><i class="fas fa-sync-alt"></i>
                                Refresh</button>
                            <a href="{{ route('logistics.agents.create') }}" class="header-btn"><i class="fas fa-plus"></i>
                                Add New Agent</a>
                        </div>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon total"><i class="fas fa-users"></i></div>
                            <div class="stat-value">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                        <div class="stat-label">Total Agents</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon available"><i class="fas fa-check-circle"></i></div>
                            <div class="stat-value">{{ $stats['available'] ?? 0 }}</div>
                        </div>
                        <div class="stat-label">Available</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon busy"><i class="fas fa-truck"></i></div>
                            <div class="stat-value">{{ $stats['busy'] ?? 0 }}</div>
                        </div>
                        <div class="stat-label">Busy</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon offline"><i class="fas fa-clock"></i></div>
                            <div class="stat-value">{{ $stats['offline'] ?? 0 }}</div>
                        </div>
                        <div class="stat-label">Offline</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon total"><i class="fas fa-box"></i></div>
                            <div class="stat-value">{{ number_format($stats['total_deliveries'] ?? 0) }}</div>
                        </div>
                        <div class="stat-label">Total Deliveries</div>
                    </div>
                </div>

                <div class="filter-bar">
                    <form method="GET" action="{{ route('logistics.agents.index') }}" class="filter-form" id="filterForm">
                        <div class="filter-group"><label class="filter-label">Search</label><input type="text"
                                name="search" class="filter-input" placeholder="Name, phone, code..."
                                value="{{ request('search') }}"></div>
                        <div class="filter-group"><label class="filter-label">Status</label><select name="status"
                                class="filter-select">
                                <option value="">All Status</option>
                                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available
                                </option>
                                <option value="busy" {{ request('status') == 'busy' ? 'selected' : '' }}>Busy</option>
                                <option value="offline" {{ request('status') == 'offline' ? 'selected' : '' }}>Offline
                                </option>
                            </select></div>
                        <div class="filter-group"><label class="filter-label">City</label><input type="text"
                                name="city" class="filter-input" placeholder="Filter by city"
                                value="{{ request('city') }}"></div>
                        <div class="filter-group"><label class="filter-label">Vehicle Type</label><select
                                name="vehicle_type" class="filter-select">
                                <option value="">All Vehicles</option>
                                <option value="bike" {{ request('vehicle_type') == 'bike' ? 'selected' : '' }}>Bike
                                </option>
                                <option value="scooter" {{ request('vehicle_type') == 'scooter' ? 'selected' : '' }}>
                                    Scooter</option>
                                <option value="van" {{ request('vehicle_type') == 'van' ? 'selected' : '' }}>Van
                                </option>
                            </select></div>
                        <div class="filter-actions"><button type="submit" class="filter-btn filter-btn-primary"><i
                                    class="fas fa-search"></i> Apply Filters</button><a
                                href="{{ route('logistics.agents.index') }}" class="filter-btn filter-btn-secondary"><i
                                    class="fas fa-redo-alt"></i> Reset</a></div>
                    </form>
                </div>

                <div id="tableView">
                    <div class="table-responsive">
                        <table class="agents-table">
                            <thead>
                                32
                                <th>Agent</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th>Vehicle</th>
                                <th>Location</th>
                                <th>Performance</th>
                                <th>Rating</th>
                                <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $__col = $agents; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $agent)
                                    <tr class="status-{{ $agent->status }}">
                                        <td>
                                            <div class="agent-info">
                                                <div class="agent-avatar">{{ substr($agent->name, 0, 1) }}</div>
                                                <div>
                                                    <div class="agent-name" style="font-weight:600;">{{ $agent->name }}
                                                    </div>
                                                    <div style="font-size:0.8rem; color:#64748b;">
                                                        {{ $agent->agent_code ?? 'AG' . str_pad($agent->id, 4, '0', STR_PAD_LEFT) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div><i class="fas fa-phone"></i> {{ $agent->phone }}</div>
                                            @if ($agent->email)
                                                <div style="font-size:0.8rem; color:#64748b;"><i
                                                        class="fas fa-envelope"></i> {{ $agent->email }}</div>
                                            @endif
                                        </td>
                                        <td><span class="status-badge {{ $agent->status }}"><span
                                                    class="status-dot {{ $agent->status }}"></span>
                                                {{ ucfirst($agent->status) }}</span></td>
                                        <td>
                                            @if ($agent->vehicle_type)
                                                <div><i class="fas fa-motorcycle"></i> {{ ucfirst($agent->vehicle_type) }}
                                                </div>
                                                @if ($agent->vehicle_number)
                                                    <div style="font-size:0.8rem;">{{ $agent->vehicle_number }}</div>
                                                @endif
                                            @else
                                                <span style="color:#94a3b8;">Not assigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($agent->city)
                                                <div><i class="fas fa-map-marker-alt"></i> {{ $agent->city }}</div>
                                                @if ($agent->current_latitude && $agent->current_longitude)
                                                    <div style="font-size:0.75rem; color:#10b981;"><i
                                                            class="fas fa-satellite-dish"></i> Live location</div>
                                                @endif
                                            @else
                                                <span style="color:#94a3b8;">Not specified</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div><strong>{{ number_format($agent->successful_deliveries ?? 0) }}</strong> /
                                                {{ number_format($agent->total_deliveries ?? 0) }}</div>
                                            @if (($agent->total_deliveries ?? 0) > 0)
                                                <div style="font-size:0.8rem; color:#10b981;">
                                                    {{ round(($agent->successful_deliveries / $agent->total_deliveries) * 100) }}%
                                                    success</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="rating-stars">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= round($agent->rating ?? 4.5))
                                                    <span class="star-filled">★</span>@else<span
                                                            class="star-empty">☆</span>
                                                    @endif
                                                @endfor
                                            </div>
                                            <span style="font-size:0.85rem;">{{ number_format($agent->rating ?? 4.5, 1) }}
                                                ★</span>
                                        </td>
                                        <td>
                                            <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                                                <a href="{{ route('logistics.agents.show', $agent->id) }}"
                                                    class="action-btn action-btn-view"><i class="fas fa-eye"></i> View</a>
                                                <a href="{{ route('logistics.agents.edit', $agent->id) }}"
                                                    class="action-btn action-btn-edit"><i class="fas fa-edit"></i>
                                                    Edit</a>
                                                @if ($agent->current_latitude && $agent->current_longitude)
                                                    <button class="action-btn action-btn-map"
                                                        onclick="showAgentMap({{ $agent->id }}, '{{ $agent->name }}', {{ $agent->current_latitude }}, {{ $agent->current_longitude }})"><i
                                                            class="fas fa-map-marker-alt"></i> Map</button>
                                                @endif
                                                <button class="action-btn action-btn-status"
                                                    onclick="changeStatus({{ $agent->id }}, '{{ $agent->status }}')"><i
                                                        class="fas fa-exchange-alt"></i> Status</button>
                                                <button class="action-btn action-btn-danger"
                                                    onclick="showDeleteModal({{ $agent->id }}, '{{ $agent->name }}', '{{ $agent->agent_code ?? 'AG' . str_pad($agent->id, 4, '0', STR_PAD_LEFT) }}', '{{ $agent->phone }}')"><i
                                                        class="fas fa-trash-alt"></i> Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
@else
                                    <tr>
                                        <td colspan="8" style="text-align:center; padding:3rem;">
                                            <div style="font-size:3rem; margin-bottom:1rem;">👥</div>
                                            <h3>No Agents Found</h3>
                                            <p style="margin-bottom:1.5rem;">Add your first delivery agent to get started
                                            </p><a href="{{ route('logistics.agents.create') }}"
                                                class="filter-btn filter-btn-primary">+ Add New Agent</a>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                @if (isset($agents) && $agents->hasPages())
                    <div class="pagination-section">
                        <div>Showing {{ $agents->firstItem() }} to {{ $agents->lastItem() }} of {{ $agents->total() }}
                            agents</div>
                        <div class="pagination">
                            @if ($agents->onFirstPage())
                            <span class="pagination-btn disabled">←</span>@else<a
                                    href="{{ $agents->previousPageUrl() }}" class="pagination-btn">←</a>
                            @endif
                            @foreach ($agents->getUrlRange(max(1, $agents->currentPage() - 2), min($agents->lastPage(), $agents->currentPage() + 2)) as $page => $url)
                                <a href="{{ $url }}"
                                    class="pagination-btn {{ $page == $agents->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                            @endforeach
                            @if ($agents->hasMorePages())
                            <a href="{{ $agents->nextPageUrl() }}" class="pagination-btn">→</a>@else<span
                                    class="pagination-btn disabled">→</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Agent Map Modal --}}
    <div id="agentMapModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalAgentName">Agent Location</h3>
                <button class="modal-close" onclick="closeAgentMap()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="agentMap"></div>
            </div>
            <div class="modal-footer" style="padding:1rem; border-top:1px solid #e5e7eb; text-align:right;">
                <button class="filter-btn filter-btn-secondary" onclick="closeAgentMap()">Close</button>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteModal" class="delete-modal">
        <div class="delete-modal-content">
            <div class="delete-modal-header">
                <i class="fas fa-exclamation-triangle"></i>
                <h3 class="delete-modal-title">Delete Delivery Agent</h3>
            </div>
            <div class="delete-modal-body">
                <p>Are you sure you want to delete this delivery agent?</p>
                <div id="deleteAgentInfo" class="delete-agent-info">
                    <div class="delete-agent-name" id="deleteAgentName">Agent Name</div>
                    <div class="delete-agent-details" id="deleteAgentDetails">Agent Code: AG001 | Phone: 9876543210</div>
                </div>
                <div class="delete-warning">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>This action cannot be undone. All associated data will be permanently removed.</span>
                </div>
            </div>
            <div class="delete-modal-footer">
                <button class="delete-btn-cancel" onclick="closeDeleteModal()"><i class="fas fa-times"></i>
                    Cancel</button>
                <button class="delete-btn-confirm" id="confirmDeleteBtn"><i class="fas fa-trash-alt"></i> Delete
                    Permanently</button>
            </div>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMapCallback"
        async defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

    <script>
        let map = null;
        let marker = null;
        let mapInitialized = false;
        let deleteAgentId = null;

        function initMapCallback() {
            mapInitialized = true;
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

        function refreshAgents() {
            showLoading();
            window.location.reload();
        }

        function showAgentMap(id, name, lat, lng) {
            document.getElementById('modalAgentName').innerHTML =
                `<i class="fas fa-map-marker-alt"></i> ${name} - Current Location`;
            document.getElementById('agentMapModal').style.display = 'flex';

            setTimeout(() => {
                if (map) {
                    map = null;
                }
                const mapContainer = document.getElementById('agentMap');
                mapContainer.innerHTML = '';

                if (!window.google || !google.maps) {
                    mapContainer.innerHTML =
                        '<div style="height:100%; display:flex; align-items:center; justify-content:center;"><p>Loading maps...</p></div>';
                    return;
                }

                map = new google.maps.Map(mapContainer, {
                    center: {
                        lat: parseFloat(lat),
                        lng: parseFloat(lng)
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
                        lat: parseFloat(lat),
                        lng: parseFloat(lng)
                    },
                    map: map,
                    title: name,
                    icon: {
                        url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                        scaledSize: new google.maps.Size(48, 48)
                    },
                    animation: google.maps.Animation.DROP
                });

                new google.maps.InfoWindow({
                    content: `<b>${name}</b><br>Current Location<br><span style="font-size:0.8rem;">Lat: ${lat}, Lng: ${lng}</span>`
                }).open(map, marker);
            }, 100);
        }

        function closeAgentMap() {
            document.getElementById('agentMapModal').style.display = 'none';
            if (map) map = null;
        }

        function changeStatus(agentId, currentStatus) {
            const newStatus = prompt(`Change status from "${currentStatus}" to:`, 'available');
            if (!newStatus || !['available', 'busy', 'offline'].includes(newStatus)) {
                showToast('Invalid status. Use: available, busy, or offline', 'error');
                return;
            }

            showLoading();
            fetch(`/logistics/agents/${agentId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status: newStatus
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

        // ==================== DELETE AGENT FUNCTIONS ====================
        function showDeleteModal(id, name, agentCode, phone) {
            deleteAgentId = id;
            document.getElementById('deleteAgentName').innerHTML = name;
            document.getElementById('deleteAgentDetails').innerHTML = `Agent Code: ${agentCode} | Phone: ${phone}`;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            deleteAgentId = null;
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (!deleteAgentId) return;

            showLoading();
            fetch(`/logistics/agents/${deleteAgentId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        showToast('✅ Agent deleted successfully!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('❌ ' + (data.message || 'Error deleting agent'), 'error');
                    }
                    closeDeleteModal();
                })
                .catch(error => {
                    hideLoading();
                    showToast('❌ Error deleting agent', 'error');
                    console.error('Error:', error);
                    closeDeleteModal();
                });
        });

        window.onclick = function(e) {
            if (e.target === document.getElementById('agentMapModal')) closeAgentMap();
            if (e.target === document.getElementById('deleteModal')) closeDeleteModal();
        };

        setInterval(() => {
            if (!document.getElementById('loadingOverlay').style.display === 'flex') location.reload();
        }, 300000);
    </script>
@endsection
