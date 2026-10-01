    @extends('admin.layouts.app') @section('content')

<div class="card shadow">
    <div class="card-header d-flex justify-content-between">
        <h4>Edit Course</h4>

        <a href="{{ route('courses.index') }}" class="btn btn-secondary"> Back </a>
    </div>

    <div class="card-body">
        <form action="{{ route('courses.update',$course->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Course Name</label>

                    <input type="text" name="course_name" value="{{ $course->course_name }}" class="form-control" />
                </div>

                <div class="col-md-6 mb-3">
                    <label>Price</label>

                    <input type="number" min="0.01" step="0.01" name="price" value="{{ $course->price }}" class="form-control" />
                </div>
                <div class="col-md-6 mb-3">
                    <label>Discount Price (optional)</label>
                    <input type="number" min="0.01" step="0.01" name="discount_price" value="{{ old('discount_price', $course->discount_price) }}" class="form-control" />
                </div>
            </div>

            <div class="mb-3">
                <label>Description</label>

                <textarea name="description" rows="4" class="form-control">{{ $course->description }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label>Duration</label>

                    <input type="number" name="duration" value="{{ $course->duration }}" class="form-control" />
                </div>

                <div class="col-md-4">
                    <label>Image</label>

                    <input type="file" name="image" class="form-control" />
                </div>

                <div class="col-md-4">
                    <label>Status</label>

                    <select name="status" class="form-select">
                        <option value="Active" {{ $course->status=='Active' ? 'selected':'' }}> Active</option>

                        <option value="Inactive" {{ $course->status=='Inactive' ? 'selected':'' }}> Inactive</option>
                    </select>
                </div>
            </div>

            <br />

            @if($course->image)

            <img src="{{ asset('storage/'.$course->image) }}" width="120" class="rounded border" />

            @endif

            <br /><br />

            <button class="btn btn-success">Update Course</button>

            <a href="{{ route('courses.index') }}" class="btn btn-danger"> Cancel </a>
        </form>
    </div>
</div>

@endsection
