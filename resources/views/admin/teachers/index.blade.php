@extends('admin.layouts.app') @section('content')

<div class="d-flex justify-content-between mb-3">
    <h2>Teacher List</h2>

    <a href="{{ route('teachers.create') }}" class="btn btn-primary"> Add Teacher </a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Designation</th>
                    <th>Qualification</th>
                    <th>Experience</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($teachers as $teacher)

                <tr>
                    <td>{{ $teacher->id }}</td>

                    <td>
                        @if($teacher->photo)
                        <img src="{{ asset('storage/'.$teacher->photo) }}" width="60" class="rounded" />
                        @else No Photo @endif
                    </td>

                    <td>{{ $teacher->name }}</td>
                    <td>{{ $teacher->designation }}</td>
                    <td>{{ $teacher->qualification }}</td>
                    <td>{{ $teacher->experience }} Years</td>

                    <td>
                        @if($teacher->status=='Active')
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-danger">Inactive</span>
                        @endif
                    </td>

                    <td>
                        <a href="{{ route('teachers.edit',$teacher->id) }}" class="btn btn-warning btn-sm"> Edit </a>
                    </td>
                </tr>

                @empty

                <tr>
                    <td colspan="8" class="text-center">No Teacher Found</td>
                </tr>

                @endforelse
            </tbody>
        </table>

        {{ $teachers->links() }}
    </div>
</div>

@endsection
