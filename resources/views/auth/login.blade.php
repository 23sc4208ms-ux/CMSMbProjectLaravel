<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #667eea;
            font-size: 24px;
        }
        .message {
            text-align: center;
            padding: 20px;
            background: #f0f4ff;
            border: 2px solid #667eea;
            border-radius: 8px;
            margin-bottom: 30px;
            color: #667eea;
            font-weight: 500;
            font-size: 16px;
        }
        .error-message {
            text-align: center;
            padding: 15px;
            background: #ffebee;
            border: 2px solid #d32f2f;
            border-radius: 8px;
            margin-bottom: 25px;
            color: #d32f2f;
            font-weight: 500;
            font-size: 14px;
        }
        .countdown-timer {
            text-align: center;
            padding: 20px;
            background: #fff3cd;
            border: 2px solid #ff9800;
            border-radius: 8px;
            margin-bottom: 25px;
            border-radius: 8px;
        }
        .countdown-timer .timer-label {
            color: #ff9800;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .countdown-timer .timer-display {
            color: #d32f2f;
            font-weight: 700;
            font-size: 48px;
            font-family: 'Courier New', monospace;
        }
        .countdown-timer .timer-unit {
            color: #ff9800;
            font-weight: 600;
            font-size: 14px;
            margin-top: 5px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        input:disabled {
            background-color: #f5f5f5;
            cursor: not-allowed;
            border-color: #ccc;
        }
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-size: 16px;
            transition: transform 0.2s;
        }
        button:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        button:disabled {
            background: linear-gradient(135deg, #999 0%, #666 100%);
            cursor: not-allowed;
            opacity: 0.6;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #999;
            font-size: 13px;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <div class="message">Please login first</div>

        <div id="errorContainer">
            @if (session('error'))
                <div class="error-message">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="error-message">{{ $errors->first() }}</div>
            @endif
        </div>
        <div id="countdownContainer">
            @if (session('locked'))
                <div class="countdown-timer">
                    <div class="timer-label">Account Locked - Time Remaining:</div>
                    <div class="timer-display">{{ session('remaining_seconds', 0) }}</div>
                    <div class="timer-unit">seconds</div>
                </div>
            @endif
        </div>

        <form id="loginForm" method="POST" action="{{ route('login.submit', [], false) }}">
            @csrf

            <div class="form-group">
                <label for="username">Username or Email</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter your username or email"
                    autocomplete="off"
                    value=""
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    autocomplete="new-password"
                    value=""
                    required
                >
            </div>

            <button type="submit" id="loginBtn">Login</button>
        </form>

        <div class="footer">
            <a href="/">Back to Home</a>
        </div>
    </div>

    <!-- Plain HTML form submission is intentional here so Render/login issues are visible as normal POST requests. -->

    <script>
        // Clear form fields on page load to prevent browser auto-fill
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').value = '';
            document.getElementById('password').value = '';
        });
    </script>
</body>
</html>
