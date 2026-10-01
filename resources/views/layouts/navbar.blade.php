<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">

        <a class="navbar-brand fw-bold text-primary" href="{{ url('/') }}">
            Spoken English LMS
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav mx-auto">

                <li class="nav-item">
                    <a class="nav-link" href="/">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/about">About</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/courses">Courses</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">Teachers</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">Gallery</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/contact">Contact</a>
                </li>

            </ul>

            <ul class="navbar-nav">

                @guest

                    <li class="nav-item">
                        <a class="btn btn-outline-primary me-2"
                           href="{{ route('login') }}">
                            Login
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="btn btn-primary"
                           href="{{ route('register') }}">
                            Register
                        </a>
                    </li>

                @else

                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle"
                           href="#"
                           data-bs-toggle="dropdown">

                            {{ Auth::user()->name }}

                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">

                            <li>
                                <a class="dropdown-item"
                                   href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('student.dashboard') }}">
                                    Dashboard
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>

                                <form method="POST"
                                      action="{{ route('logout') }}">
                                    @csrf

                                    <button class="dropdown-item">
                                        Logout
                                    </button>

                                </form>

                            </li>

                        </ul>

                    </li>

                @endguest

            </ul>

        </div>

    </div>
</nav>