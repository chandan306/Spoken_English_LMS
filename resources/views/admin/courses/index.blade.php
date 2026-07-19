@extends('admin.layouts.app') @section('content')

<div class="d-flex justify-content-between mb-3">
    <h2>Course List</h2>

    <a href="{{ route('courses.create') }}" class="btn btn-primary"> Add Course </a>
</div>
<table class="table table-bordered table-hover">
    <thead  class="table-dark">
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Course</th>
            <th>Price</th>
            <th>Duration</th>
            <th>Status</th>
            <th width="180">Action</th>
        </tr>
    </thead>

   <tbody>
    @forelse($courses as $course)

    <tr>
        <td>{{ $course->id }}</td>
        <td>
            @if($course->image)
            <img src="{{ asset('storage/'.$course->image) }}" width="60" height="60" class="rounded" />
            @else
            <img src="https://via.placeholder.com/60" class="rounded" />
            @endif
        </td>
        <td>{{ $course->course_name }}</td>
        <td>₹ {{ $course->price }}</td>
        <td>{{ $course->duration }} Days</td>
        <td>
            @if($course->status=='Active')
            <span class="badge bg-success"> Active </span>
             @else
            <span class="badge bg-danger"> Inactive </span>
            @endif
        </td>
        <td>
            <a href="{{ route('courses.edit',$course->id) }}" class="btn btn-warning btn-sm"> Edit </a>
            <form action="{{ route('courses.destroy',$course->id) }}" method="POST" class="d-inline delete-form">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm">Delete</button>
            </form>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="7" class="text-center">No Course Found</td>
    </tr>
    @endforelse
</tbody>
</table>
<!-- <div class="mt-3">
    {{ $courses->links() }}
</div> -->

@endsection
