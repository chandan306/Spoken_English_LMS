@extends('student.layout')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm mx-auto" style="max-width: 680px">
        <div class="card-body p-4 p-md-5 text-center">
            <div class="display-4 text-warning"><i class="fa-solid fa-circle-exclamation"></i></div>
            <h1 class="h2 mt-2">Payment Not Completed</h1>
            <p class="text-muted">Order {{ $order->order_number }} was not confirmed as paid. You have not been enrolled or invoiced.</p>
            @if($order->status === 'pending')
                <p>You can resume checkout for {{ $order->course->course_name }}.</p>
                <form action="{{ route('payment.checkout', ['course' => $order->course]) }}" method="POST">
                    @csrf
                    <button class="btn btn-primary" type="submit">Try Payment Again</button>
                </form>
            @else
                <a class="btn btn-primary" href="{{ route('courses.details', ['course' => $order->course]) }}">Return to Course</a>
            @endif
            <a class="btn btn-outline-secondary mt-2" href="{{ route('student.dashboard') }}">Back to Dashboard</a>
        </div>
    </div>
</div>
@endsection
