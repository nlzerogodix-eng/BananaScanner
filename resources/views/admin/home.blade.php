@extends('admin.dashboard')

@section('admin-content')
<div class="admin-header">
    <h1 class="h3 mb-0 text-success">
        <i class="fas fa-tachometer-alt me-2"></i>Dashboard Overview
    </h1>
    <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }}!</p>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Users</h6>
                        <h3 class="mb-0">{{ $totalUsers }}</h3>
                        <small class="text-success">
                            <i class="fas fa-users me-1"></i> Registered
                        </small>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Scans</h6>
                        <h3 class="mb-0">{{ $totalScans }}</h3>
                        <small class="text-primary">
                            <i class="fas fa-search me-1"></i> Performed
                        </small>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Successful Scans</h6>
                        <h3 class="mb-0">85%</h3>
                        <small class="text-info">
                            <i class="fas fa-check-circle me-1"></i> High accuracy
                        </small>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">User Growth</h6>
                        <h3 class="mb-0">12%</h3>
                        <small class="text-warning">
                            <i class="fas fa-arrow-up me-1"></i> This month
                        </small>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-trend-up"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Percentage Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="position-relative d-inline-block">
                        <svg class="circular-chart" width="120" height="120" viewBox="0 0 36 36">
                            <path class="circle-bg"
                                d="M18 2.0845
                                    a 15.9155 15.9155 0 0 1 0 31.831
                                    a 15.9155 15.9155 0 0 1 0 -31.831"
                                fill="none"
                                stroke="#e6e6e6"
                                stroke-width="3.8"/>
                            <path class="circle"
                                stroke-dasharray="78, 100"
                                d="M18 2.0845
                                    a 15.9155 15.9155 0 0 1 0 31.831
                                    a 15.9155 15.9155 0 0 1 0 -31.831"
                                fill="none"
                                stroke="#198754"
                                stroke-width="3.8"
                                stroke-linecap="round"/>
                            <text x="18" y="20" class="percentage">78%</text>
                            <text x="18" y="26" class="percentage-label">Healthy</text>
                        </svg>
                    </div>
                </div>
                <div class="text-center">
                    <h5 class="mb-1">Overall Health Rate</h5>
                    <p class="text-muted mb-0">Based on all scans performed</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="position-relative d-inline-block">
                        <svg class="circular-chart" width="120" height="120" viewBox="0 0 36 36">
                            <path class="circle-bg"
                                d="M18 2.0845
                                    a 15.9155 15.9155 0 0 1 0 31.831
                                    a 15.9155 15.9155 0 0 1 0 -31.831"
                                fill="none"
                                stroke="#e6e6e6"
                                stroke-width="3.8"/>
                            <path class="circle"
                                stroke-dasharray="92, 100"
                                d="M18 2.0845
                                    a 15.9155 15.9155 0 0 1 0 31.831
                                    a 15.9155 15.9155 0 0 1 0 -31.831"
                                fill="none"
                                stroke="#0d6efd"
                                stroke-width="3.8"
                                stroke-linecap="round"/>
                            <text x="18" y="20" class="percentage">92%</text>
                            <text x="18" y="26" class="percentage-label">Accuracy</text>
                        </svg>
                    </div>
                </div>
                <div class="text-center">
                    <h5 class="mb-1">System Accuracy</h5>
                    <p class="text-muted mb-0">AI detection reliability</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="position-relative d-inline-block">
                        <svg class="circular-chart" width="120" height="120" viewBox="0 0 36 36">
                            <path class="circle-bg"
                                d="M18 2.0845
                                    a 15.9155 15.9155 0 0 1 0 31.831
                                    a 15.9155 15.9155 0 0 1 0 -31.831"
                                fill="none"
                                stroke="#e6e6e6"
                                stroke-width="3.8"/>
                            <path class="circle"
                                stroke-dasharray="88, 100"
                                d="M18 2.0845
                                    a 15.9155 15.9155 0 0 1 0 31.831
                                    a 15.9155 15.9155 0 0 1 0 -31.831"
                                fill="none"
                                stroke="#6f42c1"
                                stroke-width="3.8"
                                stroke-linecap="round"/>
                            <text x="18" y="20" class="percentage">88%</text>
                            <text x="18" y="26" class="percentage-label">Active</text>
                        </svg>
                    </div>
                </div>
                <div class="text-center">
                    <h5 class="mb-1">User Activity Rate</h5>
                    <p class="text-muted mb-0">Monthly active users</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="fas fa-chart-simple me-2"></i>Quick Statistics
                </h5>
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <div class="p-3 border rounded">
                            <div class="text-success mb-2">
                                <i class="fas fa-leaf fa-2x"></i>
                            </div>
                            <h4 class="mb-1">62%</h4>
                            <p class="text-muted mb-0">Healthy Scans</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="p-3 border rounded">
                            <div class="text-warning mb-2">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                            </div>
                            <h4 class="mb-1">23%</h4>
                            <p class="text-muted mb-0">Early Detection</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="p-3 border rounded">
                            <div class="text-danger mb-2">
                                <i class="fas fa-plus-square fa-2x"></i>
                            </div>
                            <h4 class="mb-1">15%</h4>
                            <p class="text-muted mb-0">Treatment Needed</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="p-3 border rounded">
                            <div class="text-primary mb-2">
                                <i class="fas fa-user-clock fa-2x"></i>
                            </div>
                            <h4 class="mb-1">94%</h4>
                            <p class="text-muted mb-0">User Satisfaction</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Scans -->
    <div class="col-lg-8">
        <div class="table-container">
            <div class="p-3 border-bottom">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Recent Scan Activity
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Disease Detected</th>
                            <th>Confidence</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentScans as $scan)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($scan->user->profile_picture && file_exists(public_path('uploads/profile_pictures/' . $scan->user->profile_picture)))
                                        <img src="{{ asset('uploads/profile_pictures/' . $scan->user->profile_picture) }}" 
                                             class="user-avatar me-2">
                                    @else
                                        <div class="user-avatar bg-light text-dark d-flex align-items-center justify-content-center me-2">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div>{{ $scan->user->name }}</div>
                                        <small class="text-muted">{{ $scan->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $scan->disease_color }}">
                                    {{ $scan->disease_type }}
                                </span>
                            </td>
                            <td>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-{{ $scan->disease_color }}" 
                                         style="width: <?php echo $scan->confidence; ?>%"></div>
                                </div>
                                <small>{{ $scan->confidence }}%</small>
                            </td>
                            <td>{{ $scan->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <i class="fas fa-search fa-2x text-muted mb-2"></i>
                                <p class="text-muted">No scan records found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">
                <a href="{{ route('admin.scans') }}" class="btn btn-outline-success btn-sm">
                    View All Scans <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Recent Users -->
    <div class="col-lg-4">
        <div class="table-container">
            <div class="p-3 border-bottom">
                <h5 class="mb-0">
                    <i class="fas fa-user-plus me-2"></i>Recently Registered
                </h5>
            </div>
            <div class="list-group list-group-flush">
                @forelse($recentUsers as $user)
                <div class="list-group-item d-flex align-items-center">
                    @if($user->profile_picture && file_exists(public_path('uploads/profile_pictures/' . $user->profile_picture)))
                        <img src="{{ asset('uploads/profile_pictures/' . $user->profile_picture) }}" 
                             class="rounded-circle me-3" 
                             style="width: 40px; height: 40px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center me-3" 
                             style="width: 40px; height: 40px;">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif
                    <div class="flex-grow-1">
                        <h6 class="mb-0">{{ $user->name }}</h6>
                        <small class="text-muted">{{ $user->email }}</small>
                    </div>
                    <div>
                        <span class="badge badge-user">User</span>
                    </div>
                </div>
                @empty
                <div class="list-group-item text-center py-4">
                    <i class="fas fa-users fa-2x text-muted mb-2"></i>
                    <p class="text-muted">No users found</p>
                </div>
                @endforelse
            </div>
            <div class="p-3 border-top">
                <a href="{{ route('admin.users') }}" class="btn btn-outline-success btn-sm">
                    View All Users <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effect to stats cards
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px)';
                this.style.transition = 'transform 0.3s ease';
                this.style.boxShadow = '0 10px 20px rgba(0,0,0,0.1)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            });
        });
        
        // Add animation to percentage cards
        document.querySelectorAll('.circular-chart .circle').forEach(circle => {
            const length = circle.getTotalLength();
            circle.style.strokeDasharray = length + ' ' + length;
            circle.style.strokeDashoffset = length;
            
            setTimeout(() => {
                circle.style.transition = 'stroke-dashoffset 1.5s ease-in-out';
                circle.style.strokeDashoffset = circle.getAttribute('stroke-dashoffset');
            }, 300);
        });
        
        // Add pulse animation to stats
        setTimeout(() => {
            document.querySelectorAll('.stat-card').forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('pulse-animation');
                    setTimeout(() => {
                        card.classList.remove('pulse-animation');
                    }, 600);
                }, index * 200);
            });
        }, 500);
    });
