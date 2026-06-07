<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('bootstrap/bootstrap.min.css') }}">
    <script src="{{ asset('bootstrap/bootstrap.bundle.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('structures/style.css') }}">
    <title>Home Page - Banana Scan</title>
</head>
<body>
    
    @include('header.offcanvas')

    <nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
        <div class="container-fluid">
            <a class="px-3 navbar-brand" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanv">
                <img src="{{ asset('images/icon.png') }}" alt="Profile" style="width: 50px">
                BANANA SCAN
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" 
                    aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link teks" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('model') }}">Scan Now</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('support') }}">Support</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('about') }}">About Us</a>
                    </li>

                    <!-- Admin Panel Link -->
                    @auth
                        @if(Auth::user()->is_admin)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-shield-alt me-1"></i>Admin Panel
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
                
                <!-- Search Bar and User Profile -->
                <div class="d-flex align-items-center">
                    <form class="d-flex me-3">
                        <input class="form-control me-2" type="text" placeholder="Search...">
                    </form>
                    
                    @auth
                    <div class="dropdown user-profile-dropdown">
                        <a href="#" class="user-profile-toggle d-flex align-items-center text-white text-decoration-none dropdown-toggle" 
                        id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            @if(Auth::user()->profile_picture && file_exists(public_path('uploads/profile_pictures/' . Auth::user()->profile_picture)))
                                <img src="{{ asset('uploads/profile_pictures/' . Auth::user()->profile_picture) }}" 
                                    alt="Profile" 
                                    class="rounded-circle me-2 profile-image"
                                    style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center me-2 profile-placeholder" 
                                    style="width: 40px; height: 40px;">
                                    <span style="font-size: 1.2rem;">👨🏻‍💼</span>
                                </div>
                            @endif
                            <span class="d-none d-lg-inline user-name">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item dropdown-option" href="{{ route('profile.edit') }}">
                                    Edit Profile
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger dropdown-option" href="#" 
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hidden Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Hero Section -->
    <section class="hero-section py-5">
        <div class="container">
            <div class="row align-items-center min-vh-50">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold text-success mb-4">
                        Detect Banana Leaf Diseases with AI
                    </h1>
                    <p class="lead mb-4">
                        Advanced machine learning technology to identify Sigatoka, Panama, and healthy banana leaves with precision and speed.
                    </p>
                    <div class="d-flex gap-3 flex-wrap mb-4">
                        <a href="{{ route('model') }}" class="btn btn-success btn-lg px-4 py-2">Start Detection</a>
                        <a href="#how-it-works" class="btn btn-outline-success btn-lg px-4 py-2">Learn More</a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="hero-image-wrapper">
                        <img src="images/hero.jpg" alt="Banana Plantation" class="img-fluid rounded shadow-lg" style="max-height: 400px;">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section py-5 bg-light">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="display-5 fw-bold text-success mb-3">Why Choose Banana Scan?</h2>
                    <p class="lead">Fast, accurate, and easy-to-use disease detection</p>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row justify-content-center g-4">
                    <div class="col-sm-10 col-md-10 col-lg-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="feature-icon mb-3">
                                    <span style="font-size: 3rem;">📸</span>
                                </div>
                                <h4 class="card-title text-success">Webcam & Upload</h4>
                                <p class="card-text">Use your camera or upload images for instant analysis. Multiple input methods for your convenience.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-10 col-md-10 col-lg-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="feature-icon mb-3">
                                    <span style="font-size: 3rem;">⚡</span>
                                </div>
                                <h4 class="card-title text-success">Real-time Results</h4>
                                <p class="card-text">Get immediate diagnosis with confidence scores and detailed preventive measures for each disease.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-10 col-md-10 col-lg-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="feature-icon mb-3">
                                    <span style="font-size: 3rem;">🌱</span>
                                </div>
                                <h4 class="card-title text-success">Expert Guidance</h4>
                                <p class="card-text">Comprehensive treatment recommendations and preventive measures for healthy banana cultivation.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Disease Cards Section -->
    <section class="diseases-section py-5" id="diseases">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="display-5 fw-bold text-success mb-3">Detectable Diseases</h2>
                    <p class="lead">Identify common banana leaf diseases with high accuracy</p>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row justify-content-center g-4">
                    <div class="col-sm-10 col-md-10 col-lg-4 mb-4">
                        <div class="card h-100 border-0 shadow-lg">
                            <img class="card-img-top" src="images/2.jpg" alt="Sigatoka Disease" style="height: 250px; object-fit: cover;">
                            <div class="card-body text-center">
                                <h4 class="card-title text-warning fw-bold"> SIGATOKA </h4>
                                    <p class="card-text text-muted"> Leaf spot disease caused by fungal infection </p>
                                        <div class="symptoms mb-3">
                                            <small class="text-muted">
                                                <strong>Symptoms:</strong> 
                                                Yellow spots, leaf blight.
                                            </small>
                                        </div>
                                <a href="{{ route('support') }}#FAQ" class="btn btn-warning btn-sm">Learn More</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-10 col-md-10 col-lg-4 mb-4">
                        <div class="card h-100 border-0 shadow-lg">
                            <img class="card-img-top" src="images/3.jpg" alt="Healthy Leaf" style="height: 250px; object-fit: cover;">
                            <div class="card-body text-center">
                                <h4 class="card-title text-success fw-bold">HEALTHY</h4>
                                    <p class="card-text text-muted">Disease-free banana leaf</p>
                                        <div class="symptoms mb-3">
                                            <small class="text-muted">
                                                <strong>Characteristics:</strong> 
                                                Green color, no spots.
                                            </small>
                                        </div>
                                <a href="{{ route('support') }}#FAQ" class="btn btn-success btn-sm">Learn More</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-10 col-md-10 col-lg-4 mb-4">
                        <div class="card h-100 border-0 shadow-lg">
                            <img class="card-img-top" src="images/1.jpg" alt="Panama Disease" style="height: 250px; object-fit: cover;">
                            <div class="card-body text-center">
                                <h4 class="card-title text-danger fw-bold">PANAMA</h4>
                                    <p class="card-text text-muted">Vascular wilt disease affecting banana plants</p>
                                        <div class="symptoms mb-3">
                                            <small class="text-muted">
                                                <strong>Symptoms: </strong>
                                                Wilting, yellowing, plant death.
                                            </small>
                                        </div>
                                <a href="{{ route('support') }}#FAQ" class="btn btn-danger btn-sm">Learn More</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works py-5 bg-light" id="how-it-works">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="display-5 fw-bold text-success mb-3">How It Works</h2>
                    <p class="lead">Simple steps to detect banana leaf diseases</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-3 text-center">
                    <div class="step-number mb-3">
                        <span class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.5rem;">1</span>
                    </div>
                    <h5>Choose Input Method</h5>
                    <p class="text-muted">Select between webcam or image upload</p>
                </div>
                <div class="col-md-3 text-center">
                    <div class="step-number mb-3">
                        <span class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.5rem;">2</span>
                    </div>
                    <h5>Capture/Upload Image</h5>
                    <p class="text-muted">Take a photo or upload banana leaf images</p>
                </div>
                <div class="col-md-3 text-center">
                    <div class="step-number mb-3">
                        <span class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.5rem;">3</span>
                    </div>
                    <h5>AI Analysis</h5>
                    <p class="text-muted">Our model analyzes the image in seconds</p>
                </div>
                <div class="col-md-3 text-center">
                    <div class="step-number mb-3">
                        <span class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.5rem;">4</span>
                    </div>
                    <h5>Get Results</h5>
                    <p class="text-muted">Receive diagnosis and treatment recommendations</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Section -->
    <div class="container-fluid d-flex justify-content-center py-5">
        <div class="col-lg-10 text-center">
            <h2 class="display-5 fw-bold text-success mb-4">SCAN IN ACTION</h2>
            <p class="lead mb-4">Watch how our technology scans and detects banana leaf diseases in real time</p>
            <div class="video-wrapper">
                <video class="rounded shadow-lg" controls style="max-width: 100%;" autoplay muted loop>
                    <source src="videos/bananavideo.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            <div class="mt-4">
                <a href="{{ route('model') }}" class="btn btn-success btn-lg px-5">Try It Now</a>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <section class="cta-section py-5 bg-success text-white">
        <div class="container text-center">
            <h2 class="display-5 fw-bold mb-4">Ready to Protect Your Banana Plants?</h2>
            <p class="lead mb-4">Start detecting diseases today and ensure healthy growth for your banana plantation</p>
            <a href="{{ route('model') }}" class="btn btn-light btn-lg px-5 py-3 fw-bold text-success">Start Detection Now</a>
        </div>
    </section>

    @include('footer.footer')

</body>
</html>