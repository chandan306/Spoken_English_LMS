@extends('student.layout')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm mx-auto" style="max-width: 820px">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="display-4 text-success"><i class="fa-solid fa-circle-check"></i></div>
                <h1 class="h2 mt-2">Payment Successful</h1>
                <p class="text-muted mb-0">Your payment is verified and your course enrollment is active.</p>
            </div>

            <dl class="row mb-4">
                <dt class="col-sm-5">Order Number</dt><dd class="col-sm-7">{{ $order->order_number }}</dd>
                <dt class="col-sm-5">Transaction ID</dt><dd class="col-sm-7">{{ $order->payment->transaction_id ?? 'Pending gateway reference' }}</dd>
                <dt class="col-sm-5">Course</dt><dd class="col-sm-7">{{ $order->course->course_name }}</dd>
                <dt class="col-sm-5">Customer</dt><dd class="col-sm-7">{{ $order->user->name }} · {{ $order->user->email }}</dd>
                <dt class="col-sm-5">Amount</dt><dd class="col-sm-7">{{ $order->currency }} {{ number_format((float) $order->amount, 2) }}</dd>
                <dt class="col-sm-5">Payment Gateway</dt><dd class="col-sm-7">{{ ucfirst($order->payment->payment_gateway) }}</dd>
                <dt class="col-sm-5">Payment Date</dt><dd class="col-sm-7">{{ $order->payment->paid_at?->format('Y-m-d H:i') }}</dd>
                <dt class="col-sm-5">Payment Status</dt><dd class="col-sm-7"><span class="badge bg-success">{{ ucfirst($order->payment->status) }}</span></dd>
                <dt class="col-sm-5">Enrollment Status</dt><dd class="col-sm-7"><span class="badge bg-success">{{ ucfirst($order->enrollment->status) }}</span></dd>
            </dl>

            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <a class="btn btn-primary" href="{{ route('courses.details', ['course' => $order->course]) }}">View Course</a>
                <a class="btn btn-outline-primary" href="{{ route('invoices.download', $order->invoice) }}">Download Invoice</a>
                <a class="btn btn-outline-primary" href="{{ route('my-courses') }}">Go to My Courses</a>
                <a class="btn btn-outline-secondary" href="{{ route('student.dashboard') }}">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>
@endsection
