<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Add these BEFORE your content -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

    <meta name="google-maps-key" content="{{ config('services.google.maps_api_key') }}">
    <meta charset="UTF-8">
    <title>INVOZA One - @yield('page-title', 'Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ================= PROFESSIONAL DESIGN SYSTEM ================= */
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #8b5cf6;
            --success: #10b981;
            --success-dark: #059669;
            --danger: #ef4444;
            --danger-dark: #dc2626;
            --warning: #f59e0b;
            --info: #3b82f6;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --bg-light: #f9fafb;
            --bg-white: #ffffff;
            --bg-sidebar: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;
            --radius-2xl: 20px;
            --font-sans: 'Segoe UI', Tahoma, -apple-system, BlinkMacSystemFont, sans-serif;
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 0px;
            --header-height: 70px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-sans);
            background: var(--bg-light);
            color: var(--text-main);
            line-height: 1.5;
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* ================= SIDEBAR OVERLAY (Mobile) ================= */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* ================= MAIN SIDEBAR ================= */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: var(--bg-sidebar);
            color: #fff;
            padding: 25px 20px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 4px 0 30px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #4b5563 #1f2937;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Desktop - Sidebar always visible */
        @media (min-width: 992px) {
            #sidebar {
                transform: translateX(0);
            }
        }

        /* Mobile/Tablet - Sidebar hidden by default */
        @media (max-width: 991px) {
            #sidebar {
                transform: translateX(-100%);
                box-shadow: none;
            }

            #sidebar.active {
                transform: translateX(0);
                box-shadow: 4px 0 30px rgba(0, 0, 0, 0.3);
            }
        }

        /* Custom scrollbar for Webkit browsers */
        #sidebar::-webkit-scrollbar {
            width: 6px;
        }

        #sidebar::-webkit-scrollbar-track {
            background: #1f2937;
        }

        #sidebar::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 3px;
        }

        #sidebar::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }

        /* ========== SIDEBAR CLOSE BUTTON (Mobile Only) ========== */
        .sidebar-close {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-md);
            color: white;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.3s ease;
            z-index: 1001;
        }

        .sidebar-close:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(90deg);
        }

        @media (max-width: 991px) {
            .sidebar-close {
                display: flex;
            }
        }

        /* Logo Section */
        .sidebar-logo {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
        }

        .logo-icon {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            width: 60px;
            height: 60px;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 15px auto;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
        }

        .logo-text {
            margin: 0;
            font-size: 24px;
            font-weight: 900;
            letter-spacing: 0.5px;
            background: linear-gradient(135deg, #fff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .logo-subtitle {
            margin-top: 8px;
            font-size: 12px;
            color: #94a3b8;
            font-weight: 500;
        }

        /* User Info */
        .user-info {
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-lg);
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease;
        }

        .user-info:hover {
            transform: translateX(5px);
            background: rgba(255, 255, 255, 0.08);
        }

        .user-details {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
            width: 45px;
            height: 45px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        }

        .user-name {
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 2px;
            word-break: break-word;
        }

        .user-role {
            font-size: 12px;
            color: #cbd5e1;
            word-break: break-word;
        }

        /* Navigation Menu */
        .nav-menu {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin-bottom: 20px;
        }

        /* Dropdown Styles */
        .nav-item {
            width: 100%;
        }

        .nav-link {
            color: #e2e8f0;
            text-decoration: none;
            padding: 14px 16px;
            border-radius: var(--radius-lg);
            text-align: left;
            font-size: 15px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid transparent;
            font-weight: 500;
            width: 100%;
            background: transparent;
            border: none;
            cursor: pointer;
        }

        .nav-link:hover {
            background-color: rgba(99, 102, 241, 0.1);
            transform: translateX(8px);
            color: white;
        }

        .nav-link.active {
            background: rgba(99, 102, 241, 0.15);
            border-color: rgba(99, 102, 241, 0.3);
            font-weight: 700;
            transform: translateX(5px);
        }

        .nav-icon {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            background: #475569;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .nav-link:hover .nav-icon {
            background: var(--primary);
            transform: scale(1.1);
        }

        .nav-link.active .nav-icon {
            background: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3);
        }

        .dropdown-icon {
            margin-left: auto;
            transition: transform 0.3s ease;
            font-size: 12px;
        }

        .dropdown-icon.rotate {
            transform: rotate(180deg);
        }

        .dropdown-menu {
            list-style: none;
            padding-left: 52px;
            margin-top: 4px;
            margin-bottom: 4px;
            display: none;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            color: #cbd5e1;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            transition: all 0.2s ease;
            margin-bottom: 2px;
        }

        .dropdown-item:hover {
            background-color: rgba(99, 102, 241, 0.1);
            color: white;
            transform: translateX(8px);
        }

        .dropdown-item.active {
            background: rgba(99, 102, 241, 0.15);
            color: white;
            font-weight: 600;
        }

        .dropdown-item i {
            width: 20px;
            font-size: 14px;
            color: #94a3b8;
            transition: color 0.3s ease;
        }

        .dropdown-item:hover i {
            color: var(--primary);
        }

        /* Sidebar Footer */
        .sidebar-footer {
            margin-top: auto;
            padding-top: 20px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            opacity: 0.7;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* ================= TOP NAVBAR ================= */
        .top-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--header-height);
            background: var(--bg-white);
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            z-index: 998;
            transition: left 0.3s ease;
        }

        /* Desktop - Navbar starts after sidebar */
        @media (min-width: 992px) {
            .top-navbar {
                left: var(--sidebar-width);
            }
        }

        /* Navbar left section */
        .navbar-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* Menu Toggle Button (Mobile/Tablet only) */
        .menu-toggle {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: var(--radius-md);
            color: white;
            font-size: 20px;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .menu-toggle:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4);
        }

        @media (max-width: 991px) {
            .menu-toggle {
                display: flex;
            }
        }

        .page-title {
            font-size: clamp(18px, 3vw, 22px);
            font-weight: 700;
            color: var(--text-main);
            word-break: break-word;
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-detail {
            text-align: right;
        }

        .user-fullname {
            font-weight: 600;
            color: var(--text-main);
            font-size: 15px;
            word-break: break-word;
        }

        .user-badge {
            font-size: 13px;
            color: var(--text-muted);
            background: var(--bg-light);
            padding: 3px 10px;
            border-radius: 20px;
            display: inline-block;
            word-break: break-word;
        }

        .logout-btn {
            background: linear-gradient(135deg, var(--danger) 0%, var(--danger-dark) 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);
            white-space: nowrap;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.3);
        }

        /* ================= MAIN CONTENT ================= */
        .main-content {
            flex: 1;
            margin-top: var(--header-height);
            min-height: calc(100vh - var(--header-height));
            padding: 30px;
            background: var(--bg-light);
            overflow-y: auto;
            transition: margin-left 0.3s ease;
        }

        /* Desktop - Content starts after sidebar */
        @media (min-width: 992px) {
            .main-content {
                margin-left: var(--sidebar-width);
            }
        }

        /* ================= TOAST NOTIFICATION ================= */
        .toast-notification {
            position: fixed;
            top: 90px;
            right: 30px;
            background: var(--success);
            color: #fff;
            padding: 14px 20px;
            border-radius: var(--radius-md);
            font-weight: 600;
            box-shadow: var(--shadow-lg);
            z-index: 9999;
            animation: slideIn 0.3s ease;
            max-width: 90%;
            word-break: break-word;
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

        /* ================= ANIMATIONS ================= */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .nav-link,
        .dropdown-item,
        .user-info {
            animation: fadeIn 0.5s ease;
        }

        /* ================= RESPONSIVE BREAKPOINTS ================= */

        /* Large Desktop (1200px and above) */
        @media (min-width: 1200px) {
            .main-content {
                padding: 30px;
            }
        }

        /* Desktop (992px to 1199px) */
        @media (min-width: 992px) and (max-width: 1199px) {
            .main-content {
                padding: 25px;
            }
        }

        /* Tablet (768px to 991px) */
        @media (max-width: 991px) {
            .top-navbar {
                padding: 0 20px;
            }

            .main-content {
                padding: 20px;
            }

            .page-title {
                font-size: 18px;
            }

            .user-detail {
                display: none;
            }
        }

        /* Mobile Landscape (576px to 767px) */
        @media (max-width: 767px) {
            .top-navbar {
                padding: 0 15px;
            }

            .main-content {
                padding: 15px;
            }

            .user-section {
                gap: 10px;
            }

            .logout-btn {
                padding: 8px 15px;
                font-size: 13px;
            }

            .logout-btn span {
                font-size: 16px;
            }

            .toast-notification {
                top: 80px;
                right: 15px;
                left: 15px;
                max-width: none;
            }
        }

        /* Mobile Portrait (up to 575px) */
        @media (max-width: 575px) {
            .top-navbar {
                padding: 0 12px;
            }

            .page-title {
                font-size: 16px;
            }

            .logout-btn {
                padding: 6px 12px;
                font-size: 12px;
            }

            .logout-btn span {
                font-size: 14px;
            }

            .main-content {
                padding: 12px;
            }

            #sidebar {
                width: 260px;
            }

            .dropdown-menu {
                padding-left: 46px;
            }

            .dropdown-item {
                padding: 8px 12px;
                font-size: 13px;
            }

            .nav-link {
                padding: 12px 14px;
                font-size: 14px;
            }

            .nav-icon {
                width: 32px;
                height: 32px;
                font-size: 16px;
            }

            .user-avatar {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }
        }

        /* Extra Small Devices (up to 360px) */
        @media (max-width: 360px) {
            .top-navbar {
                padding: 0 8px;
            }

            .page-title {
                font-size: 14px;
            }

            .logout-btn {
                padding: 4px 8px;
                font-size: 11px;
            }

            .logout-btn span {
                font-size: 12px;
            }

            .main-content {
                padding: 8px;
            }

            #sidebar {
                width: 240px;
                padding: 20px 15px;
            }

            .nav-link {
                padding: 10px 12px;
                font-size: 13px;
            }

            .nav-icon {
                width: 28px;
                height: 28px;
                font-size: 14px;
            }

            .user-avatar {
                width: 35px;
                height: 35px;
                font-size: 16px;
            }

            .user-name {
                font-size: 14px;
            }

            .user-role {
                font-size: 11px;
            }
        }

        /* Print Styles */
        @media print {

            #sidebar,
            .top-navbar,
            .toast-notification,
            .menu-toggle,
            .sidebar-close,
            .sidebar-overlay {
                display: none !important;
            }

            .main-content {
                margin-left: 0;
                margin-top: 0;
                padding: 0;
            }
        }

        /* DataTable Custom Styling */
        .dataTables_wrapper .dataTables_filter input {
            border: 2px solid var(--border) !important;
            border-radius: var(--radius-md) !important;
            padding: 8px 12px !important;
            margin-left: 10px !important;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 2px solid var(--border) !important;
            border-radius: var(--radius-md) !important;
            padding: 6px 10px !important;
        }

        .dataTables_wrapper .dt-buttons {
            margin-bottom: 10px;
        }

        .dt-button {
            background: var(--primary) !important;
            color: white !important;
            border: none !important;
            border-radius: var(--radius-sm) !important;
            padding: 8px 16px !important;
            margin-right: 5px !important;
            cursor: pointer !important;
        }

        .dt-button:hover {
            background: var(--primary-dark) !important;
        }

        @media (max-width: 767px) {
            .dataTables_wrapper .dt-buttons {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }

            .dt-button {
                width: 100%;
                margin-right: 0 !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    {{-- ================= SIDEBAR OVERLAY (Mobile) ================= --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- ================= MAIN SIDEBAR ================= --}}
    <div id="sidebar">
        {{-- Close Button (Mobile only) --}}
        <button class="sidebar-close" id="sidebarClose">
            <i class="fas fa-times"></i>
        </button>

        {{-- Logo --}}
        <div class="sidebar-logo">
            <div class="logo-icon">⚡</div>
            <h2 class="logo-text">INVOZA One</h2>
            <div class="logo-subtitle">Business Intelligence System</div>
        </div>

        {{-- User Info --}}
        @auth
            <div class="user-info">
                <div class="user-details">
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role">{{ ucfirst(auth()->user()->role) }}</div>
                    </div>
                </div>
            </div>
        @endauth

        {{-- Navigation Menu --}}
        <div class="nav-menu">
            {{-- Common Links for All Users --}}
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon">📊</span>
                Dashboard
            </a>

            {{-- ========== LOGISTICS DROPDOWN ========== --}}
            @if (in_array(auth()->user()->role, ['admin', 'logistics', 'staff']))
                <div class="nav-item">
                    <button class="nav-link" onclick="toggleDropdown('logisticsDropdown')" id="logisticsBtn">
                        <span class="nav-icon">📦</span>
                        <span>Logistics</span>
                        <span class="dropdown-icon" id="logisticsIcon">▼</span>
                    </button>
                    <ul class="dropdown-menu" id="logisticsDropdown">
                        <a href="{{ route('logistics.shipments.index') }}"
                            class="dropdown-item {{ request()->routeIs('logistics.shipments.index') ? 'active' : '' }}">
                            <i class="fas fa-box"></i> All Shipments
                        </a>
                        <a href="{{ route('logistics.shipments.create') }}"
                            class="dropdown-item {{ request()->routeIs('logistics.shipments.create') ? 'active' : '' }}">
                            <i class="fas fa-plus-circle"></i> New Shipment
                        </a>
                        <a href="{{ route('logistics.agents.index') }}"
                            class="dropdown-item {{ request()->routeIs('logistics.agents.index') ? 'active' : '' }}">
                            <i class="fas fa-users"></i> Delivery Agents
                        </a>
                        <a href="{{ route('logistics.agents.create') }}"
                            class="dropdown-item {{ request()->routeIs('logistics.agents.create') ? 'active' : '' }}">
                            <i class="fas fa-user-plus"></i> Add Agent
                        </a>
                        <a href="{{ route('logistics.service-areas') }}"
                            class="dropdown-item {{ request()->routeIs('logistics.service-areas') ? 'active' : '' }}">
                            <i class="fas fa-map-marked-alt"></i> Service Areas
                        </a>
                        <a href="{{ route('logistics.route-planner') }}"
                            class="dropdown-item {{ request()->routeIs('logistics.route-planner') ? 'active' : '' }}">
                            <i class="fas fa-route"></i> Route Planner
                        </a>
                        <a href="{{ route('logistics.reports') }}"
                            class="dropdown-item {{ request()->routeIs('logistics.reports') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar"></i> Reports
                        </a>
                    </ul>
                </div>
            @endif

            {{-- ========== AGENT SPECIFIC MENU ========== --}}
            @if (auth()->user()->role === 'delivery_agent')
                {{-- Delivery Dashboard --}}
                <a href="{{ route('agent.dashboard') }}"
                    class="nav-link {{ request()->routeIs('agent.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">🏍️</span>
                    Delivery Dashboard
                </a>

                {{-- Active Deliveries --}}
                <div class="nav-item">
                    <button class="nav-link" onclick="toggleDropdown('deliveryDropdown')" id="deliveryBtn">
                        <span class="nav-icon">🚚</span>
                        <span>Deliveries</span>
                        <span class="dropdown-icon" id="deliveryIcon">▼</span>
                    </button>
                    <ul class="dropdown-menu" id="deliveryDropdown">
                        <a href="{{ route('agent.deliveries.active') }}"
                            class="dropdown-item {{ request()->routeIs('agent.deliveries.active') ? 'active' : '' }}">
                            <i class="fas fa-play-circle"></i> Active Deliveries
                        </a>
                        <a href="{{ route('agent.deliveries.history') }}"
                            class="dropdown-item {{ request()->routeIs('agent.deliveries.history') ? 'active' : '' }}">
                            <i class="fas fa-history"></i> Delivery History
                        </a>
                        <a href="{{ route('agent.deliveries.assigned') }}"
                            class="dropdown-item {{ request()->routeIs('agent.deliveries.assigned') ? 'active' : '' }}">
                            <i class="fas fa-list"></i> Assigned Shipments
                        </a>
                    </ul>
                </div>

                {{-- Live Tracking --}}
                @if (isset($activeShipment))
                    <a href="{{ route('agent.tracking.live', $activeShipment->id) }}"
                        class="nav-link {{ request()->routeIs('agent.tracking.live') ? 'active' : '' }}">
                        <span class="nav-icon">📍</span>
                        Live Tracking
                        @if ($activeShipment)
                            <span class="badge"
                                style="background: #10b981; margin-left: auto; font-size: 10px;">Active</span>
                        @endif
                    </a>
                @endif

                {{-- Performance --}}
                <a href="{{ route('agent.performance.index') }}"
                    class="nav-link {{ request()->routeIs('agent.performance*') ? 'active' : '' }}">
                    <span class="nav-icon">📈</span>
                    My Performance
                </a>

                {{-- Earnings --}}
                <a href="{{ route('agent.earnings') }}"
                    class="nav-link {{ request()->routeIs('agent.earnings') ? 'active' : '' }}">
                    <span class="nav-icon">💰</span>
                    Earnings
                </a>

                {{-- Profile Settings --}}
                <a href="{{ route('agent.profile') }}"
                    class="nav-link {{ request()->routeIs('agent.profile') ? 'active' : '' }}">
                    <span class="nav-icon">⚙️</span>
                    Profile Settings
                </a>

                {{-- Support --}}
                <a href="{{ route('agent.support') }}"
                    class="nav-link {{ request()->routeIs('agent.support') ? 'active' : '' }}">
                    <span class="nav-icon">🆘</span>
                    Support
                </a>
            @endif

            {{-- ========== AGENT APPROVAL DROPDOWN (ADMIN ONLY) ========== --}}
            @if (auth()->check() && auth()->user()->role === 'admin')
                <div class="nav-item">
                    <button class="nav-link" onclick="toggleDropdown('approvalDropdown')" id="approvalBtn">
                        <span class="nav-icon">✅</span>
                        <span>Approvals</span>
                        <span class="dropdown-icon" id="approvalIcon">▼</span>
                    </button>
                    <ul class="dropdown-menu" id="approvalDropdown">
                        <a href="{{ route('admin.staff.approval') }}"
                            class="dropdown-item {{ request()->routeIs('admin.staff.approval') ? 'active' : '' }}">
                            <i class="fas fa-user-tie"></i> Staff Approval
                        </a>
                        <a href="{{ route('admin.agent.approvals') }}"
                            class="dropdown-item {{ request()->routeIs('admin.agent.approvals') ? 'active' : '' }}">
                            <i class="fas fa-motorcycle"></i> Agent Approval
                        </a>
                    </ul>
                </div>

                {{-- Track All Agents (Admin) --}}
                <a href="{{ route('admin.tracking.agents') }}"
                    class="nav-link {{ request()->routeIs('admin.tracking.agents') ? 'active' : '' }}">
                    <span class="nav-icon">🗺️</span>
                    Track All Agents
                </a>
            @endif

            {{-- ADMIN MENU --}}
            @if (auth()->check() && auth()->user()->role === 'admin')
                <a href="{{ route('employees.index') }}"
                    class="nav-link {{ request()->routeIs('employees*') ? 'active' : '' }}">
                    <span class="nav-icon">👥</span>
                    Employees
                </a>

                <a href="{{ route('inventory.index') }}"
                    class="nav-link {{ request()->routeIs('inventory*') ? 'active' : '' }}">
                    <span class="nav-icon">📦</span>
                    Inventory
                </a>

                <a href="{{ route('customers.index') }}"
                    class="nav-link {{ request()->routeIs('customers*') ? 'active' : '' }}">
                    <span class="nav-icon">👤</span>
                    Customers
                </a>

                <a href="{{ route('sales.index') }}"
                    class="nav-link {{ request()->routeIs('sales*') ? 'active' : '' }}">
                    <span class="nav-icon">💰</span>
                    Sales
                </a>

                <a href="{{ route('purchases.index') }}"
                    class="nav-link {{ request()->routeIs('purchases*') ? 'active' : '' }}">
                    <span class="nav-icon">🛒</span>
                    Purchases
                </a>

                <a href="{{ route('attendance.manage') }}"
                    class="nav-link {{ request()->routeIs('attendance.manage') ? 'active' : '' }}">
                    <span class="nav-icon">🕒</span>
                    Attendance
                </a>

                <a href="{{ route('leaves.manage') }}"
                    class="nav-link {{ request()->routeIs('leaves.manage') ? 'active' : '' }}">
                    <span class="nav-icon">✅</span>
                    Manage Leaves
                </a>

                <a href="{{ route('hr.dashboard') }}"
                    class="nav-link {{ request()->routeIs('hr.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">👨‍💼</span>
                    HR Dashboard
                </a>
            @endif

            {{-- HR MENU --}}
            @if (auth()->check() && auth()->user()->role === 'hr')
                <a href="{{ route('hr.dashboard') }}"
                    class="nav-link {{ request()->routeIs('hr.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">👨‍💼</span>
                    HR Dashboard
                </a>

                <a href="{{ route('employees.index') }}"
                    class="nav-link {{ request()->routeIs('employees*') ? 'active' : '' }}">
                    <span class="nav-icon">👥</span>
                    Employees
                </a>

                <a href="{{ route('attendance.manage') }}"
                    class="nav-link {{ request()->routeIs('attendance*') ? 'active' : '' }}">
                    <span class="nav-icon">🕒</span>
                    Attendance
                </a>

                <a href="{{ route('attendance.mark') }}"
                    class="nav-link {{ request()->routeIs('attendance.mark') ? 'active' : '' }}">
                    <span class="nav-icon">📝</span>
                    Mark Attendance
                </a>

                <a href="{{ route('leaves.manage') }}"
                    class="nav-link {{ request()->routeIs('leaves*') ? 'active' : '' }}">
                    <span class="nav-icon">✅</span>
                    Manage Leaves
                </a>

                <a href="{{ route('employees.create') }}"
                    class="nav-link {{ request()->routeIs('employees.create') ? 'active' : '' }}">
                    <span class="nav-icon">➕</span>
                    Add Employee
                </a>

                <a href="{{ route('reports.attendance') }}"
                    class="nav-link {{ request()->routeIs('reports*') ? 'active' : '' }}">
                    <span class="nav-icon">📊</span>
                    HR Reports
                </a>

                <a href="{{ route('hr.analytics') }}"
                    class="nav-link {{ request()->routeIs('hr.analytics') ? 'active' : '' }}">
                    <span class="nav-icon">📈</span>
                    HR Analytics
                </a>
            @endif

            {{-- STAFF MENU --}}
            @if (auth()->check() && auth()->user()->role === 'staff')
                <a href="{{ route('attendance.my') }}"
                    class="nav-link {{ request()->routeIs('attendance.my') ? 'active' : '' }}">
                    <span class="nav-icon">🕒</span>
                    My Attendance
                </a>

                <a href="{{ route('leaves.my') }}"
                    class="nav-link {{ request()->routeIs('leaves.my') ? 'active' : '' }}">
                    <span class="nav-icon">📝</span>
                    My Leaves
                </a>

                <a href="{{ route('sales.index') }}"
                    class="nav-link {{ request()->routeIs('sales*') ? 'active' : '' }}">
                    <span class="nav-icon">💰</span>
                    Sales
                </a>

                <a href="{{ route('customers.index') }}"
                    class="nav-link {{ request()->routeIs('customers*') ? 'active' : '' }}">
                    <span class="nav-icon">👤</span>
                    Customers
                </a>
            @endif
        </div>

        {{-- Footer --}}
        <div class="sidebar-footer">
            INVOZA One v1.0 • {{ date('Y') }}
        </div>
    </div>

    {{-- ================= TOP NAVBAR ================= --}}
    <div class="top-navbar">
        <div class="navbar-left">
            {{-- Menu Toggle Button (Mobile/Tablet) --}}
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>

            <div class="page-title">
                @yield('page-title', 'Dashboard')
            </div>
        </div>

        @auth
            <div class="user-section">
                <div class="user-detail">
                    <div class="user-fullname">{{ auth()->user()->name }}</div>
                    <div class="user-badge">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <span>🚪</span>
                        Logout
                    </button>
                </form>
            </div>
        @endauth
    </div>

    {{-- ================= MAIN CONTENT ================= --}}
    <div class="main-content">
        @yield('content')
    </div>

    {{-- ================= TOAST NOTIFICATION ================= --}}
    @if (session('success'))
        <div class="toast-notification">
            ✅ {{ session('success') }}
        </div>
        <script>
            setTimeout(() => {
                document.querySelector('.toast-notification')?.remove();
            }, 3000);
        </script>
    @endif

    {{-- ================= JAVASCRIPT LIBRARIES ================= --}}
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- DataTables Core -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- DataTables Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <!-- DataTables Responsive -->
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <!-- Chart.js (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- ================= CUSTOM JAVASCRIPT ================= --}}
    <script>
        // DOM Elements
        const sidebar = document.getElementById('sidebar');
        const menuToggle = document.getElementById('menuToggle');
        const sidebarClose = document.getElementById('sidebarClose');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        // Function to open sidebar
        function openSidebar() {
            sidebar.classList.add('active');
            sidebarOverlay.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        // Function to close sidebar
        function closeSidebar() {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = ''; // Restore scrolling
        }

        // Toggle sidebar on menu button click
        if (menuToggle) {
            menuToggle.addEventListener('click', openSidebar);
        }

        // Close sidebar on close button click
        if (sidebarClose) {
            sidebarClose.addEventListener('click', closeSidebar);
        }

        // Close sidebar on overlay click
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }

        // Close sidebar on escape key press
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('active')) {
                closeSidebar();
            }
        });

        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth > 991) {
                    // Desktop view - sidebar always visible
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }, 250);
        });

        // Dropdown toggle function
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            const icon = document.getElementById(id.replace('Dropdown', 'Icon'));

            if (dropdown) {
                dropdown.classList.toggle('show');
            }

            if (icon) {
                icon.classList.toggle('rotate');
            }

            // Close other dropdowns
            const allDropdowns = document.querySelectorAll('.dropdown-menu');
            allDropdowns.forEach(d => {
                if (d.id !== id && d.classList.contains('show')) {
                    d.classList.remove('show');
                    const otherIcon = document.getElementById(d.id.replace('Dropdown', 'Icon'));
                    if (otherIcon) {
                        otherIcon.classList.remove('rotate');
                    }
                }
            });
        }

        // Auto-open dropdown if any child is active
        document.addEventListener('DOMContentLoaded', function() {
            const logisticsDropdown = document.getElementById('logisticsDropdown');
            const logisticsIcon = document.getElementById('logisticsIcon');
            const approvalDropdown = document.getElementById('approvalDropdown');
            const approvalIcon = document.getElementById('approvalIcon');
            const deliveryDropdown = document.getElementById('deliveryDropdown');
            const deliveryIcon = document.getElementById('deliveryIcon');

            if (logisticsDropdown) {
                const activeItems = logisticsDropdown.querySelectorAll('.active');
                if (activeItems.length > 0) {
                    logisticsDropdown.classList.add('show');
                    if (logisticsIcon) {
                        logisticsIcon.classList.add('rotate');
                    }
                }
            }

            if (approvalDropdown) {
                const activeApprovalItems = approvalDropdown.querySelectorAll('.active');
                if (activeApprovalItems.length > 0) {
                    approvalDropdown.classList.add('show');
                    if (approvalIcon) {
                        approvalIcon.classList.add('rotate');
                    }
                }
            }

            if (deliveryDropdown) {
                const activeDeliveryItems = deliveryDropdown.querySelectorAll('.active');
                if (activeDeliveryItems.length > 0) {
                    deliveryDropdown.classList.add('show');
                    if (deliveryIcon) {
                        deliveryIcon.classList.add('rotate');
                    }
                }
            }

            // Close sidebar when clicking a link on mobile
            if (window.innerWidth <= 991) {
                document.querySelectorAll('#sidebar a, #sidebar button').forEach(link => {
                    link.addEventListener('click', function() {
                        setTimeout(closeSidebar, 200);
                    });
                });
            }
        });

        // Basic DataTable Initialization (will be overridden by specific page scripts)
        document.addEventListener('DOMContentLoaded', function() {
            if ($.fn.DataTable && $('.datatable').length) {
                $('.datatable').DataTable({
                    pageLength: 10,
                    responsive: true,
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search..."
                    }
                });
            }
        });
    </script>

    {{-- ================= PAGE SPECIFIC SCRIPTS ================= --}}
    @stack('scripts')
</body>

</html>
