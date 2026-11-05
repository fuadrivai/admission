<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Merchandise Payment Receipt</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #333; font-size: 12px; }
        .header { border-bottom: 3px solid #800000; padding-bottom: 12px; margin-bottom: 20px; }
        .logo { max-width: 150px; max-height: 65px; }
        h1 { color: #800000; font-size: 22px; margin: 0; }
        .muted { color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th { background: #800000; color: #fff; text-align: left; }
        th, td { padding: 8px; border: 1px solid #ddd; }
        .number { text-align: right; white-space: nowrap; }
        .summary { width: 55%; margin-left: auto; }
        .summary td { border: 0; padding: 5px 8px; }
        .total { color: #800000; font-weight: bold; border-top: 1px solid #800000 !important; }
    </style>
</head>
<body>
    <div class="header">
        @if ($logo)
            <img class="logo" src="{{ $logo }}" alt="MHIS">
        @endif
        <h1>Payment Receipt</h1>
        <div class="muted">Mutiara Harapan Islamic School</div>
    </div>

    <table style="margin-top:0;border:0">
        <tr>
            <td style="border:0;padding:3px 0"><strong>Order number:</strong> {{ $order->code }}</td>
            <td style="border:0;padding:3px 0"><strong>Payment date:</strong> {{ $order->paid_at ? $order->paid_at->copy()->setTimezone('Asia/Jakarta')->format('d M Y H:i') : now()->format('d M Y H:i') }}</td>
        </tr>
        <tr>
            <td style="border:0;padding:3px 0"><strong>Parent:</strong> {{ $order->parent_name }}</td>
            <td style="border:0;padding:3px 0"><strong>Branch:</strong> {{ $order->branch_name }}</td>
        </tr>
        <tr>
            <td style="border:0;padding:3px 0"><strong>Portal username:</strong> {{ $order->portal_username }}</td>
            <td style="border:0;padding:3px 0"><strong>Description:</strong> {{ $description }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr><th>Product</th><th class="number">Quantity</th><th class="number">Unit price</th><th class="number">Amount</th></tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}<br><span class="muted">{{ $item->variant_name }}</span></td>
                    <td class="number">{{ number_format($item->quantity) }}</td>
                    <td class="number">Rp{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="number">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary">
        <tr><td>Subtotal</td><td class="number">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</td></tr>
        <tr><td>Discount</td><td class="number">-Rp{{ number_format($order->discount_amount, 0, ',', '.') }}</td></tr>
        <tr><td>Admin fee</td><td class="number">Rp{{ number_format($order->bank_charger, 0, ',', '.') }}</td></tr>
        <tr><td class="total">Total paid</td><td class="number total">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</td></tr>
    </table>
</body>
</html>
