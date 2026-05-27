<?php
// Count rows in a table using .env DB credentials
if ($argc < 2) {
    echo "Usage: php count_table.php table_name\n";
    exit(1);
}
$table = $argv[1];

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
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
    exit(2);
}

try {
    $stmt = $pdo->query("SELECT COUNT(*) AS c FROM `" . addslashes($table) . "`");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $row['c'] ?? 0;
    echo "Table $table: $count rows\n";
} catch (Exception $e) {
    echo "Query failed: " . $e->getMessage() . "\n";
    exit(3);
}

exit(0);
