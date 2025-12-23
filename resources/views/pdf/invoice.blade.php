<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Invoice - Mutiara Harapan Islamic School</title>
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

        .header-table {
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


        .invoice-title {
            color: #800000;
            font-size: 28px;
            font-weight: bold;
            margin: 0 0 10px 0;
            text-align: right;
        }

        .invoice-info {
            color: #666666;
            font-size: 14px;
            margin: 5px 0;
            text-align: right;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            color: #800000;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
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
            font-size: 14px;
            display: inline-block;
            width: 160px;
        }

        .info-value {
            font-size: 15px;
            color: #333333;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .invoice-table th {
            background-color: #800000;
            color: #ffffff;
            text-align: left;
            padding: 12px 15px;
            font-weight: bold;
            font-size: 14px;
        }

        .invoice-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .invoice-table tr:last-child td {
            border-bottom: none;
        }

        .amount {
            text-align: right;
            font-family: "Courier New", monospace;
            font-weight: bold;
        }

        .total-row {
            background-color: #fdf8f3;
        }

        .total-label {
            font-size: 15px;
            color: #800000;
            font-weight: bold;
            text-align: right;
            padding-right: 15px;
        }

        .total-amount {
            font-size: 16px;
            color: #800000;
            font-weight: bold;
        }

        .description-box {
            background-color: #f9f7f4;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #e8e4e0;
        }

        .description-content {
            font-size: 13px;
            color: #555555;
            line-height: 1.5;
        }

        .description-content p {
            margin: 0 0 10px 0;
        }

        /* PDF Print Optimization */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .container {
                padding: 15px;
                max-width: 100%;
            }

            .invoice-table th,
            .invoice-table td {
                padding: 10px 12px;
            }

            .header {
                page-break-inside: avoid;
            }

            .total-row {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header Section - Logo Left, Title Right -->
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="header-left">
                        <img src="{{ $logo }}" alt="Mutiara Harapan Islamic School" class="logo">
                    </td>

                    <td class="header-right">
                        <h1 class="invoice-title">INVOICE</h1>

                        <div class="invoice-info">
                            <div><strong>Invoice No:</strong> {{ $invoice_no }}</div>
                            <div><strong>Payment Date:</strong> {{ $payment_date }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Student Information -->
        <div class="section">
            <div class="section-title">Student Information</div>
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Student Name</span>
                    <span class="info-value">: {{ $student_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Academic Year</span>
                    <span class="info-value">: {{ $academic_year }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Invoice Status</span>
                    <span class="info-value">: PAID</span>
                </div>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="section">
            <div class="section-title">Payment Details</div>
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th style="text-align: right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Registration Fee</td>
                        <td class="amount">Rp {{ $registration_fee }}</td>
                    </tr>
                    <tr>
                        <td>Bank Charges</td>
                        <td class="amount">Rp {{ $bank_charger }}</td>
                    </tr>
                    <tr class="total-row">
                        <td class="total-label">TOTAL</td>
                        <td class="amount total-amount">Rp {{ $total }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Description -->
        <div class="section">
            <div class="section-title">Note</div>
            <div class="description-box">
                <div class="description-content">
                    <p>
                        1. This invoice serves as proof of payment for student
                        registration at Mutiara Harapan Islamic School.
                    </p>
                    <p>
                        2. Payments are
                        <strong style="color: #800000">non-refundable</strong> except in
                        special circumstances approved by the school administration.
                    </p>
                    <p>
                        3. Please keep this invoice as official payment documentation.
                    </p>
                    <p>
                        4. For further information, please contact the Admission
                        Department at +62 812 9182 3247.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
