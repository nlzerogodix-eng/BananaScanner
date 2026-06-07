<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="structures/style3.css">
    <title>Header</title>
</head>
<body>

    <div class="offcanvas offcanvas-start" id="offcanv" tabindex="-1">
        <div class="offcanvas-header bg-success text-white">
            <div class="container-fluid">
                <div class="d-flex align-items-center">
                    <img src="images/icon.png" alt="Logo" style="width: 60px" class="me-3">
                    <div>
                        <h4 class="offcanvas-title mb-0 fw-bold">
                            BANANA SCAN
                        </h4>
                        <small class="opacity-75">
                            Arctic v4.0
                        </small>
                    </div>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body bg-light">
            <div class="offcanvas-content">
                @auth
                    <div class="mb-4">
                        <h6 class="text-success fw-bold mb-3 text-uppercase small">Account Info</h6>
                        <div class="list-group list-group-flush">
                            <div class="list-group-item border-0 bg-light d-flex align-items-center">
                                <span class="me-3">👨🏻‍💼</span>
                                <div>
                                    <div class="fw-semibold">{{ Auth::user()->name }}</div>
                                    <small class="text-muted">{{ Auth::user()->email }}</small>
                                </div>
                            </div>

                            <a href="{{ route('profile.edit') }}" class="list-group-item list-group-item-action border-0 bg-light d-flex align-items-center">
                                <span class="me-3">⚙️</span>
                                <div>
                                    <div class="fw-semibold">Edit Account Settings</div>
                                    <small class="text-muted">Update profile & upload photo</small>
                                </div>
                            </a>
                            
                            <a href="#" 
                            class="list-group-item list-group-item-action border-0 bg-light d-flex align-items-center text-danger"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <span class="me-3">⛔</span>
                                <div>
                                    <div class="fw-semibold">Logout</div>
                                    <small class="text-muted">Sign out your account</small>
                                </div>
                            </a>
                        </div>
                    </div>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                @else
                    <div class="mb-4">
                        <h6 class="text-success fw-bold mb-3 text-uppercase small">Account</h6>
                        <div class="d-grid gap-2">
                            <a href="{{ route('login') }}" class="btn btn-success btn-sm">
                                <span class="me-2">🔑</span>
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-outline-success btn-sm">
                                <span class="me-2">📝</span>
                                Register
                            </a>
                        </div>
                    </div>
                @endauth


                <div class="mb-4">
                    <h6 class="text-success fw-bold mb-3 text-uppercase small">Main Navigation</h6>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('home') }}" class="list-group-item list-group-item-action border-0 bg-light d-flex align-items-center">
                            <span class="me-3">🏠</span>
                            <div>
                                <div class="fw-semibold">Home</div>
                                <small class="text-muted">Welcome to Banana Scan</small>
                            </div>
                        </a>
                        <a href="{{ route('model') }}" class="list-group-item list-group-item-action border-0 bg-light d-flex align-items-center">
                            <span class="me-3">🔍</span>
                            <div>
                                <div class="fw-semibold">Disease Detection</div>
                                <small class="text-muted">Scan banana leaves</small>
                            </div>
                        </a>
                        <a href="{{ route('support') }}#FAQ" class="list-group-item list-group-item-action border-0 bg-light d-flex align-items-center">
                            <span class="me-3">💡</span>
                            <div>
                                <div class="fw-semibold">Support</div>
                                <small class="text-muted">Get help & guidance</small>
                            </div>
                        </a>
                        <a href="{{ route('about') }}#team" class="list-group-item list-group-item-action border-0 bg-light d-flex align-items-center">
                            <span class="me-3">🤝🏻</span>
                            <div>
                                <div class="fw-semibold">About Us</div>
                                <small class="text-muted">Meet our team</small>
                            </div>
                        </a>
                    </div>
                </div>
            
                <div class="pt-4 border-top mt-auto">
                    <h6 class="text-success fw-bold mb-3 text-uppercase small">Contact & Support</h6>
                    <div class="text-muted small">
                        <div class="mb-2">
                            <strong>Developers:</strong>
                            <ul class="list-unstyled mb-1">
                                <li>Adrian Plaza</li>
                                <li>John Dane Galvez</li>
                                <li>Axcel Tabada</li>
                                <li>Elljay Ballo</li>
                            </ul>
                        </div>
                        <div class="mb-2">
                            <strong>Email:</strong>
                            <ul class="list-unstyled mb-1">
                                <li>scan@bananascan.com.ph</li>
                            </ul>
                        </div>
                        <div class="mb-2">
                            <strong>Contact No.:</strong>
                            <ul class="list-unstyled mb-1">
                                <li>(+63) 926-508-6466</li>
                            </ul>
                        </div>
                        <div class="mb-2">
                            <strong>Version:</strong> Arctic v4.0
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                For more details and support, please contact the developer.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>