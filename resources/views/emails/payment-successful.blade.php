<p>Dear {{ $invoice->user->name }},</p>

<p>Your payment was successful and your enrollment is active.</p>

<dl>
    <dt>Course</dt><dd>{{ $invoice->order->course->course_name }}</dd>
    <dt>Order Number</dt><dd>{{ $invoice->order->order_number }}</dd>
    <dt>Transaction ID</dt><dd>{{ $invoice->payment->transaction_id ?? 'Pending gateway reference' }}</dd>
    <dt>Amount</dt><dd>{{ $invoice->order->currency }} {{ number_format((float) $invoice->total_amount, 2) }}</dd>
    <dt>Payment Date</dt><dd>{{ $invoice->payment->paid_at?->format('Y-m-d H:i') }}</dd>
    <dt>Payment Status</dt><dd>{{ ucfirst($invoice->payment->status) }}</dd>
</dl>

<p>Your invoice is attached to this email.</p>
