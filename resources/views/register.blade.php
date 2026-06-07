<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="images/icon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap/bootstrap.min.css">
    <script src="bootstrap/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="structures/style.css">
    <title>Register to Banana Scan</title>
</head>
<body class="login-body" style="align-content: center;">

    <div class="container" style="text-align: center;">
        <div class="row justify-content-center">
            <div class="col-10 col-sm-12 col-md-8 col-lg-6 col-xl-5 col-xxl-4">

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @auth
                <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                    You are already logged in as <strong>{{ Auth::user()->name }}</strong>. 
                    <a href="{{ route('home') }}" class="alert-link">Go to Home</a> or 
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link alert-link p-0">logout</button> to register a new account.
                    </form>
                </div>
                @endauth

                <div class="log py-4">
                    <h1 class="text-success">Register</h1>

                        @guest
                        <form action="{{ route('registerValidate') }}" method="post">
                            @csrf

                            <div class="mb-3 mt-4">
                                <input type="text" name="first_name" id="first_name" placeholder="Enter your first name...">
                                @if($errors->has('first_name'))
                                    <p class="text-danger" style="font-size: 0.6rem;">{{ $errors->first('first_name') }}</p>
                                @endif
                            </div>

                            <div class="mb-3">
                                <input type="text" name="last_name" id="last_name" placeholder="Enter your last name...">
                                @if($errors->has('last_name'))
                                    <p class="text-danger" style="font-size: 0.6rem;">{{ $errors->first('last_name') }}</p>
                            @endif
                            </div>

                            <div class="mb-3">
                                <input type="email" name="email" id="email" placeholder="Enter email address...">
                                    @if($errors->has('email'))
                                        <p class="text-danger" style="font-size: 0.6rem;">{{ $errors->first('email') }}</p>
                                    @endif
                            </div>

                            <div class="mb-3">
                                <input type="password" name="password" id="password" placeholder="Enter password...">
                                    @if($errors->has('password'))
                                        <p class="text-danger" style="font-size: 0.6rem;">{{ $errors->first('password') }}</p>
                                    @endif
                            </div>

                            <div class="mb-4">
                                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm password...">
                            </div>

                            <div class="mb-4">
                                <p class="mb-3">
                                    <input type="checkbox" name="agree" id="agree" required>
                                    I agree to the <a href="#" class="text-success">Terms & Conditions</a>
                                </p>
                                    @if($errors->has('agree'))
                                        <p class="text-danger mt-1" style="font-size: 0.8rem;">{{ $errors->first('agree') }}</p>
                                    @endif

                                <button type="submit" class="btn btn-md btn-outline-success">Create Account</button>
                                
                            </div>
                        </form>

                        <p class="mb-1">Already have an account?</p>
                        <a href="{{ route('login') }}">Login</a>
                        @endguest

                        @auth
                        <div class="text-center">
                            <p class="text-success mb-3">You're already signed in!</p>
                            <a href="{{ route('home') }}" class="btn btn-success me-2">Go to Home</a>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger">Logout</button>
                            </form>
                        </div>
                        @endauth

                </div>
            </div>
        </div>
    </div>
    
</body>
</html>