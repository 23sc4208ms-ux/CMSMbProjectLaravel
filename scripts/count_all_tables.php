<?php
// Lists all tables in the target DB and row counts
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

$tables = $pdo->query("SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = '" . addslashes($db) . "'")->fetchAll(PDO::FETCH_COLUMN);
if (!$tables) { echo "No tables found in database {$db}\n"; exit(0); }

foreach ($tables as $t) {
    try {
        $c = $pdo->query("SELECT COUNT(*) AS c FROM `{$t}`")->fetch(PDO::FETCH_ASSOC);
        $n = $c ? (int)$c['c'] : 0;
    } catch (Exception $e) {
        $n = 'error';
    }
    echo "Table {$t}: {$n}\n";
}
