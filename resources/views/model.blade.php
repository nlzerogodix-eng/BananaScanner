<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="infer-route" content="{{ route('infer') }}">
    <link rel="stylesheet" href="{{ asset('bootstrap/bootstrap.min.css') }}">
    <script src="{{ asset('bootstrap/bootstrap.bundle.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('structures/style2.css') }}">
    <title>System Model - Banana Scan</title>
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
                        <a class="nav-link teks" href="{{ route('model') }}">Scan Now</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('support') }}">Support</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('about') }}">About Us</a>
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

<section>
    <div class="container py-2 col-md-9">
        <h1 class="text-center mt-4 mb-3">Banana Leaf Disease Detection</h1>
        <p class="lead text-center">
            Upload images or use your webcam to detect banana leaf diseases using
            Arctic v4.
        </p>

        <!-- Input Method Selection -->
        <div class="card mb-4" style="text-align: center;">
            <div class="card-body">
                <h5 class="card-title mb-3">Select Input Method:</h5>
                <div class="input-method">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="uploadMethod" name="inputMethod" value="upload" checked>
                        <label class="form-check-label" for="uploadMethod">Upload Image</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="webcamMethod" name="inputMethod" value="webcam">
                        <label class="form-check-label" for="webcamMethod">Use Webcam</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Section -->
        <div id="uploadSection">
            <div class="card">
                <div class="card-body">
                    <form id="inferenceForm" action="{{ route('infer') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="imageInput" class="form-label">Select Files (Max 10):</label>
                            <input class="form-control" type="file" id="imageInput" name="images[]" accept="image/*" multiple required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" id="runButton" class="btn btn-success btn-lg">Run Inference</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Webcam Section -->
        <div id="webcamSection" style="display: none;">
            <div class="card">
                <div class="card-body">
                    <div class="webcam-container text-center">
                        <div class="webcam-wrapper mb-3">
                            <video id="webcam" autoplay playsinline class="img-fluid rounded shadow"></video>
                            <canvas id="webcamCanvas" style="display: none;"></canvas>
                        </div>
                        <div class="webcam-controls">
                            <button id="startWebcam" type="button" class="btn btn-success">Start Webcam</button>
                            <button id="captureBtn" type="button" class="btn btn-warning" disabled>Capture & Analyze</button>
                            <button id="stopWebcam" type="button" class="btn btn-danger" disabled>Stop Webcam</button>
                        </div>
                    </div>
                    
                    <div id="webcamResults" class="mt-4"></div>
                </div>
            </div>
        </div>

        <!-- Loading and Results -->
        <div id="loading" class="alert alert-info text-center mt-4" style="display: none;">Processing... Please wait.</div>
        <div id="resultContainer" class="mt-4"></div>
        </div>

    <!-- Background Bubbles -->
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>

</section>

    <br><br><br><br>
    <hr class="container my-5">

    @include('footer.footer')

<script src="structures/script.js"></script>

</body>
</html>