<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Student Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>

        body{
            background:#f5f7fb;
            overflow-x:hidden;
        }

        .main-content{
            margin-left:260px;
            transition:.3s;
        }

        .top-navbar{

            height:70px;
            background:#fff;
            box-shadow:0 2px 15px rgba(0,0,0,.08);

            display:flex;
            justify-content:space-between;
            align-items:center;

            padding:0 30px;

        }

        .profile-img{

            width:45px;
            height:45px;
            border-radius:50%;
            object-fit:cover;

        }

    </style>

</head>

<body>

@include('student.sidebar')

<div class="main-content">

    <div class="top-navbar">

        <h4 class="fw-bold">
            Student Dashboard
        </h4>

        <div class="d-flex align-items-center">

            <span class="me-3">

                {{ auth()->user()->name }}

            </span>

            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0D6EFD&color=fff"
                 class="profile-img">

        </div>

    </div>

    <div class="container-fluid p-4">

        @yield('content')

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>