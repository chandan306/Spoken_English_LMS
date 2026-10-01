@extends('student.layout')

@section('content')

<h3 class="mb-4">Live Classes</h3>

<div class="card shadow">

    <div class="card-body">

        <table class="table table-bordered align-middle">

            <thead class="table-primary">

                <tr>
                    <th>Course</th>
                    <th>Title</th>
                    <th>Teacher</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Join</th>
                </tr>

            </thead>

            <tbody>

            @forelse($classes as $class)

                <tr>

                    <td>{{ $class->course?->course_name ?? 'Course unavailable' }}</td>

                    <td>{{ $class->title }}</td>

                    <td>{{ $class->teacher_name }}</td>

                    <td>{{ $class->class_date }}</td>

                    <td>
                        {{ $class->start_time }}
                        -
                        {{ $class->end_time }}
                    </td>

                    <td>

                        @if($class->status=='Live')

                            <span class="badge bg-success">
                                Live
                            </span>

                        @elseif($class->status=='Upcoming')

                            <span class="badge bg-warning">
                                Upcoming
                            </span>

                        @else

                            <span class="badge bg-secondary">
                                Completed
                            </span>

                        @endif

                    </td>

                    <td>

                        @if($class->status=='Live')

                            <a href="{{ $class->meeting_link }}"
                               target="_blank"
                               class="btn btn-success btn-sm">

                                Join Now

                            </a>

                        @else

                            <button class="btn btn-secondary btn-sm" disabled>

                                Not Available

                            </button>

                        @endif

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="7" class="text-center">

                        No Live Classes Available

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection
