<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f2f5;
        }

        .container {
            display: flex;
            background-color: white;
            padding: 80px 40px 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .separator {
            position: relative;
            margin: 0 40px;
        }

        .separator::before {
            content: "";
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            border-left: 2px solid #ccc;
        }

        .admin-text {
            position: absolute;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 32px;
            font-weight: bold;
            color: #1a1a1a;
            text-align: center;
        }

        .form-section {
            width: 300px;
        }

        h2 {
            color: #1a1a1a;
            margin-bottom: 30px;
            text-align: center;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #1877f2;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        button:hover {
            background-color: #166fe5;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="admin-text">ADMIN</div>

        <div class="form-section">
            <h2>LOGIN</h2>
            <div class="mt-5">
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if (session()->has('error_key_2'))
                <div class="alert alert-danger">{{ session('error_key_2') }}</div>
                @endif
            </div>

            <form class="auth-form" action="{{ route('admin.login.post.route') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Login</button>
            </form>
        </div>

        <div class="separator"></div>

        <div class="form-section">
            <h2>REGISTER</h2>
            <div class="mt-5">
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if (session()->has('error_key_1'))
                <div class="alert alert-danger">{{ session('error_key_1') }}</div>
                @endif
            </div>

            <form class="auth-form" action="{{ route('admin.registration.post.route') }}" method="POST" id="adminRegisterForm">
                @csrf
                <div class="form-group">
                    <label>Company Name:</label>
                    <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" pattern="[a-zA-Z0-9\s\-&.,']+" title="Only letters, numbers, spaces, hyphens, &, commas, and apostrophes allowed" maxlength="255" required>
                    @error('company_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Official Contact Number:</label>
                    <input type="tel" name="telephone_number" class="form-control @error('telephone_number') is-invalid @enderror" pattern="[0-9]{10,15}" title="10-15 digit phone number" required>
                    @error('telephone_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Official Email:</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="adminPassword" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least 8 characters including uppercase, lowercase and number" required>
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Confirm Password:</label>
                    <input type="password" name="confirm_password" class="form-control @error('confirm_password') is-invalid @enderror" id="adminConfirmPassword" required>
                    @error('confirm_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-danger d-none" id="adminPasswordError">
                    Passwords do not match!
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2">Register</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('adminRegisterForm').addEventListener('submit', function(e) {
            const password = document.getElementById('adminPassword');
            const confirmPassword = document.getElementById('adminConfirmPassword');
            const errorDiv = document.getElementById('adminPasswordError');

            if (password.value !== confirmPassword.value) {
                e.preventDefault();
                errorDiv.classList.remove('d-none');
                confirmPassword.focus();
            }
        });
    </script>

</body>

</html>