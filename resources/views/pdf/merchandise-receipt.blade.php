<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Merchandise Receipt - Mutiara Harapan Islamic School</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #333333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            width: 100%;
            border-bottom: 3px solid #800000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header-table,
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-left {
            width: 50%;
            vertical-align: top;
        }

        .header-right {
            width: 50%;
            text-align: right;
            vertical-align: top;
        }

        .logo {
            height: 70px;
        }

        .receipt-title {
            color: #800000;
            font-size: 26px;
            font-weight: bold;
            margin: 0 0 10px;
            text-align: right;
        }

        .receipt-info {
            color: #666666;
            font-size: 12px;
            margin: 4px 0;
            text-align: right;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            color: #800000;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 12px;
            text-transform: uppercase;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 5px;
        }

        .info-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #800000;
        }

        .info-row {
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            color: #666666;
            font-size: 13px;
        }

        .invoice-table {
            margin-top: 10px;
        }

        .invoice-table th {
            background-color: #800000;
            color: #ffffff;
            text-align: left;
            padding: 10px 9px;
            font-weight: bold;
            font-size: 12px;
        }

        .invoice-table td {
            padding: 8px 9px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 11px;
        }

        .amount {
            text-align: right;
            white-space: nowrap;
        }

        .total-row {
            background-color: #fdf8f3;
        }

        .total-label,
        .total-amount {
            color: #800000;
            font-weight: bold;
        }

        .total-label {
            text-align: right;
        }

        .note-box {
            background-color: #f9f7f4;
            padding: 10px 14px;
            border-radius: 5px;
            border: 1px solid #e8e4e0;
            font-size: 12px;
            color: #555555;
        }

        .note-box p {
            margin: 0 0 8px;
        }

        .note-box p:last-child {
            margin-bottom: 0;
        }

        @media print {
            .container {
                padding: 15px;
                max-width: 100%;
            }

            .header,
            .total-row {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="header-left">
                        @if ($logo)
                            <img src="{{ $logo }}" alt="Mutiara Harapan Islamic School" class="logo" />
                        @endif
                    </td>
                    <td class="header-right">
                        <h1 class="receipt-title">Merchandise Receipt</h1>
                        <div class="receipt-info"><strong>Transaction No:</strong> {{ $order->code }}</div>
                        <div class="receipt-info"><strong>Payment Date:</strong>
                            {{ $order->paid_at ? $order->paid_at->copy()->setTimezone('Asia/Jakarta')->format('d M Y H:i') : now()->format('d M Y H:i') }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Customer Information</div>
            <div class="info-box">
                <div class="info-row"><span class="info-label">Parent Name:</span> {{ $order->parent_name }}</div>
                <div class="info-row"><span class="info-label">Portal Username:</span> {{ $order->portal_username }}</div>
                <div class="info-row"><span class="info-label">Email:</span> {{ $order->email }}</div>
                <div class="info-row"><span class="info-label">Phone:</span> {{ $order->phone }}</div>
                <div class="info-row"><span class="info-label">Branch:</span> {{ $order->branch_name }}</div>
                <div class="info-row"><span class="info-label">Status:</span> PAID</div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Payment Details</div>
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Variant</th>
                        <th class="amount">Qty</th>
                        <th class="amount">Unit Price</th>
                        <th class="amount">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->variant_name }}</td>
                            <td class="amount">{{ number_format($item->quantity) }}</td>
                            <td class="amount">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="amount">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="4">Subtotal</td>
                        <td class="amount">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="4">Discount</td>
                        <td class="amount">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="4">Admin Fee</td>
                        <td class="amount">Rp {{ number_format($order->bank_charger, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="4" class="total-label">TOTAL PAID</td>
                        <td class="amount total-amount">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Note</div>
            <div class="note-box">
                <p>1. This receipt is proof of payment for a merchandise order at Mutiara Harapan Islamic School.</p>
                <p>2. Please keep this receipt for your records.</p>
                <p>3. Description: {{ $description }}</p>
            </div>
        </div>
    </div>
</body>

</html>
