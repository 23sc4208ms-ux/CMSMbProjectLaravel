<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
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
            max-width: 800px;
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

        .welcome {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .nav-item {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 6px;
            background: white;
            color: #667eea;
            font-weight: 700;
            margin-right: 8px;
            text-align: center;
            text-decoration: none;
            border: 1px solid rgba(0,0,0,0.04);
        }

        .nav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        .nav-item {
            background: white;
            padding: 14px 16px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            color: #667eea;
            font-weight: bold;
            text-align: center;
        }

        .welcome h2 {
            color: #667eea;
            margin-bottom: 15px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #667eea;
            font-weight: bold;
        }

        .info-value {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Student Dashboard</h1>
            <div style="display:flex; align-items:center; gap:8px;">
                <a href="{{ route('activity-logs.index', [], false) }}" class="nav-item">Activity Log</a>
                <a href="{{ route('logout', [], false) }}" class="logout-btn">Logout</a>
            </div>
        </div>

        @if($student)
            <div class="welcome">
                <h2>Student Profile</h2>
                <div class="info-row">
                    <span class="info-label">Student ID:</span>
                    <span class="info-value">{{ $student->student_id ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">First Name:</span>
                    <span class="info-value">{{ $student->first_name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Middle Name:</span>
                    <span class="info-value">{{ $student->middle_name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Last Name:</span>
                    <span class="info-value">{{ $student->last_name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Contact Number:</span>
                    <span class="info-value">{{ $student->contact_number ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Address:</span>
                    <span class="info-value">{{ $student->address ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Degree:</span>
                    <span class="info-value">{{ $student->degree?->degree_title ?? $student->degree?->code ?? 'N/A' }}</span>
                </div>
            </div>
        @endif
    </div>
</body>
</html>
