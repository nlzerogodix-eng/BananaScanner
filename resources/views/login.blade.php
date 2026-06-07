<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="images/icon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap/bootstrap.min.css">
    <script src="bootstrap/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="structures/style.css">
    <title>Login to Banana Scan</title>
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
                        <button type="submit" class="btn btn-link alert-link p-0">logout</button>.
                    </form>
                </div>
                @endauth

                <div class="log py-4">
                    <h1 class="text-success">Sign in</h1>

                        @guest
                        <form action="{{ route('loginValidate') }}" method="post">
                            @csrf

                            <div class="mb-3 mt-5">
                                <input type="email" name="email" id="email" placeholder="Enter email...">
                                    @if($errors->has('email'))
                                        <p class="text-danger" style="font-size: 0.6rem;">{{ $errors->first('email') }}</p>
                                    @endif
                            </div>

                            <div class="mb-5    ">
                                <input type="password" name="password" id="password" placeholder="Enter password...">
                                    @if($errors->has('password'))
                                        <p class="text-danger" style="font-size: 0.6rem;">{{ $errors->first('password') }}</p>
                                    @endif
                            </div>

                                    @if($errors->has('all'))
                                        <p class="text-danger" style="font-size: 0.6rem;">{{ $errors->first('all') }}</p>
                                    @endif

                            <div class="mb-2">
                                <p class="mb-2">
                                    <input type="checkbox" id="remember" name="remember" required> 
                                    Remember me 
                                </p>
                            </div>

                            <div class="mb-4">
                                <button type="submit" class="btn btn-md btn-outline-success">LOGIN</button>
                            </div>
                        </form>

                        <p class="mb-1">Didn't have an account?</p>
                        <a href="{{ route('register') }}">Register here</a>
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