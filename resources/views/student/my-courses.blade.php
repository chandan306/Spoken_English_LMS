@extends('student.layout')

@section('content')

<div class="container-fluid">

    <h3 class="mb-4">My Courses</h3>

    <div class="row">

        @forelse($enrollments as $enrollment)
            @php($course = $enrollment->course)

            <div class="col-lg-4 mb-4">

                <div class="card shadow border-0">

                    @if($course->image)<img src="{{ asset('storage/'.$course->image) }}"
                         class="card-img-top"
                         style="height:220px;object-fit:cover;" alt="{{ $course->course_name }}">@endif

                    <div class="card-body">

                        <h5>{{ $course->course_name }}</h5>
                        <p>{{ $course->description }}</p>
                        <p><strong>Duration:</strong> {{ $course->duration }} days</p>
                        <p><strong>Purchase amount:</strong> {{ $enrollment->order->currency }} {{ number_format((float) $enrollment->order->amount, 2) }}</p>
                        <p><strong>Enrolled:</strong> {{ $enrollment->enrolled_at->format('Y-m-d') }}</p>
                        <p><strong>Payment:</strong> {{ ucfirst($enrollment->payment->status) }}</p>
                        <p><strong>Access:</strong> {{ ucfirst($enrollment->status) }}</p>

                        <a href="{{ route('courses.details', ['course' => $course]) }}" class="btn btn-primary w-100">
                            Continue Learning
                        </a>

                    </div>

                </div>

            </div>

        @empty

            <div class="col-12">
                <div class="alert alert-warning">
                No active enrollments found.
                </div>
            </div>

        @endforelse

    </div>

</div>

@endsection
