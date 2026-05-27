<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header h1 {
            color: #333;
            font-size: 28px;
        }

        .header p {
            color: #666;
            margin-top: 6px;
            font-size: 14px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logout-btn {
            padding: 10px 20px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .logout-btn:hover {
            background: #c0392b;
        }

        .nav-btn {
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            font-weight: 600;
            border-radius: 4px;
            transition: background 0.3s;
            display: inline-block;
        }

        .nav-btn:hover {
            background: #764ba2;
        }

        .welcome-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .welcome-card h2 {
            color: #333;
            margin-bottom: 10px;
            font-size: 24px;
        }

        .welcome-card .subtext {
            color: #666;
            line-height: 1.6;
        }

        .section-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .section-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            min-height: 180px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .section-card h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .section-card .description {
            color: #666;
            line-height: 1.6;
            font-size: 14px;
        }

        .footer-note {
            text-align: center;
            color: #999;
            font-size: 12px;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .header-right {
                flex-direction: column;
                width: 100%;
            }

            .nav-btn, .logout-btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Teacher Dashboard</h1>
                <p>Welcome, {{ $user->name }}!</p>
            </div>
            <div class="header-right">
                <a href="{{ route('activity-logs.index', [], false) }}" class="nav-btn">Activity Log</a>
                <a href="{{ route('logout', [], false) }}" class="logout-btn">Logout</a>
            </div>
        </div>

        <div class="welcome-card">
            <h2>Overview</h2>
            <p class="subtext">
                This dashboard provides a simple overview of the main teacher sections: students, courses, and grades.
            </p>
        </div>

        <div class="section-grid">
            <div class="section-card">
                <h3>👨‍🎓 Students</h3>
                <p class="description">List of students assigned to the teacher.</p>
            </div>

            <div class="section-card">
                <h3>📚 Courses</h3>
                <p class="description">Courses handled by the teacher.</p>
            </div>

            <div class="section-card">
                <h3>📊 Grades</h3>
                <p class="description">Grade section for tracking student performance.</p>
            </div>
        </div>

        <div class="footer-note">
            Static layout only. No actions or form functions are attached.
        </div>
    </div>
</body>
</html>
