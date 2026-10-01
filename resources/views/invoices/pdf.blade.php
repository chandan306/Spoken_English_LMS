<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #202938; font-size: 12px; }
        .header { border-bottom: 2px solid #1d4ed8; padding-bottom: 16px; margin-bottom: 24px; }
        .muted { color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin-top: 24px; }
        th, td { padding: 10px; border-bottom: 1px solid #dbe2ea; text-align: left; }
        th { background: #f1f5f9; }
        .total { font-size: 16px; font-weight: bold; text-align: right; margin-top: 20px; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('invoice.company_name') }}</h1>
        @if(config('invoice.company_address'))<p class="muted">{{ config('invoice.company_address') }}</p>@endif
        @if(config('invoice.tax_number'))<p class="muted">Tax ID: {{ config('invoice.tax_number') }}</p>@endif
    </div>

    <table>
        <tr><th>Invoice Number</th><td>{{ $invoice->invoice_number }}</td><th>Invoice Date</th><td>{{ $invoice->invoice_date->format('Y-m-d') }}</td></tr>
        <tr><th>Customer</th><td>{{ $invoice->user->name }}</td><th>Email</th><td>{{ $invoice->user->email }}</td></tr>
        <tr><th>Order Number</th><td>{{ $invoice->order->order_number }}</td><th>Transaction ID</th><td>{{ $invoice->payment->transaction_id ?? 'Pending gateway reference' }}</td></tr>
        <tr><th>Payment Gateway</th><td>{{ ucfirst($invoice->payment->payment_gateway) }}</td><th>Status</th><td>{{ ucfirst($invoice->payment->status) }}</td></tr>
    </table>

    <table>
        <thead><tr><th>Course</th><th class="right">Course Price</th><th class="right">Discount</th><th class="right">Tax</th><th class="right">Total</th></tr></thead>
        <tbody><tr>
            <td>{{ $invoice->order->course->course_name }}</td>
            <td class="right">{{ $invoice->order->currency }} {{ number_format((float) $invoice->amount, 2) }}</td>
            <td class="right">{{ $invoice->order->currency }} {{ number_format((float) $invoice->discount_amount, 2) }}</td>
            <td class="right">{{ $invoice->order->currency }} {{ number_format((float) $invoice->tax_amount, 2) }}</td>
            <td class="right">{{ $invoice->order->currency }} {{ number_format((float) $invoice->total_amount, 2) }}</td>
        </tr></tbody>
    </table>

    <p class="total">Paid: {{ $invoice->order->currency }} {{ number_format((float) $invoice->total_amount, 2) }}</p>
</body>
</html>
