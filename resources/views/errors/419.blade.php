<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-container {
            background: white;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            text-align: center;
            max-width: 500px;
        }
        .error-icon {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 1rem;
        }
        .error-code {
            font-size: 3rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 0.5rem;
        }
        .error-message {
            font-size: 1.5rem;
            color: #666;
            margin-bottom: 2rem;
        }
        .error-description {
            color: #888;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .btn-home {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 40px;
            font-size: 1.1rem;
            border-radius: 25px;
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.2s;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="bi bi-clock-history">⏱️</i>
        </div>
        <div class="error-code">419</div>
        <div class="error-message">Session Expired</div>
        <div class="error-description">
            Your session has expired due to inactivity or a login conflict. 
            This happens when you switch between admin and customer accounts in the same browser.
            <br><br>
            Please return to the home page and login again.
        </div>
        <a href="{{ route('home_route') }}" class="btn-home">
            Go to Home Page
        </a>
    </div>

    <script>
        // Auto redirect after 5 seconds
        setTimeout(function() {
            window.location.href = "{{ route('home_route') }}";
        }, 5000);
    </script>
</body>
</html>
