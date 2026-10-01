@extends('student.layout')

@section('content')
<div class="alert alert-warning">
    <h1>Payment cancelled</h1>
    <p>No payment was recorded. You can return to the course catalog and try again.</p>
    <a class="btn btn-primary" href="{{ route('courses.catalog') }}">Browse courses</a>
</div>
@endsection
