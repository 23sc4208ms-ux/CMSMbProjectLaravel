<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            color: #667eea;
            margin-bottom: 10px;
        }

        .stat-card .number {
            font-size: 32px;
            font-weight: bold;
            color: #333;
        }

        .actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .action-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }

        .action-card h3 {
            color: #333;
            margin-bottom: 15px;
        }

        .action-card a {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .action-card a:hover {
            background: #764ba2;
        }

        .message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Admin Dashboard</h1>
            <a href="{{ route('logout') }}" class="logout-btn">Logout</a>
        </div>

        @if ($message = Session::get('success'))
            <div class="message">
                {{ $message }}
            </div>
        @endif

        <div class="stats">
            <div class="stat-card">
                <h3>Total Teachers</h3>
                <div class="number">{{ $totalTeachers }}</div>
            </div>
            <div class="stat-card">
                <h3>Total Students</h3>
                <div class="number">{{ $totalStudents }}</div>
            </div>
        </div>

        <div class="actions">
            <div class="action-card">
                <h3>👨‍🏫 Add Teacher</h3>
                <a href="{{ route('teacher.create') }}">Add New Teacher</a>
            </div>
            <div class="action-card">
                <h3>👨‍🎓 Add Student</h3>
                <a href="{{ route('students.create') }}">Add New Student</a>
            </div>
            <div class="action-card">
                <h3>📊 View Teachers</h3>
                <a href="{{ route('teacher.index') }}">View All Teachers</a>
            </div>
            <div class="action-card">
                <h3>📋 View Students</h3>
                <a href="{{ route('students.index') }}">View All Students</a>
            </div>
            <div class="action-card">
                <h3>🎓 Degrees</h3>
                <a href="{{ route('degrees.index') }}">Go to Degrees</a>
            </div>
        </div>
    </div>
</body>
</html>
