{{-- resources/views/logistics/shipments/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Shipments Management')

@section('content')
    <style>
        /* ================= PROFESSIONAL SHIPMENTS MANAGEMENT STYLES ================= */
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

        .shipments-page {
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
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .header-text p {
            font-size: 1rem;
            opacity: 0.9;
            margin: 0.5rem 0 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            transition: width 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
        }

        .stat-card:hover::before {
            width: 6px;
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .stat-title {
            font-size: 1rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .stat-icon.primary {
            background: linear-gradient(135deg, #667eea15, #764ba215);
            color: #667eea;
        }

        .stat-icon.warning {
            background: linear-gradient(135deg, #f59e0b15, #d9770615);
            color: #f59e0b;
        }

        .stat-icon.info {
            background: linear-gradient(135deg, #3b82f615, #2563eb15);
            color: #3b82f6;
        }

        .stat-icon.success {
            background: linear-gradient(135deg, #10b98115, #05966915);
            color: #10b981;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.2;
            margin-bottom: 0.25rem;
        }

        .stat-trend {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            background: #f1f5f9;
        }

        .stat-trend.up {
            color: #10b981;
            background: #d1fae5;
        }

        .stat-trend.down {
            color: #ef4444;
            background: #fee2e2;
        }

        .action-bar {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .action-grid {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .btn-group-left,
        .btn-group-right {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            white-space: nowrap;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
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
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        }

        .search-box {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .search-input {
            padding: 0.875rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.95rem;
            width: 300px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .filter-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .filter-card:hover {
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.15);
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
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }

        .filter-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
        }

        .filter-select {
            padding: 0.75rem 2rem 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 0.95rem;
            background: white;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1rem;
            min-width: 150px;
        }

        .filter-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .date-input {
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 0.95rem;
            min-width: 150px;
        }

        .date-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 0.75rem;
            margin-left: auto;
        }

        .table-card {
            background: white;
            border-radius: 30px;
            padding: 1.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .table-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .table-title-icon {
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

        .table-title h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }

        .table-stats {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .stat-pill {
            background: #f1f5f9;
            padding: 0.5rem 1rem;
            border-radius: 30px;
            font-size: 0.9rem;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stat-pill i {
            color: #667eea;
        }

        .table-responsive {
            overflow-x: auto;
            border-radius: 20px;
            margin-top: 1rem;
        }

        .shipments-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1300px;
        }

        .shipments-table thead tr {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        }

        .shipments-table th {
            padding: 1.25rem 1rem;
            text-align: left;
            font-weight: 700;
            font-size: 0.9rem;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e5e7eb;
            white-space: nowrap;
        }

        .shipments-table td {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
            font-size: 0.95rem;
            color: #1e293b;
            transition: all 0.2s ease;
        }

        .shipments-table tbody tr {
            transition: all 0.3s ease;
        }

        .shipments-table tbody tr:hover {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            transform: scale(1.01);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.1);
        }

        .shipment-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }

        .shipment-link:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .invoice-link {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.5rem;
            background: #d1fae5;
            border-radius: 6px;
            font-size: 0.75rem;
            color: #065f46;
            text-decoration: none;
            font-weight: 500;
        }

        .invoice-link:hover {
            background: #a7f3d0;
            color: #065f46;
        }

        .badge-from-sale {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            background: #ede9fe;
            border-radius: 20px;
            font-size: 0.75rem;
            color: #5b21b6;
        }

        .badge-direct {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            background: #f1f5f9;
            border-radius: 20px;
            font-size: 0.75rem;
            color: #64748b;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.5rem 1rem;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
            border: 1px solid transparent;
        }

        .status-badge.pending {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #92400e;
            border-color: #fcd34d;
        }

        .status-badge.picked {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #1e40af;
            border-color: #93c5fd;
        }

        .status-badge.in_transit {
            background: linear-gradient(135deg, #ede9fe, #ddd6fe);
            color: #5b21b6;
            border-color: #c4b5fd;
        }

        .status-badge.out_for_delivery {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: #166534;
            border-color: #86efac;
        }

        .status-badge.delivered {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
            border-color: #6ee7b7;
        }

        .status-badge.failed {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
            border-color: #fca5a5;
        }

        .status-badge.returned {
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            color: #475569;
            border-color: #cbd5e1;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-dot.pending {
            background: #f59e0b;
        }

        .status-dot.picked {
            background: #3b82f6;
        }

        .status-dot.in_transit {
            background: #8b5cf6;
        }

        .status-dot.out_for_delivery {
            background: #10b981;
        }

        .status-dot.delivered {
            background: #10b981;
        }

        .status-dot.failed {
            background: #ef4444;
        }

        .status-dot.returned {
            background: #6b7280;
        }

        .action-group {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.4s, height 0.4s;
        }

        .action-btn:hover::before {
            width: 100px;
            height: 100px;
        }

        .action-btn:hover {
            transform: translateY(-2px) scale(1.1);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .action-btn.info {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
        }

        .action-btn.warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .action-btn.success {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .action-btn.track {
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        }

        .action-btn i {
            font-size: 1rem;
            transition: transform 0.3s ease;
        }

        .action-btn:hover i {
            transform: scale(1.2);
        }

        .map-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .map-modal-content {
            background: white;
            border-radius: 30px;
            width: 100%;
            max-width: 1000px;
            max-height: 90vh;
            overflow: hidden;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
            animation: modalZoomIn 0.4s ease;
        }

        @keyframes modalZoomIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .map-modal-header {
            padding: 1.5rem 2rem;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .map-modal-title {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .map-modal-title i {
            font-size: 2rem;
            color: #fbbf24;
        }

        .map-modal-title h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        .map-modal-title p {
            margin: 0.25rem 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .map-modal-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.2rem;
        }

        .map-modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        .map-modal-body {
            padding: 1.5rem;
            background: #f8fafc;
        }

        #shipmentMap {
            height: 400px;
            width: 100%;
            border-radius: 20px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .map-info-panel {
            margin-top: 1.5rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .info-panel-card {
            background: white;
            border-radius: 16px;
            padding: 1.25rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .info-panel-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.15);
            border-color: #667eea;
        }

        .info-panel-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
            color: #1e293b;
            font-weight: 600;
        }

        .info-panel-header i {
            color: #667eea;
            font-size: 1.2rem;
        }

        .info-panel-content {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px dashed #e5e7eb;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #64748b;
            font-size: 0.9rem;
        }

        .info-value {
            font-weight: 600;
            color: #1e293b;
            text-align: right;
        }

        .info-value.highlight {
            color: #667eea;
            font-size: 1.1rem;
        }

        .tracking-timeline {
            max-height: 200px;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        .timeline-item {
            display: flex;
            gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .timeline-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-top: 0.25rem;
            position: relative;
        }

        .timeline-dot::after {
            content: '';
            position: absolute;
            top: 12px;
            left: 5px;
            width: 2px;
            height: 100%;
            background: #e5e7eb;
        }

        .timeline-item:last-child .timeline-dot::after {
            display: none;
        }

        .timeline-dot.pending {
            background: #f59e0b;
        }

        .timeline-dot.in_transit {
            background: #8b5cf6;
        }

        .timeline-dot.delivered {
            background: #10b981;
        }

        .timeline-content {
            flex: 1;
        }

        .timeline-status {
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .timeline-time {
            color: #64748b;
            font-size: 0.8rem;
            margin: 0.25rem 0;
        }

        .timeline-location {
            color: #667eea;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: #f8fafc;
            border-radius: 20px;
            margin: 2rem 0;
        }

        .empty-icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            animation: bounce 2s ease-in-out infinite;
        }

        .empty-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .empty-text {
            color: #64748b;
            margin-bottom: 2rem;
        }

        .empty-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .empty-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }

        .pagination-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 12px;
        }

        .pagination-info {
            color: #64748b;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .pagination-info i {
            color: #667eea;
        }

        .pagination {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .page-btn {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            color: #475569;
            text-decoration: none;
            font-weight: 600;
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .page-btn:hover {
            border-color: #667eea;
            color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .page-btn.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-color: transparent;
        }

        .page-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
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

        .modal-title i {
            color: #667eea;
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
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .modal-close:hover {
            background: #fee2e2;
            color: #ef4444;
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
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

        .modal-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .modal-btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 2px solid #e5e7eb;
        }

        .modal-btn-secondary:hover {
            background: #e2e8f0;
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
            width: calc(100% - 60px);
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
            .shipments-page {
                padding: 1rem;
            }

            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .header-text h1 {
                font-size: 2rem;
            }

            .action-grid {
                flex-direction: column;
            }

            .btn-group-left,
            .btn-group-right {
                width: 100%;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .search-box {
                width: 100%;
            }

            .search-input {
                width: 100%;
            }

            .filter-grid {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-select,
            .date-input {
                width: 100%;
            }

            .filter-actions {
                margin-left: 0;
                width: 100%;
            }

            .table-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .pagination-wrapper {
                flex-direction: column;
                align-items: center;
            }

            .map-modal-content {
                max-height: 95vh;
            }

            .map-info-panel {
                grid-template-columns: 1fr;
            }

            #shipmentMap {
                height: 300px;
            }

            .toast {
                left: 15px;
                right: 15px;
                width: calc(100% - 30px);
            }
        }
    </style>

    <div class="shipments-page">
        <div id="loadingOverlay" class="loading-overlay">
            <div class="spinner"></div>
            <div class="loading-text">Loading...</div>
        </div>

        <div class="page-header">
            <div class="header-content">
                <div class="header-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="header-text">
                    <h1>Shipments Management</h1>
                    <p><i class="fas fa-map-marker-alt"></i> Track, manage, and monitor all your shipments</p>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Total Shipments</span>
                    <span class="stat-icon primary"><i class="fas fa-box"></i></span>
                </div>
                <div class="stat-value">{{ $stats['total'] ?? 0 }}</div>
                <div class="stat-trend up">
                    <i class="fas fa-arrow-up"></i> All time shipments
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Pending</span>
                    <span class="stat-icon warning"><i class="fas fa-clock"></i></span>
                </div>
                <div class="stat-value">{{ $stats['pending'] ?? 0 }}</div>
                <div class="stat-trend">
                    <i class="fas fa-hourglass-half"></i> Awaiting pickup
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">In Transit</span>
                    <span class="stat-icon info"><i class="fas fa-truck"></i></span>
                </div>
                <div class="stat-value">{{ $stats['in_transit'] ?? 0 }}</div>
                <div class="stat-trend">
                    <i class="fas fa-route"></i> On the way
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Delivered Today</span>
                    <span class="stat-icon success"><i class="fas fa-check-circle"></i></span>
                </div>
                <div class="stat-value">{{ $stats['delivered_today'] ?? 0 }}</div>
                <div class="stat-trend up">
                    <i class="fas fa-calendar-day"></i> Today's deliveries
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">From Sales</span>
                    <span class="stat-icon info"><i class="fas fa-file-invoice"></i></span>
                </div>
                <div class="stat-value">{{ $stats['from_sales'] ?? 0 }}</div>
                <div class="stat-trend">
                    <i class="fas fa-truck"></i> Created from invoices
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Direct Shipments</span>
                    <span class="stat-icon primary"><i class="fas fa-plus-circle"></i></span>
                </div>
                <div class="stat-value">{{ $stats['direct'] ?? 0 }}</div>
                <div class="stat-trend">
                    <i class="fas fa-box"></i> Manual entries
                </div>
            </div>
        </div>

        <div class="action-bar">
            <div class="action-grid">
                <div class="btn-group-left">
                    @if (auth()->user()->hasPermission('create_shipments'))
                        <a href="{{ route('logistics.shipments.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> New Shipment
                        </a>
                    @endif
                    @if (auth()->user()->hasPermission('export_shipments'))
                        <button class="btn btn-secondary" onclick="exportData()">
                            <i class="fas fa-download"></i> Export
                        </button>
                    @endif
                </div>
                <div class="btn-group-right">
                    <div class="search-box">
                        <input type="text" id="searchInput" class="search-input"
                            placeholder="Search by tracking #, invoice #, customer, city..."
                            value="{{ request('search') }}">
                        <button class="btn btn-secondary" onclick="performSearch()">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-card">
            <div class="filter-header">
                <div class="filter-icon">
                    <i class="fas fa-filter"></i>
                </div>
                <h3 class="filter-title">Advanced Filters</h3>
            </div>
            <form method="GET" action="{{ route('logistics.shipments.index') }}" id="filterForm">
                <div class="filter-grid">
                    <select name="status" class="filter-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                        <option value="picked" {{ request('status') == 'picked' ? 'selected' : '' }}>📦 Picked Up</option>
                        <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>🚚 In Transit
                        </option>
                        <option value="out_for_delivery" {{ request('status') == 'out_for_delivery' ? 'selected' : '' }}>
                            🚀 Out for Delivery</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>✅ Delivered
                        </option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>❌ Failed</option>
                    </select>

                    <select name="source" class="filter-select">
                        <option value="">All Sources</option>
                        <option value="sales" {{ request('source') == 'sales' ? 'selected' : '' }}>📄 From Sales
                            (Invoices)</option>
                        <option value="direct" {{ request('source') == 'direct' ? 'selected' : '' }}>📦 Direct Shipment
                        </option>
                    </select>

                    <input type="date" name="date_from" class="date-input" value="{{ request('date_from') }}"
                        placeholder="From Date">
                    <input type="date" name="date_to" class="date-input" value="{{ request('date_to') }}"
                        placeholder="To Date">

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Apply Filters
                        </button>
                        <a href="{{ route('logistics.shipments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-card">
            <div class="table-header">
                <div class="table-title">
                    <div class="table-title-icon">
                        <i class="fas fa-list"></i>
                    </div>
                    <h3>Shipments List</h3>
                </div>
                <div class="table-stats">
                    <span class="stat-pill">
                        <i class="fas fa-box"></i> Total: {{ $shipments->total() }}
                    </span>
                    <span class="stat-pill">
                        <i class="fas fa-eye"></i> Showing {{ $shipments->firstItem() }}-{{ $shipments->lastItem() }}
                    </span>
                    <span class="stat-pill">
                        <i class="fas fa-file-invoice"></i> From Sales: {{ $stats['from_sales'] ?? 0 }}
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="shipments-table">
                    <thead>

                        <th><i class="fas fa-hashtag"></i> Shipment #</th>
                        <th><i class="fas fa-barcode"></i> Tracking #</th>
                        <th><i class="fas fa-file-invoice"></i> Invoice / Source</th>
                        <th><i class="fas fa-user"></i> Customer</th>
                        <th><i class="fas fa-map-marker-alt"></i> Destination</th>
                        <th><i class="fas fa-truck"></i> Type</th>
                        <th><i class="fas fa-info-circle"></i> Status</th>
                        <th><i class="fas fa-calendar"></i> Delivery Date</th>
                        <th><i class="fas fa-user-tie"></i> Assigned To</th>
                        <th><i class="fas fa-cog"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $__col = $shipments; @endphp
                        @if (is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
                            @foreach ($__col as $shipment)
                                <tr>
                                    <td>
                                        @if(auth()->user()->hasPermission('view_shipments'))
                                            <a href="{{ route('logistics.shipments.show', $shipment->id) }}"
                                                class="shipment-link">
                                                <i class="fas fa-box"></i> {{ $shipment->shipment_number }}
                                            </a>
                                        @else
                                            <i class="fas fa-box"></i> {{ $shipment->shipment_number }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($shipment->tracking_number)
                                            <span
                                                style="font-family: monospace; font-weight: 600;">{{ $shipment->tracking_number }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($shipment->sale_id && $shipment->sale)
                                            <a href="{{ route('sales.show', $shipment->sale_id) }}" class="invoice-link"
                                                target="_blank">
                                                <i class="fas fa-file-invoice"></i> {{ $shipment->sale->invoice_no }}
                                            </a>
                                            <div style="font-size: 0.7rem; color: #10b981; margin-top: 0.25rem;">
                                                <i class="fas fa-check-circle"></i> From Invoice
                                            </div>
                                        @else
                                            <span class="badge-direct">
                                                <i class="fas fa-plus-circle"></i> Direct Shipment
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <i class="fas fa-user-circle" style="color: #667eea;"></i>
                                            <div>
                                                <div style="font-weight: 600;">{{ $shipment->receiver_name }}</div>
                                                <div style="font-size: 0.8rem; color: #64748b;">
                                                    {{ $shipment->receiver_phone ?? 'No phone' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <i class="fas fa-map-pin" style="color: #ef4444; margin-right: 0.25rem;"></i>
                                        {{ $shipment->city }}, {{ $shipment->state }}
                                        @if ($shipment->pincode)
                                            <div style="font-size: 0.75rem; color: #64748b;">PIN: {{ $shipment->pincode }}
                                            </div>
                                        @endif
                                        @if ($shipment->destination_latitude && $shipment->destination_longitude)
                                            <div style="font-size: 0.7rem; color: #8b5cf6;">
                                                <i class="fas fa-map-marker-alt"></i> GPS Available
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($shipment->sale_id)
                                            <span class="badge-from-sale">
                                                <i class="fas fa-truck"></i> From Sale
                                            </span>
                                        @else
                                            <span class="badge-direct">
                                                <i class="fas fa-plus"></i> Direct
                                            </span>
                                        @endif
                                        @if ($shipment->delivery_instructions)
                                            <div style="font-size: 0.7rem; color: #8b5cf6; margin-top: 0.25rem;">
                                                <i class="fas fa-info-circle"></i> Instructions provided
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="status-badge {{ $shipment->status }}">
                                            <span class="status-dot {{ $shipment->status }}"></span>
                                            {{ ucfirst(str_replace('_', ' ', $shipment->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($shipment->actual_delivery_date)
                                            <span style="color: #10b981;">
                                                <i class="fas fa-check-circle"></i>
                                                {{ $shipment->actual_delivery_date->format('d M Y') }}
                                            </span>
                                        @elseif($shipment->estimated_delivery_date)
                                            <span style="color: #f59e0b;">
                                                <i class="fas fa-calendar-alt"></i>
                                                {{ $shipment->estimated_delivery_date->format('d M Y') }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($shipment->deliveryAgent)
                                            <span
                                                style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.75rem; background: #dbeafe; border-radius: 20px; font-size: 0.85rem;">
                                                <i class="fas fa-user-check" style="color: #2563eb;"></i>
                                                {{ $shipment->deliveryAgent->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-group">
                                            @if (auth()->user()->hasPermission('view_shipments'))
                                                <a href="{{ route('logistics.shipments.show', $shipment->id) }}"
                                                    class="action-btn info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif

                                            @if (auth()->user()->hasPermission('edit_shipments'))
                                                @if ($shipment->status !== 'delivered')
                                                    <button type="button" class="action-btn warning"
                                                        onclick="updateStatus({{ $shipment->id }})" title="Update Status">
                                                        <i class="fas fa-truck"></i>
                                                    </button>
                                                @endif
                                            @endif

                                            @if (auth()->user()->hasPermission('view_shipments'))
                                                <button type="button" class="action-btn track"
                                                    onclick="showMapPopup('{{ $shipment->tracking_number ?? $shipment->shipment_number }}', '{{ $shipment->receiver_name }}', '{{ $shipment->city }}', {{ $shipment->id }})"
                                                    title="Track on Map">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                </button>
                                            @endif

                                            @if ($shipment->sale_id)
                                                @if (auth()->user()->hasPermission('view_sales'))
                                                    <a href="{{ route('sales.show', $shipment->sale_id) }}"
                                                        class="action-btn success" title="View Invoice" target="_blank">
                                                        <i class="fas fa-file-invoice"></i>
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="10" style="text-align: center;">
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            📦
                                        </div>
                                        <h3 class="empty-title">No Shipments Found</h3>
                                        <p class="empty-text">
                                            @if (request('source') === 'sales')
                                                No shipments created from sales found.
                                            @else
                                                Get started by creating your first shipment
                                            @endif
                                        </p>
                                        @if (auth()->user()->hasPermission('create_shipments'))
                                            <a href="{{ route('logistics.shipments.create') }}" class="empty-btn">
                                                <i class="fas fa-plus-circle"></i> Create New Shipment
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @if ($shipments->hasPages())
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        <i class="fas fa-info-circle"></i>
                        Showing {{ $shipments->firstItem() }} to {{ $shipments->lastItem() }} of
                        {{ $shipments->total() }} shipments
                    </div>
                    <div class="pagination">
                        {{ $shipments->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Map Popup Modal --}}
    <div class="map-modal" id="mapModal">
        <div class="map-modal-content">
            <div class="map-modal-header">
                <div class="map-modal-title">
                    <i class="fas fa-map-marked-alt"></i>
                    <div>
                        <h3 id="mapShipmentNumber">Shipment Tracking</h3>
                        <p id="mapReceiverName">Loading...</p>
                    </div>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button class="map-modal-close" onclick="startLiveTracking()" title="Start Live Tracking"
                        id="liveTrackBtn">
                        <i class="fas fa-satellite-dish"></i>
                    </button>
                    <button class="map-modal-close" onclick="stopLiveTracking()" title="Stop Live Tracking"
                        id="stopTrackBtn" style="display: none;">
                        <i class="fas fa-stop"></i>
                    </button>
                    <button class="map-modal-close" onclick="closeMapPopup()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="map-modal-body">
                <div id="shipmentMap"></div>

                <div class="map-info-panel" id="mapInfoPanel">
                    <div class="info-panel-card">
                        <div class="info-panel-header">
                            <i class="fas fa-info-circle"></i>
                            <span>Shipment Details</span>
                        </div>
                        <div class="info-panel-content" id="shipmentDetails">
                            <div class="info-row">
                                <span class="info-label">Status:</span>
                                <span class="info-value" id="mapStatus">Loading...</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Estimated Delivery:</span>
                                <span class="info-value" id="mapEstDelivery">Loading...</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Current Location:</span>
                                <span class="info-value" id="mapCurrentLocation">Loading...</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Last Updated:</span>
                                <span class="info-value" id="mapLastUpdate">Loading...</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Accuracy:</span>
                                <span class="info-value" id="mapAccuracy">Loading...</span>
                            </div>
                            <div class="info-row" id="mapInvoiceRow" style="display: none;">
                                <span class="info-label">Invoice:</span>
                                <span class="info-value" id="mapInvoiceNo">Loading...</span>
                            </div>
                        </div>
                    </div>

                    <div class="info-panel-card">
                        <div class="info-panel-header">
                            <i class="fas fa-history"></i>
                            <span>Tracking History</span>
                        </div>
                        <div class="tracking-timeline" id="trackingTimeline">
                            <div class="timeline-item">
                                <div class="timeline-dot pending"></div>
                                <div class="timeline-content">
                                    <div class="timeline-status">Loading...</div>
                                    <div class="timeline-time">Just now</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Update Modal --}}
    <div class="modal" id="statusModal" tabindex="-1">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-truck"></i> Update Shipment Status
                </h5>
                <button type="button" class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="statusForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control" required>
                            <option value="picked">📦 Picked Up</option>
                            <option value="in_transit">🚚 In Transit</option>
                            <option value="out_for_delivery">🚀 Out for Delivery</option>
                            <option value="delivered">✅ Delivered</option>
                            <option value="failed">❌ Failed</option>
                            <option value="returned">🔄 Returned</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" placeholder="Current location">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="3" placeholder="Additional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn modal-btn-secondary" onclick="closeModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="modal-btn modal-btn-primary">
                        <i class="fas fa-check"></i> Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        let currentShipmentId = null;
        let currentTrackingNumber = null;
        let map = null;
        let marker = null;
        let watchId = null;
        let mapUpdateInterval = null;

        const GUJARAT_CITIES = {
            'ahmedabad': {
                lat: 23.0225,
                lng: 72.5714
            },
            'surat': {
                lat: 21.1702,
                lng: 72.8311
            },
            'vadodara': {
                lat: 22.3072,
                lng: 73.1812
            },
            'rajkot': {
                lat: 22.3039,
                lng: 70.8022
            },
            'bhavnagar': {
                lat: 21.7645,
                lng: 72.1519
            },
            'jamnagar': {
                lat: 22.4707,
                lng: 70.0577
            },
            'junagadh': {
                lat: 21.5222,
                lng: 70.4579
            },
            'gandhinagar': {
                lat: 23.2156,
                lng: 72.6369
            },
            'anand': {
                lat: 22.5645,
                lng: 72.9289
            },
            'naliya': {
                lat: 23.1167,
                lng: 68.8333
            },
            'bhuj': {
                lat: 23.2420,
                lng: 69.6669
            },
            'porbandar': {
                lat: 21.6417,
                lng: 69.6293
            },
            'veraval': {
                lat: 20.9157,
                lng: 70.3678
            },
            'dwarka': {
                lat: 22.2442,
                lng: 68.9685
            },
            'somnath': {
                lat: 20.8880,
                lng: 70.4015
            }
        };

        function getCityCoordinates(city) {
            const cityLower = city.toLowerCase();
            if (GUJARAT_CITIES[cityLower]) return GUJARAT_CITIES[cityLower];
            for (let [key, coords] of Object.entries(GUJARAT_CITIES)) {
                if (cityLower.includes(key) || key.includes(cityLower)) return coords;
            }
            return {
                lat: 22.524768,
                lng: 72.955568
            };
        }

        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.innerHTML =
                `<div class="toast-content"><span class="toast-icon">${type === 'success' ? '✅' : type === 'error' ? '❌' : '⚠️'}</span><span class="toast-message">${message}</span></div>`;
            toast.className = 'toast ' + type;
            toast.style.display = 'block';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);
        }

        function showMapPopup(trackingNumber, receiverName, city, shipmentId) {
            currentTrackingNumber = trackingNumber;
            currentShipmentId = shipmentId;
            showLoading();

            document.getElementById('mapModal').style.display = 'flex';
            document.getElementById('mapShipmentNumber').textContent = `Tracking: ${trackingNumber}`;
            document.getElementById('mapReceiverName').textContent = `Receiver: ${receiverName}`;

            fetch(`/logistics/track/${trackingNumber}`)
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        updateMapWithData(data);
                        if (mapUpdateInterval) clearInterval(mapUpdateInterval);
                        mapUpdateInterval = setInterval(() => refreshTrackingData(trackingNumber), 30000);
                    } else {
                        const cityCoords = getCityCoordinates(city);
                        showCityMap(city, cityCoords.lat, cityCoords.lng, receiverName, trackingNumber);
                        showToast('Using approximate location', 'warning');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error fetching tracking data:', error);
                    const cityCoords = getCityCoordinates(city);
                    showCityMap(city, cityCoords.lat, cityCoords.lng, receiverName, trackingNumber);
                    showToast('Could not fetch exact location', 'warning');
                });
        }

        function refreshTrackingData(trackingNumber) {
            fetch(`/logistics/track/${trackingNumber}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.latitude && data.longitude) {
                        updateMapPosition(data.latitude, data.longitude, data);
                    }
                })
                .catch(error => console.log('Auto-refresh error:', error));
        }

        function updateMapWithData(data) {
            document.getElementById('mapStatus').innerHTML =
                `<span class="status-badge ${data.status}">${data.status.replace('_', ' ').toUpperCase()}</span>`;
            document.getElementById('mapEstDelivery').textContent = data.estimated_delivery || 'Not set';
            document.getElementById('mapCurrentLocation').textContent = data.current_location || 'Location unknown';
            document.getElementById('mapLastUpdate').textContent = data.last_location_update || 'Just now';
            document.getElementById('mapAccuracy').textContent = data.accuracy ? `${data.accuracy}m` : 'Unknown';

            if (data.invoice_no) {
                document.getElementById('mapInvoiceNo').textContent = data.invoice_no;
                document.getElementById('mapInvoiceRow').style.display = 'flex';
            }

            const timeline = document.getElementById('trackingTimeline');
            timeline.innerHTML = '';

            if (data.tracking_history && data.tracking_history.length > 0) {
                data.tracking_history.forEach(item => {
                    const timelineItem = document.createElement('div');
                    timelineItem.className = 'timeline-item';
                    timelineItem.innerHTML = `
                    <div class="timeline-dot ${item.status}"></div>
                    <div class="timeline-content">
                        <div class="timeline-status">${item.status.replace('_', ' ').toUpperCase()}</div>
                        <div class="timeline-time">${item.tracked_at}</div>
                        ${item.location ? `<div class="timeline-location"><i class="fas fa-map-pin"></i> ${item.location}</div>` : ''}
                        ${item.remarks ? `<div class="timeline-remarks" style="font-size:0.8rem; color:#64748b;">${item.remarks}</div>` : ''}
                    </div>
                `;
                    timeline.appendChild(timelineItem);
                });
            } else {
                timeline.innerHTML =
                    '<div style="text-align:center; padding:1rem; color:#64748b;">No tracking history available</div>';
            }

            const lat = data.latitude || 22.524768;
            const lng = data.longitude || 72.955568;

            if (map) map.remove();

            map = L.map('shipmentMap').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            marker = L.marker([lat, lng]).addTo(map).bindPopup(`
            <b>${data.receiver_name || 'Shipment'}</b><br>
            Status: ${data.status}<br>
            Tracking: ${data.tracking_number}<br>
            Updated: ${data.last_location_update || 'Just now'}<br>
            Accuracy: ${data.accuracy || 'Unknown'}m
        `).openPopup();

            if (data.accuracy) {
                L.circle([lat, lng], {
                    color: '#3b82f6',
                    fillColor: '#3b82f6',
                    fillOpacity: 0.1,
                    radius: data.accuracy
                }).addTo(map);
            }

            document.getElementById('liveTrackBtn').style.display = 'flex';
            document.getElementById('liveTrackBtn').onclick = () => startLiveTracking();
        }

        function updateMapPosition(lat, lng, data) {
            if (map && marker) {
                marker.setLatLng([lat, lng]);
                map.setView([lat, lng], 15);
                marker.bindPopup(
                    `<b>${data.receiver_name || 'Shipment'}</b><br>Status: ${data.status}<br>Updated: Just now<br>Accuracy: ${data.accuracy || 'Unknown'}m`
                ).openPopup();
                document.getElementById('mapLastUpdate').textContent = 'Just now';
                document.getElementById('mapAccuracy').textContent = data.accuracy ? `${data.accuracy}m` : 'Unknown';
            }
        }

        function showCityMap(city, lat, lng, receiverName, trackingNumber) {
            if (map) map.remove();
            map = L.map('shipmentMap').setView([lat, lng], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            marker = L.marker([lat, lng]).addTo(map).bindPopup(
                `<b>Shipment to ${city}</b><br>Receiver: ${receiverName}<br>Tracking: ${trackingNumber}<br><i>Approximate location</i>`
            ).openPopup();
            document.getElementById('mapStatus').innerHTML = `<span class="status-badge">PENDING</span>`;
            document.getElementById('mapEstDelivery').textContent = 'Not available';
            document.getElementById('mapCurrentLocation').textContent = `Approximate location: ${city}`;
            document.getElementById('mapLastUpdate').textContent = 'Not available';
            document.getElementById('mapAccuracy').textContent = 'Not available';
            document.getElementById('trackingTimeline').innerHTML =
                `<div style="text-align: center; padding: 1rem; color: #64748b;"><i class="fas fa-info-circle"></i> Live tracking not available</div>`;
        }

        function startLiveTracking() {
            if (!navigator.geolocation) {
                showToast('Geolocation not supported', 'error');
                return;
            }
            document.getElementById('liveTrackBtn').style.display = 'none';
            document.getElementById('stopTrackBtn').style.display = 'flex';
            showToast('Starting live tracking...', 'info');

            watchId = navigator.geolocation.watchPosition(
                function(position) {
                    const lat = position.coords.latitude,
                        lng = position.coords.longitude,
                        accuracy = position.coords.accuracy;
                    if (marker) {
                        marker.setLatLng([lat, lng]);
                        marker.bindPopup(
                            `<b>Live Location</b><br>Updated: ${new Date().toLocaleTimeString()}<br>Accuracy: ${accuracy.toFixed(0)}m`
                        ).openPopup();
                        map.setView([lat, lng], 17);
                    }
                    if (currentShipmentId) updateServerLocation(currentShipmentId, lat, lng, accuracy);
                },
                function(error) {
                    let message = 'Location error: ';
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            message += 'Permission denied';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            message += 'Position unavailable';
                            break;
                        case error.TIMEOUT:
                            message += 'Request timed out';
                            break;
                        default:
                            message += 'Unknown error';
                    }
                    showToast(message, 'error');
                    stopLiveTracking();
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }

        function stopLiveTracking() {
            if (watchId) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
            }
            document.getElementById('liveTrackBtn').style.display = 'flex';
            document.getElementById('stopTrackBtn').style.display = 'none';
            showToast('Live tracking stopped', 'info');
        }

        function updateServerLocation(shipmentId, lat, lng, accuracy) {
            fetch(`/logistics/api/shipments/${shipmentId}/location`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    latitude: lat,
                    longitude: lng,
                    accuracy: accuracy
                })
            }).catch(error => console.error('Error sending location:', error));
        }

        function closeMapPopup() {
            document.getElementById('mapModal').style.display = 'none';
            if (map) {
                map.remove();
                map = null;
            }
            if (mapUpdateInterval) {
                clearInterval(mapUpdateInterval);
                mapUpdateInterval = null;
            }
            stopLiveTracking();
        }

        function updateStatus(shipmentId) {
            currentShipmentId = shipmentId;
            const form = document.getElementById('statusForm');
            form.action = `/logistics/shipments/${shipmentId}/status`;
            document.getElementById('statusModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('statusModal').style.display = 'none';
            document.getElementById('statusForm').reset();
        }

        document.getElementById('statusForm').addEventListener('submit', function(e) {
            e.preventDefault();
            showLoading();
            const formData = new FormData(this);
            fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    closeModal();
                    if (data.success) {
                        showToast('✅ ' + data.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('❌ ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    hideLoading();
                    showToast('❌ Error updating status', 'error');
                    console.error('Error:', error);
                });
        });

        function performSearch() {
            const searchValue = document.getElementById('searchInput').value;
            const url = new URL(window.location.href);
            url.searchParams.set('search', searchValue);
            window.location.href = url.toString();
        }

        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') performSearch();
        });

        function exportData() {
            showLoading();
            setTimeout(() => {
                hideLoading();
                showToast('Export functionality coming soon!', 'warning');
            }, 1000);
        }

        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        window.onclick = function(event) {
            const modal = document.getElementById('statusModal');
            const mapModal = document.getElementById('mapModal');
            if (event.target === modal) closeModal();
            if (event.target === mapModal) closeMapPopup();
        }
    </script>
@endsection
