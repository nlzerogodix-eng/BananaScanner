<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('../images/icon.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('../bootstrap/bootstrap.min.css') }}">
    <script src="{{ asset('../bootstrap/bootstrap.bundle.min.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('../structures/adminstyle.css') }}">
    <title>Admin Dashboard - Banana Scan</title>
</head>
<body>
    
    <div class="admin-sidebar">
        <div class="sidebar-brand">
            <h4 class="offcanvas-title mb-0 fw-bold">
                BANANA SCAN
            </h4>
            <small class="opacity-75">
                Arctic v4.0
            </small>
        </div>
        
        <div class="user-info px-3 mb-4">
            <div class="d-flex align-items-center">
                @if(Auth::user()->profile_picture_url)
                    <img src="{{ Auth::user()->profile_picture_url }}" 
                         class="rounded-circle me-3" 
                         style="width: 50px; height: 50px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center me-3" 
                         style="width: 50px; height: 50px;">
                        <i class="fas fa-user"></i>
                    </div>
                @endif
                <div>
                    <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                    <small class="opacity-75">Administrator</small>
                </div>
            </div>
        </div>
        
        <ul class="sidebar-nav">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Users Management
                </a>
            </li>
            <li>
                <a href="{{ route('admin.scans') }}" class="{{ request()->routeIs('admin.scans') ? 'active' : '' }}">
                    <i class="fas fa-search"></i> Scan History
                </a>
            </li>
            <li>
                <a href="{{ route('admin.profile.edit') }}" class="{{ request()->routeIs('admin.profile.edit') ? 'active' : '' }}">
                    <i class="fas fa-user-shield"></i> Admin Profile
                </a>
            </li>
            <li class="mt-4">
                <a href="{{ route('home') }}">
                    <i class="fas fa-home"></i> Back to Main Site
                </a>
            </li>
            <li>
                <a href="#" class="text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
    
    <div class="admin-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @yield('admin-content')
    </div>
    
    <!-- Hidden Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
    
    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            // Create mobile menu toggle button
            const mobileToggle = document.createElement('button');
            mobileToggle.className = 'mobile-menu-toggle';
            mobileToggle.innerHTML = '<i class="fas fa-bars"></i>';
            mobileToggle.setAttribute('aria-label', 'Toggle menu');
            document.body.appendChild(mobileToggle);
            
            // Toggle sidebar on mobile
            mobileToggle.addEventListener('click', function() {
                document.querySelector('.admin-sidebar').classList.toggle('show');
                this.innerHTML = document.querySelector('.admin-sidebar').classList.contains('show') 
                    ? '<i class="fas fa-times"></i>' 
                    : '<i class="fas fa-bars"></i>';
            });
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(e) {
                const sidebar = document.querySelector('.admin-sidebar');
                if (window.innerWidth <= 991.98 && 
                    !sidebar.contains(e.target) && 
                    !mobileToggle.contains(e.target) && 
                    sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                    mobileToggle.innerHTML = '<i class="fas fa-bars"></i>';
                }
            });
            
            // Auto-close sidebar on navigation click for mobile
            document.querySelectorAll('.sidebar-nav a').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 991.98) {
                        document.querySelector('.admin-sidebar').classList.remove('show');
                        mobileToggle.innerHTML = '<i class="fas fa-bars"></i>';
                    }
                });
            });
            
            // Add active class to current page in sidebar
            const currentPath = window.location.pathname;
            document.querySelectorAll('.sidebar-nav a').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });
            
            // Smooth animations for progress bars
            document.querySelectorAll('.progress-bar').forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 300);
            });
            
            // Auto-dismiss alerts with animation
            setTimeout(() => {
                document.querySelectorAll('.alert').forEach(alert => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        if (alert.parentNode) {
                            alert.remove();
                        }
                    }, 300);
                });
            }, 5000);
            
            // Prevent form double submission
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                    }
                });
            });
        });
        
        // Window resize handler
        window.addEventListener('resize', function() {
            const sidebar = document.querySelector('.admin-sidebar');
            const mobileToggle = document.querySelector('.mobile-menu-toggle');
            
            if (window.innerWidth > 991.98) {
                sidebar.classList.remove('show');
                if (mobileToggle) {
                    mobileToggle.style.display = 'none';
                }
            } else {
                if (mobileToggle) {
                    mobileToggle.style.display = 'flex';
                }
            }
        });
        
        // Trigger initial resize check
        window.dispatchEvent(new Event('resize'));
    </script>
</body>
</html>