<!DOCTYPE html>
<html>
<head>
    <title>Customer Verify Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @error('otp')<div class="alert alert-danger">{{ $message }}</div>@enderror
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Verify Your Account</div>
                    <div class="card-body">
                        <p>
                            A 6-digit verification code has been sent to:<br>
                            <strong>{{ session('email') }}</strong>
                        </p>
                        <form method="POST" action="{{ route('customer.verification.verify') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="otp" class="form-label">Enter Email Verification Code</label>
                            <input type="text" class="form-control @error('otp') is-invalid @enderror" id="otp" name="otp" required autofocus maxlength="6" placeholder="123456">
                            @error('otp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <p>A 6-digit verification code has been sent to:<br><strong>{{ session('telephone_number') }}</strong></p>
                        <div class="mb-3">
                            <label for="otp" class="form-label">Enter Mobile Verification Code</label>
                            <div class="d-flex justify-content-center mb-4 gap-2">
                                <input type="text" class="form-control otp-input text-center" name="otp1" maxlength="1" autocomplete="off" required style="width: 50px;">
                                <input type="text" class="form-control otp-input text-center" name="otp2" maxlength="1" autocomplete="off" required style="width: 50px;">
                                <input type="text" class="form-control otp-input text-center" name="otp3" maxlength="1" autocomplete="off" required style="width: 50px;">
                                <input type="text" class="form-control otp-input text-center" name="otp4" maxlength="1" autocomplete="off" required style="width: 50px;">
                                <input type="text" class="form-control otp-input text-center" name="otp5" maxlength="1" autocomplete="off" required style="width: 50px;">
                                <input type="text" class="form-control otp-input text-center" name="otp6" maxlength="1" autocomplete="off" required style="width: 50px;">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Verify Now</button>
                    </form>
                    <div class="mt-3">
                        <form method="POST" action="{{ route('customer.verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0">Resend Verification Code</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced OTP Input Navigation
    const otpInputs = document.querySelectorAll('.otp-input');

    function setupOTPInputs() {
        otpInputs.forEach((input, index) => {
            // Handle input event for typing
            input.addEventListener('input', function(e) {
                // Only allow digits and limit to 1 character
                let value = this.value.replace(/[^0-9]/g, '');
                if (value.length > 1) {
                    value = value.charAt(0);
                }
                this.value = value;
                
                // Reset border color when user starts typing
                this.classList.remove('is-invalid');
                
                // Auto-move to next input if current input is filled
                if (value.length === 1 && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });
            
            // Handle keydown events
            input.addEventListener('keydown', function(e) {
                // Handle backspace to move to previous input
                if (e.key === 'Backspace') {
                    if (this.value === '' && index > 0) {
                        otpInputs[index - 1].focus();
                        otpInputs[index - 1].value = '';
                    } else {
                        this.value = '';
                    }
                }
                // Handle arrow keys for navigation
                else if (e.key === 'ArrowLeft' && index > 0) {
                    e.preventDefault();
                    otpInputs[index - 1].focus();
                }
                else if (e.key === 'ArrowRight' && index < otpInputs.length - 1) {
                    e.preventDefault();
                    otpInputs[index + 1].focus();
                }
                // Handle delete key
                else if (e.key === 'Delete') {
                    this.value = '';
                    if (index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                }
            });
            
            // Handle paste event
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                const digits = paste.replace(/[^0-9]/g, '').slice(0, 6);
                
                // Clear all inputs first
                otpInputs.forEach(inp => {
                    inp.value = '';
                    inp.classList.remove('is-invalid');
                });
                
                // Fill inputs with pasted digits starting from first input
                digits.split('').forEach((digit, i) => {
                    if (i < otpInputs.length) {
                        otpInputs[i].value = digit;
                    }
                });
                
                // Focus the next empty input or the last filled input
                const nextIndex = Math.min(digits.length, otpInputs.length - 1);
                otpInputs[nextIndex].focus();
            });
            
            // Handle focus event to select all text
            input.addEventListener('focus', function() {
                this.select();
            });
            
            // Prevent non-numeric input on keypress
            input.addEventListener('keypress', function(e) {
                if (!/[0-9]/.test(e.key) && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key)) {
                    e.preventDefault();
                }
            });
        });
    }

    // Initialize OTP inputs
    if (otpInputs.length > 0) {
        setupOTPInputs();
        // Focus first input on page load
        otpInputs[0].focus();
    }
});
</script>
</body>
</html>