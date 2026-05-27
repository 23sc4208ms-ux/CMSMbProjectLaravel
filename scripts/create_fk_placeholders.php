<?php
// Create FK-linked placeholder rows for `posts` and `teacher_annotations`.
// Uses DB credentials from .env (target Railway DB).

$envPath = __DIR__ . '/../.env';
$env = [];
if (is_file($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (!str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        $env[trim($k)] = trim($v);
    }
}
$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? '3306';
$db   = $env['DB_DATABASE'] ?? '';
$user = $env['DB_USERNAME'] ?? 'root';
$pass = $env['DB_PASSWORD'] ?? '';

try {
    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

// find a student id
$studentId = $pdo->query("SELECT id FROM students ORDER BY id LIMIT 1")->fetchColumn();
if (!$studentId) { echo "No students found to link posts to.\n"; exit(0); }

// find a teacher user id by email then fallback to any user
$teacherId = $pdo->query("SELECT id FROM users WHERE email LIKE '%teacher%' LIMIT 1")->fetchColumn();
if (!$teacherId) $teacherId = $pdo->query("SELECT id FROM users ORDER BY id LIMIT 1")->fetchColumn();
if (!$teacherId) { echo "No users found to link teacher_annotations to.\n"; exit(0); }

$created = ['posts'=>0, 'teacher_annotations'=>0];

// Helper to insert into a table using column introspection
function insertRow(PDO $pdo, $table, $values) {
    global $studentId, $teacherId;
    $colsStmt = $pdo->query("SHOW COLUMNS FROM `{$table}`");
    $cols = $colsStmt->fetchAll(PDO::FETCH_ASSOC);
    $insertCols = [];
    $insertVals = [];
    foreach ($cols as $col) {
        $field = $col['Field'];
        if (str_contains($col['Extra'] ?? '', 'auto_increment')) continue;
        if (array_key_exists($field, $values)) {
            $insertCols[] = $field; $insertVals[] = $values[$field];
            continue;
        }
        // if column allows null or has default, skip
        if ($col['Null'] === 'YES' || $col['Default'] !== null) continue;
        // supply safe placeholder based on type
        $type = strtolower($col['Type']);
        if (str_contains($type, 'int')) {
            // try to detect FK columns ending with _id and provide valid IDs
            if (str_ends_with($field, '_id')) {
                if (str_contains($field, 'student')) $val = $studentId;
                elseif (str_contains($field, 'teacher') || str_contains($field, 'admin') || str_contains($field, 'user')) $val = $teacherId;
                else $val = $teacherId ?? 0;
                $insertCols[] = $field;
                $insertVals[] = $val;
            } else {
                $insertCols[] = $field;
                $insertVals[] = 0;
            }
        } elseif (str_contains($type, 'char') || str_contains($type, 'text')) {
            $insertCols[] = $field;
            $insertVals[] = 'placeholder';
        } elseif (str_contains($type, 'date') || str_contains($type, 'timestamp')) {
            $insertCols[] = $field;
            $insertVals[] = date('Y-m-d H:i:s');
        } else {
            $insertCols[] = $field;
            $insertVals[] = 'placeholder';
        }
    }
    if (empty($insertCols)) return 0;
    $colSql = implode(',', array_map(function($c){ return "`$c`"; }, $insertCols));
    $place = '(' . implode(',', array_fill(0, count($insertCols), '?')) . ')';
    $sql = "INSERT INTO `{$table}` ({$colSql}) VALUES {$place}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($insertVals);
    return $stmt->rowCount();
}

// Create a post linked to $studentId if posts table exists and is empty
if ($pdo->query("SHOW TABLES LIKE 'posts'")->fetchColumn()) {
    $count = (int)$pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
    if ($count === 0) {
        $values = ['student_id'=>$studentId, 'title'=>'Placeholder Post', 'body'=>'Imported placeholder post', 'created_at'=>date('Y-m-d H:i:s'), 'updated_at'=>date('Y-m-d H:i:s')];
        $n = insertRow($pdo, 'posts', $values);
        $created['posts'] = $n;
        echo "Inserted {$n} post(s)\n";
    } else echo "Posts already have {$count} rows, skipping.\n";
} else echo "Posts table missing, skipping.\n";

// Create a teacher_annotation linked to $teacherId if table exists and is empty
if ($pdo->query("SHOW TABLES LIKE 'teacher_annotations'")->fetchColumn()) {
    $count = (int)$pdo->query("SELECT COUNT(*) FROM teacher_annotations")->fetchColumn();
    if ($count === 0) {
        $values = ['teacher_id'=>$teacherId, 'student_id'=>$studentId, 'notes'=>'Placeholder annotation', 'created_at'=>date('Y-m-d H:i:s'), 'updated_at'=>date('Y-m-d H:i:s')];
        $n = insertRow($pdo, 'teacher_annotations', $values);
        $created['teacher_annotations'] = $n;
        echo "Inserted {$n} teacher_annotation(s)\n";
    } else echo "teacher_annotations already have {$count} rows, skipping.\n";
} else echo "teacher_annotations table missing, skipping.\n";

echo "Done. Created: " . json_encode($created) . "\n";
