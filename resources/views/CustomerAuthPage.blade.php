<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hall Reservation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container.min-vh-100 {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .main-container {
            width: 100%;
            max-width: 1000px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .left-side {
            background: linear-gradient(135deg, #3a1c71, #d76d77, #ffaf7b);
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100%;
        }
        
        .left-side h1 {
            font-size: 2.2rem;
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .left-side p {
            line-height: 1.6;
            font-size: 1.1rem;
            margin-bottom: 20px;
        }
        
        .left-side ul {
            padding-left: 20px;
        }
        
        .left-side li {
            margin-bottom: 10px;
            font-size: 1.05rem;
        }
        
        .right-side {
            padding: 40px;
        }
        
        .form-tabs {
            border-bottom: 2px solid #e1e5eb;
            margin-bottom: 25px;
        }
        
        .form-tabs .nav-link {
            border: none;
            font-size: 1.1rem;
            font-weight: 600;
            color: #7f8c8d;
            padding: 10px 20px;
            transition: all 0.3s;
        }
        
        .form-tabs .nav-link.active {
            color: #3498db;
            background: transparent;
            border-bottom: 3px solid #3498db;
        }
        
        .form-tabs .nav-link:hover {
            color: #3498db;
        }
        
        .auth-form .form-control {
            padding: 12px 15px;
            border: 2px solid #e1e5eb;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .auth-form .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
            outline: none;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
            cursor: pointer;
            transition: color 0.3s;
        }
        
        .input-icon:hover {
            color: #3498db;
        }
        
        .password-strength {
            height: 5px;
            border-radius: 2.5px;
            background: #ecf0f1;
            margin-top: 8px;
            overflow: hidden;
        }
        
        .strength-meter {
            height: 100%;
            width: 0;
            background: #e74c3c;
            transition: width 0.5s, background 0.5s;
        }
        
        .requirements {
            background: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin-top: 20px;
            border-radius: 0 8px 8px 0;
        }
        
        .requirements h4 {
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .requirements ul {
            padding-left: 20px;
            margin-bottom: 0;
        }
        
        .requirements li {
            margin-bottom: 5px;
            color: #7f8c8d;
        }
        
        .requirements li.valid {
            color: #27ae60;
        }
        
        .requirements li.valid::before {
            content: "✓ ";
            color: #27ae60;
        }
        
        .btn-primary {
            background: #3498db;
            border: none;
            padding: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .forgot-password {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #3498db;
            text-decoration: none;
        }
        
        .forgot-password:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-top: 15px;
        }
        
        .modal-content {
            border-radius: 15px;
            overflow: hidden;
        }
        
        .modal-header {
            background: #f8f9fa;
            border-bottom: 1px solid #e1e5eb;
        }
        
        @media (max-width: 768px) {
            .left-side {
                padding: 30px;
            }
            
            .right-side {
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="container min-vh-100 d-flex">
        <div class="main-container mx-auto my-auto">
            <div class="row g-0">
                <!-- Left Side -->
                <div class="col-md-6 left-side d-flex align-items-center">
                    <div>
                        <div class="" style="text-align: center; margin-bottom: 20px">
                            <a href="<?php echo config('app.url'); ?>">
                                <img style="width: 80px; margin: 0px auto " src="https://upload.wikimedia.org/wikipedia/commons/5/5f/Emblem_of_Sri_Lanka.svg" alt="PM Office Logo" class="">
                            </a>
                        </div>
                        <h1 class="display-4 mb-4">Reserve your hall matter of seconds.</h1>
                        <p class="lead">
                            Book your perfect event space with ease. Manage reservations, 
                            check availability in real-time, and enjoy seamless booking 
                            experience.
                        </p>
                        <ul class="list-unstyled mt-4">
                            <li class="mb-3"><i class="fas fa-check-circle me-2"></i>Real-time availability checking</li>
                            <li class="mb-3"><i class="fas fa-check-circle me-2"></i>Instant booking confirmation</li>
                            <li class="mb-3"><i class="fas fa-check-circle me-2"></i>Secure payment processing</li>
                        </ul>
                        
                        <div class="requirements mt-4">
                            <h4>Password Requirements:</h4>
                            <ul>
                                <li id="length-req">At least 8 characters</li>
                                <li id="uppercase-req">At least one uppercase letter</li>
                                <li id="lowercase-req">At least one lowercase letter</li>
                                <li id="number-req">At least one number</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Right Side -->
                <div class="col-md-6 right-side">
                    <ul class="nav nav-tabs form-tabs mb-4">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#signup">Sign Up</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Login Form -->
                        <div class="tab-pane fade show active" id="login">
                            <div class="mt-3">
                                @if ($errors->any())
                                <div>
                                    @foreach ($errors->all() as $error)
                                    <div class="alert alert-danger">{{$error}}</div>                                    
                                    @endforeach
                                </div>                                
                                @endif
                                @if (session()->has('error_key_2'))
                                <div class="alert alert-danger">{{session('error_key_2')}}</div>                                
                                @endif
                            </div>
                            <form class="auth-form" action="{{route('login_post_route')}}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                                </div>
                                <div class="mb-4">
                                    <div class="input-group">
                                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                                        <span class="input-icon" id="toggleLoginPassword">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Login</button>
                                <a href="#" class="forgot-password" data-bs-toggle="modal" data-bs-target="#resetPassword">
                                    Forgot Password?
                                </a>
                            </form>
                        </div>

                        <!-- Sign Up Form -->
                        <div class="tab-pane fade" id="signup">
                            <div class="mt-3">
                                @if ($errors->any())
                                <div>
                                    @foreach ($errors->all() as $error)
                                    <div class="alert alert-danger">{{$error}}</div>                                    
                                    @endforeach
                                </div>                                
                                @endif
                                @if (session()->has('error_key_1'))
                                <div class="alert alert-danger">{{session('error_key_1')}}</div>                                
                                @endif
                            </div>
                            <form class="auth-form" action="{{route('registration_post_route')}}" method="POST" id="signupForm">
                                @csrf
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <input type="text" name="first_name" class="form-control" placeholder="First Name" pattern="[a-zA-Z\s'\-]{2,50}" title="Only letters, spaces, hyphens and apostrophes allowed (2-50 characters)" maxlength="50" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="last_name" class="form-control" placeholder="Last Name" pattern="[a-zA-Z\s'\-]{2,50}" title="Only letters, spaces, hyphens and apostrophes allowed (2-50 characters)" maxlength="50" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <input type="email" name="email" class="form-control" placeholder="Email Address" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" title="Valid email format required (e.g. user@example.com)" maxlength="255" required>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <input type="tel" name="telephone_number" class="form-control" 
                                               placeholder="Telephone Number"
                                               pattern="[0-9]{10}"
                                               title="10-digit phone number (e.g. 0771234567)"
                                               required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="national_id" class="form-control" 
                                               placeholder="National ID Number"
                                               pattern="[a-zA-Z0-9]{10,12}"
                                               title="10-12 alphanumeric characters only"
                                               required>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <div class="input-group">
                                            <input type="password" name="password" class="form-control" 
                                                   id="password" 
                                                   placeholder="Password"
                                                   pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                                   title="Must contain 8+ chars with uppercase, lowercase and number"
                                                   required>
                                            <span class="input-icon" id="togglePassword">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                        <div class="password-strength">
                                            <div class="strength-meter" id="strengthMeter"></div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group">
                                            <input type="password" name="confirm_password" class="form-control" 
                                                   id="confirmPassword"
                                                   placeholder="Confirm Password" 
                                                   required>
                                            <span class="input-icon" id="toggleConfirmPassword">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-danger d-none" id="passwordError">
                                    <i class="fas fa-exclamation-circle me-2"></i>Passwords do not match!
                                </div>

                                <button type="submit" id="createAccountBtn" class="btn btn-primary w-100 py-2">Create Account</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="resetPassword" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="resetModalTitle">Customer Password Reset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-2">
                    <!-- Step 1: Email & Phone Verification -->
                    <div id="step1" class="reset-step">
                        <p class="text-muted mb-4">Enter your email and phone number to receive a reset OTP.</p>
                        <div id="forgotPasswordForm">
                            @csrf
                            <div class="mb-3">
                                <input type="email" id="resetEmail" class="form-control py-3" placeholder="Email Address" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-4">
                                <input type="tel" id="resetPhone" class="form-control py-3" placeholder="Telephone Number" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <button type="button" class="btn btn-primary w-100 py-3 fw-bold" id="sendResetOtpBtn">
                                Send Reset OTP
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: OTP Verification -->
                    <div id="step2" class="reset-step d-none">
                        <p class="text-muted mb-3">Enter the verification codes sent to:</p>
                        <div class="alert alert-info border-0 mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <small class="text-muted">Email: <span id="maskedEmail"></span></small>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-mobile-alt text-success me-2"></i>
                                <small class="text-muted">Phone: <span id="maskedPhone"></span></small>
                            </div>
                        </div>
                        
                        <form id="verifyOtpForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-primary">Email OTP</label>
                                <input type="text" id="emailOtp" class="form-control py-3 text-center fs-5 fw-bold" maxlength="6" placeholder="••••••" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-success">Phone OTP</label>
                                <div class="d-flex gap-2 justify-content-center">
                                    <input type="text" class="form-control phone-otp text-center fs-4 fw-bold" maxlength="1" data-index="0" required>
                                    <input type="text" class="form-control phone-otp text-center fs-4 fw-bold" maxlength="1" data-index="1" required>
                                    <input type="text" class="form-control phone-otp text-center fs-4 fw-bold" maxlength="1" data-index="2" required>
                                    <input type="text" class="form-control phone-otp text-center fs-4 fw-bold" maxlength="1" data-index="3" required>
                                    <input type="text" class="form-control phone-otp text-center fs-4 fw-bold" maxlength="1" data-index="4" required>
                                    <input type="text" class="form-control phone-otp text-center fs-4 fw-bold" maxlength="1" data-index="5" required>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success py-3 fw-bold" id="verifyOtpBtn">
                                    Verify OTP
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="resendOtpBtn">
                                    Resend OTP
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Step 3: New Password -->
                    <div id="step3" class="reset-step d-none">
                        <div class="alert alert-success border-0 mb-4">
                            Verification successful! Set your new password below.
                        </div>
                        
                        <form id="newPasswordForm">
                            @csrf
                            <div class="mb-3">
                                <div class="input-group">
                                    <input type="password" id="newPassword" class="form-control py-3" placeholder="New Password" required
                                           pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                                           title="Must contain 8+ chars with uppercase, lowercase and number">
                                    <span class="input-group-text bg-transparent border-start-0 toggle-password" data-target="newPassword">
                                        <i class="fas fa-eye text-muted"></i>
                                    </span>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="input-group">
                                    <input type="password" id="confirmNewPassword" class="form-control py-3" placeholder="Confirm New Password" required>
                                    <span class="input-group-text bg-transparent border-start-0 toggle-password" data-target="confirmNewPassword">
                                        <i class="fas fa-eye text-muted"></i>
                                    </span>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="password-requirements mb-4">
                                <small class="text-muted">Password must contain:</small>
                                <div class="mt-2">
                                    <small class="d-block" id="req-length"><i class="fas fa-times text-danger me-2"></i>At least 8 characters</small>
                                    <small class="d-block" id="req-uppercase"><i class="fas fa-times text-danger me-2"></i>One uppercase letter</small>
                                    <small class="d-block" id="req-lowercase"><i class="fas fa-times text-danger me-2"></i>One lowercase letter</small>
                                    <small class="d-block" id="req-number"><i class="fas fa-times text-danger me-2"></i>One number</small>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold" id="resetPasswordBtn">
                                Reset Password
                            </button>
                        </form>
                    </div>

                    <!-- Loading/Error States -->
                    <div id="loadingState" class="text-center d-none py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Processing your request...</p>
                    </div>

                    <div id="errorState" class="alert alert-danger d-none mt-3"></div>
                    <div id="successState" class="alert alert-success d-none mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- OTP model -->
    <div class="modal fade" id="otpModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Verify OTP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Enter 6-digit code sent to your email/phone</p>
                    <form id="otpForm">
                        @csrf
                        <div class="mb-3">
                            <input type="text" class="form-control text-center fs-4" name="otp" maxlength="6" pattern="\d{6}" title="Enter 6-digit code" required autocomplete="off">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() 
        {
            // Password visibility toggle for login form
            const toggleLoginPassword = document.getElementById('toggleLoginPassword');
            if (toggleLoginPassword) {
                const loginPasswordInput = document.querySelector('#login input[name="password"]');
                toggleLoginPassword.addEventListener('click', function() {
                    const type = loginPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    loginPasswordInput.setAttribute('type', type);
                    this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
                });
            }
            
            // Password visibility toggle for signup form
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
            const confirmPasswordInput = document.getElementById('confirmPassword');
            
            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
                });
            }
            
            if (toggleConfirmPassword && confirmPasswordInput) {
                toggleConfirmPassword.addEventListener('click', function() {
                    const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    confirmPasswordInput.setAttribute('type', type);
                    this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
                });
            }
            
            // Password strength indicator
            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    const strengthMeter = document.getElementById('strengthMeter');
                    let strength = 0;
                    
                    // Check password requirements
                    const hasLength = password.length >= 8;
                    const hasUppercase = /[A-Z]/.test(password);
                    const hasLowercase = /[a-z]/.test(password);
                    const hasNumber = /\d/.test(password);
                    
                    // Update requirement indicators
                    if (document.getElementById('length-req')) {
                        document.getElementById('length-req').className = hasLength ? 'valid' : '';
                    }
                    if (document.getElementById('uppercase-req')) {
                        document.getElementById('uppercase-req').className = hasUppercase ? 'valid' : '';
                    }
                    if (document.getElementById('lowercase-req')) {
                        document.getElementById('lowercase-req').className = hasLowercase ? 'valid' : '';
                    }
                    if (document.getElementById('number-req')) {
                        document.getElementById('number-req').className = hasNumber ? 'valid' : '';
                    }
                    
                    // Calculate strength
                    if (hasLength) strength += 25;
                    if (hasUppercase) strength += 25;
                    if (hasLowercase) strength += 25;
                    if (hasNumber) strength += 25;
                    
                    // Update strength meter
                    if (strengthMeter) {
                        strengthMeter.style.width = strength + '%';
                        
                        // Update color based on strength
                        if (strength < 50) {
                            strengthMeter.style.background = '#e74c3c';
                        } else if (strength < 75) {
                            strengthMeter.style.background = '#f39c12';
                        } else {
                            strengthMeter.style.background = '#2ecc71';
                        }
                    }
                });
            }
            
            // Password match validation
            if (confirmPasswordInput) {
                confirmPasswordInput.addEventListener('input', function() {
                    const password = passwordInput.value;
                    const confirmPassword = this.value;
                    const errorDiv = document.getElementById('passwordError');
                    
                    if (password && confirmPassword && password !== confirmPassword) {
                        errorDiv.classList.remove('d-none');
                    } else {
                        errorDiv.classList.add('d-none');
                    }
                });
            }
            
            // Form submission handling
            const signupForm = document.getElementById('signupForm');
            if (signupForm) {
                signupForm.addEventListener('submit', function(e) {
                    const password = document.getElementById('password')?.value;
                    const confirmPassword = document.getElementById('confirmPassword')?.value;
                    
                    if (password && confirmPassword && password !== confirmPassword) {
                        e.preventDefault();
                        document.getElementById('passwordError').classList.remove('d-none');
                    }
                });
            }

            /*/ otp model related
            const signupform = document.getElementById('signupForm');
            const submitBtn = document.getElementById('createAccountBtn');
            const modal = new bootstrap.Modal(document.getElementById('otpModal'));

            submitBtn.addEventListener('click', function(e)
            {
                if (signupform.checkValidity())
                {
                    //e.preventDefault();// prevent until validation done
                    form.submit();
                    // poping up model
                    modal.show();
                    
                }                
            });*/

            // ============ FORGOT PASSWORD FUNCTIONALITY ============
            
            const resetPasswordModal = new bootstrap.Modal(document.getElementById('resetPassword'));
            let currentStep = 1;
            let forgotPasswordData = {};

            // Utility functions for showing/hiding elements
            function showStep(stepNumber) {
                document.querySelectorAll('.reset-step').forEach(step => step.classList.add('d-none'));
                document.getElementById(`step${stepNumber}`).classList.remove('d-none');
                currentStep = stepNumber;
                
                // Update modal title based on step
                const titles = {
                    1: 'Customer Password Reset',
                    2: 'Verify Your Identity', 
                    3: 'Set New Password'
                };
                document.getElementById('resetModalTitle').textContent = titles[stepNumber];
            }

            function showLoading() {
                document.querySelectorAll('.reset-step').forEach(step => step.classList.add('d-none'));
                document.getElementById('loadingState').classList.remove('d-none');
                hideMessages();
            }

            function hideLoading() {
                document.getElementById('loadingState').classList.add('d-none');
            }

            function showError(message) {
                const errorElement = document.getElementById('errorState');
                errorElement.textContent = message;
                errorElement.classList.remove('d-none');
                document.getElementById('successState').classList.add('d-none');
            }

            function showSuccess(message) {
                const successElement = document.getElementById('successState');
                successElement.textContent = message;
                successElement.classList.remove('d-none');
                document.getElementById('errorState').classList.add('d-none');
            }

            function hideMessages() {
                document.getElementById('errorState').classList.add('d-none');
                document.getElementById('successState').classList.add('d-none');
            }

            function resetModal() {
                currentStep = 1;
                forgotPasswordData = {};
                showStep(1);
                hideMessages();
                // Clear all form inputs
                document.querySelectorAll('#resetPassword input').forEach(input => {
                    input.value = '';
                    input.classList.remove('is-invalid', 'is-valid');
                });
            }

            // Reset modal when opened
            document.getElementById('resetPassword').addEventListener('show.bs.modal', function () {
                resetModal();
            });

            // Step 1: Email & Phone Submission - Button Click Event
            document.getElementById('sendResetOtpBtn').addEventListener('click', async function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Forgot password button clicked'); // Debug log
                
                const email = document.getElementById('resetEmail').value.trim();
                const phone = document.getElementById('resetPhone').value.trim();
                
                console.log('Email:', email, 'Phone:', phone); // Debug log
                
                if (!email || !phone) {
                    showError('Please fill in both email and phone number.');
                    return;
                }

                // Disable button to prevent double-clicks
                this.disabled = true;
                this.innerHTML = 'Sending...';

                showLoading();

                try {
                    console.log('Making API request to forgot password'); // Debug log
                    const response = await fetch('/customer/forgot-password/request', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            email: email,
                            telephone_number: phone
                        })
                    });

                    const data = await response.json();
                    hideLoading();

                    if (data.success) {
                        forgotPasswordData = { customer_id: data.customer_id };
                        
                        // Update masked contact info
                        document.getElementById('maskedEmail').textContent = data.masked_email;
                        document.getElementById('maskedPhone').textContent = data.masked_phone;
                        
                        showSuccess(data.message);
                        setTimeout(() => {
                            hideMessages();
                            showStep(2);
                        }, 2000);
                    } else {
                        showError(data.message || 'Failed to send reset code. Please try again.');
                        showStep(1);
                    }
                } catch (error) {
                    console.error('Fetch error:', error); // Debug log
                    hideLoading();
                    showError('An error occurred. Please check your connection and try again.');
                    showStep(1);
                } finally {
                    // Re-enable button
                    document.getElementById('sendResetOtpBtn').disabled = false;
                    document.getElementById('sendResetOtpBtn').innerHTML = 'Send Reset OTP';
                }
                
                // Ensure no form submission happens
                return false;
            });

            // Additional safety: prevent any form submission in the modal
            document.getElementById('resetPassword').addEventListener('submit', function(e) {
                console.log('Modal form submit prevented'); // Debug log
                e.preventDefault();
                e.stopPropagation();
                return false;
            });

            // Phone OTP input handling (auto-move between fields)
            document.querySelectorAll('.phone-otp').forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    const value = e.target.value;
                    
                    // Only allow digits
                    e.target.value = value.replace(/[^0-9]/g, '');
                    
                    // Auto-move to next input
                    if (e.target.value && index < 5) {
                        document.querySelector(`.phone-otp[data-index="${index + 1}"]`).focus();
                    }
                });
                
                input.addEventListener('keydown', function(e) {
                    // Move to previous input on backspace
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        document.querySelector(`.phone-otp[data-index="${index - 1}"]`).focus();
                    }
                });
            });

            // Step 2: OTP Verification
            document.getElementById('verifyOtpForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const emailOtp = document.getElementById('emailOtp').value.trim();
                const phoneOtpInputs = document.querySelectorAll('.phone-otp');
                let phoneOtp = '';
                
                phoneOtpInputs.forEach(input => {
                    phoneOtp += input.value;
                });

                if (emailOtp.length !== 6 || phoneOtp.length !== 6) {
                    showError('Please enter both 6-digit codes.');
                    return;
                }

                showLoading();

                try {
                    const response = await fetch('/customer/forgot-password/verify-otp', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            email_otp: emailOtp,
                            otp1: phoneOtp[0], otp2: phoneOtp[1], otp3: phoneOtp[2],
                            otp4: phoneOtp[3], otp5: phoneOtp[4], otp6: phoneOtp[5]
                        })
                    });

                    const data = await response.json();
                    hideLoading();

                    if (data.success) {
                        showSuccess(data.message);
                        setTimeout(() => {
                            hideMessages();
                            showStep(3);
                        }, 2000);
                    } else {
                        showError(data.message || 'Invalid verification codes. Please try again.');
                        showStep(2);
                    }
                } catch (error) {
                    hideLoading();
                    showError('An error occurred during verification. Please try again.');
                    showStep(2);
                }
            });

            // Resend OTP functionality
            document.getElementById('resendOtpBtn').addEventListener('click', async function() {
                this.disabled = true;
                this.innerHTML = 'Sending...';

                try {
                    const response = await fetch('/customer/forgot-password/resend-otp', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({})
                    });

                    const data = await response.json();

                    if (data.success) {
                        showSuccess('New verification codes sent successfully.');
                        // Clear current OTP inputs
                        document.getElementById('emailOtp').value = '';
                        document.querySelectorAll('.phone-otp').forEach(input => input.value = '');
                    } else {
                        showError(data.message || 'Failed to resend codes. Please try again.');
                    }
                } catch (error) {
                    showError('Failed to resend codes. Please check your connection.');
                }

                // Re-enable button after delay
                setTimeout(() => {
                    this.disabled = false;
                    this.innerHTML = 'Resend OTP';
                }, 3000);
            });

            // Password visibility toggle for new password form
            document.querySelectorAll('.toggle-password').forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const targetId = this.dataset.target;
                    const targetInput = document.getElementById(targetId);
                    const icon = this.querySelector('i');
                    
                    if (targetInput.type === 'password') {
                        targetInput.type = 'text';
                        icon.classList.replace('fa-eye', 'fa-eye-slash');
                    } else {
                        targetInput.type = 'password';
                        icon.classList.replace('fa-eye-slash', 'fa-eye');
                    }
                });
            });

            // Password validation for new password
            document.getElementById('newPassword').addEventListener('input', function() {
                const password = this.value;
                const requirements = {
                    'req-length': password.length >= 8,
                    'req-uppercase': /[A-Z]/.test(password),
                    'req-lowercase': /[a-z]/.test(password),
                    'req-number': /\d/.test(password)
                };

                Object.keys(requirements).forEach(reqId => {
                    const element = document.getElementById(reqId);
                    const icon = element.querySelector('i');
                    
                    if (requirements[reqId]) {
                        icon.className = 'fas fa-check text-success me-2';
                        element.classList.add('text-success');
                        element.classList.remove('text-muted');
                    } else {
                        icon.className = 'fas fa-times text-danger me-2';
                        element.classList.remove('text-success');
                        element.classList.add('text-muted');
                    }
                });
            });

            // Step 3: New Password Submission
            document.getElementById('newPasswordForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const newPassword = document.getElementById('newPassword').value;
                const confirmPassword = document.getElementById('confirmNewPassword').value;
                
                if (newPassword !== confirmPassword) {
                    showError('Passwords do not match.');
                    return;
                }

                if (newPassword.length < 8 || 
                    !/[A-Z]/.test(newPassword) || 
                    !/[a-z]/.test(newPassword) || 
                    !/\d/.test(newPassword)) {
                    showError('Password does not meet the requirements.');
                    return;
                }

                showLoading();

                try {
                    const response = await fetch('/customer/forgot-password/reset', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            password: newPassword,
                            password_confirmation: confirmPassword
                        })
                    });

                    const data = await response.json();
                    hideLoading();

                    if (data.success) {
                        showSuccess(data.message);
                        setTimeout(() => {
                            resetPasswordModal.hide();
                            // Optionally show a success toast on the main page
                            alert('Password reset successfully! You can now login with your new password.');
                        }, 3000);
                    } else {
                        showError(data.message || 'Failed to reset password. Please try again.');
                        showStep(3);
                    }
                } catch (error) {
                    hideLoading();
                    showError('An error occurred while resetting password. Please try again.');
                    showStep(3);
                }
            });
        });
    </script>
</body>
</html>
