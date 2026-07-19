<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />

        <title>Register</title>

        @vite(['resources/css/app.css','resources/js/app.js'])

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    </head>

    <body class="bg-light">
        <div class="container">
            <div class="row justify-content-center align-items-center vh-100">
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-header bg-success text-white">
                            <h3 class="text-center">Register</h3>
                        </div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('register') }}">
                                @csrf

                                <div class="mb-3">
                                    <label>Name</label>

                                    <input type="text" class="form-control" name="name" required />
                                </div>

                                <div class="mb-3">
                                    <label>Email</label>

                                    <input type="email" class="form-control" name="email" required />
                                </div>

                                <div class="mb-3">
                                    <label>Password</label>

                                    <input type="password" class="form-control" name="password" required />
                                </div>

                                <div class="mb-3">
                                    <label>Confirm Password</label>

                                    <input type="password" class="form-control" name="password_confirmation" required />
                                </div>

                                <button class="btn btn-success w-100">Register</button>

                                <div class="text-center mt-3">
                                    <a href="{{ route('login') }}"> Already Registered? </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
