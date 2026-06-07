<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('bootstrap/bootstrap.min.css') }}">
    <script src="{{ asset('bootstrap/bootstrap.bundle.min.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('structures/style.css') }}">
    <title>About Us - Banana Scan</title>
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
                        <a class="nav-link" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('model') }}">Scan Now</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('support') }}">Support</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link teks" href="{{ route('about') }}">About Us</a>
                    </li>
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
    <section class="hero-section1">
        <div class="container">
            <h1>Our Journey</h1>
            <p>
                Since our founding, Banana Scan has grown to become a leader in agricultural technology, 
                helping farmers locally detect and prevent banana diseases with AI-powered solutions.
            </p>
            <a href="#journey" class="btn btn-light btn-lg">Explore Our Journey</a>
        </div>
    </section>

    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number">5K+</span>
                        <span class="stat-label">Farmers Helped</span>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number">92.89%</span>
                        <span class="stat-label">Accuracy Rate</span>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number">20+</span>
                        <span class="stat-label">Plantations</span>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number">24/7</span>
                        <span class="stat-label">Support</span>
                    </div>
                </div>
            </div>
        </div>  
    </section>

    <section class="journey-section" id="journey">
        <div class="container">
            <h2>Our Journey</h2>
            <div class="simple-timeline">
                <div class="timeline-item">
                    <div class="timeline-content" style="text-align: justify;">
                        <span class="timeline-year">2025</span>
                        <h3>Our Foundation</h3>
                        <p>
                            Banana Scan was founded by a team of agricultural experts and AI researchers with a mission to revolutionize banana farming through technology.
                        </p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-content" style="text-align: justify;">
                        <span class="timeline-year">2026</span>
                        <h3>Future Vision</h3>
                        <p>
                            Continuing to innovate with plans for real-time monitoring solutions and expanding to other crops, aiming to help 50,000 farmers by 2026.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="simplify-section">
        <div class="container">
            <h2>SIMPLIFY</h2>
            <h3>Simplify Your Security Operations With Arctic v4</h3>
            <p>Operational expertise, delivered by experts, 90% less hassle, and 24/7 services.</p>
            <p>The Banana Scan platform delivers comprehensive security operations.</p>
            <a href="{{ route('model') }}" class="btn btn-light btn-lg mt-3">Scan Now</a>
        </div>
    </section>

    <!-- Meet the team section -->
    <section class="team-section">
        <div class="container-fluid">
            <h1 class="mb-5" id="team">Meet The Team</h1>
            <div class="row justify-content-center g-4">
                <div class="col-sm-10 col-md-10 col-lg-5 mb-4">
                    <div class="team-card h-100">
                        <img src="images/4.png" alt="Adrian Plaza" class="team-img">
                        <div class="team-card-body">
                            <h4 class="team-name">Adrian Plaza</h4>
                            <p class="team-role">IT Specialist & Banana Disease Researcher</p>
                            <h5 class="fw-bold">Skills</h5>
                            <ul>
                                <li>AI & Machine Learning</li>
                                <li>Image Recognition & Computer Vision</li>
                                <li>Data Analysis & Model Optimization</li>
                                <li>Banana Plant Disease Researcher</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-sm-10 col-md-10 col-lg-5 mb-4">
                    <div class="team-card h-100">
                        <img src="images/5.jpg" alt="John Dave Galvez" class="team-img">
                        <div class="team-card-body">
                            <h4 class="team-name">John Dave Galvez</h4>
                            <p class="team-role">IT Specialist</p>
                            <h5 class="fw-bold">Skills</h5>
                            <ul>
                                <li>AI & Machine Learning</li>
                                <li>Image Recognition & Computer Vision</li>
                                <li>Layout & Contextual Designs</li>
                                <li>Banana Plant Disease Researcher</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-10 col-md-10 col-lg-5 mb-4">
                    <div class="team-card h-100">
                        <img src="images/6.jpg" alt="Axcel D. Tabada" class="team-img">
                        <div class="team-card-body">
                            <h4 class="team-name">Axcel D. Tabada</h4>
                            <p class="team-role">IT Specialist</p>
                            <h5 class="fw-bold">Skills</h5>
                            <ul>
                                <li>Image Recognition & Computer Vision</li>
                                <li>Data Researcher</li>
                                <li>Data Entry Specialist</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-sm-10 col-md-10 col-lg-5 mb-4">
                    <div class="team-card h-100">
                        <img src="images/7.jpg" alt="Elljay C. Ballo" class="team-img">
                        <div class="team-card-body">
                            <h4 class="team-name">Elljay C. Ballo</h4>
                            <p class="team-role">IT Specialist</p>
                            <h5 class="fw-bold">Skills</h5>
                            <ul>
                                <li>Machine Learning</li>
                                <li>Image Recognition & Computer Vision</li>
                                <li>Data Researcher</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <hr class="container">

    @include('footer.footer')

</body>
</html>