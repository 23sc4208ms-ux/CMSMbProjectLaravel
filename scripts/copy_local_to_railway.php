<?php
// Copy a whole source MySQL database into the target DB (Railway) using .env for target.
// Usage: php copy_local_to_railway.php [src_host] [src_port] [src_user] [src_pass] [src_db]

$args = $argv;
array_shift($args);
$srcHost = $args[0] ?? '127.0.0.1';
$srcPort = $args[1] ?? '3306';
$srcUser = $args[2] ?? 'root';
$srcPass = $args[3] ?? '';
$srcDb   = $args[4] ?? 'students';

// load .env for target
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
$tHost = $env['DB_HOST'] ?? '127.0.0.1';
$tPort = $env['DB_PORT'] ?? '3306';
$tDb   = $env['DB_DATABASE'] ?? '';
$tUser = $env['DB_USERNAME'] ?? 'root';
$tPass = $env['DB_PASSWORD'] ?? '';

echo "Source: {$srcUser}@{$srcHost}:{$srcPort}/{$srcDb}\n";
echo "Target: {$tUser}@{$tHost}:{$tPort}/{$tDb}\n";

try {
    $srcDsn = "mysql:host={$srcHost};port={$srcPort};dbname={$srcDb};charset=utf8mb4";
    $src = new PDO($srcDsn, $srcUser, $srcPass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    echo "Failed connecting to source: " . $e->getMessage() . PHP_EOL; exit(2);
}

try {
    $tDsn = "mysql:host={$tHost};port={$tPort};dbname={$tDb};charset=utf8mb4";
    $tgt = new PDO($tDsn, $tUser, $tPass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    echo "Failed connecting to target: " . $e->getMessage() . PHP_EOL; exit(3);
}

// get tables
$tables = $src->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_COLUMN);
if (!$tables) { echo "No tables found in source DB.\n"; exit(0); }

echo "Found " . count($tables) . " tables.\n";

// disable fk checks on target
$tgt->exec('SET FOREIGN_KEY_CHECKS=0');

foreach ($tables as $table) {
    echo "Processing table: {$table}\n";
    // get create table
    $row = $src->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_ASSOC);
    $create = $row['Create Table'] ?? $row['Create View'] ?? null;
    if (!$create) { echo "  Could not get CREATE for {$table}\n"; continue; }
    // drop if exists target
    try {
        $tgt->exec("DROP TABLE IF EXISTS `{$table}`");
    } catch (Exception $e) { echo "  Warning dropping {$table}: " . $e->getMessage() . "\n"; }
    // create table on target
    try {
        $tgt->exec($create);
    } catch (Exception $e) { echo "  Failed to create {$table} on target: " . $e->getMessage() . "\n"; continue; }

    // copy data in chunks
    $count = (int) $src->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
    echo "  Rows to copy: {$count}\n";
    if ($count === 0) continue;
    $chunk = 500;
    $offset = 0;
    while ($offset < $count) {
        $stmt = $src->prepare("SELECT * FROM `{$table}` LIMIT :lim OFFSET :off");
        $stmt->bindValue(':lim', $chunk, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) break;
        // build insert
        $cols = array_keys($rows[0]);
        $colSql = implode(',', array_map(function($c){ return "`$c`"; }, $cols));
        $place = '(' . implode(',', array_fill(0, count($cols), '?')) . ')';
        $insertSql = "INSERT INTO `{$table}` ({$colSql}) VALUES ";
        $vals = [];
        $parts = [];
        foreach ($rows as $r) {
            $parts[] = $place;
            foreach ($r as $v) $vals[] = $v;
        }
        $insertSql .= implode(',', $parts);
        $tstmt = $tgt->prepare($insertSql);
        try {
            $tstmt->execute($vals);
        } catch (Exception $e) {
            echo "  Failed inserting chunk at offset {$offset}: " . $e->getMessage() . "\n";
            // attempt row-by-row fallback
            foreach ($rows as $r) {
                $placeR = '(' . implode(',', array_fill(0, count($cols), '?')) . ')';
                $sqlR = "INSERT INTO `{$table}` ({$colSql}) VALUES {$placeR}";
                try { $tgt->prepare($sqlR)->execute(array_values($r)); } catch (Exception $e2) { echo "    Row insert failed: " . $e2->getMessage() . "\n"; }
            }
        }
        $offset += count($rows);
        echo "  Copied {$offset}/{$count}\r";
    }
    echo "\n";
}

$tgt->exec('SET FOREIGN_KEY_CHECKS=1');
echo "Done copying database.\n";
