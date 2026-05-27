<?php
// Populate minimal placeholder rows for selected empty tables.
// Safe approach: inspect table columns and supply placeholder values
// for NOT NULL columns based on their types.

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

$tables = [
    'courses', 'course_students', 'posts', 'teacher_annotations',
    'cache', 'cache_locks', 'failed_jobs', 'job_batches', 'jobs', 'password_reset_tokens'
];

function tableExists($pdo, $table) {
    $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
    $stmt->execute([$table]);
    return (bool) $stmt->fetchColumn();
}

function rowCount($pdo, $table) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM `" . $table . "`");
    return (int) $stmt->fetchColumn();
}

function placeholderForType($type) {
    $t = strtolower($type);
    if (str_contains($t, 'int')) return 0;
    if (str_contains($t, 'decimal') || str_contains($t, 'float') || str_contains($t, 'double')) return 0;
    if (str_contains($t, 'char') || str_contains($t, 'text') || str_contains($t, 'blob')) return 'placeholder';
    if (str_contains($t, 'bool') || str_contains($t, 'tinyint(1)')) return 0;
    if (str_contains($t, 'datetime') || str_contains($t, 'timestamp') || str_contains($t, 'date')) return date('Y-m-d H:i:s');
    if (str_starts_with($t, 'enum')) {
        // parse enum values
        if (preg_match("/enum\((.*)\)/", $t, $m)) {
            $opts = array_map(function($s){ return trim($s, "'\""); }, explode(',', $m[1]));
            return $opts[0] ?? 'placeholder';
        }
        return 'placeholder';
    }
    if (str_contains($t, 'json')) return '{}';
    return 'placeholder';
}

$createdSummary = [];
foreach ($tables as $table) {
    if (!tableExists($pdo, $table)) { echo "Skipping missing table: {$table}\n"; continue; }
    $count = rowCount($pdo, $table);
    if ($count > 0) { echo "Skipping {$table}: already has {$count} rows\n"; continue; }

    $colsStmt = $pdo->query("SHOW COLUMNS FROM `{$table}`");
    $cols = $colsStmt->fetchAll(PDO::FETCH_ASSOC);
    $insertCols = [];
    $insertVals = [];

    foreach ($cols as $col) {
        $field = $col['Field'];
        $type = $col['Type'];
        $null = $col['Null'];
        $extra = $col['Extra'];
        $default = $col['Default'];
        if (str_contains($extra, 'auto_increment')) continue;
        if ($default !== null) continue; // let DB default
        if ($null === 'YES') { $insertCols[] = $field; $insertVals[] = null; continue; }
        // NOT NULL and no default -> provide placeholder
        $val = placeholderForType($type);
        $insertCols[] = $field;
        $insertVals[] = $val;
    }

    if (empty($insertCols)) { echo "No suitable columns to insert for {$table}, skipping.\n"; continue; }

    // Special handling for course_students: link first student and first course if possible
    if ($table === 'course_students') {
        // ensure there's at least one course
        if (tableExists($pdo, 'courses') && rowCount($pdo, 'courses') === 0) {
            // create one course
            $now = date('Y-m-d H:i:s');
            $pdo->exec("INSERT INTO `courses` (name, code, created_at, updated_at) VALUES ('Placeholder Course','PLH-101','{$now}','{$now}')");
        }
        // fetch first course and first student
        $courseId = $pdo->query("SELECT id FROM courses ORDER BY id LIMIT 1")->fetchColumn();
        $studentId = $pdo->query("SELECT id FROM students ORDER BY id LIMIT 1")->fetchColumn();
        if ($courseId && $studentId) {
            try {
                $stmt = $pdo->prepare("INSERT INTO course_students (course_id, student_id, created_at, updated_at) VALUES (?, ?, ?, ?)");
                $now = date('Y-m-d H:i:s');
                $stmt->execute([$courseId, $studentId, $now, $now]);
                echo "Inserted placeholder into course_students linking course {$courseId} and student {$studentId}\n";
                $createdSummary[$table] = 1;
            } catch (Exception $e) { echo "Failed course_students insert: " . $e->getMessage() . "\n"; }
        } else {
            echo "Cannot create course_students: missing course or student\n";
        }
        continue;
    }

    // Build prepared statement
    $colList = implode(',', array_map(function($c){ return "`$c`"; }, $insertCols));
    $placeholders = implode(',', array_fill(0, count($insertCols), '?'));
    $sql = "INSERT INTO `{$table}` ({$colList}) VALUES ({$placeholders})";
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute($insertVals);
        $createdSummary[$table] = $stmt->rowCount();
        echo "Inserted {$createdSummary[$table]} row(s) into {$table}\n";
    } catch (Exception $e) {
        echo "Failed to insert into {$table}: " . $e->getMessage() . "\n";
    }
}

echo "Done. Summary:\n";
foreach ($createdSummary as $t => $c) echo " - {$t}: {$c}\n";
