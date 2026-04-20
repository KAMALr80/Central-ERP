@extends('layouts.app')

@section('page-title', 'Product Details - ' . ($product->name ?? ''))

@section('content')
<style>
    /* ================= PROFESSIONAL DESIGN SYSTEM ================= */
    :root {
        --primary: #667eea;
        --primary-dark: #5a67d8;
        --secondary: #764ba2;
        --success: #10b981;
        --success-dark: #059669;
        --danger: #ef4444;
        --danger-dark: #dc2626;
        --warning: #f59e0b;
        --info: #3b82f6;
        --info-dark: #1d4ed8;
        --purple: #8b5cf6;
        --text-main: #1f2937;
        --text-muted: #6b7280;
        --border: #e5e7eb;
        --bg-light: #f9fafb;
        --bg-white: #ffffff;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --shadow-xl: 0 10px 40px rgba(0, 0, 0, 0.1);
        --radius-sm: 6px;
        --radius-md: 8px;
        --radius-lg: 12px;
        --radius-xl: 16px;
        --radius-2xl: 20px;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        color: var(--text-main);
        line-height: 1.5;
    }

    /* ================= MAIN CONTAINER ================= */
    .product-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: clamp(16px, 3vw, 20px);
        margin-left: 260px;
        margin-top: 70px;
        transition: margin 0.3s ease;
        width: calc(100% - 260px);
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        width: 100%;
    }

    /* ================= HEADER CARD ================= */
    .header-card {
        background: var(--bg-white);
        border-radius: var(--radius-2xl);
        padding: clamp(20px, 4vw, 30px);
        margin-bottom: 30px;
        box-shadow: var(--shadow-xl);
        border: 1px solid var(--border);
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .header-left {
        flex: 1;
        min-width: 280px;
    }

    .header-title {
        margin: 0;
        font-size: clamp(24px, 5vw, 28px);
        font-weight: 800;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .header-icon {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        width: clamp(40px, 8vw, 50px);
        height: clamp(40px, 8vw, 50px);
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: clamp(20px, 4vw, 24px);
        color: white;
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        flex-shrink: 0;
    }

    .header-subtitle {
        margin: 10px 0 0 0;
        color: var(--text-muted);
        font-size: clamp(14px, 3vw, 16px);
        word-break: break-word;
    }

    .btn-back {
        background: var(--bg-light);
        color: #4b5563;
        text-decoration: none;
        padding: clamp(10px, 2.5vw, 12px) clamp(20px, 4vw, 24px);
        border-radius: var(--radius-lg);
        font-weight: 600;
        font-size: clamp(13px, 2.5vw, 14px);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .btn-back:hover {
        background: #e5e7eb;
        transform: translateY(-1px);
    }

    /* ================= MAIN CARD ================= */
    .main-card {
        background: var(--bg-white);
        border-radius: var(--radius-2xl);
        overflow: hidden;
        box-shadow: var(--shadow-xl);
        border: 1px solid var(--border);
        width: 100%;
    }

    /* ================= IMAGE & BASIC INFO SECTION ================= */
    .info-section {
        display: grid;
        grid-template-columns: minmax(250px, 300px) 1fr;
        gap: clamp(20px, 4vw, 30px);
        padding: clamp(20px, 4vw, 30px);
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 2px solid var(--border);
    }

    /* ================= IMAGE COLUMN ================= */
    .image-column {
        background: white;
        border-radius: var(--radius-lg);
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .image-container {
        position: relative;
        text-align: center;
    }

    .product-image {
        max-width: 100%;
        max-height: 250px;
        object-fit: contain;
        border-radius: var(--radius-lg);
        cursor: pointer;
        transition: transform 0.3s;
    }

    .product-image:hover {
        transform: scale(1.02);
    }

    .image-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        color: white;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .image-badge.external {
        background: var(--purple);
    }

    .image-badge.local {
        background: var(--success);
    }

    .no-image {
        width: 100%;
        height: 250px;
        background: var(--bg-light);
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 10px;
        border: 2px dashed var(--border);
    }

    .no-image-icon {
        font-size: 64px;
    }

    .no-image-text {
        color: #9ca3af;
        font-weight: 500;
        margin: 0;
    }

    .no-image-subtext {
        color: #d1d5db;
        font-size: 12px;
        margin: 0;
    }

    /* ================= BASIC INFO COLUMN ================= */
    .info-column {
        display: flex;
        flex-direction: column;
    }

    .product-name {
        margin: 0 0 10px 0;
        font-size: clamp(24px, 5vw, 32px);
        font-weight: 800;
        color: var(--text-main);
        word-break: break-word;
    }

    .badge-group {
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .badge {
        padding: 6px 16px;
        border-radius: 30px;
        font-weight: 600;
        font-size: clamp(12px, 2.5vw, 14px);
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .badge.category {
        background: #e0e7ff;
        color: #3730a3;
    }

    .badge.code {
        background: var(--bg-light);
        color: #4b5563;
    }

    /* ================= QUICK STATS GRID ================= */
    .quick-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: white;
        padding: 15px;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .stat-label {
        font-size: 12px;
        color: var(--text-muted);
        margin-bottom: 5px;
    }

    .stat-value {
        font-size: clamp(20px, 4vw, 24px);
        font-weight: 800;
        line-height: 1.2;
        word-break: break-word;
    }

    .stat-value.price {
        color: var(--success-dark);
    }

    .stat-value.quantity {
        color: var(--text-main);
    }

    .stat-value.total {
        color: var(--purple);
        font-size: clamp(18px, 3.5vw, 20px);
    }

    .stat-value.quantity.low {
        color: var(--danger);
    }

    /* ================= STOCK STATUS BADGE ================= */
    .stock-status {
        display: inline-flex;
        align-items: center;
        padding: 10px 20px;
        border-radius: 30px;
        font-weight: 600;
        gap: 8px;
        font-size: clamp(13px, 2.5vw, 14px);
        width: fit-content;
    }

    .stock-status.low {
        background: #fee2e2;
        color: var(--danger-dark);
    }

    .stock-status.normal {
        background: #fef3c7;
        color: var(--warning);
    }

    .stock-status.high {
        background: #d1fae5;
        color: var(--success-dark);
    }

    /* ================= DETAILS GRID ================= */
    .details-grid {
        padding: clamp(20px, 4vw, 30px);
    }

    .two-column-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 30px;
    }

    /* ================= SECTION TITLES ================= */
    .section-title {
        margin: 0 0 20px 0;
        font-size: clamp(16px, 3vw, 18px);
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .title-underline {
        width: 4px;
        height: 20px;
        border-radius: 2px;
        display: inline-block;
    }

    .title-underline.blue {
        background: var(--primary);
    }

    .title-underline.purple {
        background: var(--purple);
    }

    .title-underline.orange {
        background: var(--warning);
    }

    /* ================= INFO CARD ================= */
    .info-card {
        background: var(--bg-light);
        padding: 25px;
        border-radius: var(--radius-lg);
        margin-bottom: 25px;
    }

    .info-row {
        display: flex;
        border-bottom: 1px solid var(--border);
        padding-bottom: 10px;
        margin-bottom: 10px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .info-row:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .info-label {
        width: 120px;
        font-weight: 600;
        color: #4b5563;
        flex-shrink: 0;
    }

    .info-value {
        color: var(--text-main);
        font-weight: 500;
        word-break: break-word;
        flex: 1;
    }

    /* ================= DESCRIPTION ================= */
    .description-card {
        margin-top: 25px;
    }

    .description-content {
        background: var(--bg-light);
        padding: 20px;
        border-radius: var(--radius-lg);
        color: #4b5563;
        line-height: 1.8;
        border-left: 4px solid var(--warning);
        word-break: break-word;
    }

    /* ================= STOCK ANALYSIS ================= */
    .analysis-card {
        background: var(--bg-light);
        padding: 25px;
        border-radius: var(--radius-lg);
    }

    /* ================= STOCK LEVEL INDICATOR ================= */
    .stock-level {
        margin-bottom: 30px;
    }

    .level-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .level-label {
        font-weight: 600;
        color: #4b5563;
    }

    .level-value {
        font-weight: 700;
        color: var(--text-main);
    }

    .progress-bar {
        height: 12px;
        background: var(--border);
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 5px;
    }

    .progress-fill {
        height: 100%;
        border-radius: 6px;
        transition: width 0.5s ease;
    }

    .scale-labels {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: #9ca3af;
    }

    /* ================= STOCK STATUS CARDS ================= */
    .status-cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 25px;
    }

    .status-card {
        background: white;
        padding: 15px;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        text-align: center;
        transition: all 0.3s ease;
    }

    .status-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .status-card-label {
        font-size: 12px;
        color: var(--text-muted);
        margin-bottom: 5px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: clamp(12px, 2.5vw, 14px);
    }

    .status-badge.low {
        background: #fee2e2;
        color: var(--danger-dark);
    }

    .status-badge.normal {
        background: #fef3c7;
        color: var(--warning);
    }

    .status-badge.high {
        background: #d1fae5;
        color: var(--success-dark);
    }

    .reorder-point {
        font-weight: 700;
        color: var(--text-main);
    }

    /* ================= INVENTORY VALUE CARD ================= */
    .value-card {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        padding: 25px;
        border-radius: var(--radius-lg);
        color: white;
        text-align: center;
        margin-bottom: 25px;
    }

    .value-label {
        font-size: 14px;
        opacity: 0.9;
        margin-bottom: 10px;
    }

    .value-amount {
        font-size: clamp(28px, 5vw, 36px);
        font-weight: 800;
        margin-bottom: 5px;
        word-break: break-word;
    }

    .value-desc {
        font-size: 14px;
        opacity: 0.8;
        word-break: break-word;
    }

    /* ================= QUICK ACTIONS ================= */
    .quick-actions {
        display: flex;
        gap: 10px;
        margin-top: 25px;
    }

    .btn-quick {
        flex: 1;
        background: white;
        text-decoration: none;
        padding: 12px;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: clamp(13px, 2.5vw, 14px);
        text-align: center;
        border: 2px solid;
        transition: all 0.3s;
    }

    .btn-quick.edit {
        color: var(--info);
        border-color: #e0e7ff;
    }

    .btn-quick.edit:hover {
        background: #e0e7ff;
    }

    .btn-quick.add {
        color: var(--success);
        border-color: #d1fae5;
    }

    .btn-quick.add:hover {
        background: #d1fae5;
    }

    /* ================= FOOTER ACTIONS ================= */
    .footer-actions {
        padding: 25px 30px;
        border-top: 2px solid #f3f4f6;
        background: #f9fafb;
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .btn-footer {
        padding: clamp(12px, 2.5vw, 14px) clamp(24px, 4vw, 30px);
        border-radius: var(--radius-lg);
        font-weight: 600;
        font-size: clamp(14px, 2.5vw, 15px);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-footer.edit {
        background: linear-gradient(135deg, var(--info) 0%, var(--info-dark) 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.25);
    }

    .btn-footer.edit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.35);
    }

    .btn-footer.delete {
        background: linear-gradient(135deg, var(--danger) 0%, var(--danger-dark) 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.25);
    }

    .btn-footer.delete:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.35);
    }

    /* ================= MODAL ================= */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.9);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-close {
        position: absolute;
        top: 20px;
        right: 30px;
        color: white;
        font-size: 40px;
        cursor: pointer;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        transition: all 0.3s;
    }

    .modal-close:hover {
        background: rgba(255,255,255,0.2);
    }

    .modal-image {
        max-width: 90%;
        max-height: 90%;
        border-radius: var(--radius-lg);
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    }

    /* ================= RESPONSIVE BREAKPOINTS ================= */
    
    /* Large Desktop (1200px and above) */
    @media (min-width: 1200px) {
        .two-column-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Desktop (992px to 1199px) */
    @media (max-width: 1199px) {
        .product-page {
            margin-left: 220px;
            width: calc(100% - 220px);
        }
    }

    /* Tablet (768px to 991px) */
    @media (max-width: 991px) {
        .product-page {
            margin-left: 0;
            margin-top: 60px;
            width: 100%;
            padding: 15px;
        }

        .info-section {
            grid-template-columns: 1fr;
        }

        .image-column {
            max-width: 400px;
            margin: 0 auto;
        }

        .header-content {
            flex-direction: column;
            align-items: flex-start;
        }

        .btn-back {
            align-self: flex-start;
        }

        .two-column-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Mobile Landscape (576px to 767px) */
    @media (max-width: 767px) {
        .product-page {
            padding: 10px;
        }

        .quick-stats-grid {
            grid-template-columns: 1fr;
        }

        .status-cards {
            grid-template-columns: 1fr;
        }

        .quick-actions {
            flex-direction: column;
        }

        .btn-quick {
            width: 100%;
        }

        .footer-actions {
            flex-direction: column;
        }

        .btn-footer {
            width: 100%;
            justify-content: center;
        }

        .info-row {
            flex-direction: column;
            gap: 4px;
        }

        .info-label {
            width: 100%;
        }
    }

    /* Mobile Portrait (up to 575px) */
    @media (max-width: 575px) {
        .product-page {
            padding: 8px;
        }

        .badge-group {
            flex-direction: column;
            align-items: flex-start;
        }

        .badge {
            width: 100%;
            justify-content: center;
        }

        .stat-value {
            font-size: 20px;
        }

        .value-amount {
            font-size: 28px;
        }

        .info-card {
            padding: 20px;
        }

        .analysis-card {
            padding: 20px;
        }

        .modal-close {
            top: 10px;
            right: 10px;
            width: 40px;
            height: 40px;
            font-size: 30px;
        }
    }

    /* Extra Small Devices (up to 360px) */
    @media (max-width: 360px) {
        .product-page {
            padding: 5px;
        }

        .header-title {
            font-size: 22px;
        }

        .product-name {
            font-size: 22px;
        }

        .stat-value {
            font-size: 18px;
        }

        .value-amount {
            font-size: 24px;
        }

        .info-card {
            padding: 16px;
        }

        .analysis-card {
            padding: 16px;
        }

        .btn-footer {
            padding: 10px 20px;
            font-size: 13px;
        }
    }

    /* Print Styles */
    @media print {
        .product-page {
            margin: 0;
            padding: 0;
            background: white;
        }

        .btn-back,
        .btn-quick,
        .btn-footer,
        .modal-overlay {
            display: none !important;
        }

        .info-section {
            background: white;
        }

        .badge {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .value-card {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }

    /* Scrollbar Styling */
    ::-webkit-scrollbar {
        width: 8px;
    }
    ::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<div class="product-page">
    <div class="container">
        <!-- Header -->
        <div class="header-card">
            <div class="header-content">
                <div class="header-left">
                    <h1 class="header-title">
                        <span class="header-icon">📦</span>
                        Product Details
                    </h1>
                    <p class="header-subtitle">
                        View complete information about the product
                    </p>
                </div>
                <a href="{{ route('inventory.index') }}" class="btn-back">
                    ← Back to Inventory
                </a>
            </div>
        </div>

        <!-- Product Details Card -->
        <div class="main-card">
            <!-- Image and Basic Info Section -->
            <div class="info-section">
                <!-- Image Column -->
                <div class="image-column">
                    @if($product->image)
                        @php
                            $imageUrl = filter_var($product->image, FILTER_VALIDATE_URL)
                                ? $product->image
                                : asset('storage/'.$product->image);
                        @endphp
                        <div class="image-container">
                            <img src="{{ $imageUrl }}"
                                 alt="{{ $product->name }}"
                                 class="product-image"
                                 onclick="openFullImage('{{ $imageUrl }}')">
                            @if(filter_var($product->image, FILTER_VALIDATE_URL))
                                <span class="image-badge external">
                                    <span>🔗</span> External URL
                                </span>
                            @else
                                <span class="image-badge local">
                                    <span>📁</span> Local Storage
                                </span>
                            @endif
                        </div>
                    @else
                        <div class="no-image">
                            <span class="no-image-icon">📦</span>
                            <p class="no-image-text">No Image Available</p>
                            <p class="no-image-subtext">Upload an image to see it here</p>
                        </div>
                    @endif
                </div>

                <!-- Basic Info Column -->
                <div class="info-column">
                    <!-- Product Header -->
                    <div style="margin-bottom: 20px;">
                        <h2 class="product-name">{{ $product->name }}</h2>
                        <div class="badge-group">
                            <span class="badge category">
                                {{ $product->category ?? 'Uncategorized' }}
                            </span>
                            <span class="badge code">
                                <span style="font-size: 12px;">🔢</span>
                                {{ $product->product_code }}
                            </span>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="quick-stats-grid">
                        <div class="stat-card">
                            <div class="stat-label">Price</div>
                            <div class="stat-value price">₹ {{ number_format($product->price, 2) }}</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-label">Quantity</div>
                            <div class="stat-value quantity {{ $product->quantity <= 10 ? 'low' : '' }}">
                                {{ $product->quantity }}
                                @if($product->quantity <= 10)
                                    <span style="font-size: 14px; margin-left: 2px;">⚠️</span>
                                @endif
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-label">Total Value</div>
                            <div class="stat-value total">
                                ₹ {{ number_format($product->price * $product->quantity, 2) }}
                            </div>
                        </div>
                    </div>

                    <!-- Stock Status Badge -->
                    <div class="stock-status {{ $product->quantity <= 10 ? 'low' : ($product->quantity <= 30 ? 'normal' : 'high') }}">
                        @if($product->quantity <= 10)
                            <span>⚠️</span> Low Stock - Reorder Recommended
                        @elseif($product->quantity <= 30)
                            <span>📊</span> Normal Stock Level
                        @else
                            <span>✅</span> High Stock Level
                        @endif
                    </div>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="details-grid">
                <div class="two-column-grid">
                    <!-- Left Column - Product Information -->
                    <div>
                        <h3 class="section-title">
                            <span class="title-underline blue"></span>
                            Product Information
                        </h3>

                        <div class="info-card">
                            <div class="info-row">
                                <div class="info-label">Product Code</div>
                                <div class="info-value">{{ $product->product_code }}</div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Category</div>
                                <div>
                                    <span class="badge category">
                                        {{ $product->category ?? 'Uncategorized' }}
                                    </span>
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Price</div>
                                <div class="info-value" style="font-weight: 700; color: var(--success-dark);">
                                    ₹ {{ number_format($product->price, 2) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Quantity</div>
                                <div class="info-value">
                                    {{ $product->quantity }} units
                                    @if($product->quantity <= 10)
                                        <span style="margin-left: 10px; background: #fee2e2; color: var(--danger-dark); padding: 2px 8px; border-radius: 12px; font-size: 12px;">Low Stock</span>
                                    @endif
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Created</div>
                                <div class="info-value">
                                    {{ $product->created_at->format('F d, Y \a\t h:i A') }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Last Updated</div>
                                <div class="info-value">
                                    {{ $product->updated_at->format('F d, Y \a\t h:i A') }}
                                </div>
                            </div>
                        </div>

                        <!-- Description Section -->
                        @if($product->description)
                            <div class="description-card">
                                <h3 class="section-title">
                                    <span class="title-underline orange"></span>
                                    Description
                                </h3>
                                <div class="description-content">
                                    {{ $product->description }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Right Column - Stock Analysis -->
                    <div>
                        <h3 class="section-title">
                            <span class="title-underline purple"></span>
                            Stock Analysis
                        </h3>

                        <div class="analysis-card">
                            <!-- Stock Level Indicator -->
                            <div class="stock-level">
                                <div class="level-header">
                                    <span class="level-label">Stock Level</span>
                                    <span class="level-value">{{ $product->quantity }} units</span>
                                </div>
                                @php
                                    $maxStock = 100; // Maximum expected stock for visualization
                                    $percentage = min(100, ($product->quantity / $maxStock) * 100);
                                    $color = $product->quantity <= 10 ? '#ef4444' : ($product->quantity <= 30 ? '#f59e0b' : '#10b981');
                                @endphp
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ $percentage }}%; background: {{ $color }};"></div>
                                </div>
                                <div class="scale-labels">
                                    <span>0</span>
                                    <span>25</span>
                                    <span>50</span>
                                    <span>75</span>
                                    <span>100+</span>
                                </div>
                            </div>

                            <!-- Stock Status Cards -->
                            <div class="status-cards">
                                <div class="status-card">
                                    <div class="status-card-label">Status</div>
                                    <div class="status-badge {{ $product->quantity <= 10 ? 'low' : ($product->quantity <= 30 ? 'normal' : 'high') }}">
                                        @if($product->quantity <= 10)
                                            ⚠️ Low Stock
                                        @elseif($product->quantity <= 30)
                                            📊 Normal
                                        @else
                                            ✅ High Stock
                                        @endif
                                    </div>
                                </div>

                                <div class="status-card">
                                    <div class="status-card-label">Reorder Point</div>
                                    <div class="reorder-point">10 units</div>
                                </div>
                            </div>

                            <!-- Inventory Value Card -->
                            <div class="value-card">
                                <div class="value-label">Total Inventory Value</div>
                                <div class="value-amount">₹ {{ number_format($product->price * $product->quantity, 2) }}</div>
                                <div class="value-desc">
                                    (₹{{ number_format($product->price, 2) }} × {{ $product->quantity }} units)
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="quick-actions">
                                @if(auth()->user()->hasPermission('edit_inventory'))
                                    <a href="{{ route('inventory.edit', $product->id) }}" class="btn-quick edit">
                                        ✏️ Quick Edit
                                    </a>
                                @endif
                                @if(auth()->user()->hasPermission('create_inventory'))
                                    <a href="{{ route('inventory.create') }}" class="btn-quick add">
                                        ➕ Add New
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Footer -->
            <div class="footer-actions">
                @if(auth()->user()->hasPermission('edit_inventory'))
                    <a href="{{ route('inventory.edit', $product->id) }}" class="btn-footer edit">
                        ✏️ Edit Product
                    </a>
                @endif

                @if(auth()->user()->hasPermission('delete_inventory'))
                    <form method="POST" action="{{ route('inventory.destroy', $product->id) }}" style="margin: 0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this product?')" class="btn-footer delete">
                            🗑️ Delete Product
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Full Screen Image Modal -->
<div id="imageModal" class="modal-overlay" onclick="closeFullImage()">
    <span class="modal-close">×</span>
    <img id="fullImage" src="" alt="Full size image" class="modal-image">
</div>

<script>
function openFullImage(src) {
    document.getElementById('fullImage').src = src;
    document.getElementById('imageModal').classList.add('active');
    document.body.style.overflow = 'hidden'; // Prevent scrolling
}

function closeFullImage() {
    document.getElementById('imageModal').classList.remove('active');
    document.body.style.overflow = 'auto'; // Restore scrolling
}

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeFullImage();
    }
});

// Handle window resize for sidebar
window.addEventListener('resize', function() {
    const productPage = document.querySelector('.product-page');
    if (window.innerWidth <= 991) {
        productPage.style.marginLeft = '0';
        productPage.style.width = '100%';
    } else {
        productPage.style.marginLeft = '260px';
        productPage.style.width = 'calc(100% - 260px)';
    }
});
</script>
@endsection