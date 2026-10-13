<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt Voucher #REC-{{ str_pad($receipt->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            margin: 25mm 20mm 20mm 20mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 13px;
            color: #1e293b;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .voucher-card {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 24px;
            position: relative;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #cbd5e1;
            padding-bottom: 18px;
            margin-bottom: 20px;
        }
        .brand-title {
            font-size: 24px;
            font-weight: bold;
            color: #4338ca;
            margin: 0;
            line-height: 1.1;
        }
        .brand-subtitle {
            font-size: 11px;
            color: #64748b;
            margin-top: 4px;
        }
        .voucher-title-badge {
            display: inline-block;
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .voucher-id {
            font-size: 20px;
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            color: #0f172a;
            margin: 6px 0 2px 0;
        }
        .amount-box {
            background-color: #f0fdf4;
            border: 2px solid #86efac;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 22px;
        }
        .amount-box table {
            width: 100%;
        }
        .amount-label {
            font-size: 11px;
            font-weight: bold;
            color: #15803d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .amount-val {
            font-size: 28px;
            font-weight: bold;
            font-family: 'Courier New', Courier, monospace;
            color: #166534;
            margin-top: 4px;
        }
        .method-badge {
            background-color: #059669;
            color: #ffffff;
            font-size: 12px;
            font-weight: bold;
            padding: 5px 12px;
            border-radius: 4px;
            display: inline-block;
        }
        .details-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: separate;
            border-spacing: 12px 0;
        }
        .details-col {
            width: 50%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 14px;
            vertical-align: top;
        }
        .col-label {
            font-size: 10px;
            font-weight: bold;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .col-val-main {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
        }
        .col-val-sub {
            font-size: 11px;
            color: #64748b;
            margin-top: 3px;
        }
        .notes-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 14px;
            margin-bottom: 24px;
        }
        .signature-table {
            width: 100%;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px dashed #cbd5e1;
        }
        .sig-line {
            border-top: 1px solid #64748b;
            width: 70%;
            margin: 40px auto 6px auto;
        }
        .sig-title {
            font-size: 11px;
            font-weight: bold;
            color: #334155;
            text-align: center;
        }
        .sig-sub {
            font-size: 10px;
            color: #94a3b8;
            text-align: center;
        }
        .footer-note {
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            margin-top: 25px;
            border-top: 1px solid #f1f5f9;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <div class="voucher-card">
        <!-- Header -->
        <table class="header-table" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <h1 class="brand-title">CargoPort</h1>
                    <div class="brand-subtitle">
                        Shipping Logistics & Terminal Invoicing System<br>
                        Commercial Port Operations & Freight Solutions
                    </div>
                </td>
                <td style="width: 40%; text-align: right; vertical-align: top;">
                    <span class="voucher-title-badge">&#9679; Official Payment Receipt</span>
                    <div class="voucher-id">#REC-{{ str_pad($receipt->id, 4, '0', STR_PAD_LEFT) }}</div>
                    <div style="font-size: 11px; color: #64748b;">
                        Date: {{ $receipt->payment_date ? $receipt->payment_date->format('F d, Y') : '—' }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- Amount Box -->
        <div class="amount-box">
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <td style="vertical-align: middle;">
                        <div class="amount-label">Amount Received / المبلغ المقبوض</div>
                        <div class="amount-val">${{ number_format($receipt->amount_paid, 2) }}</div>
                    </td>
                    <td style="text-align: right; vertical-align: middle;">
                        <div class="amount-label" style="margin-bottom: 6px;">Payment Method</div>
                        <span class="method-badge">{{ $receipt->payment_method }}</span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Details Grid -->
        <table class="details-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="details-col">
                    <div class="col-label">Received From (Customer / Client)</div>
                    <div class="col-val-main">{{ $receipt->invoice->customer_name ?? '—' }}</div>
                    <div class="col-val-sub">Verified Shipping Client Account</div>
                </td>
                <td class="details-col">
                    <div class="col-label">For Invoice Settlement</div>
                    @if($receipt->invoice)
                        <div class="col-val-main" style="color: #4338ca; font-family: 'Courier New', Courier, monospace;">
                            {{ $receipt->invoice->invoice_id }}
                        </div>
                        <div class="col-val-sub">
                            Invoice Date: {{ $receipt->invoice->date ? $receipt->invoice->date->format('M d, Y') : '—' }}
                        </div>
                    @else
                        <div class="col-val-main">Invoice #{{ $receipt->invoice_id }}</div>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Notes Box -->
        <div class="notes-box">
            <div class="col-label">Notes & Transaction Remarks</div>
            <div style="font-size: 12px; color: #334155; margin-top: 4px;">
                {{ $receipt->notes ?: 'No additional transaction notes provided.' }}
            </div>
        </div>

        <!-- Signatures & Stamp -->
        <table class="signature-table" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 50%; text-align: center;">
                    <div class="sig-line"></div>
                    <div class="sig-title">Authorized Cashier / Receiver</div>
                    <div class="sig-sub">CargoPort Finance Dept.</div>
                </td>
                <td style="width: 50%; text-align: center;">
                    <div class="sig-line"></div>
                    <div class="sig-title">Official Accounting Stamp</div>
                    <div class="sig-sub">Paid & Settled Record</div>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer-note">
            Generated electronically on {{ now()->format('Y-m-d H:i:s') }} &bull; CargoPort Shipping Management System &bull; Strict Deletion Protection Active
        </div>
    </div>

</body>
</html>
