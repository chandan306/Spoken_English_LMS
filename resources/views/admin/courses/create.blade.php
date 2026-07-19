@extends('admin.layouts.app') @section('content')

<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Add New Course</h4>

        <a href="{{ route('courses.index') }}" class="btn btn-secondary"> <i class="bi bi-arrow-left"></i> Back </a>
    </div>

    <div class="card-body">
        <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Course Name</label>

                    <input type="text" name="course_name" class="form-control" value="{{ old('course_name') }}" />
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Price</label>

                    <input type="number" name="price" class="form-control" value="{{ old('price') }}" />
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>

                <textarea name="description" rows="4" class="form-control">{{ old('description') }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Duration (Days)</label>

                    <input type="number" name="duration" class="form-control" value="{{ old('duration') }}" />
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Image</label>

                    <input type="file" name="image" class="form-control" />
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Status</label>

                    <select name="status" class="form-select">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Save Course</button>

            <a href="{{ route('courses.index') }}" class="btn btn-danger"> Cancel </a>
        </form>
    </div>
</div>

@endsection
