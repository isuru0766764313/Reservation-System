<!DOCTYPE html>
<html>
<head>
    <title>Admin Verify Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Verify Your Email
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        @error('otp')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror

                        <p>
                            A 6-character verification code has been sent to:<br>
                            <strong>{{ $email }}</strong>
                        </p>

                        <form method="POST" action="{{ route('admin.verification.verify') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="otp" class="form-label">Enter Verification Code</label>
                                <input type="text" 
                                       class="form-control @error('otp') is-invalid @enderror" 
                                       id="otp" 
                                       name="otp"
                                       required
                                       autofocus
                                       maxlength="6"
                                       placeholder="Enter 6-character code">
                                @error('otp')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">
                                Verify Email
                            </button>
                        </form>

                        <div class="mt-3">
                            <form method="POST" action="{{ route('admin.verification.resend') }}">
                                @csrf
                                <button type="submit" class="btn btn-link p-0">
                                    Resend Verification Code
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
// Get the OTP input element
const otpInput = document.getElementById('otp');

// Fix paste functionality - allow pasting alphanumeric codes
otpInput.addEventListener('paste', function(e) {
    e.preventDefault();
    
    // Get pasted text from clipboard
    const pastedData = e.clipboardData.getData('text');
    
    // Remove whitespace and any special formatting, but keep letters and numbers
    const cleanText = pastedData.replace(/\s/g, ''); // Remove only whitespace
    
    // Take only the first 6 characters
    const sixChars = cleanText.slice(0, 6);
    
    // Set the value
    if (sixChars.length > 0) {
        this.value = sixChars;
    }
});

// Optional: Add input validation to prevent entering more than 6 characters
otpInput.addEventListener('input', function(e) {
    // Just ensure we don't exceed 6 characters
    if (this.value.length > 6) {
        this.value = this.value.slice(0, 6);
    }
});
</script>
</body>
</html>