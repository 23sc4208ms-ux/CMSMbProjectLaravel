<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Degree List</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 24px; min-height: 100vh; display: flex; flex-direction: column; background: linear-gradient(135deg, #eef7ff 0%, #dceeff 55%, #c8e2ff 100%); color: #17324d; }
        .container { width: 100%; max-width: 1000px; margin: 0 auto; background: rgba(255, 255, 255, 0.9); border: 1px solid #b9d7f5; border-radius: 16px; padding: 22px; box-shadow: 0 12px 30px rgba(52, 103, 160, 0.16); backdrop-filter: blur(8px); }
        .row { margin-bottom: 14px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; }
        input { width: 100%; padding: 8px 10px; border: 1px solid #b8d3ee; border-radius: 8px; background: #fff; box-sizing: border-box; }
        table { border-collapse: collapse; width: 100%; margin-top: 16px; }
        th, td { border: 1px solid #c6dbf2; padding: 10px; text-align: left; }
        th { background: linear-gradient(180deg, #1f4f86 0%, #163c68 100%); color: #f7fbff; }
        .btn { display: inline-block; padding: 9px 14px; text-decoration: none; border: 1px solid #2f6fae; background: linear-gradient(180deg, #4f8fd1 0%, #2f6fae 100%); color: #fff; border-radius: 999px; cursor: pointer; font-weight: 600; }
        .btn.small { padding: 6px 10px; font-size: 13px; }
        .btn.secondary { background: #f5faff; color: #1f4f86; border-color: #b8d3ee; }
        .btn.danger { background: linear-gradient(180deg, #5d8fc4 0%, #2b5d91 100%); border-color: #2b5d91; }
        .alert { background: #eaf4ff; border: 1px solid #b9d7f5; color: #1f4f86; padding: 10px; margin-bottom: 14px; }
        .alert.error { background: #eef6ff; border-color: #9cc0e8; color: #17406a; }
        .error { color: #d13b3b; font-size: 14px; margin-top: 4px; }
        .muted { color: #5f7590; }
        .nav { display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 16px; flex-wrap: wrap; }
        .nav a { text-decoration: none; border: 1px solid #b8d3ee; color: #1f4f86; background: #f5faff; padding: 8px 12px; border-radius: 999px; font-weight: 600; }
        .nav a.active { background: linear-gradient(180deg, #4f8fd1 0%, #2f6fae 100%); color: #fff; border-color: #2f6fae; box-shadow: 0 6px 16px rgba(47, 111, 174, 0.28); }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .footer { width: 100%; max-width: 1000px; margin: auto auto 0; padding-top: 12px; text-align: center; color: #5f7590; font-size: 14px; }

        @media (max-width: 700px) {
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="container">
        <nav class="nav">
            <a href="{{ route('home', [], false) }}">Home</a>
            <a href="{{ route('students.index', [], false) }}">Students</a>
            <a href="{{ route('degrees.index', [], false) }}" class="active">Degrees</a>
            <a href="{{ route('activity-logs.index', [], false) }}">Activity Logs</a>
            <a href="{{ route('dashboard', [], false) }}">Dashboard</a>
            <a href="{{ route('about-us', [], false) }}">About Us</a>
            <a href="{{ route('logout', [], false) }}">Logout</a>
        </nav>

        <h1>Degree Management</h1>

        @if (session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        <form action="{{ route('degrees.store', [], false) }}" method="POST">
            @csrf

            <div class="grid">
                <div class="row">
                    <label for="code">Degree Code</label>
                    <input type="text" id="code" name="code" value="{{ old('code') }}" placeholder="Example: BSIT" minlength="2" maxlength="120" required>
                    @error('code') <div class="error">{{ $message }}</div> @enderror
                </div>
            </div>

            <button type="submit" class="btn">Add Degree</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($degrees as $degree)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $degree->code }}</td>
                        <td>
                            <a href="{{ route('degrees.show', [$degree], false) }}" class="btn small secondary">View</a>
                            <a href="{{ route('degrees.edit', [$degree], false) }}" class="btn small">Edit</a>
                            <form action="{{ route('degrees.destroy', [$degree], false) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this degree?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn small danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="muted">No degree records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <footer class="footer">
        Copyright &copy; {{ date('Y') }} Student Management System. All rights reserved.
    </footer>
</body>
</html>
