<div class="sidebar">

    <div class="logo">

        <h3>Spoken LMS</h3>

    </div>

    <ul>

        <li>

            <a href="{{ route('student.dashboard') }}">

                <i class="fas fa-home"></i>

                Dashboard

            </a>

        </li>

        <li>

            <a href="{{ route('student.courses') }}">

                <i class="fas fa-book-open"></i>

                My Courses

            </a>

        </li>

        <li>

            <a href="{{ route('student.live.classes') }}">

                <i class="fas fa-video"></i>

                Live Classes

            </a>

        </li>

        <li>

            <a href="#">

                <i class="fas fa-file-alt"></i>

                Assignments

            </a>

        </li>

        <li>

            <a href="#">

                <i class="fas fa-award"></i>

                Certificates

            </a>

        </li>

        <li>

            <a href="#">

                <i class="fas fa-credit-card"></i>

                Payments

            </a>

        </li>

        <li>

            <a href="#">

                <i class="fas fa-user"></i>

                Profile

            </a>

        </li>

        <li>

            <form method="POST" action="{{ route('logout') }}">

                @csrf

                <button class="btn text-white w-100 text-start">

                    <i class="fas fa-sign-out-alt"></i>

                    Logout

                </button>

            </form>

        </li>

    </ul>

</div>

<style>

.sidebar{

    position:fixed;

    width:260px;

    height:100vh;

    background:#0d6efd;

    color:#fff;

    left:0;

    top:0;

}

.logo{

    padding:25px;

    text-align:center;

    border-bottom:1px solid rgba(255,255,255,.2);

}

.sidebar ul{

    list-style:none;

    padding:0;

    margin:0;

}

.sidebar ul li{

    border-bottom:1px solid rgba(255,255,255,.08);

}

.sidebar ul li a{

    display:block;

    color:#fff;

    text-decoration:none;

    padding:16px 25px;

    transition:.3s;

}

.sidebar ul li a:hover{

    background:#084298;

    padding-left:35px;

}

.sidebar ul li a i{

    width:25px;

}

.sidebar button{

    padding:16px 25px;

}

</style>