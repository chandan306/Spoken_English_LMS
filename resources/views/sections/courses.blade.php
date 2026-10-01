<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Popular Courses</h2>
            <p class="text-muted">Choose the best course to improve your English.</p>
        </div>
        <div class="row">
            @forelse($courses as $course)
                <div class="col-md-4 mb-4">
                    <div class="card shadow border-0 h-100">
                        <img src="{{ $course->image ? asset('storage/'.$course->image) : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600' }}" class="card-img-top" height="220" alt="{{ $course->course_name }}">
                        <div class="card-body">
                            <h4>{{ $course->course_name }}</h4>
                            <p>{{ $course->description }}</p>
                            <h5 class="text-primary">INR {{ number_format((float) $course->effective_price, 2) }}</h5>
                            <a href="{{ route('courses.details', ['course' => $course]) }}" class="btn btn-outline-primary w-100 mb-2">Course Details</a>
                            @auth
                                @if(auth()->user()->role === 'student')
                                    <form action="{{ route('payment.checkout', ['course' => $course]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary w-100">Enroll Now</button>
                                    </form>
                                @else
                                    <p class="mb-0">Student accounts can enroll.</p>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary w-100">Login to Enroll</a>
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12"><p class="text-muted text-center">No courses are available right now.</p></div>
            @endforelse
        </div>
    </div>
</section>
