<?php
// Usage: php fill_empty_from_dump.php path/to/dump.sql
if ($argc < 2) {
    echo "Usage: php fill_empty_from_dump.php path/to/dump.sql\n";
    exit(1);
}
$dump = $argv[1];
if (!is_file($dump)) { echo "Dump not found: $dump\n"; exit(1); }

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

try {
    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

// Find empty tables
$tables = $pdo->query("SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = '" . addslashes($db) . "'")->fetchAll(PDO::FETCH_COLUMN);
$empty = [];
foreach ($tables as $t) {
    try { $c = $pdo->query("SELECT COUNT(*) AS c FROM `{$t}`")->fetch(PDO::FETCH_ASSOC); $n = $c ? (int)$c['c'] : 0; } catch (Exception $e) { $n = 0; }
    if ($n === 0) $empty[] = $t;
}

if (empty($empty)) { echo "No empty tables found.\n"; exit(0); }

echo "Empty tables: " . implode(', ', $empty) . "\n";

$sql = file_get_contents($dump);
// Normalize endings
$sql = str_replace(["\r\n","\r"], "\n", $sql);
$parts = preg_split('/;\s*\n/', $sql);

foreach ($empty as $t) {
    echo "\nProcessing inserts for table: {$t}\n";
    $found = 0;
    foreach ($parts as $part) {
        $stmt = trim($part);
        if ($stmt === '') continue;
        if (stripos($stmt, "INSERT INTO `{$t}`") === 0 || stripos($stmt, "INSERT INTO `{$t}`") !== false) {
            try {
                $pdo->exec($stmt);
                $found++;
            } catch (Exception $e) {
                echo "Error executing insert for {$t}: " . $e->getMessage() . "\n";
            }
        }
    }
    echo "Executed {$found} insert statements for {$t}\n";
}

echo "Done.\n";
