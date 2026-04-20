<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Labels Preview</title>
    <style>
        /* ================= PROFESSIONAL DESIGN SYSTEM ================= */
        :root {
            --primary: #667eea;
            --success: #10b981;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --border-dashed: #cbd5e1;
            --bg-light: #f9fafb;
            --bg-white: #ffffff;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --radius-sm: 4px;
            --radius-md: 6px;
            --radius-lg: 8px;
            --font-sans: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-sans);
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: var(--text-main);
            line-height: 1.5;
            min-height: 100vh;
            padding: 20px;
        }

        /* ================= HEADER ================= */
        .header {
            max-width: 1200px;
            margin: 0 auto 30px;
            background: var(--bg-white);
            border-radius: 12px;
            padding: 20px 30px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-icon {
            background: linear-gradient(135deg, var(--primary) 0%, #764ba2 100%);
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
        }

        .header-text h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
        }

        .header-text p {
            color: var(--text-muted);
            font-size: 14px;
            margin: 4px 0 0 0;
        }

        .header-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            background: var(--bg-white);
            color: var(--text-main);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }

        .btn:hover {
            background: var(--bg-light);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, #764ba2 100%);
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(16, 185, 129, 0.4);
        }

        /* ================= INFO CARD ================= */
        .info-card {
            max-width: 1200px;
            margin: 0 auto 20px;
            background: #e0f2fe;
            border-left: 4px solid var(--primary);
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            color: #1e40af;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .info-icon {
            font-size: 18px;
        }

        /* ================= BARCODE TABLE ================= */
        .barcode-container {
            max-width: 1200px;
            margin: 0 auto;
            background: var(--bg-white);
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border);
            overflow-x: auto;
        }

        .barcode-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        .barcode-cell {
            width: 50%;
            padding: 15px;
            vertical-align: top;
        }

        .barcode-label {
            border: 2px dashed var(--border-dashed);
            border-radius: 12px;
            padding: 20px 15px;
            text-align: center;
            min-height: 180px;
            background: linear-gradient(135deg, var(--bg-white) 0%, var(--bg-light) 100%);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-shadow: var(--shadow-sm);
        }

        .barcode-label:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary);
        }

        .product-name {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 12px;
            word-break: break-word;
            line-height: 1.4;
        }

        .barcode-wrapper {
            margin: 12px auto;
            background: white;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid var(--border);
            display: inline-block;
        }

        .barcode-wrapper svg {
            display: block;
            max-width: 100%;
            height: auto;
            margin: 0 auto;
        }

        /* Ensure barcode is visible in print */
        @media print {
            .barcode-wrapper svg {
                width: 200px !important;
                height: 50px !important;
            }
        }

        .product-code {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-muted);
            margin-top: 10px;
            font-family: monospace;
            letter-spacing: 1px;
        }

        .product-code span {
            background: var(--bg-light);
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
        }

        /* ================= EMPTY STATE ================= */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .empty-text {
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        /* ================= PRINT STYLES ================= */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .header,
            .info-card,
            .btn {
                display: none !important;
            }

            .barcode-container {
                box-shadow: none;
                padding: 0;
                border: none;
            }

            .barcode-label {
                border: 1px dashed #999;
                box-shadow: none;
                page-break-inside: avoid;
            }

            .barcode-label:hover {
                transform: none;
            }
        }

        /* ================= RESPONSIVE BREAKPOINTS ================= */
        
        /* Desktop (992px to 1199px) */
        @media (max-width: 1199px) {
            .barcode-container {
                padding: 25px;
            }
        }

        /* Tablet (768px to 991px) */
        @media (max-width: 991px) {
            body {
                padding: 15px;
            }

            .header {
                padding: 15px 20px;
            }

            .header-text h1 {
                font-size: 22px;
            }

            .barcode-container {
                padding: 20px;
            }

            .barcode-label {
                min-height: 160px;
                padding: 15px 12px;
            }

            .product-name {
                font-size: 15px;
            }

            .product-code {
                font-size: 13px;
            }
        }

        /* Mobile Landscape (576px to 767px) */
        @media (max-width: 767px) {
            body {
                padding: 10px;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-actions {
                width: 100%;
            }

            .btn {
                flex: 1;
                justify-content: center;
            }

            .barcode-table {
                min-width: 500px;
            }

            .barcode-label {
                min-height: 150px;
                padding: 12px 10px;
            }

            .product-name {
                font-size: 14px;
                margin-bottom: 8px;
            }

            .barcode-wrapper {
                margin: 8px auto;
                padding: 6px;
            }

            .product-code {
                font-size: 12px;
                margin-top: 6px;
            }

            .info-card {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        /* Mobile Portrait (up to 575px) */
        @media (max-width: 575px) {
            body {
                padding: 8px;
            }

            .header-text h1 {
                font-size: 20px;
            }

            .header-text p {
                font-size: 13px;
            }

            .header-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }

            .barcode-container {
                padding: 15px;
            }

            .barcode-table {
                min-width: 400px;
            }

            .barcode-label {
                min-height: 140px;
                padding: 10px 8px;
            }

            .product-name {
                font-size: 13px;
            }

            .barcode-wrapper {
                margin: 6px auto;
            }

            .product-code {
                font-size: 11px;
            }

            .info-card {
                padding: 10px 15px;
                font-size: 13px;
            }
        }

        /* Extra Small Devices (up to 360px) */
        @media (max-width: 360px) {
            body {
                padding: 5px;
            }

            .header {
                padding: 12px 15px;
            }

            .header-icon {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }

            .header-text h1 {
                font-size: 18px;
            }

            .header-text p {
                font-size: 12px;
            }

            .barcode-container {
                padding: 10px;
            }

            .barcode-table {
                min-width: 350px;
            }

            .barcode-label {
                padding: 8px 6px;
            }

            .product-name {
                font-size: 12px;
            }

            .barcode-wrapper {
                margin: 4px auto;
            }

            .product-code {
                font-size: 10px;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="header-title">
            <div class="header-icon">
                <span>📦</span>
            </div>
            <div class="header-text">
                <h1>Barcode Labels Preview</h1>
                <p>{{ count($products) }} product(s) selected</p>
            </div>
        </div>
        <div class="header-actions">
            <button onclick="window.print()" class="btn btn-primary">
                <span>🖨️</span>
                Print Labels
            </button>
            <button onclick="document.getElementById('downloadForm').submit()" class="btn btn-success">
                <span>📥</span>
                Download PDF
            </button>
            <button onclick="window.close()" class="btn">
                <span>✕</span>
                Close
            </button>
        </div>
    </div>

    <!-- Info Card -->
    <div class="info-card">
        <span class="info-icon">📌</span>
        <span>Each page contains 2 labels per row. Cut along the dashed lines to separate individual labels.</span>
    </div>

    <!-- Barcode Container -->
    <div class="barcode-container">
        @php
            $dns1d = new \Milon\Barcode\DNS1D();
        @endphp
        @if(count($products) > 0)
            <table class="barcode-table">
                <tr>
                    @foreach ($products as $index => $p)

                        <td class="barcode-cell">
                            <div class="barcode-label">
                                <div class="product-name">{{ $p->name }}</div>

                                <div class="barcode-wrapper">
                                    {!! $dns1d->getBarcodeSVG($p->product_code, 'C128', 2, 50) !!}
                                </div>

                                <div class="product-code">
                                    <span>{{ $p->product_code }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- 2 labels per row --}}
                        @if (($index + 1) % 2 == 0 && !$loop->last)
                            </tr><tr>
                        @endif
                    @endforeach

                    {{-- Fill empty cells if odd number of products --}}
                    @if(count($products) % 2 != 0)
                        <td class="barcode-cell"></td>
                    @endif
                </tr>
            </table>
        @else
            <div class="empty-state">
                <div class="empty-icon">🏷️</div>
                <h3 class="empty-title">No Products Selected</h3>
                <p class="empty-text">Please select at least one product to generate barcodes.</p>
                <button onclick="window.close()" class="btn btn-primary">Close Window</button>
            </div>
        @endif
    </div>

    <form id="downloadForm" action="{{ route('inventory.barcode.download') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="product_ids" value="{{ $products->pluck('product_code')->implode(',') }}">
    </form>

    <script>
        // Auto-print dialog (optional - uncomment if you want auto-print)
        // window.onload = function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 500);
        // };

        // Keyboard shortcut for printing (Ctrl+P)
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>

</html>