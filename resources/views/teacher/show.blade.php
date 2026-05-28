<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $teacher->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ADD8E6 0%, #87CEEB 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 900px;
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

        .header a {
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .header a:hover {
            background: #764ba2;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .card h2 {
            color: #667eea;
            margin-bottom: 20px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .info-group {
            margin-bottom: 20px;
        }

        .info-group label {
            display: block;
            color: #667eea;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .info-group p {
            color: #333;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 4px;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        .button-group a {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
            display: inline-block;
            flex: 1;
            text-align: center;
        }

        .edit-btn {
            background: #f39c12;
            color: white;
        }

        .edit-btn:hover {
            background: #e67e22;
        }

        .note-btn {
            background: #27ae60;
            color: white;
        }

        .note-btn:hover {
            background: #229954;
        }

        .back-btn {
            background: #95a5a6;
            color: white;
        }

        .back-btn:hover {
            background: #7f8c8d;
        }

        .annotation-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
        }

        .annotation-label {
            color: #856404;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .annotation-text {
            color: #333;
            line-height: 1.5;
        }

        .annotation-time {
            color: #999;
            font-size: 12px;
            margin-top: 5px;
        }

        .no-notes {
            color: #999;
            font-style: italic;
            padding: 20px;
            text-align: center;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $teacher->name }}</h1>
            <a href="{{ route('teacher.index', [], false) }}">← Back</a>
        </div>

        @if (session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <!-- Teacher Information -->
        <div class="card">
            <h2>👨‍🏫 Teacher Information</h2>
            <div class="info-group">
                <label>Name</label>
                <p>{{ $teacher->name }}</p>
            </div>

            <div class="info-group">
                <label>Email</label>
                <p>{{ $teacher->email }}</p>
            </div>

            <div class="info-group">
                <label>Role</label>
                <p>{{ ucfirst($teacher->role) }}</p>
            </div>

            <div class="button-group">
                <a href="{{ route('teacher.edit', [$teacher->id], false) }}" class="edit-btn">✏️ Edit</a>
                <a href="{{ route('teacher.annotate', [$teacher->id], false) }}" class="note-btn">📝 Add Note</a>
                <a href="{{ route('teacher.index', [], false) }}" class="back-btn">← Back to List</a>
            </div>
        </div>

        <!-- Admin Notes -->
        <div class="card">
            <h2>📝 Admin Notes</h2>
            @if($teacher->annotations && $teacher->annotations->count())
                @foreach($teacher->annotations as $annotation)
                    <div class="annotation-box">
                        <div class="annotation-label">Note from Admin</div>
                        <div class="annotation-text">{{ $annotation->annotation }}</div>
                        <div class="annotation-time">Added: {{ $annotation->created_at->format('M d, Y - g:i A') }}</div>
                    </div>
                @endforeach
            @else
                <div class="no-notes">No notes added yet. <a href="{{ route('teacher.annotate', [$teacher->id], false) }}" style="color: #667eea; text-decoration: none;">Add one now →</a></div>
            @endif
        </div>
    </div>
</body>
</html>
