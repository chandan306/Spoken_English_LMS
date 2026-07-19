@extends('admin.layouts.app') @section('content')

<div class="card">
    <div class="card-header bg-warning">
        <h3>Edit Teacher</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('teachers.update',$teacher->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" value="{{ $teacher->name }}" class="form-control" />
            </div>
            <div class="mb-3">
                <label>Designation</label>
                <input type="text" name="designation" value="{{ $teacher->designation }}" class="form-control" />
            </div>
            <div class="mb-3">
                <label>Qualification</label>
                <input type="text" name="qualification" value="{{ $teacher->qualification }}" class="form-control" />
            </div>
            <div class="mb-3">
                <label>Experience</label>
                <input type="number" name="experience" value="{{ $teacher->experience }}" class="form-control" />
            </div>
            <div class="mb-3">
                <label>About</label>
                <textarea name="about" class="form-control" rows="4">{{ $teacher->about }}</textarea>
            </div>
            <div class="mb-3">
                <label>Current Photo</label>
                <br />
                @if($teacher->photo)
                <img src="{{ asset('storage/'.$teacher->photo) }}" width="120" class="rounded mb-2" />
                @endif
                <input type="file" name="photo" class="form-control" />
            </div>
            <div class="mb-3">
                <label>Status</label>

                <select name="status" class="form-select">
                    <option value="Active" {{ $teacher->status=='Active' ? 'selected':'' }}> Active</option>
                    <option value="Inactive" {{ $teacher->status=='Inactive' ? 'selected':'' }}> Inactive</option>
                </select>
            </div>
            <button class="btn btn-success">Update Teacher</button>
            <a href="{{ route('teachers.index') }}" class="btn btn-secondary"> Back </a>
        </form>
    </div>
</div>

@endsection
