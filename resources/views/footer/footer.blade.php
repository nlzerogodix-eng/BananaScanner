<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer</title>
</head>
<body>
    <!-- Footer -->
    <footer class="footer-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="footer-brand mb-3">
                        <img src="images/logo.png" alt="Logo" style="width: 50px" class="me-2">
                        <span class="h5 text-success fw-bold">BANANA SCAN</span>
                    </div>
                    <p class="text-muted">Advanced AI-powered solution for detecting banana leaf diseases with precision and speed.</p>
                    
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="fw-bold text-success mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('home') }}" class="text-muted text-decoration-none">Home</a></li>
                        <li class="mb-2"><a href="{{ route('model') }}" class="text-muted text-decoration-none">How it Works</a></li>
                        <li class="mb-2"><a href="{{ route('support') }}" class="text-muted text-decoration-none">Support</a></li>
                        <li class="mb-2"><a href="{{ route('about') }}" class="text-muted text-decoration-none">About Us</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="fw-bold text-success mb-3">Diseases</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('support') }}#FAQ" class="text-muted text-decoration-none">Sigatoka Disease</a></li>
                        <li class="mb-2"><a href="{{ route('support') }}#FAQ" class="text-muted text-decoration-none">Panama Disease</a></li>
                        <li class="mb-2"><a href="{{ route('support') }}#FAQ" class="text-muted text-decoration-none">Healthy Leaves</a></li>
                        <li class="mb-2"><a href="{{ route('support') }}#FAQ" class="text-muted text-decoration-none">Prevention Tips</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="fw-bold text-success mb-3">Contact Info</h5>
                    <ul class="list-unstyled text-muted">
                        <li class="mb-2">📧 scan@bananascan.com.ph</li>
                        <li class="mb-2">📞 (+63) 926-508-6466</li>
                        <li class="mb-2">📍 Carcor, Poblacion, New Sambog, Carmen, DDN</li>
                        <li class="mb-2">🕒 Mon-Fri: 8AM-4PM</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">&copy; 2024 Banana Scan. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-muted text-decoration-none me-3">Privacy Policy</a>
                    <a href="#" class="text-muted text-decoration-none me-3">Terms of Service</a>
                    <a href="#" class="text-muted text-decoration-none">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer> 
</body>
</html>