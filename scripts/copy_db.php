<?php
// Usage: php copy_db.php source_host source_port source_user source_pass source_db target_host target_port target_user target_pass target_db overwrite(yes|no)
if ($argc < 11) {
    echo "Usage: php copy_db.php src_host src_port src_user src_pass src_db dst_host dst_port dst_user dst_pass dst_db overwrite(yes|no)\n";
    exit(1);
}

array_shift($argv); // script name
list($srcHost, $srcPort, $srcUser, $srcPass, $srcDb, $dstHost, $dstPort, $dstUser, $dstPass, $dstDb, $overwrite) = array_pad($argv, 11, null);
$overwrite = strtolower($overwrite) === 'yes';

function pdoConnect($host, $port, $user, $pass, $db)
{
    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
        ]);
        return $pdo;
    } catch (Exception $e) {
        echo "Connection failed: " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "Connecting to source {$srcHost}:{$srcPort} / db={$srcDb}\n";
$src = pdoConnect($srcHost, $srcPort, $srcUser, $srcPass, $srcDb);
echo "Connecting to target {$dstHost}:{$dstPort} / db={$dstDb}\n";
$dst = pdoConnect($dstHost, $dstPort, $dstUser, $dstPass, $dstDb);

// Get list of tables
$tables = $src->query("SHOW FULL TABLES WHERE Table_type='BASE TABLE'")->fetchAll(PDO::FETCH_NUM);
if (!$tables) {
    echo "No tables found in source DB.\n";
    exit(0);
}

foreach ($tables as $row) {
    $table = $row[0];
    echo "\nProcessing table: {$table}\n";

    if ($overwrite) {
        echo "Dropping table if exists on target...\n";
        $dst->exec("DROP TABLE IF EXISTS `{$table}`");
    }

    // Get create statement from source
    $r = $src->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_ASSOC);
    if (!$r || !isset($r['Create Table'])) {
        echo "Failed to get CREATE TABLE for {$table}\n";
        continue;
    }
    $createSql = $r['Create Table'];

    // Create on target
    try {
        echo "Creating table on target...\n";
        $dst->exec($createSql);
    } catch (Exception $e) {
        echo "Create failed (may already exist): " . $e->getMessage() . "\n";
    }

    // Copy data in batches
    $countStmt = $src->query("SELECT COUNT(*) AS c FROM `{$table}`")->fetch();
    $total = $countStmt ? (int)$countStmt['c'] : 0;
    echo "Rows to copy: {$total}\n";
    if ($total === 0) continue;

    $batch = 500;
    $offset = 0;
    while ($offset < $total) {
        $stmt = $src->prepare("SELECT * FROM `{$table}` LIMIT :offset, :limit");
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $batch, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        if (!$rows) break;

        // build insert
        $columns = array_keys($rows[0]);
        $colList = implode('`,`', $columns);
        $placeholders = '(' . rtrim(str_repeat('?,', count($columns)), ',') . ')';
        $allPlaceholders = rtrim(str_repeat($placeholders . ',', count($rows)), ',');
        $sql = "INSERT INTO `{$table}` (`{$colList}`) VALUES {$allPlaceholders}";

        $values = [];
        foreach ($rows as $r) {
            foreach ($columns as $c) $values[] = $r[$c];
        }

        try {
            $dst->prepare($sql)->execute($values);
        } catch (Exception $e) {
            echo "Insert batch failed: " . $e->getMessage() . "\n";
            // try row-by-row
            foreach ($rows as $r) {
                $ph = '(' . rtrim(str_repeat('?,', count($columns)), ',') . ')';
                $sql2 = "INSERT INTO `{$table}` (`{$colList}`) VALUES {$ph}";
                $vals = array_values($r);
                try { $dst->prepare($sql2)->execute($vals); } catch (Exception $e2) { echo "Row insert failed: " . $e2->getMessage() . "\n"; }
            }
        }

        $offset += count($rows);
        echo "Copied {$offset}/{$total}\n";
    }
}

// Copy views
$views = $src->query("SHOW FULL TABLES WHERE Table_type='VIEW'")->fetchAll(PDO::FETCH_NUM);
foreach ($views as $v) {
    $view = $v[0];
    echo "\nProcessing view: {$view}\n";
    if ($overwrite) {
        $dst->exec("DROP VIEW IF EXISTS `{$view}`");
    }
    $r = $src->query("SHOW CREATE VIEW `{$view}`")->fetch(PDO::FETCH_ASSOC);
    if ($r && isset($r['Create View'])) {
        try { $dst->exec($r['Create View']); } catch (Exception $e) { echo "Create view failed: " . $e->getMessage() . "\n"; }
    }
}

echo "\nCopy complete.\n";
