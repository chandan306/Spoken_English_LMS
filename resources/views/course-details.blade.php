<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $course->course_name }} · Spoken English LMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('layouts.navbar')

    <main class="container py-5">
        @if($errors->has('payment'))
            <div class="alert alert-danger">{{ $errors->first('payment') }}</div>
        @endif

        <div class="row g-4 align-items-start">
            <div class="col-lg-7">
                @if($course->image)
                    <img src="{{ asset('storage/'.$course->image) }}" alt="{{ $course->course_name }}" class="img-fluid rounded shadow-sm">
                @endif
                <h1 class="mt-4">{{ $course->course_name }}</h1>
                <p class="lead">{{ $course->description }}</p>
                <p><strong>Duration:</strong> {{ $course->duration }} days</p>
            </div>
            <aside class="col-lg-5">
                <div class="card shadow-sm"><div class="card-body">
                    @if($course->discount_price)
                        <p class="text-muted text-decoration-line-through mb-1">INR {{ number_format((float) $course->price, 2) }}</p>
                    @endif
                    <h2 class="h4">INR {{ number_format((float) $course->effective_price, 2) }}</h2>
                    @auth
                        @if(auth()->user()->role === 'student')
                            @if($isEnrolled)
                                <a class="btn btn-success w-100" href="{{ route('my-courses') }}">Already enrolled · View my courses</a>
                            @else
                                <form action="{{ route('payment.checkout', ['course' => $course]) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-primary w-100" type="submit">Buy / Enroll</button>
                                </form>
                            @endif
                        @else
                            <p class="mb-0">Student accounts can enroll in this course.</p>
                        @endif
                    @else
                        <a class="btn btn-primary w-100" href="{{ route('login') }}">Login to Buy / Enroll</a>
                        <a class="btn btn-outline-primary w-100 mt-2" href="{{ route('register') }}">Create an account</a>
                    @endauth
                </div></div>
            </aside>
        </div>
    </main>

    @include('layouts.footer')
</body>
</html>
