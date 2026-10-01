@extends('student.layout')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">My Payments</h2>
    @if(session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif
    @if($errors->has('payment'))
        <div class="alert alert-danger">{{ $errors->first('payment') }}</div>
    @endif
    <div class="card"><div class="card-body">
        <div class="table-responsive"><table class="table table-striped align-middle">
            <thead><tr><th>Course</th><th>Amount</th><th>Order</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->course?->course_name ?? 'Course unavailable' }}</td>
                        <td>{{ $payment->currency }} {{ number_format((float) $payment->amount, 2) }}</td>
                        <td>{{ $payment->order_number }}</td>
                        <td>{{ ucfirst($payment->payment_status) }}</td>
                        <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">No payments found.</td></tr>
                @endforelse
            </tbody>
        </table></div>
    </div></div>
</div>
@endsection
