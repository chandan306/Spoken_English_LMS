<div class="bg-dark text-white p-3 vh-100">
    <h4 class="mb-4">Admin Menu</h4>

    <ul class="nav flex-column">
        <li class="nav-item mb-2">
            <a href="{{ route('admin.dashboard') }}" class="nav-link text-white">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="#" class="nav-link text-white"> <i class="bi bi-book"></i> Courses </a>
        </li>

        <li class="nav-item mb-2">
            <a href="#" class="nav-link text-white"> <i class="bi bi-person-workspace"></i> Teachers </a>
        </li>

        <li class="nav-item mb-2">
            <a href="#" class="nav-link text-white"> <i class="bi bi-images"></i> Gallery </a>
        </li>

        <li class="nav-item mb-2">
            <a href="#" class="nav-link text-white"> <i class="bi bi-chat-left-text"></i> Testimonials </a>
        </li>

        <li class="nav-item mb-2">
            <a href="#" class="nav-link text-white"> <i class="bi bi-envelope"></i> Contact Enquiry </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('/admin/courses') }}" class="nav-link text-white">
                <i class="bi bi-book"></i>
                Courses
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('/admin/teachers') }}" class="nav-link text-white">
                <i class="bi bi-person-workspace"></i>
                Teachers
            </a>
        </li>
    </ul>
</div>
