<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('bootstrap/bootstrap.min.css') }}">
    <script src="{{ asset('bootstrap/bootstrap.bundle.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('structures/style.css') }}">
    <title>Support - Banana Scan</title>
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
                        <a class="nav-link teks" href="{{ route('support') }}">Support</a>
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
        <div class="container mt-5 col-md-9" id="FAQ">
            <h1 class="faq-header text-center">Frequently Asked Questions</h1>
            
            <div id="faqAccordion" class="mb-5">
                <div class="card mb-2">
                    <div class="card-header">
                        <a class="btn" data-bs-toggle="collapse" href="#collapseOne">
                            <h6>FAQ 1: What is Panama Disease (Fusarium Wilt)?</h6>
                        </a>
                    </div>
                    <div id="collapseOne" class="collapse" data-bs-parent="#faqAccordion">
                        <div class="card-body" style="margin-left: 20px; margin-right: 20px; text-align: justify;">
                            <p>
                                <strong>Panama disease (Fusarium Wilt), </strong>
                                is a soil-borne fungal infection caused by Fusarium oxysporum f.sp. cubense. 
                                It attacks the roots of banana plants, causing yellowing and wilting of leaves. 
                                Once infected, the soil can remain contaminated for decades, making it very hard to control.
                            </p>
                            <p>
                                <strong>Symptoms:</strong> 
                                Yellowing of older leaves, wilting, splitting of pseudostem base, and eventual plant death.
                            </p>
                            <p>
                                <strong>Impact:</strong> 
                                Can wipe out entire banana plantations and persists in soil for decades.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-2">
                    <div class="card-header">
                        <a class="btn" data-bs-toggle="collapse" href="#collapseTwo">
                            <h6>FAQ 2: What is Sigatoka (Yellow/Black Sigatoka)?</h6>
                        </a>
                    </div>
                    <div id="collapseTwo" class="collapse" data-bs-parent="#faqAccordion">
                        <div class="card-body" style="margin-left: 20px; margin-right: 20px; text-align: justify;">
                            <p>
                                <strong>Sigatoka disease</strong> 
                                is a leaf spot disease caused by fungi (Pseudocercospora musae for Yellow Sigatoka and Pseudocercospora fijiensis for Black Sigatoka). 
                                It reduces the plant’s ability to photosynthesize, leading to lower fruit yield and quality. 
                                Black Sigatoka is more severe and spreads faster than Yellow Sigatoka.
                            </p>
                            <p>
                                <strong>Yellow Sigatoka:</strong> 
                                Caused by Pseudocercospora musae, results in yellow streaks on leaves.
                            </p>
                            <p>
                                <strong>Black Sigatoka:</strong> 
                                Caused by Pseudocercospora fijiensis, more severe with dark spots that can coalesce and kill large areas of leaf tissue.
                            </p>
                            <p>
                                <strong>Impact:</strong> 
                                Reduces photosynthetic capacity, leading to smaller fruit and lower yields.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card mb-2">
                    <div class="card-header">
                        <a class="btn" data-bs-toggle="collapse" href="#collapseThree">
                            <h6>FAQ 3: What is a Healthy banana leaf?</h6>
                        </a>
                    </div>
                    <div id="collapseThree" class="collapse" data-bs-parent="#faqAccordion">
                        <div class="card-body" style="margin-left: 20px; margin-right: 20px; text-align: justify;">
                            <p>
                                A <strong>Healthy banana</strong> leaf is vibrant, sturdy, and free from significant damage or disease. 
                                It exhibits a rich, uniform green color, indicating ample chlorophyll content and efficient photosynthesis.
                            </p>
                            <p>
                                <strong>Characteristics of a Healthy Leaf</strong>
                            </p>
                            <ul>
                                <li>No Spots or Lesions</li>
                                <li>No Discoloration</li>
                                <li>No Pests</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card mb-2">
                    <div class="card-header">
                        <a class="btn" data-bs-toggle="collapse" href="#collapseFour">
                            <h6>FAQ 4: How to prevent these diseases and eliminate such threats?</h6>
                        </a>
                    </div>
                    <div id="collapseFour" class="collapse" data-bs-parent="#faqAccordion">
                        <div class="card-body" style="margin-left: 20px; margin-right: 20px; text-align: justify;">
                            <p>
                                Prevention includes using 
                                <strong>
                                    disease-resistant banana varieties, maintaining field sanitation, removing infected plants, and ensuring proper drainage.
                                </strong> 
                                Farmers can also apply approved fungicides and practice crop rotation to reduce disease spread.
                            </p>
                            <p><strong>For Panama Disease:</strong></p>
                            <ul>
                                <li>Use disease-free planting material</li>
                                <li>Implement strict quarantine measures</li>
                                <li>Plant resistant varieties when available</li>
                                <li>Avoid moving soil from infected areas</li>
                            </ul>
                            
                            <p><strong>For Sigatoka Diseases:</strong></p>
                            <ul>
                                <li>Regular fungicide applications</li>
                                <li>Proper plantation sanitation</li>
                                <li>Good drainage and air circulation</li>
                                <li>Remove and destroy infected leaves</li>
                            </ul>
                            
                            <p><strong>General Prevention:</strong></p>
                            <ul>
                                <li>Regular monitoring and early detection</li>
                                <li>Crop rotation where possible</li>
                                <li>Use of Banana Scan for early disease identification</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="goals-section">
                <h2 class="faq-header text-center mb-4">
                    <strong>Our Goals and Objectives</strong>
                </h2>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="goal-item">
                            <h5>Early Disease Detection</h5>
                            <p>Develop accurate AI models to identify banana leaf diseases at their earliest stages, enabling prompt intervention.</p>
                        </div>
                        
                        <div class="goal-item">
                            <h5>Farmer Education</h5>
                            <p>Provide comprehensive resources and guidance to help farmers understand, prevent, and manage banana diseases.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="goal-item">
                            <h5>Sustainable Solutions</h5>
                            <p>Promote environmentally friendly farming practices that reduce reliance on chemical treatments.</p>
                        </div>
                        
                        <div class="goal-item">
                            <h5>Global Impact</h5>
                            <p>Make our technology accessible to banana farmers worldwide, particularly in developing regions.</p>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <p class="lead">We're committed to protecting banana crops and supporting the livelihoods of farmers through innovative technology.</p>
                </div>
            </div>
        </div>

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


    <hr class="container my-5">

    @include('footer.footer')

</body>
</html>