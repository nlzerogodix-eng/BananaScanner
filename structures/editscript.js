document.getElementById('profile_picture').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const existingImg = document.getElementById('profileImage');
                    const placeholder = document.getElementById('profilePlaceholder');
                    
                    if (existingImg) {
                        existingImg.src = e.target.result;
                    } else if (placeholder) {
                        placeholder.outerHTML = `<div class="profile-img-container">
                            <img src="${e.target.result}" 
                                 alt="Profile Preview" 
                                 class="rounded-circle border border-success"
                                 style="width: 150px; height: 150px; object-fit: cover;"
                                 id="profileImage">
                            <div class="profile-overlay">
                                <i class="fas fa-camera text-white fa-2x"></i>
                            </div>
                        </div>`;
                    }
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        });
        
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';
            }
        });

        // Password visibility toggle
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                const icon = this.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
        
        // Enable new password fields only when current password is entered
        document.getElementById('current_password').addEventListener('input', function() {
            const newPassword = document.getElementById('password');
            const confirmPassword = document.getElementById('password_confirmation');
            
            if (this.value.trim() !== '') {
                newPassword.removeAttribute('disabled');
                confirmPassword.removeAttribute('disabled');
            } else {
                newPassword.value = '';
                confirmPassword.value = '';
                newPassword.setAttribute('disabled', 'disabled');
                confirmPassword.setAttribute('disabled', 'disabled');
                
                // Reset eye icons to closed state
                const passwordEye = document.querySelector('[data-target="password"] i');
                const confirmEye = document.querySelector('[data-target="password_confirmation"] i');
                if (passwordEye) {
                    passwordEye.classList.remove('fa-eye-slash');
                    passwordEye.classList.add('fa-eye');
                }
                if (confirmEye) {
                    confirmEye.classList.remove('fa-eye-slash');
                    confirmEye.classList.add('fa-eye');
                }
                
                // Reset input types to password
                if (newPassword.type === 'text') newPassword.type = 'password';
                if (confirmPassword.type === 'text') confirmPassword.type = 'password';
            }
        });
        
        // Initialize form state
        document.addEventListener('DOMContentLoaded', function() {
            const currentPassword = document.getElementById('current_password');
            const newPassword = document.getElementById('password');
            const confirmPassword = document.getElementById('password_confirmation');
            
            // Only disable new password fields if current password is empty
            if (currentPassword && currentPassword.value.trim() === '') {
                newPassword.setAttribute('disabled', 'disabled');
                confirmPassword.setAttribute('disabled', 'disabled');
            }
            
            // Prevent double form submission
            const form = document.getElementById('profileForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const submitBtn = document.getElementById('submitBtn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';
                    }
                });
            }
        });