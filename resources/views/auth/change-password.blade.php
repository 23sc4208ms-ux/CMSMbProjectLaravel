<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
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
            margin-bottom: 10px;
            color: #667eea;
            font-size: 24px;
        }
        .subtitle {
            text-align: center;
            margin-bottom: 30px;
            color: #999;
            font-size: 14px;
        }
        .message {
            text-align: center;
            padding: 15px;
            background: #f0f4ff;
            border: 2px solid #667eea;
            border-radius: 8px;
            margin-bottom: 25px;
            color: #667eea;
            font-weight: 500;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
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
        input.error {
            border-color: #ff6b6b;
        }
        .error-message {
            color: #ff6b6b;
            font-size: 12px;
            margin-top: 5px;
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
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #999;
            font-size: 13px;
        }
        .logout-link {
            text-align: center;
            margin-top: 15px;
        }
        .logout-link a {
            color: #e74c3c;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }
        .logout-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Change Password</h1>
        <p class="subtitle">Please update your password to continue</p>

        @if ($errors->any())
            <div class="message" style="background: #ffe0e0; border-color: #ff6b6b; color: #ff6b6b;">
                <strong>Error!</strong> Please check the form below.
            </div>
        @endif

        <form action="{{ route('password.update', [], false) }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input
                    type="password"
                    id="current_password"
                    name="current_password"
                    placeholder="Enter your current password"
                    class="@error('current_password') error @enderror"
                    required>
                @error('current_password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="new_password">New Password</label>
                <input
                    type="password"
                    id="new_password"
                    name="new_password"
                    placeholder="Enter your new password (min. 6 characters)"
                    class="@error('new_password') error @enderror"
                    required>
                @error('new_password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="new_password_confirmation">Confirm Password</label>
                <input
                    type="password"
                    id="new_password_confirmation"
                    name="new_password_confirmation"
                    placeholder="Confirm your new password"
                    class="@error('new_password_confirmation') error @enderror"
                    required>
                @error('new_password_confirmation')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit">Update Password</button>
        </form>

        <div class="footer">
            Password must be at least 6 characters long
        </div>

        <div class="logout-link">
            <a href="{{ route('logout', [], false) }}">Logout</a>
        </div>
    </div>
</body>
</html>
