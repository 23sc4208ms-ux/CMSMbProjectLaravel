<?php
// Usage: php drop_tables_from_dump.php path/to/dump.sql
if ($argc < 2) {
    echo "Usage: php drop_tables_from_dump.php path/to/dump.sql\n";
    exit(1);
}
$file = $argv[1];
if (!is_file($file)) { echo "File not found: $file\n"; exit(1); }
$sql = file_get_contents($file);

preg_match_all('/CREATE TABLE `([^`]+)`/i', $sql, $m);
$tables = array_unique($m[1] ?? []);
if (empty($tables)) { echo "No tables found in dump.\n"; exit(0); }

// load target DB from .env
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
    echo "Failed to connect to target DB: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Dropping " . count($tables) . " tables on target if they exist...\n";
foreach ($tables as $t) {
    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
        $pdo->exec("DROP TABLE IF EXISTS `{$t}`");
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
        echo "Dropped: {$t}\n";
    } catch (Exception $e) {
        echo "Failed to drop {$t}: " . $e->getMessage() . "\n";
    }
}
echo "Done.\n";
