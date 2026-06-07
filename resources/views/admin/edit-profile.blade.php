@extends('admin.dashboard')

@section('admin-content')
<div class="admin-header">
    <h1 class="h3 mb-0 text-success">
        <i class="fas fa-user-shield me-2"></i>Edit Admin Profile
    </h1>
    <p class="text-muted mb-0">Update your admin account information</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="table-container">
            <div class="p-3 border-bottom">
                <h5 class="mb-0">Admin Account Settings</h5>
            </div>
            
            <div class="p-4">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('admin.profile.update') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                @if($admin->profile_picture && file_exists(public_path('uploads/profile_pictures/' . $admin->profile_picture)))
                                    <div class="profile-img-container">
                                        <img src="{{ asset('uploads/profile_pictures/' . $admin->profile_picture) }}" 
                                             alt="Profile Picture" 
                                             class="rounded-circle border border-warning"
                                             style="width: 120px; height: 120px; object-fit: cover;"
                                             id="profileImage">
                                    </div>
                                @else
                                    <div class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center" 
                                         style="width: 120px; height: 120px; font-size: 2.5rem; border: 3px solid #ffc107;">
                                        <i class="fas fa-user-shield"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="form-text text-muted small mt-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Profile picture can be updated in the regular profile settings
                            </div>
                        </div>
                    </div>
                    
                    <!-- Replace single name field with first_name and last_name -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" 
                                   class="form-control @error('first_name') is-invalid @enderror" 
                                   id="first_name" 
                                   name="first_name" 
                                   value="{{ old('first_name', $admin->first_name) }}"
                                   required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" 
                                   class="form-control @error('last_name') is-invalid @enderror" 
                                   id="last_name" 
                                   name="last_name" 
                                   value="{{ old('last_name', $admin->last_name) }}"
                                   required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $admin->email) }}"
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="text-success mb-3">
                        <i class="fas fa-key me-2"></i>Change Password
                    </h6>
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" 
                               class="form-control @error('current_password') is-invalid @enderror" 
                               id="current_password" 
                               name="current_password"
                               placeholder="Enter current password"
                               required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" 
                               class="form-control @error('new_password') is-invalid @enderror" 
                               id="new_password" 
                               name="new_password"
                               placeholder="Enter new password (optional)">
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Leave blank if you don't want to change password
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" 
                               class="form-control" 
                               id="new_password_confirmation" 
                               name="new_password_confirmation"
                               placeholder="Confirm new password">
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Update Admin Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="table-container">
            <div class="p-3 border-bottom bg-warning bg-opacity-10">
                <h5 class="mb-0">
                    <i class="fas fa-shield-alt me-2"></i>Admin Privileges
                </h5>
            </div>
            <div class="p-4">
                <div class="alert alert-warning">
                    <h6 class="alert-heading">
                        <i class="fas fa-crown me-2"></i>Administrator Account
                    </h6>
                    <p class="mb-0">You have full access to the admin panel with the following privileges:</p>
                </div>
                
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex align-items-center">
                        <i class="fas fa-check text-success me-3"></i>
                        <div>
                            <div class="fw-semibold">View All Users</div>
                            <small class="text-muted">Access to all registered user accounts</small>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center">
                        <i class="fas fa-check text-success me-3"></i>
                        <div>
                            <div class="fw-semibold">View Scan History</div>
                            <small class="text-muted">Access to all disease detection scans</small>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center">
                        <i class="fas fa-check text-success me-3"></i>
                        <div>
                            <div class="fw-semibold">Delete Users</div>
                            <small class="text-muted">Remove regular user accounts</small>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center">
                        <i class="fas fa-check text-success me-3"></i>
                        <div>
                            <div class="fw-semibold">View Statistics</div>
                            <small class="text-muted">Access to system analytics and reports</small>
                        </div>
                    </li>
                </ul>
                
                <div class="mt-4 pt-3 border-top">
                    <h6 class="mb-3">Account Information</h6>
                    <div class="mb-2">
                        <small class="text-muted d-block">Account Type</small>
                        <span class="badge bg-success">Administrator</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Admin Since</small>
                        <div>{{ $admin->created_at->format('F d, Y') }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Last Login</small>
                        <div>{{ $admin->updated_at->diffForHumans() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .profile-img-container {
        position: relative;
        display: inline-block;
    }
    
    .profile-img-container:hover .profile-overlay {
        opacity: 1;
    }
    
    .profile-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s;
    }
</style>
@endsection