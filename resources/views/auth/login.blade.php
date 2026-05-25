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

        <div id="errorContainer"></div>
        <div id="countdownContainer"></div>

        <form id="loginForm">
            @csrf

            <div class="form-group">
                <label for="username">Username or Email</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter your username or email"
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
                    required
                >
            </div>

            <button type="submit" id="loginBtn">Login</button>
        </form>

        <div class="footer">
            <a href="{{ route('home') }}">Back to Home</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let lockoutInterval = null;

            // Handle form submission with AJAX
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();

                const username = $('#username').val();
                const password = $('#password').val();
                const token = $('input[name="_token"]').val();

                // Disable button during submission
                $('#loginBtn').prop('disabled', true).text('Logging in...');

                $.ajax({
                    url: "{{ route('login.submit') }}",
                    type: 'POST',
                    data: {
                        _token: token,
                        username: username,
                        password: password
                    },
                    success: function(response) {
                        // Redirect on successful login
                        window.location.href = response.redirect_url;
                    },
                    error: function(xhr) {
                        $('#loginBtn').prop('disabled', false).text('Login');

                        const response = xhr.responseJSON;

                        if (response.locked) {
                            // Show lockout message
                            showLockoutCountdown(response.remaining_seconds);
                        } else {
                            // Show error message
                            $('#errorContainer').html(
                                '<div class="error-message">' + response.message + '</div>'
                            );
                        }
                    }
                });
            });

            // Display lockout countdown
            function showLockoutCountdown(seconds) {
                let remaining = seconds;

                $('#errorContainer').html(
                    '<div class="error-message">Account locked due to too many failed login attempts</div>'
                );

                $('#countdownContainer').html(
                    '<div class="countdown-timer">' +
                    '<div class="timer-label">Account Locked - Time Remaining:</div>' +
                    '<div class="timer-display" id="countdown">' + remaining + '</div>' +
                    '<div class="timer-unit">seconds</div>' +
                    '</div>'
                );

                // Disable form
                $('#email').prop('disabled', true);
                $('#password').prop('disabled', true);
                $('#loginBtn').prop('disabled', true);

                // Update countdown every second
                lockoutInterval = setInterval(function() {
                    remaining--;
                    $('#countdown').text(remaining);

                    if (remaining <= 0) {
                        clearInterval(lockoutInterval);
                        // Re-enable form
                        $('#email').prop('disabled', false);
                        $('#password').prop('disabled', false);
                        $('#loginBtn').prop('disabled', false);

                        $('#countdownContainer').html(
                            '<div style="padding: 15px; background: #d4edda; border: 2px solid #4caf50; border-radius: 8px; color: #155724; text-align: center; margin-bottom: 20px;">Account Unlocked! You can try again.</div>'
                        );
                    }
                }, 1000);
            }

            // Check if locked on page load
            @if (session('locked'))
                showLockoutCountdown({{ session('remaining_seconds', 0) }});
            @endif
        });
    </script>
</body>
</html>
