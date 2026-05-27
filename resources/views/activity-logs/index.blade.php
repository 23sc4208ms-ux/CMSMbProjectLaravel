<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 24px; min-height: 100vh; display: flex; flex-direction: column; background: linear-gradient(135deg, #eef7ff 0%, #dceeff 55%, #c8e2ff 100%); color: #17324d; }
        .container { width: 100%; max-width: 1100px; margin: 0 auto; background: rgba(255, 255, 255, 0.88); border: 1px solid #b9d7f5; border-radius: 16px; padding: 22px; box-shadow: 0 12px 30px rgba(52, 103, 160, 0.16); backdrop-filter: blur(8px); }
        .table-wrap { margin-top: 16px; overflow-x: auto; }
        table { border-collapse: collapse; width: 100%; min-width: 900px; }
        th, td { border: 1px solid #c6dbf2; padding: 11px 10px; text-align: left; vertical-align: top; }
        th { background: linear-gradient(180deg, #1f4f86 0%, #163c68 100%); color: #f7fbff; font-weight: 700; }
        tr:nth-child(even) td { background: #f6fbff; }
        tr:nth-child(odd) td { background: #ffffff; }
        .muted { color: #5f7590; }
        .nav { display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 16px; flex-wrap: wrap; }
        .nav a { text-decoration: none; border: 1px solid #b8d3ee; color: #1f4f86; background: #f5faff; padding: 8px 12px; border-radius: 999px; font-weight: 600; }
        .nav a.active { background: linear-gradient(180deg, #4f8fd1 0%, #2f6fae 100%); color: #fff; border-color: #2f6fae; box-shadow: 0 6px 16px rgba(47, 111, 174, 0.28); }
        .action { display: inline-block; font-weight: 700; color: #fff; background: linear-gradient(180deg, #4f8fd1 0%, #2f6fae 100%); text-transform: uppercase; font-size: 12px; letter-spacing: 0.4px; padding: 5px 10px; border-radius: 999px; box-shadow: 0 6px 14px rgba(47, 111, 174, 0.18); }
        .details { color: #24425f; }
        .footer { width: 100%; max-width: 1100px; margin: auto auto 0; padding-top: 12px; text-align: center; color: #5f7590; font-size: 14px; }
    </style>
</head>
<body>

    <div class="container">
        <nav class="nav">
            <a href="{{ route('home', [], false) }}">Home</a>
            <a href="{{ route('students.index', [], false) }}">Students</a>
            <a href="{{ route('degrees.index', [], false) }}">Degrees</a>
            <a href="{{ route('activity-logs.index', [], false) }}" class="active">Activity Logs</a>
            <a href="{{ route('dashboard', [], false) }}">Dashboard</a>
            <a href="{{ route('about-us', [], false) }}">About Us</a>
            <a href="{{ route('logout', [], false) }}">Logout</a>
        </nav>

        <h1>Activity Logs</h1>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Model</th>
                        <th>Model ID</th>
                        <th>Details</th>
                        <th>IP Address</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        @php($displayAction = str($log->action)->afterLast('.'))
                        <tr>
                            <td><span class="action">{{ $displayAction }}</span></td>
                            <td>{{ $log->subject_type ? class_basename($log->subject_type) : '-' }}</td>
                            <td>{{ $log->subject_id ?? '-' }}</td>
                            <td>
                                <div class="details">{{ $log->description }}</div>
                            </td>
                            <td>{{ $log->ip_address ?? '-' }}</td>
                            <td>{{ optional($log->created_at)->format('M d, Y h:i A') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="muted">No activity logs yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            {{ $logs->links() }}
        </div>
    </div>

    <footer class="footer">
        Copyright &copy; {{ date('Y') }} Student Management System. All rights reserved.
    </footer>
</body>
</html>
