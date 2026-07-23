@extends('student.layout')

@section('content')

<div class="container-fluid">

    <h3 class="mb-4">My Courses</h3>

    <div class="row">

        @forelse($courses as $course)

            <div class="col-lg-4 mb-4">

                <div class="card shadow border-0">

                    <img src="{{ asset($course['image']) }}"
                         class="card-img-top"
                         style="height:220px;object-fit:cover;">

                    <div class="card-body">

                        <h5>{{ $course['title'] }}</h5>

                        <p><strong>Trainer:</strong> {{ $course['trainer'] }}</p>

                        <p><strong>Duration:</strong> {{ $course['duration'] }}</p>

                        <div class="progress mb-3">
                            <div class="progress-bar bg-success"
                                 style="width: {{ $course['progress'] }}%">
                                {{ $course['progress'] }}%
                            </div>
                        </div>

                        <a href="#" class="btn btn-primary w-100">
                            Continue Learning
                        </a>

                    </div>

                </div>

            </div>

        @empty

            <div class="col-12">
                <div class="alert alert-warning">
                    No courses found.
                </div>
            </div>

        @endforelse

    </div>

</div>

@endsection