</script>

<style>
    .stat-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.1);
        height: 100%;
    }
    
    .stat-card:hover {
        border-color: rgba(25, 135, 84, 0.2);
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .badge-user {
        background-color: #e8f5e9;
        color: #2e7d32;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
    }
    
    /* Circular chart styles */
    .circular-chart {
        display: block;
        margin: 0 auto;
    }
    
    .circle {
        fill: none;
        stroke-width: 3.8;
        stroke-linecap: round;
        animation: progress 1.5s ease-in-out forwards;
    }
    
    .circle-bg {
        fill: none;
        stroke: #f0f0f0;
        stroke-width: 3.8;
    }
    
    .percentage {
        fill: #333;
        font-size: 0.35em;
        font-weight: bold;
        text-anchor: middle;
    }
    
    .percentage-label {
        fill: #666;
        font-size: 0.15em;
        text-anchor: middle;
    }
    
    @keyframes progress {
        0% {
            stroke-dasharray: 0 100;
        }
    }
    
    /* Progress bar colors for different diseases */
    .badge.bg-success, .progress-bar.bg-success { background-color: #198754 !important; }
    .badge.bg-warning, .progress-bar.bg-warning { background-color: #ffc107 !important; }
    .badge.bg-danger, .progress-bar.bg-danger { background-color: #dc3545 !important; }
    .badge.bg-info, .progress-bar.bg-info { background-color: #0dcaf0 !important; }
    .badge.bg-primary, .progress-bar.bg-primary { background-color: #0d6efd !important; }
    
    /* Pulse animation */
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(25, 135, 84, 0); }
        100% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
    }
    
    .pulse-animation {
        animation: pulse 0.6s ease-in-out;
    }
    
    /* Border hover effect */
    .border {
        transition: all 0.3s ease;
    }
    
    .border:hover {
        border-color: #198754 !important;
        transform: translateY(-2px);
    }
    
    /* Table improvements */
    .table-container {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .table-container .table {
        margin-bottom: 0;
    }
    
    .table-container .table thead th {
        border-bottom: 2px solid #f0f0f0;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
    }
    
    .table-container .table tbody tr:hover {
        background-color: rgba(25, 135, 84, 0.02);
    }
</style>
@endsection