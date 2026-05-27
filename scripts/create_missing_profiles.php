<?php
// Creates a minimal profile row for each student that doesn't have one yet.
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

$students = $pdo->query("SELECT id FROM students")->fetchAll(PDO::FETCH_COLUMN);
$created = 0;
foreach ($students as $sid) {
    $has = $pdo->prepare("SELECT 1 FROM profiles WHERE student_id = ? LIMIT 1");
    $has->execute([$sid]);
    if ($has->fetch()) continue;
    $now = date('Y-m-d H:i:s');
    $stmt = $pdo->prepare("INSERT INTO profiles (student_id, bio, phone, avatar_path, created_at, updated_at) VALUES (?, NULL, NULL, NULL, ?, ?)");
    try { $stmt->execute([$sid, $now, $now]); $created++; } catch (Exception $e) { echo "Failed to create profile for student {$sid}: " . $e->getMessage() . "\n"; }
}
echo "Created {$created} profile(s).\n";
