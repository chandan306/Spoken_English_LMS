<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Login</title>

        @vite(['resources/css/app.css','resources/js/app.js'])

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    </head>

    <body class="bg-light">
        <div class="container">
            <div class="row justify-content-center align-items-center vh-100">
                <div class="col-md-5">
                    <div class="card shadow">
                        <div class="card-header text-center bg-primary text-white">
                            <h3>Login</h3>
                        </div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="mb-3">
                                    <label>Email</label>

                                    <input
                                        type="email"
                                        name="email"
                                        class="form-control"
                                        value="{{ old('email') }}"
                                        required
                                    />
                                </div>

                                <div class="mb-3">
                                    <label>Password</label>

                                    <input type="password" name="password" class="form-control" required />
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="remember" />

                                    <label class="form-check-label"> Remember Me </label>
                                </div>

                                <button class="btn btn-primary w-100">Login</button>

                                <div class="text-center mt-3">
                                    <a href="{{ route('register') }}"> Create Account </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
