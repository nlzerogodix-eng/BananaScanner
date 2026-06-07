@extends('admin.dashboard')

@section('admin-content')
<div class="admin-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 text-success">
                <i class="fas fa-users me-2"></i>Users Management
            </h1>
            <p class="text-muted mb-0">Manage registered users (Admin accounts are managed separately)</p>
        </div>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fas fa-user-plus me-1"></i> Create New User
        </button>
    </div>
</div>

<!-- Admin Account Info Card -->
<div class="card stat-card mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="stat-icon bg-warning bg-opacity-10 text-warning me-4">
                <i class="fas fa-user-shield fa-2x"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="card-title mb-1">Admin Account</h5>
                <p class="text-muted mb-2">
                    <i class="fas fa-user me-1"></i> {{ Auth::user()->name }}
                    <span class="mx-2">•</span>
                    <i class="fas fa-envelope me-1"></i> {{ Auth::user()->email }}
                </p>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.profile.edit') }}" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-edit me-1"></i> Edit Admin Profile
                    </a>
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-user-circle me-1"></i> User Profile Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="table-container mb-4">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Regular Users ({{ $users->total() }})</h5>
            <small class="text-muted">Users without admin privileges</small>
        </div>
        <div class="d-flex gap-2">
            <input type="text" class="form-control form-control-sm" placeholder="Search users..." id="searchUsers">
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>User</th>
                    <th>Contact Info</th>
                    <th>Scans</th>
                    <th>Joined</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($user->profile_picture && file_exists(public_path('uploads/profile_pictures/' . $user->profile_picture)))
                                <img src="{{ asset('uploads/profile_pictures/' . $user->profile_picture) }}" 
                                    class="rounded-circle me-3" 
                                    style="width: 45px; height: 45px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center me-3" 
                                    style="width: 45px; height: 45px;">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                            <div>
                                <div class="fw-semibold">{{ $user->first_name }} {{ $user->last_name }}</div>
                                <small class="text-muted">ID: {{ $user->id }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div>{{ $user->email }}</div>
                        <small class="text-muted">
                            @if($user->phone)
                                <i class="fas fa-phone me-1"></i>{{ $user->phone }}
                            @endif
                            @if($user->location)
                                <br><i class="fas fa-map-marker-alt me-1"></i>{{ $user->location }}
                            @endif
                        </small>
                    </td>
                    <td>
                        <span class="fw-semibold">{{ $user->scan_histories_count }}</span>
                        <small class="text-muted">scans</small>
                    </td>
                    <td>
                        {{ $user->created_at->format('M d, Y') }}
                    </td>
                    <td>
                        @if($user->email_verified_at)
                            <span class="badge bg-success">Verified</span>
                        @else
                            <span class="badge bg-warning">Pending</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item edit-user-btn" href="#"
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->name }}"
                                        data-user-first-name="{{ $user->first_name }}"
                                        data-user-last-name="{{ $user->last_name }}"
                                        data-user-email="{{ $user->email }}"
                                        data-user-phone="{{ $user->phone }}"
                                        data-user-location="{{ $user->location }}"
                                        data-user-verified="{{ $user->email_verified_at ? '1' : '0' }}">
                                        <i class="fas fa-edit me-2"></i> Edit User
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger delete-user-btn" 
                                       href="#"
                                       data-user-id="{{ $user->id }}"
                                       data-user-name="{{ $user->name }}">
                                        <i class="fas fa-trash me-2"></i> Delete User
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <i class="fas fa-users fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No users found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($users->hasPages())
    <div class="p-3 border-top">
        {{ $users->links() }}
    </div>
    @endif
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-success">
                        <i class="fas fa-user-plus me-2"></i>Create New User Account
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Create a new user account with custom credentials.
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name *</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required
                                placeholder="Enter first name">
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name *</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required
                                placeholder="Enter last name">
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="email" name="email" required
                                   placeholder="Enter email address">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password *</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required
                                       placeholder="Enter password">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <small class="form-text text-muted">Minimum 8 characters</small>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirm Password *</label>
                            <input type="password" class="form-control" id="password_confirmation" 
                                   name="password_confirmation" required placeholder="Confirm password">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                   placeholder="Optional phone number">
                        </div>
                        <div class="col-md-6">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location"
                                   placeholder="Optional location">
                        </div>
                        
                        <div class="col-12">
                            <label for="profile_picture" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" id="profile_picture" name="profile_picture"
                                   accept="image/*">
                            <small class="form-text text-muted">Optional: JPG, PNG, GIF (Max 2MB)</small>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-check">
                                <input type="hidden" name="email_verified" value="0">
                                <input class="form-check-input" type="checkbox" id="email_verified" 
                                    name="email_verified" value="1">
                                <label class="form-check-label" for="email_verified">
                                    Mark email as verified (skip verification)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-user-plus me-1"></i> Create User Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="editUserForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title text-primary">
                        <i class="fas fa-user-edit me-2"></i>Edit User Account
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Update user information. Leave password fields empty to keep current password.
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_first_name" class="form-label">First Name *</label>
                            <input type="text" class="form-control" id="edit_first_name" name="first_name" required
                                placeholder="Enter first name">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_last_name" class="form-label">Last Name *</label>
                            <input type="text" class="form-control" id="edit_last_name" name="last_name" required
                                placeholder="Enter last name">
                        </div>

                        <div class="col-md-6">
                            <label for="edit_email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required
                                   placeholder="Enter email address">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="edit_password" class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="edit_password" name="password"
                                       placeholder="Leave empty to keep current">
                                <button class="btn btn-outline-secondary" type="button" id="toggleEditPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <small class="form-text text-muted">Minimum 8 characters if changing</small>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="edit_password_confirmation" 
                                   name="password_confirmation" placeholder="Confirm new password">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="edit_phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="edit_phone" name="phone"
                                   placeholder="Optional phone number">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="edit_location" name="location"
                                   placeholder="Optional location">
                        </div>
                        
                        <div class="col-12">
                            <label for="edit_profile_picture" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" id="edit_profile_picture" name="profile_picture"
                                   accept="image/*">
                            <small class="form-text text-muted">Optional: JPG, PNG, GIF (Max 2MB)</small>
                            <div id="current-profile-preview" class="mt-2"></div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-check">
                                <input type="hidden" name="email_verified" value="0">
                                <input class="form-check-input" type="checkbox" id="edit_email_verified" 
                                    name="email_verified" value="1" checked>
                                <label class="form-check-label" for="edit_email_verified">
                                    Mark email as verified
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete user <strong id="deleteUserName"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    This action cannot be undone. All scan history associated with this user will also be deleted.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" id="deleteUserForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Search functionality
    document.getElementById('searchUsers').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
    
    // Delete user confirmation
    document.querySelectorAll('.delete-user-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            
            document.getElementById('deleteUserName').textContent = userName;
            document.getElementById('deleteUserForm').action = `/admin/users/${userId}`;
            
            new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
        });
    });
    
    // Edit user functionality
    // In the edit-user-btn click handler
    document.querySelectorAll('.edit-user-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            const userEmail = this.dataset.userEmail;
            const userPhone = this.dataset.userPhone || '';
            const userLocation = this.dataset.userLocation || '';
            const userVerified = this.dataset.userVerified === '1';
            
            // Better approach: Use the data attributes for first and last name
            // We need to add these to the data attributes in the table
            const userFirstName = this.dataset.userFirstName || '';
            const userLastName = this.dataset.userLastName || '';
            
            document.getElementById('editUserForm').action = `/admin/users/${userId}`;
            document.getElementById('edit_first_name').value = userFirstName;
            document.getElementById('edit_last_name').value = userLastName;
            document.getElementById('edit_email').value = userEmail;
            document.getElementById('edit_phone').value = userPhone;
            document.getElementById('edit_location').value = userLocation;
            document.getElementById('edit_email_verified').checked = userVerified;
            
            document.getElementById('edit_password').value = '';
            document.getElementById('edit_password_confirmation').value = '';
            document.getElementById('current-profile-preview').innerHTML = '';
            
            new bootstrap.Modal(document.getElementById('editUserModal')).show();
        });
    });
    
    // Toggle password visibility for create form
    document.getElementById('togglePassword')?.addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
    
    // Toggle password visibility for edit form
    document.getElementById('toggleEditPassword')?.addEventListener('click', function() {
        const passwordInput = document.getElementById('edit_password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
    
    // Form validation for create user
    const createUserForm = document.querySelector('#createUserModal form');
    if (createUserForm) {
        createUserForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long!');
                return false;
            }
            
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Creating...';
            submitBtn.disabled = true;
        });
    }
    
    // Form validation for edit user
    const editUserForm = document.getElementById('editUserForm');
    if (editUserForm) {
        editUserForm.addEventListener('submit', function(e) {
            const password = document.getElementById('edit_password').value;
            const confirmPassword = document.getElementById('edit_password_confirmation').value;
            
            // Validate passwords only if they're being changed
            if (password || confirmPassword) {
                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Passwords do not match!');
                    return false;
                }
                
                if (password.length < 8) {
                    e.preventDefault();
                    alert('Password must be at least 8 characters long if changing!');
                    return false;
                }
            }
            
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Updating...';
            submitBtn.disabled = true;
        });
    }
    
    // Auto-generate password button for create form
    document.addEventListener('DOMContentLoaded', function() {
        if (!document.getElementById('generatePassword')) {
            const passwordGroup = document.getElementById('password').closest('.col-md-6');
            const generateBtn = document.createElement('button');
            generateBtn.type = 'button';
            generateBtn.className = 'btn btn-outline-info btn-sm mt-1';
            generateBtn.id = 'generatePassword';
            generateBtn.innerHTML = '<i class="fas fa-key me-1"></i> Generate Strong Password';
            
            passwordGroup.appendChild(generateBtn);
            
            generateBtn.addEventListener('click', function() {
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
                let password = '';
                for (let i = 0; i < 12; i++) {
                    password += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                
                document.getElementById('password').value = password;
                document.getElementById('password_confirmation').value = password;
                
                document.getElementById('password').type = 'text';
                document.getElementById('password_confirmation').type = 'text';
                
                setTimeout(() => {
                    document.getElementById('password').type = 'password';
                    document.getElementById('password_confirmation').type = 'password';
                }, 2000);
            });
        }
    });
</script>

<style>
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
    
    .stat-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.1);
    }
    
    .stat-card:hover {
        border-color: rgba(255, 193, 7, 0.3);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .dropdown-menu {
        border: 1px solid rgba(0,0,0,0.1);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .dropdown-item:hover {
        background-color: rgba(25, 135, 84, 0.1);
    }
    
    .form-control:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    }
    
    .modal-header {
        background-color: #f8f9fa;
        border-bottom: 2px solid #198754;
    }
    
    #editUserModal .modal-header {
        border-bottom: 2px solid #0d6efd;
    }
    
    .badge.bg-success {
        background-color: #198754 !important;
    }
    
    .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #000;
    }
    
    @media (max-width: 768px) {
        .admin-header {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .admin-header .btn {
            margin-top: 1rem;
            align-self: flex-start;
        }
        
        .btn-group-sm {
            flex-direction: column;
        }
        
        .dropdown-menu {
            position: absolute !important;
        }
    }
    
    #current-profile-preview img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #dee2e6;
    }
</style>
@endsection