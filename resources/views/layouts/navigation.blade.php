<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 sticky top-0 z-50">
    <style>
        /* ================= PROFESSIONAL NAVIGATION STYLES ================= */
        :root {
            --nav-bg: #ffffff;
            --nav-bg-dark: #1f2937;
            --nav-border: #e5e7eb;
            --nav-border-dark: #374151;
            --nav-text: #374151;
            --nav-text-dark: #e5e7eb;
            --nav-hover: #f3f4f6;
            --nav-hover-dark: #374151;
            --nav-active: #3b82f6;
            --nav-active-dark: #60a5fa;
            --nav-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --radius-md: 8px;
            --radius-lg: 12px;
        }

        /* ================= ANIMATIONS ================= */
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

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* ================= NAVIGATION STYLES ================= */
        .nav-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 clamp(16px, 4vw, 32px);
        }

        .nav-content {
            display: flex;
            justify-content: space-between;
            height: 70px;
        }

        /* Logo Section */
        .logo-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logo-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .logo-wrapper:hover {
            transform: scale(1.02);
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3);
        }

        .logo-text {
            font-size: 20px;
            font-weight: 700;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Desktop Navigation Links */
        .desktop-nav {
            display: flex;
            align-items: center;
            margin-left: 40px;
            gap: 8px;
        }

        @media (max-width: 768px) {
            .desktop-nav {
                display: none;
            }
        }

        .nav-link {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            border-radius: var(--radius-md);
            font-size: 15px;
            font-weight: 500;
            color: var(--nav-text);
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .nav-link:hover {
            background-color: var(--nav-hover);
            transform: translateY(-1px);
        }

        .nav-link.active {
            color: var(--nav-active);
            font-weight: 600;
            background-color: rgba(59, 130, 246, 0.1);
        }

        .dark .nav-link {
            color: var(--nav-text-dark);
        }

        .dark .nav-link:hover {
            background-color: var(--nav-hover-dark);
        }

        .dark .nav-link.active {
            color: var(--nav-active-dark);
            background-color: rgba(96, 165, 250, 0.15);
        }

        /* Desktop Dropdown */
        .desktop-dropdown {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-menu-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 500;
            color: var(--nav-text);
            background-color: transparent;
            border: 1px solid var(--nav-border);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .user-menu-btn:hover {
            background-color: var(--nav-hover);
            border-color: var(--nav-active);
        }

        .dark .user-menu-btn {
            color: var(--nav-text-dark);
            border-color: var(--nav-border-dark);
        }

        .dark .user-menu-btn:hover {
            background-color: var(--nav-hover-dark);
            border-color: var(--nav-active-dark);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }

        /* Dropdown Menu */
        .dropdown-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            min-width: 240px;
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--nav-border);
            overflow: hidden;
            animation: slideDown 0.2s ease;
            z-index: 1000;
        }

        .dark .dropdown-menu {
            background: var(--nav-bg-dark);
            border-color: var(--nav-border-dark);
        }

        .dropdown-item {
            display: block;
            padding: 12px 16px;
            color: var(--nav-text);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            border-bottom: 1px solid var(--nav-border);
        }

        .dropdown-item:last-child {
            border-bottom: none;
        }

        .dropdown-item:hover {
            background-color: var(--nav-hover);
            padding-left: 20px;
        }

        .dark .dropdown-item {
            color: var(--nav-text-dark);
            border-color: var(--nav-border-dark);
        }

        .dark .dropdown-item:hover {
            background-color: var(--nav-hover-dark);
        }

        .dropdown-item.logout {
            color: #ef4444;
        }

        .dropdown-item.logout:hover {
            background-color: #fee2e2;
        }

        .dark .dropdown-item.logout {
            color: #f87171;
        }

        .dark .dropdown-item.logout:hover {
            background-color: rgba(239, 68, 68, 0.2);
        }

        /* Hamburger Menu */
        .hamburger-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: var(--radius-md);
            color: var(--nav-text);
            background: transparent;
            border: 1px solid var(--nav-border);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .hamburger-btn:hover {
            background-color: var(--nav-hover);
            border-color: var(--nav-active);
        }

        .dark .hamburger-btn {
            color: var(--nav-text-dark);
            border-color: var(--nav-border-dark);
        }

        .dark .hamburger-btn:hover {
            background-color: var(--nav-hover-dark);
            border-color: var(--nav-active-dark);
        }

        /* Responsive Menu */
        .responsive-menu {
            padding: 16px 0;
            border-top: 1px solid var(--nav-border);
            animation: slideDown 0.3s ease;
        }

        .dark .responsive-menu {
            border-color: var(--nav-border-dark);
        }

        .responsive-nav {
            display: flex;
            flex-direction: column;
            padding: 8px 16px;
            gap: 4px;
        }

        .responsive-link {
            display: block;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            font-size: 15px;
            font-weight: 500;
            color: var(--nav-text);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .responsive-link:hover {
            background-color: var(--nav-hover);
            transform: translateX(4px);
        }

        .responsive-link.active {
            color: var(--nav-active);
            background-color: rgba(59, 130, 246, 0.1);
            font-weight: 600;
        }

        .dark .responsive-link {
            color: var(--nav-text-dark);
        }

        .dark .responsive-link:hover {
            background-color: var(--nav-hover-dark);
        }

        .dark .responsive-link.active {
            color: var(--nav-active-dark);
            background-color: rgba(96, 165, 250, 0.15);
        }

        /* User Info in Responsive Menu */
        .responsive-user {
            padding: 16px;
            border-bottom: 1px solid var(--nav-border);
            margin-bottom: 8px;
        }

        .dark .responsive-user {
            border-color: var(--nav-border-dark);
        }

        .responsive-user-name {
            font-weight: 600;
            font-size: 16px;
            color: var(--nav-text);
            margin-bottom: 4px;
        }

        .dark .responsive-user-name {
            color: var(--nav-text-dark);
        }

        .responsive-user-email {
            font-size: 13px;
            color: #6b7280;
        }

        .dark .responsive-user-email {
            color: #9ca3af;
        }

        /* ================= RESPONSIVE BREAKPOINTS ================= */
        
        /* Mobile Landscape (576px to 767px) */
        @media (max-width: 767px) {
            .logo-text {
                display: none;
            }

            .logo-icon {
                width: 35px;
                height: 35px;
                font-size: 18px;
            }
        }

        /* Mobile Portrait (up to 575px) */
        @media (max-width: 575px) {
            .nav-content {
                height: 60px;
            }

            .logo-icon {
                width: 32px;
                height: 32px;
                font-size: 16px;
            }

            .hamburger-btn {
                width: 40px;
                height: 40px;
            }
        }

        /* Extra Small Devices (up to 360px) */
        @media (max-width: 360px) {
            .nav-container {
                padding: 0 12px;
            }

            .logo-icon {
                width: 28px;
                height: 28px;
                font-size: 14px;
            }

            .hamburger-btn {
                width: 36px;
                height: 36px;
            }
        }
    </style>

    <!-- Primary Navigation Menu -->
    <div class="nav-container">
        <div class="nav-content">
            <div class="logo-section">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="logo-wrapper">
                        <div class="logo-icon">⚡</div>
                        <span class="logo-text">{{ config('app.name', 'INVOZA') }}</span>
                    </a>
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="desktop-nav">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="nav-link">
                        <span>📊</span>
                        <span style="margin-left: 8px;">{{ __('Dashboard') }}</span>
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown (Desktop) -->
            <div class="desktop-dropdown">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="user-menu-btn">
                            <div class="user-avatar">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div>{{ Auth::user()->name }}</div>
                            <div style="margin-left: 4px;">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="dropdown-menu">
                            <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                <span style="display: flex; align-items: center; gap: 8px;">
                                    <span>👤</span>
                                    {{ __('Profile') }}
                                </span>
                            </a>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item logout" style="width: 100%; text-align: left;">
                                    <span style="display: flex; align-items: center; gap: 8px;">
                                        <span>🚪</span>
                                        {{ __('Log Out') }}
                                    </span>
                                </button>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (Mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="hamburger-btn">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden responsive-menu">
        <div class="responsive-user">
            <div class="responsive-user-name">{{ Auth::user()->name }}</div>
            <div class="responsive-user-email">{{ Auth::user()->email }}</div>
        </div>

        <div class="responsive-nav">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="responsive-link">
                <span style="display: flex; align-items: center; gap: 8px;">
                    <span>📊</span>
                    {{ __('Dashboard') }}
                </span>
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('profile.edit')" class="responsive-link">
                <span style="display: flex; align-items: center; gap: 8px;">
                    <span>👤</span>
                    {{ __('Profile') }}
                </span>
            </x-responsive-nav-link>

            <!-- Authentication -->
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="responsive-link" style="width: 100%; text-align: left; color: #ef4444;">
                    <span style="display: flex; align-items: center; gap: 8px;">
                        <span>🚪</span>
                        {{ __('Log Out') }}
                    </span>
                </button>
            </form>
        </div>
    </div>

    <script>
        // Close responsive menu when clicking outside
        document.addEventListener('click', function(event) {
            const nav = document.querySelector('nav[x-data]');
            const menu = document.querySelector('.responsive-menu');
            const hamburger = document.querySelector('.hamburger-btn');

            if (window.innerWidth <= 768 && 
                nav && 
                menu && 
                hamburger && 
                !nav.contains(event.target) && 
                !hamburger.contains(event.target)) {
                if (typeof Alpine !== 'undefined') {
                    Alpine.store('nav', { open: false });
                }
            }
        });

        // Close menu on window resize
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                if (window.innerWidth > 768 && typeof Alpine !== 'undefined') {
                    Alpine.store('nav', { open: false });
                }
            }, 250);
        });
    </script>
</nav>