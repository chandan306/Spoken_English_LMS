@extends('student.layout')

@section('content')

<h3 class="mb-4">
    Welcome, {{ auth()->user()->name }}
</h3>

<div class="row">

    <div class="col-md-3">
        <div class="card shadow border-0 rounded-4">
            <div class="card-body">
                <h5>My Courses</h5>
                <h2>5</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow border-0 rounded-4">
            <div class="card-body">
                <h5>Live Classes</h5>
                <h2>3</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow border-0 rounded-4">
            <div class="card-body">
                <h5>Certificates</h5>
                <h2>1</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow border-0 rounded-4">
            <div class="card-body">
                <h5>Payments</h5>
                <h2>₹4999</h2>
            </div>
        </div>
    </div>

</div>

@endsection