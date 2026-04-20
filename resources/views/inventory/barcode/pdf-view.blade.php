<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Barcode Labels PDF</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            margin: 0;
            padding: 0;
        }
        .barcode-table {
            width: 100%;
            border-collapse: collapse;
        }
        .barcode-cell {
            width: 50%;
            padding: 10px;
            text-align: center;
        }
        .barcode-label {
            border: 1px dashed #ccc;
            padding: 15px;
            margin-bottom: 10px;
        }
        .product-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .barcode-img {
            margin: 10px 0;
        }
        .product-code {
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    @php
        $dns1d = new \Milon\Barcode\DNS1D();
    @endphp

    <table class="barcode-table">
        @foreach ($products->chunk(2) as $chunk)
            <tr>
                @foreach ($chunk as $p)
                    <td class="barcode-cell">
                        <div class="barcode-label">
                            <div class="product-name">{{ $p->name }}</div>
                            <div class="barcode-img">
                                <img src="data:image/png;base64,{{ $dns1d->getBarcodePNG($p->product_code, 'C128', 2, 50) }}" alt="barcode" />
                            </div>
                            <div class="product-code">{{ $p->product_code }}</div>
                        </div>
                    </td>
                @endforeach
                @if (count($chunk) < 2)
                    <td class="barcode-cell"></td>
                @endif
            </tr>
        @endforeach
    </table>
</body>
</html>
