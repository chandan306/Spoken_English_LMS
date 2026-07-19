@extends('admin.layouts.app')

@section('content')

        <h2 class="mb-4">Dashboard</h2>

            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h2>10</h2>

                            <h5>Courses</h5>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h2>5</h2>

                            <h5>Teachers</h5>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <div class="card bg-warning text-dark">
                        <div class="card-body text-center">
                            <h2>50</h2>

                            <h5>Students</h5>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <h2>15</h2>

                            <h5>Enquiries</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Welcome</div>

                <div class="card-body">
                    <h4>Welcome, {{ auth()->user()->name }}</h4>

                    <p>You are logged in as <strong>{{ auth()->user()->role }}</strong>.</p>
                </div>
            </div>


@endsection
