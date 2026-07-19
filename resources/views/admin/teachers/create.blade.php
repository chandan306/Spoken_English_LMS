@extends('admin.layouts.app') @section('content')

<div class="card">
    <div class="card-header bg-primary text-white">
        <h3>Add New Teacher</h3>
    </div>

    <div class="card-body">
        <form action="{{ route('teachers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required />
            </div>

            <div class="mb-3">
                <label>Designation</label>
                <input type="text" name="designation" class="form-control" required />
            </div>

            <div class="mb-3">
                <label>Qualification</label>
                <input type="text" name="qualification" class="form-control" required />
            </div>

            <div class="mb-3">
                <label>Experience (Years)</label>
                <input type="number" name="experience" class="form-control" required />
            </div>

            <div class="mb-3">
                <label>About</label>
                <textarea name="about" rows="4" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label>Photo</label>
                <input type="file" name="photo" class="form-control" />
            </div>

            <div class="mb-3">
                <label>Status</label>

                <select name="status" class="form-select">
                    <option value="Active">Active</option>

                    <option value="Inactive">Inactive</option>
                </select>
            </div>

            <button class="btn btn-success">Save Teacher</button>

            <a href="{{ route('teachers.index') }}" class="btn btn-secondary"> Back </a>
        </form>
    </div>
</div>

@endsection
