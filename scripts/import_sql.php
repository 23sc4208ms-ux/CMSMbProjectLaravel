<?php
// Simple SQL importer: parses .env for DB creds and executes statements from a file.
if ($argc < 2) {
    echo "Usage: php import_sql.php path/to/file.sql\n";
    exit(1);
}
$file = $argv[1];
if (!is_file($file)) {
    echo "File not found: $file\n";
    exit(1);
}

// Load .env
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

echo "Connecting to $host:$port / database: $db\n";
try {
    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
    exit(2);
}

$sql = file_get_contents($file);
// Normalize line endings
$sql = str_replace(["\r\n", "\r"], "\n", $sql);

// Split statements naively on semicolon at line end. This handles basic dumps.
$parts = preg_split('/;\s*\n/', $sql);
$count = 0;
foreach ($parts as $part) {
    $stmt = trim($part);
    if ($stmt === '') continue;
    try {
        $pdo->exec($stmt);
        $count++;
    } catch (Exception $e) {
        echo "Error executing statement: " . $e->getMessage() . "\n";
        echo "Statement:\n" . substr($stmt, 0, 400) . "\n---\n";
    }
}

echo "Finished. Executed $count statements from $file\n";

exit(0);
